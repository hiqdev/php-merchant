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

/**
 * OmnipayMerchant class.
 */
class OmnipayMerchant extends AbstractMerchant
{
    public $library = 'Omnipay';

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
            if (Helper::simplify($norm) === Helper::simplify($gateway)) {
                return $norm;
            }
        }

        return $gateway;
    }

    public static $_gateways = [
        'eCoin', 'ePayments', 'ePayService', 'InterKassa', 'OKPAY', 'Qiwi',
        'Paxum', 'PayPal', 'RoboKassa', 'WebMoney', 'YandexMoney',
    ];

    public static function setGateways($gateways)
    {
        static::$_gateways = array_merge(static::$_gateways, $gateways);
    }

    /**
     * No prepare by default.
     * To be redefined in specific gateway merchants.
     */
    public function prepareData(array $data)
    {
        return $data;
    }

}
