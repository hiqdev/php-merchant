<?php

/*
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2016, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit\webmoney;

use hiqdev\php\merchant\Helper;

/**
 * AbstractMerchant test suite.
 */
class AbstractMerchantTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OmnipayMerchant
     */
    protected $object;

    protected $gateway = 'WebMoney';

    protected function setUp()
    {
        $this->object = Helper::create([
            'gateway' => $this->gateway,
        ]);
    }

    protected function tearDown()
    {
    }

    public function testGetLabel()
    {
        $this->assertSame($this->gateway, $this->object->getLabel());
    }

    public function testGetSimpleName()
    {
        $this->assertSame(strtolower($this->gateway), $this->object->getSimpleName());
    }
}
