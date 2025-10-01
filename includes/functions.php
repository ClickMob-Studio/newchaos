<?php

require_once __DIR__ . '/../dbcon.php';
require_once __DIR__ . '/../database/pdo_class.php';
require_once __DIR__ . '/cache.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

function howlongtil($futureTimestamp)
{
    $secondsLeft = $futureTimestamp - time();

    if ($secondsLeft <= 0) {
        return "now";
    }

    $units = [
        'year' => 365 * 24 * 60 * 60,
        'month' => 30 * 24 * 60 * 60,
        'week' => 7 * 24 * 60 * 60,
        'day' => 24 * 60 * 60,
        'hour' => 60 * 60,
        'minute' => 60,
        'second' => 1
    ];

    $parts = [];

    foreach ($units as $name => $duration) {
        if ($secondsLeft >= $duration) {
            $value = floor($secondsLeft / $duration);
            $secondsLeft %= $duration;
            $parts[] = "$value $name" . ($value > 1 ? "s" : "");
        }
    }

    return implode(", ", array_slice($parts, 0, 4)); // Show up to 4 time units
}

function ordinal($number)
{
    $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
    if ((($number % 100) >= 11) && (($number % 100) <= 13))
        return $number . 'th';
    else
        return $number . $ends[$number % 10];
}
function get_users_online()
{
    global $db;

    $db->query("SELECT COUNT(id) c FROM grpgusers WHERE lastactive > unix_timestamp() - 3600");
    $db->execute();
    $r = $db->fetch_row(true);
    $rtn = $r['c'];

    return number_format($rtn);
}

function display_online_staff()
{
    global $db;

    $db->query("SELECT id FROM grpgusers WHERE lastactive > unix_timestamp() - 3600 AND admin + gm + fm + cm + eo + st > 0");
    $db->execute();
    $r = $db->fetch_row();
    foreach ($r as $row)
        $rtn[] = formatName($row['id']);

    return implode('<br />', $rtn);
}
function security($string, $type = 'num', $nots = "")
{
    global $user_class;
    if ($type == "string") {
        preg_match("/([^{$nots}]*)/", $string, $matched);
        if ($string != $matched[0]) {
            header("Location: login.php");
            exit;
        }
    } elseif ($type == "num") {
        $string = str_replace(',', '', $string);
        preg_match('/[0-9]*/', $string, $matched);
        if ($string != $matched[0]) {
            header("Location: index.php");
            exit;
        }
    }
    return $string;
}
function gradientTag($gangId)
{
    global $db;
    $db->query("SELECT tag, tColor1, tColor2, tColor3 FROM gangs WHERE id = ?");
    $db->execute(array(
        $gangId
    ));
    $r = $db->fetch_row(true);
    $color0 = $r['tColor1'];
    $color1 = $r['tColor2'];
    $color2 = $r['tColor3'];
    $tag = str_split($r['tag']);
    $math = count($tag);
    $x = $t = 0;
    $name = "";
    while ($math > 0) {
        $name .= "<span style='color:#{$r['tColor' . ++$t]}'>{$tag[$x++]}</span>";
        $math--;
    }
    return $name;
}
function url_exists($url)
{
    if (@file_get_contents($url, 0, NULL, 0, 1))
        return 1;
    else
        return 0;
}
function Get_ID($username)
{
    global $db;
    $db->query("SELECT id FROM grpgusers WHERE username = ?");
    $db->execute(array(
        $username
    ));
    return $db->fetch_single();
}

function timeLeft($ts)
{
    if ($ts > 86400) {
        $ts = date('j \D\a\y H:i:s', $ts);
    } else {
        $ts = date('H:i:s', $ts);
    }
    return $ts;
}

function mrefresh($url, $time = "1")
{
    echo '<meta http-equiv="refresh" content="' . $time . ';url=' . $url . '">';
}
function car_popup($text, $id)
{
    return "<a href='javascript:;' onclick=\"javascript:window.open( 'cardesc.php?id=$id', '60', 'left = 20, top = 20, width = 400, height = 350, toolbar = 0, resizable = 0, scrollbars=1' );\">" . $text . "</a>";
}
function secondsToTime($seconds)
{
    $dtF = new DateTime('@0');
    $dtF = new DateTime('@0');
    $dtT = new DateTime("@$seconds");
    $interval = $dtF->diff($dtT);

    $parts = [];

    if ($interval->y > 0) {
        $parts[] = $interval->y . ' y';
    }

    if ($interval->y > 0 || $interval->m > 0) {
        $parts[] = $interval->m . ' mo';
    }

    // Convert leftover days into weeks and days
    $weeks = floor($interval->d / 7);
    $days = $interval->d % 7;

    if ($interval->y > 0 || $interval->m > 0 || $weeks > 0) {
        $parts[] = $weeks . ' w';
    }

    if ($interval->y > 0 || $interval->m > 0 || $weeks > 0 || $days > 0) {
        $parts[] = $days . ' d';
    }

    if ($interval->y > 0 || $interval->m > 0 || $weeks > 0 || $days > 0 || $interval->h > 0) {
        $parts[] = $interval->h . ' h';
    }

    if ($interval->y > 0 || $interval->m > 0 || $weeks > 0 || $days > 0 || $interval->h > 0 || $interval->i > 0) {
        $parts[] = str_pad($interval->i, 2, '0', STR_PAD_LEFT) . ' m';
    }

    $parts[] = str_pad($interval->s, 2, '0', STR_PAD_LEFT) . ' s';

    return implode(', ', $parts);
}
function daysToTime($seconds)
{
    $dtF = new DateTime("@0");
    $dtT = new DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%d days, %h hours, %i minutes and %s seconds');
}
function plane_popup($text, $id)
{
    return "<a href='javascript:;' onclick=\"javascript:window.open( 'planedesc.php?id=" . $id . "', '60', 'left = 20, top = 20, width = 400, height = 350, toolbar = 0, resizable = 0, scrollbars=1' );\">" . $text . "</a>";
}
function item_popup($text, $id, $color = '#fff')
{
    return "<a class='item-popup' style='color:" . $color . "' href='javascript:;' onclick=\"javascript:window.open( 'description.php?id=" . $id . "', '60', 'left = 20, top = 20, width = 400, height = 550, toolbar = 0, resizable = 0, scrollbars=0, location=0, menubar=0'  );\">" . $text . "</a>";
}
function gifts_popup($text, $id)
{
    return "<a href='#' onclick=\"javascript:window.open( 'gdesc.php?id=" . $id . "', '60', 'left = 20, top = 20, width = 400, height = 475, toolbar = 0, resizable = 0, scrollbars=1' );\">" . $text . "</a>";
}
function image_popup($image, $id)
{
    return "<a href='javascript:;' onclick=\"javascript:window.open( 'description.php?id=" . $id . "', '60', 'left = 20, top = 20, width = 400, height = 440, toolbar = 0, resizable = 0, scrollbars=0, location=0, menubar=0'  );\"><img src='" . $image . "' width='100' height='100' style='border: 1px solid #000000'></a>";
}
function drug_popup($text, $id)
{
    return "<a href='javascript:;' onclick=\"javascript:window.open( 'drugdesc.php?id=" . $id . "', '60', 'left = 20, top = 20, width = 400, height = 440, toolbar = 0, resizable = 0, scrollbars=0, location=0, menubar=0'  );\">" . $text . "</a>";
}
function prettynum($num, $dollar = "0")
{
    $out = strrev((string) preg_replace('/(\d{3})(?=\d)(?!\d*\.)/', '$1,', strrev($num)));
    if ($dollar && is_numeric($num))
        $out = "$" . $out;
    return $out;
}
function number_format_short($n, $precision = 2)
{
    if ($n < 900) {
        $n_format = number_format($n, $precision);
        $suffix = '';
    } else if ($n < 900000) {
        $n_format = number_format($n / 1000, $precision);
        $suffix = 'K';
    } else if ($n < 900000000) {
        $n_format = number_format($n / 1000000, $precision);
        $suffix = 'M';
    } else if ($n < 900000000000) {
        $n_format = number_format($n / 1000000000, $precision);
        $suffix = 'B';
    } else {
        $n_format = number_format($n / 1000000000000, $precision);
        $suffix = 'T';
    }
    if ($precision > 0) {
        $dotzero = '.' . str_repeat('0', $precision);
        $n_format = str_replace($dotzero, '', $n_format);
    }
    return $n_format . $suffix;
}
function StaffLog($id, $text, $extra = "0")
{
    global $db;
    $db->query("INSERT INTO staff_logs (player, text, timestamp, extra) VALUES (?, ?, unix_timestamp(), ?)");
    $db->execute(array(
        $id,
        $text,
        $extra
    ));
}
function Relationship_Req($player, $type, $from)
{
    global $db;
    $db->query("SELECT * FROM rel_requests WHERE player = ? AND `from` = ?");
    $db->execute(array(
        $player,
        $from
    ));
    if (!$db->num_rows()) {
        $db->query("INSERT INTO rel_requests (player, `from`, `status`, timestamp) VALUES (?, ?, ?, unix_timestamp())");
        $db->execute(array(
            $player,
            $from,
            $type
        ));
        Send_Event($player, "[-_USERID_-] requested a relationship with you. <a href='chapel.php'>[Click here to view]</a>", $from);
    }
}
function addBrowser($userid, $name)
{
    global $db;
    $db->query("
        INSERT INTO forum_browsers (userid, name)
        VALUES (?, ?)
        ON DUPLICATE KEY UPDATE name = VALUES(name)
    ");
    $db->execute([$userid, $name]);
}
function getRank($userid, $stat)
{
    global $db;

    $db->query("SELECT count(g.id) FROM grpgusers g WHERE (SELECT count(*) FROM bans b WHERE b.id = g.id AND type IN ('perm','freeze')) = 0 AND g.admin = 0 AND g.$stat > (SELECT xf.$stat FROM grpgusers xf WHERE xf.id = ?)");
    $db->execute(array(
        $userid
    ));
    $rtn = $db->fetch_single() + 1;

    return $rtn;
}
function Check_Item($itemid, $userid)
{
    global $db;
    $db->query("SELECT quantity FROM inventory WHERE userid = ? AND itemid = ?");
    $db->execute(array(
        $userid,
        $itemid
    ));
    $rtn = $db->fetch_single();
    return ($rtn > 0) ? $rtn : 0;
}

function Get_Item($itemid)
{
    global $db, $cache;

    if ($cache->exists('item_' . $itemid)) {
        $item = $cache->get('item_' . $itemid);
        return json_decode($item, true);
    }

    $db->query("SELECT * FROM items WHERE id = ?");
    $db->execute([$itemid]);
    $item = $db->fetch_row(true);
    $cache->setEx('item_' . $itemid, 3600, json_encode($item));

    return $item;
}

function Check_Loan($itemid, $userid)
{
    global $db;
    $db->query("SELECT quantity FROM gang_loans WHERE idto = ? AND item = ?");
    $db->execute(array(
        $userid,
        $itemid
    ));
    $rtn = $db->fetch_single();
    return ($rtn > 0) ? $rtn : 0;
}
function Check_Car($itemid, $userid)
{
    global $db;
    $db->query("SELECT COUNT(*) FROM cars WHERE userid = ? AND carid = ?");
    $db->execute(array(
        $userid,
        $itemid
    ));
    $rtn = $db->fetch_single();
    return ($rtn > 0) ? $rtn : 0;
}

function CheckGangWar($gang)
{
    global $db;
    $db->query("SELECT COUNT(*) FROM gangwars WHERE (gang1 = ? OR gang2 = ?) AND accepted = 1");
    $db->execute(array(
        $gang,
        $gang
    ));
    $rtn = $db->fetch_single();
    return ($rtn > 0) ? $rtn : 0;
}
function CheckCourse($id)
{
    global $db;
    $db->query("SELECT COUNT(*) FROM uni WHERE playerid = ?");
    $db->execute(array(
        $id
    ));
    $rtn = $db->fetch_single();
    return ($rtn > 0) ? $rtn : 0;
}

function AddToArmory($itemid, $gangid, $quantity = 1)
{
    global $db;
    $db->query("
        INSERT INTO gangarmory (itemid, gangid, quantity)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
    ");
    $db->execute(array(
        $itemid,
        $gangid,
        $quantity
    ));
}

function TakeFromArmory($itemid, $gangid, $quantity = 1)
{
    global $db;
    $db->query("
        UPDATE gangarmory
        SET quantity = GREATEST(quantity - ?, 0)
        WHERE gangid = ? AND itemid = ? AND quantity >= ?
    ");
    $db->execute(array(
        $quantity,
        $gangid,
        $itemid,
        $quantity
    ));

    // If the quantity is reduced to 0, delete the row
    if ($db->affected_rows() > 0) {
        $db->query("DELETE FROM gangarmory WHERE gangid = ? AND itemid = ? AND quantity = 0");
        $db->execute(array($gangid, $itemid));
    }
}

function Give_Item($itemid, $userid, $quantity = "1")
{
    global $db;
    $db->query("
        INSERT INTO inventory (itemid, userid, quantity)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
    ");
    $db->execute([
        $itemid,
        $userid,
        $quantity
    ]);
}

function Loan_Item($gang, $itemid, $userid, $quantity = 1)
{
    global $db;
    $db->query("
        INSERT INTO gang_loans (idto, gang, item, quantity)
        VALUES (?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
    ");
    $db->execute(array(
        $userid,
        $gang,
        $itemid,
        $quantity
    ));
}

function Take_Item($itemid, $userid, $quantity = 1)
{
    global $db;
    $db->query("
        UPDATE inventory 
        SET quantity = GREATEST(quantity - ?, 0)
        WHERE userid = ? AND itemid = ?
        AND quantity >= ?
    ");

    $db->execute(array(
        $quantity,
        $userid,
        $itemid,
        $quantity
    ));

    // If quantity is 0, delete the row
    if ($db->affected_rows() > 0) {
        // Check if quantity reached zero and delete row
        $db->query("DELETE FROM inventory WHERE userid = ? AND itemid = ? AND quantity = 0");
        $db->execute(array($userid, $itemid));
    }
}

function Item_Name($itemId)
{
    global $db, $cache;

    $item = $cache->get('item_' . $itemId);
    if (!$item) {
        $db->query("SELECT * FROM items WHERE id = ?");
        $db->execute([$itemId]);
        $item = $db->fetch_row(true);
        $cache->setEx("item_" . $itemId, 3600, json_encode($item));
        return $item['itemname'];
    } else {
        $item = json_decode($item, true);
        return $item['itemname'];
    }
}

function Item_Image($itemId)
{
    global $db, $cache;

    $item = $cache->get('item_' . $itemId);
    if (!$item) {
        $db->query("SELECT * FROM items WHERE id = ?");
        $db->execute([$itemId]);
        $item = $db->fetch_row(true);
        $cache->setEx("item_" . $itemId, 3600, json_encode($item));
        return $item['image'];
    } else {
        $item = json_decode($item, true);
        return $item['image'];
    }
}

function Item_Details($itemId)
{
    global $db, $cache;

    $item = $cache->get('item_' . $itemId);
    if (!$item) {
        $db->query("SELECT * FROM items WHERE id = ?");
        $db->execute([$itemId]);
        $item = $db->fetch_row(true);
        $cache->setEx("item_" . $itemId, 3600, json_encode($item));
        return $item;
    } else {
        $item = json_decode($item, true);
        return $item;
    }
}

function Take_Loan($itemid, $userid, $quantity = 1)
{
    global $db;
    $db->query("SELECT * FROM gang_loans WHERE idto = ? AND id = ?");
    $db->execute(array(
        $userid,
        $itemid
    ));
    if ($db->num_rows()) {
        $row = $db->fetch_row(true);
        $row['quantity'] -= $quantity;
        if ($row['quantity'] <= 0) {
            $db->query("DELETE FROM gang_loans WHERE idto = ? AND id = ?");
            $db->execute(array(
                $userid,
                $itemid
            ));
        } else {
            $db->query("UPDATE gang_loans SET quantity = ? WHERE idto = ? AND id = ?");
            $db->execute(array(
                $row['quantity'],
                $userid,
                $itemid
            ));
        }
    }
}
function Take_Car($itemid, $userid, $quantity = "1")
{
    global $db;
    $db->query("SELECT COUNT(*) FROM cars WHERE userid = ? AND carid = ?");
    $db->execute(array(
        $userid,
        $itemid
    ));
    $amnt = $db->fetch_single();
    if ($amnt) {
        $db->query("DELETE FROM cars WHERE userid = ? AND carid = ?");
        $db->execute(array(
            $userid,
            $itemid
        ));
    }
}
function Take_Plane($itemid, $userid, $quantity = "1")
{
    global $db;
    $db->query("SELECT COUNT(*) FROM hangar WHERE userid = ? AND planeid = ?");
    $db->execute(array(
        $userid,
        $itemid
    ));
    $amnt = $db->fetch_single();
    if ($amnt) {
        $db->query("DELETE FROM hangar WHERE userid = ? AND planeid = ?");
        $db->execute(array(
            $userid,
            $itemid
        ));
    }
}
function Message($text)
{
    $rtn = '';
    $rtn .= '<div class="dcPanel p-3" style="text-align:center">';
    $rtn .= $text;
    $rtn .= '</div>';
    return $rtn;
}
function Send_Event($id, $text, $extra = "0")
{
    global $db;
    if (empty($id))
        return;
    $db->query("INSERT INTO events (`to`, timesent, `text`, `extra`) VALUES (?, unix_timestamp(), ?, ?)");
    $db->execute(array(
        $id,
        $text,
        $extra
    ));
}

function Send_Event2($id, $text, $extra = "0")
{
    global $db;
    if (empty($id))
        return;
    $db->query("INSERT INTO eventsmain (`to`, timesent, `text`, `extra`) VALUES (?, unix_timestamp(), ?, ?)");
    $db->execute(array(
        $id,
        $text,
        $extra
    ));
}
function Send_PM($text, $to, $subject)
{
    global $db;
    $db->query("INSERT INTO pms (`from`, `to`, timesent, msgtext, subject) VALUES (1, ?, unix_timestamp(), ?, ?)");
    $db->execute(array(
        $to,
        $text,
        $subject
    ));
}
function Gang_Event($id, $text, $extra = "0")
{
    global $db;
    if (empty($id))
        return;
    $db->query("INSERT INTO gangevents (gang, timesent, `text`, `extra`) VALUES (?, unix_timestamp(), ?, ?)");
    $db->execute(array(
        $id,
        $text,
        $extra
    ));
}
function Vault_Event($gangid, $text, $extra = "0")
{
    global $db;
    $db->query("INSERT INTO vlog (gangid, timestamp, `text`, userid) VALUES (?, unix_timestamp(), ?, ?)");
    $db->execute(array(
        $gangid,
        $text,
        $extra
    ));
}
function Crime_Event($gangid, $text, $reward, $extra = "0")
{
    global $db;
    $db->query("INSERT INTO gcrimelog (gangid, timestamp, `text`, reward, userid) VALUES (?, unix_timestamp(), ?, ?, ?)");
    $db->execute(array(
        $gangid,
        $text,
        $reward,
        $extra
    ));
}
function daysago($ts)
{
    return howlongago($ts);
}
function lastactive($ts, $stop = 'none')
{
    return howlongago($ts, $stop);
}
function crimeleft($ts)
{
    return howlongtil($ts);
}

function howlongago($ts, $stop = 'none')
{
    $ts = time() - $ts;

    if ($ts < 1)
        return " NOW";
    elseif ($ts == 1)
        return $ts . "s";
    elseif ($ts < 60)
        return $ts . "s";
    elseif ($ts < 120)
        return "1m " . ($ts % 60) . "s";
    elseif ($ts < 3600)
        return floor($ts / 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 7200)
        return "1h " . floor((floor($ts / 60)) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 86400)
        return floor($ts / 3600) . "h " . floor((floor($ts / 60)) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 172800)
        return "1d " . floor((floor($ts / 3600)) % 24) . "h " . floor((floor($ts / 60)) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 604800 || $stop === 'days')
        return
            floor($ts / 86400) . "d " .
            floor((floor($ts / 3600)) % 24) . "h " .
            floor((floor($ts / 60)) % 60) . "m " .
            floor($ts % 60) . "s";
    elseif ($ts < 2628000) // ~30.5 days
        return floor($ts / 604800) . " weeks";
    elseif ($ts < 31536000)
        return floor($ts / 2628000) . " months";
    else
        return floor($ts / 31536000) . " years";
}

function howlongleft($ts)
{
    return howlongtil($ts);
}
function checkers()
{
    if (!file_exists('/usr/antiste/lic.txt'))
        die("");
}
function jailleft($ts)
{
    return howlongtil($ts);
}
function experience2($L)
{
    $end = 0;
    $a = 0;
    for ($x = 1; $x < $L; $x++)
        $a += round($x + 1500 * pow(4, ($x / 190)));
    if ($x >= 100)
        $a *= 3;

    return round($a / 4);
}

function experience($L)
{
    if ($L > 4400) {
        $L = 4400;
    }
    $a = 0;
    $end = 0;
    for ($x = 1; $x < $L; $x++)
        $a += round($x + 1500 * pow(4, ($x / 190)));
    if ($x >= 100)
        $a *= 3;
    if ($x >= 800)
        $a *= 0.5;
    if ($x >= 900)
        $a *= 0.5;
    if ($x >= 1000)
        $a *= 2;



    return round($a / 4);
}
function GangExperience($L)
{
    $a = 0;
    $end = 0;
    for ($x = 1; $x < $L; $x++)
        $a += round($x + 2000 * pow(4, ($x / 190)));
    if ($x > 349)
        $a += round($x + 2500 * pow(4, ($x / 190)));
    if ($x > 478)
        $a += round($x + 3500 * pow(6, ($x / 130)));
    if ($x > 499)
        $a += round($x + 10000 * pow(6, ($x / 120)));
    return round($a / 4);
}
function displayInfo($the_userid)
{
    $bar_class = new User($the_userid);
    if ($bar_class->id == "") {
        $the_info = "<u><i>Invalid User ID</i></u>.";
    } else {
        if ($bar_class->hppercent >= 75) {
            $colour = "#00DD00";
        } else {
            $colour = "#BB0000";
        }
        if ((time() - $bar_class->lastactive) < 900) {
            $colour1 = "#005500";
            $colour2 = "#006600";
        } else {
            $colour1 = "#550000";
            $colour2 = "#660000";
        }
        $the_info = '<table width="450px" class="userbar" cellspacing="0" cellpadding="3"><tr onclick="DoNav(\'https://chaoscity.co.uk/profiles.php?id=' . $bar_class->id . '\');"><td width="40%">' . $bar_class->formattedname . '</td><td style="border-left: 1px solid #444444;" width="15%">LVL:&nbsp;' . $bar_class->level . '</td><td style="border-left: 1px solid #444444;" width="15%">HP:&nbsp;<font color="' . $colour . '">' . $bar_class->hppercent . '%</font></td><td style="border-left: 1px solid #444444;" width="26%"><a href="bus.php">' . $bar_class->cityname . '</a></td><td align="center" style="border-left: 1px solid #444444; background-color:' . $colour1 . ';" width="4%"><div style="background-color:' . $colour2 . ';">&nbsp;&nbsp;</div></td></tr></table>';
    }
    return $the_info;
}
function displayInfo2($the_userid)
{
    $bar_class = new User($the_userid);
    if ($bar_class->id == "") {
        $the_info = "<u><i>Invalid User ID</i></u>.";
    } else {
        if ($bar_class->hppercent >= 75) {
            $colour = "#00DD00";
        } else {
            $colour = "#BB0000";
        }
        if ((time() - $bar_class->lastactive) < 900) {
            $colour1 = "#005500";
            $colour2 = "#006600";
        } else {
            $colour1 = "#550000";
            $colour2 = "#660000";
        }
        $the_info = '<table width="450px" class="userbar" cellspacing="0" cellpadding="3"><tr onclick="DoNav(\'https://chaoscity.co.uk/profiles.php?id=' . $bar_class->id . '\');"><td width="40%">' . $bar_class->formattedname . '</td><td style="border-left: 1px solid #444444;" width="15%">LVL:&nbsp;' . $bar_class->level . '</td><td style="border-left: 1px solid #444444;" width="15%">HP:&nbsp;<font color="' . $colour . '">' . $bar_class->hppercent . '%</font></td><td style="border-left: 1px solid #444444;" width="26%"><a href="bus.php">' . $bar_class->cityname . '</a></td><td align="center" style="border-left: 1px solid #444444; background-color:' . $colour1 . ';" width="4%"><div style="background-color:' . $colour2 . ';">&nbsp;&nbsp;</div></td></tr></table><table width="450px" class="userbar2" cellspacing="0" cellpadding="3"><tr><td width="20%" align="center">[<a href="attack.php?attack=' . $bar_class->id . '">attack</a>]</td><td style="border-left: 1px solid #444444;" width="20%" align="center">[<a href="mug.php?mug=' . $bar_class->id . '">mug</a>]</td><td style="border-left: 1px solid #444444;" width="20%" align="center">[<a href="spy.php?id=' . $bar_class->id . '">spy</a>]</td><td style="border-left: 1px solid #444444;" width="20%" align="center">[<a href="profiles.php?id=' . $bar_class->id . '&contact=friend">friend</a>]</td><td style="border-left: 1px solid #444444;" width="20%" align="center">[<a href="profiles.php?id=' . $bar_class->id . '&contact=enemy">enemy</a>]</td></tr></table>';
    }
    return $the_info;
}
function getBrowser()
{
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
    $bname = 'Unknown';
    $platform = 'Unknown';
    $version = "";
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif (preg_match('/Chrome/i', $u_agent)) {
        $bname = 'Google Chrome';
        $ub = "Chrome";
    } elseif (preg_match('/Safari/i', $u_agent)) {
        $bname = 'Apple Safari';
        $ub = "Safari";
    } elseif (preg_match('/Opera/i', $u_agent)) {
        $bname = 'Opera';
        $ub = "Opera";
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
        $ub = "Netscape";
    } else {
        $bname = $ub = "Unknown";
    }
    $known = array(
        'Version',
        $ub,
        'other'
    );
    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
    }
    $i = count($matches['browser']);
    if ($i != 1) {
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }
    if ($version == null || $version == "") {
        $version = "?";
    }
    return array(
        'userAgent' => $u_agent,
        'name' => $bname,
        'version' => $version,
        'platform' => $platform,
        'pattern' => $pattern
    );
}

/**
 * Cached user display name formatter with O(1) invalidation.
 */
function formatName($id, $nogang = 0)
{
    global $db, $cache;

    $id = (int) $id;
    $nogang = (int) $nogang;

    $verKey = "usr:fmt:v:$id";
    $version = $cache->get($verKey);
    if ($version === false) {
        $version = 1;
    }
    $cacheKey = "usr:fmt:$id:$nogang:v:$version";

    $cached = $cache->get($cacheKey);
    if ($cached !== false && $cached !== null) {
        return $cached;
    }

    $sql = "
    SELECT
        gu.id,
        gu.username,
        gu.gang,
        gu.admin,
        gu.rmdays,
        gu.gm,
        gu.colours,
        gu.image_name,
        gu.pdimgname,
        gu.gradient,
        gu.gndays,
        gu.prestige,
        gu.uninfo,
        g.tag,
        g.leader       AS gang_leader,
        g.formattedTag AS formattedTag,
        EXISTS (
            SELECT 1
            FROM bans b
            WHERE b.id = gu.id
            AND b.type IN ('perm','freeze')
        ) AS is_banned
    FROM grpgusers gu
    LEFT JOIN gangs g ON g.id = gu.gang
    WHERE gu.id = ?
    LIMIT 1;
";
    $db->query($sql);
    $db->execute([$id]);
    $row = $db->fetch_row(true);

    $name = "";

    if ($row && (int) $row['gang'] !== 0 && $nogang !== 1) {
        $name .= "<a href='viewgang.php?id={$row['gang']}'";
        if ((int) $id === 2 && (int) $row['gndays'] > 0) {
            $name = str_replace("<a ", "<a style='font-size:1.5em;' ", $name);
        }
        $isLeader = ((int) $row['gang_leader'] === $id);
        if ($row['formattedTag'] === "Yes") {
            $name .= $isLeader
                ? " title='Gang Leader'><font color=grey>[<b>" . gradientTag($row['gang']) . "</b>]</font></a> "
                : "><font color=grey>[" . gradientTag($row['gang']) . "]</font></a> ";
        } else {
            $name .= $isLeader
                ? " title='Gang Leader'><font color=blue>[<b>{$row['tag']}</b>]</font></a> "
                : "><font color=white>[{$row['tag']}]</font></a> ";
        }
    }

    $bdays = ((int) ($row['is_banned'] ?? 0) === 1);
    if ($bdays) {
        $title = "Banned";
        $whichfont = "#FFFFFF";
    } elseif ($row && (int) $row['admin'] === 1) {
        $title = "Admin";
        $whichfont = "#FF1111";
    } elseif ($row && (int) $row['gm'] === 1) {
        $title = "Chat Moderator";
        $whichfont = "#FFFFFF";
    } elseif ($row && (int) $row['rmdays'] >= 1) {
        $title = "VIP ({$row['rmdays']} VIP Days Left)";
        $whichfont = "#00BF03";
    } else {
        $title = "Not Respected";
        $whichfont = "#009102";
    }

    if ($bdays) {
        $name .= "<a title='$title' href='profiles.php?id=$id'>&nbsp;<font color='$whichfont'>{$row['username']}</s></font></a>";
    } elseif (!empty($row['image_name']) && (int) $row['pdimgname'] > 0) {
        $name .= ((int) $row['admin'] === 1 || (int) $row['gm'] === 1)
            ? "<a title='" . $title . " [" . $row['username'] . "]' href='profiles.php?id=$id'>"
            : "<a title='" . $title . "' href='profiles.php?id=$id'>";
        $name .= "<img src='{$row['image_name']}' style='max-width:84px; max-height:50px;' title='" . $row['username'] . "' />";
        $name .= "</a>";
    } elseif ($row && (int) $row['gndays'] > 0) {
        $gradientText = generateGradientName($id);
        if ($gradientText) {
            $name .= "<a href='profiles.php?id=$id'>{$gradientText}</a>";
        } else {
            $name .= "<a href='profiles.php?id=$id'>{$row['username']}</a>";
        }
    } elseif (!empty($row['colours']) && (int) $row['gradient'] === 2 && (int) $row['gndays'] > 0) {
        $colours = explode("~", str_replace('#', '', $row['colours']));
        $gradient = text_gradient($colours[0] ?? '', $colours[1] ?? '', 1, $row['username']);
        $name .= ((int) $row['admin'] === 1 || (int) $row['gm'] === 1) ? "<b><i><a title='$title' href='profiles.php?id=$id'>" : "<b><a title='$title' href='profiles.php?id=$id'>";
        $name .= $gradient;
        $name .= ((int) $row['admin'] === 1 || (int) $row['gm'] === 1) ? "</a></u></b>" : "</a></b>";
    } elseif (!empty($row['colours']) && (int) $row['gradient'] === 3 && (int) $row['gndays'] > 0) {
        $gn = explode("~", str_replace('#', '', $row['colours']));
        $username = $row['username'];
        $half = (int) (strlen($username) / 2);
        $left = substr($username, 0, $half);
        $right = substr($username, $half);
        $gradient = text_gradient($gn[0] ?? '', $gn[1] ?? '', 1, $left);
        $gradient .= text_gradient($gn[1] ?? '', $gn[2] ?? '', 1, $right);
        if ((int) $id === 146) {
            $gradient = "<span style='text-shadow:0 0 2px #404200;letter-spacing:-1px;font-weight:900;font-size:16px;'>$gradient</span>";
        }
        $name .= ((int) $row['admin'] === 1 || (int) $row['gm'] === 1) ? "<b><i><a title='$title' href='profiles.php?id=$id'>" : "<b><a title='$title' href='profiles.php?id=$id'>";
        $name .= $gradient;
        $name .= ((int) $row['admin'] === 1 || (int) $row['gm'] === 1) ? "</a></i></b>" : "</a></b>";
    } elseif ((int) $id === 146) {
        $name .= "<a title='$title' href='profiles.php?id=$id'>{$row['username']}</a>";
    } elseif ($row && ((int) $row['admin'] === 1 || (int) $row['gm'] === 1)) {
        $name .= "<i><b><a title='$title' href='profiles.php?id=$id'><font color='$whichfont'>{$row['username']}</a></font></b></i>";
    } elseif ($row && (int) $row['rmdays'] > 0) {
        $name .= "<b><a title='$title' href='profiles.php?id=$id'><font color='$whichfont'>{$row['username']}</a></font></b>";
    } elseif ($row) {
        $name .= "<a title='$title' href='profiles.php?id=$id'><font color='$whichfont'>{$row['username']}</a></font>";
    } else {
        $name .= "<a href='profiles.php?id=$id'>Unknown</a>";
    }

    if ($row && (int) $row['prestige'] > 0) {
        if ((int) $row['prestige'] >= 10) {
            $db->query("SELECT skull FROM prestige_skull WHERE user_id = ? LIMIT 1");
            $db->execute([$id]);
            $skull = $db->fetch_single();
            if ($skull !== false) {
                $name .= " <img src='images/skullpres_" . (int) $skull . ".png?v=5' title='Prestige ({$row['prestige']})' height='36' width='36' />";
            } else {
                $name .= " <img src='images/skullpres_" . (int) $row['prestige'] . ".png?v=5' title='Prestige ({$row['prestige']})' height='36' width='36' />";
            }
        } else {
            $name .= " <img src='images/skullpres_" . (int) $row['prestige'] . ".png?v=5' title='Prestige ({$row['prestige']})' height='36' width='36' />";
        }
    }

    $ttl = 86400;
    if (!$cache->exists($verKey)) {
        $cache->set($verKey, $version);
    }
    $cache->setex($cacheKey, $ttl, $name);

    return $name;
}

/**
 * Invalidate the cached formatted name for a user.
 * Call this whenever the display could change:
 *  - ban/unban
 *  - user joins/leaves a gang
 *  - VIP days change
 *  - image/gradient name changes
 *  - prestige / skull changes
 */
function invalidateFormattedName($userId)
{
    global $cache;
    $verKey = "usr:fmt:v:" . (int) $userId;
    // Bump version; no need to delete any keys
    $cache->incr($verKey);
}

function invalidateGangMembersFormattedNames($gangId)
{
    global $db, $cache;
    $db->query("SELECT id FROM grpgusers WHERE gang = ?");
    $db->execute([(int) $gangId]);
    while ($uid = $db->fetch_single()) {
        $cache->incr("usr:fmt:v:" . (int) $uid);
    }
}

function text_gradient($startcol, $endcol, $fontsize, $user)
{
    $letters = str_split($user, 1);
    $graduations = count($letters);
    $graduations--;
    $startcoln['r'] = hexdec(substr($startcol, 0, 2));
    $startcoln['g'] = hexdec(substr($startcol, 2, 2));
    $startcoln['b'] = hexdec(substr($startcol, 4, 2));
    $GSize['r'] = (hexdec(substr($endcol, 0, 2)) - $startcoln['r']) / $graduations;
    $GSize['g'] = (hexdec(substr($endcol, 2, 2)) - $startcoln['g']) / $graduations;
    $GSize['b'] = (hexdec(substr($endcol, 4, 2)) - $startcoln['b']) / $graduations;
    for ($i = 0; $i <= $graduations; $i++) {
        $HexR = dechex(intval($startcoln['r'] + ($GSize['r'] * $i)));
        $HexG = dechex(intval($startcoln['g'] + ($GSize['g'] * $i)));
        $HexB = dechex(intval($startcoln['b'] + ($GSize['b'] * $i)));
        if (strlen($HexR) == 1)
            $HexR = "0$HexR";
        if (strlen($HexG) == 1)
            $HexG = "0$HexG";
        if (strlen($HexB) == 1)
            $HexB = "0$HexB";
        $HexCol[] = "$HexR$HexG$HexB";
    }
    $i = 0;
    $user = "";
    while ($i < count($letters)) {
        $user .= "<span style=\"color:#$HexCol[$i]\">{$letters[$i]}</span>";
        $i++;
    }
    return $user;
}
function raidMission($userId)
{
    global $db;

    $user_class = new User($userId);

    $prestigeUserSKills = getUserPrestigeSkills($user_class);
    $pointsPayoutBoost = 0;
    if ($prestigeUserSKills['mission_point_boost_level'] > 0) {
        $pointsPayoutBoost = 2 * $prestigeUserSKills['mission_point_boost_level'];
    }


    $db->query("SELECT * FROM missions WHERE userid = ? AND completed = 'no'");
    $db->execute(array(
        $user_class->id
    ));

    if ($userMiss = $db->fetch_row(true)) {
        $db->query("SELECT * FROM mission WHERE id = ?");
        $db->execute(array(
            $userMiss['mid']
        ));
        $miss = $db->fetch_row(true);


        $db->query("UPDATE missions SET raids = raids + 1 WHERE userid = ? AND completed = 'no'");
        $db->execute([$user_class->id]);
        if (++$userMiss['raids'] == $miss['raids']) {
            $mPointsPayout = $miss['payRaids'];
            if ($pointsPayoutBoost) {
                $mPointsPayout = $mPointsPayout + ($mPointsPayout / 100 * $pointsPayoutBoost);
            }

            $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
            $db->execute(array(
                $mPointsPayout,
                $user_class->id
            ));
            $db->query("INSERT INTO missionlog VALUES(NULL,'[x] successfully completed {$miss['name']} objective to get {$miss['raids']} Raids,$user_class->id',unix_timestamp())");
            $db->execute();
            Send_event($user_class->id, "You have completed {$miss['name']} objective to get {$miss['raids']} raids.");
        }
    } else {
        return 1;
    }

    return 1;
}

function contribute_mission($update, $howmany = 1)
{
    global $db, $user_class, $cache;

    $prestigeUserSKills = getUserPrestigeSkills($user_class);
    $pointsPayoutBoost = 0;
    if ($prestigeUserSKills['mission_point_boost_level'] > 0) {
        $pointsPayoutBoost = 2 * $prestigeUserSKills['mission_point_boost_level'];
    }

    $userMiss = get_user_mission($user_class->id);
    if (empty($userMiss)) {
        return 1;
    }

    $db->query("SELECT * FROM mission WHERE id = ?");
    $db->execute([$userMiss['mid']]);
    $miss = $db->fetch_row(true);

    // ---- Inline cache update helper ----
    $cacheKey = "userMission:{$user_class->id}";
    $updateCache = function ($field, $increment = 1) use ($cache, $cacheKey, &$userMiss) {
        $cached = $cache->get($cacheKey);
        if ($cached !== null) {
            $decoded = json_decode($cached, true);
            if (is_array($decoded)) {
                if (!isset($decoded[$field])) {
                    $decoded[$field] = 0;
                }
                $decoded[$field] += $increment;
                $cache->setEx($cacheKey, 300, json_encode($decoded));
            }
        }
        // also keep local copy in sync
        if (!isset($userMiss[$field])) {
            $userMiss[$field] = 0;
        }
        $userMiss[$field] += $increment;
    };
    // ------------------------------------

    if ($update == 'k') {
        $db->query("UPDATE missions SET kills = kills + 1 WHERE userid = ? AND completed = 'no'");
        $db->execute([$user_class->id]);
        $updateCache('kills', 1);
    }
    if ($update == 'b') {
        if ($prestigeUserSKills['super_busts_unlock'] > 0) {
            $db->query("UPDATE missions SET busts = busts + 5 WHERE userid = ? AND completed = 'no'");
            $updateCache('busts', 5);
        } else {
            $db->query("UPDATE missions SET busts = busts + 1 WHERE userid = ? AND completed = 'no'");
            $updateCache('busts', 1);
        }
        $db->execute([$user_class->id]);
    }
    if ($update == 'c') {
        $db->query("UPDATE missions SET crimes = crimes + ? WHERE userid = ? AND completed = 'no'");
        $db->execute([$howmany, $user_class->id]);
        $updateCache('crimes', $howmany);
    }
    if ($update == 'm') {
        $db->query("UPDATE missions SET mugs = mugs + ? WHERE userid = ? AND completed = 'no'");
        $db->execute([$howmany, $user_class->id]);
        $updateCache('mugs', $howmany);
    }
    if ($update == 'ba') {
        $db->query("UPDATE missions SET backalleys = backalleys + 1 WHERE userid = ? AND completed = 'no'");
        $db->execute([$user_class->id]);
        $updateCache('backalleys', 1);

        if ($userMiss['backalleys'] == $miss['backalleys']) {
            $mPointsPayout = $miss['payBackalleys'];
            if ($pointsPayoutBoost) {
                $mPointsPayout += ($mPointsPayout / 100 * $pointsPayoutBoost);
            }
            $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
            $db->execute([$mPointsPayout, $user_class->id]);
            $db->query("INSERT INTO missionlog VALUES(NULL, ?, unix_timestamp())");
            $db->execute(["[x] successfully completed {$miss['name']} objective to get {$miss['backalleys']} backalleys,$user_class->id"]);
            Send_event($user_class->id, "You have completed {$miss['name']} objective to get {$miss['backalleys']} backalleys.");
        }
    }
    if ($update == 'ra') {
        $db->query("UPDATE missions SET raids = raids + 1 WHERE userid = ? AND completed = 'no'");
        $db->execute([$user_class->id]);
        $updateCache('raids', 1);

        if ($userMiss['raids'] == $miss['raids']) {
            $mPointsPayout = $miss['payRaids'];
            if ($pointsPayoutBoost) {
                $mPointsPayout += ($mPointsPayout / 100 * $pointsPayoutBoost);
            }
            $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
            $db->execute([$mPointsPayout, $user_class->id]);
            $db->query("INSERT INTO missionlog VALUES(NULL, ?, unix_timestamp())");
            $db->execute(["[x] successfully completed {$miss['name']} objective to get {$miss['raids']} raids,$user_class->id"]);
            Send_event($user_class->id, "You have completed {$miss['name']} objective to get {$miss['raids']} raids.");
        }
    }

    return 1;
}

function bloodbath($att, $id, $amnt = 1, $user = null)
{
    global $db, $user_class;

    $db->query("INSERT INTO bbusers (userid, $att) VALUES (?, ?) ON DUPLICATE KEY UPDATE $att = $att + VALUES($att)");
    $db->execute([$id, $amnt]);

    if ($att == 'crimes') {
        if ($user_class->gang) {
            $gangId = $user_class->gang;
        } else if (isset($user) && isset($user->gang)) {
            $gangId = $user->gang;
        } else {
            $db->query("SELECT gang FROM grpgusers WHERE id = ?");
            $db->execute([$id]);
            $gangId = $db->fetch_single();
        }

        if (isset($gangId) && $gangId > 0) {
            $db->query("UPDATE gangs SET dailyCrimes = dailyCrimes + ? WHERE id = ?");
            $db->execute(array(
                $amnt,
                $gangId
            ));
        }
    }
}
function emotes()
{
    global $smiarr;
    $innarr = array();
    print <<<OUT
        <script type="text/javascript">
        function addsmiley(code) {
                var pretext = document.message.msgtext.value;
                this.code = code;
                document.message.msgtext.value = pretext + code;
                document.message.msgtext.focus;
                $('#reply').focus();
        }
        </script>
OUT;
    foreach ($smiarr as $index => $img) {
        if (empty($img[1]))
            $img[1] = $img[2] = 19;
        if (isset($innarr[$img[0]]))
            continue;
        echo ' <img src="smileys/' . $img[0] . '" onClick="addsmiley(\' ' . $index . ' \')" style="cursor:pointer;border:0;width:' . $img[1] . 'px;height:' . $img[2] . 'px;" /> ';
        $innarr[$img[0]] = 1;
    }
}
function genBars()
{
    global $user_class;
    $rtn = '<div class="row" align="center">';

    // Energy Bar
    $rtn .= '<div class="col" style="padding: 0px !important;">';
    $rtn .= 'Energy';
    $rtn .= '<div class="progress" style="height: 8px;width: 95%;">';
    $rtn .= '<div class="progress-bar bg-danger" role="progressbar" style="width: ' . $user_class->energypercent . '%;" aria-valuemin="0" aria-valuemax="100"></div>';
    $rtn .= '</div></div>';

    // Nerve Bar
    $rtn .= '<div class="col" style="padding: 0px !important;">';
    $rtn .= 'Nerve';
    $rtn .= '<div class="progress" style="height: 8px;width: 95%;">';
    $rtn .= '<div class="progress-bar bg-warning" role="progressbar" style="width: ' . $user_class->nervepercent . '%;" aria-valuemin="0" aria-valuemax="100"></div>';
    $rtn .= '</div></div>';

    // Awake Bar
    $rtn .= '<div class="col" style="padding: 0px !important;">';
    $rtn .= 'Awake';
    $rtn .= '<div class="progress" style="height: 8px;width: 95%;">';
    $rtn .= '<div class="progress-bar bg-info" role="progressbar" style="width: ' . $user_class->awakepercent . '%;" aria-valuemin="0" aria-valuemax="100"></div>';
    $rtn .= '</div></div>';


    // EXP Bar
    $rtn .= '<div class="col" style="padding: 0px !important;">';
    $rtn .= 'EXP';
    $rtn .= '<div class="progress" style="height: 8px;width: 95%;">';
    $rtn .= '<div class="progress-bar bg-primary" role="progressbar" style="width: ' . $user_class->exppercent . '%;" aria-valuemin="0" aria-valuemax="100"></div>';
    $rtn .= '</div>';
    $rtn .= '</div>'; // Close EXP column

    $rtn .= '</div>'; // Close row
    return $rtn;
}


function gangContest($adds)
{
    global $user_class, $db;
    $adding = [];
    foreach ($adds as $att => $perk)
        $adding[] = "$att = $att + $perk, total_$att = total_$att + $perk";
    $db->query("UPDATE gangcontest SET " . implode(",", $adding) . " WHERE userid = ?");
    $db->execute(array(
        $user_class->id
    ));
}

function diefun($msg)
{
    echo Message($msg);
    include "footer.php";
    die();
}

function Take_Pet($petid, $userid)
{
    global $db;
    $db->query("SELECT petid FROM pets WHERE userid = ? AND petid = ?");
    $db->execute(array(
        $userid,
        $petid
    ));
    $pet = $db->fetch_single();
    if (!$pet)
        return false;
    $db->query("DELETE FROM pets WHERE userid = ? AND petid = ?");
    $db->execute(array(
        $userid,
        $petid
    ));
}

function Give_Pet($petid, $userid, $picture, $str = 10, $spe = 10, $def = 10, $name = null)
{
    global $db;

    $db->query("SELECT petid FROM pets WHERE userid = ? AND petid = ?");
    $db->execute(array(
        $userid,
        $petid
    ));
    $pet = $db->fetch_row(true);
    if (!empty($pet)) {
        error_log("User $userid tried to get pet $petid but already has it. -> " . json_encode($pet));
        return false;
    }

    if ($name == null) {
        $db->query("SELECT `name` FROM petshop WHERE id = ?");
        $db->execute([$petid]);
        $petName = $db->fetch_single();
        if (!isset($petName)) {
            $name = "No Name";
        } else {
            $name = $petName;
        }
    }

    $db->query("INSERT INTO pets (petid, userid, str, spe, def, pname, avi) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $db->execute([
        $petid,
        $userid,
        $str,
        $spe,
        $def,
        $name,
        $picture
    ]);
    return true;
}

function Check_Pet($petid, $userid)
{
    global $db;
    $db->query("SELECT petid FROM pets WHERE userid = ? AND petid = ?");
    $db->execute(array(
        $userid,
        $petid
    ));
    $pet = $db->fetch_single();
    return ($pet) ? true : false;
}
function genHead($text)
{
    print '<tr><td class="contentspacer"></td></tr><tr><td class="contenthead">' . $text . '</td></tr><tr><td class="contentcontent">';
}
function gcTalking(int $which = 0, int $gang = 0): string
{
    global $db;

    // Select only the columns you need (avoid SELECT *)
    if ($which === 0) {
        $db->query("SELECT userid, typing FROM gcusers");
        $db->execute();
    } else {
        $db->query("SELECT userid, typing FROM gmusers WHERE gang = ?");
        $db->execute([$gang]);
    }

    // Prefer a clear "fetch all rows" method; adjust to your DB wrapper
    $rows = $db->fetch_row();  // if your wrapper is different, use the equivalent
    if (!$rows)
        $rows = [];

    $out = '<div class="gcgrid" id="gcgrid">';

    $seen = [];
    foreach ($rows as $row) {
        $uid = (int) $row['userid'];

        if (isset($seen[$uid]))
            continue;

        $seen[$uid] = true;

        $isTyping = !empty($row['typing']);

        $nameHtml = formatName($uid);
        $out .= '<div class="gcitem' . ($isTyping ? ' is-typing' : '') . '" data-uid="' . $uid . '" data-tag="[tag]' . $uid . '[/tag]">';
        $out .= '<span class="gcname">' . $nameHtml . '</span>';   // no htmlspecialchars
        $out .= '</div>';
    }

    $out .= '</div>';
    return $out;
}

function give_nerve($amount)
{
    global $user_class, $db;

    if ($user_class->nerve >= $user_class->maxnerve) {
        return 0;
    }

    if ($user_class->nerve + $amount > $user_class->maxnerve) {
        $amount = $user_class->maxnerve;
    } else {
        $amount += $user_class->nerve;
    }

    $db->query("UPDATE grpgusers SET nerve = ? WHERE id = ?");
    $db->execute(array(
        $amount,
        $user_class->id
    ));
    $user_class->nerve = $amount;
    $user_class->nervepercent = floor(($user_class->nerve / $user_class->maxnerve) * 100);

    return $amount;
}

function give_energy($amount)
{
    global $user_class, $db;

    if ($user_class->energy >= $user_class->maxenergy) {
        return 0;
    }

    if ($user_class->energy + $amount > $user_class->maxenergy) {
        $amount = $user_class->maxenergy;
    } else {
        $amount += $user_class->energy;
    }

    $db->query("UPDATE grpgusers SET energy = ? WHERE id = ?");
    $db->execute(array(
        $amount,
        $user_class->id
    ));
    $user_class->energy = $amount;
    $user_class->energypercent = floor(($user_class->energy / $user_class->maxenergy) * 100);

    return $amount;
}

/// Adds $amount to current users' current amount of cityturns
function give_maze_turns($amount)
{
    global $user_class, $db;

    $db->query("UPDATE grpgusers SET cityturns = cityturns + ? WHERE id = ?");
    $db->execute(array(
        $amount,
        $user_class->id
    ));
    $user_class->cityturns += $amount;
}

function refill($which)
{
    global $user_class, $db;

    // Define the lock file path


    // Open the lock file for writing

    switch ($which) {
        case 'n':
            if ($user_class->nerref == 2) {
                // if($user_class->nerve < 0){
                //     if($user_class->points < 1){
                //         diefun("You do not have enough nerve to refill.");
                //     }
                //     $user_class->nerve = 0;
                //     $db->query("UPDATE grpgusers SET nerve = 0, points = points -1 WHERE id = ?");
                //     $db->execute(array(
                //         $user_class->id
                //     ));
                // }
                $nerveneeded = $user_class->maxnerve;

                $cost = floor($nerveneeded / 10);
                if ($cost < 10)
                    $cost = 10;
                if ($cost > $user_class->points)
                    return 0;
                $user_class->nerve += $cost * 10;
                $user_class->nerve = $user_class->maxnerve;
                $user_class->points -= $cost;
                $db->query("UPDATE grpgusers SET nerve = ?, points = points - ? WHERE id = ?");
                $db->execute(array(
                    $user_class->nerve,
                    $cost,
                    $user_class->id
                ));
            } else
                return 0;
            break;
        case 'e':
            if ($user_class->ngyref == 2) {
                if (10 > $user_class->points)
                    return 0;
                $user_class->energypercent = 100;
                $user_class->energy = $user_class->maxenergy;
                $user_class->points -= 10;
                $db->query("UPDATE grpgusers SET energy = ?, points = points - 10 WHERE id = ?");
                $db->execute(array(
                    $user_class->energy,
                    $user_class->id
                ));
            } else
                return 0;
            break;
    }
}

function manual_refill($which)
{
    global $user_class, $db;
    switch ($which) {
        case 'n':
            $nerveneeded = $user_class->maxnerve - $user_class->nerve;
            if ($nerveneeded == 0) {
                return 0;
            }
            $cost = floor($nerveneeded / 10);
            if ($cost < 10) {
                $cost = 10;
            }

            if ($cost > $user_class->points) {
                return 0;
            }

            $user_class->nerve = $user_class->maxnerve;
            $user_class->points -= $cost;
            $db->query("UPDATE grpgusers SET nerve = ?, points = points - ? WHERE id = ?");
            $db->execute(array(
                $user_class->nerve,
                $cost,
                $user_class->id
            ));
            break;
        case 'e':
            if (10 > $user_class->points)
                return 0;
            if ($user_class->energy == $user_class->maxenergy)
                return 0;
            $user_class->energypercent = 100;
            $user_class->energy = $user_class->maxenergy;
            $user_class->points -= 10;
            $db->query("UPDATE grpgusers SET energy = ?, points = points - 10 WHERE id = ?");
            $db->execute(array(
                $user_class->energy,
                $user_class->id
            ));
            break;
    }
}
function pet_refill($which)
{
    global $pet_class, $user_class, $db;
    switch ($which) {
        case 'n':
            if ($pet_class->nerref == 2) {
                $nerveneeded = $pet_class->maxnerve - $pet_class->nerve;
                $cost = floor($nerveneeded / 10);
                if ($cost < 10)
                    $cost = 10;
                if ($cost > $user_class->points)
                    return 0;
                $pet_class->nerve += $cost * 10;
                $pet_class->nerve = ($pet_class->nerve > $pet_class->maxnerve) ? $pet_class->maxnerve : $pet_class->nerve;
                $user_class->points -= $cost;
                $db->query("UPDATE grpgusers SET points = ? WHERE id = ?");
                $db->execute(array(
                    $user_class->points,
                    $user_class->id
                ));
                $db->query("UPDATE pets SET nerve = ? WHERE userid = ?");
                $db->execute(array(
                    $pet_class->nerve,
                    $user_class->id
                ));
                return 1;
            } else
                return 0;
            break;
        case 'e':
            if ($user_class->ngyref == 2) {
                if (10 > $user_class->points)
                    return 0;
                $user_class->energypercent = 100;
                $user_class->energy = $user_class->maxenergy;
                $user_class->points -= 10;
                $db->query("UPDATE grpgusers SET energy = ?, points = ? WHERE id = ?");
                $db->execute(array(
                    $user_class->energy,
                    $user_class->points,
                    $user_class->id
                ));
            } else
                return 0;
            break;
    }
}
function banklog($limit = 25, $which = 'all', $format = 'us')
{
    global $user_class, $db;
    $dateformat = ($format == 'us') ? "m/d/Y, g:i:s a" : "d/m/Y, g:i:s a";
    $ret = "
        <table id='newtables' style='width:90%;table-layout:fixed;'>
            <tr>
                <th>Date/Time</th>
                <th>Adjustment</th>
                <th>New Balance</th>
            </tr>";
    switch ($which) {
        case 'all':
            $sql = "";
            break;
        case 'money':
            $sql = " AND action IN ('mdep','mwith')";
            break;
        case 'points':
            $sql = " AND action IN ('pdep','pwith')";
            break;
        case 'withs':
            $sql = " AND action IN ('pwith','mwith')";
            break;
        case 'deps':
            $sql = " AND action IN ('mdep','pdep')";
            break;
    }
    $db->query("SELECT * FROM bank_log WHERE userid = ?{$sql} ORDER BY `id` DESC LIMIT $limit");
    $db->execute(array(
        $user_class->id
    ));
    $rows = $db->fetch_row();
    foreach ($rows as $line) {
        switch ($line['action']) {
            case 'mdep':
                $adjustment = "<span style='color:green;'> + " . prettynum($line['amount'], 1) . "</span>";
                $newbalance = prettynum($line['newbalance'], 1);
                break;
            case 'mwith':
                $adjustment = "<span style='color:red;'> - " . prettynum($line['amount'], 1) . "</span>";
                $newbalance = prettynum($line['newbalance'], 1);
                break;
            case 'pdep':
                $adjustment = "<span style='color:green;'> + " . prettynum($line['amount']) . " Points</span>";
                $newbalance = prettynum($line['newbalance']) . " Points";
                break;
            case 'pwith':
                $adjustment = "<span style='color:red;'> - " . prettynum($line['amount']) . " Points</span>";
                $newbalance = prettynum($line['newbalance']) . " Points";
                break;
        }
        $ret .= "
            <tr>
                <td>" . date($dateformat, $line['timestamp']) . "</td>
                <td>$adjustment</td>
                <td>$newbalance</td>
            </tr>";
    }
    $ret .= "
        </table>
    ";
    return $ret;
}

function staff_banklog($user, $limit = 25, $which = 'all', $format = 'us')
{
    global $user_class, $db;
    $dateformat = ($format == 'us') ? "m/d/Y, g:i:s a" : "d/m/Y, g:i:s a";
    $ret = "
        <table id='newtables' style='width:90%;table-layout:fixed;'>
            <tr>
                <th>Date/Time</th>
                <th>Adjustment</th>
                <th>New Balance</th>
            </tr>";
    switch ($which) {
        case 'all':
            $sql = "";
            break;
        case 'money':
            $sql = " AND action IN ('mdep','mwith')";
            break;
        case 'points':
            $sql = " AND action IN ('pdep','pwith')";
            break;
        case 'withs':
            $sql = " AND action IN ('pwith','mwith')";
            break;
        case 'deps':
            $sql = " AND action IN ('mdep','pdep')";
            break;
    }
    $db->query("SELECT * FROM bank_log WHERE userid = ?{$sql} ORDER BY `id` DESC LIMIT $limit");
    $db->execute(array(
        $user
    ));
    $rows = $db->fetch_row();
    foreach ($rows as $line) {
        switch ($line['action']) {
            case 'mdep':
                $adjustment = "<span style='color:green;'> + " . prettynum($line['amount'], 1) . "</span>";
                $newbalance = prettynum($line['newbalance'], 1);
                break;
            case 'mwith':
                $adjustment = "<span style='color:red;'> - " . prettynum($line['amount'], 1) . "</span>";
                $newbalance = prettynum($line['newbalance'], 1);
                break;
            case 'pdep':
                $adjustment = "<span style='color:green;'> + " . prettynum($line['amount']) . " Points</span>";
                $newbalance = prettynum($line['newbalance']) . " Points";
                break;
            case 'pwith':
                $adjustment = "<span style='color:red;'> - " . prettynum($line['amount']) . " Points</span>";
                $newbalance = prettynum($line['newbalance']) . " Points";
                break;
        }
        $ret .= "
            <tr>
                <td>" . date($dateformat, $line['timestamp']) . "</td>
                <td>$adjustment</td>
                <td>$newbalance</td>
            </tr>";
    }
    $ret .= "
        </table>
    ";
    return $ret;
}
function mailHeader()
{
    return "
        <table id='newtables' class='linkstable' style='width:100%;table-layout:fixed;'>
            <tr>
                <td align='center'><a href='pms.php?view=new'>New</a></td>
                <td align='center'><a href='pms.php?view=inbox'>Inbox</a></td>
                <td align='center'><a href='pms.php?view=outbox'>Outbox</a></td>
            </tr>
        </table>";
}
function ofthes($userid, &$toadd)
{
    global $db;
    $sql = array();
    foreach ($toadd as $what => $add)
        $sql[] = "$what = $what + $add";
    if (count($sql) == 0)
        return;
    $db->query("UPDATE ofthes SET " . implode(",", $sql) . " WHERE userid = $userid");
    $db->execute();
}
function check_items($itemid, $userid = null)
{
    global $db, $user_class;
    if (empty($userid))
        $userid = $user_class->id;
    $db->query("SELECT quantity FROM inventory WHERE userid = ? AND itemid = ?");
    $db->execute(array(
        $userid,
        $itemid
    ));
    return $db->fetch_single();
}
function nameGen($gndays, $donatordays, $uninfo, $username)
{
    $uninfo = explode("|", $uninfo);
    $out = explode("~", $uninfo[4]);
    if ($gndays > 0) {
        $gnparts = $uninfo[0];
        $glowparts = $uninfo[5];
        $glows = explode(",", $out[1]);
        $gn = explode("~", $uninfo[1]);
        switch ($gnparts) {
            case 3:
                $half = (int) ((strlen($username) / 2));
                $left = substr($username, 0, $half);
                $right = substr($username, $half);
                for ($i = 0; $i < 3; $i++)
                    $gn[$i] = empty($gn[$i]) ? "#000000" : $gn[$i];
                $gnarray = array_merge(gradient($gn[0], $gn[1], strlen($left)), gradient($gn[1], $gn[2], strlen($right)));
                break;
            case 2:
                $gnarray = gradient($gn[0], $gn[1], strlen($username));
                break;
            default:
                for ($i = 0; $i < strlen($username); $i++)
                    $gnarray[] = $gn[0];
                break;
        }
        switch ($glowparts) {
            case 3:
                $half = (int) ((strlen($username) / 2));
                $left = substr($username, 0, $half);
                $right = substr($username, $half);
                for ($i = 0; $i < 3; $i++)
                    $glows[$i] = empty($glows[$i]) ? "#000000" : $glows[$i];
                $glowsarray = array_merge(gradient($glows[0], $glows[1], strlen($left)), gradient($glows[1], $glows[2], strlen($right)));
                break;
            case 2:
                $glowsarray = gradient($glows[0], $glows[1], strlen($username));
                break;
            default:
                for ($i = 0; $i < strlen($username); $i++)
                    $glowsarray[] = $glows[0];
                break;
        }
        $len = strlen($username);
        $un = '';
        for ($i = 0; $i < $len; $i++) {
            $un .= '<span style="color:#' . str_replace('#', '', $gnarray[$i]) . ';text-shadow: 0 0 ' . $out[0] . 'px #' . str_replace('#', '', $glowsarray[$i]) . ';">' . $username[$i] . '</span>';
        }
        $bold = "font-weight:{$uninfo[2]};";
        $italic = ($uninfo[3] == 'yes') ? "font-style:italic;" : "";
        $spacing = ($uninfo[5] != 'normal') ? "letter-spacing:{$uninfo[5]}px;" : "";
        $title = "GD: {$gndays}, VIP: {$donatordays}";
        if ($username == 'StarK') {
            $fontsize = "font-size:2em;font-family:'Boogaloo';";
            $title = "Developer";
            return "<span title=\"{$title}\" style=\"{$fontsize}\">" . $un . "</span>";
        } else if ($username == "Punisher") {
            $fontsize = "font-size:1.5em;";
            return "<span title=\"{$title}\" style=\"{$bold}{$italic}{$spacing}{$fontsize}\">" . $un . "</span>";
        } else if ($username == "Admin") {
            $fontsize = "font-size:2em;font-family:'Chewy';";
            return "<span title=\"{$title}\" style=\"{$bold}{$italic}{$spacing}{$fontsize}\">" . $un . "</span>";
        } else {
            $fontsize = "font-size:1.25em;";

            return "<span title=\"{$title}\" style=\"{$bold}{$italic}{$spacing}{$fontsize}\">" . $un . "</span>";
        }
    } else if ($donatordays > 0) {
        $days = "RM: {$donatordays}";
        return "<span class=\"rm\" title=\"$days\">$username</span>";
    } else
        return "<span class=\"user\">$username</span>";
}
function gradient($from_color, $to_color, $graduations = 10)
{
    $graduations--;
    $startcol = str_replace("#", "", $from_color);
    $endcol = str_replace("#", "", $to_color);
    $RedOrigin = hexdec(substr($startcol, 0, 2));
    $GrnOrigin = hexdec(substr($startcol, 2, 2));
    $BluOrigin = hexdec(substr($startcol, 4, 2));
    if ($graduations >= 2) { // for at least 3 colors
        $GradientSizeRed = (hexdec(substr($endcol, 0, 2)) - $RedOrigin) / $graduations; //Graduation Size Red
        $GradientSizeGrn = (hexdec(substr($endcol, 2, 2)) - $GrnOrigin) / $graduations;
        $GradientSizeBlu = (hexdec(substr($endcol, 4, 2)) - $BluOrigin) / $graduations;
        for ($i = 0; $i <= $graduations; $i++) {
            $RetVal[$i] = strtoupper("#" . str_pad(dechex($RedOrigin + ($GradientSizeRed * $i)), 2, '0', STR_PAD_LEFT) .
                str_pad(dechex($GrnOrigin + ($GradientSizeGrn * $i)), 2, '0', STR_PAD_LEFT) .
                str_pad(dechex($BluOrigin + ($GradientSizeBlu * $i)), 2, '0', STR_PAD_LEFT));
        }
    } elseif ($graduations == 1) { // exactlly 2 colors
        $RetVal[] = $from_color;
        $RetVal[] = $to_color;
    } else { // one color
        $RetVal[] = $from_color;
    }
    return $RetVal;
}
function missiontype($which)
{
    switch ($which) {
        case 'crimes1':
            return "1+ nerve crimes";
        case 'crimes5':
            return "5+ nerve crimes";
        case 'crimes10':
            return "10+ nerve crimes";
        case 'crimes25':
            return "25+ nerve crimes";
        case 'crimes50':
            return "50+ nerve crimes";
        case 'kills':
            return "Kills";
        case 'busts':
            return "Busts";
        case 'mugs':
            return "Mugs";
        default:
            return $which;
    }
}
function newmissions($what, $howmany = 1)
{
    global $db, $user_class;
    $db->query("SELECT * FROM missions_in_progress WHERE userid = ? AND status = 'inprogress'");
    $db->execute(array(
        $user_class->id
    ));
    $r = $db->fetch_row(true);
    if (empty($r))
        return;
    $reqs = $r['requirements'];
    $done = explode("|", $r['done']);
    $reqs = explode(";", $reqs);
    for ($i = 0; $i < count($reqs); $i++) {
        $the = explode("|", $reqs[$i]);
        $final[$the[0]] = array( //att
            $i, //done offset
            $the[1], //requirement
            $the[2], //payout
            $the[3]  //currency
        );
    }
    switch ($what) {
        case 'crimes50':
            if (key_exists('crimes50', $final)) {
                $array = $final['crimes50'];
                if (missioncheck($array, $done, $what, $howmany))
                    break;
            }
        case 'crimes25':
            if (key_exists('crimes25', $final)) {
                $array = $final['crimes25'];
                if (missioncheck($array, $done, $what, $howmany))
                    break;
            }
        case 'crimes10':
            if (key_exists('crimes10', $final)) {
                $array = $final['crimes10'];
                if (missioncheck($array, $done, $what, $howmany))
                    break;
            }
        case 'crimes5':
            if (key_exists('crimes5', $final)) {
                $array = $final['crimes5'];
                if (missioncheck($array, $done, $what, $howmany))
                    break;
            }
        case 'crimes1':
            if (key_exists('crimes1', $final)) {
                $array = $final['crimes1'];
                missioncheck($array, $done, $what, $howmany);
            }
            break;
        default:
            if (key_exists($what, $final)) {
                $array = $final[$what];
                missioncheck($array, $done, $what, $howmany);
            }
    }
    $done = implode("|", $done);
    $db->query("UPDATE missions_in_progress SET done = ? WHERE userid = ? AND status = 'inprogress'");
    $db->execute(array(
        $done,
        $user_class->id
    ));
}

function missioncheck(&$array, &$done, &$what, &$howmany)
{
    global $db, $user_class;
    $done[$array[0]] += $howmany;
    if ($done[$array[0]] == $array[1]) {
        $att = ($array[3] == "money") ? "bank" : $array[3];
        $db->query("UPDATE grpgusers SET $att = $att + ? WHERE id = ?");
        $db->execute(array(
            $array[2],
            $user_class->id
        ));
        $pay = ($att == "bank") ? prettynum($array[2], 1) : number_format($array[2]) . " " . $att;
        Send_Event($user_class->id, "You have completed the mission objective to get " . number_format($array[1]) . " " . missiontype($what) . ". [+ $pay]");
    } elseif ($done[$array[0]] > $array[1])
        return false;
    return true;
}

function anticheat()
{
    global $db, $user_class;
    //$user_class->actions += 1;
    // $db->query("UPDATE grpgusers SET actions = actions + 1 WHERE id = ?");
    // $db->execute(
    //     array(
    //         $user_class->id
    //     )
    // );
    $amt = mt_rand(25, 60);
    if ($user_class->actions >= $amt) {
        $_SESSION['anticheat'] = 1;
    }
}

function getCityNameByID($cityId)
{
    global $db;

    $db->query("SELECT `name` FROM cities WHERE id = ?");
    $db->execute([$cityId]);
    $rtn = $db->fetch_single();

    return $rtn;
}

function countdown($time, $h = true, $m = true, $s = true)
{
    $rem = $time - time();
    $day = floor($rem / 86400);
    $hr = floor(($rem % 86400) / 3600);
    $min = floor(($rem % 3600) / 60);
    $sec = ($rem % 60);

    if ($day && !$h) {
        if ($hr > 12)
            $day++; // round up if not displaying hours
    }

    $ret = array();
    if ($day && $h)
        $ret[] = ($day ? $day . "d" : "");
    if ($day && !$h)
        $ret[] = ($day ? $day . "d" : "");
    if ($hr && $h)
        $ret[] = ($hr ? $hr . "h" : "");
    if ($min && $m && $h)
        $ret[] = ($min ? $min . "m" : "");
    if ($sec && $s && $m && $h)
        $ret[] = ($sec ? $sec . "s" : "");

    $last = end($ret);
    array_pop($ret);
    $string = join(" ", $ret) . " {$last}";

    return $string;
}

function calctime($seconds = 0)
{
    $interval = date_diff(date_create("@0"), date_create("@$seconds"));

    foreach (array('y' => 'Year', 'm' => 'Month', 'D' => 'day', 'h' => 'Hour', 'i' => 'Minute', 's' => 'Second') as $format => $desc) {
        if ($interval->$format >= 1)
            $thetime[] = $interval->$format . ($interval->$format == 1 ? " $desc" : " {$desc}s");
    }

    return isset($thetime) ? implode(' ', $thetime) . ($interval->invert ? ' ago' : '') : NULL;
}

function updateGangActiveMission($field, $value)
{
    global $db, $user_class;

    // Check if the user is in a gang
    if ($user_class->gang != 0) {
        // Prepare and execute the query to check for an active mission
        $sql = "SELECT agm.kills, agm.busts, agm.crimes, agm.mugs, agm.backalleys, gm.name, gm.kills AS target_kills, gm.busts AS target_busts, gm.crimes AS target_crimes, gm.mugs AS target_mugs, gm.backalleys AS target_backalleys, gm.reward, gm.time AS 'mission_time', UNIX_TIMESTAMP() AS 'current_time', agm.end_time FROM active_gang_missions agm JOIN gang_missions gm ON agm.mission_id = gm.id WHERE agm.gangid = :gangid AND agm.completed = 0 LIMIT 1";
        $db->query($sql);
        $db->bind(':gangid', $user_class->gang);
        $activeMission = $db->fetch_row(true);

        if ($activeMission) {
            // Sanitize the field name to prevent SQL injection
            $allowed_fields = ['kills', 'busts', 'crimes', 'mugs', 'backalleys'];
            if (!in_array($field, $allowed_fields)) {
                die('Invalid field specified.');
            }

            // Prepare and execute the update statement for the active mission
            $updateSql = "UPDATE active_gang_missions SET $field = $field + :value WHERE gangid = :gangid AND completed = 0 LIMIT 1";
            $db->query($updateSql);
            $db->bind(':value', $value);
            $db->bind(':gangid', $user_class->gang);
            if (!$db->execute()) {
                die('Failed to update the mission: ' . $db->error());
            }
        }
    }
}


function generateMacroToken($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $index = mt_rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
    return $randomString;
}

function macroTokenCheck($uid)
{
    global $db;

    if (!isset($_GET['token'])) {
        echo "
            <div class='alert alert-danger'>
                <p>1 Something went wrong, an Admin has been informed.
            </div>
        ";
        exit;
    }

    $token = $_GET['token'];
    if (empty($token)) {
        echo "
            <div class='alert alert-danger'>
                <p>2 Something went wrong, an Admin has been informed.
            </div>
        ";
        exit;
    }

    $db->query("SELECT macro_token FROM grpgusers WHERE id = ? LIMIT 1");
    $db->execute([$uid]);
    $user_token = $db->fetch_single();
    if ($user_token != $token) {
        echo
            "
            <div class='alert alert-danger'>
                <p>3 Something went wrong, an Admin has been informed.
            </div>
        ";
        exit;
    }

    $newMacroToken = generateMacroToken();
    perform_query("UPDATE grpgusers SET macro_token = ? WHERE id = ?", [$newMacroToken, $uid]);
    return $newMacroToken;
}

function getItemTempUse($userId)
{
    global $db, $cache;

    $cacheKey = "item_temp_use:$userId";
    if (($cached = $cache->get($cacheKey)) !== false) {
        return json_decode($cached, true);
    }

    $db->query("SELECT * FROM item_temp_use WHERE user_id = ? LIMIT 1");
    $db->execute([(int) $userId]);
    $rows = $db->fetch_row();

    if (!isset($rows[0]['id'])) {
        $db->query("INSERT INTO item_temp_use (user_id) VALUES (?)");
        $db->execute([(int) $userId]);
        return getItemTempUse($userId);
    }

    $row = $rows[0];
    $cache->setEx($cacheKey, 360, json_encode($row));
    return $row;
}

function addItemTempUse($user_class, $field, $qty = 1)
{
    global $db, $cache;

    $setFields = ['maze_boost', 'easter_bead', 'crime_potion_time', 'crime_booster_time', 'nerve_vial_time', 'gang_double_exp_time', 'gym_10_multiplier_time', 'crime_15_multiplier_time', 'gym_protein_bar_time', 'gym_super_pills_time', 'ghost_vacuum_time', 'trick_or_treat_pass_time', 'double_gym_time', 'love_potions_time'];
    $incFields = ['…put your incrementable columns here…'];

    $item = getItemTempUse($user_class->id);
    $id = (int) $item['id'];
    $qty = (int) $qty;

    if (in_array($field, $setFields, true)) {
        $db->query("UPDATE item_temp_use SET `$field` = ? WHERE id = ?");
        $db->execute([$qty, $id]);
    } else {
        $db->query("UPDATE item_temp_use SET `$field` = `$field` + ? WHERE id = ?");
        $db->execute([$qty, $id]);
    }

    $cache->del("item_temp_use:{$user_class->id}");
}

function removeItemTempUse($userId, $field, $qty = 1)
{
    global $cache;

    $cacheKey = "item_temp_use:$userId";
    $qty = (int) $qty;
    $userId = (int) $userId;

    perform_query(
        "UPDATE item_temp_use 
         SET `$field` = GREATEST(`$field` - ?, 0) 
         WHERE user_id = ?",
        [$qty, $userId]
    );

    try {
        $newVal = $cache->hIncrBy($cacheKey, $field, -$qty);
        if ($newVal < 0) {
            $cache->hSet($cacheKey, $field, 0);
        }
    } catch (Throwable $e) {
        $cache->del($cacheKey);
    }
}

function getUserBaStats($user_class)
{
    global $db;

    $userPrestigeSkills = getUserPrestigeSkills($user_class);

    $db->query("SELECT * FROM user_ba_stats WHERE user_id = " . $user_class->id . " LIMIT 1");
    $db->execute();
    $r = $db->fetch_row();

    if (isset($r[0]['id'])) {
        $r = $r[0];
        if ($r['level'] > 15) {
            $r['maxexp'] = 10000 * 15;
            $r['maxexp'] += 25000 * ($r['level'] - 15);
        } else {
            $r['maxexp'] = 10000 * $r['level'];
        }

        if ($userPrestigeSkills['ba_point_boost_level'] > 0) {
            $r['additional_max_levels'] = $userPrestigeSkills['ba_point_boost_level'];
        }

        return $r;
    } else {
        $db->query("INSERT INTO user_ba_stats (user_id, gold_rush_credits) VALUES (" . $user_class->id . ", 20)");
        $db->execute();

        $r = getUserBaStats($user_class);

        return $r;
    }
}

function addUserBaStatExp($userBaStats, $baExpWon, $user_class)
{
    global $db;

    $maxLevels = 20;
    if (isset($userBaStats['additional_max_levels']) && $userBaStats['additional_max_levels'] > 0) {
        $maxLevels = $maxLevels + $userBaStats['additional_max_levels'];
    }

    if (isset($user_class->completeUserResearchTypesIndexedOnId[5])) {
        $maxLevels += 1;
    }
    if (isset($user_class->completeUserResearchTypesIndexedOnId[8])) {
        $maxLevels += 1;
    }
    if (isset($user_class->completeUserResearchTypesIndexedOnId[13])) {
        $maxLevels += 1;
    }
    if (isset($user_class->completeUserResearchTypesIndexedOnId[33])) {
        $maxLevels += 1;
    }
    if (isset($user_class->completeUserResearchTypesIndexedOnId[34])) {
        $maxLevels += 1;
    }
    if (isset($user_class->completeUserResearchTypesIndexedOnId[44])) {
        $maxLevels += 1;
    }
    if (isset($user_class->completeUserResearchTypesIndexedOnId[45])) {
        $maxLevels += 1;
    }
    if (isset($user_class->completeUserResearchTypesIndexedOnId[46])) {
        $maxLevels += 1;
    }


    if ($userBaStats['level'] <= $maxLevels) {
        $newExp = $userBaStats['exp'] + $baExpWon;
        if ($newExp > $userBaStats['maxexp'] && $userBaStats['level'] < $maxLevels) {
            $db->query("UPDATE `user_ba_stats` SET `exp` = 0, `level` = `level` + 1  WHERE `id` = '" . $userBaStats['id'] . "'");
            $db->execute();
        } else {
            $db->query("UPDATE `user_ba_stats` SET `exp` = `exp` + " . $baExpWon . "  WHERE `id` = '" . $userBaStats['id'] . "'");
            $db->execute();
        }
    }

    if ($userBaStats['level'] > $maxLevels) {
        $db->query("UPDATE `user_ba_stats` SET `exp` = 0, `level` = " . $maxLevels . "  WHERE `id` = '" . $userBaStats['id'] . "'");
        $db->execute();
    }
}

function getGangCompLeaderboard($gangId)
{
    global $db;

    $db->query("SELECT * FROM gang_comp_leaderboard WHERE gang_id = " . $gangId . " LIMIT 1");
    $db->execute();
    $r = $db->fetch_row();

    if (isset($r[0]['id'])) {
        return $r[0];
    } else {
        $db->query("INSERT INTO gang_comp_leaderboard (gang_id) VALUES (" . $gangId . ")");
        $db->execute();
        $r = getGangCompLeaderboard($gangId);

        return $r;
    }
}


function addToGangCompLeaderboard($gangId, $field, $value)
{
    global $db;

    $dailyField = 'daily_' . $field;
    $weeklyField = 'weekly_' . $field;

    $db->query("SELECT `id` FROM `gang_comp_leaderboard` WHERE `gang_id` = " . $gangId . " LIMIT 1");
    $db->execute();
    $gclId = $db->fetch_single();

    if ($gclId) {
        $db->query("UPDATE `gang_comp_leaderboard` SET `" . $dailyField . "` = `" . $dailyField . "` + " . $value . ", `" . $weeklyField . "` = `" . $weeklyField . "` + " . $value . " WHERE gang_id = " . $gangId);
        $db->execute();
    } else {
        $db->query("INSERT INTO `gang_comp_leaderboard` (`gang_id`, `" . $dailyField . "`, `" . $weeklyField . "`) VALUES (" . $gangId . ", " . $value . ", " . $value . ")");
        $db->execute();
    }
}

function getUserCompLeaderboard($userId)
{
    global $db;

    $db->query("SELECT * FROM user_comp_leaderboard WHERE user_id = " . $userId . " LIMIT 1");
    $db->execute();
    $r = $db->fetch_row();

    if (isset($r[0]['id'])) {
        return $r[0];
    } else {
        $db->query("INSERT INTO user_comp_leaderboard (user_id) VALUES (" . $userId . ")");
        $db->execute();
        $r = getUserCompLeaderboard($userId);

        return $r;
    }
}

function addToUserCompLeaderboard($userId, $field, $value)
{
    global $db;

    $dailyField = 'daily_' . $field;
    $weeklyField = 'overall_' . $field;

    $db->query("SELECT `id` FROM `user_comp_leaderboard` WHERE `user_id` = " . $userId . " LIMIT 1");
    $db->execute();
    $gclId = $db->fetch_single();

    if ($gclId) {
        $db->query("UPDATE `user_comp_leaderboard` SET `" . $dailyField . "` = `" . $dailyField . "` + " . $value . ", `" . $weeklyField . "` = `" . $weeklyField . "` + " . $value . " WHERE user_id = " . $userId);
        $db->execute();
    } else {
        $db->query("INSERT INTO `user_comp_leaderboard` (`user_id`, `" . $dailyField . "`, `" . $weeklyField . "`) VALUES (" . $userId . ", " . $value . ", " . $value . ")");
        $db->execute();
    }
}

function geRelCompLeaderboard($userId)
{
    global $db;

    $db->query("SELECT * FROM rel_comp_leaderboard WHERE (user_id = " . $userId . " OR two_user_id = " . $userId . ") LIMIT 1");
    $db->execute();
    $r = $db->fetch_row();

    if (isset($r[0]['id'])) {
        return $r[0];
    }

    return false;
}

function addToRelCompLeaderboard($userId, $field, $value)
{
    global $db;

    $dailyField = 'daily_' . $field;
    $weeklyField = 'overall_' . $field;

    $db->query("SELECT `id` FROM `rel_comp_leaderboard` WHERE (`user_id` = " . $userId . " OR `two_user_id` = " . $userId . ") LIMIT 1");
    $db->execute();
    $gclId = $db->fetch_single();

    if ($gclId) {
        $db->query("UPDATE `rel_comp_leaderboard` SET `" . $dailyField . "` = `" . $dailyField . "` + " . $value . ", `" . $weeklyField . "` = `" . $weeklyField . "` + " . $value . " WHERE user_id = " . $userId);
        $db->execute();

        $db->query("UPDATE `rel_comp_leaderboard` SET `" . $dailyField . "` = `" . $dailyField . "` + " . $value . ", `" . $weeklyField . "` = `" . $weeklyField . "` + " . $value . " WHERE two_user_id = " . $userId);
        $db->execute();
    }

    return null;
}

function addCountTracking($userId)
{
    global $db;

    $db->query("SELECT * FROM mission_count_tracking WHERE user_id = " . $userId . " LIMIT 1");
    $db->execute();
    $r = $db->fetch_row();

    if (isset($r[0]['id'])) {
        $r = $r[0];

        $newCount = $r['count'] + 1;

        if ($newCount > 124) {
            $db->query("UPDATE grpgusers SET donate_token = donate_token + 1 WHERE id = " . $userId);
            $db->execute();

            Send_Event($userId, 'You earned a 2 for 1 donation token for completing 125 missions!');

            $db->query("UPDATE mission_count_tracking SET count = 0 WHERE user_id = " . $userId);
            $db->execute();
        } else {
            $db->query("UPDATE mission_count_tracking SET count = count + 1 WHERE user_id = " . $userId);
            $db->execute();
        }

        return $r;
    } else {
        $db->query("INSERT INTO mission_count_tracking (user_id, count) VALUES (" . $userId . ", 1)");
        $db->execute();

        return $r;
    }

}

function checkCaptchaRequired($user_class)
{
    $captchaRequired = false;

    if ($user_class->captcha_timestamp < 1) {
        $captchaRequired = true;
    }

    $inThePast = strtotime("-45 minutes");
    if ($inThePast > $user_class->captcha_timestamp) {
        $captchaRequired = true;
    }

    return $captchaRequired;
}

function getItemDailyLimit($userId)
{
    global $db;

    $now = new \DateTime();

    $db->query("SELECT * FROM item_daily_limit WHERE user_id = " . $userId . " AND use_date = '" . $now->format('d-m-Y') . "' LIMIT 1");
    $db->execute();
    $r = $db->fetch_row();

    if (isset($r[0]['id'])) {
        return $r[0];
    } else {
        $db->query("INSERT INTO item_daily_limit (user_id, use_date) VALUES (" . $userId . ", '" . $now->format('d-m-Y') . "')");
        $db->execute();
        $r = getItemDailyLimit($userId);

        return $r;
    }
}

function addItemDailyLimit($user_class, $field, $qty = 1)
{
    global $db;

    $itemDailyLimit = getItemDailyLimit($user_class->id);

    $db->query("UPDATE item_daily_limit SET {$field} = {$field} + {$qty} WHERE id = " . $itemDailyLimit['id']);
    $db->execute();
}

function getUserItemDropLog($userId)
{
    global $db;

    $db->query("SELECT * FROM user_item_drop_log WHERE user_id = " . $userId . " LIMIT 1");
    $db->execute();
    $r = $db->fetch_row();

    if (isset($r[0]['id'])) {
        return $r[0];
    } else {
        $db->query("INSERT INTO user_item_drop_log (user_id) VALUES (" . $userId . ")");
        $db->execute();
        $r = getUserItemDropLog($userId);

        return $r;
    }
}

function addUserItemDropLog($user_class, $field, $qty = 1)
{
    global $db;

    $itemTempUse = getUserItemDropLog($user_class->id);

    $db->query("UPDATE user_item_drop_log SET {$field} = {$field} + {$qty} WHERE id = " . $itemTempUse['id']);
    $db->execute();
}

function getLimitedStorePackPurchase($userId, $limitedStorePackId)
{
    global $db;

    $db->query("SELECT * FROM limited_store_pack_purchase WHERE user_id = " . $userId . " AND limited_store_pack_id = " . $limitedStorePackId . " LIMIT 1");
    $db->execute();
    $r = $db->fetch_row();

    if (isset($r[0]['id'])) {
        return $r[0];
    } else {
        $db->query("INSERT INTO limited_store_pack_purchase (user_id, limited_store_pack_id) VALUES (" . $userId . ", " . $limitedStorePackId . ")");
        $db->execute();
        $r = getLimitedStorePackPurchase($userId, $limitedStorePackId);

        return $r;
    }
}

function addLimitedStorePackPurchase($user_class, $limitedStorePackId)
{
    global $db;

    $limitedStorePack = getLimitedStorePackPurchase($user_class->id, $limitedStorePackId);

    $db->query("UPDATE limited_store_pack_purchase SET purchases = purchases + 1 WHERE id = " . $limitedStorePack['id']);
    $db->execute();
}

function getUserPrestigeSkills($user_class)
{
    global $db, $cache;

    $cacheKey = "userPrestigeSkills:{$user_class->id}";

    // Try cache
    $cached = $cache->get($cacheKey);
    if ($cached !== null) {
        $decoded = json_decode($cached, true);
        if (is_array($decoded) && isset($decoded['id'])) {
            return $decoded;
        }
        // fall through to DB if bad cache
    }

    // Query DB
    $db->query("SELECT * FROM user_prestige_skills WHERE user_id = ? LIMIT 1");
    $db->execute([$user_class->id]);
    $row = $db->fetch_row(true);

    if (get_class($user_class) === SlimUser::class) {
        $user = new User($user_class->id);
    } else {
        $user = $user_class;
    }

    if ($row && isset($row['id'])) {
        $row['prestige_unlocks_available'] = ($user->prestige * 1) - (int) $row['unlock_points_spent'];
        $row['prestige_boosts_available'] = ($user->prestige * 5) - (int) $row['boosts_spent'];

        $cache->setEx($cacheKey, 360, json_encode($row));
        return $row;
    }

    // Ensure row exists (safe insert, ignoring duplicates)
    $db->query("INSERT IGNORE INTO user_prestige_skills (user_id) VALUES (?)");
    $db->execute([$user_class->id]);

    // Re-query once (not infinite recursion)
    $db->query("SELECT * FROM user_prestige_skills WHERE user_id = ? LIMIT 1");
    $db->execute([$user_class->id]);
    $row = $db->fetch_row(true);

    if ($row && isset($row['id'])) {
        $row['prestige_unlocks_available'] = ($user->prestige * 1) - (int) $row['unlock_points_spent'];
        $row['prestige_boosts_available'] = ($user->prestige * 5) - (int) $row['boosts_spent'];
        $cache->setEx($cacheKey, 360, json_encode($row));
        return $row;
    }

    return null;
}

function addUserPrestigeSkill($user_class, $field, $qty = 1)
{
    global $db, $cache;

    $data = getUserPrestigeSkills($user_class);

    $db->query("UPDATE user_prestige_skills SET {$field} = {$field} + ? WHERE id = ?");
    $db->execute([$qty, $data['id']]);

    $data[$field] += $qty;
    if (get_class($user_class) === SlimUser::class) {
        $user = new User($user_class->id);
    } else {
        $user = $user_class;
    }
    $data['prestige_unlocks_available'] = ($user->prestige * 1) - $data['unlock_points_spent'];
    $data['prestige_boosts_available'] = ($user->prestige * 5) - $data['boosts_spent'];

    $cache->setEx("userPrestigeSkills:{$user_class->id}", 300, json_encode($data));
}

function resetUserPrestigeSkills($id)
{
    global $db, $cache;

    $db->query("UPDATE user_prestige_skills SET energy_boost_level = 0, crime_cash_boost_level = 0, mission_point_boost_level = 0, mission_exp_boost_level = 0, ba_point_boost_level = 0, hourly_searches_boost_level = 0, ba_raidtokens_unlock = 0, speed_attack_unlock = 0, super_mugs_unlock = 0, super_busts_unlock = 0, ba_gold_rush_unlock = 0, crime_cash_unlock = 0, throne_points_unlock = 0, travel_cost_unlock = 0, ba_cash_unlock = 0, training_dummy_cash_unlock = 0, crime_exp_unlock = 0, unlock_points_spent = 0, boosts_spent = 0, reset_points = 0, research_cash_unlock = 0, research_cash_boost_level = 0, last_reset = NOW() WHERE user_id = ?");
    $db->execute([$id]);

    $cache->del("userPrestigeSkills:{$id}");
}

function getBpCategory($overrideId = null)
{
    global $db;

    if ($overrideId) {
        $db->query("SELECT * FROM bp_category WHERE id = '" . $overrideId . "' LIMIT 1");
        $db->execute();
        $r = $db->fetch_row(true);

        if (isset($r['id'])) {
            return $r;
        }
    } else {
        $now = new \DateTime();

        $db->query("SELECT * FROM bp_category WHERE month_year = '" . $now->format('m-Y') . "' LIMIT 1");
        $db->execute();
        $r = $db->fetch_row(true);

        if (isset($r['id'])) {
            return $r;
        }
    }

    return null;
}

function getBpCategoryChallenges($bpCategory)
{
    global $db;

    $now = new \DateTime();

    $db->query("SELECT * FROM bp_category_challenges WHERE bp_category_id = " . $bpCategory['id'] . " ORDER BY type ASC, amount ASC");
    $db->execute();
    $r = $db->fetch_row();

    // Index them on ID for easier use later
    $indexedById = array();
    foreach ($r as $row) {
        $indexedById[$row['id']] = $row;
    }

    return $indexedById;
}

function getBpCategoryPrizes($bpCategory)
{
    global $db;

    $db->query("SELECT * FROM bp_category_prizes WHERE bp_category_id = ? ORDER BY cost ASC");
    $db->execute([$bpCategory['id']]);
    $r = $db->fetch_row();

    // Index them on ID for easier use later
    $indexedById = array();
    foreach ($r as $row) {
        $indexedById[$row['id']] = $row;
    }

    return $indexedById;
}

function displayBpCategoryPrize($bpCategoryPrize)
{
    if ($bpCategoryPrize['type'] === 'item') {
        return number_format($bpCategoryPrize['amount'], 0) . ' x ' . Item_Name($bpCategoryPrize['entity_id']);
    } else if ($bpCategoryPrize['type'] === 'money') {
        return '$' . number_format($bpCategoryPrize['amount'], 0);
    } else if ($bpCategoryPrize['type'] === 'raid_tokens') {
        return number_format($bpCategoryPrize['amount'], 0) . ' x Raid Tokens';
    } else if ($bpCategoryPrize['type'] === 'exp') {
        return number_format($bpCategoryPrize['amount'], 0) . '% of Max EXP';
    } else if ($bpCategoryPrize['type'] === 'vip') {
        return number_format($bpCategoryPrize['amount'], 0) . ' VIP Days';
    } else {
        return number_format($bpCategoryPrize['amount'], 0) . ' x ' . ucfirst($bpCategoryPrize['type']);
    }
}


function getBpCategoryUser($bpCategory, $user_class)
{
    global $db;

    $db->query("SELECT * FROM bp_category_user WHERE user_id = ? AND bp_category_id = ? LIMIT 1");
    $db->execute([$user_class->id, $bpCategory['id']]);
    $r = $db->fetch_row(true);
    if (isset($r['id'])) {
        return $r;
    }

    $db->query("INSERT INTO bp_category_user (bp_category_id, user_id) VALUES (?, ?)");
    $db->execute([$bpCategory['id'], $user_class->id]);
    return getBpCategoryUser($bpCategory['id'], $user_class);
}

function addToBpCategoryUser($bpCategory, $user_class, $field, $qty = 1)
{
    global $db;

    $data = getBpCategoryUser($bpCategory, $user_class);

    $db->query("UPDATE bp_category_user SET {$field} = {$field} + {$qty} WHERE id = " . $data['id']);
    $db->execute();
}

function getActiveGangTerritoryZoneBattle($gangTerritoryZone)
{
    global $db;

    $db->query("SELECT * FROM gang_territory_zone_battle WHERE (is_complete IS NULL OR is_complete = 0) AND gang_territory_zone_id = " . $gangTerritoryZone['id'] . " LIMIT 1");
    $db->execute();

    return $db->fetch_row(true);
}

function getTimeRemainingForDisplay($time)
{
    $time = $time - time();

    return number_format(($time / 60), 0) . ' minutes until battle';
}

function getAttackDamageOLD($attacker, $defender)
{
    $criticalHit = 1;

    $logStrength = log($attacker->moddedstrength);
    $logDefence = log($defender->moddeddefense);

    $maxDamage = ceil($logStrength / $logDefence * 15000);

    if ($logStrength > $logDefence) {
        $damMinPerc = 70;
        $damMaxPerc = 85;
    } else if ($logDefence > $logStrength) {
        $damMinPerc = 20;
        $damMaxPerc = 30;
    } else {
        $damMinPerc = 40;
        $damMaxPerc = 50;
    }

    // Critical Hit
    if (mt_rand(1, 100) <= $criticalHit) {
        return array(
            'damage' => $maxDamage,
            'is_critical_hit' => true
        );
    }

    $lowMaxDamage = $maxDamage / 100 * $damMinPerc;
    $highMaxDamage = $maxDamage / 100 * $damMaxPerc;

    return array(
        'damage' => mt_rand($lowMaxDamage, $highMaxDamage),
        'is_critical_hit' => false
    );
}

function getAttackDamage($attacker, $defender)
{
    $criticalHit = 1;

    $maxDamage = $attacker->moddedstrength / $defender->moddeddefense;

    $maxDamage = $maxDamage * 15000;
    if ($maxDamage < 100) {
        $maxDamage = 100;
    }
    if ($maxDamage > 40000) {
        $maxDamage = 40000;
    }
    $maxDamage = ceil($maxDamage);

    $damMinPerc = 70;
    $damMaxPerc = 90;

    // Critical Hit
    if (mt_rand(1, 100) <= $criticalHit) {
        return array(
            'damage' => $maxDamage,
            'is_critical_hit' => true
        );
    }

    $lowMaxDamage = $maxDamage / 100 * $damMinPerc;
    $highMaxDamage = $maxDamage / 100 * $damMaxPerc;

    return array(
        'damage' => mt_rand($lowMaxDamage, $highMaxDamage),
        'is_critical_hit' => false
    );
}

function addToUserOperations($user_class, $field, $qty = 1)
{
    global $db;

    $db->query("SELECT * FROM `user_operations` WHERE `user_id` = ? AND (`is_complete` = 0 OR `is_complete` IS NULL) AND (`is_skipped` = 0 OR `is_skipped` IS NULL) ORDER BY `id` DESC LIMIT 1");
    $db->execute(array($user_class->id));
    $currentUserOperation = $db->fetch_row(true);

    if ($currentUserOperation) {
        $db->query("SELECT * FROM operations WHERE id = " . $currentUserOperation['operations_id'] . " LIMIT 1");
        $db->execute();
        $currentOperation = $db->fetch_row(true);

        $db->query("UPDATE user_operations SET {$field} = {$field} + {$qty} WHERE id = " . $currentUserOperation['id']);
        $db->execute();

        $currentUserOperation[$field] = $currentUserOperation[$field] + $qty;

        if ($currentOperation[$field] > 0 && $currentUserOperation[$field] >= $currentOperation[$field]) {
            $newMoney = $user_class->money;
            $newExp = $user_class->exp;
            $newPoints = $user_class->points;

            $message = 'You have successfully completed your operation and earned ';
            if ($currentOperation['money_reward']) {
                $h = true;
                $newMoney = $newMoney + $currentOperation['money_reward'];

                $message .= '$' . number_format($currentOperation['money_reward']);
            }

            if ($currentOperation['points_reward']) {
                $newPoints = $newPoints + $currentOperation['points_reward'];

                if (isset($h) && $h) {
                    $message .= ' & ';
                }
                $message .= '' . number_format($currentOperation['points_reward']) . ' points';
            }


            if ($currentOperation['exp_reward']) {
                $expReward = ($user_class->maxexp / 100) * $currentOperation['exp_reward'];
                $newExp = $newExp + $expReward;

                $message .= ' & ' . number_format($expReward) . ' EXP';
            }

            $db->query("UPDATE grpgusers SET money = {$newMoney}, exp = {$newExp}, points = {$newPoints} WHERE id = " . $user_class->id);
            $db->execute();

            $db->query("UPDATE user_operations SET is_complete = 1 WHERE id = " . $currentUserOperation['id']);
            $db->execute();

            Send_Event($user_class->id, $message);
        }

    }
}

function getCurrentQuestSeasonForUser($userId)
{
    global $db, $cache;

    $cacheKey = "questSeasonForUser:" . $userId;
    $cached = $cache->get($cacheKey);
    if ($cached !== null) {
        $decoded = json_decode($cached, true);
        if (is_array($decoded) && isset($decoded['id'])) {
            return $decoded;
        }
    }

    $db->query("SELECT * FROM quest_season_user WHERE user_id = ? AND (is_complete IS NULL OR is_complete = 0) ORDER BY quest_season_id DESC LIMIT 1");
    $db->execute([$userId]);
    $questSeasonUser = $db->fetch_row(true);

    $questSeasonId = null;
    if ($questSeasonUser && isset($questSeasonUser['id'])) {
        $questSeasonId = $questSeasonUser['quest_season_id'];
    }

    if (!$questSeasonId) {
        $db->query("SELECT * FROM quest_season_user WHERE user_id = ? AND is_complete = 1 ORDER BY quest_season_id DESC LIMIT 1");
        $db->execute([$userId]);
        $questSeasonUser = $db->fetch_row(true);

        if ($questSeasonUser && isset($questSeasonUser['id'])) {
            $questSeasonId = $questSeasonUser['quest_season_id'] + 1;
        }
    }

    if (!$questSeasonId) {
        $questSeasonId = 1;
    }

    if ($userId == 1059) {
        $db->query("SELECT * FROM quest_season WHERE id = ? LIMIT 1");
        $db->execute([$questSeasonId]);
    } else {
        $db->query("SELECT * FROM quest_season WHERE id = ? AND is_active > 0 LIMIT 1");
        $db->execute([$questSeasonId]);
    }
    $questSeason = $db->fetch_row(true);

    if (!$questSeason) {
        return [];
    }

    $cache->setEx($cacheKey, 360, json_encode($questSeason));

    return $questSeason;
}
function getNextQuestSeason($userId, $isAdmin = 0)
{
    global $db, $cache;

    $cacheKey = "nextQuestSeason:{$userId}:{$isAdmin}";
    $questSeason = $cache->get($cacheKey);
    if ($questSeason !== null) {
        return json_decode($questSeason, true);
    }

    $db->query("SELECT * FROM quest_season_user WHERE user_id = ? AND is_complete = 1 ORDER BY quest_season_id DESC LIMIT 1");
    $db->execute([$userId]);
    $questSeasonUser = $db->fetch_row(true);

    if ($questSeasonUser && isset($questSeasonUser['id'])) {
        $questSeasonId = $questSeasonUser['quest_season_id'] + 1;
    } else {
        $questSeasonId = 1;
    }

    if ($isAdmin) {
        $db->query("SELECT * FROM quest_season WHERE id = ? LIMIT 1");
    } else {
        $db->query("SELECT * FROM quest_season WHERE id = ? AND is_active > 0 LIMIT 1");
    }
    $db->execute([$questSeasonId]);

    $questSeason = $db->fetch_row(true);
    $cache->setEx($cacheKey, 300, json_encode($questSeason));

    return $questSeason;
}

function getQuestSeasonUser($userId, $questSeasonId)
{
    global $db, $cache;

    $cacheKey = "questSeasonUser:{$userId}:{$questSeasonId}";

    $cached = $cache->get($cacheKey);
    if ($cached !== null) {
        $decoded = json_decode($cached, true);
        if (is_array($decoded) && isset($decoded['id'])) {
            return $decoded;
        }
        // fall through if cache is invalid/bad
    }

    // Try select first
    $db->query("SELECT * FROM quest_season_user WHERE user_id = ? AND quest_season_id = ? LIMIT 1");
    $db->execute([$userId, $questSeasonId]);
    $r = $db->fetch_row(true);

    if ($r && isset($r['id'])) {
        $cache->setEx($cacheKey, 300, json_encode($r));
        return $r;
    }

    // Insert with ON DUPLICATE KEY to guarantee row exists
    $db->query("INSERT INTO quest_season_user (user_id, quest_season_id) VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE quest_season_id = VALUES(quest_season_id)");
    $db->execute([$userId, $questSeasonId]);

    // Re-select
    $db->query("SELECT * FROM quest_season_user WHERE user_id = ? AND quest_season_id = ? LIMIT 1");
    $db->execute([$userId, $questSeasonId]);
    $r = $db->fetch_row(true);

    if ($r && isset($r['id'])) {
        $cache->setEx($cacheKey, 300, json_encode($r));
        return $r;
    }

    // Final safeguard: always return at least a minimal struct
    return [
        'id' => null,
        'user_id' => $userId,
        'quest_season_id' => $questSeasonId,
        'is_complete' => 0,
    ];
}


function getQuestSeasonMissionUser($userId, $questSeasonId)
{
    global $db, $cache;

    $cacheKey = "questSeasonMissionUser:{$userId}:{$questSeasonId}";
    $cached = $cache->get($cacheKey);
    if ($cached !== null) {
        $decoded = json_decode($cached, true);
        if (is_array($decoded) && isset($decoded['id'])) {
            return $decoded;
        }
    }

    // Try to get active mission
    $db->query("SELECT * FROM quest_season_mission_user 
                WHERE user_id = ? AND quest_season_id = ? 
                AND (is_complete IS NULL OR is_complete = 0) 
                ORDER BY id DESC LIMIT 1");
    $db->execute([$userId, $questSeasonId]);
    $questSeasonMissionUser = $db->fetch_row(true);

    if ($questSeasonMissionUser && isset($questSeasonMissionUser['id'])) {
        if (isset($questSeasonMissionUser['progress'])) {
            $questSeasonMissionUser['progress'] = json_decode($questSeasonMissionUser['progress'], true) ?: [];
        }
        $cache->setEx($cacheKey, 120, json_encode($questSeasonMissionUser));
        return $questSeasonMissionUser;
    }

    // Try last completed
    $db->query("SELECT * FROM quest_season_mission_user 
                WHERE user_id = ? AND quest_season_id = ? 
                AND is_complete = 1 
                ORDER BY id DESC LIMIT 1");
    $db->execute([$userId, $questSeasonId]);
    $questSeasonMissionUser = $db->fetch_row(true);

    if ($questSeasonMissionUser && isset($questSeasonMissionUser['id'])) {
        if (isset($questSeasonMissionUser['progress'])) {
            $questSeasonMissionUser['progress'] = json_decode($questSeasonMissionUser['progress'], true) ?: [];
        }
        $cache->setEx($cacheKey, 120, json_encode($questSeasonMissionUser));
        return $questSeasonMissionUser;
    }

    // Otherwise: assign first mission for season
    $db->query("SELECT * FROM quest_season_mission 
                WHERE quest_season_id = ? 
                ORDER BY id ASC LIMIT 1");
    $db->execute([$questSeasonId]);
    $questSeasonMission = $db->fetch_row(true);

    if ($questSeasonMission && isset($questSeasonMission['id'])) {
        $progress = [];
        $questSeasonMission['requirements'] = json_decode($questSeasonMission['requirements'], true) ?: [];
        foreach ($questSeasonMission['requirements'] as $key => $req) {
            $progress[$key] = 0;
        }

        $db->query("INSERT INTO quest_season_mission_user (user_id, quest_season_id, quest_season_mission_id, progress) 
                    VALUES (?, ?, ?, ?)");
        $db->execute([$userId, $questSeasonId, $questSeasonMission['id'], json_encode($progress)]);

        $db->query("SELECT * FROM quest_season_mission_user 
                    WHERE user_id = ? AND quest_season_id = ? 
                    ORDER BY id DESC LIMIT 1");
        $db->execute([$userId, $questSeasonId]);
        $r = $db->fetch_row(true);
        if ($r && isset($r['progress'])) {
            $r['progress'] = json_decode($r['progress'], true) ?: [];
        }

        if ($r && isset($r['id'])) {
            $cache->setEx($cacheKey, 120, json_encode($r));
            return $r;
        }
    }

    return null;
}

function getQuestSeasonMission($userId, $questSeasonId)
{
    global $db, $cache;

    $questSeasonMissionUser = getQuestSeasonMissionUser($userId, $questSeasonId);
    if (!$questSeasonMissionUser || empty($questSeasonMissionUser['quest_season_mission_id'])) {
        return null;
    }

    $missionId = (int) $questSeasonMissionUser['quest_season_mission_id'];
    $cacheKey = "questSeasonMission:{$missionId}";

    $cached = $cache->get($cacheKey);
    if ($cached !== null) {
        $decoded = json_decode($cached, true);
        if (is_array($decoded) && isset($decoded['id'])) {
            return $decoded;
        }
    }

    $db->query("SELECT * FROM quest_season_mission WHERE id = ? LIMIT 1");
    $db->execute([$missionId]);
    $questSeasonMission = $db->fetch_row(true);

    if ($questSeasonMission && isset($questSeasonMission['id'])) {
        $questSeasonMission['requirements'] = json_decode($questSeasonMission['requirements'], true) ?: [];
        $cache->setEx($cacheKey, 86400, json_encode($questSeasonMission));
        return $questSeasonMission;
    }

    return null;
}


function getDisplayForQuestReq($req, $num, $progress)
{
    if (is_string($progress)) {
        $progress = json_decode($progress, true);
    } else {
        $progress = (array) $progress;
    }

    if (isset($progress[$req]) && $progress[$req] >= $num) {
        $status = ' <span style="color: green;">(Complete)</span>';
    } else {
        $status = ' <span style="color: red;">(Incomplete)</span>';
    }

    if ($req === 'vinny_the_fish_delivery') {
        return 'Deliver the package to Vinny The Fish' . $status;
    } else if ($req === 'pharmacy_protection') {
        return 'Get Marco at the pharmacy to pay up' . $status;
    } else if ($req === 'follow_salvatore') {
        return 'Follow Salvatore' . $status;
    } else if ($req === 'interrogate_phil') {
        return 'Kidnap and interrogate Phil' . $status;
    } else if ($req === 'steal_books') {
        return 'Steal the books' . $status;
    } else if ($req === 'attack_player') {
        return 'Attack Player: ' . formatName($num) . $status;
    } else if ($req === 'attacks') {
        return 'Attack: ' . number_format($progress[$req]) . '/' . $num . ' ' . $status;
    } else if ($req === 'whitecollar_fraud') {
        return 'Whitecollar fraud: ' . number_format($progress[$req]) . '/' . $num . ' ' . $status;
    } else if ($req === 'mastermind_ops') {
        return 'Mastermind operations' . $status;
    } else if ($req === 'crime_cash') {
        if (isset($progress[$req])) {
            return 'Cash from crimes: $' . number_format($progress[$req]) . '/$' . number_format($num) . $status;
        }
        return 'Cash from crimes: $0/$' . number_format($num) . $status;
    } else if ($req === 'backalley') {
        if (isset($progress[$req])) {
            return 'Backalley searches: ' . number_format($progress[$req]) . '/' . number_format($num) . $status;
        }
        return 'Backalley Searches: 0/' . number_format($num) . $status;
    } else if ($req === 'raids') {
        if (isset($progress[$req])) {
            return 'Raids: ' . number_format($progress[$req]) . '/' . number_format($num) . $status;
        }
        return 'Raids: 0/' . number_format($num) . $status;
    } else if ($req === 'city_goons') {
        if (isset($progress[$req])) {
            return 'City goons: ' . number_format($progress[$req]) . '/' . number_format($num) . $status;
        }
        return 'City Goons: 0/' . number_format($num) . $status;
    } else if ($req === 'busts') {
        if (isset($progress[$req])) {
            return 'Busts: ' . number_format($progress[$req]) . '/' . number_format($num) . $status;
        }
        return 'Busts: 0/' . number_format($num) . $status;
    } else if ($req === 'maze') {
        if (isset($progress[$req])) {
            return 'Maze: ' . number_format($progress[$req]) . '/' . number_format($num) . $status;
        }
        return 'Busts: 0/' . number_format($num) . $status;
    } else {
        return '<strong>' . $req . ': ' . number_format($num) . '</strong>' . $status;
    }
}

function updateQuestSeasonMissionUserProgress($questSeasonMissionUser, $req, $value)
{
    global $db, $cache;

    $db->query("SELECT * FROM quest_season_mission WHERE id = ? LIMIT 1");
    $db->execute([$questSeasonMissionUser['quest_season_mission_id']]);
    $questSeasonMission = $db->fetch_row(true);

    if ($questSeasonMission) {
        $isComplete = false;

        $progress = is_string($questSeasonMissionUser['progress'])
            ? json_decode($questSeasonMissionUser['progress'], true)
            : (array) $questSeasonMissionUser['progress'];

        if (!is_array($progress)) {
            $progress = [];
        }

        $progress[$req] = ($progress[$req] ?? 0) + (int) $value;
        $questSeasonMissionUser['progress'] = $progress;

        $requirements = json_decode($questSeasonMission['requirements'], true);
        foreach ($requirements as $key => $r) {
            if (($progress[$key] ?? 0) < $r) {
                $isComplete = false;
                break;
            }
            $isComplete = true;
        }

        if ($isComplete && !$questSeasonMissionUser['is_complete']) {
            Send_Event(
                $questSeasonMissionUser['user_id'],
                'You have completed the mission: ' . $questSeasonMission['name'] . '! <a href="quest.php">Click here to claim your reward</a>'
            );
        }

        $db->query("UPDATE quest_season_mission_user SET progress = ?, is_complete = ? WHERE id = ?");
        $db->execute([json_encode($progress), $isComplete, $questSeasonMissionUser['id']]);

        $questSeasonMissionUser['is_complete'] = (int) $isComplete;
        $cache->setEx(
            "questSeasonMissionUser:{$questSeasonMissionUser['user_id']}:{$questSeasonMissionUser['quest_season_id']}",
            300,
            json_encode($questSeasonMissionUser)
        );
    }

    return $questSeasonMissionUser;
}

function generateGradientName($user_id)
{
    // Fetch user settings from the database based on the user_id
    global $db;
    $db->query("SELECT start_color, end_color, is_bold, is_italic, glow, u.username 
                FROM user_gradients ug
                JOIN grpgusers u ON u.id = ug.user_id
                WHERE ug.user_id = ? AND u.gndays > 0");
    $db->execute([$user_id]);
    $settings = $db->fetch_row(true);

    // If settings are found, generate the gradient text
    if ($settings) {
        $startColor = $settings['start_color'];
        $endColor = $settings['end_color'];
        $username = $settings['username'];
        $isBold = $settings['is_bold'];
        $isItalic = $settings['is_italic'];
        $glow = $settings['glow'];

        // Generate the gradient colors for each letter of the username
        $gradientColors = generateGradient($startColor, $endColor, strlen($username));
        $gradientText = "";

        // Loop through each character of the username and apply the gradient
        for ($i = 0; $i < strlen($username); $i++) {
            $gradientText .= "<span style=\"color: {$gradientColors[$i]};" .
                ($glow ? " text-shadow: 0 0 10px {$gradientColors[$i]}" : '') .
                "; font-size:22px; font-weight: " . ($isBold ? "bold" : "normal") . "; font-style: " . ($isItalic ? "italic" : "normal") . ";\">" .
                $username[$i] . "</span>";
        }

        return $gradientText;
    }

}

// Function to generate a gradient between two colors for each letter
function generateGradient($startColor, $endColor, $length)
{
    $start = hexToRgb($startColor);
    $end = hexToRgb($endColor);

    $gradientColors = [];
    $stepR = ($end['r'] - $start['r']) / ($length - 1);
    $stepG = ($end['g'] - $start['g']) / ($length - 1);
    $stepB = ($end['b'] - $start['b']) / ($length - 1);

    for ($i = 0; $i < $length; $i++) {
        $r = round($start['r'] + $stepR * $i);
        $g = round($start['g'] + $stepG * $i);
        $b = round($start['b'] + $stepB * $i);

        $gradientColors[] = rgbToHex($r, $g, $b);
    }

    return $gradientColors;
}

// Helper functions for color conversion
function hexToRgb($hex)
{
    $r = hexdec(substr($hex, 1, 2));
    $g = hexdec(substr($hex, 3, 2));
    $b = hexdec(substr($hex, 5, 2));

    return ['r' => $r, 'g' => $g, 'b' => $b];
}

function rgbToHex($r, $g, $b)
{
    return "#" . str_pad(dechex($r), 2, "0", STR_PAD_LEFT) .
        str_pad(dechex($g), 2, "0", STR_PAD_LEFT) .
        str_pad(dechex($b), 2, "0", STR_PAD_LEFT);
}

function getUserSantasGrotto($userId)
{
    global $db;

    $db->query("SELECT * FROM user_santas_grotto WHERE user_id = " . $userId . " LIMIT 1");
    $db->execute();

    $r = $db->fetch_row(true);
    if (isset($r['id'])) {
        return $r;
    } else {
        $db->query("INSERT INTO user_santas_grotto (user_id) VALUES (" . $userId . ")");
        $db->execute();
        $r = getUserSantasGrotto($userId);

        return $r;
    }

    return $db->fetch_row(true);
}

function payoutChristmasGift($userId)
{
    //    global $db;
//
//    $userSantasGrotto = getUserSantasGrotto($userId);
//
//    if ($userSantasGrotto['todays_gifts_found'] < 12) {
//        $findChance = mt_rand(1,10000);
//
//        if ($findChance <= 10) {
//            $db->query("UPDATE user_santas_grotto SET todays_gifts_found = todays_gifts_found + 1 WHERE user_id = " . $userId);
//            $db->execute();
//
//            Give_Item(295, $userId);
//
//            Send_Event($userId, 'You found a Christmas Gift!');
//        }
//    }
}

function getPetladder($petId)
{
    global $db;

    $db->query("SELECT * FROM petladder WHERE pet_id = " . $petId . " LIMIT 1");
    $db->execute();
    $r = $db->fetch_row();

    if (isset($r[0]['id'])) {
        return $r[0];
    } else {
        $db->query("INSERT INTO petladder (pet_id) VALUES (" . $petId . ")");
        $db->execute();
        $r = getPetladder($petId);

        return $r;
    }
}

function addToPetladder($petId, $field, $qty = 1)
{
    global $db;

    $petladder = getPetladder($petId);

    $db->query("UPDATE petladder SET {$field} = {$field} + {$qty} WHERE id = " . $petladder['id']);
    $db->execute();
}

function getMissionBadges()
{
    $badges = array(
        '13' => array(
            'needed' => 25000,
            'payout' => 150000,
            'img' => 'missions25k',
            'title' => 'Mission Master: Complete 25000 Missions'
        ),
        '12' => array(
            'needed' => 20000,
            'payout' => 125000,
            'img' => 'missions20k',
            'title' => 'Mission Master: Complete 20000 Missions'
        ),
        '11' => array(
            'needed' => 15000,
            'payout' => 100000,
            'img' => 'missions15k',
            'title' => 'Mission Master: Complete 15000 Missions'
        ),
        '10' => array(
            'needed' => 10000,
            'payout' => 70000,
            'img' => 'missions10k',
            'title' => 'Mission Master: Complete 10000 Missions'
        ),
        '9' => array(
            'needed' => 7500,
            'payout' => 50000,
            'img' => 'missions7.5k',
            'title' => 'Mission Master: Complete 7500 Missions'
        ),
        '8' => array(
            'needed' => 5000,
            'payout' => 30000,
            'img' => 'missions5k',
            'title' => 'Mission Master: Complete 5000 Missions'
        ),
        '7' => array(
            'needed' => 2500,
            'payout' => 20000,
            'img' => 'missions2.5k',
            'title' => 'Mission Master: Complete 2500 Missions'
        ),
        '6' => array(
            'needed' => 1000,
            'payout' => 15000,
            'img' => 'missions1k',
            'title' => 'Mission Master: Complete 1000 Missions'
        ),
        '5' => array(
            'needed' => 500,
            'payout' => 10000,
            'img' => 'missions500',
            'title' => 'Mission Master: Complete 500 Missions'
        ),
        '4' => array(
            'needed' => 250,
            'payout' => 5000,
            'img' => 'missions250',
            'title' => 'Mission Master: Complete 250 Missions'
        ),
        '3' => array(
            'needed' => 100,
            'payout' => 3000,
            'img' => 'missions100',
            'title' => 'Mission Master: Complete 100 Missions'
        ),
        '2' => array(
            'needed' => 50,
            'payout' => 2000,
            'img' => 'missions50',
            'title' => 'Mission Master: Complete 50 Missions'
        ),
        '1' => array(
            'needed' => 10,
            'payout' => 1000,
            'img' => 'missions10',
            'title' => 'Mission Master: Complete 10 Missions'
        )
    );

    return $badges;
}

function getRaidBadges()
{
    $badges = array(
        '11' => array(
            'needed' => 150000,
            'payout' => 175000,
            'img' => 'raids150k',
            'title' => 'Raid Master: Complete 150,000 Raids'
        ),
        '10' => array(
            'needed' => 125000,
            'payout' => 150000,
            'img' => 'raids125k',
            'title' => 'Raid Master: Complete 125,000 Raids'
        ),
        '9' => array(
            'needed' => 100000,
            'payout' => 125000,
            'img' => 'raids100k',
            'title' => 'Raid Master: Complete 100,000 Raids'
        ),
        '8' => array(
            'needed' => 75000,
            'payout' => 100000,
            'img' => 'raids75k',
            'title' => 'Raid Master: Complete 75,000 Raids'
        ),
        '7' => array(
            'needed' => 50000,
            'payout' => 75000,
            'img' => 'raids50k',
            'title' => 'Raid Master: Complete 50,000 Raids'
        ),
        '6' => array(
            'needed' => 25000,
            'payout' => 30000,
            'img' => 'raids25k',
            'title' => 'Raid Master: Complete 25,000 Raids'
        ),
        '5' => array(
            'needed' => 10000,
            'payout' => 10000,
            'img' => 'raids10k',
            'title' => 'Raid Master: Complete 10,000 Raids'
        ),
        '4' => array(
            'needed' => 5000,
            'payout' => 5000,
            'img' => 'raids5k',
            'title' => 'Raid Master: Complete 5,000 Raids'
        ),
        '3' => array(
            'needed' => 1000,
            'payout' => 3000,
            'img' => 'raids1k',
            'title' => 'Raid Master: Complete 1,000 Raids'
        ),
        '2' => array(
            'needed' => 100,
            'payout' => 2000,
            'img' => 'raids100',
            'title' => 'Raid Master: Complete 100 Raids'
        ),
        '1' => array(
            'needed' => 10,
            'payout' => 1000,
            'img' => 'raids10',
            'title' => 'Raid Master: Complete 10 Raids'
        )
    );

    return $badges;
}

function getRacketBadges()
{
    $badges = array(
        '13' => array(
            'needed' => 500,
            'payout' => 180000,
            'img' => 'rackets500',
            'title' => 'Protection Racket Master: Win 500 Protection Racket Battles'
        ),
        '12' => array(
            'needed' => 400,
            'payout' => 160000,
            'img' => 'rackets400',
            'title' => 'Protection Racket Master: Win 400 Protection Racket Battles'
        ),
        '11' => array(
            'needed' => 300,
            'payout' => 140000,
            'img' => 'rackets300',
            'title' => 'Protection Racket Master: Win 300 Protection Racket Battles'
        ),
        '10' => array(
            'needed' => 250,
            'payout' => 120000,
            'img' => 'rackets250',
            'title' => 'Protection Racket Master: Win 250 Protection Racket Battles'
        ),
        '9' => array(
            'needed' => 200,
            'payout' => 100000,
            'img' => 'rackets200',
            'title' => 'Protection Racket Master: Win 200 Protection Racket Battles'
        ),
        '8' => array(
            'needed' => 150,
            'payout' => 80000,
            'img' => 'rackets150',
            'title' => 'Protection Racket Master: Win 150 Protection Racket Battles'
        ),
        '7' => array(
            'needed' => 100,
            'payout' => 60000,
            'img' => 'rackets100',
            'title' => 'Protection Racket Master: Win 100 Protection Racket Battles'
        ),
        '6' => array(
            'needed' => 75,
            'payout' => 40000,
            'img' => 'rackets75',
            'title' => 'Protection Racket Master: Win 75 Protection Racket Battles'
        ),
        '5' => array(
            'needed' => 50,
            'payout' => 20000,
            'img' => 'rackets50',
            'title' => 'Protection Racket Master: Win 50 Protection Racket Battles'
        ),
        '4' => array(
            'needed' => 25,
            'payout' => 10000,
            'img' => 'rackets25',
            'title' => 'Protection Racket Master: Win 25 Protection Racket Battles'
        ),
        '3' => array(
            'needed' => 15,
            'payout' => 5000,
            'img' => 'rackets15',
            'title' => 'Protection Racket Master: Win 15 Protection Racket Battles'
        ),
        '2' => array(
            'needed' => 10,
            'payout' => 2000,
            'img' => 'rackets10',
            'title' => 'Protection Racket Master: Win 10 Protection Racket Battles'
        ),
        '1' => array(
            'needed' => 5,
            'payout' => 1000,
            'img' => 'rackets5',
            'title' => 'Protection Racket Master: Win 5 Protection Racket Battles'
        )
    );

    return $badges;
}

function getBackalleyBadges()
{
    $badges = array(
        '15' => array(
            'needed' => 3000000,
            'payout' => 350000,
            'img' => 'backalley3m',
            'title' => 'Backalley Master: Win 3,000,000 Backalley Wins'
        ),
        '14' => array(
            'needed' => 2750000,
            'payout' => 300000,
            'img' => 'backalley2_75m',
            'title' => 'Backalley Master: Win 2,750,000 Backalley Wins'
        ),
        '13' => array(
            'needed' => 2500000,
            'payout' => 275000,
            'img' => 'backalley2_5m',
            'title' => 'Backalley Master: Win 2,500,000 Backalley Wins'
        ),
        '12' => array(
            'needed' => 2250000,
            'payout' => 250000,
            'img' => 'backalley2_25m',
            'title' => 'Backalley Master: Win 2,250,000 Backalley Wins'
        ),
        '11' => array(
            'needed' => 2000000,
            'payout' => 200000,
            'img' => 'backalley2m',
            'title' => 'Backalley Master: Win 2,000,000 Backalley Wins'
        ),
        '10' => array(
            'needed' => 1500000,
            'payout' => 175000,
            'img' => 'backalley1_5m',
            'title' => 'Backalley Master: Win 1,500,000 Backalley Wins'
        ),
        '9' => array(
            'needed' => 1000000,
            'payout' => 150000,
            'img' => 'backalley1m',
            'title' => 'Backalley Master: Win 1,000,000 Backalley Wins'
        ),
        '8' => array(
            'needed' => 750000,
            'payout' => 125000,
            'img' => 'backalley750k',
            'title' => 'Backalley Master: Win 750,000 Backalley Wins'
        ),
        '7' => array(
            'needed' => 500000,
            'payout' => 100000,
            'img' => 'backalley500k',
            'title' => 'Backalley Master: Win 500,000 Backalley Wins'
        ),
        '6' => array(
            'needed' => 250000,
            'payout' => 70000,
            'img' => 'backalley250k',
            'title' => 'Backalley Master: Win 250,000 Backalley Wins'
        ),
        '5' => array(
            'needed' => 100000,
            'payout' => 50000,
            'img' => 'backalley100k',
            'title' => 'Backalley Master: Win 100,000 Backalley Wins'
        ),
        '4' => array(
            'needed' => 25000,
            'payout' => 35000,
            'img' => 'backalley25k',
            'title' => 'Backalley Master: Win 25,000 Backalley Wins'
        ),
        '3' => array(
            'needed' => 10000,
            'payout' => 20000,
            'img' => 'backalley10k',
            'title' => 'Backalley Master: Win 10,000 Backalley Wins'
        ),
        '2' => array(
            'needed' => 5000,
            'payout' => 10000,
            'img' => 'backalley5k',
            'title' => 'Backalley Master: Win 5,000 Backalley Wins'
        ),
        '1' => array(
            'needed' => 1000,
            'payout' => 5000,
            'img' => 'backalley1k',
            'title' => 'Backalley Master: Win 1,000 Backalley Wins'
        )
    );

    return $badges;
}


/**
 * (0 -> DIAMOND, 1 -> SAPPHIRE, 2 -> EMERALD, 3 -> RUBY)
 * (50, 30, 15, 5)
 * 
 * ---
 * 
 * COMMON GEM BAG (10-15 GEMS):
 * 1st RNG -> Amount of gems: [10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20)
 * 
 * 2nd RNG -> [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 3] -> Index from 1 to 20, the higher 
 * the number the better the gem, repeat for #amount of gems#
 * 
 * (0 -> Common, 1 -> Uncommon)
 * (75, 25)
 * 3rd RNG -> [0, 0, 0, 1] -> Index from 1 to 4 to decide rarity of gem, repeat for each gem
 * 
 * ---
 * 
 * RARE GEM BAG (10-20 GEMS):
 * 1st RNG -> Amount of gems: [10, 11, 12, 13, 14, 15)
 * 
 * 2nd RNG -> [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 3] -> Index from 1 to 20, the higher 
 * the number the better the gem, repeat for #amount of gems#
 * 
 * (0 -> Common, 1 -> Uncommon, 2 -> Rare)
 * (50, 35, 15)
 * 3rd RNG -> [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1, 2, 2, 2] -> Index from 1 to 20 to decide rarity of gem, repeat for each gem
 * 
 * ---
 * 
 * ULTRA RARE GEM BAG (10-20 GEMS):
 * 1st RNG -> Amount of gems: [10, 11, 12, 13, 14, 15)
 * 
 * 2nd RNG -> [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 3] -> Index from 1 to 20, the higher 
 * the number the better the gem, repeat for #amount of gems#
 * 
 * (0 -> Uncommon, 1 -> Rare, 2 -> Ultra Rare)
 * (50, 40, 10)
 * 3rd RNG -> [0, 0, 0, 0, 0, 1, 1, 1, 1, 2] -> Index from 1 to 10 to decide rarity of gem, repeat for each gem
 */
function get_gem_ids()
{
    return [
        // COMMON
        [
            209, // Common Diamond
            212, // Common Sapphire
            211, // Common Emerald
            210, // Common Ruby
        ],
        // UNCOMMON
        [
            225, // Uncommon Diamond
            228, // Uncommon Sapphire
            227, // Uncommon Emerald
            226, // Uncommon Ruby
        ],
        // RARE
        [
            242, // Rare Diamond
            245, // Rare Sapphire
            244, // Rare Emerald
            243, // Rare Ruby
        ],
        // ULTRA RARE
        [
            246, // Ultra Rare Diamond
            249, // Ultra Rare Sapphire
            248, // Ultra Rare Emerald
            247, // Ultra Rare Ruby
        ],
    ];
}

function get_gem_name_from_id($id)
{
    switch ($id) {
        case 209:
            return 'Common Diamond';
        case 212:
            return 'Common Sapphire';
        case 211:
            return 'Common Emerald';
        case 210:
            return 'Common Ruby';
        case 225:
            return 'Uncommon Diamond';
        case 228:
            return 'Uncommon Sapphire';
        case 227:
            return 'Uncommon Emerald';
        case 226:
            return 'Uncommon Ruby';
        case 242:
            return 'Rare Diamond';
        case 245:
            return 'Rare Sapphire';
        case 244:
            return 'Rare Emerald';
        case 243:
            return 'Rare Ruby';
        case 246:
            return 'Ultra Rare Diamond';
        case 249:
            return 'Ultra Rare Sapphire';
        case 248:
            return 'Ultra Rare Emerald';
        case 247:
            return 'Ultra Rare Ruby';
    }
}

function open_gem_bag($quality_array = [])
{
    global $user_class;

    $gem_ids = get_gem_ids();

    $amount_rng = mt_rand(10, 15);
    $gem_type_array = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 2, 2, 2, 3];

    $rewards = [];
    for ($i = 0; $i < $amount_rng; $i++) {
        // Find the gem type (Diamond, Sapphire, Emerald, Ruby)
        $type_rng = mt_rand(0, 19);
        $gem_type = $gem_type_array[$type_rng];

        // Find the gem quality
        $quality_rng = mt_rand(0, count($quality_array) - 1);
        $quality = $quality_array[$quality_rng];

        // Find the gem id
        $rewards[] = $gem_ids[$quality][$gem_type];
    }

    foreach ($rewards as $id) {
        Give_Item($id, $user_class->id, 1);
    }

    return $rewards;
}
// Function to format a number with abbreviations
// e.g., 1,000 -> 1K, 1,000,000 -> 1M, etc.
// This function will round the number to one decimal place and append the appropriate abbreviation.
// 
function pretty_format_number($value)
{
    $abbreviations = array(21 => 'Sx', 18 => 'Qi', 15 => 'Qa', 12 => 'T', 9 => 'B', 6 => 'M', 3 => 'K', 0 => '');
    foreach ($abbreviations as $exponent => $abbreviation) {
        if ($value >= pow(10, $exponent)) {
            return round(floatval($value / pow(10, $exponent)), 1) . $abbreviation;
        }
    }

    return $value;
}

// Requires db and cache to be globally available
function set_last_active($uid)
{
    global $db, $cache;

    $current = time();
    $lastactive = $cache->get('lastactive_' . $uid);
    if (empty($lastactive) || ($current - $lastactive) > 5) {
        $cache->setEx('lastactive_' . $uid, 10, $current);
        perform_query("UPDATE `grpgusers` SET `lastactive` = ? WHERE id = ?", [$current, $uid]);
    }
}

function set_last_active_ip($uid, $ip)
{
    global $db, $cache;

    $current = time();
    $lastactive = $cache->get('lastactive_' . $uid);
    if (!$lastactive || ($current - $lastactive) >= 10) {
        $cache->setEx('lastactive_' . $uid, 10, $current);
        perform_query("UPDATE `grpgusers` SET `lastactive` = ?, ip = ? WHERE id = ?", [$current, $ip, $uid]);
    }
}

function perform_query($query, $params = [])
{
    global $db;

    $db->query($query);
    $db->execute($params);
}

function read_user_for_advertisement($uid, $ttl = 60)
{
    global $db, $cache;

    if ($cache->exists("adv_user_" . $uid)) {
        return json_decode($cache->get("adv_user_" . $uid));
    }

    $db->query("SELECT * FROM grpgusers WHERE id = ?");
    $db->execute([$uid]);
    $user = $db->fetch_row(true);

    $cache->setEx("adv_user_" . $uid, $ttl, json_encode($user));

    return $user;
}

function getForums()
{
    global $db, $cache;

    if ($cache->exists("forums")) {
        return json_decode($cache->get("forums"), true);
    }

    $db->query("SELECT * FROM forums ORDER BY disporder ASC");
    $db->execute();
    $forums = $db->fetch_row();
    $cache->setEx("forums", 3600, json_encode($forums));

    return $forums;
}

function getThread($tid)
{
    global $db;

    $db->query("SELECT * FROM threads WHERE id = ? LIMIT 1");
    $db->execute([$tid]);

    return $db->fetch_row(true);
}

function getThreads($fid, $page = 1)
{
    global $db;

    $offset = ($page - 1) * 20;
    $db->query("SELECT * FROM threads WHERE fid = ? ORDER BY sticky DESC, createdat DESC LIMIT $offset, 20");
    $db->execute([$fid]);

    return $db->fetch_row();
}

function getOwnThreads($fid, $uid, $page = 1)
{
    global $db;

    $offset = ($page - 1) * 20;
    $db->query("SELECT * FROM threads WHERE fid = ? AND uid = ? ORDER BY sticky DESC, createdat DESC LIMIT $offset, 20");
    $db->execute([$fid, $uid]);

    return $db->fetch_row();
}

function getPermissions($fid, $gid)
{
    global $db, $cache;

    if ($cache->exists("forumpermissions_" . $fid . "_" . $gid)) {
        return json_decode($cache->get("forumpermissions_" . $fid . "_" . $gid), true);
    }

    $db->query("SELECT * FROM forumpermissions WHERE fid = ? AND gid = ?");
    $db->execute([$fid, $gid]);
    $permissions = $db->fetch_row(true);

    $cache->setEx("forumpermissions_" . $fid . "_" . $gid, 3600, json_encode($permissions));
    return $permissions;
}

function insertThread($fid, $uid, $subject, $content)
{
    global $db;

    $now = time();

    $db->startTrans();
    $db->query("INSERT INTO threads (fid, uid, subject, createdat, firstpost) VALUES (?, ?, ?, ?, ?)");
    $db->execute([$fid, $uid, $subject, $now, 0]);
    $threadId = $db->insert_id();

    $db->query("INSERT INTO posts (tid, fid, uid, subject, message, createdat) VALUES (?, ?, ?, ?, ?, ?)");
    $db->execute([$threadId, $fid, $uid, $subject, $content, $now]);
    $postId = $db->insert_id();

    $db->query("UPDATE threads SET firstpost = ?, lastpost = ? WHERE id = ?");
    $db->execute([$postId, $postId, $threadId]);

    $db->endTrans();
}

function getPosts($tid, $page = 1)
{
    global $db;

    $offset = ($page - 1) * 20;
    $db->query("SELECT * FROM posts WHERE tid = ? ORDER BY createdat ASC LIMIT $offset, 20");
    $db->execute([$tid]);

    return $db->fetch_row();
}

function getScheduledEvent($type = 'gym')
{
    global $db, $cache;

    $now = time();
    if ($cache->exists("scheduled_event_" . $type)) {
        $event = json_decode($cache->get("scheduled_event_" . $type), true);
        if ($event['start'] <= $now && $event['end'] >= $now) {
            return $event;
        }

        $cache->delete("scheduled_event_" . $type);
    }

    $db->query("SELECT * FROM scheduledevents WHERE type = ? AND start <= ? AND end >= ? LIMIT 1");
    $db->execute([$type, $now, $now]);
    $event = $db->fetch_row(true);
    if ($event) {
        $timetolive = $event['end'] - $now;
        if ($timetolive > 0) {
            $cache->setEx("scheduled_event_" . $type, $timetolive, json_encode($event));
            return $event;
        }
    }

    return null;
}

function getEventsMessage()
{
    $events = getAllScheduledEvents();
    if (empty($events)) {
        return null;
    }

    $now = time();
    $message = "";
    foreach ($events as $event) {
        $timeleft = secondsToTime($event['end'] - $now);
        $message .= "<div class='event-countdown' data-end='{$event['end']}'>" . _eventMessageByType($event['type'], $event['multiplier'], $timeleft) . "</div>";
    }

    return $message;
}

function _eventMessageByType($type, $multiplier, $timeleft)
{
    switch ($type) {
        case 'gym':
            return "Gym event is currently active with a multiplier of <span class='text-warning'>{$multiplier}x</span> for <span class='text-danger countdown-text'>{$timeleft}</span>";
        case 'crime_exp':
            return "Crime EXP event is currently active with a multiplier of <span class='text-warning'>{$multiplier}x</span> for <span class='text-danger countdown-text'>{$timeleft}</span>";
        case 'crime_money':
            return "Crime money event is currently active with a multiplier of <span class='text-warning'>{$multiplier}x</span> for <span class='text-danger countdown-text'>{$timeleft}</span>";
        case 'raid':
            return "Raid event is currently active with a multiplier of <span class='text-warning'>{$multiplier}x</span> for <span class='text-danger countdown-text'>{$timeleft}</span>";
        case 'mission_exp':
            return "Mission EXP event is currently active with a multiplier of <span class='text-warning'>{$multiplier}x</span> for <span class='text-danger countdown-text'>{$timeleft}</span>";
        case 'mission_money':
            return "Mission money event is currently active with a multiplier of <span class='text-warning'>{$multiplier}x</span> for <span class='text-danger countdown-text'>{$timeleft}</span>";
        case 'backalley':
            return "Backalley event is currently active with a multiplier of <span class='text-warning'>{$multiplier}x</span> for <span class='text-danger countdown-text'>{$timeleft}</span>";
        default:
            return "";
    }
}

// Function to start a session if it is not already started
function start_session_guarded()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function get_missions_by_category($category)
{
    global $db, $cache;

    $missions = $cache->get("missions_category_" . $category);
    if (!empty($missions) && $missions) {
        return json_decode($missions, true);
    }

    $db->query("SELECT * FROM mission WHERE category = ? ORDER BY id ASC");
    $db->execute([$category]);
    $missions = $db->fetch_row();
    if (!empty($missions)) {
        $cache->setEx("missions_category_" . $category, 7200, json_encode($missions));
        return $missions;
    }

    return [];
}

function get_current_operation($uid)
{
    global $cache, $db;

    $currentUserOperation = $cache->get("currentUserOperation_" . $uid);
    if (empty($currentUserOperation) || !$currentUserOperation) {
        $db->query("SELECT * FROM `user_operations` WHERE `user_id` = ? AND (`is_complete` = 0 OR `is_complete` IS NULL) AND (`is_skipped` = 0 OR `is_skipped` IS NULL) ORDER BY `id` DESC LIMIT 1");
        $db->execute(array($uid));
        $currentUserOperation = $db->fetch_row(true);

        if (!empty($currentUserOperation)) {
            $cache->setEx("currentUserOperation_" . $uid, 5, json_encode($currentUserOperation));
        }
    } else {
        $currentUserOperation = json_decode($currentUserOperation, true);
    }

    return $currentUserOperation;
}

function get_operation($id)
{
    global $db, $cache;

    $operation = $cache->get("operation_" . $id);
    if (!empty($operation) && $operation) {
        return json_decode($operation, true);
    }

    $db->query("SELECT * FROM operations WHERE id = ?");
    $db->execute([$id]);
    $operation = $db->fetch_row(true);
    if (!empty($operation)) {
        $cache->setEx("operation_" . $id, 3600, json_encode($operation));
        return $operation;
    }

    return null;
}

function get_user_mission($uid)
{
    global $cache, $db;

    $usermission = $cache->get("userMission_" . $uid);
    if (empty($usermission) || !$usermission) {
        $query = $db->query("SELECT * FROM missions WHERE userid = ? AND completed = 'no'");
        $db->execute([$uid]);
        $usermission = $db->fetch_row(true);

        if (!empty($usermission)) {
            $cache->setEx("userMission_" . $uid, 5, json_encode($usermission));
        }
    } else {
        $usermission = json_decode($usermission, true);
    }

    return $usermission;
}

function get_mission($id)
{
    global $db, $cache;

    $mission = $cache->get("mission_" . $id);
    if (!empty($mission) && $mission) {
        return json_decode($mission, true);
    }

    $db->query("SELECT * FROM mission WHERE id = ?");
    $db->execute([$id]);
    $mission = $db->fetch_row(true);
    if (!empty($mission)) {
        $cache->setEx("mission_" . $id, 3600, json_encode($mission));
        return $mission;
    }

    return null;
}

function getAllScheduledEvents()
{
    global $db, $cache;

    $now = time();
    if ($cache->exists("all_scheduled_events")) {
        $events = json_decode($cache->get("all_scheduled_events"), true);
        return $events;
    }

    $db->query("SELECT * FROM scheduledevents WHERE start <= ? AND end >= ?");
    $db->execute([$now, $now]);
    $events = $db->fetch_row();
    if ($events) {
        $cache->setEx("all_scheduled_events", 900, json_encode($events));
        return $events;
    }

    return null;
}

function isIPBanned($ip)
{
    global $db, $cache;

    if ($cache->exists('ipbans')) {
        $bannedIps = json_decode($cache->get('ipbans'));
    } else {
        $db->query("SELECT ip FROM ipbans");
        $db->execute();
        $bannedIps = $db->fetch_row();
        $bannedIps = array_map(function ($row) {
            return $row['ip'];
        }, $bannedIps);
        $cache->setEx('ipbans', 3600, json_encode($bannedIps));
    }

    return in_array($ip, $bannedIps);
}

function get_user_count()
{
    global $db, $cache;

    $userCount = $cache->get("user_count");
    if ($userCount !== false) {
        return $userCount;
    }

    $db->query("SELECT COUNT(*) FROM grpgusers");
    $db->execute();
    $userCount = $db->fetch_single();
    if ($userCount) {
        $cache->setEx("user_count", 36000, $userCount);
        return $userCount;
    }
}

function add_to_user_count()
{
    global $cache;

    if ($cache->exists("user_count")) {
        $cache->incr("user_count"); // atomic
    }
}

function get_pet_crimes()
{
    global $db, $cache;

    if ($cache->exists("pet_crimes")) {
        return json_decode($cache->get("pet_crimes"), true);
    }

    $db->query("SELECT * FROM petcrimes ORDER BY nerve ASC");
    $db->execute();
    $petCrimes = $db->fetch_row();
    if (!empty($petCrimes)) {
        $cache->setEx("pet_crimes", 36000, json_encode($petCrimes));
        return $petCrimes;
    }

    return [];
}

function get_skilltree_nodes($id)
{
    global $db, $cache;

    $cacheKey = "skilltree_nodes_" . $id;

    $cached = $cache->del($cacheKey);
    $cached = $cache->get($cacheKey);
    if (!empty($cached)) {
        return json_decode($cached, true);
    }

    $db->query("SELECT * FROM skilltree_nodes WHERE treeid = ?");
    $db->execute([$id]);
    $rows = $db->fetch_row();
    if (empty($rows)) {
        return [];
    }

    $indexed = [];
    foreach ($rows as $node) {
        if (!isset($node['rewards'])) {
            $indexed[$node['id']] = $node;
            continue; // No rewards, just return the node as is
        }

        $node['rewards'] = json_decode($node['rewards'], true);
        $indexed[$node['id']] = $node;
    }

    $cache->setEx($cacheKey, 3600, json_encode($indexed));
    return $indexed;
}

function get_skilltree_from_skill($id)
{
    global $db;

    $db->query("SELECT treeid FROM skilltree_nodes WHERE id = ?");
    $db->execute([$id]);

    $skillTreeId = $db->fetch_single();
    if ($skillTreeId) {
        return get_skilltree($skillTreeId);
    }

    return null;
}

function get_skilltree($id)
{
    global $db, $cache;

    $cache->del("skilltree_" . $id); // Clear cache to ensure fresh data

    $skillTree = $cache->get("skilltree_" . $id);
    if (!empty($skillTree) && $skillTree) {
        return json_decode($skillTree, true);
    }

    $db->query("SELECT * FROM skilltrees WHERE id = ?");
    $db->execute([$id]);
    $skillTree = $db->fetch_row(true);
    if (!empty($skillTree)) {
        $cache->setEx("skilltree_" . $id, 3600, json_encode($skillTree));
        return $skillTree;
    }

    return null;
}

// We support multiple skill trees, but we only use one massive collective
// skill tree for now. This function will claim the specialization for the user
// by setting the specialization level to 1 and assigning the root node's ID
// to the user's skill_ids field.
function claim_specialization($id, $uid)
{
    global $db;

    $skilltree = get_skilltree($id);
    if (empty($skilltree)) {
        return "Specialization could not be found!"; // Invalid skill tree
    }

    $db->query("SELECT * FROM skilltree_nodes WHERE treeid = ? AND parent IS NULL LIMIT 1");
    $db->execute([$id]);
    $rootNode = $db->fetch_row(true);
    if (empty($rootNode)) {
        return "Root node for specialization (" . $skilltree['id'] . ") does not exist!"; // No root node found
    }

    $db->query("UPDATE grpgusers SET specialization_level = 1, skill_points = 1, skill_ids = ? WHERE id = ?");
    $db->execute([$rootNode['id'], $uid]);

    return null;
}

function claim_skill($uid, $id)
{
    global $db;

    $db->query("SELECT skill_points, skill_ids FROM grpgusers WHERE id = ?");
    $db->execute([$uid]);
    $user = $db->fetch_row(true);

    if (empty($user)) {
        return "User not found"; // User not found
    }

    $skillIds = array_map('intval', explode(',', $user['skill_ids']));
    if (in_array($id, $skillIds)) {
        return "User already has this skill"; // Skill already claimed
    }

    if ($user['skill_points'] <= 0) {
        return "No skill points available"; // No skill points available
    }

    // Check if the skill exists and is valid
    $db->query("SELECT * FROM skilltree_nodes WHERE id = ?");
    $db->execute([$id]);
    $skill = $db->fetch_row(true);
    if (empty($skill)) {
        return "Skill not found"; // Skill does not exist
    }

    // Check if the skill's parent is already claimed
    if (isset($skill['parent']) && !in_array($skill['parent'], $skillIds)) {
        return "User needs to claim parent to claim this skill"; // Parent skill not claimed
    }
    // Add the skill to the user's claimed skills
    $skillIds[] = $id;
    $newSkillIds = implode(',', $skillIds);
    $db->query("UPDATE grpgusers SET skill_ids = ?, skill_points = skill_points - 1 WHERE id = ?");
    $db->execute([$newSkillIds, $uid]);

    return null; // Skill claimed successfully
}

function get_skill_boosts(array $userSkillIds)
{
    $allSkills = get_skilltree_nodes(1);

    $merged = [];
    foreach ($userSkillIds as $skillId) {
        if (!isset($allSkills[$skillId]['rewards']))
            continue;

        foreach ($allSkills[$skillId]['rewards'] as $key => $value) {
            if (!isset($merged[$key])) {
                $merged[$key] = in_array($key, ['ba_gold_rush_chance']) ? 0 : 1.0;
            }

            if (is_numeric($value)) {
                if (in_array($key, ['ba_gold_rush_chance'])) {
                    $merged[$key] += $value;
                } elseif ($value > 1.0) {
                    $merged[$key] += ($value - 1.0);
                } else {
                    $merged[$key] = max($merged[$key], $value);
                }
            }
        }
    }

    return $merged;
}

function get_maze_options()
{
    global $db, $cache;

    if ($cache->exists('maze_options')) {
        return json_decode($cache->get('maze_options'), true);
    }

    $db->query("SELECT * FROM citygame");
    $db->execute();
    $options = $db->fetch_row();
    $cache->setEx('maze_options', 3600, json_encode($options));

    return $options;
}

function get_pm_count($uid)
{
    global $db, $cache;

    $cacheKey = "pm_count_" . $uid;
    if ($cache->exists($cacheKey)) {
        return (int) $cache->get($cacheKey);
    }

    $db->query("SELECT count(*) FROM pms WHERE `to` = ? AND viewed = 1");
    $db->execute([$uid]);
    $count = (int) $db->fetch_single();
    $cache->setEx($cacheKey, 3600, $count);

    return $count;
}

function increase_pm_count($uid)
{
    global $cache;

    if ($cache->exists("pm_count_" . $uid)) {
        $cache->incr("pm_count_" . $uid);
    }
}

function decrease_pm_count($uid)
{
    global $cache;

    if ($cache->exists("pm_count_" . $uid)) {
        $new = $cache->decr("pm_count_" . $uid);
        if ($new < 0) {
            $cache->set("pm_count_" . $uid, 0);
        }
    }
}

function get_crime_rank($userId, $crimeId)
{
    global $db, $cache;

    $cacheKey = "crime_rank_{$userId}_{$crimeId}";
    if ($cache->exists($cacheKey)) {
        return (int) $cache->get($cacheKey);
    }

    $db->query("SELECT `count` FROM crimeranks WHERE userid = ? AND crimeid = ?");
    $db->execute([$userId, $crimeId]);
    $rank = (int) $db->fetch_single();
    $cache->setEx($cacheKey, 3600, $rank);

    return $rank;
}

function incrementCrimeRank($userId, $crimeId)
{
    global $db, $cache;

    $current = get_crime_rank($userId, $crimeId);
    $newCount = $current + 1;

    $db->query("INSERT INTO crimeranks (userid, crimeid, `count`) VALUES (?, ?, 1) ON DUPLICATE KEY UPDATE `count` = `count` + 1");
    $db->execute([$userId, $crimeId]);

    $cache->set("crime_rank_{$userId}_{$crimeId}", $newCount);

    return $newCount;
}

function get_game_bonus($id)
{
    global $db, $cache;

    $cacheKey = "game_bonus_{$id}";
    if ($cache->exists($cacheKey)) {
        return json_decode($cache->get($cacheKey), true);
    }

    $db->query("SELECT * FROM gamebonus WHERE ID = 1 LIMIT 1");
    $db->execute();
    $bonus_row = $db->fetch_row(true);
    $cache->setEx($cacheKey, 3600, json_encode($bonus_row));

    return $bonus_row;
}

function get_gang_crimeinfo($gid)
{
    global $db, $cache;

    $cacheKey = "gang_crimeinfo_{$gid}";
    if ($cache->exists($cacheKey)) {
        return json_decode($cache->get($cacheKey), true);
    }

    $db->query("SELECT upgrade_crimecash, tax FROM gangs WHERE id = ?");
    $db->execute([$gid]);
    $crime_info = $db->fetch_row(true);
    $cache->setEx($cacheKey, 3600, json_encode($crime_info));

    return $crime_info;
}

function get_crimes()
{
    global $db, $cache;

    $crimes = $cache->get("all_crimes");
    if (empty($crimes)) {
        $db->query("SELECT * FROM crimes ORDER BY nerve DESC");
        $db->execute();
        $crimes = $db->fetch_row();
        $cache->setEx("all_crimes", 7200, json_encode($crimes)); // Cache for 2 hours
    } else {
        $crimes = json_decode($crimes, true);
    }

    return $crimes;
}

function get_crime($crimeId)
{
    $crimes = get_crimes();
    foreach ($crimes as $crime) {
        if ((int) $crime['id'] === (int) $crimeId) {
            return $crime;
        }
    }

    return null;
}

function invalidateQuestSeasonCache(int $userId, int $questSeasonId): void
{
    global $cache;

    $keys = [
        "questSeasonForUser:{$userId}",
        "questSeasonUser:{$userId}:{$questSeasonId}",
        "questSeasonMissionUser:{$userId}:{$questSeasonId}",
    ];

    foreach ($keys as $key) {
        $cache->del($key);
    }
}

function cleanOldDBEntries()
{
    global $db;

    $total = 0;

    $timestamp = time() - (31 * 24 * 60 * 60);

    $db->startTrans();
    try {
        $db->query("DELETE FROM `active_raids` WHERE `summoned_at` < DATE_SUB(NOW(), INTERVAL 31 DAY)");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `raid_participants` WHERE `joined_at` < DATE_SUB(NOW(), INTERVAL 31 DAY)");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `raid_battle_logs` WHERE `timestamp` < DATE_SUB(NOW(), INTERVAL 31 DAY)");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `attacklog` WHERE `timestamp` < $timestamp");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `attlog` WHERE `timestamp` < $timestamp");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `deflog` WHERE `timestamp` < $timestamp");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `events` WHERE `timesent` < $timestamp");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `muglog` WHERE `timestamp` < $timestamp");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `user_logs` where `timestamp` < $timestamp");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `attack_v2` where `attack_time` < $timestamp");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `addptmarketlog` where `timestamp` < $timestamp");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `ads` where `timestamp` < $timestamp");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `bank_log` where `timestamp` < $timestamp");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `missionlog` where `timestamp` < $timestamp");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `oth` where `timestamp` < $timestamp");
        $db->execute();
        $total += $db->affected_rows();

        $db->query("DELETE FROM `missions` where  `timestamp` < $timestamp AND `completed` = 'successful'");
        $db->execute();
        $total += $db->affected_rows();
    } catch (Throwable $e) {
        $db->cancelTransaction();

        error_log('=== DEBUG DB CLEANUP ===');
        error_log($e);
        error_log('=== DEBUG DB CLEANUP ===');

        return -1;
    }

    return $total;
}

function canPerformAction($uid, $action)
{
    global $cache, $msPerAction;

    $key = "last_action:$uid:$action";
    $nowMs = (int) (microtime(true) * 1000);

    $configured = $msPerAction[$action] ?? 100;
    $grace = max(20, (int) round($configured * 0.2));
    $minMs = max(0, $configured - $grace);

    $lastMs = $cache->get($key);
    if ($lastMs !== false) {
        $delta = $nowMs - (int) $lastMs;
        if ($delta < $minMs) {
            return false;
        }
    }

    $cache->set($key, (string) ($nowMs - 5), ['px' => $minMs + 50]);

    return true;
}

$msPerAction = [
    "crime" => 50,
    "gym" => 30,
    "backalley" => 300,
];

function checkAJAXCaptchaRequired($uid): bool
{
    if (isset($_SESSION['force_captcha']) && $_SESSION['force_captcha'] === true) {
        $newMacroToken = generateMacroToken();
        header('Location: captcha.php?token=' . $newMacroToken . '&page=' . urlencode($_SERVER['REQUEST_URI']));

        return true;
    }

    return false;
}

function getChatMessage($chatmessage): string
{
    global $user_class;

    $chatMessage = "";

    $reply_class = new User($chatmessage['playerid']);
    $array = array(
        'name' => $reply_class->formattedname,
        'avatar' => $reply_class->avatar,
        'admin' => $reply_class->admin,
        'gm' => $reply_class->gm
    );

    $avatar = ($array['avatar'] != "") ? $array['avatar'] : "/images/no-avatar.png";
    $quotetext = str_replace(array('\'', '"'), array('\\\'', '&quot;'), $chatmessage['body']);

    $chatMessage .= '<div class="floaty">';
    $chatMessage .= '</div>';

    $parityClass = ($chatmessage['id'] % 2 === 0) ? 'msg-even' : 'msg-odd';
    $chatMessage .= '<table class="flexcont ' . $parityClass . '" id="chat-' . $chatmessage['id'] . '" style="width:100%;">';
    $chatMessage .= '<tr>';

    $chatMessage .= '<td class="flexele" style="border-right:thin solid #333;text-align:center;width:200px;">';
    $chatMessage .= '<img src="' . $avatar . '" height="150" width="150" style="border:1px solid #666666;margin-bottom: 6px;" />';
    $chatMessage .= '<br />';
    if ($chatmessage['playerid'] > 0) {
        $chatMessage .= $array['name'];
    } else {
        $chatMessage .= '<span style="color:red">System</span>';
    }
    $chatMessage .= '</td>';

    // Right cell for the body content
    $chatMessage .= '<td class="flexele" style="padding:10px;">';
    $chatMessage .= BBCodeParse(stripslashes($chatmessage['body']));
    $chatMessage .= '<br><br>';
    $chatMessage .= howlongago($chatmessage['timesent']) . ' ago <br><br>';
    $chatMessage .= (($user_class->admin || $user_class->gm || $user_class->cm) && (!$array['admin'] && !$array['gm'])) ? '<a href="?gcban=' . $chatmessage['playerid'] . '&conf=' . $_SESSION['security'] . '">Ban User</a>' : '';
    $chatMessage .= ($user_class->admin || $user_class->gm || $user_class->cm) ? '<a href="?delgc=' . $chatmessage['id'] . '">Delete Post</a>' : '';
    $chatMessage .= '<br><div class="flexele forumhover" onClick="addsmiley(\'[quote=' . $chatmessage['playerid'] . ']' . str_replace(array("\n", "\r"), array('', '\n'), $quotetext) . '[/quote]\\n\\n\');">';
    $chatMessage .= 'Quote';
    $chatMessage .= '</div>';
    $chatMessage .= '</td>';

    $chatMessage .= '</tr>';
    $chatMessage .= '</table>';

    return $chatMessage;
}

function checkMaintenanceIPs()
{
    if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $user_ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } else {
        $user_ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '';
    }

    $allowed_ips = [
        // '217.62.148.209', // Luuk
        // '2a00:4820:42e0:1400:5465:1987:e6e6:42b7', // Matt
    ];

    if (!in_array($user_ip, $allowed_ips, true)) {
        header('Location: maintenance.php');
        die();
    }
}