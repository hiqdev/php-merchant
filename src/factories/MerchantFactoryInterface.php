<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2018, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\factories;

use hiqdev\php\merchant\credentials\CredentialsInterface;
use hiqdev\php\merchant\merchants\MerchantInterface;
use hiqdev\php\merchant\merchants\PaymentCardMerchantInterface;

/**
 * Interface MerchantFactoryInterface.
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface MerchantFactoryInterface
{
    /**
     * Builds a merchant using the given $credentials
     * may be used when credentials could not be distinguished
     * with the merchant name.
     *
     * @param string $name
     * @param CredentialsInterface $credentials
     * @return MerchantInterface|PaymentCardMerchantInterface
     */
    public function buildUsingCredentials(string $name, CredentialsInterface $credentials): MerchantInterface;

    /**
     * Builds a Merchant and guesses the credentials
     *
     * @param string $name
     * @return MerchantInterface
     */
    public function build(string $name): MerchantInterface;

}
