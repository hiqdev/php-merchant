<?php
declare(strict_types=1);

namespace hiqdev\php\merchant\merchants;

interface CanIgnore3dSecureInterface
{
    public function setIgnore3dSecure(): CanIgnore3dSecureInterface;

    public function is3dSecureIgnorred(): bool;
}
