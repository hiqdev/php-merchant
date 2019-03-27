<?php

namespace hiqdev\php\merchant\merchants\coingate;

use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\AbstractMerchant;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use Omnipay\CoinGate\Gateway;

/**
 * Class CoinGateMerchant.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CoinGateMerchant extends AbstractMerchant
{
    /**
     * @var Gateway
     */
    protected $gateway;

    protected function createGateway()
    {
        return $this->gatewayFactory->build('CoinGate', [
            'apiKey'  => $this->credentials->getKey1(),
           ]);
    }

    /**
     * @param InvoiceInterface $invoice
     * @return RedirectPurchaseResponse
     */
    public function requestPurchase(InvoiceInterface $invoice)
    {
        /**
         * @var \Omnipay\CoinGate\Message\PurchaseResponse $response
         */
        $response = $this->gateway->purchase([
            'transactionId' => $invoice->getId(),
            'currency' => $invoice->getCurrency()->getCode(),
            'description' => $invoice->getDescription(),
            'amount' => $this->moneyFormatter->format($invoice->getAmount()),
            'returnUrl' => $invoice->getReturnUrl(),
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
        /** @var \Omnipay\CoinGate\Message\CompletePurchaseResponse $response */
        $response = $this->gateway->completePurchase($data)->send();

        return (new CompletePurchaseResponse())
            ->setIsSuccessful($response->isSuccessful())
            ->setAmount($this->moneyParser->parse($response->getAmount(), $response->getCurrency()))
            ->setTransactionReference($response->getTransactionReference())
            ->setTransactionId($response->getTransactionId())
            ->setPayer($response->getPayer())
            ->setTime(new \DateTime($response->getTime()));
    }
}
