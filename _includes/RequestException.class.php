<?php

class RequestException extends Exception
{
    private int $statusCode;

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function __construct(int $statusCode = 500 ,$message = "", $code = 0, Throwable $previous = null)
    {
        $code = $statusCode;
        parent::__construct($message, $code, $previous);
    }

}