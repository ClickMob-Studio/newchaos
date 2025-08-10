<?php
include "header.php";
?>

<div class='box_top'>Numbers Game</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $db->query("SELECT * FROM `grpgusers` WHERE `id` = ? LIMIT 1");
        $db->execute([$_SESSION['id']]);
        $ir = $db->fetch_row(true);

        if ($ir['jail'] or $ir['hospital']) {
            die("<br />This page is unavailable while in hospital or jail.<br /><br /><hr width=80%> <a href='index.php'>Home</a><hr width=80%>");
        }

        $db->query("SELECT * FROM numbergame WHERE userid = ?");
        $db->execute([$user_class->id]);
        $games = $db->fetch_row();
        $no2 = count($games);

        if (!isset($_GET['take'])) {
            print "<b>Mini Competition! Current Prize: 1,000 Points<br />Guess a number from 1 to 25<br />You will recieve a event if you win.</b><br /><br /><table width='30%' cellspacing=1 class='myTable'><tr><td class='contenthead' align='center'>Number</td><td class='contenthead' align='center'>Player</td></tr>";
            $db->query("SELECT * FROM numbergame ORDER BY number ASC");
            $results = $db->fetch_row();
            foreach ($results as $r) {
                $usersid = ($r['userid']);
                $db->query("SELECT * FROM grpgusers WHERE id = ?");
                $db->execute([$usersid]);
                $u = $db->fetch_row(true);
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
            }

        } else {
            $_GET['take'] = abs((int) $_GET['take']);
            $getid = $_GET['take'];

            $db->query("SELECT * FROM numbergame WHERE number = ?");
            $db->execute([$getid]);
            $nogame = $db->fetch_row(true);
            if ($nogame['userid'] != 0) {
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