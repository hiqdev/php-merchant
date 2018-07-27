<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\merchants\epayments;

use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\AbstractMerchant;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use Omnipay\ePayments\Message\DetailsResponse;
use Omnipay\ePayments\Message\PurchaseResponse;

/**
 * Class EpaymentsMerchant.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 *
 * @property \Omnipay\ePayments\Gateway $gateway
 */
class EpaymentsMerchant extends AbstractMerchant
{
    /**
     * @return \Omnipay\Common\GatewayInterface
     */
    protected function createGateway()
    {
        return $this->gatewayFactory->build('ePayments', [
            'partnerId' => $this->credentials->getPurse(),
            'secret'  => $this->credentials->getKey1(),
        ]);
    }

    /**
     * @param InvoiceInterface $invoice
     * @return RedirectPurchaseResponse
     */
    public function requestPurchase(InvoiceInterface $invoice)
    {
        /**
         * @var PurchaseResponse
         */
        $response = $this->gateway->purchase([
            'orderId' => $invoice->getId(),
            'details' => $invoice->getDescription(),
            'amount' => $this->moneyFormatter->format($invoice->getAmount()),
            'currency' => strtolower($invoice->getCurrency()->getCode()),
            'returnUrl' => $invoice->getReturnUrl(),
            'cancelUrl' => $invoice->getCancelUrl(),
        ])->send();

        return new RedirectPurchaseResponse($response->getRedirectUrl(), $response->getRedirectData());
    }

    /**
     * @param array $data
     * @return CompletePurchaseResponse
     */
    public function completePurchase($data)
    {
        /** @var \Omnipay\ePayments\Message\CompletePurchaseResponse $notification */
        $notification = $this->gateway->completePurchase($data)->send();

        $response = $this->fetchOrderDetails($notification->getOrderId());

        return (new CompletePurchaseResponse())
            ->setIsSuccessful($response->isSuccessful())
            ->setAmount($this->moneyParser->parse($response->getAmount(), $response->getCurrency()))
            ->setTransactionReference($response->getTransactionReference())
            ->setTransactionId($response->getTransactionId())
            ->setPayer($response->getTransactionReference())
            ->setTime($response->getPaymentDate());
    }

    protected function fetchOrderDetails(string $orderId): DetailsResponse
    {
        return $this->gateway->details(['orderId' => $orderId])->send();
    }
}
