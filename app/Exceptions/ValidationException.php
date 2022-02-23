<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    protected string $errorMessage;

    protected int|null $errorCode;

    public function __construct($errorMessage, $errorCode = null,)
    {
        $this->errorMessage = $errorMessage;
        $this->message = $errorMessage;
        $this->errorCode = $errorCode;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return int|null
     */
    public function getErrorCode(): int|null
    {
        return $this->errorCode;
    }
}
