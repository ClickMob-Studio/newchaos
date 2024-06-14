<?php

abstract class AjaxUtility
{
    public static function SmartEscape($value)
    {
        // Stripslashes
        if (get_magic_quotes_gpc()) {
            $value = stripslashes($value);
        }
        // Quote if not integer
        if (!is_numeric($value)) {
            $value = "'" . mysqli_real_escape_string(DBi::$conn, $value) . "'";
        }

        return $value;
    }
}
