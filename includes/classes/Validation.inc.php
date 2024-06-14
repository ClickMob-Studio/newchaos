<?php

/**
 * Class is used to implement validations.
 */
final class Validation
{
    public static function IsInteger($num)
    {
        return (is_numeric($num) && intval($num) == $num) || empty($num);
    }

    public static function IsDouble($num)
    {
        return is_numeric($num) && doubleval($num) == $num;
    }

    public static function IsID($num)
    {
        return is_numeric($num) && intval($num) == $num;
    }
}
