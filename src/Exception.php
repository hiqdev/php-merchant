<?php

namespace hiqdev\php\merchant;

/**
 * Generic exception.
 */
class Exception extends \Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Exception';
    }
}
