<?php

/*
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\webmoney;

use hiqdev\php\merchant\Helper;

/**
 * WebMoney Omnipay Response class.
 */
class OmnipayResponse extends \hiqdev\php\merchant\OmnipayResponse
{
    public function getTime()
    {
        return Helper::isotime($this->getVar('LMI_SYS_TRANS_DATE') . ' Europe/Moscow');
    }

    public function getPayer()
    {
        return $this->getVar('LMI_PAYER_PURSE') . '/' . $this->getVar('LMI_PAYER_WM');
    }
}
