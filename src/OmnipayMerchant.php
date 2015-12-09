<?php

/*
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant;

use Omnipay\Omnipay;
use Omnipay\Common\Helper;

/**
 * OmnipayMerchant class.
 */
class OmnipayMerchant extends AbstractMerchant
{
    public $requestClass  = 'hiqdev\php\merchant\OmnipayRequest';
    public $responseClass = 'hiqdev\php\merchant\OmnipayResponse';

    /**
     * Omnipay Gateway object.
     */
    protected $_worker;

    public function getWorker()
    {
        if ($this->_worker === null) {
            $this->_worker = Omnipay::create($this->getGateway())->initialize($this->prepareData($this->data));
        }

        return $this->_worker;
    }

    public function getGateway($gateway = null)
    {
        if (!isset($gateway)) {
            $gateway = $this->gateway;
        }

        return $this->normalizeGateway($gateway);
    }

    public function normalizeGateway($gateway)
    {
        foreach (static::$_gateways as $norm) {
            if ($this->simplifyGateway($norm) == $this->simplifyGateway($gateway)) {
                return $norm;
            }
        }

        return $gateway;
    }

    public function simplifyGateway($gateway)
    {
        return preg_replace('/[^a-z0-9]+/','', strtolower($gateway));
    }

    public static $_gateways = [
        'eCoin', 'ePayments', 'ePayService', 'InterKassa', 'OKPAY', 'Qiwi',
        'Paxum', 'PayPal', 'RoboKassa', 'WebMoney', 'YandexMoney',
    ];

    public static function setGateways($gateways)
    {
        static::$_gateways = array_merge(static::$_gateways, $gateways);
    }

    protected $_prepareTable = [
        'WebMoney' => [
            'purse'  => 'merchantPurse',
            'secret' => 'secretKey',
        ],
    ];

    public function prepareData(array $data)
    {
        if (isset($this->_prepareTable[$this->getGateway()])) {
            foreach ($this->_prepareTable[$this->getGateway()] as $name => $rename) {
                $data[$rename] = $data[$name];
            }
        }

        return $data;
    }
}
