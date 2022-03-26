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
use hiqdev\php\merchant\merchants\yandexkassa\YandexKassaMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use Money\Currency;
use Money\Money;
use Omnipay\YandexKassa\Gateway;
use Omnipay\YandexKassa\Message\CaptureRequest;
use Omnipay\YandexKassa\Message\CaptureResponse;
use Omnipay\YandexKassa\Message\DetailsRequest;
use Omnipay\YandexKassa\Message\DetailsResponse;
use Omnipay\YandexKassa\Message\IncomingNotificationRequest;
use Omnipay\YandexKassa\Message\IncomingNotificationResponse;
use Omnipay\YandexKassa\Message\PurchaseRequest;
use Omnipay\YandexKassa\Message\PurchaseResponse;
use YandexCheckout\Request\Payments\PaymentResponse;

class YandexKassaMerchantTest extends AbstractMerchantTest
{
    /** @var YandexKassaMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new YandexKassaMerchant($this->getCredentials(), $this->getGatewayFactory(), $this->getMoneyFormatter(),
            $this->getMoneyParser());
    }

    public function testCredentialsWereMappedCorrectly()
    {
        $gatewayPropertyReflection = (new \ReflectionObject($this->merchant))->getProperty('gateway');
        $gatewayPropertyReflection->setAccessible(true);
        /** @var Gateway $gateway */
        $gateway = $gatewayPropertyReflection->getValue($this->merchant);

        $this->assertSame($this->getCredentials()->getPurse(), $gateway->getShopId());
        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getSecret());
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        $this->merchant = $this->buildMerchant();
        $gatewayStub = $this->makeGatewayStub();
        $gatewayStub->method('purchase')->willReturnCallback(function () use ($invoice) {
            $requestMock = $this->getMockBuilder(PurchaseRequest::class)->disableOriginalConstructor()->onlyMethods(['send'])->getMock();
            $responseMock = $this->getMockBuilder(PurchaseResponse::class)->disableOriginalConstructor()->onlyMethods(['getRedirectUrl'])
                                 ->getMock();

            $responseMock->method('getRedirectUrl')
                         ->willReturn('https://money.yandex.ru/api-pages/v2/payment-confirm/epl?orderId=' . $invoice->getId());
            $requestMock->method('send')->willReturn($responseMock);

            return $requestMock;
        });
        $this->setGateway($gatewayStub);

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);
        $this->assertSame('GET', $purchaseResponse->getMethod());
        $this->assertStringStartsWith('https://money.yandex.ru/api-pages/v2/payment-confirm/epl?orderId=',
            $purchaseResponse->getRedirectUrl());
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
        $this->merchant = $this->buildMerchant();

        $gatewayStub = $this->makeGatewayStub();
        $gatewayStub->method('notification')->willReturnCallback(function () {
                $requestMock = $this->getMockBuilder(IncomingNotificationRequest::class)
                                    ->disableOriginalConstructor()->onlyMethods(['send'])
                                    ->getMock();

                $reflection = new \ReflectionClass(IncomingNotificationResponse::class);
                /** @var IncomingNotificationResponse $response */
                $response = $reflection->newInstanceWithoutConstructor();
                $data = $reflection->getProperty('data');
                $data->setAccessible(true);
                $data->setValue($response, $this->fixture('notification.payment.waiting_for_capture'));

                $requestMock->method('send')->willReturn($response);
                return $requestMock;
            });
        $gatewayStub->method('details')->willReturnCallback(function () {
                $requestMock = $this->getMockBuilder(DetailsRequest::class)
                                    ->disableOriginalConstructor()->onlyMethods(['send'])
                                    ->getMock();

                $reflection = new \ReflectionClass(DetailsResponse::class);
                /** @var DetailsResponse $response */
                $response = $reflection->newInstanceWithoutConstructor();

                $data = $reflection->getProperty('data');
                $data->setAccessible(true);
                $data->setValue($response, new PaymentResponse($this->fixture('payment.waiting_for_capture')));

                $requestMock->method('send')->willReturn($response);
                return $requestMock;
            });
        $gatewayStub->method('capture')->willReturnCallback(function () {
                $requestMock = $this->getMockBuilder(CaptureRequest::class)
                                    ->disableOriginalConstructor()->onlyMethods(['send'])
                                    ->getMock();

                $reflection = new \ReflectionClass(CaptureResponse::class);
                /** @var CaptureResponse $response */
                $response = $reflection->newInstanceWithoutConstructor();

                $data = $reflection->getProperty('data');
                $data->setAccessible(true);
                $data->setValue($response, new PaymentResponse($this->fixture('payment.succeeded')));

                $requestMock->method('send')->willReturn($response);
                return $requestMock;
            });
        $this->setGateway($gatewayStub);

        $completePurchaseResponse = $this->merchant->completePurchase([]);
        $this->assertInstanceOf(\hiqdev\php\merchant\response\CompletePurchaseResponse::class, $completePurchaseResponse);
        $this->assertTrue($completePurchaseResponse->getIsSuccessful());
        $this->assertSame('5ce3cdb0d1436', $completePurchaseResponse->getTransactionId());
        $this->assertSame('2475e163-000f-5000-9000-18030530d620', $completePurchaseResponse->getTransactionReference());
        $this->assertTrue((new Money(18750, new Currency('RUB')))->equals($completePurchaseResponse->getAmount()));
        $this->assertTrue((new Money(0, new Currency('RUB')))->equals($completePurchaseResponse->getFee()));
        $this->assertSame('RUB', $completePurchaseResponse->getCurrency()->getCode());
        $this->assertEquals(new \DateTime('2019-05-21T10:09:54.154000+0000'), $completePurchaseResponse->getTime());
        $this->assertEquals('Bank card *4444', $completePurchaseResponse->getPayer());
    }

    private function fixture(string $name)
    {
        return json_decode(file_get_contents(__DIR__ . '/fixture/' . $name . '.json'), true);
    }

    private function setGateway(Gateway $gateway): void
    {
        $gatewayPropertyReflection = (new \ReflectionObject($this->merchant))->getProperty('gateway');
        $gatewayPropertyReflection->setAccessible(true);

        $gatewayPropertyReflection->setValue($this->merchant, $gateway);
    }

    private function makeGatewayStub()
    {
        return $this->getMockBuilder(Gateway::class)
                    ->onlyMethods(['purchase', 'capture', 'notification', 'details'])
                    ->getMock();
    }
}
