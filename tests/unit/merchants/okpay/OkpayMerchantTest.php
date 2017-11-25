<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit\merchants\paypal;

use hiqdev\php\merchant\merchants\okpay\OkpayMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;
use Omnipay\OKPAY\Gateway;
use Omnipay\OKPAY\Message\CompletePurchaseRequest;

class OkpayMerchantTest extends AbstractMerchantTest
{
    /** @var OkpayMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new OkpayMerchant(
            $this->getCredentials(),
            $this->getGatewayFactory(),
            $this->getMoneyFormatter(),
            $this->getMoneyParser()
        );
    }

    public function testCredentialsWereMappedCorrectly()
    {
        $gatewayPropertyReflection = (new \ReflectionObject($this->merchant))->getProperty('gateway');
        $gatewayPropertyReflection->setAccessible(true);
        $gateway = $gatewayPropertyReflection->getValue($this->merchant);

        $this->assertSame($this->getCredentials()->getPurse(), $gateway->getPurse());
        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getSecret());
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);
        $this->assertSame('https://www.okpay.com/process.html', $purchaseResponse->getRedirectUrl());
        $this->assertArraySubset([
            'ok_receiver' => $this->getCredentials()->getPurse(),
            'ok_item_1_name' => $invoice->getDescription(),
            'ok_currency' => $invoice->getCurrency()->getCode(),
            'ok_item_1_price' => $this->getMoneyFormatter()->format($invoice->getAmount()),
            'ok_invoice' => $invoice->getId(),
            'ok_ipn' => $invoice->getNotifyUrl(),
            'ok_return_success' => $invoice->getReturnUrl(),
            'ok_return_fail' => $invoice->getCancelUrl(),
            'ok_fees' => 1,
        ], $purchaseResponse->getRedirectData());
    }

    public function testCompletePurchase()
    {
        $_POST = [
            'ok_txn_status' => 'completed',
            'ok_txn_id'  => '123456789',
            'ok_item_1_name' => 'Test Transaction long description',
            'ok_txn_gross' => '10.99',
            'ok_txn_currency' => 'USD',
            'ok_txn_fee' => '0.00',
            'ok_receiver' => $this->merchant->getCredentials()->getPurse(),
            'ok_invoice' => '123456',
            'ok_txn_datetime' => '2017-10-05T01:02:20',
        ];

        $gatewayMock = $this->getMockBuilder(Gateway::class)
            ->setMethods(['completePurchase'])->getMock();

        $httpClient = $this->buildHttpClient();
        $gatewayMock->method('completePurchase')->willReturn(
            new class($httpClient, \Symfony\Component\HttpFoundation\Request::createFromGlobals()) extends CompletePurchaseRequest {
                public function getData()
                {
                    return $this->httpRequest->request->all();
                }
            }
        );

        $merchantGatewayReflection = (new \ReflectionObject($this->merchant));
        $gatewayPropertyReflection= $merchantGatewayReflection->getProperty('gateway');
        $gatewayPropertyReflection->setAccessible(true);
        $gatewayPropertyReflection->setValue($this->merchant, $gatewayMock);

        $completePurchaseResponse = $this->merchant->completePurchase([]);

        $this->assertInstanceOf(\hiqdev\php\merchant\response\CompletePurchaseResponse::class, $completePurchaseResponse);
        $this->assertTrue($completePurchaseResponse->getIsSuccessful());
        $this->assertSame('123456', $completePurchaseResponse->getTransactionId());
        $this->assertSame('123456789', $completePurchaseResponse->getTransactionReference());
        $this->assertTrue((new Money(1099, new Currency('USD')))->equals($completePurchaseResponse->getAmount()));
        $this->assertTrue((new Money(0, new Currency('USD')))->equals($completePurchaseResponse->getFee()));
        $this->assertSame('USD', $completePurchaseResponse->getCurrency()->getCode());
        $this->assertEquals(new \DateTime($_POST['ok_txn_datetime']), $completePurchaseResponse->getTime());
    }
}
