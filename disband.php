<?php
include 'header.php';
?>
<div class='box_top'>Disband Gang</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $gang_class = new Gang($user_class->gang);

        if ($user_class->gang == 0) {
            echo Message("You aren't in a gang.");
            include("footer.php");
            die();
        }
        if ($gang_class->leader == $user_class->id) {
            if (CheckGangWar($user_class->gang)) {
                echo Message("You can't delete your gang while your at war.");
                include("footer.php");
                die();
            }
            echo "
<tr><td class='contentspacer'></td></tr><tr><td class='contenthead'>Delete Gang</td></tr>
<tr><td class='contentcontent'>This action will permanently delete the gang. Anything left in vaults or armories will also be deleted!<br /><br />
<a href='disband.php?x=delete'>Continue</a><br />
<a href='gang.php'>No thanks!</a>
</td>
</tr>";
            if (isset($_GET['x']) && $_GET['x'] == "delete") {
                $atawr = CheckGangWar($user_class->gang);
                if ($atwar == 1) {
                    echo Message("You can't delete your gang while your at war.");
                    include("footer.php");
                    die();
                }

                perform_query("UPDATE `grpgusers` SET `gangleader` = '0' WHERE `id` = ?", [$gang_class->leader]);
                perform_query("UPDATE `grpgusers` SET `gang` = '0', `grank` = '0' WHERE `gang` = ?", [$gang_class->id]);
                perform_query("DELETE FROM `gangs` WHERE `id` = ?", [$gang_class->id]);
                perform_query("DELETE FROM `gangarmory` WHERE `gangid` = ?", [$gang_class->id]);
                perform_query("DELETE FROM `gangmail` WHERE `gangid` = ?", [$gang_class->id]);
                perform_query("DELETE FROM `ranks` WHERE `gang` = ?", [$gang_class->id]);
                perform_query("DELETE FROM `ganginvites` WHERE `gangid` = ?", [$gang_class->id]);
                perform_query("DELETE FROM `gang_loans` WHERE `gang` = ?", [$gang_class->id]);

                $db->query("SELECT * FROM `grpgusers` WHERE `gang` = ?");
                $db->execute([$user_class->gang]);
                $result = $db->fetch_row();
                foreach ($result as $line) {
                    $gang_user = new User($line['id']);
                    if ($gang_user->weploaned == 1) {
                        perform_query("UPDATE `grpgusers` SET `eqweapon` = '0', `weploaned` = '0' WHERE `id` = ?", [$line['id']]);
                    }
                    if ($gang_user->armorloaned == 1) {
                        perform_query("UPDATE `grpgusers` SET `eqarmor` = '0', `armloaned` = '0' WHERE `id` = ?", [$line['id']]);
                    }
                    if ($gang_user->shoesloaned == 1) {
                        perform_query("UPDATE `grpgusers` SET `eqshoes` = '0', `shoeloaned` = '0' WHERE `id` = ?", [$line['id']]);
                    }
                }
                echo Message("Your gang has been permanently deleted.");
            }
        } else {
            echo Message("You do not have authorization to be here.");
            include 'footer.php';
            die();
        }

        include("gangheaders.php");
        include 'footer.php';
