<?php
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
use Exception;
use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\AbstractMerchant;
use hiqdev\php\merchant\merchants\HostedPaymentPageMerchantInterface;
use hiqdev\php\merchant\merchants\PaymentCardMerchantInterface;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use Money\Money;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Stripe\Gateway;

/**
 * Class StripeMerchant
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class StripeMerchant extends AbstractMerchant implements HostedPaymentPageMerchantInterface, PaymentCardMerchantInterface
{
    /**
     * @var Gateway
     */
    protected $gateway;

    protected function createGateway()
    {
        return $this->gatewayFactory->build('Stripe\PaymentIntents', [
            'apiKey' => $this->credentials->getKey1(),
        ]);
    }

    public function requestPurchase(InvoiceInterface $invoice)
    {
        $customerReference = $this->fetchCustomerReference($invoice->getClient());
        $clientSecret = $this->fetchClientSecret($customerReference);

        $response = new RedirectPurchaseResponse('', [
            'clientSecret' => $clientSecret,
            'publicKey' => $this->credentials->getKey2()
        ]);
        $response->setMethod('GET');

        return $response;
    }

    public function chargeCard(InvoiceInterface $invoice)
    {
        $customerReference = $this->fetchCustomerReference($invoice->getClient());

        /** @var \Omnipay\Stripe\Message\Response $response */
        $response = $this->gateway->purchase([
            'amount'            => $invoice->getAmount(),
            'currency'          => $invoice->getCurrency()->getCode(),
            'description'       => $invoice->getDescription(),
            'customerReference' => $customerReference,
            'paymentMethod'     => $invoice->getPreferredPaymentMethod(),
            'returnUrl'         => $invoice->getReturnUrl(),
            'confirm'           => true,
        ])->send();

        if ($response->isRedirect()) {
            return new RedirectPurchaseResponse($response->getRedirectUrl(), $response->getRedirectData());
        }

        if ($response->isSuccessful()) {
            return (new CompletePurchaseResponse())
                ->setIsSuccessful(true)
                ->setAmount($invoice->getAmount())
                ->setFee(new Money(0, $invoice->getAmount()->getCurrency()))
                ->setTransactionReference($response->getTransactionReference())
                ->setTransactionId($response->getTransactionId())
                ->setPayer($response->getCustomerReference())
                ->setTime(new DateTime());
        }

        throw new Exception('Neither success nor redirect');
    }

    public function completePurchase($data)
    {
        throw new Exception('Not implemented');
    }

    private function fetchCustomerReference(string $email): string
    {
        $response = $this->gateway->createCustomer(compact('email'))->send();
        if (!$response->isSuccessful()) {
            throw new RuntimeException($response->getMessage());
        }

        return $response->getCustomerReference();
    }

    private function fetchClientSecret(string $customerReference): string
    {
        $response = $this->gateway->createSetupIntent(compact('customerReference'))->send();
        if ($response->isSuccessful()) {
            return $response->getData()['client_secret'];
        }

        throw new RuntimeException($response->getMessage());
    }
}
