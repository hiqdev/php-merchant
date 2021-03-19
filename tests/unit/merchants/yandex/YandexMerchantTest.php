<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit\merchants\yandex;

use hiqdev\php\merchant\Invoice;
use hiqdev\php\merchant\merchants\yandex\YandexP2pMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;
use Omnipay\YandexMoney\P2pGateway;

class YandexMerchantTest extends AbstractMerchantTest
{
    /** @var YandexP2pMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new YandexP2pMerchant(
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

        $this->assertSame($this->getCredentials()->getPurse(), $gateway->getAccount());
        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getPassword());
        $this->assertSame($this->getCredentials()->isTestMode(), $gateway->getTestMode());
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);
        $this->assertSame('POST', $purchaseResponse->getMethod());
        $this->assertSame('https://yoomoney.ru/quickpay/confirm.xml', $purchaseResponse->getRedirectUrl());
        $this->assertSame([
            'receiver' => $this->getCredentials()->getPurse(),
            'formcomment' => $invoice->getDescription(),
            'short-dest' => $invoice->getDescription(),
            'writable-targets' => 'false',
            'comment-needed' => 'true',
            'label' => $invoice->getId(),
            'quickpay-form' => 'shop',
            'targets' => 'Order ' . $invoice->getId(),
            'sum' => $this->getMoneyFormatter()->format($invoice->getAmount()),
            'comment' => null,
            'need-fio' => 'false',
            'need-email' => 'false',
            'need-phone' => 'false',
            'need-address' => 'false',
            'paymentType' => 'AC',
            'successURL' => $invoice->getReturnUrl(),
            'failURL' => $invoice->getCancelUrl(),
        ], $purchaseResponse->getRedirectData());
    }

    protected function getCredentials()
    {
        return parent::getCredentials()
            ->setPurse('test@example.com')
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
            'notification_type' => 'p2p-incoming',
            'amount' => '1171.68',
            'datetime' => '2017-10-11T05:37:04Z',
            'codepro' => 'false',
            'withdraw_amount' => '1179.68',
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
        $this->assertTrue((new Money(117968, new Currency('RUB')))->equals($completePurchaseResponse->getAmount()));
        $this->assertTrue((new Money(0, new Currency('RUB')))->equals($completePurchaseResponse->getFee()));
        $this->assertSame('RUB', $completePurchaseResponse->getCurrency()->getCode());
        $this->assertEquals(new \DateTime('2017-10-11T05:37:04'), $completePurchaseResponse->getTime());
        $this->assertEquals('410013314711147', $completePurchaseResponse->getPayer());
    }
}
