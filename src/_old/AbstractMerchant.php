<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant;

abstract class AbstractMerchant implements MerchantInterface
{
    /**
     * @var string Unique merchant identification. E.g. paypal, webmoney_usd, webmoney_rub.
     */
    public $id;

    public $library;

    /**
     * @var string gateway name, corresponding to namespace, e.g.: paypal, webmoney
     */
    public $gateway;

    /**
     * @var array Data that will be passed to Request
     * @see request
     */
    public $data = [];

    /**
     * @var string gateway label that should be used to display the merchant, e.g.: PayPal, WebMoney
     */
    public $label;

    /**
     * @var string request class name
     */
    public $requestClass;

    /**
     * @var string response class name
     */
    public $responseClass;

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @return string
     */
    abstract public function getGatewayNamespacePart();

    /**
     * {@inheritdoc}
     * @return AbstractRequest
     */
    public function request($type, array $data)
    {
        return Helper::createObject([
            'class'     => $this->getRequestClass(),
            'merchant'  => $this,
            'type'      => $type,
            'data'      => array_merge((array) $this->data, (array) $data),
        ]);
    }

    /**
     * @param RequestInterface $request
     * @return AbstractResponse
     */
    public function response(RequestInterface $request)
    {
        return Helper::createObject([
            'class'     => $this->getResponseClass(),
            'merchant'  => $this,
            'request'   => $request,
        ]);
    }

    /**
     * @return string
     */
    public function getRequestClass()
    {
        return $this->requestClass ?: Helper::findClass($this->gateway, $this->library, 'Request');
    }

    /**
     * @return string
     */
    public function getResponseClass()
    {
        return $this->responseClass ?: Helper::findClass($this->gateway, $this->library, 'Response');
    }
}
