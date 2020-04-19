<?php

namespace App\Exceptions;

use Exception;

class UserException extends Exception
{
    public const THROW_NONE = 0;
    public const INCORRECT_PASSWORD = 401;
    public const USER_NOT_EXISTS = 404;

    public function __construct(
        String $message = null,
        Int $code = self::THROW_NONE,
        Exception $previous = null
    ) {
        if (is_null($message)) {
            switch ($code) {
                case self::USER_NOT_EXISTS:
                    $message = 'User does not exists';
                    break;
            }
        }

        parent::__construct($message, $code, $previous);
    }

    public function userNotExists()
    {
        return $this->code === self::USER_NOT_EXISTS;
    }

    public function incorrectPassowrd()
    {
        return $this->code === self::INCORRECT_PASSWORD;
    }
}
