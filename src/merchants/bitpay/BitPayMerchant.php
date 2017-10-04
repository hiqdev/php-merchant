<?php

namespace hiqdev\php\merchant\merchants\bitpay;

use hiqdev\php\merchant\credentials\CredentialsInterface;
use hiqdev\php\merchant\factories\GatewayFactoryInterface;
use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\MerchantInterface;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use Money\Currency;
use Omnipay\BitPay\Gateway;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Message\ResponseInterface;

/**
 * Class BitPayAdapter
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class BitPayMerchant implements MerchantInterface
{
    /**
     * @var GatewayInterface|Gateway
     */
    protected $gateway;
    /**
     * @var CredentialsInterface
     */
    private $credentials;
    /**
     * @var GatewayFactoryInterface
     */
    private $gatewayFactory;

    public function __construct(CredentialsInterface $credentials, GatewayFactoryInterface $gatewayFactory)
    {
        $this->credentials = $credentials;
        $this->gatewayFactory = $gatewayFactory;
        $this->gateway = $this->gatewayFactory->build('BitPay', [
            'purse' => $this->credentials->getKey1(),
            'secret'  => $this->credentials->getKey2(),
            'secret2' => $this->credentials->getKey3(),
        ]);
    }

    /**
     * @param InvoiceInterface $invoice
     * @return RedirectPurchaseResponse
     */
    public function requestPurchase(InvoiceInterface $invoice)
    {
        /**
         * @var \Omnipay\BitPay\Message\PurchaseResponse $response
         */
        $response = $this->gateway->purchase([
            'transactionReference' => $invoice->getId(),
            'description' => $invoice->getDescription(),
            'amount' => $invoice->getAmount(),
            'currency' => $invoice->getCurrency()->getCode(),
            'returnUrl' => $invoice->getReturnUrl(),
            'notifyUrl' => $invoice->getNotifyUrl(),
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
        /** @var \Omnipay\BitPay\Message\CompletePurchaseResponse $response */
        $response = $this->gateway->completePurchase($data)->send();

        $this->verifyCompetePurchaseResponse($response);

        return (new CompletePurchaseResponse())
            ->setIsSuccessful($response->isSuccessful())
            ->setCurrency(new Currency($response->getCurrency()))
            ->setAmount($response->getAmount())
            ->setFee($response->getFee())
            ->setTransactionReference($response->getTransactionReference())
            ->setTransactionId($response->getTransactionId())
            ->setPayer($response->getPayer())
            ->setTime(new \DateTime($response->getTime()));
    }

    /**
     * @param ResponseInterface $response
     */
    protected function verifyCompetePurchaseResponse(ResponseInterface $response)
    {
        (new CompletePurchaseResponseVerifier($this, $response))->verify();
    }

    public function getCredentials(): CredentialsInterface
    {
        return $this->credentials;
    }
}
