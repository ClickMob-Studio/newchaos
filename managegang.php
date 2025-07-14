<?php
include 'header.php';
?>
<div class='box_top'>Manage Members</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $gang_class = new Gang($user_class->gang);
        if ($user_class->gang == 0)
            error("You aren't in a gang.");
        $user_rank = new GangRank($user_class->grank);
        if ($user_class->gangleader != $user_class->id && $user_rank->members < 1)
            error("You don't have permission to be here!");
        if (isset($_POST['submit']) && isset($_POST['rank'])) {
            $target = new User($_POST['id']);
            if ($target->gang != $user_class->gang)
                error("That player is not in your gang!");
            elseif (empty($_POST['rank']))
                error("You didn't choose a rank!");
            else {
                perform_query("UPDATE grpgusers SET grank = ? WHERE id = ?", [$_POST['rank'], $target->id]);
                echo Message("You have changed $target->formattedname's rank.");
                $gang_class = new Gang($user_class->gang);
            }
        }

        $db->query("SELECT lastactive FROM grpgusers WHERE lastactive > unix_timestamp() - 86400 ORDER BY lastactive DESC");
        $db->execute();
        $rows = $db->fetch_row();
        foreach ($rows as $row) {
            $store[] = array(
                'lastactive' => howlongago($row['lastactive'])
            );
        }

        if (isset($_POST['dismiss'])) {
            $target = new User($_POST['id']);
            if ($target->gang != $user_class->gang)
                error("That player is not in your gang!");
            if ($_POST['id'] != $user_class->id)
                if ($_POST['id'] != $gang_class->leader) {
                    perform_query("UPDATE grpgusers SET gang = 0, gangwait = 1240, grank = 0, gangmail = 0 WHERE id = ?", [$_POST['id']]);
                    echo Message("You have kicked $target->formattedname out of the gang.");
                    Gang_Event($gang_class->id, "[-_USERID_-] has been kicked from the gang.", $target->id);
                } else
                    error("You can't kick the leader out of the gang.");
            else
                error("You can't kick yourself out of the gang.");
        }
        print "
<table id='newtables' style='width:100%;'>
    <tr>
        <th colspan='5'>Manage Members</th>
    </tr>
    <tr>
        <th>Mobster</th>
        <th>Rank</th>
        <th>Edit Rank</th>
        <th>Kick Out</th>
        <th>Last Active</th>
    </tr>
";
        $result = mysql_query("SELECT `id` FROM grpgusers WHERE gang = $user_class->gang ORDER BY level DESC");
        while ($line = mysql_fetch_array($result)) {
            $gang_member = new User($line['id']);
            print "
        <tr>
            <td width='15%'>$gang_member->formattedname</td>
                <form method='post'><input type='hidden' name='id' value='$gang_member->id' />
                    <td width='10%'>
                        <select name='rank'>
    ";
            $searchranks = mysql_query("SELECT * FROM ranks WHERE gang = $user_class->gang");
            echo "<option value=''></option>";
            while ($rank = mysql_fetch_array($searchranks))
                echo "<option value='{$rank['id']}'", ($rank['id'] == $gang_member->grank) ? "selected" : "", ">{$rank['title']}</option>";
            echo "
                        </select>
                    </td>
                    <td width='5%'><input type='submit' name='submit' value='Edit Rank' /></td>
                </form>
            <td width='5%'><form method='post'><input type='hidden' name='id' value='$gang_member->id' /><input type='submit' name='dismiss' value='Kick Out' /></form></td>
            <td width='15%'>" . howlongago($gang_member->lastactive) . "</td>
        </tr>
    ";
        }
        print "</table>";
        include("gangheaders.php");
        include 'footer.php';
        function error($text)
        {
            echo Message($text);
            include 'footer.php';
            die();
        }
        ?>