<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\merchants\yandexkassa;

use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\AbstractMerchant;
use hiqdev\php\merchant\merchants\HostedPaymentPageMerchantInterface;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use Money\Currency;
use Omnipay\YandexKassa\Gateway;
use Omnipay\YandexKassa\Message\CaptureResponse;
use Omnipay\YandexKassa\Message\DetailsResponse;

/**
 * Class YandexKassaMerchant.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class YandexKassaMerchant extends AbstractMerchant implements HostedPaymentPageMerchantInterface
{
    /**
     * @var Gateway
     */
    protected $gateway;

    protected function createGateway()
    {
        return $this->gatewayFactory->build('YandexKassa', [
            'shopId' => $this->credentials->getPurse(),
            'secret' => $this->credentials->getKey1(),
        ]);
    }

    /**
     * @param InvoiceInterface $invoice
     * @return RedirectPurchaseResponse
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function requestPurchase(InvoiceInterface $invoice)
    {
        /**
         * @var \Omnipay\YandexKassa\Message\PurchaseResponse $response
         */
        $response = $this->gateway->purchase([
            'transactionId' => $invoice->getId(),
            'description' => $invoice->getDescription(),
            'amount' => $this->moneyFormatter->format($invoice->getAmount()),
            'currency' => $invoice->getCurrency()->getCode(),
            'returnUrl' => $invoice->getReturnUrl(),
        ])->send();

        return (new RedirectPurchaseResponse($response->getRedirectUrl(), $response->getRedirectData()))
            ->setMethod($response->getRedirectMethod());
    }

    /**
     * @param array $data
     * @return CompletePurchaseResponse
     */
    public function completePurchase($data)
    {
        $notification = $this->gateway->notification($data)->send();
        /** @var DetailsResponse $details */
        $details = $this->gateway->details([
            'transactionReference' => $notification->getTransactionReference()
        ])->send();
        /** @var CaptureResponse $response */
        $response = $this->gateway->capture([
            'transactionId' => $details->getTransactionId(),
            'transactionReference' => $details->getTransactionReference(),
            'amount' => $details->getAmount(),
            'currency' => $details->getCurrency(),
        ])->send();

        return (new CompletePurchaseResponse())
            ->setIsSuccessful($response->isSuccessful())
            ->setAmount($this->moneyParser->parse($response->getAmount(), new Currency($response->getCurrency())))
            ->setTransactionReference($response->getTransactionReference())
            ->setTransactionId($response->getTransactionId())
            ->setPayer($response->getPayer())
            ->setTime($response->getPaymentDate());
    }
}
