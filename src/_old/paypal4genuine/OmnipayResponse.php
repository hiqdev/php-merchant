<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\paypal;

use hiqdev\php\merchant\Helper;

/**
 * PayPal Omnipay Response class.
 */
class OmnipayResponse extends \hiqdev\php\merchant\OmnipayResponse
{
    /**
     * Get payment time.
     * @return string
     */
    public function getTime()
    {
        return Helper::isotime($this->getVar('LMI_SYS_TRANS_DATE') . ' Europe/Moscow');
    }

    /**
     * Get payer info.
     * @return string
     */
    public function getPayer()
    {
        return $this->getVar('LMI_PAYER_PURSE') . '/' . $this->getVar('LMI_PAYER_WM');
    }
}
