<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit\merchants\robokassa;

use hiqdev\php\merchant\merchants\freekassa\FreeKassaMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;

class FreeKassaMerchantTest extends AbstractMerchantTest
{
    /** @var FreeKassaMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new FreeKassaMerchant(
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
        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getSecretKey());
        $this->assertSame($this->getCredentials()->getKey2(), $gateway->getSecretKey2());
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);

        $url = 'https://www.free-kassa.ru/merchant/cash.php';
        $this->assertContains($url, $purchaseResponse->getRedirectUrl());

        $this->assertArraySubset([
            'm' => $this->getCredentials()->getPurse(),
            'oa' => $this->getMoneyFormatter()->format($invoice->getAmount()),
            'o' => $invoice->getId(),
            'i' => strtolower($invoice->getCurrency()->getCode()),
            'us_client' => $invoice->getClient(),
            'us_system' => 'freekassa',
            'us_currency' => 'USD',
        ], $purchaseResponse->getRedirectData());
    }

    public function testCompletePurchase()
    {
        $_POST = [
            'MERCHANT_ORDER_ID' => '597ef770b5fcf',
            'P_PHONE' => '',
            'P_EMAIL' => 'silverfire@hiqdev.com',
            'CUR_ID' => '116',
            'AMOUNT' => '625.21',
            'MERCHANT_ID' => '47215',
            'SIGN' => 'a67916164b4167ebeabbdbf5d49e50ab',
            'intid' => '22861661',
            'us_time' => '1501493104',
            'us_client' => 'silverfire',
            'us_system' => 'freekassa',
            'us_currency' => 'USD',
        ];

        $this->merchant = $this->buildMerchant();

        $completePurchaseResponse = $this->merchant->completePurchase([]);

        $this->assertInstanceOf(\hiqdev\php\merchant\response\CompletePurchaseResponse::class, $completePurchaseResponse);
        $this->assertTrue($completePurchaseResponse->getIsSuccessful());
        $this->assertSame('597ef770b5fcf', $completePurchaseResponse->getTransactionId());
        $this->assertSame('22861661', $completePurchaseResponse->getTransactionReference());
        $this->assertTrue((new Money(62521, new Currency('USD')))->equals($completePurchaseResponse->getAmount()));
        $this->assertTrue((new Money(0, new Currency('USD')))->equals($completePurchaseResponse->getFee()));
        $this->assertSame('USD', $completePurchaseResponse->getCurrency()->getCode());
        $this->assertSame('silverfire@hiqdev.com / Bitcoin', $completePurchaseResponse->getPayer());
        $this->assertInstanceOf(\DateTime::class, $completePurchaseResponse->getTime());
    }
}
