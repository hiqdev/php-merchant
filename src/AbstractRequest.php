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
        return static::id2camel($this->type);
    }

    /**
     * Converts an ID into a CamelCase name.
     * Words in the ID separated by `$separator` (defaults to '-') will be concatenated into a CamelCase name.
     * For example, 'post-tag' is converted to 'PostTag'.
     * Taken from Yii 2 Inflector.
     *
     * @param string $id        the ID to be converted
     * @param string $separator the character used to separate the words in the ID
     *
     * @return string the resulting CamelCase name
     */
    public static function id2camel($id, $separator = '-')
    {
        return str_replace(' ', '', ucwords(implode(' ', explode($separator, $id))));
    }

    public function getData()
    {
        return $this->data;
    }
}
