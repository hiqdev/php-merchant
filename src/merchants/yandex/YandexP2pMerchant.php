<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\merchants\yandex;

use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\AbstractMerchant;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use Omnipay\YandexMoney\P2pGateway;

/**
 * Class YandexP2pMerchant.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class YandexP2pMerchant extends AbstractMerchant
{
    /**
     * @var P2pGateway
     */
    protected $gateway;

    protected function createGateway()
    {
        return $this->gatewayFactory->build('YandexMoney_P2p', [
            'account' => $this->credentials->getPurse(),
            'password' => $this->credentials->getKey1(),
            'testMode' => $this->credentials->isTestMode(),
        ]);
    }

    /**
     * @param InvoiceInterface $invoice
     * @return RedirectPurchaseResponse
     */
    public function requestPurchase(InvoiceInterface $invoice)
    {
        /**
         * @var \Omnipay\YandexMoney\Message\p2p\PurchaseResponse
         */
        $response = $this->gateway->purchase([
            'transactionId' => $invoice->getId(),
            'description' => $invoice->getDescription(),
            'amount' => $this->moneyFormatter->format($invoice->getAmount()),
            'currency' => $invoice->getCurrency()->getCode(),
            'returnUrl' => $invoice->getReturnUrl(),
            'notifyUrl' => $invoice->getNotifyUrl(),
            'cancelUrl' => $invoice->getCancelUrl(),
            'method' => 'AC', // https://money.yandex.ru/doc.xml?id=526991
        ])->send();

        return new RedirectPurchaseResponse($response->getRedirectUrl(), $response->getRedirectData());
    }

    /**
     * @param array $data
     * @return CompletePurchaseResponse
     */
    public function completePurchase($data)
    {
        /** @var \Omnipay\YandexMoney\Message\p2p\CompletePurchaseResponse $response */
        $response = $this->gateway->completePurchase($data)->send();

        return (new CompletePurchaseResponse())
            ->setIsSuccessful($response->isSuccessful())
            ->setAmount($this->moneyParser->parse($response->getWithdrawAmount(), $response->getCurrency()))
            ->setFee($this->moneyParser->parse($response->getFee(), $response->getCurrency()))
            ->setTransactionReference($response->getTransactionReference())
            ->setTransactionId($response->getTransactionId())
            ->setPayer($response->getData()['sender'] ?? $response->getData()['email'] ?? '')
            ->setTime(
                (new \DateTime($response->getTime(), new \DateTimeZone('Europe/Moscow')))
                    ->setTimezone(new \DateTimeZone('UTC'))
            );
    }
}
