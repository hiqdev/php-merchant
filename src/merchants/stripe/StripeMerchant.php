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

use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\AbstractMerchant;
use hiqdev\php\merchant\merchants\HostedPaymentPageMerchantInterface;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Stripe\Gateway;

/**
 * Class StripeMerchant
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class StripeMerchant extends AbstractMerchant implements HostedPaymentPageMerchantInterface
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

    public function completePurchase($data)
    {
        throw new \Exception('Not implemented');
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
