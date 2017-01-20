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

use hiqdev\php\merchant\OmnipayRequest;

/**
 * AbstractRequest test suite.
 */
class AbstractRequestTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OmnipayRequest
     */
    protected $object;

    protected $type = 'Purchase';
    protected $data = [
        'currency' => 'USD',
        'amount' => '123.45',
        'fee' => '12.34',
        'sum' => '111.11',
        'commission_fee' => '11.11',
    ];

    protected function setUp()
    {
        $this->object = new OmnipayRequest();
        $this->object->type = $this->type;
        $this->object->data = $this->data;
    }

    protected function tearDown()
    {
    }

    public function testGetters()
    {
        $this->assertSame($this->type, $this->object->getType());
        $this->assertSame($this->data, $this->object->getData());

        /*$this->assertSame($this->data['currency'],  $this->object->getCurrency());
        $this->assertSame($this->data['amount'],    $this->object->getAmount());
        $this->assertSame($this->data['fee'],       $this->object->getFee());
        $this->assertSame($this->data['sum'],       $this->object->getSum());*/
    }
}
