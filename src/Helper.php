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
class Helper
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

    /**
     * Creates merchant according to given config.
     */
    public static function create(array $config)
    {
        return static::createObject(array_merge([
            'data'  => $config,
            'class' => static::findClass($config['gateway'], $config['library'], 'Merchant'),
        ], $config));
    }

    public static function findClass($gateway, $library, $what)
    {
        $library = $library ?: 'Omnipay';
        $class = static::buildClass($gateway, $library, $what);

        return class_exists($class) ? $class : static::buildClass(null, $library, $what);
    }

    /**
     * Builds class name: hiqdev\php\merchant\gateway\LibraryWhat
     */
    public static function buildClass($gateway, $library, $what)
    {
        $gateway = $gateway ? static::simplify($gateway) . '\\' : '';
        return 'hiqdev\php\merchant\\' . $gateway . $library . $what;
    }

    /**
     * Converts an ID into a CamelCase name.
     * Words in the ID separated by `$separator` (defaults to '-') will be concatenated into a CamelCase name.
     * For example, 'post-tag' is converted to 'PostTag'.
     * Taken from Yii 2 Inflector.
     *
     * @param string $id        the ID to be converted
     * @param string $separator the character used to separate the words in the ID
     *
     * @return string the resulting CamelCase name
     */
    public static function id2camel($id, $separator = '-')
    {
        return str_replace(' ', '', ucwords(implode(' ', explode($separator, $id))));
    }

    /**
     * Converts to simple name - only lowercase letters and numbers.
     */
    public static function simplify($name)
    {
        return preg_replace('/[^a-z0-9]+/', '', strtolower($name));
    }
    public static function isotime($time)
    {
        return date('c', strtotime($time));
    }
}
