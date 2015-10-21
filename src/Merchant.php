<?php

namespace hiqdev\php\merchant;

use Closure;

abstract class Merchant
{
    protected $_secret;
    protected $_secret2;
    protected $_config;
    protected $_defaults = [
        'basePage'  => '/merchant/pay/',
        'proto'     => 'https',
        'fee'       => 0,
        'method'    => 'POST',
    ];

    public function __construct($config = [])
    {
        $config = array_merge((array)$this->_defaults, (array)$config);
        $this->_$secret  = $config['secret'];
        $this->_$secret2 = $config['secret'];
        unset($config['secret'], $config['secret2']);
        $this->_config = $config;
    }

    abstract public function getInputs();

    abstract public function validateConfirmation($data);

    public function get($name, $default = null)
    {
        $res = $this->_config[$name]
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

    public function getInfopath()
    {
        return '/' . join('/', [$this->system, $this->currency, $this->client]);
    }

    public function getConfirmUrl()
    {
        return $this->getActionUrl('confirm');
    }

    public function getSuccessUrl()
    {
        return $this->getActionUrl('success');
    }

    public function getFailureUrl()
    {
        return $this->getActionUrl('failure');
    }

    public function getActionUrl($action)
    {
        return $this->siteUrl . $this->getActionPage($action) . $this->infopath;
    }

    public function getSite()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public function getSiteUrl()
    {
        return $this->proto . '://' . $this->site;
    }

    public function getActionPage($action)
    {
        return $this->get($action.'Page', $this->basePage . $action);
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

    public function formatMoney($sum)
    {
        return number_format($sum, 2, '.', '')
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
}
