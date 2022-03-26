<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit\merchants\bitpay;

use hiqdev\php\merchant\merchants\bitpay\BitPayMerchant;
use hiqdev\php\merchant\response\CompletePurchaseResponse;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;

class BitPayMerchantTest extends AbstractMerchantTest
{
    /** @var BitPayMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new BitPayMerchant(
            $this->getCredentials(),
            $this->getGatewayFactory(),
            $this->getMoneyFormatter(),
            $this->getMoneyParser()
        );
    }

    protected function getCredentials()
    {
        return parent::getCredentials()
            ->setKey1('8ZerGR1iWwH3iPchhCsSQba6rvHyPQw9Dx1jiYR2J4vX');
    }

    public function testCredentialsWereMappedCorrectly()
    {
        $gatewayPropertyReflection = (new \ReflectionObject($this->merchant))->getProperty('gateway');
        $gatewayPropertyReflection->setAccessible(true);

        /** @var \Omnipay\BitPay\Gateway $gateway */
        $gateway = $gatewayPropertyReflection->getValue($this->merchant);

        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getToken());
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);
        $this->assertStringContainsString('https://test.bitpay.com/invoice?id=', $purchaseResponse->getRedirectUrl());
        $this->assertArrayHasKey('id', $purchaseResponse->getRedirectData());
    }

    public function testCompletePurchase()
    {
        $completePurchaseResponse = $this->merchant->completePurchase([
            'id' => 'S4hVo7z6XZQ3yDUyHyUJD7',
        ]);

        $this->assertInstanceOf(CompletePurchaseResponse::class, $completePurchaseResponse);
        $this->assertTrue($completePurchaseResponse->getIsSuccessful());
        $this->assertSame('12345ASD67890sd', $completePurchaseResponse->getTransactionId());
        $this->assertSame('S4hVo7z6XZQ3yDUyHyUJD7', $completePurchaseResponse->getTransactionReference());
        $this->assertTrue((new Money(1465, new Currency('USD')))->equals($completePurchaseResponse->getAmount()));
        $this->assertTrue((new Money(0, new Currency('USD')))->equals($completePurchaseResponse->getFee()));
        $this->assertSame('USD', $completePurchaseResponse->getCurrency()->getCode());
        $this->assertEquals(new \DateTime('2022-03-14 08:47:58'), $completePurchaseResponse->getTime());
    }
}
