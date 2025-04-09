<?php

function timeRemaining($futureTimestamp)
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
function item_popup($text, $id)
{
    return "<a href='javascript:;' onclick=\"javascript:window.open( 'description.php?id=" . $id . "', '60', 'left = 20, top = 20, width = 400, height = 440, toolbar = 0, resizable = 0, scrollbars=0, location=0, menubar=0'  );\">" . $text . "</a>";
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
        Send_Event($player, "[-_USERID_-] requested a relationship with you. <a href='rel_requests.php'>[Click here to view]</a>", $from);
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
        $m->set("gymrank.$stat.$userid", $rtn, 300);
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
    return '<table style="width:100%;"><tr><td class="contenthead">' . $text . '</td></tr></table>';
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
    $db->query("INSERT INTO eventslog (`to`, timesent, `text`, `extra`) VALUES (?, unix_timestamp(), ?, ?)");
    $db->execute(array(
        $id,
        $text,
        $extra
    ));
}
function Send_Event1($id, $text, $extra = "0")
{
    global $db;
    if (empty($id))
        return;
    $db->query("INSERT INTO cityevents (`to`, timesent, `text`, `extra`) VALUES (?, unix_timestamp(), ?, ?)");
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
/*
function experience($L) {
   $a = 0;
   $end = 0;
   for ($x = 1; $x < $L; $x++)
       $a += round($x + 1500 * pow(4, ($x / 190)));
   if ($x >= 200)
       $a *= 2;
   if ($L >= 1000)
       $a *= 2;
   if ($L >= 5000)
       $a *= 2;
   if ($L >= 10000)
       $a *= 2;
   if ($L >= 20000)
       $a *= 2000000000000;
   if ($L >= 50000)
       $a *= 2;
   if ($L >= 100000)
       $a *= 2;
   if ($L >= 200000)
       $a *= 2;
   if ($L >= 500000)
       $a *= 2;
if ($L >= 1000000)
       $a *= 1.2;

   return round($a / 4);
}
*/
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
        $the_info = '<table width="450px" class="userbar" cellspacing="0" cellpadding="3"><tr onclick="DoNav(\'http://army-wars.com/profiles.php?id=' . $bar_class->id . '\');"><td width="40%">' . $bar_class->formattedname . '</td><td style="border-left: 1px solid #444444;" width="15%">LVL:&nbsp;' . $bar_class->level . '</td><td style="border-left: 1px solid #444444;" width="15%">HP:&nbsp;<font color="' . $colour . '">' . $bar_class->hppercent . '%</font></td><td style="border-left: 1px solid #444444;" width="26%"><a href="bus.php">' . $bar_class->cityname . '</a></td><td align="center" style="border-left: 1px solid #444444; background-color:' . $colour1 . ';" width="4%"><div style="background-color:' . $colour2 . ';">&nbsp;&nbsp;</div></td></tr></table>';
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
        $the_info = '<table width="450px" class="userbar" cellspacing="0" cellpadding="3"><tr onclick="DoNav(\'http://army-wars.com/profiles.php?id=' . $bar_class->id . '\');"><td width="40%">' . $bar_class->formattedname . '</td><td style="border-left: 1px solid #444444;" width="15%">LVL:&nbsp;' . $bar_class->level . '</td><td style="border-left: 1px solid #444444;" width="15%">HP:&nbsp;<font color="' . $colour . '">' . $bar_class->hppercent . '%</font></td><td style="border-left: 1px solid #444444;" width="26%"><a href="bus.php">' . $bar_class->cityname . '</a></td><td align="center" style="border-left: 1px solid #444444; background-color:' . $colour1 . ';" width="4%"><div style="background-color:' . $colour2 . ';">&nbsp;&nbsp;</div></td></tr></table><table width="450px" class="userbar2" cellspacing="0" cellpadding="3"><tr><td width="20%" align="center">[<a href="attack.php?attack=' . $bar_class->id . '">attack</a>]</td><td style="border-left: 1px solid #444444;" width="20%" align="center">[<a href="mug.php?mug=' . $bar_class->id . '">mug</a>]</td><td style="border-left: 1px solid #444444;" width="20%" align="center">[<a href="spy.php?id=' . $bar_class->id . '">spy</a>]</td><td style="border-left: 1px solid #444444;" width="20%" align="center">[<a href="profiles.php?id=' . $bar_class->id . '&contact=friend">friend</a>]</td><td style="border-left: 1px solid #444444;" width="20%" align="center">[<a href="profiles.php?id=' . $bar_class->id . '&contact=enemy">enemy</a>]</td></tr></table>';
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
    $db->query("SELECT username, gang, admin, rmdays, gm, colours, image_name, gradient, gndays, leader, g.tag, formattedTag, prestige, uninfo FROM grpgusers gu LEFT JOIN gangs g ON g.id = gu.gang WHERE gu.id = ?");
    $db->execute(array(
        $id
    ));
    $row = $db->fetch_row(true);
    if ($row['gang'] != 0 and $nogang != 1) {
        $name .= "<a href='viewgang.php?id={$row['gang']}'";
        if ($row['formattedTag'] == "Yes")
            $name .= ($row['leader'] == $id) ? " title='Gang Leader'><font color=grey>[<b>" . gradientTag($row['gang']) . "</b>]</font></a> " : "><font color=grey>[" . gradientTag($row['gang']) . "]</font></a> ";
        else
            $name .= ($row['leader'] == $id) ? " title='Gang Leader'><font color=gold>[<b>{$row['tag']}</b>]</font></a> " : "><font color=white>[{$row['tag']}]</font></a> ";
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
    } else if ($row['pg'] == 1) {
        $title = "Player Guide";
        $whichfont = "#33BB11";
    } else if ($row['rmdays'] >= 1) {
        $title = "VIP ({$row['rmdays']} VIP Days Left)";
        $whichfont = "03C903";
    } else {
        $title = "Not Respected";
        $whichfont = "#CCB8FF";
    }
    if ($bdays)
        $name .= "<a title='$title' href='profiles.php?id=$id'>&nbsp;<font color = '$whichfont'>{$row['username']}</s></font></a>";
    else if (!empty($row['image_name'])) {
        $name .= ($row['admin'] == 1 || $row['pg'] == 1) ? "<a title='" . $title . " [" . $row['username'] . "]' href='profiles.php?id=" . $id . "'>" : "<a title='" . $title . "' href='profiles.php?id=" . $id . "'>";
        $name .= "<img src='{$row['image_name']}' width='95px' height='18px' title='" . $row['username'] . "' />";
        $name .= ($row['admin'] == 1 || $row['pg'] == 1) ? "</a>" : "</a>";
    } else if ($id >= 334 and $id <= 353) {
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
        $name .= ($row['admin'] == 1 || $row['pg'] == 1) ? "<b><i><a title='" . $title . "' href='profiles.php?id=" . $id . "'>" : "<b><a title='" . $title . "' href='profiles.php?id=" . $id . "'>";
        $name .= $gradient;
        $name .= ($row['admin'] == 1 || $row['pg'] == 1) ? "</a></u></b>" : "</a></b>";
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
        $name .= ($row['admin'] == 1 || $row['pg'] == 1) ? "<b><i><a title='" . $title . "' href='profiles.php?id=" . $id . "'>" : "<b><a title='" . $title . "' href='profiles.php?id=" . $id . "'>";
        $name .= $gradient;
        $name .= ($row['admin'] == 1 || $row['pg'] == 1) ? "</a></i></b>" : "</a></b>";
    } else if ($id == 146)
        $name .= "<a title='$title' href='profiles.php?id=$id'>{$row['username']}</a>";
    else if ($row['admin'] == 1 || $row['pg'] == 1)
        $name .= "<i><b><a title='$title' href='profiles.php?id=$id'><font color = '$whichfont'>{$row['username']}</a></font></b></i>";
    else if ($row['rmdays'] > 0)
        $name .= "<b><a title='$title' href='profiles.php?id=$id'><font color='$whichfont'>{$row['username']}</a></font></b>";
    else
        $name .= "<a title='$title' href='profiles.php?id=$id'><font color='$whichfont'>{$row['username']}</a></font>";
    //if($row['prestige'] && $nogang == 0)
    //$name .= " <img src='images/pres.png' title='prestige ({$row['prestige']})' />";
    if ($nogang == 0)
        $m->set('formatName.' . $id, $name, 120);
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
function mission($update)
{
    global $user_class, $db;
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
                $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $db->execute(array(
                    $miss['payKills'],
                    $user_class->id
                ));
                $db->query("INSERT INTO missionlog VALUES('','[x] successfully completed {$miss['name']} objective to get {$miss['kills']} kills,$user_class->id',unix_timestamp())");
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
                $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $db->execute(array(
                    $miss['payBusts'],
                    $user_class->id
                ));
                $db->query("INSERT INTO missionlog VALUES('','[x] successfully completed {$miss['name']} objective to get {$miss['busts']} busts,$user_class->id',unix_timestamp())");
                $db->execute();
                Send_event($user_class->id, "You have completed {$miss['name']} objective to get {$miss['busts']} busts.");
            }
        }
        if ($update == 'c') {
            $db->query("UPDATE missions SET crimes = crimes + WHERE userid = ? AND completed = 'no'");
            $db->execute(array(
                $user_class->id
            ));
            if (++$userMiss['crimes'] == $miss['crimes']) {
                $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $db->execute(array(
                    $miss['payCrimes'],
                    $user_class->id
                ));
                $db->query("INSERT INTO missionlog VALUES('','[x] successfully completed {$miss['name']} objective to get {$miss['crimes']} crimes,$user_class->id',unix_timestamp())");
                $db->execute();
                Send_event($user_class->id, "You have completed {$miss['name']} objective to get {$miss['crimes']} crimes.");
            }
        }
        if ($update == 'm') {
            $db->query("UPDATE missions SET mugs = mugs + 1 WHERE userid = $user_class->id AND completed = 'no'");
            $db->execute(array(
                $user_class->id
            ));
            if (++$userMiss['mugs'] == $miss['mugs']) {
                $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $db->execute(array(
                    $miss['payMugs'],
                    $user_class->id
                ));
                $db->query("INSERT INTO missionlog VALUES('','[x] successfully completed {$miss['name']} objective to get {$miss['mugs']} mugs,$user_class->id',unix_timestamp())");
                $db->execute();
                Send_event($user_class->id, "You have completed {$miss['name']} objective to get {$miss['mugs']} mugs.");
            }
        }
    } else {
        return 1;
    }
    if ($userMiss['kills'] >= $miss['kills'] && $userMiss['crimes'] >= $miss['crimes'] && $userMiss['busts'] >= $miss['busts'] && $userMiss['mugs'] >= $miss['mugs']) {
        $exp = 5 + (5 * $userMiss['mid']);
        $levelhurts = floor($user_class->level / 10);
        $exp = ($exp - $levelhurts < 3) ? 3 : $exp - $levelhurts;
        $expgain = floor($user_class->maxexp * ($exp / 100));
        $db->query("UPDATE grpgusers SET exp = exp + ? WHERE id = ?");
        $db->execute(array(
            $expgain,
            $user_class->id
        ));
        Send_event($user_class->id, "You have completed the {$miss['name']}! [+ $expgain EXP]");
        $db->query("UPDATE missions SET completed = 'successful' WHERE id = ?");
        $db->execute(array(
            $userMiss['id']
        ));
        $db->query("INSERT INTO missionlog VALUES ('','[x] successfully completed their {$miss['name']}.,$user_class->id',unix_timestamp())");
        $db->execute();
    }
    return 1;
}
function bloodbath($att, $id, $amnt = 1)
{
    global $db;
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
    $rtn = '<div class="sidebar-item sidebar-menu">';

    // Energy
    $rtn .= '<ul><li class="header-menu" style="margin-bottom: -6px;margin-top: -5px;">';
    $rtn .= '<span>Energy - ' . $user_class->energypercent . '%</span></li>';
    $rtn .= '<div class="progress" style="height: 5px;width:50% !important;margin: 0 auto !important;">';
    $rtn .= '<div class="progress-bar bg-danger" role="progressbar" style="width: ' . $user_class->energypercent . '%" aria-valuemin="0" aria-valuemax="100"></div>';
    $rtn .= '</div></ul>';

    // Nerve
    $rtn .= '<ul><li class="header-menu" style="margin-bottom: -6px;margin-top: -5px;">';
    $rtn .= '<span>Nerve - ' . $user_class->nervepercent . '%</span></li>';
    $rtn .= '<div class="progress" style="height: 5px;width:50% !important;margin: 0 auto !important;">';
    $rtn .= '<div class="progress-bar bg-danger" role="progressbar" style="width: ' . $user_class->nervepercent . '%" aria-valuemin="0" aria-valuemax="100"></div>';
    $rtn .= '</div></ul>';

    // Awake
    $rtn .= '<ul><li class="header-menu" style="margin-bottom: -6px;margin-top: -5px;">';
    $rtn .= '<span>Awake - ' . $user_class->awakepercent . '%</span></li>';
    $rtn .= '<div class="progress" style="height: 5px;width:50% !important;margin: 0 auto !important;">';
    $rtn .= '<div class="progress-bar bg-danger" role="progressbar" style="width: ' . $user_class->awakepercent . '%" aria-valuemin="0" aria-valuemax="100"></div>';
    $rtn .= '</div></ul>';

    $rtn .= '</div>'; // Closing sidebar-item sidebar-menu div

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
function Give_Pet($petid, $userid, $str = 10, $spe = 10, $def = 10, $name = "No Name")
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
    $db->query("INSERT INTO pets (petid, userid, str, spe, def, pname) VALUES (?, ?, ?, ?, ?, ?)");
    $db->execute(array(
        $petid,
        $userid,
        $str,
        $spe,
        $def,
        $name
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
    $ret = '<div class="flexcont" style="margin:2px;flex-wrap:wrap;">';
    $count = count($rows);
    $leftover = 4 - ($count % 4);
    if ($count < 4)
        $leftover = 0;
    foreach ($rows as $row) {
        $ret .= '<div class="flexele" style="margin:2px;flex-basis:22%;">';
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
    switch ($which) {
        case 'n':
            if ($user_class->nerref == 2) {
                $nerveneeded = $user_class->maxnerve - $user_class->nerve;
                $cost = floor($nerveneeded / 10);
                if ($cost < 10)
                    $cost = 10;
                if ($cost > $user_class->points)
                    return 0;
                $user_class->nerve += $cost * 10;
                $user_class->nerve = ($user_class->nerve > $user_class->maxnerve) ? $user_class->maxnerve : $user_class->nerve;
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
                <td align='center' onclick=\"location.href = 'pms.php?view=new'\">New</td>
                <td align='center' onclick=\"location.href = 'pms.php?view=inbox'\">Inbox</td>
                <td align='center' onclick=\"location.href = 'pms.php?view=outbox'\">Outbox</td>
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
        $glowparts = $uninfo[6];
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
        $title = "GD: {$gndays}, RM: {$donatordays}";
        return "<span title=\"{$title}\" style=\"{$bold}{$italic}{$spacing}\">" . $un . "</span>";
    } else if ($donatordays > 0) {
        $days = "RM: {$donatordays}";
        return "<span class=\"rm\" title=\"$days\">$username</span>";
    } else
        return "<span class=\"user\">$username</span>";
}


function check_number($number)
{
    if (preg_match("/^[0-9]+$/", $number)) {
        return $number;
    } else {
        return -1;
    }
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
?>