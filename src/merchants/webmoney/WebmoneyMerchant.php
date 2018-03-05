<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\merchants\webmoney;

use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\AbstractMerchant;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use Money\Currency;
use Money\Money;
use Omnipay\WebMoney\Gateway;

/**
 * Class WebmoneyMerchant.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class WebmoneyMerchant extends AbstractMerchant
{
    /**
     * @var Gateway
     */
    protected $gateway;

    protected function createGateway()
    {
        return $this->gatewayFactory->build('WebMoney', [
            'merchantPurse' => $this->credentials->getPurse(),
            'secretKey'  => $this->credentials->getKey1(),
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
         * @var \Omnipay\WebMoney\Message\PurchaseResponse
         */
        $response = $this->gateway->purchase([
            'transactionId' => $invoice->getId(),
            'description' => $invoice->getDescription(),
            'amount' => $this->moneyFormatter->format($invoice->getAmount()),
            'currency' => $invoice->getCurrency()->getCode(),
            'returnUrl' => $invoice->getReturnUrl(),
            'returnMethod' => $invoice->getReturnMethod(),
            'notifyUrl' => $invoice->getNotifyUrl(),
            'notifyMethod' => $invoice->getNotifyMethod(),
            'cancelUrl' => $invoice->getCancelUrl(),
            'cancelMethod' => $invoice->getCancelMethod(),
        ])->send();

        return new RedirectPurchaseResponse($response->getRedirectUrl(), $response->getRedirectData());
    }

    /**
     * @param array $data
     * @return CompletePurchaseResponse
     */
    public function completePurchase($data)
    {
        /** @var \Omnipay\WebMoney\Message\CompletePurchaseResponse $response */
        $response = $this->gateway->completePurchase($data)->send();

        return (new CompletePurchaseResponse())
            ->setIsSuccessful($response->isSuccessful())
            ->setAmount($this->moneyParser->parse($response->getAmount(), $response->getCurrency()))
            ->setFee(new Money(0, new Currency($response->getCurrency())))
            ->setTransactionReference($response->getTransactionReference())
            ->setTransactionId($response->getTransactionId())
            ->setPayer($response->getData()['LMI_PAYER_PURSE'])
            ->setTime(
                (new \DateTime($response->getData()['LMI_SYS_TRANS_DATE'], new \DateTimeZone('Europe/Moscow')))
                ->setTimezone(new \DateTimeZone('UTC'))
            );
    }
}
