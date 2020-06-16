<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\merchants\bitpay;

use hiqdev\php\merchant\exceptions\MerchantException;
use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\AbstractMerchant;
use hiqdev\php\merchant\merchants\HostedPaymentPageMerchantInterface;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use Omnipay\BitPay\Gateway;

/**
 * Class BitPayAdapter.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class BitPayMerchant extends AbstractMerchant implements HostedPaymentPageMerchantInterface
{
    /**
     * @return Gateway
     */
    protected function createGateway()
    {
        return $this->gatewayFactory->build('BitPay', [
            'token' => $this->credentials->getKey1(),
            'privateKey'  => $this->credentials->getKey2(),
            'publicKey' => $this->credentials->getKey3(),
            'testMode' => $this->getCredentials()->isTestMode(),
        ]);
    }

    /**
     * @param InvoiceInterface $invoice
     * @return RedirectPurchaseResponse
     */
    public function requestPurchase(InvoiceInterface $invoice)
    {
        /**
         * @var \Omnipay\BitPay\Message\PurchaseResponse
         */
        $response = $this->gateway->purchase([
            'transactionId' => $invoice->getId(),
            'description' => $invoice->getDescription(),
            'amount' => $this->moneyFormatter->format($invoice->getAmount()),
            'currency' => $invoice->getCurrency()->getCode(),
            'returnUrl' => $invoice->getReturnUrl(),
            'notifyUrl' => $invoice->getNotifyUrl(),
            'cancelUrl' => $invoice->getCancelUrl(),
        ])->send();

        if ($response->getRedirectUrl() === null) {
            throw new MerchantException('Failed to request purchase');
        }

        $response = new RedirectPurchaseResponse($response->getRedirectUrl(), $response->getRedirectData());
        $response->setMethod('GET');

        return $response;
    }

    /**
     * @param array $data
     * @return CompletePurchaseResponse
     */
    public function completePurchase($data)
    {
        /** @var \Omnipay\BitPay\Message\CompletePurchaseResponse $response */
        $response = $this->gateway->completePurchase($data)->send();

        return (new CompletePurchaseResponse())
            ->setIsSuccessful($response->isSuccessful())
            ->setAmount($this->moneyParser->parse($response->getAmount(), $response->getCurrency()))
            ->setFee($this->moneyParser->parse($response->getFee(), $response->getCurrency()))
            ->setTransactionReference($response->getTransactionReference())
            ->setTransactionId($response->getTransactionId())
            ->setPayer($response->getPayer())
            ->setTime(new \DateTime($response->getTime()));
    }
}
