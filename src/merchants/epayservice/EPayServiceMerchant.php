<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\merchants\epayservice;

use GuzzleHttp\Psr7\Response;
use hiqdev\php\merchant\InvoiceInterface;
use hiqdev\php\merchant\merchants\AbstractMerchant;
use hiqdev\php\merchant\merchants\HostedPaymentPageMerchantInterface;
use hiqdev\php\merchant\merchants\SupportsPreflightNotificationRequestInterface;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use Money\Currency;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class EPayServiceMerchant.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class EPayServiceMerchant extends AbstractMerchant implements HostedPaymentPageMerchantInterface, SupportsPreflightNotificationRequestInterface
{
    /**
     * @var \Omnipay\ePayService\Gateway
     */
    protected $gateway;

    /**
     * @return \Omnipay\Common\GatewayInterface
     */
    protected function createGateway()
    {
        return $this->gatewayFactory->build('ePayService', [
            'purse' => $this->credentials->getPurse(),
            'secret'  => $this->credentials->getKey1(),
            'signAlgorithm' => 'sha256',
        ]);
    }

    /**
     * @param InvoiceInterface $invoice
     * @return RedirectPurchaseResponse
     */
    public function requestPurchase(InvoiceInterface $invoice)
    {
        /**
         * @var \Omnipay\ePayService\Gateway
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
            ->setAmount($this->moneyParser->parse($response->getAmount(), new Currency($response->getCurrency())))
            ->setTransactionReference($response->getTransactionReference())
            ->setTransactionId($response->getTransactionId())
            ->setPayer(
                $response->getData()['EPS_ACCNUM']
                ?? $response->getData()['us_client']
                ?? $response->getData()['EPS_WALLETNUM']
                ?? ''
            )
            ->setTime((new \DateTime())->setTimezone(new \DateTimeZone('UTC')));
    }

    public function handlePreflightCompletePurchaseRequest(ServerRequestInterface $request): ?ResponseInterface
    {
        if (($request->getParsedBody()['EPS_REQUEST'] ?? '') !== 'check') {
            return null;
        }

        return new Response(200, [], '"OK"');
    }
}
