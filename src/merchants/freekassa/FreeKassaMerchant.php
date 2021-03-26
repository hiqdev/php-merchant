<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\merchants\freekassa;

use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\AbstractMerchant;
use hiqdev\php\merchant\merchants\HostedPaymentPageMerchantInterface;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use Omnipay\FreeKassa\Gateway;

/**
 * Class FreeKassaMerchant.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class FreeKassaMerchant extends AbstractMerchant implements HostedPaymentPageMerchantInterface
{
    /**
     * @var Gateway
     */
    protected $gateway;

    protected function createGateway()
    {
        return $this->gatewayFactory->build('FreeKassa', [
            'purse' => $this->credentials->getPurse(),
            'secretKey' => $this->credentials->getKey1(),
            'secretKey2' => $this->credentials->getKey2(),
        ]);
    }

    /**
     * @param InvoiceInterface $invoice
     * @return RedirectPurchaseResponse
     */
    public function requestPurchase(InvoiceInterface $invoice)
    {
        /**
         * @var \Omnipay\FreeKassa\Message\PurchaseResponse
         */
        $response = $this->gateway->purchase([
            'transaction_id' => $invoice->getId(),
            'currency' => $invoice->getCurrency()->getCode(),
            'amount' => $this->moneyFormatter->format($invoice->getAmount()),
            'client' => $invoice->getClient()->login(),
        ])->send();

        return new RedirectPurchaseResponse($response->getRedirectUrl(), $response->getRedirectData());
    }

    /**
     * @param array $data
     * @return CompletePurchaseResponse
     */
    public function completePurchase($data)
    {
        /** @var \Omnipay\FreeKassa\Message\CompletePurchaseResponse $response */
        $response = $this->gateway->completePurchase($data)->send();

        return (new CompletePurchaseResponse())
            ->setIsSuccessful($response->isSuccessful())
            // TODO: !(>_<)! FreeKassa does not indicate currency.
            ->setAmount($this->moneyParser->parse($response->getAmount(), $response->getCurrency() ?? 'RUB'))
            ->setTransactionReference($response->getTransactionReference())
            ->setTransactionId($response->getTransactionId())
            ->setPayer($response->getPayer())
            ->setTime((new \DateTime($response->getTime()))->setTimezone(new \DateTimeZone('UTC')));
    }
}
