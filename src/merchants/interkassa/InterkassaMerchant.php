<?php

namespace hiqdev\php\merchant\merchants\bitpay;

use ahnames\php\oldapi\lib\merchant\AdapterInterface;
use ahnames\php\oldapi\lib\merchant\CredentialsInterface;
use ahnames\php\oldapi\lib\merchant\InvoiceInterface;
use ahnames\php\oldapi\lib\merchant\response\CompletePurchaseResponse;
use ahnames\php\oldapi\lib\merchant\response\RedirectPurchaseResponse;
use Money\Currency;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\InterKassa\Gateway;

/**
 * Class BitPayAdapter
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class InterkassaMerchant implements AdapterInterface
{
    /**
     * @var CredentialsInterface
     */
    private $credentials;

    public function __construct(CredentialsInterface $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @return OmnipayMerchant
     */
    private function interKassa()
    {
        return new Gateway();
    }

    /**
     * @param array $data
     * @return CompletePurchaseResponse
     */
    public function completePurchase($data)
    {
        /** @var \Omnipay\BitPay\Message\CompletePurchaseResponse $response */
        $response = $this->bitPay()->request('completePurchase', $data)->send();

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
     * @param InvoiceInterface $invoice
     * @return RedirectPurchaseResponse
     */
    public function requestPurchase(InvoiceInterface $invoice)
    {
        /**
         * @var \Omnipay\BitPay\Message\PurchaseResponse $response
         */
        $response = $this->bitPay()->request('purchase', [
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
     * @param ResponseInterface $response
     */
    protected function verifyCompetePurchaseResponse(ResponseInterface $response)
    {
        (new CompletePurchaseResponseVerifier($this, $response))->verify();
    }
}
