<?php

declare(strict_types=1);

/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2024, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit\merchants\cryptomus;

use DateTime;
use GuzzleHttp\Psr7\Message;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use hiqdev\php\merchant\merchants\cryptomus\CryptomusMerchant;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Http\Discovery\Psr18Client;
use Http\Discovery\Psr18ClientDiscovery;
use Http\Discovery\Strategy\MockClientStrategy;
use Http\Mock\Client as MockClient;
use Money\Currency;
use Money\Money;
use Omnipay\Common\Http\Client;
use Symfony\Component\HttpClient\Response\MockResponse;

class CryptomusMerchantTest extends AbstractMerchantTest
{
    /** @var CryptomusMerchant */
    protected $merchant;

    private MockClient $httpClientMock;

    public function setUp(): void
    {
        Psr18ClientDiscovery::prependStrategy(MockClientStrategy::class);

        parent::setUp();
    }

    protected function buildMerchant()
    {
        return new CryptomusMerchant(
            $this->getCredentials(),
            $this->getGatewayFactory(),
            $this->getMoneyFormatter(),
            $this->getMoneyParser()
        );
    }

    protected function getCredentials()
    {
        return parent::getCredentials()
                     ->setPurse('788de464-d530-4444-9ecb-2254411b35ad')
                     ->setKey1(
                         'QKF0FiDuzsO6imz6egMTowYNV1YfjmRdtTMWugDqhCtW8AttHZpXeea9wh0uLZp3VJrEQ1L1pR25gl7o2CDpzuE1DTrQgSmEX02tMrt6QiiNXlMXoEQYw860YfZlCLyZ'
                     );
    }

    public function testCredentialsWereMappedCorrectly()
    {
        $gatewayPropertyReflection = (new \ReflectionObject($this->merchant))->getProperty('gateway');
        $gatewayPropertyReflection->setAccessible(true);

        /** @var \Omnipay\Cryptomus\Gateway $gateway */
        $gateway = $gatewayPropertyReflection->getValue($this->merchant);

        $this->assertSame($this->getCredentials()->getPurse(), $gateway->getMerchantUUID());
        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getPaymentKey());
    }

    protected function buildHttpClient()
    {
        $this->httpClientMock = Psr18ClientDiscovery::find();
        return new Client($this->httpClientMock);
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $this->httpClientMock->addResponse(
            (new Response())
                ->withBody(
                    Utils::streamFor(json_encode([
                        'state' => 0,
                        'result' => [
                            'url' => $url = 'https://pay.cryptomus.com/pay/e1bf6e4a-4465-40b4-849e-60dfe7bced49',
                        ]
                    ]))
                )
        );

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);
        $this->assertStringContainsString($url, $purchaseResponse->getRedirectUrl());
    }

    public function testCompletePurchase()
    {
        $this->httpClientMock->addResponse(
            Message::parseResponse(file_get_contents(__DIR__ . '/Mock/InfoResponse.txt'))
        );

        $_POST = json_decode(
            file_get_contents(__DIR__ . '/Mock/Webhook.json'), true
        );

        $this->merchant = $this->buildMerchant();
        $completePurchaseResponse = $this->merchant->completePurchase(['transactionId' => 'rGrIxUZFVknrFwbYvB']);

        $this->assertInstanceOf(CompletePurchaseResponse::class, $completePurchaseResponse);
        $this->assertTrue($completePurchaseResponse->getIsSuccessful());
        $this->assertSame('e1bf6e4a-4465-40b4-849e-60dfe7bced49', $completePurchaseResponse->getTransactionId());
        $this->assertSame('rGrIxUZFVknrFwbYvB', $completePurchaseResponse->getTransactionReference());
        $this->assertTrue((new Money(1000, new Currency('USD')))->equals($completePurchaseResponse->getAmount()));
        $this->assertTrue((new Money(6, new Currency('USD')))->equals($completePurchaseResponse->getFee()));
        $this->assertSame('USD', $completePurchaseResponse->getCurrency()->getCode());
        $this->assertEquals(new DateTime('2024-03-27T10:51:48.000000+0000'), $completePurchaseResponse->getTime());
    }
}
