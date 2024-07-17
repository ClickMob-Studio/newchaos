<?php
            function ordinal($number) {
                $ends = array('th','st','nd','rd','th','th','th','th','th','th');
                if ((($number % 100) >= 11) && (($number%100) <= 13))
                    return $number. 'th';
                else
                    return $number. $ends[$number % 10];
            }
function get_users_online()
{
    global $db, $m;
    if (!$rtn = $m->get('get.users.online')) {
        $db->query("SELECT COUNT(id) c FROM grpgusers WHERE lastactive > unix_timestamp() - 3600");
        $db->execute();
        $r = $db->fetch_row(true);
        $rtn = $r['c'];
        $m->set('get.users.online', $rtn, false, 60);
    }
    return number_format($rtn);
}

function display_online_staff()
{
    global $db, $m;
    if (!$rtn = $m->get('display.online.staff')) {
        $db->query("SELECT id FROM grpgusers WHERE lastactive > unix_timestamp() - 3600 AND admin + gm + fm + cm + eo + st > 0");
        $db->execute();
        $r = $db->fetch_row();
        foreach ($r as $row)
            $rtn[] = formatName($row['id']);
        $m->set('display.online.staff', $rtn, false, 60);
    }
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
    $dtF = new DateTime("@0");
    $dtT = new DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%h hours, %i minutes and %s seconds');
}
function plane_popup($text, $id)
{
    return "<a href='javascript:;' onclick=\"javascript:window.open( 'planedesc.php?id=" . $id . "', '60', 'left = 20, top = 20, width = 400, height = 350, toolbar = 0, resizable = 0, scrollbars=1' );\">" . $text . "</a>";
}
function item_popup($text, $id, $color = '#fff')
{
    return "<a class='item-popup' style='color:" . $color . "' href='javascript:;' onclick=\"javascript:window.open( 'description.php?id=" . $id . "', '60', 'left = 20, top = 20, width = 400, height = 440, toolbar = 0, resizable = 0, scrollbars=0, location=0, menubar=0'  );\">" . $text . "</a>";
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
    $db->query("REPLACE INTO forum_browsers (userid, name) VALUES (?, ?)");
    $db->execute(array(
        $userid,
        $name
    ));
}
function getRank($userid, $stat)
{
    global $db, $m;
    if (!$rtn = $m->get("gymrank.$stat.$userid")) {
        $db->query("SELECT count(g.id) FROM grpgusers g WHERE (SELECT count(*) FROM bans b WHERE b.id = g.id AND type IN ('perm','freeze')) = 0 AND g.admin = 0 AND g.$stat > (SELECT xf.$stat FROM grpgusers xf WHERE xf.id = ?)");
        $db->execute(array(
            $userid
        ));
        $rtn = $db->fetch_single() + 1;
        $m->set("gymrank.$stat.$userid", $rtn, false, 300);
    }
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
function Check_Plane($itemid, $userid)
{
    global $db;
    $db->query("SELECT COUNT(*) FROM hangar WHERE userid = ? AND planeid = ?");
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
function AddToArmory($itemid, $gangid, $quantity = "1")
{
    global $db;
    $db->query("SELECT quantity FROM gangarmory WHERE gangid = ? AND itemid = ?");
    $db->execute(array(
        $gangid,
        $itemid
    ));
    $amnt = $db->fetch_single();
    if (!$amnt) {
        $db->query("INSERT INTO gangarmory (itemid, gangid, quantity) VALUES (?, ?, ?)");
        $db->execute(array(
            $itemid,
            $gangid,
            $quantity
        ));
    } else {
        $amnt += $quantity;
        $db->query("UPDATE gangarmory SET quantity = ? WHERE gangid = ? AND itemid = ?");
        $db->execute(array(
            $amnt,
            $gangid,
            $itemid
        ));
    }
}
function TakeFromArmory($itemid, $gangid, $quantity = "1")
{
    global $db;
    $db->query("SELECT quantity FROM gangarmory WHERE gangid = ? AND itemid = ?");
    $db->execute(array(
        $gangid,
        $itemid
    ));
    $amnt = $db->fetch_single();
    if ($amnt) {
        $amnt -= $quantity;
        if ($amnt > 0) {
            $db->query("UPDATE gangarmory SET quantity = ? WHERE gangid = ? AND itemid = ?");
            $db->execute(array(
                $amnt,
                $gangid,
                $itemid
            ));
        } else {
            $db->query("DELETE FROM gangarmory WHERE gangid = ? AND itemid = ?");
            $db->execute(array(
                $gangid,
                $itemid
            ));
        }
    }
}
function Give_Item($itemid, $userid, $quantity = "1")
{
    global $db;
    $db->query("SELECT quantity FROM inventory WHERE userid = ? AND itemid = ?");
    $db->execute(array(
        $userid,
        $itemid
    ));
    $amnt = $db->fetch_single();
    if (!$amnt) {
        $db->query("INSERT INTO inventory (itemid, userid, quantity) VALUES (?, ?, ?)");
        $db->execute(array(
            $itemid,
            $userid,
            $quantity
        ));
    } else {
        $amnt += $quantity;
        $db->query("UPDATE inventory SET quantity = ? WHERE userid = ? AND itemid = ?");
        $db->execute(array(
            $amnt,
            $userid,
            $itemid
        ));
    }
}
function Loan_Item($gang, $itemid, $userid, $quantity = 1)
{
    global $db;
    $db->query("SELECT * FROM gang_loans WHERE idto = ? and item = ?");
    $db->execute(array(
        $userid,
        $itemid
    ));
    if (!$db->num_rows()) {
        $db->query("INSERT INTO gang_loans VALUES ('', ?, ?, ?, ?)");
        $db->execute(array(
            $userid,
            $gang,
            $itemid,
            $quantity
        ));
    } else {
        $row = $db->fetch_row(true);
        $quantity += $row['quantity'];
        $db->query("UPDATE gang_loans SET quantity = ? WHERE idto = ? AND item = ?");
        $db->execute(array(
            $quantity,
            $userid,
            $itemid
        ));
    }
}
function Take_Item($itemid, $userid, $quantity = 1)
{
    global $db;
    $db->query("SELECT quantity FROM inventory WHERE userid = ? AND itemid = ?");
    $db->execute(array(
        $userid,
        $itemid
    ));
    $amnt = $db->fetch_single();
    if ($amnt) {
        $amnt -= $quantity;
        if ($amnt > 0) {
            $db->query("UPDATE inventory SET quantity = ? WHERE userid = ? AND itemid = ?");
            $db->execute(array(
                $amnt,
                $userid,
                $itemid
            ));
        } else {
            $db->query("DELETE FROM inventory WHERE userid = ? AND itemid = ?");
            $db->execute(array(
                $userid,
                $itemid
            ));
        }
    }
}

function Item_Name($itemId)
{
    global $db;
    $db->query("SELECT itemname FROM items WHERE id = ?");
    $db->execute(array(
        $itemId
    ));
    return $db->fetch_single();
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
    $rtn .=  $text;
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
    // $db->query("INSERT INTO eventslog (`to`, timesent, `text`, `extra`) VALUES (?, unix_timestamp(), ?, ?)");
    // $db->execute(array(
    //     $id,
    //     $text,
    //     $extra
    // ));
}
function Send_Event1($id, $text, $extra = "0")
{
  
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
    elseif ($ts < 60 * 60)
        return floor($ts / 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 60 * 60 * 2)
        return "1h " . floor(($ts / 60) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 60 * 60 * 24)
        return floor($ts / (60 * 60)) . "h " . floor(($ts / 60) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 60 * 60 * 24 * 2)
        return "1d " . floor($ts / (60 * 60) % 24) . "h " . floor(($ts / 60) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < (60 * 60 * 24 * 7) or $stop == 'days')
        return floor($ts / (60 * 60 * 24)) . "d " . floor($ts / (60 * 60) % 24) . "h " . floor(($ts / 60) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 60 * 60 * 24 * 30.5)
        return floor($ts / (60 * 60 * 24 * 7)) . " weeks ago";
    elseif ($ts < 60 * 60 * 24 * 365)
        return floor($ts / (60 * 60 * 24 * 30.5)) . " months ago";
    else
        return floor($ts / (60 * 60 * 24 * 365)) . " years ago";
}
function howlongtil($ts)
{
    $ts = $ts - time();
    if ($ts < 1)
        return " NOW";
    elseif ($ts == 1)
        return $ts . " second";
    elseif ($ts < 60)
        return $ts . " seconds";
    elseif ($ts < 120)
        return "1m " . ($ts % 60) . "s";
    elseif ($ts < 60 * 60)
        return floor($ts / 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 60 * 60 * 2)
        return "1h " . floor(($ts / 60) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 60 * 60 * 24)
        return floor($ts / (60 * 60)) . "h " . floor(($ts / 60) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 60 * 60 * 24 * 2)
        return "1d " . floor($ts / (60 * 60) % 24) . "h " . floor(($ts / 60) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < (60 * 60 * 24 * 7))
        return floor($ts / (60 * 60 * 24)) . "d " . floor($ts / (60 * 60) % 24) . "h " . floor(($ts / 60) % 60) . "m " . ($ts % 60) . "s";
    elseif ($ts < 60 * 60 * 24 * 30.5)
        return floor($ts / (60 * 60 * 24 * 7)) . " weeks";
    elseif ($ts < 60 * 60 * 24 * 365)
        return floor($ts / (60 * 60 * 24 * 30.5)) . " months";
    else
        return floor($ts / (60 * 60 * 24 * 365)) . " years";
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
function formatName($id, $nogang = 0)
{
    global $db, $m;
    $name = "";
    if ($nogang == 0 && $id != 864 and !empty($rtn = $m->get('formatName.' . $id)))
        return $rtn;
    $db->query("SELECT username, gang, admin, rmdays, gm, colours, image_name, pdimgname, gradient, gndays, leader, g.tag, formattedTag, prestige, uninfo FROM grpgusers gu LEFT JOIN gangs g ON g.id = gu.gang WHERE gu.id = ?");
    $db->execute(array(
        $id
    ));
    $row = $db->fetch_row(true);
    if ($row['gang'] != 0 and $nogang != 1) {
        if ($id == 2) {
            if ($row['gndays'] > 0) {
                $name .= "<a style='font-size:1.5em;' href='viewgang.php?id={$row['gang']}'";
            } else {
                $name .= "<a href='viewgang.php?id={$row['gang']}'";
            }
        } else {
            $name .= "<a href='viewgang.php?id={$row['gang']}'";
        }
        if ($row['formattedTag'] == "Yes")
            $name .= ($row['leader'] == $id) ? " title='Gang Leader'><font color=grey>[<b>" . gradientTag($row['gang']) . "</b>]</font></a> " : "><font color=grey>[" . gradientTag($row['gang']) . "]</font></a> ";
        else
            $name .= ($row['leader'] == $id) ? " title='Gang Leader'><font color=blue>[<b>{$row['tag']}</b>]</font></a> " : "><font color=white>[{$row['tag']}]</font></a> ";
    }
    $db->query("SELECT days FROM bans WHERE id = ? AND type IN ('perm','freeze')");
    $db->execute(array(
        $id
    ));
    $bdays = $db->fetch_single();
    if ($bdays) {
        $title = "Banned";
        $whichfont = "#FFFFFF";
    } else if ($row['admin'] == 1) {
        $title = "Admin";
        $whichfont = "#FF1111";
    } else if ($row['gm'] == 1) {
        $title = "Chat Moderator";
        $whichfont = "#FFFFFF";
    } else if ($row['rmdays'] >= 1) {
        $title = "VIP ({$row['rmdays']} VIP Days Left)";
        $whichfont = "#00BF03";
    } else {
        $title = "Not Respected";
        $whichfont = "#009102";
    }
    if ($bdays)
        $name .= "<a title='$title' href='profiles.php?id=$id'>&nbsp;<font color = '$whichfont'>{$row['username']}</s></font></a>";
    else if (!empty($row['image_name']) && $row['pdimgname'] > 0) {
        $name .= ($row['admin'] == 1 || $row['gm'] == 1) ? "<a title='" . $title . " [" . $row['username'] . "]' href='profiles.php?id=" . $id . "'>" : "<a title='" . $title . "' href='profiles.php?id=" . $id . "'>";
        $name .= "<img src='{$row['image_name']}' style='max-width:84px; max-height:50px;' title='" . $row['username'] . "' />";
        $name .= ($row['admin'] == 1 || $row['gm'] == 1) ? "</a>" : "</a>";
    } else if (false) { //$id >= 334 AND $id <= 353) {
        $username = $row['username'];
        $half = (int) ((strlen($username) / 2));
        $left = substr($username, 0, $half);
        $right = substr($username, $half);
        $gradient = text_gradient('008800', '00CC00', 1, $left);
        $gradient .= text_gradient('00CC00', '008800', 1, $right);
        $name .= "<b><a title='BOT' href='profiles.php?id=" . $id . "'>";
        $name .= $gradient;
        $name .= "</b></a>";
    } elseif ($row['gndays']) {
        $name .= "<a href='profiles.php?id=" . $id . "'>" . nameGen($row['gndays'], $row['rmdays'], $row['uninfo'], $row['username']) . "</a>";
    } else if (!empty($row['colours']) and $row['gradient'] == 2 and $row['gndays']) {
        $row['colours'] = str_replace('#', '', $row['colours']);
        $colours = explode("~", $row['colours']);
        $gradient = text_gradient($colours[0], $colours[1], 1, $row['username']);
        $name .= ($row['admin'] == 1 || $row['gm'] == 1) ? "<b><i><a title='" . $title . "' href='profiles.php?id=" . $id . "'>" : "<b><a title='" . $title . "' href='profiles.php?id=" . $id . "'>";
        $name .= $gradient;
        $name .= ($row['admin'] == 1 || $row['gm'] == 1) ? "</a></u></b>" : "</a></b>";
    } else if (!empty($row['colours']) and $row['gradient'] == 3 and $row['gndays']) {
        $row['colours'] = str_replace('#', '', $row['colours']);
        $gn = explode("~", $row['colours']);
        $username = $row['username'];
        $half = (int) ((strlen($username) / 2));
        $left = substr($username, 0, $half);
        $right = substr($username, $half);
        $gradient = text_gradient($gn[0], $gn[1], 1, $left);
        $gradient .= text_gradient($gn[1], $gn[2], 1, $right);
        if ($id == 146)
            $gradient = "<span style='text-shadow: 0 0 2px #404200;letter-spacing:-1px;font-weight:900;font-size:16px;'>$gradient</span>";
        $name .= ($row['admin'] == 1 || $row['gm'] == 1) ? "<b><i><a title='" . $title . "' href='profiles.php?id=" . $id . "'>" : "<b><a title='" . $title . "' href='profiles.php?id=" . $id . "'>";
        $name .= $gradient;
        $name .= ($row['admin'] == 1 || $row['gm'] == 1) ? "</a></i></b>" : "</a></b>";
    } else if ($id == 146)
        $name .= "<a title='$title' href='profiles.php?id=$id'>{$row['username']}</a>";
    else if ($row['admin'] == 1 || $row['gm'] == 1)
        $name .= "<i><b><a title='$title' href='profiles.php?id=$id'><font color = '$whichfont'>{$row['username']}</a></font></b></i>";
    else if ($row['rmdays'] > 0)
        $name .= "<b><a title='$title' href='profiles.php?id=$id'><font color='$whichfont'>{$row['username']}</a></font></b>";
    else
        $name .= "<a title='$title' href='profiles.php?id=$id'><font color='$whichfont'>{$row['username']}</a></font>";
    if ($row['prestige'] > 0) {

        if ($row['prestige'] >= 10) {
            $db->query("SELECT skull FROM prestige_skull WHERE `user_id` = ?");
            $db->execute(array(
                $id
            ));
            $skull = $db->fetch_single();

            if ($skull !== false) {
                $name .= " <img src='images/skullpres_" . $skull . ".png' title='Prestige ({$row['prestige']})' />";
            } else {
                $name .= " <img src='images/skullpres_" . $row['prestige'] . ".png' title='Prestige ({$row['prestige']})' />";
            }
        } else {
            $name .= " <img src='images/skullpres_" . $row['prestige'] . ".png' title='Prestige ({$row['prestige']})' />";
        }
    }
    if ($nogang == 0)
        $m->set('formatName.' . $id, $name, false, 60);
    return $name;
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
function mission($update, $howmany = 1)
{
    global $user_class, $db;

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
        if ($update == 'k') {
            $db->query("UPDATE missions SET kills = kills + 1 WHERE userid = ? AND completed = 'no'");
            $db->execute(array(
                $user_class->id
            ));
            if (++$userMiss['kills'] == $miss['kills']) {
                $mPointsPayout = $miss['payKills'];
                if ($pointsPayoutBoost) {
                    $mPointsPayout = $mPointsPayout + ($mPointsPayout / 100 * $pointsPayoutBoost);
                }
                $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $db->execute(array(
                    $mPointsPayout,
                    $user_class->id
                ));
                $db->query("INSERT INTO missionlog VALUES(NULL,'[x] successfully completed {$miss['name']} objective to get {$miss['kills']} kills,$user_class->id',unix_timestamp())");
                $db->execute();
                Send_event($user_class->id, "You have completed {$miss['name']} objective to get {$miss['kills']} kills.");
            }
        }
        if ($update == 'b') {
            $db->query("UPDATE missions SET busts = busts + 1 WHERE userid = ? AND completed = 'no'");
            $db->execute(array(
                $user_class->id
            ));
            if (++$userMiss['busts'] == $miss['busts']) {
                $mPointsPayout = $miss['payBusts'];
                if ($pointsPayoutBoost) {
                    $mPointsPayout = $mPointsPayout + ($mPointsPayout / 100 * $pointsPayoutBoost);
                }

                $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $db->execute(array(
                    $mPointsPayout,
                    $user_class->id
                ));
                $db->query("INSERT INTO missionlog VALUES(NULL,'[x] successfully completed {$miss['name']} objective to get {$miss['busts']} busts,$user_class->id',unix_timestamp())");
                $db->execute();
                Send_event($user_class->id, "You have completed {$miss['name']} objective to get {$miss['busts']} busts.");
            }
        }
        if ($update == 'c') {
            $db->query("UPDATE missions SET crimes = crimes + ? WHERE userid = ? AND completed = 'no'");
            $db->execute(array(
                $howmany,
                $user_class->id
            ));
            // if (++$userMiss['crimes'] == $miss['crimes']) {
            // if (($userMiss['crimes'] + $howmany) >= $miss['crimes']) {
            //     $_SESSION['crime_lock'] = 1;
            //     $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
            //     $db->execute(array(
            //         $miss['payCrimes'],
            //         $user_class->id
            //     ));
            //     $db->query("INSERT INTO missionlog VALUES(NULL,'[x] successfully completed {$miss['name']} objective to get {$miss['crimes']} crimes,$user_class->id',unix_timestamp())");
            //     $db->execute();
            //     Send_event($user_class->id, "You have completed {$miss['name']} objective to get {$miss['crimes']} crimes.");
            //     unset($_SESSION['crime_lock']);;
            // }
        }
        if ($update == 'm') {
            $db->query("UPDATE missions SET mugs = mugs + 1 WHERE userid = $user_class->id AND completed = 'no'");
            $db->execute(array(
                $user_class->id
            ));
            if (++$userMiss['mugs'] == $miss['mugs']) {
                $mPointsPayout = $miss['payMugs'];
                if ($pointsPayoutBoost) {
                    $mPointsPayout = $mPointsPayout + ($mPointsPayout / 100 * $pointsPayoutBoost);
                }

                $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $db->execute(array(
                    $mPointsPayout,
                    $user_class->id
                ));
                $db->query("INSERT INTO missionlog VALUES(NULL,'[x] successfully completed {$miss['name']} objective to get {$miss['mugs']} mugs,$user_class->id',unix_timestamp())");
                $db->execute();
                Send_event($user_class->id, "You have completed {$miss['name']} objective to get {$miss['mugs']} mugs.");
            }
        }
        if ($update == 'ba') {
            $db->query("UPDATE missions SET backalleys = backalleys + 1 WHERE userid = $user_class->id AND completed = 'no'");
            $db->execute(array(
                $user_class->id
            ));
            if (++$userMiss['backalleys'] == $miss['backalleys']) {
                $mPointsPayout = $miss['payBackalleys'];
                if ($pointsPayoutBoost) {
                    $mPointsPayout = $mPointsPayout + ($mPointsPayout / 100 * $pointsPayoutBoost);
                }

                $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $db->execute(array(
                    $mPointsPayout,
                    $user_class->id
                ));
                $db->query("INSERT INTO missionlog VALUES(NULL,'[x] successfully completed {$miss['name']} objective to get {$miss['backalleys']} backalleys,$user_class->id',unix_timestamp())");
                $db->execute();
                Send_event($user_class->id, "You have completed {$miss['name']} objective to get {$miss['backalleys']} backalleys.");
            }
        }
    } else {
        return 1;
    }
    // if ($userMiss['kills'] >= $miss['kills'] && $userMiss['crimes'] >= $miss['crimes'] && $userMiss['busts'] >= $miss['busts'] && $userMiss['mugs'] >= $miss['mugs']) {
    //     $exp = 5 + (5 * $userMiss['mid']);
    //     $levelhurts = floor($user_class->level / 10);
    //     $exp = ($exp - $levelhurts < 3) ? 3 : $exp - $levelhurts;
    //     $expgain = floor($user_class->maxexp * ($exp / 100));
    //     $db->query("UPDATE grpgusers SET exp = exp + ? WHERE id = ?");
    //     $db->execute(array(
    //         $expgain,
    //         $user_class->id
    //     ));
    //     Send_event($user_class->id, "You have completed the {$miss['name']}! [+ $expgain EXP]");
    //     $db->query("UPDATE missions SET completed = 'successful' WHERE id = ?");
    //     $db->execute(array(
    //         $userMiss['id']
    //     ));
    //     $db->query("INSERT INTO missionlog VALUES (NULL,'[x] successfully completed their {$miss['name']}.,$user_class->id',unix_timestamp())");
    //     $db->execute();
    // }
    return 1;
}
function bloodbath($att, $id, $amnt = 1)
{
    global $db, $user_class;

    $db->query("SELECT userid FROM bbusers WHERE userid = ?");
    $db->execute(array(
        $id
    ));
    $uid = $db->fetch_single();
    if (!$uid) {
        $db->query("INSERT INTO bbusers (userid) VALUES (?)");
        $db->execute(array(
            $id
        ));
    }
    $db->query("UPDATE bbusers SET $att = $att + ? WHERE userid = ?");
    $db->execute(array(
        $amnt,
        $id
    ));

    $db->query("SELECT gang FROM grpgusers WHERE id = ?");
    $db->execute(array(
        $id
    ));
    $gangid = $db->fetch_single();

    // HOOK TO UPDATE GANG dailyCrimes
    if ($att == 'crimes' && $gangid > 0) {
        $db->query("UPDATE gangs SET dailyCrimes = dailyCrimes + ? WHERE id = ?");
        $db->execute(array(
            $amnt,
            $gangid
        ));
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
function genBars() {
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
    $adding = "";
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
function Give_Pet($petid, $userid, $picture, $str = 10, $spe = 10, $def = 10, $name = "No Name")
{
    global $db;
    $db->query("SELECT petid FROM pets WHERE userid = ? AND petid = ?");
    $db->execute(array(
        $userid,
        $petid
    ));
    $pet = $db->fetch_single();
    if ($pet)
        return false;
    $db->query("INSERT INTO pets (petid, userid, str, spe, def, pname, avi) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $db->execute(array(
        $petid,
        $userid,
        $str,
        $spe,
        $def,
        $name,
        $picture
    ));
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
function gcTalking($which = 0, $gang = 0)
{
    global $db;
    if ($which == 0) {
        $db->query("SELECT * FROM gcusers");
        $db->execute();
    } else {
        $db->query("SELECT * FROM gmusers WHERE gang = ?");
        $db->execute(array(
            $gang
        ));
    }
    $rows = $db->fetch_row();
    $ret = '<div class="flexcont" style="margin:2px; display: flex; flex-wrap: nowrap;">'; // Changed flex-wrap to nowrap
    $count = count($rows);
    $leftover = 4 - ($count % 4);
    if ($count < 4)
        $leftover = 0;
    foreach ($rows as $row) {
        if ($row['userid'] == 150) continue;
        $ret .= '<div class="flexele" style="margin:2px; flex-basis:22%;">';
        $ret .= '<div class="floaty" style="margin:0;height:20px;line-height:20px;';
        $ret .= ($row['typing']) ? 'background:rgba(0,255,0,.125);' : '';
        $ret .= '" onclick="addsmiley(\' [tag]' . $row['userid'] . '[/tag] \');">';
        $ret .= formatName($row['userid']);
        $ret .= '</div>';
        $ret .= '</div>';
    }
    for ($i = 0; $i < $leftover; $i++) {
        $ret .= '<div class="flexele" style="margin:5px;flex-basis:22%;">';
        $ret .= '</div>';
    }
    $ret .= '</div>';
    return $ret;
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
    $db->query("SELECT * FROM bank_log WHERE userid = ?{$sql} ORDER BY timestamp DESC LIMIT $limit");
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
    global $db, $m;
//    if (!$rtn = $m->get('citynames.' . $cityId)) {
//        $db->query("SELECT `name` FROM cities WHERE id = ?");
//        $db->execute([$cityId]);
//        $rtn = $db->fetch_single();
//        //$m->set('citynames.' . $cityId, $city, false, 60);
//    }

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
        if ($hr > 12) $day++; // round up if not displaying hours
    }

    $ret = array();
    if ($day && $h) $ret[] = ($day ? $day . "d" : "");
    if ($day && !$h) $ret[] = ($day ? $day . "d" : "");
    if ($hr && $h) $ret[] = ($hr ? $hr . "h" : "");
    if ($min && $m && $h) $ret[] = ($min ? $min . "m" : "");
    if ($sec && $s && $m && $h) $ret[] = ($sec ? $sec . "s" : "");

    $last = end($ret);
    array_pop($ret);
    $string = join(" ", $ret) . " {$last}";

    return $string;
}

function calctime($seconds = 0)
{
    $interval = date_diff(date_create("@0"), date_create("@$seconds"));

    foreach (array('y' => 'Year', 'm' => 'Month', 'D' => 'day', 'h' => 'Hour', 'i' => 'Minute', 's' => 'Second') as $format => $desc) {
        if ($interval->$format >= 1) $thetime[] = $interval->$format . ($interval->$format == 1 ? " $desc" : " {$desc}s");
    }

    return isset($thetime) ? implode(' ', $thetime) . ($interval->invert ? ' ago' : '') : NULL;
}

function Get_Item_name($id){
    $id = intval($id);
    $query = mysql_query("SELECT * FROM items WHERE `id` = ".$id);
    if(mysql_num_rows($query)){
        $result = mysql_fetch_assoc($query);
        return $result['itemname'];
    }else{
        return 'Unknown Item';
    }
}
function updateGangActiveMission( $field, $value) {
  global $db, $user_class;

    // Check if the user is in a gang
    if ($user_class->gang != 0) {
        // Prepare and execute the query to check for an active mission
        $sql = "SELECT agm.kills, agm.busts, agm.crimes, agm.mugs, gm.name, gm.kills AS target_kills, gm.busts AS target_busts, gm.crimes AS target_crimes, gm.mugs AS target_mugs, gm.reward, gm.time AS 'mission_time', UNIX_TIMESTAMP() AS 'current_time', agm.end_time FROM active_gang_missions agm JOIN gang_missions gm ON agm.mission_id = gm.id WHERE agm.gangid = :gangid AND agm.completed = 0 LIMIT 1";
        $db->query($sql);
        $db->bind(':gangid', $user_class->gang);
        $activeMission = $db->fetch_row(true);

        if ($activeMission) {
            // Sanitize the field name to prevent SQL injection
            $allowed_fields = ['kills', 'busts', 'crimes', 'mugs'];
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


function generateMacroToken($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $index = mt_rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
    return $randomString;
}

function macroTokenCheck($user_class)
{
    if (!isset($_GET['token'])) {
//        Send_Event(1, 'ID ' . $user_class-> id . ' MUGGING: NO TOKEN PROVIDED', 1);
//        Send_Event(2, 'ID ' . $user_class-> id . ' MUGGING: NO TOKEN PROVIDED', 2);

        echo "
            <div class='alert alert-danger'>
                <p>1 Something went wrong, an Admin has been informed.
            </div>
        ";
        exit;
    }

    $token = $_GET['token'];
    if (empty($token)) {
//        Send_Event(1, 'ID ' . $user_class-> id . ' WRONG TOKEN PROVIDED ' . $token, 1);
//        Send_Event(2, 'ID ' . $user_class-> id . ' WRONG TOKEN PROVIDED ' . $token, 2);

        echo "
            <div class='alert alert-danger'>
                <p>2 Something went wrong, an Admin has been informed.
            </div>
        ";
        exit;
    }

    if ($user_class->macro_token != $token) {
//        Send_Event(1, 'ID ' . $user_class-> id . ' WRONG TOKEN PROVIDED ' . $token . ' - ' . $user_class->macro_token, 1);
//        Send_Event(2, 'ID ' . $user_class-> id . ' WRONG TOKEN PROVIDED ' . $token . ' - ' . $user_class->macro_token, 2);


        echo
        "
            <div class='alert alert-danger'>
                <p>3 Something went wrong, an Admin has been informed.
            </div>
        ";
        exit;
    }

    $newMacroToken = generateMacroToken();
    mysql_query("UPDATE grpgusers SET macro_token = '" . $newMacroToken ."' WHERE id = " . $user_class->id);

    return $newMacroToken;
}

function getItemTempUse($userId)
{
    global $db;

    $db->query("SELECT * FROM item_temp_use WHERE user_id = " . $userId . " LIMIT 1");
    $db->execute();
    $r = $db->fetch_row();

    if (isset($r[0]['id'])) {
        return $r[0];
    } else {
        $db->query("INSERT INTO item_temp_use (user_id) VALUES (" . $userId . ")");
        $db->execute();
        $r = getItemTempUse($userId);

        return $r;
    }
}

function addItemTempUse($user_class, $field, $qty = 1)
{
    global $db;

    $itemTempUse = getItemTempUse($user_class->id);

    if ($field == 'crime_potion_time' || $field == 'crime_booster_time' || $field == 'nerve_vial_time' || $field == 'gang_double_exp_time') {
        $db->query("UPDATE item_temp_use SET {$field} = {$qty} WHERE id = " . $itemTempUse['id']);
        $db->execute();
    } else {
        $db->query("UPDATE item_temp_use SET {$field} = {$field} + {$qty} WHERE id = " . $itemTempUse['id']);
        $db->execute();
    }
}

function removeItemTempUse($userId, $field, $qty = 1)
{
    $itemTempUse = getItemTempUse($userId);

    mysql_query("UPDATE item_temp_use SET {$field} = {$field} - {$qty} WHERE user_id = " . $userId);
}

function removeFromInventory($userId, $item, $qty = 1){
    $item = intval($item);
    $check = mysql_query("SELECT * FROM inventory WHERE itemid = $item AND userid = $userId");
    if(mysql_num_rows($check) < 0){
        return false;
    }
    $ch = mysql_fetch_array($check);
    if($ch['quantity'] > $qty){
        mysql_query("UPDATE inventory SET quantity = quantity - $qty WHERE itemid = $item AND userid = $userId");
        return true;
    }else{
        mysql_query("DELETE FROM inventory WHERE itemid = $item AND userid = $userId");
        return true;
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

    $maxLevels = 15;
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
        $db->query("UPDATE `gang_comp_leaderboard` SET `" . $dailyField ."` = `" . $dailyField ."` + " . $value . ", `" . $weeklyField ."` = `" . $weeklyField ."` + " . $value . " WHERE gang_id = " . $gangId);
        $db->execute();
    } else {
        $db->query("INSERT INTO `gang_comp_leaderboard` (`gang_id`, `" . $dailyField . "`, `" . $weeklyField . "`) VALUES (" . $gangId .", " . $value . ", " . $value . ")");
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
        $db->query("UPDATE `user_comp_leaderboard` SET `" . $dailyField ."` = `" . $dailyField ."` + " . $value . ", `" . $weeklyField ."` = `" . $weeklyField ."` + " . $value . " WHERE user_id = " . $userId);
        $db->execute();
    } else {
        $db->query("INSERT INTO `user_comp_leaderboard` (`user_id`, `" . $dailyField . "`, `" . $weeklyField . "`) VALUES (" . $userId .", " . $value . ", " . $value . ")");
        $db->execute();
    }
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
    global $db;

    $db->query("SELECT * FROM user_prestige_skills WHERE user_id = " . $user_class->id . " LIMIT 1");
    $db->execute();
    $r = $db->fetch_row();

    if (isset($r[0]['id'])) {
        $r[0]['prestige_unlocks_available'] = ($user_class->prestige * 1) - $r[0]['unlock_points_spent'];
        $r[0]['prestige_boosts_available'] = ($user_class->prestige * 5) - $r[0]['boosts_spent'];

        return $r[0];
    } else {
        $db->query("INSERT INTO user_prestige_skills (user_id) VALUES (" . $user_class->id . ")");
        $db->execute();
        $r = getUserPrestigeSkills($user_class);

        return $r;
    }
}

function addUserPrestigeSkill($user_class, $field, $qty = 1)
{
    global $db;

    $data = getUserPrestigeSkills($user_class->id);

    $db->query("UPDATE user_prestige_skills SET {$field} = {$field} + {$qty} WHERE id = " . $data['id']);
    $db->execute();
}

function getBpCategory($overrideId = null)
{
    global $db;

    if ($overrideId) {
        $db->query("SELECT * FROM bp_category WHERE id = '" . $overrideId . "' LIMIT 1");
        $db->execute();
        $r = $db->fetch_row();

        if (isset($r[0]['id'])) {
            return $r[0];
        }
    } else {
        $now = new \DateTime();

        $db->query("SELECT * FROM bp_category WHERE month_year = '" . $now->format('m-Y') . "' LIMIT 1");
        $db->execute();
        $r = $db->fetch_row();

        if (isset($r[0]['id'])) {
            return $r[0];
        }
    }

    return null;
}

function getBpCategoryChallenges($bpCategory)
{
    global $db;

    $now = new \DateTime();

    $db->query("SELECT * FROM bp_category_challenges WHERE bp_category_id = " . $bpCategory['id']);
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

    $now = new \DateTime();

    $db->query("SELECT * FROM bp_category_prizes WHERE bp_category_id = " . $bpCategory['id']);
    $db->execute();
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
        return number_format($bpCategoryPrize['amount'], 0) . ' x ' .  ucfirst($bpCategoryPrize['type']);
    }
}


function getBpCategoryUser($bpCategory, $user_class)
{
    global $db;

    $db->query("SELECT * FROM bp_category_user WHERE user_id = " . $user_class->id . " AND bp_category_id = " . $bpCategory['id'] . " LIMIT 1");
    $db->execute();
    $r = $db->fetch_row();

    if (isset($r[0]['id'])) {
        return $r[0];
    } else {
        $db->query("INSERT INTO bp_category_user (bp_category_id, user_id) VALUES (" . $bpCategory['id'] . ", " . $user_class->id . ")");
        $db->execute();
        $r = getBpCategoryUser($bpCategory['id'], $user_class);

        return $r;
    }
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
    if ($attacker->moddedstrength > $defender->moddeddefense) {
        $maxDamage = 15000;
    } else {
        $maxDamage = 12000;
    }

    $milDifference = $attacker->moddedstrength - $defender->moddeddefense;
    if ($milDifference > 0) {
        $milDifference = $milDifference / 10000000;
        if ($milDifference > 0) {
            $maxDamage += ceil($milDifference);
        }
    }

    $log = log($attacker->moddedstrength, $defender->moddeddefense);
    $maxDamage = $maxDamage * $log;
    $maxDamage = ceil($maxDamage);

    if ($log > 1.9) {
        $damMinPerc = 70;
        $damMaxPerc = 80;
    } else if ($log > 1.5) {
        $damMinPerc = 60;
        $damMaxPerc = 70;
    } else if ($log > 1) {
        $damMinPerc = 50;
        $damMaxPerc = 60;
    } else if ($log > 0.9) {
        $damMinPerc = 40;
        $damMaxPerc = 50;
    } else {
        $damMinPerc = 20;
        $damMaxPerc = 30;
    }


    // Critical Hit
    if (mt_rand(1,100) <= $criticalHit) {
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

    $logStrength = log($attacker->moddedstrength);
    $logDefence = log($defender->moddeddefense);

    $maxDamage = ceil($logStrength / $logDefence * 10000);



    if ($logStrength > $logDefence) {
        $damMinPerc = 70;
        $damMaxPerc = 80;
    } else if ($logDefence > $logStrength) {
        $damMinPerc = 20;
        $damMaxPerc = 30;
    } else {
        $damMinPerc = 40;
        $damMaxPerc = 50;
    }

    // Critical Hit
    if (mt_rand(1,100) <= $criticalHit) {
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