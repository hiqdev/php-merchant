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
 * The sign algorithm is configured per checkout by UnityFinance support and isn't
 * necessarily the same for every account (unlike InterKassaMerchant's checkout, which is
 * known to be MD5), so it's read from the credentials' second key slot rather than
 * hardcoded. Defaults to SHA256, the algorithm UnityFinance currently documents as the
 * checkout default, when support hasn't specified otherwise.
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
            'signAlgorithm' => $this->credentials->getKey2() ?: 'sha256',
        ]);
    }
}
