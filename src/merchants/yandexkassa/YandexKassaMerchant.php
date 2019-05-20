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
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use Omnipay\YandexMoney\P2pGateway;

/**
 * Class YandexKassaMerchant.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class YandexKassaMerchant extends AbstractMerchant
{
    /**
     * @var P2pGateway
     */
    protected $gateway;

    protected function createGateway()
    {
        return $this->gatewayFactory->build('YandexKassa', [
            'shopId' => $this->credentials->getPurse(),
            'secretKey' => $this->credentials->getKey1(),
        ]);
    }

    /**
     * @param InvoiceInterface $invoice
     * @return RedirectPurchaseResponse
     */
    public function requestPurchase(InvoiceInterface $invoice)
    {
        /**
         * @var \Omnipay\YandexKassa\Message\PurchaseResponse
         */
        $response = $this->gateway->purchase([
            'transactionId' => $invoice->getId(),
            'details' => $invoice->getDescription(),
            'amount' => $this->moneyFormatter->format($invoice->getAmount()),
            'currency' => $invoice->getCurrency()->getCode(),
            'returnUrl' => $invoice->getReturnUrl(),
        ])->send();

        return new RedirectPurchaseResponse($response->getRedirectUrl(), $response->getRedirectData());
    }

    /**
     * @param array $data
     * @return CompletePurchaseResponse
     */
    public function completePurchase($data)
    {
        // TODO: Implement
        /** @var \Omnipay\YandexKasssa\Message\CompletePurchaseResponse $response */
        $response = $this->gateway->completePurchase($data)->send();

        return (new CompletePurchaseResponse())
            ->setIsSuccessful($response->isSuccessful())
            ->setAmount($this->moneyParser->parse($response->getAmount(), $response->getCurrency()))
            ->setTransactionReference($response->getTransactionReference())
            ->setTransactionId($response->getTransactionId())
            ->setPayer($response->getData()['sender'] ?? $response->getData()['email'] ?? '')
            ->setTime(
                (new \DateTime($response->getTime(), new \DateTimeZone('Europe/Moscow')))
                    ->setTimezone(new \DateTimeZone('UTC'))
            );
    }
}
