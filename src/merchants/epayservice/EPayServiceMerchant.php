<?php

namespace hiqdev\php\merchant\merchants\epayservice;

use hiqdev\php\merchant\credentials\CredentialsInterface;
use hiqdev\php\merchant\factories\GatewayFactoryInterface;
use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\MerchantInterface;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use Money\MoneyFormatter;
use Money\MoneyParser;

/**
 * Class EPayServiceMerchant
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class EPayServiceMerchant implements MerchantInterface
{
    /**
     * @var \Omnipay\ePayService\Gateway
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
    /**
     * @var MoneyFormatter
     */
    private $moneyFormatter;
    /**
     * @var MoneyParser
     */
    private $moneyParser;

    public function __construct(
        CredentialsInterface $credentials,
        GatewayFactoryInterface $gatewayFactory,
        MoneyFormatter $moneyFormatter,
        MoneyParser $moneyParser
    )
    {
        $this->credentials = $credentials;
        $this->gatewayFactory = $gatewayFactory;
        $this->moneyFormatter = $moneyFormatter;
        $this->moneyParser = $moneyParser;
        $this->gateway = $this->gatewayFactory->build('ePayService', [
            'purse' => $this->credentials->getPurse(),
            'secret'  => $this->credentials->getKey1(),
            'signAlgorithm' => 'sha256'
        ]);
    }

    /**
     * @param InvoiceInterface $invoice
     * @return RedirectPurchaseResponse
     */
    public function requestPurchase(InvoiceInterface $invoice)
    {
        /**
         * @var \Omnipay\Paxum\Message\PurchaseResponse $response
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

        return new RedirectPurchaseResponse($response->getRedirectUrl(), $response->getRedirectData());
    }

    /**
     * @param array $data
     * @return CompletePurchaseResponse
     */
    public function completePurchase($data)
    {
        /** @var \Omnipay\ePayService\Message\CompletePurchaseResponse $response */
        $response = $this->gateway->completePurchase($data)->send();

        return (new CompletePurchaseResponse())
            ->setIsSuccessful($response->isSuccessful())
            ->setAmount($this->moneyParser->parse($response->getAmount(), $response->getCurrency()))
            ->setTransactionReference($response->getTransactionReference())
            ->setTransactionId($response->getTransactionId())
            ->setPayer($response->getData()['EPS_ACCNUM'])
            ->setTime((new \DateTime())->setTimezone(new \DateTimeZone('UTC')));
    }

    /**
     * @return CredentialsInterface
     */
    public function getCredentials(): CredentialsInterface
    {
        return $this->credentials;
    }
}
