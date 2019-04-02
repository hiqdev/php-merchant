<?php

namespace hiqdev\php\merchant\tests\unit\merchants\coingate;

use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;
use hiqdev\php\merchant\merchants\coingate\CoinGateMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;

class IkajoMerchantTest extends AbstractMerchantTest
{
    /** @var CoinGateMerchant */
    protected $merchant;

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

    protected function getCredentials()
    {
        return parent::getCredentials()
            ->setKey1('q-oRs-HPzZyeJu8WzgoMTqkSuaDq-6RftTxNJHx8');
    }

    public function testCompletePurchase()
    {
    }
}
