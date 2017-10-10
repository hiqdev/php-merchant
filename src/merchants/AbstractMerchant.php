<?php

namespace hiqdev\php\merchant\merchants;

use hiqdev\php\merchant\credentials\CredentialsInterface;
use hiqdev\php\merchant\factories\GatewayFactoryInterface;
use Money\MoneyFormatter;
use Money\MoneyParser;
use Omnipay\Common\GatewayInterface;

abstract class AbstractMerchant implements MerchantInterface
{
    /**
     * @var GatewayInterface
     */
    protected $gateway;
    /**
     * @var CredentialsInterface
     */
    protected $credentials;
    /**
     * @var GatewayFactoryInterface
     */
    protected $gatewayFactory;
    /**
     * @var MoneyFormatter
     */
    protected $moneyFormatter;
    /**
     * @var MoneyParser
     */
    protected $moneyParser;

    public function __construct(
        CredentialsInterface $credentials,
        GatewayFactoryInterface $gatewayFactory,
        MoneyFormatter $moneyFormatter,
        MoneyParser $moneyParser
    ) {
        $this->credentials = $credentials;
        $this->gatewayFactory = $gatewayFactory;
        $this->moneyFormatter = $moneyFormatter;
        $this->moneyParser = $moneyParser;
        $this->gateway = $this->createGateway();
    }

    /**
     * @return CredentialsInterface
     */
    public function getCredentials(): CredentialsInterface
    {
        return $this->credentials;
    }

    /**
     * @return GatewayInterface
     */
    abstract protected function createGateway();

}
