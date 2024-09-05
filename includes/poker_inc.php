<?php

include "gen_inc.php";
$addons->get_hooks(array(), array(
    'page'     => 'includes/poker_inc.php',
    'location'  => 'page_start'
));

$best_cards = array();
$final_cards = array();

function ops_minify_html($input)
{
    if (trim($input) === "") {
        return $input;
    }

    // Minify the input HTML tags
    $input = preg_replace_callback(
        '#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s',
        create_function(
            '$matches',
            'return "<" . $matches[1] . preg_replace(
                "#([^\s=]+)(\=([\'\"]?)(.*?)\3)?(\s+|$)#s",
                " $1$2",
                isset($matches[2]) ? $matches[2] : ""
            ) . (isset($matches[3]) ? $matches[3] : "") . ">";'
        ),
        str_replace("\r", "", $input)
    );

    // Minify style tags
    if (strpos($input, '</style>') !== false) {
        $input = preg_replace_callback(
            '#<style(.*?)>(.*?)</style>#is',
            create_function(
                '$matches',
                'return "<style" . $matches[1] . ">" . ops_minify_css($matches[2]) . "</style>";'
            ),
            $input
        );
    }

    // Minify script tags
    if (strpos($input, '</script>') !== false) {
        $input = preg_replace_callback(
            '#<script(.*?)>(.*?)</script>#is',
            create_function(
                '$matches',
                'return "<script" . $matches[1] . ">" . ops_minify_js($matches[2]) . "</script>";'
            ),
            $input
        );
    }

    // Minify the HTML content by removing unnecessary whitespace and comments
    return preg_replace(
        array(
            '#<(img|input)(>| .*?>)#s',
            '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
            '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s',
            '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s',
            '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s',
            '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s',
            '#<(img|input)(>| .*?>)<\/\1>#s',
            '#(&nbsp;)&nbsp;(?![<\s])#',
            '#(?<=\>)(&nbsp;)(?=\<)#',
            '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
        ),
        array(
            '<$1$2</$1>',
            '$1$2$3',
            '$1$2$3',
            '$1$2$3$4$5',
            '$1$2$3$4$5$6$7',
            '$1$2$3',
            '<$1$2',
            '$1 ',
            '$1',
            ""
        ),
        $input
    );
}



function ops_minify_css($input)
{
    if (trim($input) === "") {
        return $input;
    }

    return preg_replace(
        array(
            // Remove comment(s)
            '#("(?:[^"\\\\]++|\\\\.)*+"|\'(?:[^\'\\\\]++|\\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
            // Remove unused white-space(s)
            '#("(?:[^"\\\\]++|\\\\.)*+"|\'(?:[^\'\\\\]++|\\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\\]++|\\\\.)*+"|\'(?:[^\'\\\\]++|\\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
            // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
            '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
            // Replace `:0 0 0 0` with `:0`
            '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
            // Replace `background-position:0` with `background-position:0 0`
            '#(background-position):0(?=[;\}])#si',
            // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
            '#(?<=[\s:,\-])0+\.(\d+)#s',
            // Minify string value
            '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
            '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
            // Minify HEX color code
            '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
            // Replace `(border|outline):none` with `(border|outline):0`
            '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
            // Remove empty selector(s)
            '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
        ),
        array(
            '$1',
            '$1$2$3$4$5$6$7',
            '$1',
            ':0',
            '$1:0 0',
            '.$1',
            '$1$3',
            '$1$2$4$5',
            '$1$2$3',
            '$1:0',
            '$1$2'
        ),
        $input
    );
}

// JavaScript Minifier
function ops_minify_js($input)
{
    if (trim($input) === "") {
        return $input;
    }

    return preg_replace(
        array(
            // Remove comment(s)
            '#\s*("(?:[^"\\\\]++|\\\\.)*+"|\'(?:[^\'\\\\]++|\\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
            // Remove white-space(s) outside the string and regex
            '#("(?:[^"\\\\]++|\\\\.)*+"|\'(?:[^\'\\\\]++|\\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
            // Remove the last semicolon
            '#;+\}#',
            // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
            '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
            // From `foo['bar']` to `foo.bar`
            '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
        ),
        array(
            '$1',
            '$1$2',
            '}',
            '$1$3',
            '$1.$3'
        ),
        $input
    );
}function OPS_pagination($array = array())
{
    extract($array);
    $prev     = $page - 1;
    $next     = $page + 1;
    $lastPage = ceil($total / $limit);
    $lpm1     = $lastPage - 1;

    $pagination = '';

    if ($lastPage > 1) {
        $pagination .= "
    <nav align=\"right\">
      <ul class=\"pagination\">
        ";
        // previous button
        if ($page > 1) {
            $pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetPage&page=1\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
        } else {
            $pagination .= "<li class=\"page-item\"><a class=\"page-link\"><span aria-hidden=\"true\">&laquo;</span></a></li>";
        }

        // pages
        if ($lastPage < 7 + ($adjacent * 2)) {
            // not enough pages to bother breaking it up
            for ($counter = 1; $counter <= $lastPage; $counter++) {
                if ($counter == $page) {
                    $pagination .= "<li class=\"page-item active\"><a class=\"page-link\"><span>$counter</span></a></li>";
                } else {
                    $pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetPage&page=$counter\">$counter</a></li>";
                }
            }
        } elseif ($lastPage > 5 + ($adjacent * 2)) {
            // enough pages to hide some
            // close to beginning; only hide later pages
            if ($page < 1 + ($adjacent * 2)) {
                for ($counter = 1; $counter < 4 + ($adjacent * 2); $counter++) {
                    if ($counter == $page) {
                        $pagination .= "<li class=\"page-item active\"><a class=\"page-link\"><span>$counter</span></a></li>";
                    } else {
                        $pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetPage&page=$counter\">$counter</a></li>";
                    }
                }

                $pagination .= "<li class=\"page-item\"><span>...</span></li>";
                $pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetPage&page=$lpm1\">$lpm1</a></li>";
                $pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetPage&page=$lastPage\">$lastPage</a></li>";
            } elseif ($lastPage - ($adjacent * 2) > $page && $page > ($adjacent * 2)) {
                // in middle; hide some front and some back
                $pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetPage&page=1\">1</a></li>";
                $pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetPage&page=2\">2</a></li>";
                $pagination .= "<li class=\"page-item\"><span>...</span></li>";

                for ($counter = $page - $adjacent; $counter <= $page + $adjacent; $counter++) {
                    if ($counter == $page) {
                        $pagination .= "<li class=\"page-item active\"><a class=\"page-link\"><span>$counter</span></a></li>";
                    } else {
                        $pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetPage&page=$counter\">$counter</a></li>";
                    }
                }

                $pagination .= "<li class=\"page-item\"><span>...</span></li>";
                $pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetPage&page=$lpm1\">$lpm1</a></li>";
                $pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetPage&page=$lastPage\">$lastPage</a></li>";
            } else {
                // close to end; only hide early pages
                $pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetPage&page=1\">1</a></li>";
                $pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetPage&page=2\">2</a></li>";
                $pagination .= "<li class=\"page-item\"><span>...</span></li>";

                for ($counter = $lastPage - (2 + ($adjacent * 2)); $counter <= $lastPage; $counter++) {
                    if ($counter == $page) {
                        $pagination .= "<li class=\"page-item active\"><a class=\"page-link\"><span>$counter</span></a></li>";
                    } else {
                        $pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetPage&page=$counter\">$counter</a></li>";
                    }
                }
            }
        }

        // next button
        if ($page < $counter - 1) {
            $pagination .= "<li class=\"page-item\"><a class=\"page-link\" href=\"$targetPage&page=$lastPage\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
        } else {
            $pagination .= "<li class=\"page-item\"><a class=\"page-link\"><span aria-hidden=\"true\">&raquo;</span></a></li>";
        }

        $pagination .= "
      </ul>
    </nav>
        ";
    }

    return $pagination;
}

function OPS_mail($to, $subject, $body)
{
    // send email
    $mailfrom = $_SERVER['HTTP_HOST'];
    $mailfrom = str_replace('www.', '', $mailfrom);
    $mailf    = explode('/', $mailfrom);
    $from     = 'support@' . $mailf[0];

    if (defined('SMTP_ON') && SMTP_ON === 'yes') {
        $mail = new PHPMailer(true);
        try {
            switch (SMTP_ENCRYPT) {
                case 'TLS':
                    $encryption = 'tls';
                    break;

                case 'SSL':
                    $encryption = 'ssl';
                    break;

                default:
                    $encryption = false;
                    break;
            }

            // Server settings
            $mail->SMTPDebug = 0; // Set to 0 to disable debugging output
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = (SMTP_AUTH === 'yes') ? true : false;
            $mail->Username   = SMTP_USER;
            $mail->Password   = SMTP_PASS;
            $mail->SMTPSecure = $encryption;
            $mail->Port       = SMTP_PORT;

            if (!$encryption) {
                $mail->SMTPAutoTLS = false;
            }

            // From
            $mail->setFrom($from, $mailf[0]);
            // Recipients
            $mail->addAddress($to);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log($mail->ErrorInfo);
            return false;
        }
    } else {
        $headers = 'From: ' . $from . "\r\n" .
            'Reply-To: ' . $from . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        if (mail($to, $subject, $body, $headers)) {
            return true;
        } else {
            return false;
        }
    }

    return false;
}

function OPS_sitelog($player, $log)
{
    if (empty($player) || empty($log)) {
        return false;
    }

    global $pdo;
    $time = date('Y-m-d H:i:s');
    // Prepared statement to prevent SQL injection, recommended over direct query
    $stmt = $pdo->prepare("INSERT INTO " . DB_SITELOG . " (player, log, dt) VALUES (:player, :log, :time)");
    $stmt->execute(array(':player' => $player, ':log' => $log, ':time' => $time));
    return true;
}

function poker_seat_circle_html($i, $isJs = false)
{
    global $pdo, $opsTheme, $addons, $gameID;
    
    $opsTheme->addVariable('seat_number', $i);
    $html = $opsTheme->viewPart('poker-player-circle');

    if ($isJs) {
        $html = str_replace("'", "\'", $html);
    }

    $html = $addons->get_hooks(
        array(
            'index'   => $i,
            'content' => $html,
            'is_js'   => $isJs
        ),
        array(
            'page'      => 'includes/poker_inc.php',
            'location'  => 'each_seat_circle',
        )
    );

    return $html;
}

// This function checks and makes sure the email address that is being added to the database is valid in format. 
function check_email_address($email)
{
    // Check that there's one @ symbol, and that the lengths are right
    if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/i", $email)) {
        // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
        return false;
    }

    // Split it into sections to make life easier
    $email_array = explode("@", $email);
    $local_array = explode(".", $email_array[0]);

    for ($i = 0; $i < count($local_array); $i++) {
        if (!preg_match("/^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
            return false;
        }
    }

    if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) {
        // Check if domain is IP. If not, it should be a valid domain name
        $domain_array = explode(".", $email_array[1]);

        if (count($domain_array) < 2) {
            return false; // Not enough parts to domain
        }

        for ($i = 0; $i < count($domain_array); $i++) {
            if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
                return false;
            }
        }
    }

    return true;
}

function get_user_ip_addr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Handle multiple IP addresses, return the first one
    $ips = explode(',', $ip);
    return trim($ips[0]);
}

function file_get_contents_su($url, $download = false)
{
    $post_data = array(
        'ip'      => get_user_ip_addr(),
        'domain'  => preg_replace('/[^A-Za-z0-9-.]/i', '', $_SERVER['SERVER_NAME']),
        'license' => LICENSEKEY,
        'version' => SCRIPTVERSIO,
    );

    if ($download) {
        $post_data['download'] = true;
    }

    $context = array(
        'http' => array(
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($post_data)
        ),
        'ssl' => array(
            'verify_peer'      => false,
            'verify_peer_name' => false,
        ),
    );

    // Suppress errors and handle empty responses
    $fgc = @file_get_contents($url, false, stream_context_create($context));
    if (empty($fgc)) {
        return '{}';
    }

    return $fgc;
}
function file_get_contents_ssl($url, $post_data = false)
{
    $context = array(
        'ssl' => array(
            'verify_peer'      => false,
            'verify_peer_name' => false,
        ),
    );

    // Check if post_data is an array and prepare the context for POST request
    if (is_array($post_data)) {
        $context['http'] = array(
            'method'  => 'POST',
            'header'  => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($post_data)
        );
    }

    // Suppress errors with @ and handle empty responses
    $fgc = @file_get_contents($url, false, stream_context_create($context));
    if (empty($fgc)) {
        return '{}';
    }

    return $fgc;
}

function transfer_from($val)
{
    if (!is_numeric($val)) {
        return 0;
    }

    if ($val == 0) {
        return $val;
    }

    global $smallbetfunc;
    if ($smallbetfunc == 1) {
        $val = $val / 1000;
    } elseif ($smallbetfunc == 2) {
        $val = $val / 100;
    } elseif ($smallbetfunc == 3) {
        $val = $val / 10;
    }

    return $val;
}

function transfer_to($val)
{
    if (!is_numeric($val)) {
        return 0;
    }

    global $smallbetfunc;
    if ($smallbetfunc == 1) {
        $val = $val * 1000;
    } elseif ($smallbetfunc == 2) {
        $val = $val * 100;
    } elseif ($smallbetfunc == 3) {
        $val = $val * 10;
    }

    return $val;
}

function moneynumber($val)
{
    global $smallbetfunc;

    if (!is_numeric($val)) {
        return 0;
    }

    if ($smallbetfunc == 1) {
        $val = ($val / 1000);
    } elseif ($smallbetfunc == 2) {
        $val = ($val / 100);
    } elseif ($smallbetfunc == 3) {
        $val = ($val / 10);
    }

    return $val;
}

function money($val, $gamestyle = 't')
{
    global $smallbetfunc;

    $currSymbol = "<nobr><img style=\"display:inline!important\" src=\"" . (($gamestyle == 't') ? 'assets/images/play/currency-s.png' : 'assets/images/play/currency-p.png') . "\"> ";
    $currSuffix = '</nobr>';

    if (is_numeric($val)) {
        if ($smallbetfunc == 1) {
            $val = ($val / 1000);
        } elseif ($smallbetfunc == 2) {
            $val = ($val / 100);
        } elseif ($smallbetfunc == 3) {
            $val = ($val / 10);
        }
        if ($val > 1000000000) {
            $money = $currSymbol . number_format(($val / 1000000000), 0, MONEY_THOUSA, MONEY_DECIMA) . ' B' . $currSuffix;
        } elseif ($val > 100000000) {
            $money = $currSymbol . number_format(($val / 1000000), 0, MONEY_THOUSA, MONEY_DECIMA) . ' M' . $currSuffix;
        } elseif ($val > 1000000) {
            $money = $currSymbol . number_format(($val / 1000000), 1, MONEY_THOUSA, MONEY_DECIMA) . ' M' . $currSuffix;
        } elseif ($smallbetfunc == 1) {
            $money = $currSymbol . number_format($val, 2, MONEY_THOUSA, MONEY_DECIMA) . $currSuffix;
        } else {
            if ($pos = strpos($val, '.')) {
                $len = strlen($val);
                $dec = $len - ($pos + 1);
                $money = $currSymbol . number_format($val, $dec, MONEY_THOUSA, MONEY_DECIMA) . $currSuffix;
            } else {
                $money = $currSymbol . number_format($val, 0, MONEY_THOUSA, MONEY_DECIMA) . $currSuffix;
            }
        }
    } elseif ($val == 'FOLD') {
        $money = $val;
    } else {
        $money = $currSymbol . '0' . $currSuffix;
    }

    return $money;
}

function money_small($val, $gamestyle = 't')
{
    if (!is_numeric($val)) {
        return false;
    }
    global $smallbetfunc;

    $currSymbol = "<nobr><img style=\"display:inline!important\" src=\"" . (($gamestyle == 't') ? 'assets/images/play/currency-s.png' : 'assets/images/play/currency-p.png') . "\"> ";
    $currSuffix = '</nobr>';
    $val = str_replace('F', '', $val);
    if ($smallbetfunc == 1) {
        $val = ($val / 1000);
    } elseif ($smallbetfunc == 2) {
        $val = ($val / 100);
    } elseif ($smallbetfunc == 3) {
        $val = ($val / 10);
    }
    if ($val > 999999999) {
        $money = $currSymbol . number_format(($val / 1000000000), 1, MONEY_DECIMA, MONEY_THOUSA) . 'B' . $currSuffix;
    } elseif ($val > 99999999) {
        $money = $currSymbol . number_format(($val / 1000000), 0, MONEY_DECIMA, MONEY_THOUSA) . 'M' . $currSuffix;
    } elseif ($val > 999999) {
        $money = $currSymbol . number_format(($val / 1000000), 1, MONEY_DECIMA, MONEY_THOUSA) . 'M' . $currSuffix;
    } elseif ($val > 999) {
        if (($val % 1000) == 0) {
            $money = $currSymbol . number_format(($val / 1000), 0, MONEY_DECIMA, MONEY_THOUSA) . 'K' . $currSuffix;
        } else {
            $money = $currSymbol . number_format(($val / 1000), 1, MONEY_DECIMA, MONEY_THOUSA) . 'K' . $currSuffix;
        }
    } elseif ($smallbetfunc == 1) {
        $money = $currSymbol . number_format($val, 2, MONEY_DECIMA, MONEY_THOUSA) . $currSuffix;
    } else {
        if ($pos = strpos($val, '.')) {
            $len = strlen($val);
            $dec = $len - ($pos + 1);
            $money = $currSymbol . number_format($val, $dec, MONEY_DECIMA, MONEY_THOUSA) . $currSuffix;
        } else {
            $money = $currSymbol . number_format($val, 0, MONEY_DECIMA, MONEY_THOUSA) . $currSuffix;
        }
    }

    return $money;
}
function get_ava($usr)
{
    global $pdo;
    // Use a prepared statement to prevent SQL injection
    $usrq = $pdo->prepare("SELECT avatar FROM grpgusers WHERE username = :username");
    $usrq->execute(array(':username' => $usr));
    $usrr = $usrq->fetch(PDO::FETCH_ASSOC);
    return isset($usrr['avatar']) ? $usrr['avatar'] : '';
}

function display_ava($usr)
{
    global $pdo;
    $usrq = $pdo->prepare("SELECT avatar FROM grpgusers WHERE username = :username");
    $usrq->execute(array(':username' => $usr));
    $usrr = $usrq->fetch(PDO::FETCH_ASSOC);
    $avatar = isset($usrr['avatar']) ? $usrr['avatar'] : '';
    return '<img src="' . htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') . '" border="0">';
}

function display_ava_profile($usr)
{
    global $pdo;
    $time = time();
    $usrq = $pdo->prepare("SELECT avatar FROM grpgusers WHERE username = :username");
    $usrq->execute(array(':username' => $usr));
    $usrr = $usrq->fetch(PDO::FETCH_ASSOC);
    $avatar = isset($usrr['avatar']) ? $usrr['avatar'] : '';
    return '<img src="' . htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') . '?x=' . $time . '" border="0">';
}

function display_ava_profiles($usr)
{
    global $pdo;
    $usrq = $pdo->prepare("SELECT avatar FROM grpgusers WHERE username = :username");
    $usrq->execute(array(':username' => $usr));
    $usrr = $usrq->fetch(PDO::FETCH_ASSOC);
    return isset($usrr['avatar']) ? $usrr['avatar'] : '';
}

function sys_msg($msg, $gameID)
{
    global $pdo;
    $chtq = $pdo->prepare("SELECT * FROM " . DB_LIVECHAT . " WHERE gameID = :gameID");
    $chtq->execute(array(':gameID' => $gameID));
    $chtr = $chtq->fetch(PDO::FETCH_ASSOC);

    $time = time() + 2;
    $c2 = addslashes($chtr['c2']);
    $c3 = addslashes($chtr['c3']);
    $c4 = addslashes($chtr['c4']);
    $c5 = addslashes($chtr['c5']);
    $msg = str_ireplace(array("'", "\\'"), '&apos;', $msg);
    $msg = '<p>' . $msg . '</p>';

    if ($chtq->rowCount() > 0) {
        $result = $pdo->prepare("UPDATE " . DB_LIVECHAT . " SET updatescreen = :time, c1 = :c2, c2 = :c3, c3 = :c4, c4 = :c5, c5 = :msg WHERE gameID = :gameID");
        $result->execute(array(':time' => $time, ':c2' => $c2, ':c3' => $c3, ':c4' => $c4, ':c5' => $c5, ':msg' => $msg, ':gameID' => $gameID));
    } else {
        $result = $pdo->prepare("INSERT INTO " . DB_LIVECHAT . " SET updatescreen = :time, c1 = :c2, c2 = :c3, c3 = :c4, c4 = :c5, c5 = :msg, gameID = :gameID");
        $result->execute(array(':time' => $time, ':c2' => $c2, ':c3' => $c3, ':c4' => $c4, ':c5' => $c5, ':msg' => $msg, ':gameID' => $gameID));
    }

    return true;
}

function poker_log($playername, $msg, $gameID)
{
    if (strlen($msg) < 1) {
        return false;
    }

    global $pdo, $opsTheme;
    $time  = time() + 2;

    $plyrF = array(
        'ID'     => '',
        'avatar' => ''
    );

    $chtq = $pdo->prepare("SELECT * FROM " . DB_LIVECHAT . " WHERE gameID = :gameID");
    $chtq->execute(array(':gameID' => $gameID));
    $chtr = $chtq->fetch(PDO::FETCH_ASSOC);

    $c2  = addslashes($chtr['c2']);
    $c3  = addslashes($chtr['c3']);
    $c4  = addslashes($chtr['c4']);
    $c5  = addslashes($chtr['c5']);
    $msg = addslashes($msg);

    if (strlen($playername) > 0) {
        $plyrQ = $pdo->prepare("SELECT ID, avatar FROM grpgusers WHERE username = :username");
        $plyrQ->execute(array(':username' => $playername));
        if ($plyrQ->rowCount() == 1) {
            $plyrF = $plyrQ->fetch(PDO::FETCH_ASSOC);
        }
    }

    $msg = '<user>
    <id>' . htmlspecialchars($plyrF['ID'], ENT_QUOTES, 'UTF-8') . '</id>
    <name>' . htmlspecialchars($playername, ENT_QUOTES, 'UTF-8') . '</name>
    <avatar>' . htmlspecialchars($plyrF['avatar'], ENT_QUOTES, 'UTF-8') . '</avatar>
</user>
<message>' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</message>';

    if ($chtq->rowCount() > 0) {
        $result = $pdo->prepare("UPDATE " . DB_LIVECHAT . " SET updatescreen = :time, c1 = :c2, c2 = :c3, c3 = :c4, c4 = :c5, c5  = :msg WHERE gameID = :gameID");
        $result->execute(array(':time' => $time, ':c2' => $c2, ':c3' => $c3, ':c4' => $c4, ':c5' => $c5, ':msg' => $msg, ':gameID' => $gameID));
    } else {
        $result = $pdo->prepare("INSERT INTO " . DB_LIVECHAT . " SET updatescreen = :time, c1 = :c2, c2 = :c3, c3 = :c4, c4 = :c5, c5 = :msg, gameID = :gameID");
        $result->execute(array(':time' => $time, ':c2' => $c2, ':c3' => $c3, ':c4' => $c4, ':c5' => $c5, ':msg' => $msg, ':gameID' => $gameID));
    }

    return true;
}
function get_ip($usr)
{
    global $pdo;
    // Use a prepared statement to prevent SQL injection
    $ipq = $pdo->prepare("SELECT ipaddress FROM " . DB_PLAYERS . " WHERE username = :username");
    $ipq->execute(array(':username' => $usr));
    $ipr = $ipq->fetch(PDO::FETCH_ASSOC);
    return isset($ipr['ipaddress']) ? $ipr['ipaddress'] : '';
}

function getplayerid($plyrname)
{
    global $tpr;
    for ($i = 1; $i < 11; $i++) {
        if ($plyrname == $tpr['p' . $i . 'name']) {
            return $i;
        }
    }
    return null;
}

function get_num_players()
{
    $x = 0;
    for ($i = 1; $i < 11; $i++) {
        if (get_name($i) != '' && (get_pot($i) > 0 || get_bet($i) > 0) && get_pot($i) != 'BUSTED') {
            $x++;
        }
    }
    return $x;
}

function get_all_players()
{
    $x = 0;
    for ($i = 1; $i < 11; $i++) {
        if (get_name($i) != '') {
            $x++;
        }
    }
    return $x;
}

function last_player()
{
    for ($i = 1; $i < 11; $i++) {
        if ((get_name($i) != '') && ((get_pot($i) > 0) || (get_bet($i) > 0))) {
            return $i;
        }
    }
    return null;
}

function in_game($i)
{
    return (get_name($i) != '') && ((get_bet($i) > 0) || (get_pot($i) > 0)) && (get_bet($i) != 'FOLD');
}

function in_gametot($i)
{
    return (get_name($i) != '');
}

function get_num_allin()
{
    $x = 0;
    for ($i = 1; $i < 11; $i++) {
        if ((get_name($i) != '') && (get_pot($i) == 0) && (get_bet($i) > 0) && (get_bet($i) != 'FOLD')) {
            $x++;
        }
    }
    return $x;
}

function get_num_left()
{
    $x = 0;
    for ($i = 1; $i < 11; $i++) {
        if ((get_name($i) != '') && ((get_bet($i) > 0) || (get_pot($i) > 0)) && (get_bet($i) != 'FOLD')) {
            $x++;
        }
    }
    return $x;
}

function check_bets()
{
    global $tablebet;
    for ($i = 1; $i < 11; $i++) {
        if ((get_name($i) != '') && (get_pot($i) > 0) && (get_bet($i) != 'FOLD')) {
            if (get_bet($i) < $tablebet) {
                return false;
            }
        }
    }
    return true;
}

function roundpot($pot)
{
    return ($pot - floor($pot) > 0.5) ? floor($pot) + 1 : floor($pot);
}

function find_winners($game_style)
{
    global $best_cards, $final_cards, $addons;
    $multiwin = array();
    $pts = 0;

    for ($i = 1; $i < 11; $i++) {
        $winpts = 0;
        if (in_game($i)) {
            if ($game_style == GAME_TEXAS) {
                $winpts = evaluate_texas_hand($i);
            } else {
                $winpts = $addons->get_hooks(
                    array(
                        'player' => $i,
                        'content' => $winpts
                    ),
                    array(
                        'page' => 'includes/poker_inc.php',
                        'location' => 'omaha_logic'
                    )
                );
            }

            if ($winpts > $pts) {
                $multiwin = array_fill(0, 5, ''); // Reset and fill with empty values
                $multiwin[0] = $i;
                $pts = $winpts;
                $final_cards = $best_cards;
            } elseif ($winpts == $pts && $winpts > 0) {
                $multiwin[] = $i;
            }
        }
    }

    // Ensure the return array has 6 elements
    return array_pad($multiwin, 6, '');
}

function decrypt_card($encrypted)
{
    $cards = array(
        'AD', '2D', '3D', '4D', '5D', '6D', '7D', '8D', '9D', '10D', 'JD', 'QD', 'KD',
        'AC', '2C', '3C', '4C', '5C', '6C', '7C', '8C', '9C', '10C', 'JC', 'QC', 'KC',
        'AH', '2H', '3H', '4H', '5H', '6H', '7H', '8H', '9H', '10H', 'JH', 'QH', 'KH',
        'AS', '2S', '3S', '4S', '5S', '6S', '7S', '8S', '9S', '10S', 'JS', 'QS', 'KS'
    );
    $stack = explode(':', $encrypted);

    foreach ($cards as $i => $card) {
        if (isset($stack[0], $stack[1]) && (md5($stack[1] . 'pokerpro' . $card) == $stack[0]) && (count($stack) == 2)) {
            return $card;
        }
    }
    return '';
}

function encrypt_card($plain)
{
    $plain = 'pokerpro' . $plain;
    $salt = substr(md5(mt_rand()), 0, 2);
    $card = md5($salt . $plain) . ':' . $salt;
    return $card;
}

function last_bet()
{
    global $tpr, $pdo;
    $lb = explode('|', $tpr['lastbet']);
    $lb1 = isset($lb[0]) ? $lb[0] : '';

    if (is_numeric($lb1) && $lb1 > 0 && empty($tpr["p{$lb1}name"])) {
        $nxt = nextplayer($lb1);

        if (!empty($nxt)) {
            $lastbet = "{$nxt}|" . $tpr["p{$nxt}bet"];
            $pdo->query("UPDATE " . DB_POKER . " SET lastbet = '{$lastbet}' WHERE gameID = " . $tpr['gameID']);
        }
    }

    return $lb1;
}
function ots($num)
{
    // Adjusts the player position according to game rules
    if ($num == 11) $num = 1;
    if ($num == 0) $num = 10;
    return $num;
}

function nextplayer($player)
{
    global $tpr;
    $i = $player;
    $z = 0;
    while ($z < 10) {
        $i++;
        $test = ots($i);
        // Check if the next player is active and not folded
        if (($tpr['p' . $test . 'name'] != '') && ($tpr['p' . $test . 'pot'] != 'BUSTED') && 
            (($tpr['p' . $test . 'pot'] > 0) || ($tpr['p' . $test . 'bet'] > 0)) && 
            ($tpr['p' . $test . 'pot'] != '') && (get_bet($test) != 'FOLD')) {
            return $test;
        }
        $i = $test;
        $z++;
    }
    return '';
}

function nextdealer($player)
{
    global $tpr;
    $i = $player;
    $z = 0;
    while ($z < 10) {
        $i++;
        $test = ots($i);
        // Check if the next player has a pot greater than 0
        if (($tpr['p' . $test . 'name'] != '') && ($tpr['p' . $test . 'pot'] > 0)) {
            return $test;
        }
        $i = $test;
        $z++;
    }
    return '';
}

function insert_cards($select, $pos, $gameID)
{
    global $pdo;
    // Use a prepared statement to prevent SQL injection
    $stmt = $pdo->prepare("UPDATE " . DB_POKER . " SET " . $pos . " = :card WHERE gameID = :gameID");
    $stmt->execute(array(':card' => encrypt_card($select), ':gameID' => $gameID));
    return true;
}

function deal($numplayers, $gameID, $game_style)
{
    $cards = array(
        'AD', '2D', '3D', '4D', '5D', '6D', '7D', '8D', '9D', '10D', 'JD', 'QD', 'KD',
        'AC', '2C', '3C', '4C', '5C', '6C', '7C', '8C', '9C', '10C', 'JC', 'QC', 'KC',
        'AH', '2H', '3H', '4H', '5H', '6H', '7H', '8H', '9H', '10H', 'JH', 'QH', 'KH',
        'AS', '2S', '3S', '4S', '5S', '6S', '7S', '8S', '9S', '10S', 'JS', 'QS', 'KS'
    );

    // Determine card positions and number of cards based on game style
    if ($game_style == GAME_TEXAS) {
        $cardpos = array(
            'card1', 'card2', 'card3', 'card4', 'card5', 
            'p1card1', 'p1card2', 'p2card1', 'p2card2', 
            'p3card1', 'p3card2', 'p4card1', 'p4card2', 
            'p5card1', 'p5card2', 'p6card1', 'p6card2', 
            'p7card1', 'p7card2', 'p8card1', 'p8card2', 
            'p9card1', 'p9card2', 'p10card1', 'p10card2'
        );
        $numcards = ($numplayers * 2) + 5;
    } else {
        $cardpos = array(
            'card1', 'card2', 'card3', 'card4', 'card5', 
            'p1card1', 'p1card2', 'p1card3', 'p1card4', 
            'p2card1', 'p2card2', 'p2card3', 'p2card4', 
            'p3card1', 'p3card2', 'p3card3', 'p3card4', 
            'p4card1', 'p4card2', 'p4card3', 'p4card4', 
            'p5card1', 'p5card2', 'p5card3', 'p5card4', 
            'p6card1', 'p6card2', 'p6card3', 'p6card4', 
            'p7card1', 'p7card2', 'p7card3', 'p7card4', 
            'p8card1', 'p8card2', 'p8card3', 'p8card4', 
            'p9card1', 'p9card2', 'p9card3', 'p9card4', 
            'p10card1', 'p10card2', 'p10card3', 'p10card4'
        );
        $numcards = ($numplayers * 4) + 5;
    }

    $pick = array();
    $i = 0;
    while ($i < $numcards) {
        $select = $cards[mt_rand(0, 51)];
        if (!in_array($select, $pick)) {
            $pick[] = $select;
            insert_cards($select, $cardpos[$i], $gameID);
            $i++;
        }
    }
}
function get_bet_math($pnum)
{
    global $tpr;
    $pbet = isset($tpr['p' . $pnum . 'bet']) ? $tpr['p' . $pnum . 'bet'] : '';
    return str_replace('F', '', $pbet);
}

function fetch_bet_math($pnum)
{
    global $tpr;
    $bet = isset($tpr['p' . $pnum . 'bet']) ? $tpr['p' . $pnum . 'bet'] : '';
    return (substr($bet, 0, 1) == 'F' || $bet == '') ? 0 : $bet;
}

function get_bet($pnum)
{
    global $tpr;
    $bet = isset($tpr['p' . $pnum . 'bet']) ? $tpr['p' . $pnum . 'bet'] : '';
    if (substr($bet, 0, 1) == 'F') {
        $bet = (substr($bet, 1, 1) != '') ? 'FOLD' : 0;
    }
    return ($bet == '') ? 0 : $bet;
}

function get_pot($pnum)
{
    global $tpr;
    $pot = isset($tpr['p' . $pnum . 'pot']) ? $tpr['p' . $pnum . 'pot'] : '';
    return is_numeric($pot) ? $pot : 0;
}

function get_name($pnum)
{
    global $tpr;
    return isset($tpr['p' . $pnum . 'name']) ? $tpr['p' . $pnum . 'name'] : '';
}

function distpot($game_style)
{
    global $tpr, $pdo, $gameID, $addons;
    $pots = array_fill(1, 10, 0);
    foreach (range(1, 10) as $i) {
        $pots[$i] = get_pot($i);
    }

    $wins = array();
    $winners = find_winners($game_style);

    $totalWinnerBets = array_sum(array_map('get_bet_math', $winners));

    foreach ($winners as $winner) {
        if ($winner == '') continue;

        $winnerName = get_name($winner);
        $winnerBet = get_bet($winner);
        $cut = $winnerBet / $totalWinnerBets;

        $pots[$winner] += $winnerBet;

        foreach (range(1, 10) as $i) {
            if ($winner == $i || in_array($i, $winners)) continue;

            $loserBet = get_bet_math($i) * $cut;

            if ($loserBet > 0) {
                if ($winnerBet >= $loserBet) {
                    $pots[$winner] += $loserBet;
                } else {
                    $pots[$winner] += $winnerBet;
                    $pots[$i] += ($loserBet - $winnerBet);
                }
            }

            $wins[$winner] = array('name' => $winnerName, 'pot' => $pots[$winner]);
        }
    }

    // Apply hooks for further modification of pots and winners
    $pots = $addons->get_hooks(
        array('content' => $pots, 'winners' => $wins),
        array('page' => 'includes/poker_inc.php', 'location' => 'distpots')
    );

    // Update the database with the calculated pot values
    $updateQuery = "UPDATE " . DB_POKER . " SET ";
    $updateFields = array();
    foreach (range(1, 10) as $i) {
        $updateFields[] = "p{$i}pot = '" . roundpot($pots[$i]) . "'";
    }
    $updateQuery .= implode(', ', $updateFields) . " WHERE gameID = " . $gameID;
    $pdo->query($updateQuery);
}

function evaluate_texas_hand($player)
{
    global $cardr, $tablecards;

    $points = 0;
    $hand = array(
        $tablecards[0], $tablecards[1], $tablecards[2],
        $tablecards[3], $tablecards[4],
        decrypt_card($cardr['p' . $player . 'card1']),
        decrypt_card($cardr['p' . $player . 'card2'])
    );

    $flush = array();
    $values = array();
    $sortvalues = array();
    $hcs = array();

    $orig = array('J', 'Q', 'K', 'A');
    $change = array(11, 12, 13, 14);

    foreach ($hand as $i => $card) {
        if ($card == '') continue;

        if (strlen($card) == 2) {
            $flush[$i] = $card[1];
            $values[$i] = str_replace($orig, $change, $card[0]);
        } else {
            $flush[$i] = $card[2];
            $values[$i] = str_replace($orig, $change, substr($card, 0, 2));
        }
        $sortvalues[$i] = $values[$i];
    }

    sort($sortvalues);

    $ispair = array_count_values($values);
    $results = array_count_values($ispair);
    $res = '';
    if (!empty($results['2'])) $res = $results['2'] > 1 ? '2pair' : '1pair';
    if (!empty($results['3'])) $res = '3s';
    if (!empty($results['4'])) $res = '4s';
    if (!empty($results['3']) && !empty($results['2']) || !empty($results['3']) > 1) $res = 'FH';

    $multipair = array_keys(array_filter($ispair, function($v) {
        return $v == 2;
    }));
    $threepair = array_keys(array_filter($ispair, function($v) {
        return $v == 3;
    }));
    
    
    // High card calculations
    $hcs = array_slice(array_reverse(array_diff($sortvalues, $multipair)), 0, 5);
    $high1 = $hcs[0];
    $high2 = $high1 + ($hcs[1] / 10);
    $high3 = $high2 + ($hcs[2] / 100);
    $high5 = $high3 + ($hcs[3] / 1000) + ($hcs[4] / 10000);

    // Points based on poker hand ranking
    if (in_array($res, ['1pair', '2pair', 'FH'])) {
        if ($res == '1pair') $points = (isset($multipair[0]) ? $multipair[0] : 0) * 10 + $high3;
        if ($res == '2pair') $points = (isset($multipair[1]) ? $multipair[1] : 0) * 100 + (isset($multipair[0]) ? $multipair[0] : 0) * 10 + $high1;
        if ($res == 'FH') $points = (isset($threepair[1]) ? $threepair[1] : 0) * 1000000 + (isset($multipair[1]) ? $multipair[1] : 0) * 100000;
    } elseif ($res == '3s') {
        $points = max(array_keys($ispair, 3)) * 1000 + $high2;
    } elseif ($res == '4s') {
        $points = max(array_keys($ispair, 4)) * 10000000 + $high1;
    }
    

    // Flush and straight calculations
    $flushsuit = array_search(max(array_count_values($flush)), array_count_values($flush));
    if ($flushsuit) {
        $flusharray = array_intersect_key($values, array_flip(array_keys($flush, $flushsuit)));
        sort($flusharray);
        $points = 250000 + $flusharray[count($flusharray) - 1] * 1000 + $flusharray[count($flusharray) - 2] * 100
                + $flusharray[count($flusharray) - 3] * 10 + $flusharray[count($flusharray) - 4] 
                + $flusharray[count($flusharray) - 5] / 10;

        if (count(array_unique(array_map(function ($v) use ($flusharray) {
            return $flusharray[$v + 1] - $flusharray[$v];
        }, array_keys($flusharray)))) === 1) {
            $points = max($flusharray) * 100000000;
            if (max($flusharray) == 14) $points *= 10;
        }
    } else {
        $straight = array_reduce(range(0, count($sortvalues) - 2), function ($carry, $i) use ($sortvalues) {
            return $carry && ($sortvalues[$i + 1] - $sortvalues[$i] == 1);
        }, true);
        if ($straight) $points = max($sortvalues) * 10000;
    }

    return $points ?: $high5;
}
function evaluatewin($player, $game_style)
{
    global $tablecards;
    global $cardr;
    global $final_cards;

    $points = 0;

    if ($game_style == GAME_TEXAS) {
        $hand = array(
            $tablecards[0],
            $tablecards[1],
            $tablecards[2],
            $tablecards[3],
            $tablecards[4],
            decrypt_card($cardr['p' . $player . 'card1']),
            decrypt_card($cardr['p' . $player . 'card2'])
        );
    } else {
        $hand = $final_cards;
    }

    $flush = array();
    $values = array();
    $sortvalues = array();
    $orig = array('J', 'Q', 'K', 'A');
    $change = array(11, 12, 13, 14);
    $i = 0;

    while ($hand[$i] != '') {
        if (strlen($hand[$i]) == 2) {
            $flush[$i] = substr($hand[$i], 1, 1);
            $values[$i] = str_replace($orig, $change, substr($hand[$i], 0, 1));
            $sortvalues[$i] = $values[$i];
        } else {
            $flush[$i] = substr($hand[$i], 2, 1);
            $values[$i] = str_replace($orig, $change, substr($hand[$i], 0, 2));
            $sortvalues[$i] = $values[$i];
        }
        $i++;
    }

    sort($sortvalues);
    $ispair = array_count_values($values);
    $results = array_count_values($ispair);
    $res = '';
    $outputvalues = array(
        '', '', '2s', '3s', '4s', '5s', '6s', '7s', '8s', '9s', '10s', 'Jacks', 'Queens', 'Kings', 'Aces'
    );
    $outputvalues2 = array(
        '', '', 2, 3, 4, 5, 6, 7, 8, 9, 10, 'Jack', 'Queen', 'King', 'Ace'
    );

    if (isset($results['2']) && $results['2'] == 1) {
        $res = '1pair';
    }
    if (isset($results['2']) && $results['2'] > 1) {
        $res = '2pair';
    }
    if (isset($results['3']) && $results['3'] > 0) {
        $res = '3s';
    }
    if (isset($results['4']) && $results['4'] > 0) {
        $res = '4s';
    }
    if ((isset($results['3']) && $results['3'] > 0 && isset($results['2']) && $results['2'] > 0) || (isset($results['3']) && $results['3'] > 1)) {
        $res = 'FH';
    }

    if ($res == '1pair' || $res == '2pair' || $res == 'FH') {
        $multipair = array();
        $threepair = array();
        $i = 2;
        $z = 0;
        $y = 0;

        while ($i < 15) {
            if (isset($ispair[$i]) && $ispair[$i] == 2) {
                $multipair[$z] = $i;
                $z++;
            }
            if (isset($ispair[$i]) && $ispair[$i] == 3) {
                $threepair[$y] = $i;
                $y++;
            }
            $i++;
        }

        if ($res == '1pair') {
            $Xres = $outputvalues[$multipair[0]];
            $res = ' ' . WIN_PAIR . ' ' . $Xres;
        }

        if ($res == '2pair') {
            sort($multipair);
            $pr1 = $multipair[count($multipair) - 1];
            $pr2 = $multipair[count($multipair) - 2];
            $Xres = $outputvalues[$pr1];
            $Xres2 = $outputvalues[$pr2];
            $res = ' ' . WIN_2PAIR . ' ' . $Xres . ' and ' . $Xres2;
        }

        if ($res == 'FH') {
            $res = ' ' . WIN_FULLHOUSE;
        }
    }

    if ($res == '3s') {
        foreach ($ispair as $key => $value) {
            if ($value == 3) {
                $res = ' ' . WIN_SETOF3 . ' ' . $outputvalues[$key];
                break;
            }
        }
    }

    if ($res == '4s') {
        foreach ($ispair as $key => $value) {
            if ($value == 4) {
                $res = ' ' . WIN_SETOF4 . ' ' . $outputvalues[$key];
                break;
            }
        }
    }

    $flushsuit = '';
    $isflush = array_count_values($flush);
    foreach (array('D', 'C', 'H', 'S') as $suit) {
        if (isset($isflush[$suit]) && $isflush[$suit] > 4) {
            $flushsuit = $suit;
            break;
        }
    }

    if ($flushsuit != '') {
        $flusharray = array();
        foreach ($flush as $index => $suit) {
            if ($suit == $flushsuit) {
                $flusharray[] = $values[$index];
            }
        }

        sort($flusharray);
        $z = count($flusharray) - 1;
        $res = ' ' . $outputvalues2[$flusharray[$z]] . ' ' . WIN_FLUSH;

        $x = 0;
        for ($i = 0; $i < count($flusharray) - 1; $i++) {
            if ($flusharray[$i] == ($flusharray[$i + 1] - 1)) {
                $x++;
                $h = $flusharray[$i] + 1;
            }
        }

        if ($x > 3) {
            $res = ' ' . $outputvalues2[$flusharray[$z]] . ' ' . WIN_STRAIGHT_FLUSH;
        }
        if ($x > 3 && $h == 14) {
            $res = ' ' . WIN_ROYALFLUSH;
        }
    }

    if ($flushsuit == '') {
        $count = 0;
        $lows = false;

        if ($sortvalues[6] == 14 && $sortvalues[0] == 2) {
            $count = 1;
            $lows = true;
        }

        foreach ($sortvalues as $index => $value) {
            if ($index < count($sortvalues) - 1 && ($sortvalues[$index] == ($sortvalues[$index + 1] - 1))) {
                $count++;
                if ($count > 3) {
                    $res = ' ' . $outputvalues2[$value + 1] . ' ' . WIN_STRAIGHT;
                    if ($lows && $value + 1 == 5) {
                        $res = ' low straight';
                    }
                }
            } else {
                $count = 0;
            }
        }
    }

    if ($res == '') {
        $res = ' ' . $outputvalues2[$sortvalues[6]] . ' ' . WIN_HIGHCARD;
    }

    return $res;
}

function find_rand($min = null, $max = null)
{
    static $seeded;
    if (!isset($seeded)) {
        mt_srand((double)microtime() * 1000000);
        $seeded = true;
    }

    if (isset($min) && isset($max)) {
        if ($min >= $max) {
            return $min;
        } else {
            return mt_rand($min, $max);
        }
    } else {
        return mt_rand();
    }
}

function validate_password($plain, $encrypted)
{
    if ($plain != '' && $encrypted != '') {
        $stack = explode(':', $encrypted);
        if (count($stack) != 2) return false;
        if ((md5($stack[1] . 'pwd' . $plain) == $stack[0]) || (md5($stack[1] . $plain) == $stack[0])) {
            return true;
        }
    }

    return false;
}

function encrypt_password($plain)
{
    $password = '';
    for ($i = 0; $i < 10; $i++) {
        $password .= find_rand();
    }

    $salt = substr(md5($password), 0, 2);
    $password = md5($salt . 'pwd' . $plain) . ':' . $salt;
    return $password;
}

function randomcode($length, $type = 'mixed')
{
    if ($type != 'mixed' && $type != 'chars' && $type != 'digits') return false;
    $rand_value = '';
    while (strlen($rand_value) < $length) {
        if ($type == 'digits') {
            $char = find_rand(0, 9);
        } else {
            $char = chr(find_rand(0, 255));
        }

        if ($type == 'mixed' && preg_match('/^[a-z0-9]$/i', $char)) {
            $rand_value .= $char;
        } elseif ($type == 'chars' && preg_match('/^[a-z]$/i', $char)) {
            $rand_value .= $char;
        } elseif ($type == 'digits' && preg_match('/^[0-9]$/', $char)) {
            $rand_value .= $char;
        }
    }

    return $rand_value;
}
?>
