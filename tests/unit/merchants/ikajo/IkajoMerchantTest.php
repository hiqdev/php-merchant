<?php

namespace hiqdev\php\merchant\tests\unit\merchants\ikajo;

use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;
use hiqdev\php\merchant\merchants\ikajo\IkajoMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;

class IkajoMerchantTest extends AbstractMerchantTest
{
    /** @var IkajoMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new IkajoMerchant(
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
        $this->assertSame('https://secure.payinspect.com/post', $purchaseResponse->getRedirectUrl());
        $this->assertArraySubset([
            'payment' => 'CC',
            'url' => 'https://example.com/return',
            'error_url' => 'https://example.com/cancel',
            'sign' => '12dd7800b6517c1df5b6eee5efcef67b',
        ], $purchaseResponse->getRedirectData());
    }

    /**
     * Used only for testCompletePurchase.
     */
    protected function buildHttpClient()
    {
        return new class() extends Client {
            public function send($requests)
            {
                return new Response(200, [], 'VERIFIED');
            }
        };
    }

    public function testCompletePurchase()
    {
        $_POST = [
            'id' => 'ikajoId',
            'order' => 'ourId',
            'status' => 'SALE',
            'approval_code' => '00',
            'card' => '123456****1234',
            'date' => '2015-12-12 12:12:12',
            'name' => 'John Doe',
            'email' => 'foo@bar.baz',
            'sign' => '989d1af2b61353be8f00ea332fc35888',
            'amount' => '0.01',
            'currency' => 'USD',
        ];

        $this->merchant = $this->buildMerchant();

        $completePurchaseResponse = $this->merchant->completePurchase([]);

        $this->assertInstanceOf(\hiqdev\php\merchant\response\CompletePurchaseResponse::class, $completePurchaseResponse);
        $this->assertTrue($completePurchaseResponse->getIsSuccessful());
        $this->assertSame('ourId', $completePurchaseResponse->getTransactionId());
        $this->assertSame('ikajoId', $completePurchaseResponse->getTransactionReference());
        $this->assertTrue((new Money(1, new Currency('USD')))->equals($completePurchaseResponse->getAmount()));
        $this->assertTrue((new Money(0, new Currency('USD')))->equals($completePurchaseResponse->getFee()));
        $this->assertSame('USD', $completePurchaseResponse->getCurrency()->getCode());
        $this->assertEquals(new \DateTime('2015-12-12 12:12:12'), $completePurchaseResponse->getTime());
    }
}
