<?php

final class ConcurrencyException extends Exception
{
    private $retry;

    public function __construct($message, $retry = false)
    {
        parent::__construct($message);
        $this->retry = $retry;
    }

    public function getRetry()
    {
        return $this->retry;
    }
}
