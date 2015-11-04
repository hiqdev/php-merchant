<?php

/*
 * PHP merchant library
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant;

use Closure;
use InvalidArgumentException;
use ReflectionClass;

/**
 * Merchant abstract class.
 *
 * Merchant for payment system:
 * ```
 * class SystemMerchant extends Merchant
 * {
 *     public function getInputs()
 *     {
 *         return [
 *             'system_sum_field'                 => $this->total,
 *             'system_payment_description_field' => $this->paymentDescription,
 *             ...
 *         ];
 *     }
 *
 *     public function validateConfirmation($data)
 *     {
 *         if (!validateData($data)) {
 *             return 'Wrong data';
 *         }
 *         if (check_sum($data) === $data['check_sum']) {
 *             return 'Wrong check sum';
 *         }
 *         return;
 *     }
 * }
 * ```
 *
 * Merchant configuration:
 * ```
 * $merchantConfig = [
 *     'system'     => 'paysystem',
 *     'purse'      => 'XYZ-123456',
 *     'secret'     => 'bigSecret',
 * ];
 * ```
 *
 * Payment page:
 * ```
 * use hiqdev\php\merchant\Merchant
 * $merchant = Merchant::create($merchantConfig);
 * $request = $merchant->setRequest([
 *     'sum'         => $sumToPay,
 *     'id'          => $myInvoiceId,
 *     'description' => $myInvoiceDescription,
 * ]);
 * print $request->renderForm();
 * ```
 *
 * Confirm page:
 * ```
 * use hiqdev\php\merchant\Merchant
 * $merchant = Merchant::create($merchantConfig);
 * $payment = $merchant->getPayment($_REQUEST);
 * if ($payment->error) {
 *     die($payment->error);
 * } else {
 *     $payment = $merchant->payment;
 *     $mydb->exec('
 *          INSERT INTO payment (sum, fee, from, transaction_id, ...)
 *          VALUES (:sum, :from, :transaction_id, ...)
 *     ', [
 *         'sum'            => $payment->sum,
 *         'from'           => $payment->from,
 *         'transaction_id' => $payment->id,
 *     ]);
 *     die($payment->confirmText);
 * }
 * ```
 *
 * Success page (fail page is similar):
 * ```
 * flash("Payment was successfull!");
 * redirect($otherPage);
 * ```
 */
abstract class Merchant
{
    protected $_error;
    protected $_secret;
    protected $_secret2;
    protected $_config;
    protected static $_defaults = [
        'basePage'    => '/merchant/pay/',
        'scheme'      => 'https',
        'fee'         => 0,
        'quantity'    => 1,
        'method'      => 'POST',
        'currency'    => 'usd',
        'username'    => '',
        'isCart'      => true,
        'confirmText' => 'OK',
    ];

    public function __construct($config = [])
    {
        $config = array_merge((array) self::$_defaults, (array) static::$_defaults, (array) $config);
        if (!$config['id']) {
            throw new InvalidArgumentException('No merchant ID given');
        }
        $this->_secret  = $config['secret'];
        $this->_secret2 = $config['secret2'];
        unset($config['secret'], $config['secret2']);
        $this->_config = $config;
    }

    public static function create($config)
    {
        $reflection = new ReflectionClass(static::guessClass($config));

        return $reflection->newInstanceArgs([$config]);
    }

    public static function guessClass($config)
    {
        if ($config['class']) {
            return $config['class'];
        }
        $system = $config['system'] ?: $config['name'];
        if (!$system) {
            throw new InvalidArgumentException('No merchant class or system given');
        }

        return "hiqdev\\php\\merchant\\$system\\Merchant";
    }

    abstract public function getInputs();

    abstract public function validateConfirmation($data);

    public function getConfig()
    {
        return $this->_config;
    }
    public function get($name, $default = null)
    {
        $res = $this->_config[$name];
        $res = is_null($res) ? $default : $res;

        return $res instanceof Closure ? call_user_func($res, $this) : $res;
    }

    public function set($name, $value)
    {
        $this->_config[$name] = $value;
    }

    public function __get($name)
    {
        if (array_key_exists($name, $this->_config)) {
            return $this->get($name);
        }
        $getter = 'get' . $name;
        if (!method_exists($this, $getter)) {
            throw new InvalidArgumentException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }

        return $this->$getter();
    }

    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } else {
            $this->set($name, $value);
        }
    }

    public function mget($names)
    {
        foreach ((array) $names as $rename => $name) {
            $res[is_int($rename) ? $name : $rename] = $this->{$name};
        }

        return $res;
    }

    public function mset($values)
    {
        foreach ($values as $k => $v) {
            $this->set($k, $v);
        }
    }

    public function error($name)
    {
        $this->_error = $name;

        return false;
    }

    public function getError()
    {
        return $this->_error;
    }

    public function getInfopath()
    {
        return '/' . implode('/', [$this->system, $this->currency, $this->username]);
    }

    public function getConfirmUrl()
    {
        return $this->getReturnUrl('confirm');
    }

    public function getSuccessUrl()
    {
        return $this->getReturnUrl('success');
    }

    public function getFailureUrl()
    {
        return $this->getReturnUrl('failure');
    }

    public function getReturnUrl($return)
    {
        return $this->siteUrl . $this->getReturnPage($return) . '?' . http_build_query([
            'merchant' => $this->id,
            'system'   => $this->system,
            'currency' => $this->currency,
            'username' => $this->username,
        ]);
    }

    public function getReturnPage($return)
    {
        return $this->get($return . 'Page', $this->basePage . $return);
    }

    public function getSite()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public function getSiteUrl()
    {
        return $this->scheme . '://' . $this->site;
    }

    public function getInvoiceDescription()
    {
        return $this->site . ': deposit ' . $this->username;
    }

    public function getPaymentFee()
    {
        return $this->paymentTotal - $this->paymentFee;
    }

    public function getPaymentSum()
    {
        return $this->paymentTotal - $this->paymentFee;
    }

    public function getPaymentLabel()
    {
        return 'From: ' . $this->from;
    }

    public function getTotal()
    {
        return $this->formatMoney($this->convertTo($this->sum + $this->fee));
    }

    public function getCents()
    {
        return $this->formatCents($this->total);
    }

    public function getInvoiceId()
    {
        return implode('_', [$this->uniqId, $this->username, $this->cents]);
    }

    public function getFormId()
    {
        return implode('_', ['merchant', $this->id]);
    }

    protected $_time;

    public function getTime()
    {
        if ($this->_time === null) {
            $this->_time = date('c');
        }

        return $this->_time;
    }

    protected $_uniqId;

    public function getUniqId()
    {
        if ($this->_uniqId === null) {
            $this->_uniqId = uniqid();
        }

        return $this->_uniqId;
    }

    public function validateMoney($sum)
    {
        return preg_match('/^\d+(\.\d{1,2})?$/', $sum) ? $this->formatMoney($sum) : null;
    }

    public function formatMoney($sum)
    {
        return number_format($sum, 2, '.', '');
    }

    public function formatCents($sum)
    {
        return floor($sum * 100);
    }

    public function formatDatetime($str = null)
    {
        return date('c', strtotime($str));
    }

    public function convertTo($sum)
    {
        /// XXX add
        return $sum;
    }
    public function renderForm()
    {
        $inputs = '';
        foreach ($this->getInputs() as $name => $value) {
            $inputs .= static::renderTag('input', null, [
                'type'  => 'hidden',
                'name'  => $name,
                'value' => $value,
            ]);
        }

        return static::renderTag('form', $inputs, [
            'id'     => $this->formId,
            'action' => $this->actionUrl,
            'method' => $this->method,
        ]);
    }

    public static function renderTag($name, $content = null, $attributes = [])
    {
        $res = "<$name" . static::renderTagAttributes($attributes) . '>';

        return is_null($content) ? $res : $res . $content . "</$name>";
    }

    public static function renderTagAttributes($attributes)
    {
        $res = '';
        foreach ($attributes as $k => $v) {
            $res .= " $k=\"$v\"";
        }

        return $res;
    }

    public static function curl($url, $data)
    {
        $ch = curl_init($this->actionUrl);
        curl_setopt_array($ch, [
            CURLOPT_USERAGENT      => 'curl/0.00 (php 5.x; U; en)',
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0, /// XXX this is the problem with PayPal
        /// CURLOPT_SSLVERSION      => 3, /// XXX this is the problem with PayPal
        /// CURLOPT_TLSV1           => 1, /// ??? recomendation from PayPal
            CURLOPT_POST       => 1,
            CURLOPT_POSTFIELDS => is_array($data) ? http_build_query($data) : $data,
        ]);
        $result = curl_exec($ch);
    }
}
