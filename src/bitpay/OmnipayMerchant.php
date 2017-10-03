<?php
/**
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015-2017, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant\bitpay;

use hiqdev\php\merchant\Helper;
use hiqdev\php\merchant\RequestInterface;
use Omnipay\BitPay\Gateway;
use Omnipay\Common\GatewayInterface;

/**
 * BitPay Omnipay Merchant class.
 */
class OmnipayMerchant extends \hiqdev\php\merchant\OmnipayMerchant
{
    /**
     * @return Gateway|GatewayInterface
     */
    public function getWorker()
    {
        return parent::getWorker();
    }

    /**
     * @param array $data
     * @return array
     */
    public function prepareData(array $data)
    {
        return array_merge([
            'token' => $data['purse'],
            'privateKey' => $data['secret'],
            'publicKey' => $data['secret2'],
            'testMode' => false
        ], $data);
    }

    public function request($type, array $data)
    {
        if (!empty($data['inputs'])) {
            return Helper::createObject([
                'class' => FakeRequest::class,
                'merchant' => $this,
                'type' => $type,
                'data' => array_merge((array)$this->data, (array)$data),
            ]);
        }

        return parent::request($type, $data);
    }

    public function response(RequestInterface $request)
    {
        if ($request instanceof FakeRequest) {
            /** @var PurchaseRequest $realRequest */
            $realRequest = $request->merchant->getWorker()->purchase($this->data);

            return new PurchaseResponse($realRequest, $this->data['inputs']);
        }

        return parent::response($request);
    }
}
