<?php

namespace hiqdev\php\merchant\credentials;

/**
 * Interface CredentialsInterface
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
     * @return self
     */
    public function setPurse($purse);

    /**
     * @return string
     */
    public function getKey1();

    /**
     * @param string $key1
     * @return self
     */
    public function setKey1($key1);

    /**
     * @return string
     */
    public function getKey2();

    /**
     * @param string $key2
     * @return self
     */
    public function setKey2($key2);

    /**
     * @return string
     */
    public function getKey3();

    /**
     * @param string $key3
     * @return self
     */
    public function setKey3($key3);
}
