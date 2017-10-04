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

    public function call($name, $args, $default = null)
    {
        if (method_exists($this->getWorker(), $name)) {
            return call_user_func_array([$this->getWorker(), $name], $args);
        }

        return $default;
    }

    public function __call($name, $args)
    {
        return $this->call($name, $args);
    }

    /**
     * Sum = Amount - Fee.
     */
    public function getSum()
    {
        return $this->call('getSum', [], (float) $this->getAmount() - (float) $this->getFee());
    }

    public function getFee()
    {
        return $this->call('getFee', [], 0);
    }

    public function getTime()
    {
        return $this->call('getTime', [], date('c'));
    }
}
