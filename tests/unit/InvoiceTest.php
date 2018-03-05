<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\tests\unit;

use hiqdev\php\merchant\Invoice;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

/**
 * Helper test suite.
 */
class InvoiceTest extends TestCase
{
    public function testInvoiceProperties()
    {
        $invoice = new Invoice();

        $invoice->setId('someid');
        $this->assertSame('someid', $invoice->getId());
        $invoice->setId(123);
        $this->assertSame('123', $invoice->getId());
        $invoice->setId(null);
        $this->assertSame('', $invoice->getId());

        $invoice->setClient('username');
        $this->assertSame('username', $invoice->getClient());
        $invoice->setClient(123);
        $this->assertSame('123', $invoice->getClient());

        $amount = new Money(1246, new Currency('USD'));
        $invoice->setAmount($amount);
        $this->assertSame($amount, $invoice->getAmount());
        $this->assertSame($amount->getCurrency(), $invoice->getCurrency());

        $invoice->setDescription('Descr');
        $this->assertSame('Descr', $invoice->getDescription());

        $invoice->setReturnUrl('return');
        $this->assertSame('return', $invoice->getReturnUrl());
        $invoice->setNotifyUrl('notify');
        $this->assertSame('notify', $invoice->getNotifyUrl());
        $invoice->setCancelUrl('cancel');
        $this->assertSame('cancel', $invoice->getCancelUrl());

        $invoice->setReturnMethod('POST');
        $this->assertSame('POST', $invoice->getReturnMethod());
        $invoice->setNotifyMethod('GET');
        $this->assertSame('GET', $invoice->getNotifyMethod());
        $invoice->setCancelMethod('DELETE');
        $this->assertSame('DELETE', $invoice->getCancelMethod());
    }
}
