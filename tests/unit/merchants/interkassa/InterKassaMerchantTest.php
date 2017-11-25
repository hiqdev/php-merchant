<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit\merchants\interkassa;

use hiqdev\php\merchant\merchants\interkassa\InterKassaMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;
use Omnipay\InterKassa\Gateway;

class InterKassaMerchantTest extends AbstractMerchantTest
{
    /** @var InterKassaMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new InterKassaMerchant(
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

        $this->assertSame($this->getCredentials()->getPurse(), $gateway->getCheckoutId());
        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getSignKey());
        $this->assertSame('md5', $gateway->getSignAlgorithm());
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);
        $this->assertSame('https://sci.interkassa.com/', $purchaseResponse->getRedirectUrl());
        $this->assertArraySubset([
            'ik_co_id' => $this->getCredentials()->getPurse(),
            'ik_am' => $this->getMoneyFormatter()->format($invoice->getAmount()),
            'ik_pm_no' => $invoice->getId(),
            'ik_desc' => $invoice->getDescription(),
            'ik_cur' => $invoice->getCurrency()->getCode(),
            'ik_pnd_u' => $invoice->getReturnUrl(),
            'ik_suc_u' => $invoice->getReturnUrl(),
            'ik_fal_u' => $invoice->getCancelUrl(),
            'ik_ia_u' => $invoice->getNotifyUrl(),
        ], $purchaseResponse->getRedirectData());
        $this->assertNotEmpty($purchaseResponse->getRedirectData()['ik_sign']);
    }

    protected function getCredentials()
    {
        return parent::getCredentials()
            ->setPurse('887ac1234c1eeee1488b156b')
            ->setKey1('Zp2zfdSJzbS61L32');
    }

    public function testCompletePurchase()
    {
        $_POST = [
            'ik_co_id'   => '887ac1234c1eeee1488b156b',
            'ik_trn_id'  => 'ID_123456',
            'ik_inv_id'  => 'tax_num_id',
            'ik_pm_no'   => '123',
            'ik_desc'    => 'Test Transaction long description',
            'ik_am'      => '1465.01',
            'ik_cur'     => 'USD',
            'ik_inv_prc' => '2015-12-22 11:07:12',
            'ik_sign'    => 'tl5gP7V8YljF8O6J2YNOHw==',
            'ik_inv_st'  => 'success',
        ];

        $this->merchant = $this->buildMerchant();

        $completePurchaseResponse = $this->merchant->completePurchase([]);

        $this->assertInstanceOf(\hiqdev\php\merchant\response\CompletePurchaseResponse::class, $completePurchaseResponse);
        $this->assertTrue($completePurchaseResponse->getIsSuccessful());
        $this->assertSame('123', $completePurchaseResponse->getTransactionId());
        $this->assertSame('tax_num_id', $completePurchaseResponse->getTransactionReference());
        $this->assertTrue((new Money(146501, new Currency('USD')))->equals($completePurchaseResponse->getAmount()));
        $this->assertTrue((new Money(0, new Currency('USD')))->equals($completePurchaseResponse->getFee()));
        $this->assertSame('USD', $completePurchaseResponse->getCurrency()->getCode());
        $this->assertEquals(new \DateTime('2015-12-22 08:07:12'), $completePurchaseResponse->getTime());
    }
}
