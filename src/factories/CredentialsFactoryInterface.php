<?php

namespace hiqdev\php\merchant\factories;

use hiqdev\php\merchant\credentials\CredentialsInterface;

/**
 * Interface CredentialsFactoryInterface
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
interface CredentialsFactoryInterface
{
    public function build(string $name): CredentialsInterface;
}
