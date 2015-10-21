<?php

namespace hiqdev\php\merchant;

/**
 * System exception - unrecoverable error caused by programmer.
 */
class SystemException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'System Exception';
    }
}
