<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class Cmp
{
    public static $fieldName = '';

    public static function Comp($a, $b)
    {
        $arr = get_object_vars($a);
        $a = $arr[self::$fieldName];
        $arr = get_object_vars($b);
        $b = $arr[self::$fieldName];
        if (is_numeric($a) && is_numeric($b)) {
            return $a - $b;
        }

        return strcmp($a, $b);
    }
}
