<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2026, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\merchants\unityfinance;

use hiqdev\php\merchant\merchants\interkassa\InterKassaMerchant;
use Omnipay\InterKassa\UnityFinanceGateway;

/**
 * UnityFinance is an Interkassa subcompany serving the same Checkout Protocol on a
 * different domain (`old-pay.unityfinance.com` instead of `sci.interkassa.com`), so this
 * only overrides which gateway class gets built.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class UnityFinanceMerchant extends InterKassaMerchant
{
    protected function createGateway()
    {
        return $this->gatewayFactory->build(UnityFinanceGateway::class, [
            'checkoutId' => $this->credentials->getPurse(),
            'signKey' => $this->credentials->getKey1(),
            'signAlgorithm' => 'md5',
        ]);
    }
}
