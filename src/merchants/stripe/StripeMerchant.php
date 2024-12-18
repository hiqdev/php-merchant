<?php declare(strict_types=1);
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\merchants\stripe;

use DateTime;
use DateTimeImmutable;
use Exception;
use hiqdev\php\merchant\card\CardInformation;
use hiqdev\php\merchant\exceptions\InsufficientFundsException;
use hiqdev\php\merchant\exceptions\MerchantException;
use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\AbstractMerchant;
use hiqdev\php\merchant\merchants\CanIgnore3dSecureMerchantInterface;
use hiqdev\php\merchant\merchants\HostedPaymentPageMerchantInterface;
use hiqdev\php\merchant\merchants\PaymentCardMerchantInterface;
use hiqdev\php\merchant\merchants\PaymentRefundInterface;
use hiqdev\php\merchant\merchants\RefundRequestInterface;
use hiqdev\php\merchant\merchants\RemoteCustomerAwareMerchant;
use hiqdev\php\merchant\response\CardAuthorizationResponse;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\Site\Merchant\Service\Exception\MeaningfulForUserMerchantException;
use Money\Currency;
use Money\Money;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Stripe\PaymentIntentsGateway;

/**
 * Class StripeMerchant
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class StripeMerchant extends AbstractMerchant implements
    HostedPaymentPageMerchantInterface,
    PaymentCardMerchantInterface,
    RemoteCustomerAwareMerchant,
    CanIgnore3dSecureMerchantInterface,
    PaymentRefundInterface
{
    /**
     * @var PaymentIntentsGateway
     */
    protected $gateway;

    private bool $ignore3dSecure = false;

    protected function createGateway()
    {
        return $this->gatewayFactory->build('Stripe\PaymentIntents', [
            'apiKey' => $this->credentials->getKey1(),
        ]);
    }

    public function requestPurchase(InvoiceInterface $invoice)
    {
        $clientSecret = $this->fetchClientSecret($invoice->getClient()->remoteId());

        $response = new RedirectPurchaseResponse('', [
            'clientSecret' => $clientSecret,
            'publicKey' => $this->credentials->getKey2()
        ]);
        $response->setMethod('GET');

        return $response;
    }

    public function refund(RefundRequestInterface $refund): void
    {
        try {
            /** @var \Omnipay\Stripe\Message\Response $response */
            $response = $this->gateway->refund([
                'transactionReference' => $refund->getRefundTransactionId(),
                'amount' => $this->moneyFormatter->format($refund->getAmount()),
            ])->send();

            if (!$response->isSuccessful()) {
                throw new MerchantException('Response is not successful');
            }
        } catch (Exception $exception) {
            throw new MerchantException('Failed to refund a card: ' . $exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function removePaymentMethod(string $paymentMethod): void
    {
        try {
            /** @var \Omnipay\Stripe\Message\Response $response */
            $response = $this->gateway->deleteCard([
                'paymentMethod' => $paymentMethod,
            ])->send();

            if (!$response->isSuccessful()) {
                throw new MerchantException($response->getData()['message']);
            }
        } catch (Exception $exception) {
            throw new MerchantException('Failed to remove a card: ' . $exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function chargeCard(InvoiceInterface $invoice)
    {
        $ignore3dSecure = $this->is3dSecureIgnored() ? ['off_session' => true] : [];
        try {
            /** @var \Omnipay\Stripe\Message\Response $response */
            $response = $this->gateway->purchase(array_merge([
                'amount' => $this->moneyFormatter->format($invoice->getAmount()),
                'currency' => $invoice->getCurrency()->getCode(),
                'description' => $invoice->getDescription(),
                'customerReference' => $invoice->getClient()->remoteId(),
                'paymentMethod' => $invoice->getPreferredPaymentMethod(),
                'returnUrl' => $invoice->getReturnUrl(),
                'confirm' => true,
            ], $ignore3dSecure))->send();
        } catch (Exception $exception) {
            throw new MerchantException('Failed to charge a card: ' . $exception->getMessage(), $exception->getCode(), $exception);
        }

        if ($response->isRedirect()) {
            return (new RedirectPurchaseResponse($response->getRedirectUrl(), $response->getRedirectData() ?? []))
                ->setMethod('GET');
        }

        if ($response->isSuccessful()) {
            return (new CompletePurchaseResponse())
                ->setIsSuccessful(true)
                ->setAmount($invoice->getAmount())
                ->setFee(new Money(0, $invoice->getAmount()->getCurrency()))
                ->setTransactionReference(
                    $response->getTransactionReference()
                    ?? $response->getData()['charges']['data'][0]['id']
                )
                ->setTransactionId($response->getTransactionId())
                ->setPayer(
                    $response->getCustomerReference()
                    ?? $response->getData()['charges']['data'][0]['customer']
                    ?? ''
                )
                ->setTime(new DateTime());
        }

        if (isset($response->getData()['error']['message'])) {
            throw new MerchantException($response->getData()['error']['message']);
        }

        throw new MerchantException('Failed to charge card');
    }

    public function completePurchase($data)
    {
        $response = $this->gateway->fetchPaymentIntent([
            'paymentIntentReference' => $data['payment_intent']
        ])->send();
        if ($response->getData()['confirmation_method'] === 'manual' && $response->getData()['status'] === 'requires_confirmation') {
            $response = $this->gateway->confirm([
                'paymentIntentReference' => $data['payment_intent']
            ])->send();
        }

        if ($response->isSuccessful()) {
            return (new CompletePurchaseResponse())
                ->setIsSuccessful(true)
                ->setAmount(new Money($response->getData()['amount'], new Currency(strtoupper($response->getData()['currency']))))
                ->setFee(new Money(0, new Currency(strtoupper($response->getData()['currency']))))
                ->setTransactionReference($response->getTransactionReference())
                ->setTransactionId($response->getTransactionId())
                ->setPayer($response->getData()['customer'] ?? '')
                ->setTime(new DateTime());
        }

        if (
            isset($response->getData()['last_payment_error'])
            && isset($response->getData()['last_payment_error']['code'])
            && isset($response->getData()['last_payment_error']['decline_code'])
            && $response->getData()['last_payment_error']['code'] === 'card_declined'
            && $response->getData()['last_payment_error']['decline_code'] === 'insufficient_funds'
        ) {
            $message = $response->getData()['last_payment_error']['message'] ?? 'Insufficient funds';
            throw (new InsufficientFundsException($message))->withContextData($response->getData());
        }

        if (isset($response->getData()['error']['message']) || isset($response->getData()['last_payment_error']['message'])) {
            $message = $response->getData()['error']['message'] ?? $response->getData()['last_payment_error']['message'];
            throw new MeaningfulForUserMerchantException("Failed to charge card:\n" . $message);
        }

        throw new MerchantException('Failed to charge card');
    }

    private function fetchClientSecret(string $customerReference): string
    {
        $response = $this->gateway->createSetupIntent(compact('customerReference'))->send();
        if ($response->isSuccessful()) {
            return $response->getData()['client_secret'];
        }

        throw new RuntimeException($response->getMessage());
    }

    public function createCustomer(string $email): string
    {
        $response = $this->gateway->createCustomer(compact('email'))->send();
        if (!$response->isSuccessful()) {
            throw new RuntimeException($response->getMessage());
        }

        return $response->getCustomerReference();
    }

    public function fetchCardInformation(string $clientId, string $token): CardInformation
    {
        $response = $this->gateway->fetchCard([
            'paymentMethod' => $token
        ])->send();

        $card = $response->getData()['card'];
        $result = new CardInformation();
        $result->brand = $card['brand'] ?? null;
        $result->last4 = $card['last4'];
        $result->fingerprint = $card['fingerprint'] ?? null;
        $result->expirationTime = DateTimeImmutable::createFromFormat('m/Y', "{$card['exp_month']}/{$card['exp_year']}");

        return $result;
    }

    public function withIgnore3dSecure(): self
    {
        $self = clone $this;
        $self->ignore3dSecure = true;

        return $self;
    }

    public function is3dSecureIgnored(): bool
    {
        return $this->ignore3dSecure;
    }

    public function authorizeCard(InvoiceInterface $invoice)
    {
        $ignore3dSecure = $this->is3dSecureIgnored() ? ['off_session' => true] : [];
        try {
            /** @var \Omnipay\Stripe\Message\Response $response */
            $response = $this->gateway->authorize(array_merge([
                'amount' => $this->moneyFormatter->format($invoice->getAmount()),
                'currency' => $invoice->getCurrency()->getCode(),
                'description' => $invoice->getDescription(),
                'customerReference' => $invoice->getClient()->remoteId(),
                'paymentMethod' => $invoice->getPreferredPaymentMethod(),
                'returnUrl' => $invoice->getReturnUrl(),
                'confirm' => true,
                'capture_method' => 'manual'
            ], $ignore3dSecure))->send();
        } catch (Exception $exception) {
            throw new MerchantException('Failed to authorize a payment card: ' . $exception->getMessage(), $exception->getCode(), $exception);
        }

        if ($response->isRedirect()) {
            return (new RedirectPurchaseResponse($response->getRedirectUrl(), $response->getRedirectData() ?? []))
                ->setMethod('GET');
        }

        if ($response->isSuccessful()) {
            return (new CardAuthorizationResponse())
                ->setExpirationTime((new DateTimeImmutable())->modify('+7 days'))
                ->setIsSuccessful(true)
                ->setAmount($invoice->getAmount())
                ->setFee(new Money(0, $invoice->getAmount()->getCurrency()))
                ->setTransactionReference(
                    $response->getData()['charges']['data'][0]['payment_intent']
                )
                ->setTransactionId($response->getTransactionId())
                ->setPayer(
                    $response->getCustomerReference()
                    ?? $response->getData()['charges']['data'][0]['customer']
                    ?? ''
                )
                ->setTime(new DateTime());
        }

        if (isset($response->getData()['error']['message'])) {
            throw new MerchantException($response->getData()['error']['message']);
        }

        throw new MerchantException('Failed to charge card');
    }

    public function cancelAuthorization(RefundRequestInterface $refundRequest): void
    {
        try {
            /** @var \Omnipay\Stripe\Message\PaymentIntents\Response $response */
            $response = $this->gateway->cancel([
                'paymentIntentReference' => $refundRequest->getRefundTransactionId(),
            ])->send();

            if (!$response->isCancelled()) {
                throw new MerchantException('Payment has not been canceled, actual status: ' . $response->getStatus());
            }
        } catch (Exception $exception) {
            throw new MerchantException('Failed to cancel a card authorization: ' . $exception->getMessage(), $exception->getCode(), $exception);
        }
    }
}
