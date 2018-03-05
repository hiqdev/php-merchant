<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\credentials;

/**
 * Interface CredentialsInterface.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface CredentialsInterface
{
    /**
     * @return string
     */
    public function getPurse();

    /**
     * @param string $purse
     * @return $this
     */
    public function setPurse($purse);

    /**
     * @return string
     */
    public function getKey1();

    /**
     * @param string $key1
     * @return $this
     */
    public function setKey1($key1);

    /**
     * @return string
     */
    public function getKey2();

    /**
     * @param string $key2
     * @return $this
     */
    public function setKey2($key2);

    /**
     * @return string
     */
    public function getKey3();

    /**
     * @param string $key3
     * @return $this
     */
    public function setKey3($key3);

    /**
     * @return bool
     */
    public function isTestMode(): bool;

    /**
     * @param boolean $value
     * @return $this
     */
    public function setTestMode($value);
}
