<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit\merchants\epayservice;

use hiqdev\php\merchant\merchants\epayments\EpaymentsMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;
use Omnipay\ePayments\Gateway;
use Omnipay\ePayments\Message\DetailsResponse;

/**
 * Class EpaymentsMerchantTest
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class EpaymentsMerchantTest extends AbstractMerchantTest
{
    /** @var EpaymentsMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new class($this->getCredentials(), $this->getGatewayFactory(), $this->getMoneyFormatter(), $this->getMoneyParser()) extends EpaymentsMerchant
        {
            protected function fetchOrderDetails(string $orderId): DetailsResponse
            {
                $request = $this->gateway->details([
                    'orderId' => $orderId,
                    'access_token' => 'we are not going to use it',
                ]);

                return new DetailsResponse($request, [
                    'errorCode' => '0',
                    'orders' => [
                        [
                            'orderId' => $orderId,
                            'extraId' => 'not used',
                            'state' => 'Paid',
                            'creationDate' => '2018-07-23T19:02:19.6925212+01:00',
                            'payDate' => '2018-07-23T19:03:19.6925212+01:00',
                            'purse' => 'abc-DEF-123',
                            'amount' => 399.12,
                            'currency' => 'usd',
                            'details' => 'foo bar baz',
                            'errorCode' => 0,
                            'paymentTransactionId' => 'd41d8cd98f00b204e9800998ecf8427e',
                        ]
                    ],
                ]);
            }
        };
    }

    public function testCredentialsWereMappedCorrectly()
    {
        $gatewayPropertyReflection = (new \ReflectionObject($this->merchant))->getProperty('gateway');
        $gatewayPropertyReflection->setAccessible(true);
        /** @var Gateway $gateway */
        $gateway = $gatewayPropertyReflection->getValue($this->merchant);

        $this->assertSame($this->getCredentials()->getPurse(), $gateway->getPartnerId());
        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getSecret());
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);

        $this->assertSame('https://api.epayments.com/merchant/prepare', $purchaseResponse->getRedirectUrl());

        $data = $purchaseResponse->getRedirectData();

        $this->assertArraySubset([
            'orderid' => $invoice->getId(),
            'partnerid' => $this->getCredentials()->getPurse(),
            'amount' => $this->getMoneyFormatter()->format($invoice->getAmount()),
            'currency' => $invoice->getAmount()->getCurrency()->getCode(),
            'successurl' => $invoice->getReturnUrl(),
            'declineurl' => $invoice->getCancelUrl(),
        ], $data);

        $this->assertNotEmpty($data['sign']);
    }

    public function testCompletePurchase()
    {
        $_POST = [
            'orderId' => '123',
            'transactionId' => 'sampleTransactionId',
            'sign' => '3575a186ee3ee9bbe28a19c95d27f662',
        ];

        $this->merchant = $this->buildMerchant();
        $completePurchaseResponse = $this->merchant->completePurchase([]);

        $this->assertInstanceOf(\hiqdev\php\merchant\response\CompletePurchaseResponse::class, $completePurchaseResponse);
        $this->assertTrue($completePurchaseResponse->getIsSuccessful());
        $this->assertSame('123', $completePurchaseResponse->getTransactionId());
        $this->assertSame('d41d8cd98f00b204e9800998ecf8427e', $completePurchaseResponse->getTransactionReference());
        $this->assertSame('d41d8cd98f00b204e9800998ecf8427e', $completePurchaseResponse->getPayer());
        $this->assertTrue((new Money(39912, new Currency('USD')))->equals($completePurchaseResponse->getAmount()));
        $this->assertTrue((new Money(0, new Currency('USD')))->equals($completePurchaseResponse->getFee()));
        $this->assertSame('USD', $completePurchaseResponse->getCurrency()->getCode());
        $this->assertInstanceOf(\DateTime::class, $completePurchaseResponse->getTime());
    }
}
