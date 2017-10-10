<?php

namespace hiqdev\php\merchant\tests\unit\merchants\robokassa;

use hiqdev\php\merchant\merchants\okpay\OkpayMerchant;
use hiqdev\php\merchant\merchants\robokassa\RoboKassaMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;
use Omnipay\OKPAY\Gateway;
use Omnipay\OKPAY\Message\CompletePurchaseRequest;

class RoboKassaMerchantTest extends AbstractMerchantTest
{
    /** @var RoboKassaMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new RoboKassaMerchant(
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
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);
        $this->assertSame('https://merchant.roboxchange.com/Index.aspx', $purchaseResponse->getRedirectUrl());

        $this->markTestIncomplete('Not implemented yet. TODO: implement');
        $this->assertArraySubset([
            'Desc' => $invoice->getDescription(),
            'MrchLogin' => $this->getCredentials()->getPurse(),
            'OutSum' => $this->getMoneyFormatter()->format($invoice->getAmount()),
            'IncCurrLabel' => $invoice->getCurrency()->getCode(),
            'InvId' => $invoice->getId(),
            'Culture' => NULL,
            'ShpCart' => NULL,
            'ShpClient' => NULL,
            'ShpTime' => '2017-10-10T13:53:24+00:00',
            'SignatureValue' => NULL,
        ], $purchaseResponse->getRedirectData());
    }

    public function testCompletePurchase()
    {
        $this->markTestIncomplete('Not implemented yet. TODO: implement');
    }
}
