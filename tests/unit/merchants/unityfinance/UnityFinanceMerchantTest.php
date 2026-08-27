<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2026, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit\merchants\unityfinance;

use hiqdev\php\merchant\merchants\unityfinance\UnityFinanceMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;
use Omnipay\InterKassa\UnityFinanceGateway;

class UnityFinanceMerchantTest extends AbstractMerchantTest
{
    /** @var UnityFinanceMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new UnityFinanceMerchant(
            $this->getCredentials(),
            $this->getGatewayFactory(),
            $this->getMoneyFormatter(),
            $this->getMoneyParser()
        );
    }

    protected function getCredentials()
    {
        return parent::getCredentials()
            ->setPurse('887ac1234c1eeee1488b156b')
            ->setKey1('Zp2zfdSJzbS61L32');
    }

    private function getGateway(UnityFinanceMerchant $merchant): UnityFinanceGateway
    {
        $gatewayPropertyReflection = (new \ReflectionObject($merchant))->getProperty('gateway');
        $gatewayPropertyReflection->setAccessible(true);

        return $gatewayPropertyReflection->getValue($merchant);
    }

    public function testCredentialsWereMappedCorrectly()
    {
        $gateway = $this->getGateway($this->merchant);

        $this->assertSame($this->getCredentials()->getPurse(), $gateway->getCheckoutId());
        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getSignKey());
    }

    public function testSignAlgorithmDefaultsToSha256WhenSupportHasNotConfiguredOne()
    {
        $this->assertNull($this->getCredentials()->getKey2());
        $this->assertSame('sha256', $this->getGateway($this->merchant)->getSignAlgorithm());
    }

    public function testSignAlgorithmIsConfigurableViaCredentialsSecondKey()
    {
        $merchant = new UnityFinanceMerchant(
            $this->getCredentials()->setKey2('md5'),
            $this->getGatewayFactory(),
            $this->getMoneyFormatter(),
            $this->getMoneyParser()
        );

        $this->assertSame('md5', $this->getGateway($merchant)->getSignAlgorithm());
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);
        $this->assertSame('https://old-pay.unityfinance.com', $purchaseResponse->getRedirectUrl());
        \DMS\PHPUnitExtensions\ArraySubset\Assert::assertArraySubset([
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

    private function assertCompletePurchaseSucceeds($completePurchaseResponse)
    {
        $this->assertInstanceOf(\hiqdev\php\merchant\response\CompletePurchaseResponse::class, $completePurchaseResponse);
        $this->assertTrue($completePurchaseResponse->getIsSuccessful());
        $this->assertSame('123', $completePurchaseResponse->getTransactionId());
        $this->assertSame('tax_num_id', $completePurchaseResponse->getTransactionReference());
        $this->assertTrue((new Money(146501, new Currency('USD')))->equals($completePurchaseResponse->getAmount()));
        $this->assertSame('USD', $completePurchaseResponse->getCurrency()->getCode());
    }

    public function testCompletePurchaseWithDefaultSha256Algorithm()
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
            'ik_pw_via'  => 'visa',
            'ik_sign'    => '9oHj7FytS3x3L4HhX8htIKOZD6k1iu0ju6W2t+6r+DE=',
            'ik_inv_st'  => 'success',
        ];

        $this->merchant = $this->buildMerchant();

        $this->assertCompletePurchaseSucceeds($this->merchant->completePurchase([]));
    }

    /**
     * Regression test for the live UnityFinance checkout (purse
     * 53dfab7bbf4efc7b7b6421ff), which turned out to be configured for MD5, not the
     * SHA256 default — a real notification failed signature validation until this was
     * made configurable via credentials.getKey2().
     */
    public function testCompletePurchaseWithMd5AlgorithmConfiguredBySupport()
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
            'ik_pw_via'  => 'visa',
            'ik_sign'    => 'xHQBxFIGn/ig4Ihl73iRIQ==',
            'ik_inv_st'  => 'success',
        ];

        $merchant = new UnityFinanceMerchant(
            $this->getCredentials()->setKey2('md5'),
            $this->getGatewayFactory(),
            $this->getMoneyFormatter(),
            $this->getMoneyParser()
        );

        $this->assertCompletePurchaseSucceeds($merchant->completePurchase([]));
    }
}
