<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit\merchants;

use Guzzle\Http\Client;
use Guzzle\Http\ClientInterface;
use hiqdev\php\merchant\credentials\Credentials;
use hiqdev\php\merchant\factories\GatewayFactoryInterface;
use hiqdev\php\merchant\Invoice;
use hiqdev\php\merchant\merchants\MerchantInterface;
use Http\Client\HttpClient;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;
use Money\Parser\DecimalMoneyParser;
use Omnipay\Common\GatewayInterface;
use Omnipay\Common\Helper;
use PHPUnit\Framework\TestCase;

abstract class AbstractMerchantTest extends TestCase
{
    /**
     * @var MerchantInterface
     */
    protected $merchant;

    /**
     * @var HttpClient
     */
    protected $httpClient;

    public function setUp()
    {
        parent::setUp();

        $this->httpClient = $this->buildHttpClient();
        $this->merchant = $this->buildMerchant();
    }

    /**
     * @return Credentials
     */
    protected function getCredentials()
    {
        return (new Credentials())
            ->setPurse('purse')
            ->setKey1('key1')
            ->setKey2('key2')
            ->setKey3('key3')
            ->setTestMode(true);
    }

    /**
     * @return GatewayFactoryInterface
     */
    protected function getGatewayFactory()
    {
        return new class($this->httpClient) implements GatewayFactoryInterface {
            /**
             * @var HttpClient
             */
            private $httpClient;

            public function __construct(ClientInterface $httpClient)
            {
                $this->httpClient = $httpClient;
            }

            public function build(string $type, $parameters): GatewayInterface
            {
                $className = Helper::getGatewayClassName($type);
                /** @var GatewayInterface $gateway */
                $gateway = new $className($this->httpClient);
                $gateway->initialize($parameters);

                return $gateway;
            }
        };
    }

    /**
     * @return DecimalMoneyFormatter
     */
    protected function getMoneyFormatter()
    {
        return new DecimalMoneyFormatter(new ISOCurrencies());
    }

    /**
     * @return DecimalMoneyParser
     */
    protected function getMoneyParser()
    {
        return new DecimalMoneyParser(new ISOCurrencies());
    }

    protected function buildHttpClient()
    {
        return new Client();
    }

    /**
     * @return \hiqdev\php\merchant\InvoiceInterface
     */
    protected function buildInvoice()
    {
        return (new Invoice())
            ->setId(uniqid())
            ->setDescription('Test purchase')
            ->setClient('silverfire')
            ->setAmount(new Money(1099, new Currency('USD')))
            ->setReturnUrl('https://example.com/return')
            ->setNotifyUrl('https://example.com/notify')
            ->setCancelUrl('https://example.com/cancel');
    }

    /**
     * @return MerchantInterface
     */
    abstract protected function buildMerchant();
}
