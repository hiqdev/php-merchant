<?php

namespace hiqdev\php\merchant;

/**
 * User exception - recoverable error caused by user.
 */
class UserException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'User Exception';
    }
}
