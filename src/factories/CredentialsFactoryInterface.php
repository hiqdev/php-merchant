<?php

namespace hiqdev\php\merchant\factories;

use hiqdev\php\merchant\credentials\CredentialsInterface;

interface CredentialsFactoryInterface
{
    public function build(string $name): CredentialsInterface;
}
