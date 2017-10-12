<?php

namespace hiqdev\php\merchant\response;

/**
 * Class RedirectPurchaseResponse
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class RedirectPurchaseResponse
{
    /**
     * @var string
     */
    private $url;
    /**
     * @var array
     */
    private $data;
    /**
     * @var string
     */
    private $method = 'POST';

    public function __construct(string $url, array $data)
    {
        $this->url = $url;
        $this->data = $data;
    }

    public function getRedirectUrl(): string
    {
        return $this->url;
    }

    public function getRedirectData(): array
    {
        return $this->data;
    }

    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
    }

    public function getMethod()
    {
        return $this->method;
    }
}
