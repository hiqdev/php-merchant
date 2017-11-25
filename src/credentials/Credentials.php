<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\credentials;

/**
 * Class Credentials.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
final class Credentials implements CredentialsInterface
{
    /**
     * @var string
     */
    private $purse;
    /**
     * @var string
     */
    private $key1;
    /**
     * @var string
     */
    private $key2;
    /**
     * @var string
     */
    private $key3;

    /**
     * @var bool
     */
    private $isTestMode = false;

    public function getPurse()
    {
        return $this->purse;
    }

    public function setPurse($purse): self
    {
        $this->purse = $purse;

        return $this;
    }

    public function getKey1()
    {
        return $this->key1;
    }

    public function setKey1($key1): self
    {
        $this->key1 = $key1;

        return $this;
    }

    public function getKey2()
    {
        return $this->key2;
    }

    public function setKey2($key2): self
    {
        $this->key2 = $key2;

        return $this;
    }

    public function getKey3()
    {
        return $this->key3;
    }

    public function setKey3($key3): self
    {
        $this->key3 = $key3;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTestMode(): bool
    {
        return $this->isTestMode;
    }

    /**
     * @param boolean $value
     * @return $this
     */
    public function setTestMode($value)
    {
        $this->isTestMode = (bool) $value;

        return $this;
    }
}
