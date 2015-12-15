<?php

/*
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit;

use hiqdev\php\merchant\Helper;

/**
 * Helper test suite.
 */
class HelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Helper
     */
    protected $object;

    protected function setUp()
    {
    }

    protected function tearDown()
    {
    }

    public function testId2camel()
    {
        $this->assertSame('PostTag', Helper::id2camel('PostTag'));
        $this->assertSame('PostTag', Helper::id2camel('post-tag'));
        $this->assertSame('PostTag', Helper::id2camel('post_tag', '_'));
        $this->assertSame('PostTag', Helper::id2camel('post-tag'));
        $this->assertSame('PostTag', Helper::id2camel('post_tag', '_'));
        $this->assertSame('FooYBar', Helper::id2camel('foo-y-bar'));
        $this->assertSame('FooYBar', Helper::id2camel('foo_y_bar', '_'));
    }

    public function testSimplify()
    {
        $this->assertSame('simpler', Helper::simplify('SimplEr'));
        $this->assertSame('simpler', Helper::simplify('S im-plEr'));
    }
}
