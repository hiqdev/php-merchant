<?php


namespace hiqdev\php\merchant\merchants\bitpay;

use Bitpay\Bitpay;
use hiqdev\php\merchant\exceptions\VerificationFailedException;
use Omnipay\BitPay\Message\CompletePurchaseResponse;
use Omnipay\Common\Message\ResponseInterface;

/**
 * Class CompletePurchaseResponseVerifier
 *
 * @author Dmytro Naumenko <d.naumenko.a@gmail.com>
 */
class CompletePurchaseResponseVerifier
{
    /**
     * @var string
     */
    protected $posDataHash;
    /**
     * @var array
     */
    private $posData;
    /**
     * @var Bitpay
     */
    private $adapter;
    /**
     * @var CompletePurchaseResponse
     */
    private $response;

    /**
     * CompletePurchaseResponseVerifier constructor.
     *
     * @param $adapter
     * @param ResponseInterface|CompletePurchaseResponse $response
     */
    public function __construct($adapter, ResponseInterface $response)
    {
        $this->adapter = $adapter;
        $this->response = $response;
    }

    public function verify()
    {
        if (!$this->response->isSuccessful()) {
            throw new VerificationFailedException('Response is not successful');
        }

        $this->ensureNotExpired();
        $this->formatPrice();
    }

    private function ensureNotExpired()
    {
        $posData = $this->posData;

        if (($posData['d'] > time()) || ($posData['d'] < strtotime('-6 day', time()))) {
            throw new VerificationFailedException('Invoice has been expired');
        }
    }

    private function formatPrice()
    {
        $this->response->getData()->setPrice(sprintf("%01.2f", round($this->response->getData()->getPrice(), 2)));
    }
}
