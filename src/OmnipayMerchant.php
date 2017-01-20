<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
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
     * @var \Omnipay\Common\GatewayInterface omnipay Gateway object
     */
    protected $_worker;

    /**
     * @return \Omnipay\Common\GatewayInterface
     */
    public function getWorker()
    {
        if ($this->_worker === null) {
            $this->_worker = Omnipay::create($this->getGateway())->initialize($this->prepareData($this->data));
        }

        return $this->_worker;
    }

    /**
     * Returns the normalized gateway $name.
     *
     * @see normalizeGateway
     * @param string $name The gateway name. Defaults to `$this->gateway`
     * @return string
     */
    public function getGateway($name = null)
    {
        if (!isset($name)) {
            $name = $this->gateway;
        }

        return $this->normalizeGateway($name);
    }

    /**
     * Normalizes gateway $name.
     *
     * @param $name
     * @return string
     */
    public function normalizeGateway($name)
    {
        foreach (static::$_gateways as $gateway) {
            if (Helper::simplify($gateway) === Helper::simplify($name)) {
                return $gateway;
            }
        }

        return $name;
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
