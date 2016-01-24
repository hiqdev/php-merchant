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

use hiqdev\php\merchant\webmoney\OmnipayMerchant;

/**
 * OmnipayMerchant test suite.
 */
class OmnipayMerchantTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OmnipayMerchant
     */
    protected $object;

    protected $data = [
        'purse'  => 'ThePurse',
        'secret' => 'TheSecret',
    ];

    protected function setUp()
    {
        $this->object = new OmnipayMerchant();
    }

    protected function tearDown()
    {
    }

    public function testPrepareData()
    {
        $res = $this->object->prepareData($this->data);
        $this->assertSame($this->data['purse'],  $res['merchantPurse']);
        $this->assertSame($this->data['secret'], $res['secretKey']);
    }
}
