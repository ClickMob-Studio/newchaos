<?php
$perms = array(
    "members" => "Manage Members",
    "crime" => "Manage Gang Crimes & Missions",
    "vault" => "Manage Vault",
    "invite" => "Invite Mobsters",
    "massmail" => "Gang Mail Access",
    "applications" => "Manage Gang Applications",
    "appearance" => "Edit Gang Access",
    "ranks" => "Manage Ranks",
    "houses" => "Gang Housing Access",
    "gforum" => "Gang Forum Powers",
    "upgrade" => "Gang Upgrade",
    "polls" => "Gang Poll Access",
    "gangwars" => "Manage Gang Wars & Rackets",
    "ganggrad" => "Manage Gang Gradient"
);
include 'header.php';
?>
<div class='box_top'>Manage Ranks</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $gang_class = new Gang($user_class->gang);
        if ($user_class->gang == 0)
            diefun("You aren't in a gang.");
        $user_rank = new GangRank($user_class->grank);

        if ($user_class->gangleader != $user_class->id && $user_rank->ranks < 1)
            diefun("You don't have permission to be here!");
        if (isset($_POST['deleterank'])) {
            if (isset($_POST['rank'])) {
                perform_query("DELETE FROM ranks WHERE gang = ? AND id = ?", [$user_class->gang, $_POST['rank']]);
                echo Message("You have deleted that rank.");
            } else
                echo Message("You need to pick a rank first!");
        }
        if (isset($_POST['edit']))
            if (isset($_POST['title']))
                if (strlen($_POST['title'] < 21)) {
                    $_POST['title'] = strip_tags($_POST['title']);
                    $_POST['title'] = str_replace('"', '', $_POST['title']);
                    $_POST['title'] = addslashes($_POST['title']);
                    perform_query("UPDATE ranks SET gangwars = ?, ganggrad = ?, upgrade = ?, title = ?, members = ?, crime = ?, vault = ?, ranks = ?, massmail = ?, applications = ?, appearance = ?, invite = ?, houses = ?, gforum = ?, polls = ?, color = ? WHERE id = ?", [$_POST['gangwars'], $_POST['ganggrad'], $_POST['upgrade'], $_POST['title'], $_POST['members'], $_POST['crime'], $_POST['vault'], $_POST['ranks'], $_POST['massmail'], $_POST['applications'], $_POST['appearance'], $_POST['invite'], $_POST['houses'], $_POST['gforum'], $_POST['polls'], '#' . $_POST['color'], $_POST['id']]);
                    echo Message("The rank {$_POST['title']} has been updated.");
                } else
                    echo Message("Your rank title can only be 20 characters long.");
            else
                echo Message("You didn't enter a rank name.");
        if (isset($_POST['create']))
            if (!empty($_POST['title']))
                if (strlen($_POST['title']) < 21) {
                    $_POST['title'] = addslashes(str_replace('"', '', strip_tags($_POST['title'])));
                    perform_query("INSERT INTO ranks (gang, title, members, crime, vault, ranks, massmail, applications, appearance, invite, houses, upgrade, gforum, polls, gangwars, ganggrad, color) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$user_class->gang, $_POST['title'], $_POST['members'], $_POST['crime'], $_POST['vault'], $_POST['ranks'], $_POST['massmail'], $_POST['applications'], $_POST['appearance'], $_POST['invite'], $_POST['houses'], $_POST['upgrade'], $_POST['gforum'], $_POST['polls'], $_POST['gangwars'], $_POST['ganggrad'], '#' . $_POST['color']]);
                    echo Message("The rank {$_POST['title']} has been created.");
                } else
                    echo Message("Your rank title can only be 20 characters long.");
            else
                echo Message("You didn't enter a rank name.");
        echo "
    <table id='newtables' style='width:50%;'>
        <tr>
            <th colspan='3'>Edit Ranks</th>
        </tr>
        <tr>
            <th>Rank</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        <tr>
            <form method='post'>
                <td width='50%'>
                    <select name='rank'>
                        <option value=''></option>
";

        $db->query("SELECT * FROM ranks WHERE gang = ?");
        $db->execute([$user_class->gang]);
        $ranks = $db->fetch_row();
        foreach ($ranks as $rank) {
            echo "<option value='{$rank['id']}'>{$rank['title']}</option>";
        }
        echo "
                    </select>
                </td>
                <td width='25%'><input type='submit' name='editrank' value='Edit Rank' /></td>
                <td width='25%'><input type='submit' name='deleterank' value='Delete Rank' /></td>
            </form>
        </tr>
    </table>
";
        if (isset($_POST['editrank'])) {
            if (isset($_POST['rank'])) {
                $db->query("SELECT * FROM ranks WHERE gang = ? AND id = ?");
                $db->execute([$user_class->gang, $_POST['rank']]);
                $line = $db->fetch_row(true);
                $rank_spec = new GangRank($line['id'], 1);
                echo "
    <script type='text/javascript' data-cfasync='false' src='js/cp/jscolor.js'></script>
    <table id='newtables' style='width:70%;'>
        <tr>
            <th colspan='4'>Edit Rank</th>
        </tr>
        <form method='post'>
            <tr>
                <th colspan='2'>Rank Title:</th>
                <td colspan='2'><input type='text' name='title' value='$rank_spec->title' size='25' /><input type='hidden' name='id' value='{$_POST['rank']}' /></td>
            </tr>
            <tr>
                <th colspan='2'>Rank Color:</th>
                <td colspan='2'><input type='text' name='color' class='color' value='$rank_spec->color' size='25' /></td>
            </tr>
        ";
                $count = 0;
                foreach ($perms as $perm => $name) {
                    if (($count % 2) == 0)
                        echo "<tr><th style='width:45%;'>$name</th><td style='width:5%;'><input type='checkbox' value='1' name='$perm'", ($rank_spec->$perm == 1) ? " checked" : "", " /></td>";
                    else
                        echo "<td style='width:5%;'><input type='checkbox' value='1' name='$perm'", ($rank_spec->$perm == 1) ? " checked" : "", " /></td><th style='width:45%;'>$name</th></tr>";
                    $count++;
                }
                echo "
            <tr>
                <td colspan='4'><input type='submit' name='edit' value='Edit Rank' /></td>
            </tr>
        </form>
    </table>
";
            } else
                echo Message("You need to select a gang to edit.");
        } else {
            echo "
    <script type='text/javascript' data-cfasync='false' src='js/cp/jscolor.js'></script>
    <table id='newtables' style='width:70%;'>
        <tr>
            <th colspan='4'>Create New Rank</th>
        </tr>
        <form method='post'>
            <tr>
                <th colspan='2'>Rank Title:</th>
                <td colspan='2'><input type='text' name='title' value='' size='25' /></td>
            </tr>
            <tr>
                <th colspan='2'>Rank Color:</th>
                <td colspan='2'><input type='text' name='color' class='color' value='FFFFFF' size='25' /></td>
            </tr>
        ";
            $count = 0;
            foreach ($perms as $perm => $name) {
                if (($count % 2) == 0)
                    echo "<tr><th style='width:45%;'>$name</th><td style='width:5%;'><input type='checkbox' value='1' name='$perm' /></td>";
                else
                    echo "<td style='width:5%;'><input type='checkbox' value='1' name='$perm' /></td><th style='width:45%;'>$name</th></tr>";
                $count++;
            }
            echo "
            <tr>
                <td colspan='4'><input type='submit' name='create' value='Create New Rank' /></td>
            </tr>
        </form>
    </table>
    ";
        }
        include("gangheaders.php");
        include 'footer.php';
        ?>