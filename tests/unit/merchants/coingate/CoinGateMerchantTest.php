<?php

namespace hiqdev\php\merchant\tests\unit\merchants\coingate;

use Omnipay\Common\Http\Client;
use GuzzleHttp\Psr7\Response;
use hiqdev\php\merchant\merchants\coingate\CoinGateMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;

class CoinGateMerchantTest extends AbstractMerchantTest
{
    /** @var CoinGateMerchant */
    protected $merchant;

    public function setUp(): void
    {
        $this->markTestSkipped('CoinGate merchant support is discontinued');
    }

    protected function buildMerchant()
    {
        return new CoinGateMerchant(
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

        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getApiKey());
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $this->markTestSkipped('Does not work.');

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);
        $this->assertSame('https://secure.payinspect.com/post', $purchaseResponse->getRedirectUrl());
        \DMS\PHPUnitExtensions\ArraySubset\Assert::assertArraySubset([
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
                return new Response(200, [], '{"payment_url": "https://secure.payinspect.com/post", "payment": "CC", "url": "https://example.com/return", "error_url": "https://example.com/cancel", "sign": "12dd7800b6517c1df5b6eee5efcef67b"}');
            }
        };
    }

    protected function getCredentials()
    {
        return parent::getCredentials()
            ->setKey1('q-oRs-HPzZyeJu8WzgoMTqkSuaDq-6RftTxNJHx8');
    }

    public function testCompletePurchase()
    {
        $this->markTestSkipped('Not implemented yet.');
    }
}
