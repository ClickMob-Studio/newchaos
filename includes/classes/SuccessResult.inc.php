<?php

final class SuccessResult extends Exception
{
    private $view;

    public function __construct($message, $view = null)
    {
        parent::__construct($message);
        $this->view = $view;
    }

    public function getView()
    {
        return $this->view;
    }
}
