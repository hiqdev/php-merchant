<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit\merchants\paxum;

use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;
use hiqdev\php\merchant\merchants\paxum\PaxumMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;

class PaxumMerchantTest extends AbstractMerchantTest
{
    /** @var PaxumMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new PaxumMerchant(
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
        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getSecret());
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);
        $this->assertSame('https://www.paxum.com/payment/phrame.php?action=displayProcessPaymentLogin', $purchaseResponse->getRedirectUrl());
        $this->assertArraySubset([
            'business_email' => 'purse',
            'amount' => $this->getMoneyFormatter()->format($invoice->getAmount()),
            'currency' => $invoice->getCurrency()->getCode(),
            'item_name' => $invoice->getDescription(),
            'finish_url' => $invoice->getReturnUrl(),
            'cancel_url' => $invoice->getCancelUrl(),
            'variables' => 'notify_url=' . $invoice->getNotifyUrl(),
            'button_type_id' => 1,
            'item_id' => $invoice->getId(),
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

    public function testCompletePurchase()
    {
        $_POST = [
            'item_id' => '123',
            'transaction_id' => 'tax_num_id',
            'transaction_status' => 'done',
            'transaction_amount' => '10.99',
            'transaction_currency' => 'USD',
            'transaction_date' => '2017-10-09T19:10:42',
        ];

        $this->merchant = $this->buildMerchant();

        $completePurchaseResponse = $this->merchant->completePurchase([]);

        $this->assertInstanceOf(\hiqdev\php\merchant\response\CompletePurchaseResponse::class, $completePurchaseResponse);
        $this->assertTrue($completePurchaseResponse->getIsSuccessful());
        $this->assertSame('123', $completePurchaseResponse->getTransactionId());
        $this->assertSame('tax_num_id', $completePurchaseResponse->getTransactionReference());
        $this->assertTrue((new Money(1099, new Currency('USD')))->equals($completePurchaseResponse->getAmount()));
        $this->assertTrue((new Money(0, new Currency('USD')))->equals($completePurchaseResponse->getFee()));
        $this->assertSame('USD', $completePurchaseResponse->getCurrency()->getCode());
        $this->assertEquals(new \DateTime('2017-10-10T00:10:42'), $completePurchaseResponse->getTime());
    }
}
