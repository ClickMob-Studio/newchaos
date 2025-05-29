<?php
include "header.php";
?>

<div class='box_top'>Numbers Game</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $iir = mysql_query("SELECT * FROM `grpgusers` WHERE `id` = '{$_SESSION['id']}' LIMIT 1") or mysql_error();
        $ir = mysql_fetch_array($iir);
        $_GET['ID'] = abs((int) $_GET['ID']);
        $userid = $_SESSION['id'];
        if ($ir['jailtime'] or $ir['hospital']) {
            die("<br />This page is unavailable while in hospital or jail.<br /><br /><hr width=80%> <a href='index.php'>Home</a><hr width=80%>");
        }
        $nogame2 = mysql_query("SELECT * FROM numbergame WHERE userid=$user_class->id");
        $no2 = mysql_num_rows($nogame2);

        $_GET['take'] = abs((int) $_GET['take']);
        $getid = $_GET['take'];
        if (!$getid) {
            print "<b>Mini Competition! Current Prize: 1,000 Points<br />Guess a number from 1 to 25<br />You will recieve a event if you win.</b><br /><br /><table width='30%' cellspacing=1 class='myTable'><tr><td class='contenthead' align='center'>Number</td><td class='contenthead' align='center'>Player</td></tr>";
            $q = mysql_query("SELECT * FROM numbergame ORDER BY number ASC") or mysql_error();
            while ($r = mysql_fetch_array($q)) {
                $usersid = ($r['userid']);
                $user = mysql_query("SELECT * FROM grpgusers WHERE id={$r['userid']}");
                #$user=mysql_query("SELECT * FROM grpgusers WHERE id=$user_class->id");
                $u = mysql_fetch_array($user);
                if ($r['userid'] == 0) {
                    if ($no2 > 0) {
                        $u['username'] = "-";
                    } else {
                        $u['username'] = "[<a href='numbergame.php?take={$r['number']}'>select</a>]";
                    }
                } else {
                    $u['username'] = "<a href='profiles.php?id={$u['id']}'>{$u['username']}</a>";
                }
                print "\n<tr align=center><td class=contentcontent>{$r['number']}</td><td class=contentcontent>{$u['username']}</td></tr>";
                $current_row = 1 - $current_row;
            }
        } else {
            $nogame = mysql_query("SELECT * FROM numbergame WHERE number=$getid");
            $no = mysql_fetch_row($nogame);
            if ($no['userid'] != 0) {
                die("<br />This number is already taken.<br /><br /><hr width=80%> <a href='numbergame.php'>Number Game</a><hr width=80%>");
            }
            if ($no2 > 0) {
                die("<br />You have already chosen a number.<br /><br /><hr width=80%> <a href='numbergame.php'>Number Game</a><hr width=80%>");
            }
            perform_query("UPDATE numbergame SET userid = ? WHERE number = ?", [$user_class->id, $getid]);
            print "<br />You have taken the number $getid<br /><br /><hr width=80%> <a href='numbergame.php'>Number Game</a><hr width=80%>";
        }
        ?>
        </table>
        <?php
        include 'footer.php';
        ?>