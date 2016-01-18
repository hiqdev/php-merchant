<?php

/*
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant;

/**
 * AbstractRequest class.
 */
abstract class AbstractRequest implements RequestInterface
{
    /**
     * @var AbstractMerchant
     */
    public $merchant;

    /**
     * @var string The type of request. For example:
     *  - `purchase`
     *  - `completePurchase`
     */
    public $type;

    /**
     * @var array the data that will be sent to the payment system.
     * Might be additionally processed by the implementation of [[AbstractRequest]] class.
     */
    public $data = [];

    /**
     * The instance of payment processing library.
     * @return \Omnipay\Common\Message\AbstractRequest|\Payum\Core\Model\Payment
     */
    abstract public function getWorker();

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->getWorker()->getCurrency();
    }

    /**
     * {@inheritdoc}
     */
    public function getAmount()
    {
        return $this->getWorker()->getAmount();
    }

    /**
     * @return string
     */
    public function getFee()
    {
        return $this->data['fee'] ?: '0';
    }

    /**
     * @return string
     */
    public function getSum()
    {
        return $this->data['sum'] ?: $this->getAmount() - $this->getFee();
    }

    /**
     * @return AbstractResponse
     */
    public function send()
    {
        return $this->merchant->response($this);
    }

    /**
     * Concrete requests can build type in other way.
     *
     * @return string
     */
    public function getType()
    {
        return Helper::id2camel($this->type);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Use worker's method when possible.
     *
     * @param $name
     * @param $args
     * @return mixed|null
     */
    public function __call($name, $args)
    {
        if (method_exists($this->getWorker(), $name)) {
            return call_user_func_array([$this->getWorker(), $name], $args);
        }

        return null;
    }
}
