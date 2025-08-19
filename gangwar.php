<?php
include 'header.php';
?>
<div class='box_top'>Gang War</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if ($user_class->gang != 0) {
            $gang = new Gang($user_class->gang);
            $user_rank = new GangRank($user_class->grank);
            if ($user_rank->gangwars != 1)
                diefun("You do not have permission to be here.");
            if (isset($_POST['invite'])) {
                security($_POST['id']);
                security($_POST['cash']);
                $to = $_POST['id'];
                $cash = $_POST['cash'];

                if ($to == $user_class->gang)
                    diefun("You can't invite yourself to a gang war.");
                if ($cash > $gang->moneyvault)
                    diefun("You don't have enough money in your gang vault to make that bet.");
                if ($cash < 1)
                    diefun("Please enter a valid cash amount.");
                if ($cash < 10000)
                    diefun("You have to bet at least $10,000.");
                if ($cash > 5000000)
                    diefun("You can only bet a maximum of $5,000,000.");

                $db->query("SELECT * FROM gangwars WHERE (gang1 = ? OR gang2 = ?) AND accepted = 1");
                $db->execute([$to, $to]);
                $r = $db->fetch_row();
                $check1 = count($r);
                if ($check1 > 0)
                    diefun("That gang is already at war with someone.");

                $db->query("SELECT * FROM gangs WHERE id = ?");
                $db->execute([$to]);
                $r = $db->fetch_row(true);
                $check2 = count($r);
                if ($check2 == 0)
                    diefun("That gang doesn't exist.");

                $db->query("SELECT * FROM gangwars WHERE (gang1 = ? OR gang2 = ?) AND accepted = 1");
                $db->execute([$user_class->gang, $user_class->gang]);
                $r = $db->fetch_row();
                $check3 = count($r);
                if ($check3 > 0)
                    diefun("Your gang is already at war with someone.");

                $invited = new Gang($to);
                perform_query("INSERT INTO gangwars (gang1, gang2, bet, timesent) VALUES (?, ?, ?, unix_timestamp())", [$user_class->gang, $to, $cash]);
                $gang->moneyvault -= $cash;
                perform_query("UPDATE gangs SET moneyvault = ? WHERE id = ?", [$gang->moneyvault, $user_class->gang]);
                Send_Event($invited->leader, "You have a new gang war invitation.", $invited->leader);
                echo Message("You have successfully invited $invited->formattedname to a gang war.");
            }

            if (isset($_GET['accept'])) {
                $war = CheckGangWar($user_class->gang);
                if ($war == 0) {
                    $db->query("SELECT * FROM gangwars WHERE accepted = 0 AND gang2 = ?");
                    $db->execute([$user_class->gang]);
                    $results = $db->fetch_row();
                    if (!empty($results)) {
                        $db->query("SELECT * FROM gangwars WHERE gang2 = ? AND warid = ?");
                        $db->execute([$user_class->gang, $_GET['accept']]);
                        $warnumber = $db->fetch_row(true);
                        if (empty($warnumber)) {
                            echo Message("You can't accept this gang war invitation.");
                        } else {
                            if ($warnumber['bet'] <= $gang->moneyvault) {
                                perform_query("UPDATE gangwars SET accepted = 1, timesent = unix_timestamp(), timeending = unix_timestamp() + 432000 WHERE warid = ? AND gang2 = ?", [$_GET['accept'], $user_class->gang]);
                                perform_query("DELETE FROM gangwars WHERE accepted = 0 AND (gang1 = ? OR gang2 = ?) AND warid != ?", [$user_class->gang, $user_class->gang, $_GET['accept']]);

                                $db->query("SELECT `id` FROM grpgusers WHERE gang = ?");
                                $db->execute([$user_class->gang]);
                                $users = $db->fetch_row();
                                foreach ($users as $user) {
                                    Send_Event($user['id'], formatName($user_class->id) . " has accepted a gang war invitation. Your gang is now at war with [-_GANGID_-] for 5 days!", $warnumber['gang1']);
                                }

                                $db->query("SELECT `id` FROM grpgusers WHERE gang = ?");
                                $db->execute([$warnumber['gang1']]);
                                $users = $db->fetch_row();
                                foreach ($users as $user) {
                                    Send_Event($user['id'], formatName($user_class->id) . " sent a gang war invitation to [-_GANGID_-] and they accepted! You are now at war with them for 5 days!", $user_class->gang);
                                }


                                $gang->moneyvault -= $warnumber['bet'];
                                perform_query("UPDATE gangs SET moneyvault = ? WHERE id = ?", [$gang->moneyvault, $user_class->gang]);
                                echo Message("You have accepted the gang war invitation. Get attacking those gang members!");
                            } else {
                                echo Message("You don't have enough money in your gang vault to make that bet.");
                            }
                        }
                    } else {
                        echo Message("This isn't a real invitation.");
                    }
                } else {
                    echo Message("Your gang is already at war with someone.");
                }
            }

            if (isset($_GET['decline'])) {
                $war = CheckGangWar($user_class->gang);
                if ($war == 0) {
                    $db->query("SELECT * FROM gangwars WHERE accepted = 0 AND gang2 = ? AND warid = ?");
                    $db->execute([$user_class->gang, $_GET['decline']]);
                    $result = $db->fetch_row(true);
                    if (!empty($result)) {
                        Send_Event($result['gang1'], "Your gang war invitation to [-_GANGID_-] has been declined.", $user_class->gang);
                        perform_query("DELETE FROM gangwars WHERE warid = ? AND gang2 = ? LIMIT 1", [$_GET['decline'], $user_class->gang]);
                        $theirgang = new Gang($result['gang1']);
                        $theirgang->moneyvault += $result['bet'];
                        perform_query("UPDATE gangs SET moneyvault = ? WHERE id = ?", [$theirgang->moneyvault, $theirgang->id]);
                        echo Message("You have declined the gang war invitation.");
                    } else
                        echo Message("This isn't a real invitation.");
                } else
                    echo Message("Your gang is already at war with someone.");
            }

            $db->query("SELECT * FROM gangwars WHERE accepted = 0 AND gang2 = ? ORDER BY timesent DESC");
            $db->execute([$user_class->gang]);
            $results = $db->fetch_row();
            $war = CheckGangWar($user_class->gang);
            if ($war != 0)
                print "
<tr><td class='contentspacer'></td></tr><tr><td class='contenthead'>Already At War</td></tr>
<tr><td class='contentcontent'>
<center>
You are currently at war with someone! Click <a href='viewwar.php'>here</a> to view your progress.
</center>
</td></tr>
        ";
            $war = CheckGangWar($user_class->gang);
            if ($war == 0) {
                print "
<center><a href='gangwarguide.php'><button class='ycbutton'>Click here to view the Gang War guide.</button></a></center><br />
<tr><td class='contentspacer'></td></tr><tr><td class='contenthead'>Gang War Invitations</td></tr>
<tr><td class='contentcontent'>
        ";
                if (!empty($results)) {
                    print "
    <table width='100%'>
        <tr>
            <td><b>From</b></td>
            <td><b>Bet [cash]</b></td>
            <td><b>Accept</b></td>
            <td><b>Decline</b></td>
        </tr>
            ";
                    foreach ($results as $line) {
                        $gang_invite = new Gang($line['gang1']);
                        echo "<tr><td width='45%'>$gang_invite->formattedname</td><td width='25%'>" . prettynum($line['bet'], 1) . "</td><td width='15%'><a href='gangwar.php?accept={$line['warid']}'>Accept War</a></td><td width='15%'><a href='gangwar.php?decline={$line['warid']}'>Decline War</a></td></tr>";
                    }
                    echo "</table>";
                } else
                    echo "You don't have any current gang war invitations.";
                print "</td></tr>";
            }
            if ($war == 0) {
                print "
<tr><td class='contentspacer'></td></tr><tr><td class='contenthead'>Invite Gang to War With</td></tr>
<tr><td class='contentcontent'>
    <table width='50%'>
        <form method='post'>
            <tr>
                <td width='10%'><b>Gang:</b></td>
                <td><input type='text' name='id' size='6' value='{$_GET['gang']}' /> [ID]</td>
            </tr>
            <tr>
                <td width='10%'><b>Bet:</b></td>
                <td><input type='text' name='cash' size='10' /> [cash]</td>
            </tr>
            <tr>
                <td width='10%'>&nbsp;</td>
                <td><input type='submit' name='invite' value='Invite Gang' /></td>
            </tr>
        </form>
    </table>
</td></tr>
        ";
            }
        } else
            echo Message("You aren't in a gang.");

        include("gangheaders.php");
        include 'footer.php';
        ?>