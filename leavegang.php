<?php
include 'header.php';
?>
<div class='box_top'>Leave Gang</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $gang_class = new Gang($user_class->gang);
        if ($gang_class->leader == $user_class->id) {
            echo Message("Leaders can't leave their own gang.");
            include("gangheaders.php");
            include 'footer.php';
            die();
        }
        if ($user_class->gang == 0) {
            echo Message("You aren't in a gang.");
            include("gangheaders.php");
            include 'footer.php';
            die();
        }
        echo "

<tr><td class='contentcontent'>Are you sure you wish to leave your gang?<br /><br />
<a href='leavegang.php?x=leave'>Continue</a><br />
<a href='gang.php'>No thanks!</a>
</td>
</tr>";
        if (isset($_GET['x']) && $_GET['x'] == "leave") {
            if ($user_class->weploaned == 1) {
                AddToArmory($user_class->eqweapon, $user_class->gang);
                perform_query("UPDATE grpgusers SET eqweapon = 0, weploaned = 0 WHERE id = ?", [$user_class->id]);
            }
            if ($user_class->armloaned == 1) {
                AddToArmory($user_class->eqarmor, $user_class->gang);
                perform_query("UPDATE grpgusers SET eqarmor = 0, armloaned = 0 WHERE id = ?", [$user_class->id]);
            }
            if ($user_class->shoesloaned == 1) {
                AddToArmory($user_class->eqshoes, $user_class->gang);
                perform_query("UPDATE grpgusers SET eqshoes = 0, shoeloaned = 0 WHERE id = ?", [$user_class->id]);
            }
            if ($user_class->glovesloaned == 1) {
                AddToArmory($user_class->eqgloves, $user_class->gang);
                perform_query("UPDATE grpgusers SET eqgloves = 0, glovesloaned = 0 WHERE id = ?", [$user_class->id]);
            }
            if ($user_class->offhandloaned == 1) {
                AddToArmory($user_class->eqoffhand, $user_class->gang);
                perform_query("UPDATE grpgusers SET eqoffhand = 0, offhandloaned = 0 WHERE id = ?", [$user_class->id]);
            }

            $db->query("SELECT * FROM gang_loans WHERE to = ?");
            $db->execute([$user_class->id]);
            $rows = $db->fetch_row();
            foreach ($rows as $line) {
                AddToArmory($line['item'], $user_class->gang);
                perform_query("DELETE FROM gang_loans WHERE id = ?", [$line['id']]);
            }
            Gang_Event($gang_class->id, "[-_USERID_-] has left the gang.", $user_class->id);
            perform_query("UPDATE grpgusers SET gang = 0, gangwait = 1240, grank = 0, gangmail = 0 WHERE id= ?", [$user_class->id]);
            echo Message("You have left your gang.");
        }
        include("gangheaders.php");
        include "footer.php";
        ?>