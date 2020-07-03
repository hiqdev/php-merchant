<?php
declare(strict_types=1);

namespace hiqdev\php\merchant;

class Client
{
    /** @var string|null */
    public ?string $id = null;

    /** @var string|null */
    public ?string $remoteId = null;

    public string $login;

    public function __toString(): string
    {
        return $this->login;
    }

    /**
     * @return string|null
     */
    public function id(): ?string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function remoteId(): ?string
    {
        return $this->remoteId;
    }

    /**
     * @return string
     */
    public function login(): string
    {
        return $this->login;
    }
}
