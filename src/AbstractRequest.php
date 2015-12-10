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

/**
 * AbstractRequest class.
 */
class AbstractRequest implements RequestInterface
{
    public $merchant;

    public $type;

    public $data = [];

    public function getAmount()
    {
        return $this->data['amount'];
    }

    public function getFee()
    {
        return $this->data['fee'] ?: 0;
    }

    public function getSum()
    {
        return $this->data['sum'] ?: $this->getAmount() - $this->getFee();
    }

    public function getCurrency()
    {
        return $this->data['currency'];
    }

    public function send()
    {
        return $this->merchant->response($this);
    }

    /**
     * Concrete requests can build type in other way.
     */
    public function getType()
    {
        return Helper::id2camel($this->type);
    }

    public function getData()
    {
        return $this->data;
    }
}
