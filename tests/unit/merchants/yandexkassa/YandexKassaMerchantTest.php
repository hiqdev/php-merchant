<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit\merchants\yandexkassa;

use hiqdev\php\merchant\Invoice;
use hiqdev\php\merchant\merchants\yandex\YandexP2pMerchant;
use hiqdev\php\merchant\merchants\yandexkassa\YandexKassaMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;
use Omnipay\YandexMoney\P2pGateway;

class YandexKassaMerchantTest extends AbstractMerchantTest
{
    /** @var YandexKassaMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new YandexKassaMerchant(
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
        /** @var P2pGateway $gateway */
        $gateway = $gatewayPropertyReflection->getValue($this->merchant);

        $this->assertSame($this->getCredentials()->getPurse(), $gateway->getShopId());
        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getSecretKey());
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);
        $this->assertSame('GET', $purchaseResponse->getMethod());
        $this->assertStringStartsWith('https://money.yandex.ru/api-pages/v2/payment-confirm/epl?orderId=', $purchaseResponse->getRedirectUrl());
        $this->assertEmpty($purchaseResponse->getRedirectData());
    }

    /**
     * @see https://github.com/yandex-money/yandex-money-joinup/blob/master/checkout-api/sample/rest/insomnia/how-to.md#тестовое-окружение-apiяндекскассы  
     */
    protected function getCredentials()
    {
        return parent::getCredentials()
            ->setPurse('54401')
            ->setKey1('test_Fh8hUAVVBGUGbjmlzba6TB0iyUbos_lueTHE-axOwM0');
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
        // TODO: Implement
        $_POST = [
            'notification_type' => 'p2p-incoming',
            'amount' => '1171.68',
            'datetime' => '2017-10-11T05:37:04Z',
            'codepro' => 'false',
            'withdraw_amount' => '1177.54',
            'sender' => '410013314711147',
            'sha1_hash' => '911c40cad72e66571be07b08674f00bb3ad3c6a9',
            'unaccepted' => 'false',
            'operation_label' => '216fc1aa-0009-5000-8000-00012710eaf3',
            'operation_id' => '1122030848336014125',
            'currency' => 'RUB',
            'label' => 'ma119annqk',
            'merchant' => 'yandex_rub',
        ];

        $this->merchant = $this->buildMerchant();

        $completePurchaseResponse = $this->merchant->completePurchase([]);

        $this->assertInstanceOf(\hiqdev\php\merchant\response\CompletePurchaseResponse::class, $completePurchaseResponse);
        $this->assertTrue($completePurchaseResponse->getIsSuccessful());
        $this->assertSame('ma119annqk', $completePurchaseResponse->getTransactionId());
        $this->assertSame('1122030848336014125', $completePurchaseResponse->getTransactionReference());
        $this->assertTrue((new Money(117168, new Currency('RUB')))->equals($completePurchaseResponse->getAmount()));
        $this->assertTrue((new Money(0, new Currency('RUB')))->equals($completePurchaseResponse->getFee()));
        $this->assertSame('RUB', $completePurchaseResponse->getCurrency()->getCode());
        $this->assertEquals(new \DateTime('2017-10-11T05:37:04'), $completePurchaseResponse->getTime());
        $this->assertEquals('410013314711147', $completePurchaseResponse->getPayer());
    }
}
