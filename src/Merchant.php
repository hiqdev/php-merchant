<?php

namespace hiqdev\php\merchant;

use Closure;
use ReflectionClass;

abstract class Merchant
{
    protected $_secret;
    protected $_secret2;
    protected $_config;
    protected static $_defaults = [
        'basePage'  => '/merchant/pay/',
        'proto'     => 'https',
        'fee'       => 0,
        'method'    => 'POST',
        'currency'  => 'usd',
        'client'    => '',
    ];

    public function __construct($config = [])
    {
        $config = array_merge((array)self::$_defaults, (array)static::$_defaults, (array)$config);
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
        $name = $config['name'] ?: $config['id'];
        if (!$name) {
            throw new SystemException('No merchant class given!');
        }
        return "hiqdev\\php\\merchant\\$name\\Merchant";
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
            throw new SystemException('Getting unknown property: ' . get_class($this) . '::' . $name);
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

    public function mset($values)
    {
        foreach ($values as $k => $v) {
            $this->set($k, $v);
        }
    }
    public function getInfopath()
    {
        return '/' . join('/', [$this->name, $this->currency, $this->client]);
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
        return $this->siteUrl . $this->getReturnPage($return) . $this->infopath;
    }

    public function getSite()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public function getSiteUrl()
    {
        return $this->proto . '://' . $this->site;
    }

    public function getReturnPage($return)
    {
        return $this->get($return.'Page', $this->basePage . $return);
    }

    public function getDescription()
    {
        return $this->site . ': deposit ' . $this->client;
    }

    public function getTotal()
    {
        return $this->formatMoney($this->convertTo($this->sum + $this->fee));
    }

    public function getInvoiceNo()
    {
        return $this->client . '_' . $this->formatCents($this->sum);
    }

    public function getFormId()
    {
        return implode('_', ['merchant', $this->name, $this->currency]);
    }

    public function formatMoney($sum)
    {
        return number_format($sum, 2, '.', '');
    }

    public function formatCents($sum)
    {
        return floor($sum*100);
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
}
