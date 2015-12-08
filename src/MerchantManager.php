<?php

/*
 * Generalization over Omnipay and Payum
 *
 * @link      https://github.com/hiqdev/php-merchant
 * @package   php-merchant
 * @license   BSD-3-Clause
 * @copyright Copyright (c) 2015, HiQDev (http://hiqdev.com/)
 */

namespace hiqdev\php\merchant;

/**
 * Ideas taken from Yii.
 */
class MerchantManager
{
    public static function configure($object, array $config)
    {
        foreach ($config as $name => $value) {
            $object->$name = $value;
        }

        return $object;
    }

    /**
     * Very straightforward object creator.
     */
    public static function createObject(array $config)
    {
        $object = new $config['class']();
        unset($config['class']);
        static::configure($object, $config);

        return $object;
    }

    public static function create(array $config)
    {
        $class = $config['class'];
        if (!$config['class']) {
            $config['class'] = 'hiqdev\php\merchant\\' . $config['library'] . 'Merchant';
        }

        return static::createObject($config);
    }

}
