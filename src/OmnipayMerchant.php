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
    public $requestClass  = 'hiqdev\php\merchant\OmnipayRequest';
    public $responseClass = 'hiqdev\php\merchant\OmnipayResponse';

    /**
     * Omnipay Gateway object.
     */
    protected $_worker;

    public function getWorker()
    {
        if ($this->_worker === null) {
            $this->_worker = Omnipay::create($this->gateway)->initialize($this->prepareData($this->data));
        }

        return $this->_worker;
    }

    protected $_prepareTable = [
        'WebMoney' => [
            'purse' => 'merchantPurse',
        ],
    ];

    public function prepareData(array $data)
    {
        if (isset($this->_prepareTable[$this->gateway])) {
            foreach ($this->_prepareTable[$this->gateway] as $name => $rename) {
                $data[$rename] = $data[$name];
            }
        }

        return $data;
    }
}
