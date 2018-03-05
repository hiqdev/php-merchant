<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\merchants\twocheckoutplus;

use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\AbstractMerchant;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use Money\Currency;
use Money\Money;
use Omnipay\TwoCheckoutPlus\Gateway;

/**
 * Class TwoCheckoutPlusMerchant.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class TwoCheckoutPlusMerchant extends AbstractMerchant
{
    /**
     * @var Gateway
     */
    protected $gateway;

    protected function createGateway()
    {
        return $this->gatewayFactory->build('TwoCheckoutPlus', [
            'account_number' => $this->credentials->getPurse(),
            'secret_word'  => $this->credentials->getKey1(),
            'testMode' => $this->credentials->isTestMode(),
            'demoMode' => $this->credentials->isTestMode(),
        ]);
    }

    /**
     * @param InvoiceInterface $invoice
     * @return RedirectPurchaseResponse
     */
    public function requestPurchase(InvoiceInterface $invoice)
    {
        $gateway = clone $this->gateway;
        $gateway->setCart([
            [
                'product' => 'product',
                'description' => $invoice->getDescription(),
                'price' => $this->moneyFormatter->format($invoice->getAmount()),
                'quantity' => 1,
            ],
        ]);
        /**
         * @var \Omnipay\TwoCheckoutPlus\Message\PurchaseResponse
         */
        $response = $this->gateway->purchase([
            'transactionId' => $invoice->getId(),
            'currency' => $invoice->getCurrency()->getCode(),
            'returnUrl' => $invoice->getReturnUrl(),
        ])->send();

        return (new RedirectPurchaseResponse($response->getRedirectUrl(), []))->setMethod('GET');
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
            ->setTime(new \DateTime($response->getData()['LMI_SYS_TRANS_DATE']));
    }
}
