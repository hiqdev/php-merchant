<?php

namespace hiqdev\php\merchant\response;

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
        return $this->method = $method;
    }

    public function getMethod()
    {
        return $this->method;
    }
}
