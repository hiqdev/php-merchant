<?php

namespace hiqdev\php\merchant;

use Closure;
use ReflectionClass;
use InvalidArgumentException;

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
        $config = array_merge((array)self::$_defaults, (array)static::$_defaults, (array)$config);
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
        foreach ((array)$names as $rename => $name) {
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
        return '/' . join('/', [$this->system, $this->currency, $this->username]);
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
        return $this->get($return.'Page', $this->basePage . $return);
    }

    public function getSite()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public function getSiteUrl()
    {
        return $this->scheme . '://' . $this->site;
    }

    public function getPaymentDescription()
    {
        return $this->site . ': deposit ' . $this->username;
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
        return floor($sum*100);
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
        foreach ($this->getInputs() as $name => $value)
        {
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

    static public function renderTag($name, $content=null, $attributes = [])
    {
        $res = "<$name" . static::renderTagAttributes($attributes) . '>';
        return is_null($content) ? $res : $res . $content . "</$name>";
    }

    static public function renderTagAttributes($attributes)
    {
        $res = '';
        foreach ($attributes as $k => $v)
        {
            $res .= " $k=\"$v\"";
        }
        return $res;
    }

    static public function curl($url, $data)
    {
        $ch = curl_init($this->actionUrl);
        curl_setopt_array($ch, array(
            CURLOPT_USERAGENT       => 'curl/0.00 (php 5.x; U; en)',
            CURLOPT_RETURNTRANSFER  => 1,
            CURLOPT_SSL_VERIFYPEER  => FALSE,
            CURLOPT_SSL_VERIFYHOST  => 0, /// XXX this is the problem with PayPal
        /// CURLOPT_SSLVERSION      => 3, /// XXX this is the problem with PayPal
        /// CURLOPT_TLSV1           => 1, /// ??? recomendation from PayPal
            CURLOPT_POST            => 1,
            CURLOPT_POSTFIELDS      => is_array($data) ? http_build_query($data) : $data,
        ));
        $result = curl_exec($ch);
    }
}
