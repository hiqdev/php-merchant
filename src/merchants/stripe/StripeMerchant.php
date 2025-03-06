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
use hiqdev\php\merchant\exceptions\MerchantException;
use hiqdev\php\merchant\Invoice;
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
use Money\Money;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Stripe\PaymentIntentsGateway;
use Stripe\StripeClient;

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
    private ?StripeClient $stripe = null;

    protected function createGateway()
    {
        $this->stripe = new StripeClient($this->credentials->getKey1());

        return $this->gatewayFactory->build('Stripe\PaymentIntents', [
            'apiKey' => $this->credentials->getKey1(),
        ]);
    }

    public function requestPurchase(InvoiceInterface $invoice)
    {
        $clientSecret = $this->fetchClientSecret($invoice);

        $response = new RedirectPurchaseResponse('', [
            'paymentClientSecret' => $clientSecret['payment_client_secret'],
            'setupClientSecret' => $clientSecret['client_secret'],
            'customerSessionClientSecret' => $clientSecret['customer_session_client_secret'],
            'publicKey' => $this->credentials->getKey2(),
            'notifyUrl' => $invoice->getNotifyUrl(),
            'returnUrl' => $invoice->getReturnUrl(),
            'errorUrl' => $invoice->getCancelUrl(),
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
        return (new ConfirmationStrategy($this->gateway, $this->credentials->getKey3()))->confirm($data);
    }

    private function fetchClientSecret(Invoice $invoice): array
    {
        $customerReference = $invoice->getClient()->remoteId();

        $setupIntent = $this->stripe->setupIntents->create([
            'customer' => $customerReference,
            'automatic_payment_methods' => ['enabled' => false],
            'payment_method_types' => ['card'],
            'metadata' => [
                'transactionId' => $invoice->getId(),
            ],
        ]);

        $paymentIntent = $this->stripe->paymentIntents->create([
            'customer' => $customerReference,
            'automatic_payment_methods' => ['enabled' => true],
            'amount' => $invoice->getAmount()->getAmount(),
            'currency' => $invoice->getAmount()->getCurrency()->getCode(),
            'metadata' => [
                'transactionId' => $invoice->getId(),
            ],
        ]);

//        $paymentIntent = $this->stripe->setupIntents->create([
//            'customer' => $customerReference,
//            'automatic_payment_methods' => ['enabled' => true],
////            'amount' => $invoice->getAmount()->getAmount(),
////            'currency' => $invoice->getAmount()->getCurrency()->getCode(),
//            'metadata' => [
//                'transactionId' => $invoice->getId(),
//            ]
//        ]);

        $customer_session = $this->stripe->customerSessions->create([
            'customer' => $customerReference,
            'components' => [
                'payment_element' => [
                    'enabled' => true,
                    'features' => [
//                        'payment_method_redisplay' => 'enabled',
                        'payment_method_save' => 'enabled',
                        'payment_method_save_usage' => 'on_session',
//                        'payment_method_remove' => 'enabled',
                    ],
                ],
            ],
        ]);
//        $payment_method_types = ['card'];
//        $response = $this->gateway->createSetupIntent([
//            'customerReference' => $customerReference,
//            'customer' => $customerReference,
//            'payment_method_types' => $payment_method_types,
//            'automatic_payment_methods' => ['enabled' => true],
//        ])->send();
        return [
            'payment_client_secret' => $paymentIntent->client_secret,
            'client_secret' => $setupIntent->client_secret,
            'customer_session_client_secret' => $customer_session->client_secret,
        ];
        if ($response->isSuccessful()) {
            return $response->getData()['client_secret'];
        }

//        throw new RuntimeException($response->getMessage());
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
            'paymentMethod' => $token,
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
                'capture_method' => 'manual',
            ], $ignore3dSecure))->send();
        } catch (Exception $exception) {
            throw new MerchantException('Failed to authorize a payment card: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception);
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
            throw new MerchantException('Failed to cancel a card authorization: ' . $exception->getMessage(),
                $exception->getCode(),
                $exception);
        }
    }
}
