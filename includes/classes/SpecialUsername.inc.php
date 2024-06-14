<?php

abstract class SpecialUsername
{
    public $uid;
    public $uname;
    public $type = 0;
    public static $internalCount = 0;

    abstract public function GetLowFiName();

    abstract public function GetMediumFiName();

    abstract public function GetHiFiName();
}
