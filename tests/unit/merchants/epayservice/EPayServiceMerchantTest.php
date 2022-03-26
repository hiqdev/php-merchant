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

use hiqdev\php\merchant\merchants\epayservice\EPayServiceMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;
use Omnipay\ePayService\Gateway;

class EPayServiceMerchantTest extends AbstractMerchantTest
{
    /** @var EPayServiceMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new EPayServiceMerchant(
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
        /** @var Gateway $gateway */
        $gateway = $gatewayPropertyReflection->getValue($this->merchant);

        $this->assertSame($this->getCredentials()->getPurse(), $gateway->getPurse());
        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getSecret());
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);
        $this->assertSame('https://online.epayservices.com/merchant/index.php', $purchaseResponse->getRedirectUrl());
        \DMS\PHPUnitExtensions\ArraySubset\Assert::assertArraySubset([
            'EPS_DESCRIPTION' => $invoice->getDescription(),
            'EPS_GUID' => $this->getCredentials()->getPurse(),
            'EPS_AMOUNT' => $this->getMoneyFormatter()->format($invoice->getAmount()),
            'EPS_RESULT_URL' => $invoice->getNotifyUrl(),
            'EPS_SUCCESS_URL' => $invoice->getReturnUrl(),
            'EPS_FAIL_URL' => $invoice->getCancelUrl(),
        ], $purchaseResponse->getRedirectData());
    }

    public function testCompletePurchase()
    {
        $_POST = [
            'EPS_TRID' => '123',
            'EPS_ACCNUM'  => 'tax_num_id',
            'EPS_AMOUNT' => '10.99',
            'EPS_CURRENCY' => 'USD',
            'EPS_RESULT' => 'done',
            'EPS_GUID' => '9ce29412-3b4b-4f8f-8b05-3f9e9b7fc1d5',
            'MERCHANT_ORDER_ID' => '123',
            'check_key' => 'b586e61468cb85091bad7cd0478942eb',
        ];

        $this->merchant = $this->buildMerchant();
        $completePurchaseResponse = $this->merchant->completePurchase([]);

        $this->assertInstanceOf(\hiqdev\php\merchant\response\CompletePurchaseResponse::class, $completePurchaseResponse);
        $this->assertTrue($completePurchaseResponse->getIsSuccessful());
        $this->assertSame('123', $completePurchaseResponse->getTransactionId());
        $this->assertSame('123', $completePurchaseResponse->getTransactionReference());
        $this->assertTrue((new Money(1099, new Currency('USD')))->equals($completePurchaseResponse->getAmount()));
        $this->assertTrue((new Money(0, new Currency('USD')))->equals($completePurchaseResponse->getFee()));
        $this->assertSame('USD', $completePurchaseResponse->getCurrency()->getCode());
        $this->assertInstanceOf(\DateTime::class, $completePurchaseResponse->getTime());
    }
}
