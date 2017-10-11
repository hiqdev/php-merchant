<?php

namespace hiqdev\php\merchant\tests\unit\merchants\webmoney;

use hiqdev\php\merchant\Invoice;
use hiqdev\php\merchant\merchants\webmoney\WebmoneyMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;
use Omnipay\WebMoney\Gateway;

class WebMoneyMerchantTest extends AbstractMerchantTest
{
    /** @var WebmoneyMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new WebmoneyMerchant(
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

        $this->assertSame($this->getCredentials()->getPurse(), $gateway->getMerchantPurse());
        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getSecretKey());
        $this->assertSame($this->getCredentials()->isTestMode(), $gateway->getTestMode());
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);
        $this->assertSame('https://merchant.wmtransfer.com/lmi/payment.asp', $purchaseResponse->getRedirectUrl());
        $this->assertArraySubset([
            'LMI_PAYEE_PURSE' => $this->getCredentials()->getPurse(),
            'LMI_PAYMENT_AMOUNT' => $this->getMoneyFormatter()->format($invoice->getAmount()),
            'LMI_PAYMENT_NO' => $invoice->getId(),
            'LMI_PAYMENT_DESC_BASE64' => 'VGVzdCBwdXJjaGFzZQ==',
            'LMI_SIM_MODE' => '2',
            'LMI_RESULT_URL' => $invoice->getNotifyUrl(),
            'LMI_SUCCESS_URL' => $invoice->getReturnUrl(),
            'LMI_SUCCESS_METHOD' => '1',
            'LMI_FAIL_URL' => $invoice->getCancelUrl(),
            'LMI_FAIL_METHOD' => '1',
            'LMI_HOLD' => '0',
        ], $purchaseResponse->getRedirectData());
    }

    protected function getCredentials()
    {
        return parent::getCredentials()
            ->setPurse('Z202718489231')
            ->setKey1('Zp2zfdSJzbS61L32');
    }

    protected function buildInvoice()
    {
        return (new Invoice())
            ->setId(uniqid())
            ->setDescription('Test purchase')
            ->setAmount(new Money(1099, new Currency('USD')))
            ->setReturnUrl('https://example.com/return')
            ->setReturnMethod('POST')
            ->setNotifyUrl('https://example.com/notify')
            ->setNotifyMethod('POST')
            ->setCancelUrl('https://example.com/cancel')
            ->setCancelMethod('POST');
    }

    public function testCompletePurchase()
    {
        $_POST = [
            'LMI_MODE' => '1',
            'LMI_PAYMENT_AMOUNT' => '24.50',
            'LMI_PAYEE_PURSE' => 'Z202718489231',
            'LMI_PAYMENT_NO' => '4181adfm',
            'LMI_PAYER_WM' => '639253354549',
            'LMI_PAYER_PURSE' => 'Z954135551122',
            'LMI_PAYER_COUNTRYID' => 'RU',
            'LMI_PAYER_PCOUNTRYID' => 'RU',
            'LMI_SYS_INVS_NO' => '709460654',
            'LMI_SYS_TRANS_NO' => '1498091225',
            'LMI_SYS_TRANS_DATE' => '20171011 10:40:17',
            'LMI_HASH' => '6C50C8C507BFE6DBD7F134B5BE6F9414',
            'LMI_PAYMENT_DESC' => 'deposit me',
            'LMI_LANG' => 'ru-RU',
            'LMI_DBLCHK' => 'SMS',
        ];

        $this->merchant = $this->buildMerchant();

        $completePurchaseResponse = $this->merchant->completePurchase([]);

        $this->assertInstanceOf(\hiqdev\php\merchant\response\CompletePurchaseResponse::class, $completePurchaseResponse);
        $this->assertTrue($completePurchaseResponse->getIsSuccessful());
        $this->assertSame('4181adfm', $completePurchaseResponse->getTransactionId());
        $this->assertSame('1498091225', $completePurchaseResponse->getTransactionReference());
        $this->assertTrue((new Money(2450, new Currency('USD')))->equals($completePurchaseResponse->getAmount()));
        $this->assertTrue((new Money(0, new Currency('USD')))->equals($completePurchaseResponse->getFee()));
        $this->assertSame('USD', $completePurchaseResponse->getCurrency()->getCode());
        $this->assertEquals(new \DateTime('2017-10-11 10:40:17'), $completePurchaseResponse->getTime());
        $this->assertEquals('Z954135551122', $completePurchaseResponse->getPayer());
    }
}
