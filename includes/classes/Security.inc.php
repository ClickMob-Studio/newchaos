<?php

class Security
{
    /*
    ** Protects all input against malicioud injection.
    */
    public static function ProtectInput()
    {
       
            $in = [&$_GET, &$_POST, &$_COOKIE, &$_FILES];
            foreach ($in as $k => $v) {
                foreach ($v as $key => $val) {
                    if (!is_array($val)) {
                        $in[$k][$key] = addslashes(strip_tags($val));
                        continue;
                    }
                    $in[] = &$in[$k][$key];
                }
            }
            unset($in);
        
    }

    /*
    ** Verifies the refreshing speed between two consecutive calls.
    */
    public static function IsFastRefresher($interval)
    {
        $keyName = 'PS-LastAction';
        if (isset($_SESSION[$keyName])) {
            if ($_SESSION[$keyName] > microtime(true) - $interval) {
                $_SESSION[$keyName] = microtime(true);

                return true;
            }
        }
        $_SESSION[$keyName] = microtime(true);

        return false;
    }

    public static function IPIsBanned($ip)
    {
        $result = DBi::$conn->query("SELECT `ip` FROM `bansIP` WHERE `ip`='$ip'");

        return mysqli_num_rows($result);
    }

    public static function IPIsAuthorized($ip)
    {
        // TODO this is temporary
        return 1;
        $result = DBi::$conn->query("SELECT `autip` FROM `autIP` WHERE `autip` like '" . $ip . "'");

        return mysqli_num_rows($result);
    }

    public static function GetBannedIPReason($ip)
    {
        $result = DBi::$conn->query("SELECT `ip`, `reason` FROM `bansIP` WHERE `ip`='$ip'");
        $worked = mysqli_fetch_array($result);

        return $worked['reason'];
    }

    public static function IsReferredByItself()
    {
        $checkUrl = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME'];
        echo 'Check url: ' . $checkUrl . '<br>';
        echo 'Referrer: ' . $_SERVER['HTTP_REFERER'] . '<br>';
        if ($_SERVER['HTTP_REFERER'] == $checkUrl) {
            return true;
        }

        return false;
    }

    public static function IsSameHost()
    {
        if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == '') {
            return true;
        }

        if (strstr($_SERVER['HTTP_REFERER'], 'paypal') !== false) {
            return true;
        }

        $ref = parse_url($_SERVER['HTTP_REFERER']);

        return $_SERVER['SERVER_NAME'] == $ref['host'];
    }

    public static function CheckDirectAccess($file)
    {
        //if (strpos($_SERVER['PHP_SELF'], $file) !== false)
        //	die('You cannot access this file directly.');
    }

    /**
     * Generates random token.
     *
     * @param int    $length
     * @param string $salt
     *
     * @return string
     */
    public static function RandomToken($length = 40, $salt = '')
    {
        return substr(sha1(mt_rand() . rand(78, 998) . $salt), 0, $length);
    }
}
