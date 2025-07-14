<?php

abstract class AjaxUtility
{
    public static function SmartEscape($value)
    {
        // Quote if not integer
        if (!is_numeric($value)) {
            $value = "'" . mysqli_real_escape_string(DBi::$conn, $value) . "'";
        }

        return $value;
    }
}
