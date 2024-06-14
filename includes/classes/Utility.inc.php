<?php

class Utility
{
    public static function SendMassMail($idSender = 2000, $subject = 'Default Subject', $text = 'Default message')
    {
        return Announcement::Add($idSender, $text, $subject);
    }

    public static function SendMassEvents($idSender = 2000, $subject = 'Default Subject', $text = 'Default message')
    {
        $active_tmp = time() - (72 * 60 * 60);
        $sql = "insert into events (`to`,`timesent`,`text`,`type`,`box`) select id,'" . time() . "','" . $text . "','" . $subject . "',0 from grpgusers  where lastactive>" . $active_tmp . '';
       DBi::$conn->query($sql);
    }

    public static function GenerateSlug(string $str = '', ?string $join = '-'): string
    {
        $str = str_replace('\'', '', $str);
        $str = preg_replace('~[^\pL\d]+~u', '-', $str);
        $str = iconv('utf-8', 'us-ascii//TRANSLIT', $str);
        $str = preg_replace('~[^-\w]+~', '', $str);
        $str = preg_replace('~-+~', $join, $str);
        $str = trim($str, $join);
        $str = strtolower($str);
        if (empty($str)) {
            return 'n-a';
        }

        return $str;
    }

    public static function GetUsername(int $id)
    {
        $query = 'SELECT id, username FROM grpgusers WHERE id = ' . $id;
        try {
            $select =DBi::$conn->query($query);
        } catch (\Exception $e) {
            throw new SoftException($e->getMessage()); // ew
        }
        if (!mysqli_num_rows($select)) {
            return false;
        }
        $row = mysqli_fetch_assoc($select);

        return self::FormatDBString($row['username']);
    }

    public static function sendXHR(array $data)
    {
        for ($i = 0; $i < ob_get_level(); ++$i) {
            ob_end_clean();
        }
        ob_start();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function substring($text, $length = 70, $ending = '&hellip;', $exact = false, $considerHtml = true)
    {
        $open_tags = [];
        if ($considerHtml === true) {
            if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
            $total_length = strlen($text);
            $truncate = '';
            foreach ($lines as $line_match) {
                if (!empty($line_match[1])) {
                    if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is',
                        $line_match[1])) {
                        // do nothing
                    } elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_match[1], $tag_match)) {
                        $pos = array_search($tag_match[1], $open_tags);
                        if ($pos !== false) {
                            unset($open_tags[$pos]);
                        }
                    } elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_match[1], $tag_match)) {
                        array_unshift($open_tags, strtolower($tag_match[1]));
                    }
                    $truncate .= $line_match[1];
                }
                $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', ' ',
                    $line_match[2]));
                if ($total_length + $content_length > $length) {
                    $left = $length - $total_length;
                    $entities_length = 0;
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|[0-9a-f]{1,6};/i', $line_match[2], $entities,
                        PREG_OFFSET_CAPTURE)) {
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entities_length <= $left) {
                                --$left;
                                $entities_length += strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }
                    $truncate .= substr($line_match[2], 0, $left + $entities_length);
                    break;
                }
                $truncate .= $line_match[2];
                $total_length += $content_length;
                if ($total_length >= $length) {
                    break;
                }
            }
        } else {
            if (strlen($text) <= $length) {
                return $text;
            }
            $truncate = substr($text, 0, $length - strlen($ending));
        }
        if ($exact !== true) {
            $spacepos = strrpos($truncate, ' ');
            if (isset($spacepos)) {
                $truncate = substr($truncate, 0, $spacepos);
            }
        }
        $truncate .= $ending;
        if ($considerHtml === true) {
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }

    public static function FormatDBString(?string $str = null, $dec = 0)
    {
        if (is_numeric($str)) {
            return number_format($str, $dec);
        }
        $str = stripslashes(htmlspecialchars($str));
        if ($dec === true) {
            $str = nl2br($str);
        }

        return $str;
    }

    public static function FormatDBTime($time, ?string $format = 'F jS, Y, H:i:s')
    {
        $date = new \DateTime($time);

        return $date->format($format);
    }

    public static function FormatMoneyBalance($balance = 0)
    {
        if ($balance < 0) {
            return '<font color="red">- $' . number_format($balance) . '</font>';
        }
        if ($balance > 0) {
            return '<font color="darkgreen">+ $' . number_format($balance) . '</font>';
        }

        return '<font color="#CCCC00">$' . number_format($balance) . '</font>';
    }

    public static function XFormatMoneyBalance($balance = 0)
    {
        $postfix = '';

        if ($balance >= 1000000) {
            $postfix = 'M';
            $balance = $balance / 1000000;
        } elseif ($balance >= 1000) {
            $postfix = 'K';
            $balance = $balance / 1000;
        }

        if ($balance < 0) {
            return '<font color="red">$' . number_format($balance) . $postfix . '</font>';
        }
        if ($balance > 0) {
            return '<font color="darkgreen">$' . number_format($balance) . $postfix . '</font>';
        }

        return '<font color="#CCCC00">$' . number_format($balance) . $postfix . '</font>';
    }

    public static function FormatBalance($balance = 0)
    {
        if ($balance < 0) {
            return '<font color="red">' . number_format($balance) . '</font>';
        }
        if ($balance > 0) {
            return '<font color="darkgreen">' . number_format($balance) . '</font>';
        }

        return '<font color="#CCCC00">' . number_format($balance) . '</font>';
    }

    public static function XFormatBalance($balance = 0)
    {
        $postfix = '';

        if ($balance >= 1000000) {
            $postfix = 'M';
            $balance = $balance / 1000000;
        } elseif ($balance >= 1000) {
            $postfix = 'K';
            $balance = $balance / 1000;
        }

        if ($balance < 0) {
            return '<font color="red">' . number_format($balance) . $postfix . '</font>';
        }
        if ($balance > 0) {
            return '<font color="darkgreen">' . number_format($balance) . $postfix . '</font>';
        }

        return '<font color="#CCCC00">' . number_format($balance) . $postfix . '</font>';
    }

    public static function GetPercent($currentValue, $maxValue)
    {
        return (int) floor(($currentValue / ($maxValue)) * 100);
    }

    public static function GetPercDiff($origin, $target)
    {
        $diff = $target - $origin;
        if ($origin < $target) {
            return (int) floor(($diff / $origin) * 100);
        }

        return (int) floor(($diff / $target) * 100);
    }

    public static function GetStatsAdj($perc)
    {
        $ranges = [
            // % Diff => Adjective
            -100000000 => '<font color="#66CCFF">terrible</font>',
            -10000000 => '<font color="#66CCDD">clowny</font>',
            -1000000 => '<font color="#66CCBB">laughable</font>',
            -100000 => '<font color="#66CC99">ridiculous</font>',
            -10000 => '<font color="#66CC77">miserable</font>',
            -1000 => '<font color="#66CC44">pitiful</font>',
            -100 => '<font color="#66CC11">shameful</font>',
            -10 => '<font color="#77CC00">lousy</font>',
            -5 => '<font color="#99CC00">slightly worse</font>',
            0 => '<font color="#CCCC00">equal</font>',
            5 => '<font color="#CCAA00">slightly better</font>',
            10 => '<font color="#CCAA00">nice</font>',
            100 => '<font color="#CC9900">grand</font>',
            1000 => '<font color="#CC8800">impressive</font>',
            10000 => '<font color="#CC6600">deadly</font>',
            100000 => '<font color="#CC5500">awesome</font>',
            1000000 => '<font color="#CC330">extreme</font>',
            10000000 => '<font color="#CC2200">INCREDIBLE</font>',
            100000000 => '<font color="#CC0000">ULTIMATE</font>',
        ];

        foreach ($ranges as $maxPerc => $adj) {
            if ($perc < $maxPerc) {
                return $adj;
            }
        }

        return $adj;
    }

    public static function GiveBarID($perc, $color = null)
    {
        return "class='bar_size red_bar_" . sprintf('%02d', floor($perc / (100 / 14))) . "'";
    }

    public static function GiveBarImg($perc, $color = 'orange', $ext = 'jpg')
    {
        if ($perc < 6) {
            return 'images/bar_images/' . $color . '/bar-00.' . $ext;
        }
        if ($perc < 12) {
            return 'images/bar_images/' . $color . '/bar-01.' . $ext;
        }
        if ($perc < 18) {
            return 'images/bar_images/' . $color . '/bar-02.' . $ext;
        }
        if ($perc < 25) {
            return 'images/bar_images/' . $color . '/bar-03.' . $ext;
        }
        if ($perc < 31) {
            return 'images/bar_images/' . $color . '/bar-04.' . $ext;
        }
        if ($perc < 37) {
            return 'images/bar_images/' . $color . '/bar-05.' . $ext;
        }
        if ($perc < 43) {
            return 'images/bar_images/' . $color . '/bar-06.' . $ext;
        }
        if ($perc < 50) {
            return 'images/bar_images/' . $color . '/bar-07.' . $ext;
        }
        if ($perc < 56) {
            return 'images/bar_images/' . $color . '/bar-08.' . $ext;
        }
        if ($perc < 62) {
            return 'images/bar_images/' . $color . '/bar-09.' . $ext;
        }
        if ($perc < 68) {
            return 'images/bar_images/' . $color . '/bar-10.' . $ext;
        }
        if ($perc < 75) {
            return 'images/bar_images/' . $color . '/bar-11.' . $ext;
        }
        if ($perc < 81) {
            return 'images/bar_images/' . $color . '/bar-12.' . $ext;
        }
        if ($perc < 87) {
            return 'images/bar_images/' . $color . '/bar-13.' . $ext;
        }
        if ($perc < 93) {
            return 'images/bar_images/' . $color . '/bar-14.' . $ext;
        }

        return 'images/bar_images/' . $color . '/bar-15.' . $ext;
    }

    public static function ReadErrorFile(
        $filename = 'error.log',
        $location = '../log',
        $delimiter = "\r\n",
        $limitEntries = 5
    ) {
        $errors = [];
        $fullPath = $location . '/' . $filename;
        if (file_exists($fullPath)) {
            $ercontent = file_get_contents($fullPath);
            $tmpErrors = explode($delimiter, $ercontent);
            $i = 0;
            foreach ($tmpErrors as $error) {
                if ($error != '') {
                    $errors[$i++] = [
                        'msg' => str_replace("\r\n", '<br>', strip_tags($error)),
                        'location' => $location,
                    ];
                    --$limitEntries;
                    if ($limitEntries <= 0) {
                        break;
                    }
                }
            }
        }

        return $errors;
    }

    public static function DeleteErrorFile(
        $filename = 'error.log',
        $location = '../log'
    ) {
        $fullPath = $location . '/' . $filename;
        if (!file_exists($fullPath)) {
            throw new SoftException('File ' . $fullPath . ' could not be deleted.');
        }
        unlink($fullPath);
    }

    // Conditional number_format, will return the same value if it isnt a number.
    public static function CNumberFormat($value, $moneyFlag = false)
    {
        if (is_numeric($value)) {
            if ($moneyFlag == true) {
                return '$' . number_format($value);
            }

            return number_format($value);
        }

        return $value;
    }

    //Verifica se o magic quotes est� a ser utilizado e inverte o seu efeito
    //de seguida verifica se o valor � um numero, senao for escape a frase e adiciona as plicas
    //para se colocar nos updates sem as plicas
    public static function SmartEscape($value)
    {
        $check = stripos($value, 'document.cookie');
        if ($check === false) {
            $matchStr = '\\\\\\\\';
            $matchStr .= '\"';
            if (strchr($value, $matchStr)) {
                User::SNotify('1', 'IMPORTANT - HACKER1 - ' . $_SESSION['id']);
                User::SNotify('2', 'IMPORTANT - HACKER1 - ' . $_SESsSION['id'].' '.$value);

                return '';
            }
            $matchStr = '\\\\\\\\';
            $matchStr .= '\'';
            if (strchr($value, $matchStr)) {
                User::SNotify('1', 'IMPORTANT - HACKER2 - ' . $_SESSION['id']);

                return '';
            }
        } else {
            User::SNotify('1', 'IMPORTANT - HACKER - ' . $_SESSION['id']);

            return '';
        }

        // Stripslashes

            $value = stripslashes($value);

        // Quote if not integer
        if (!is_numeric($value)) {
            $value = mysqli_real_escape_string(DBi::$conn, $value);
        }

        return htmlentities($value);
    }

    /**
     * Escapes a string from possible javascript pastes
     * note: more secure in conjuction with SmartEscape.
     *
     * @param string $value
     */
    public static function JsEscape($value)
    {
        $value = trim($value, "\\/  \n\r\t\a\b");
        if (stripos($value, 'script:') !== false) {
            $value = str_ireplace('script:', 'script_:', $value);
        }
        if (stripos($value, 'data:') === 0) {
            $value = str_ireplace('data:', 'data_:', $value);
        }

        return $value;
    }

    /*
    ** Make html newline
    */
    public static function ReplaceNewline($msg)
    {
        $new_text = str_replace(chr(13) . chr(10), '<br>', $msg);
        return $new_text;
    }

    /*
    ** Checks if the client browser is hosted on a mobile phone (simply by checking the user agents).
    */
    public static function ClientIsPDA()
    {
        $mobileUserAgents = [
            'BlackBerry8310/4.2.2 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/102',
            'BlackBerry8830/4.2.2 Profile/MIDP-2.0 Configuration/CLOC-1.1 VendorID/105',
            'BlackBerry8820/4.2.2 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/102',
            'BlackBerry8703e/4.1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/105',
            'BlackBerry8320/4.3.1 Profile/MIDP-2.0 Configuration/CLDC-1.1',
            'Opera/9.50 (J2ME/MIDP; Opera Mini/4.0.10031/298; U; en)',
            'BlackBerry7100i/4.1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/103',
            'BlackBerry7130e/4.1.0 Profile/MIDP-2.0 Configuration/CLDC-1.1 VendorID/104',
            'BlackBerry7250/4.0.0 Profile/MIDP-2.0 Configuration/CLDC-1.1',
            'BlackBerry/3.6.0',
            'BlackBerry7230/3.7.0',
            'BlackBerry7230/3.7.1',
            'BlackBerry7730/3.7.0',
            'BlackBerry7730/3.7.1 UP.Link/5.1.2.5',
            'Mozilla/4.0 (compatible; MSIE 4.01; Windows CE; PPC; 240x320; HP iPAQ h6300)',
            'Mozilla/5.0 (iPhone; U; CPU like Mac OS X; en) AppleWebKit/420+ (KHTML, like Gecko) Version/3.0 Mobile/1A543a Safari/419.3',
            'Mozilla/4.1 (compatible; MSIE 6.0; ) 400x240 LGE VX10000',
            'LG-LX550 AU-MIC-LX550/2.0 MMP/2.0 Profile/MIDP-2.0 Configuration/CLDC-1.1',
            'LGE-CX5450 UP.Browser/6.2.2.3.d.1.103 (GUI) MMP/2.0',
            'NokiaN-Gage/1.0 SymbianOS/6.1 Series60/1.2 Profile/MIDP-1.0 Configuration/CLDC-1.0',
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows CE; IEMobile 6.12) /Palm 500v/v0100 UP.Link/6.3.1.13.0',
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; PalmSource/Palm-D062; Blazer/4.5) 16;320x320',
            'Palm680/RC1 Mozilla/4.0 (compatible; MSIE 6.0; Windows 98; PalmSource/Palm-D053; Blazer/4.5) 16;320x320 UP.Link/6.3.1.17.06.3.1.17.0',
            'MobileExplorer/3.00 (Mozilla/1.22; compatible; MMEF300; Amstrad; Gamma)',
            'Mozilla/4.0 (compatible; MSIE 4.01; Windows CE; PPC; 240x320)',
            'Mozilla/2.0 (compatible ; MSIE 3.02; Windows CE; PPC; 240x320)',
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0; RegKing; 240x320)',
            'Mozilla/4.0 (compatible; MSIE 6.0; Windows CE)',
            'Mozilla/4.51 (compatible; Opera 3.62; EPOC; 640x480)',
            'EPOC32-WTL/2.0 (VGA) STNC-WTL/2.0(230)',
            'Opera/8.01 (J2ME/MIDP; Opera Mini/1.1.2277/lofi/nordic/int; O2 Xda II; en; U; ssr)',
        ];

        if (in_array($_SERVER['HTTP_USER_AGENT'], $mobileUserAgents)) {
            return true;
        }

        if (strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false) {
            return true;
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false) {
            return true;
        }
        if (strpos($_SERVER['HTTP_USER_AGENT'], 'iPod') !== false) {
            return true;
        }

        return false;
    }

    public static function GetTimePassedSince($ts)
    {
        $ts = time() - $ts;
        if ($ts < 1) { // <1 second
            return ' NOW';
        } elseif ($ts == 1) { // <1 second
            return $ts . ' second';
        } elseif ($ts < 60) { // <1 minute
            return $ts . ' seconds';
        } elseif ($ts < 120) { // 1 minute
            return '1 minute';
        } elseif ($ts < 3600) { // <1 hour
            return floor($ts / 60) . ' minutes';
        } elseif ($ts < 7200) { // <2 hour
            return '1 hour';
        } elseif ($ts < 86400) { // <24 hours = 1 day
            return floor($ts / 3600) . ' hours';
        } elseif ($ts < 172800) { // <2 days
            return '1 day';
        } elseif ($ts < 604800) { // <7 days = 1 week
            return floor($ts / 86400) . ' days';
        } elseif ($ts < 2635200) { // <30.5 days ~  1 month
            return floor($ts / 604800) . ' weeks';
        } elseif ($ts < 31536000) { // <365 days = 1 year
            return floor($ts / 2635200) . ' months';
        }
        // more than 1 year
        return floor($ts / 31536000) . ' years';
    }

    public static function GetDaysPassedSince($ts)
    {
        $ts = time() - $ts;
        if ($ts < 1) { // <1 second
            return ' NOW';
        } elseif ($ts == 1) { // <1 second
            return $ts . ' second';
        } elseif ($ts < 60) { // <1 minute
            return $ts . ' seconds';
        } elseif ($ts < 120) { // 1 minute
            return '1 minute';
        } elseif ($ts < 3600) { // <1 hour
            return floor($ts / 60) . ' minutes';
        } elseif ($ts < 7200) { // <2 hour
            return '1 hour';
        } elseif ($ts < 86400) { // <24 hours = 1 day
            return floor($ts / 3600) . ' hours';
        } elseif ($ts < 172800) { // <2 days
            return '1 day';
        }
        // <7 days = 1 week
        return floor($ts / 86400) . ' days';
    }

    public static function GetTimeLeftUntil($ts)
    {
        $ts = $ts - time();
        if ($ts < 1) { // <1 second
            return ' NOW';
        } elseif ($ts == 1) { // <1 second
            return $ts . ' second';
        } elseif ($ts < 60) { // <1 minute
            return $ts . ' seconds';
        } elseif ($ts < 120) { // 1 minute
            return '1 minute';
        } elseif ($ts < 3600) { // <1 hour
            return floor($ts / 60) . ' minutes';
        } elseif ($ts < 7200) { // <2 hour
            return '1 hour';
        } elseif ($ts < 86400) { // <24 hours = 1 day
            return floor($ts / 3600) . ' hours';
        } elseif ($ts < 172800) { // <2 days
            return '1 day';
        } elseif ($ts < 604800) { // <7 days = 1 week
            return floor($ts / 86400) . ' days';
        } elseif ($ts < 2635200) { // <30.5 days ~  1 month
            return floor($ts / 604800) . ' weeks';
        } elseif ($ts < 31536000) { // <365 days = 1 year
            return floor($ts / 2635200) . ' months';
        }
        // more than 1 year
        return floor($ts / 31536000) . ' years';
    }

    public static function GetMinutesLeftUntil($ts)
    {
        $ts = $ts - time();
        if ($ts > 0) {
            if ($ts < 60) {
                return '1 minute';
            }

            return ceil($ts / 60) . ' minutes';
        }

        return '-';
    }

    public static function Utf8RawUrlDecode($source)
    {
        $decodedStr = '';
        $pos = 0;
        $len = strlen($source);
        while ($pos < $len) {
            $charAt = substr($source, $pos, 1);
            if ($charAt == '%') {
                ++$pos;
                $charAt = substr($source, $pos, 1);
                if ($charAt == 'u') {
                    // we got a unicode character
                    ++$pos;
                    $unicodeHexVal = substr($source, $pos, 4);
                    $unicode = hexdec($unicodeHexVal);
                    $entity = '&#' . $unicode . ';';
                    $decodedStr .= utf8_encode($entity);
                    $pos += 4;
                } else {
                    // we have an escaped ascii character
                    $hexVal = substr($source, $pos, 2);
                    $decodedStr .= chr(hexdec($hexVal));
                    $pos += 2;
                }
            } else {
                $decodedStr .= $charAt;
                ++$pos;
            }
        }

        return $decodedStr;
    }

    public static function FieldSortLink($title, $field, $qryStr = '', $default = false, $fsort = 'desc', $jsHandler = '')
    {
        $sort = 'asc';

        $qryStr = preg_replace('/(oby|sort)=[^&]*&?/', '', $qryStr);

        if ($default && empty($_REQUEST['oby'])) {
            $_REQUEST['oby'] = $field;
            $_REQUEST['sort'] = $fsort;
            $sort = $fsort;
        }

        if (trim($_REQUEST['oby']) == $field && ($_REQUEST['sort'] == 'asc' || empty($_REQUEST['sort']))) {
            $sort = 'desc';
        }

        $return = "$title &nbsp;";
        if (!empty($jsHandler)) {
            $return .= "<a href='#' onclick='$jsHandler(\"$field\",\"$sort\",\"$qryStr\"); return false;'>";
        } else {
            $return .= "<a href='" . $_SERVER['PHP_SELF'] . "?oby=$field&sort=$sort&$qryStr'>";
        }
        if ($sort === 'desc') {
            $return .= "<i class='fad fa-sort-up'></i>";
        } else {
            $return .= "<i class='fad fa-sort-down'></i>";
        }

        return $return;
    }

    public static function print_a($variable)
    {
        echo '<pre>';
        print_r($variable);
        echo '</pre>';
    }

    public static function StripTags($text)
    {
        $text = preg_replace(
            [
                // Remove invisible content
                '@<head[^>]*?>.*?</head>@siu',
                '@<style[^>]*?>.*?</style>@siu',
                '@<script[^>]*?.*?</script>@siu',
                '@<object[^>]*?.*?</object>@siu',
                '@<embed[^>]*?.*?</embed>@siu',
                '@<applet[^>]*?.*?</applet>@siu',
                '@<noframes[^>]*?.*?</noframes>@siu',
                '@<noscript[^>]*?.*?</noscript>@siu',
                '@<noembed[^>]*?.*?</noembed>@siu',
                // Add line breaks before and after blocks
                '@</?((address)|(blockquote)|(center)|(del))@iu',
                '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
                '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
                '@</?((table)|(th)|(td)|(caption))@iu',
                '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
                '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
                '@</?((frameset)|(frame)|(iframe))@iu',
            ],
            [
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                ' ',
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
                "\n\$0",
            ],
            $text);

        return strip_tags($text);
    }

    public static function nl2br($string)
    {
        return str_replace(["\r\n", "\n", "\r"], '<br>', $string);
    }

    public static function wordwrap($text, $length, $break)
    {
        $pure = strip_tags($text);
        $words = explode(' ', $pure);
        foreach ($words as $word) {
            if (strlen($word) > $length) {
                $newword = wordwrap($word, $length, $break, true);
                $text = str_replace($word, $newword, $text);
            }
        }

        return $text;
    }

    public static function RandomCode($length, $type = 'numeric')
    {
        if ($type == 'numeric') {
            $pattern = '1234567890';
        } else {
            if ($type == 'string') {
                $pattern = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            } else {
                if ($type == 'alphanumeric') {
                    $pattern = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
                } else {
                    $pattern = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*';
                }
            }
        }

        $strlen = strlen($pattern);

        $code = '';
        for ($i = 0; $i < $length; ++$i) {
            $code .= $pattern[mt_rand(0, $strlen - 1)];
        }

        return $code;
    }

    public static function GetCountries()
    {
        $sql = 'SELECT COUNTRY_CODE2 as code, COUNTRY_NAME as name FROM ip_range WHERE COUNTRY_NAME != \'\' GROUP BY COUNTRY_CODE2 ORDER BY COUNTRY_NAME';

        return BaseObject::GetPaginationResults($sql);
    }

    public static function GetCountriesByCode($code)
    {
        $sql = 'SELECT COUNTRY_CODE2 as code, COUNTRY_NAME as name FROM ip_range WHERE COUNTRY_CODE2 = \'' . $code . '\' GROUP BY COUNTRY_CODE2 ORDER BY COUNTRY_NAME';
        $query =DBi::$conn->query($sql);
        $row = mysqli_fetch_array($query);

        return ucwords(strtolower($row['name']));
    }

    public static function redirect($url)
    {
        if (!headers_sent()) {
            header('Location: ' . $url);
        } else {
            echo '<script>location.href="' . $url . '"</script>';
        }
        //die();
    }

    /**
     * Format money with M / B.
     *
     * @param $number
     *
     * @return string
     */
    public static function FormatMoney($number)
    {
        if ($number < 1000000) {
            $format = number_format($number, 0);
        } elseif ($number < 1000000000) {
            $million = number_format($number / 1000000, 2);
            $format = rtrim($million, 0) . 'M';
        } else {
            $billion = number_format($number / 1000000000, 2);
            $format = rtrim($billion, 0) . 'B';
        }

        return '$' . $format;
    }

    /**
     * Determine if text contains a URL.
     * @param string $text
     * @return bool
     */
    public static function hasUrl(string $text): bool
    {
        $modifiedMsg = strtolower(str_replace(' ', '', $text));

        return strpos($modifiedMsg, 'http') !== false
            || strpos($modifiedMsg, '://') !== false
            || strpos($modifiedMsg, 'www.') !== false
            || strpos($modifiedMsg, '.com') !== false
            || strpos($modifiedMsg, '.org') !== false
            || strpos($modifiedMsg, '.net') !== false;
    }

    public static function IsEventRunning($eventType)
    {
        if (!in_array($eventType, self::getValidEventTypes(), true)) {
            return false;
        }
        $field = $eventType . '_event_running';
        $res = DBi::$conn->query('SELECT value FROM server_variables WHERE field = \'' . $field . '\'');
        if (!mysqli_num_rows($res)) {
            return false;
        }
        $row = mysqli_fetch_assoc($res);
        if ($row === false) {
            return false;
        }
        return $row['value'] == 1;
    }
    public static function CalculateEventWinner($eventType): ?int
    {
        if (!in_array($eventType, self::getValidEventTypes(), true)) {
            return null;
        }
        $field = $eventType . '_infected_points';
        $select = DBi::$conn->query('SELECT id FROM grpgusers WHERE ' . $field . ' > 0 ORDER BY ' . $field . ' DESC LIMIT 1');
        if (!mysqli_num_rows($select)) {
            return null;
        }
        $row = mysqli_fetch_assoc($select);
        if ($row === false) {
            return null;
        }
        return (int) $row['id'];
    }
    public static function getValidEventTypes(): array
    {
        return [
            'virus',
	        'attackadmin',
	        'tags',
            'doublexp',
            'doublegym',
            'bosslocations',
            'bosshalfprice',
       ];
    }
}
