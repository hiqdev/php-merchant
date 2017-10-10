<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit\credentials;

use hiqdev\php\merchant\credentials\Credentials;
use PHPUnit\Framework\TestCase;

/**
 * Helper test suite.
 */
class CredentialsTest extends TestCase
{
    public function testCredentialsProperties()
    {
        $credentials = new Credentials();

        $credentials->setPurse('purse');
        $this->assertSame('purse', $credentials->getPurse());
        $credentials->setKey1('key1');
        $this->assertSame('key1', $credentials->getKey1());
        $credentials->setKey2('key2');
        $this->assertSame('key2', $credentials->getKey2());
        $credentials->setKey3('key3');
        $this->assertSame('key3', $credentials->getKey3());
    }
}
