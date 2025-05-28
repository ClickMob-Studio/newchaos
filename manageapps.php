<?php
include 'header.php'; ?>

<div class='box_top'>Gang Manage Applications</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $gang_class = new Gang($user_class->gang);
        $time = time();
        if ($user_class->gang == 0 && $user_class->admin == 0) {
            echo Message("You're not in a gang.");
            include 'footer.php';
            die();
        }
        if (!isset($_GET['id']))
            $gang = $user_class->gang;
        else
            $gang = $_GET['id'];
        $user_rank = new GangRank($user_class->grank);
        if ($user_rank->applications != 1 && $user_class->admin != 1) {
            echo Message("You don't have permission to be here!");
            include 'footer.php';
            die();
        }
        if (isset($_GET['user']) && $_GET['x'] == 1) {
            $checkapps = mysql_query("SELECT * FROM gangapps WHERE gangid = $gang AND applicant = {$_GET['user']}");
            $result = mysql_num_rows($checkapps);
            if ($result > 0) {
                $app_class = new User($_GET['user']);
                $gangclass = new Gang($gang);
                echo Message("You have accepted this application.");
                $event = "Your application to " . $gangclass->formattedname . " has been accepted.";

                Send_Event($_GET['user'], $event, $gang);

                perform_query("UPDATE grpgusers SET gang = ?, grank = 0 WHERE id = ?", [$gang, $_GET['user']]);
                perform_query("DELETE FROM ganginvites WHERE playerid = ?", [$_GET['user']]);
                perform_query("DELETE FROM gangapps WHERE applicant = ?", [$_GET['user']]);

                Gang_Event($gang_class->id, "[-_USERID_-] has joined the gang.", $_GET['user']);

                perform_query("DELETE FROM gangcontest WHERE userid = ?", [$_GET['user']]);
                perform_query("INSERT INTO gangcontest (userid,gangid) VALUES (?,?)", [$_GET['user'], $gang_class->id]);
            }
        }
        if (isset($_GET['user']) && $_GET['x'] == 0) {
            $checkapps = mysql_query("SELECT * FROM gangapps WHERE gangid = $gang AND applicant = {$_GET['user']}");
            $result = mysql_num_rows($checkapps);
            if ($result > 0) {
                $app_class = new User($_GET['user']);
                echo Message("You have declined this application.");
                $event = "Your application to [-_GANGID_-] has been declined.";
                Send_Event($_GET['user'], $event, $gang);

                perform_query("DELETE FROM gangapps WHERE applicant = ? AND gangid = ?", [$_GET['user'], $gang]);
            }
        }
        $result23 = mysql_query("SELECT * FROM gangapps WHERE gangid = $gang ORDER BY date DESC");
        if (mysql_num_rows($result23)) {
            echo "
<tr><td class='contentcontent'>
    <table id='newtables' style='width:100%;table-layout:fixed;'>
        <tr>
            <th colspan='3'>Manage Applications</th>
        </tr>
        <tr>
            <th>Applicant</th>
            <th>Accept</th>
            <th>Decline</th>
        </tr>
";

            while ($line = mysql_fetch_array($result23)) {
                $gang_app = new User($line['applicant']);
                echo "
        <tr>
            <td>$gang_app->formattedname</td>
            <td><a href='manageapps.php?user={$gang_app->id}&x=1'>Accept</a></td>
            <td><a href='manageapps.php?user={$gang_app->id}&x=0'>Decline</a></td></td>
        </tr>
    ";
            }
        } else {
            echo "No applications found";
        }
        print "</table>";
        include("gangheaders.php");
        include 'footer.php';
        ?>