<?php
include 'header.php'; ?>

<div class='box_top'>Pet Jial</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if (isset($_GET['bail'])) {
            security($_GET['bail']);
            $pet_class = new Pet($_GET['bail']);
            if (!$pet_class->jail)
                diefun("Your pet is not in jail");
            if ($user_class->points < 5)
                diefun("You don't have enough points");

            perform_query("UPDATE grpgusers SET points = points - 5 WHERE id = ?", [$user_class->id]);
            perform_query("UPDATE pets SET jail = 0 WHERE userid = ?", [$user_class->id]);
            echo Message("You've posted your bail");
        }
        if (!empty($_GET['jailbreak'])) {
            security($_GET['jailbreak']);
            if (empty(mysql_fetch_array(mysql_query("SELECT userid FROM pets WHERE userid = $user_class->id"))))
                diefun("You can't bust a pet if you don't have one yourself");
            $pet_class = new Pet($user_class->id);
            if ($pet_class->nerve < 5)
                diefun($pet_class->formatName() . " doesn't have enough nerve.");
            if ($pet_class->jail < 0)
                diefun($pet_class->formatName() . " is in the pound.");
            if ($pet_class->hospital < 0)
                diefun($pet_class->formatName() . " is in the hospital.");
            if ($_GET['jailbreak'] == $user_class->id)
                diefun("You can't bust out your own pet");
            $q = mysql_query("SELECT petid FROM pets WHERE userid = {$_GET['jailbreak']}");
            if (!mysql_num_rows($q))
                diefun("That pet doesn't exist");
            $pet = new Pet($_GET['jailbreak']);
            if (!$pet->jail)
                diefun($pet->formatName() . " isn't in jail");
            $chance = rand(1, 100);
            if ($chance <= 92) {
                perform_query("UPDATE pets SET exp = exp + 500, busts = busts + 1, nerve = nerve - 5 WHERE id = ?", [$pet_class->id]);
                perform_query("UPDATE pets SET jail = 0 WHERE userid = ?", [$_GET['jailbreak']]);
                Send_Event($_GET['jailbreak'], $pet->formatName() . " has been busted out of prison by " . $pet_class->formatName() . ".");
                echo Message($pet_class->formatName() . " successfully busted " . $pet->formatName() . " out of the pet pound.<br />They received 500 exp!");
            } elseif ($chance <= 96) {
                perform_query("UPDATE pets SET jail = jail + 300, nerve = nerve - 5 WHERE id = ?", [$pet_class->id]);
                echo Message($pet_class->formatName() . " tried to bust " . $pet->formatName() . " out of the pound but was caught.<br />" . $pet_class->formatName() . " was hauled off to the pound for 5 minutes.");
            } else {
                perform_query("UPDATE pets SET nerve = nerve - 10 WHERE id = ?", [$pet_class->id]);
                echo Message($pet_class->formatName() . " tried to bust " . $pet->formatName() . " out of the pound but failed.");
            }
        }
        include 'includepet.php';
        echo '<center>
<table id="newtables">
    <tr>
        <th colspan="4">Pet Pound</th>
    </tr>
    <tr>
        <th>Pet Name</th>
        <th>Owner</th>
        <th>Time Left</th>
        <th>Actions</th>
    </tr>';
        $q = mysql_query("SELECT userid FROM pets WHERE jail > 0 ORDER BY jail DESC");
        if (!mysql_num_rows($q))
            echo "<tr><td colspan='4' class='center'>There are no pets in the pound</td></tr>";
        else {
            while ($row = mysql_fetch_array($q)) {
                $pet = new Pet($row['userid']);
                $links = ($row['userid'] == $user_class->id) ? "[<a href='petjail.php?bail={$pet->userid}'>Bail Out (5 Points)</a>]" : "[<a href='petjail.php?jailbreak={$pet->userid}'>bust</a>]";
                echo "
            <tr>
                <td>", $pet->formatName(), "</td>
                <td>", formatName($row['userid']), "</td>
                <td>", ceil($pet->jail / 60), " Mins</td>
                <td>$links</td>
            </tr>
        ";
            }
        }
        ?>
        </table>
        </center>
        </td>
        </tr><?php
        include 'footer.php';