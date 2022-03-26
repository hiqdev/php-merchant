<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit\factories;

use hiqdev\php\merchant\factories\OmnipayFactory;
use Omnipay\PayPal\Gateway;
use PHPUnit\Framework\TestCase;

/**
 * Helper test suite.
 */
class OmnipayFactoryTest extends TestCase
{
    /**
     * @var OmnipayFactory
     */
    protected $factory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->factory = new OmnipayFactory();
    }

    public function testCreatesGateway()
    {
        $gateway = $this->factory->build('PayPal', []);
        $this->assertInstanceOf(Gateway::class, $gateway);
    }

    public function testPassesParametersToTheGateway()
    {
        $params = [
            'purse' => 'test@example.com',
            'secret' => 'phpunit',
        ];

        $gateway = $this->factory->build('PayPal', $params);
        \DMS\PHPUnitExtensions\ArraySubset\Assert::assertArraySubset($params, $gateway->getParameters());
    }
}
