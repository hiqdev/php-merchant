<?php

namespace hiqdev\php\merchant\tests\unit\merchants\bitpay;

use Bitpay\Buyer;
use Bitpay\Client\Client;
use Bitpay\Invoice;
use Bitpay\InvoiceInterface;
use hiqdev\php\merchant\merchants\bitpay\BitPayMerchant;
use hiqdev\php\merchant\response\RedirectPurchaseResponse;
use hiqdev\php\merchant\tests\unit\merchants\AbstractMerchantTest;
use hiqdev\php\merchant\tests\phpunit\PHPUnit_Framework_MockObject_Stub_ReturnCallbackWithInvocationScope;
use Money\Currency;
use Money\Money;
use Omnipay\BitPay\Gateway;
use Omnipay\BitPay\Message\CompletePurchaseRequest;
use Omnipay\BitPay\Message\CompletePurchaseResponse;
use Omnipay\BitPay\Message\PurchaseRequest;
use Omnipay\BitPay\Message\PurchaseResponse;
use Omnipay\Common\Helper;
use Symfony\Component\HttpFoundation\Request as HttpRequest;

class BitPayMerchantTest extends AbstractMerchantTest
{
    /** @var BitPayMerchant */
    protected $merchant;

    protected function buildMerchant()
    {
        return new BitPayMerchant(
            $this->getCredentials(),
            $this->getGatewayFactory(),
            $this->getMoneyFormatter(),
            $this->getMoneyParser()
        );
    }

    protected function getCredentials()
    {
        return parent::getCredentials()
            ->setKey1('928MytEWMCVxD3yfPxUN2yvTBZzzXNg6XroLbqKgQeRH')
            ->setKey2('C:17:"Bitpay\PrivateKey":210:{a:5:{i:0;s:15:"/tmp/bitpay.pri";i:1;N;i:2;N;i:3;s:64:"bf631f04f1160566d2a14102c5fd41534215b1e6eefa0d2d70b5f7cafd53639b";i:4;s:77:"86566886026182798651320407713951913234789774666903933833365709263010750751643";}}')
            ->setKey3('C:16:"Bitpay\PublicKey":494:{a:5:{i:0;s:15:"/tmp/bitpay.pub";i:1;s:64:"d1d7d08302ca099e874f85c2e35d95d10d64a1d66944d63c377a4894d7407d81";i:2;s:64:"649333c4b3c706f2e023051b924578dbe1b66a429ef9c9a24a0213678a7446ff";i:3;s:128:"d1d7d08302ca099e874f85c2e35d95d10d64a1d66944d63c377a4894d7407d81649333c4b3c706f2e023051b924578dbe1b66a429ef9c9a24a0213678a7446ff";i:4;s:155:"10990371014948182676194544475608937714664754751591855109238192338231405528821043564388984300444197539690692876507369781741528994577644874894007197934831359";}}');
    }

    public function testCredentialsWereMappedCorrectly()
    {
        $gatewayPropertyReflection = (new \ReflectionObject($this->merchant))->getProperty('gateway');
        $gatewayPropertyReflection->setAccessible(true);

        /** @var \Omnipay\BitPay\Gateway $gateway */
        $gateway = $gatewayPropertyReflection->getValue($this->merchant);

        $this->assertSame($this->getCredentials()->getKey1(), $gateway->getToken());
        $this->assertSame($this->getCredentials()->getKey2(), $gateway->getPrivateKey());
        $this->assertSame($this->getCredentials()->getKey3(), $gateway->getPublicKey());
    }

    public function testRequestPurchase()
    {
        $invoice = $this->buildInvoice();

        // Get gateway from merchant
        $merchantReflection = new \ReflectionObject($this->merchant);
        $gatewayPropertyReflection = $merchantReflection->getProperty('gateway');
        $gatewayPropertyReflection->setAccessible(true);
        $gateway = $gatewayPropertyReflection->getValue($this->merchant);

        // Mock gateway purchase() method
        $gatewayMock = $this->getMockBuilder(Gateway::class)->setMethods(['purchase'])->getMock();

        $httpClient = $this->buildHttpClient();
        $gatewayMock->method('purchase')->will(
            new PHPUnit_Framework_MockObject_Stub_ReturnCallbackWithInvocationScope(
                function ($parameters) use ($httpClient) {
                    $request = new class($httpClient, HttpRequest::createFromGlobals()) extends PurchaseRequest
                    {
                        // Mock client to prevent any network calls
                        public function getClient()
                        {
                            return new class extends Client
                            {
                                public function createInvoice(InvoiceInterface $invoice)
                                {
                                    return $invoice
                                        ->setId('a928MytEWMCVxD3yfPxUN2yvT')
                                        ->setUrl('https://bitpay.com/invoice?id=a928MytEWMCVxD3yfPxUN2yvT');
                                }
                            };
                        }
                    };

                    return $request->initialize(array_replace($this->getParameters(), $parameters));
                }
            )
        );
        Helper::initialize($gatewayMock, $gateway->getParameters());
        $gatewayPropertyReflection->setValue($this->merchant, $gatewayMock);

        $purchaseResponse = $this->merchant->requestPurchase($invoice);
        $this->assertInstanceOf(RedirectPurchaseResponse::class, $purchaseResponse);
        $this->assertSame('https://bitpay.com/invoice?id=a928MytEWMCVxD3yfPxUN2yvT', $purchaseResponse->getRedirectUrl());
        $this->assertArraySubset(['id' => 'a928MytEWMCVxD3yfPxUN2yvT'], $purchaseResponse->getRedirectData());
    }

    public function testCompletePurchase()
    {
        // Get gateway from merchant
        $merchantReflection = new \ReflectionObject($this->merchant);
        $gatewayPropertyReflection = $merchantReflection->getProperty('gateway');
        $gatewayPropertyReflection->setAccessible(true);
        $gateway = $gatewayPropertyReflection->getValue($this->merchant);

        // Mock gateway purchase() method
        $gatewayMock = $this->getMockBuilder(Gateway::class)->setMethods(['completePurchase'])->getMock();

        $httpClient = $this->buildHttpClient();
        $gatewayMock->method('completePurchase')->will(
            new PHPUnit_Framework_MockObject_Stub_ReturnCallbackWithInvocationScope(
                function ($parameters) use ($httpClient) {
                    $request = new class($httpClient, HttpRequest::createFromGlobals()) extends CompletePurchaseRequest
                    {
                        // Mock client to prevent any network calls
                        public function getClient()
                        {
                            return new class extends Client
                            {
                                public function getInvoice($invoiceId)
                                {
                                    return (new Invoice())
                                        ->setId($invoiceId)
                                        ->setPosData($this->buildPosData())
                                        ->setStatus(InvoiceInterface::STATUS_CONFIRMED)
                                        ->setOrderId('123123')
                                        ->setInvoiceTime(new \DateTime())
                                        ->setPrice('10.36')
                                        ->setCurrency('USD')
                                        ->setBuyer((new Buyer())->setFirstName('First')->setLastName('Last')->setEmail('example.com'));
                                }
                            };
                        }
                    };

                    return $request->initialize(array_replace($this->getParameters(), $parameters));
                }
            )
        );
        Helper::initialize($gatewayMock, $gateway->getParameters());
        $gatewayPropertyReflection->setValue($this->merchant, $gatewayMock);

        $completePurchaseResponse = $this->merchant->completePurchase([
            'id' => 'a928MytEWMCVxD3yfPxUN2yvT'
        ]);

        $this->assertInstanceOf(\hiqdev\php\merchant\response\CompletePurchaseResponse::class, $completePurchaseResponse);
        $this->assertTrue($completePurchaseResponse->getIsSuccessful());
        $this->assertSame('123456', $completePurchaseResponse->getTransactionId());
        $this->assertSame('123456789', $completePurchaseResponse->getTransactionReference());
        $this->assertTrue((new Money(1099, new Currency('USD')))->equals($completePurchaseResponse->getAmount()));
        $this->assertTrue((new Money(0, new Currency('USD')))->equals($completePurchaseResponse->getFee()));
        $this->assertSame('USD', $completePurchaseResponse->getCurrency()->getCode());
        $this->assertEquals(new \DateTime($_POST['ok_txn_datetime']), $completePurchaseResponse->getTime());
    }
}
