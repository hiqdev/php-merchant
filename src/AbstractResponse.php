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
 * Abstract Response class.
 */
abstract class AbstractResponse implements ResponseInterface
{
    /**
     * @var ResponseInterface
     */
    public $merchant;

    /**
     * @var RequestInterface
     */
    public $request;

    /**
     * {@inheritdoc}
     */
    abstract public function redirect();

    /**
     * {@inheritdoc}
     */
    abstract public function isRedirect();

    /**
     * {@inheritdoc}
     */
    abstract public function isSuccessful();

    /**
     * The instance of payment processing library.
     * @return \Omnipay\Common\Message\AbstractResponse|\Payum\Core\Model\Response
     */
    abstract public function getWorker();

    public function __call($name, $args)
    {
        if (method_exists($this->getWorker(), $name)) {
            return call_user_func_array([$this->getWorker(), $name], $args);
        }

        return null;
    }

    public function getFee()
    {
        return 0;
    }

    public function getTime()
    {
        return date('c');
    }

}
