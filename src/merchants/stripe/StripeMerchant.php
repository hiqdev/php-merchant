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

use hiqdev\cashew\DTO\PaymentFlow\Card\CardPaymentRequest;
use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\AbstractMerchant;
use hiqdev\php\merchant\merchants\HostedPaymentPageMerchantInterface;
use hiqdev\php\merchant\merchants\PaymentCardMerchantInterface;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
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
        return $this->gatewayFactory->build('Stripe', [
            'apiKey' => $this->credentials->getKey1(),
        ]);
    }

    public function requestPurchase(InvoiceInterface $invoice)
    {
        // TODO: implement

        $response = new RedirectPurchaseResponse('', [
            'clientSecret' => bin2hex(random_bytes(20))
        ]);
        $response->setMethod('GET');

        return $response;
    }

    public function completePurchase($data)
    {
        throw new \Exception('Not implemented');
    }
}
