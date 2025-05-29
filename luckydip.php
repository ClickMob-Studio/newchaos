<?php
include 'header.php';
?>

<div class='box_top'>Lucky Dip</div>
<div class='box_middle'>
    <div class='pad'>
        <?php

        echo '<tr><td class="contentcontent">';
        if ($_GET['dip'] == 1) {
            if ($user_class->luckydip == 1) {
                if ($user_class->money >= 10000) {
                    $randnum = rand(0, 1000000);
                    if ($randnum != 0) {
                        echo "You dipped into the bag and pulled out $" . prettynum($randnum) . "!<br /><br />";
                        echo "<a href='luckydip.php'>Go Back</a>";
                        $newmoney = $user_class->money + $randnum - 10000;
                        perform_query("UPDATE `grpgusers` SET `money` = ?, `luckydip` = '0' WHERE `id` = ?", [$newmoney, $user_class->id]);
                        include("footer.php");
                        die();
                    } else {
                        echo "Unfortunately you did't get anything from your lucky dip! Better luck next time.<br /><br />";
                        echo "<a href='city.php'>Go Back</a>";
                    }
                } else {
                    echo "You don't have enough money to play the lucky dip.<br /><br />";
                    echo "<a href='city.php'>Go Back</a>";
                    include("footer.php");
                    die();
                }
            } else {
                echo "You have already played the lucky dip today. Please come back tommorow.<br /><br />";
                echo "<a href='city.php'>Go Back</a>";
                include("footer.php");
                die();
            }
        }
        if ($user_class->luckydip == 1) {
            echo "Welcome to the lucky dip. It costs $10,000 to play and you could win up to $1,000,000!<br /><br />";
            echo "<a href='luckydip.php?dip=1'>Dip It</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='city.php'>No Thanks!</a>";
            echo '</td></tr>';
        } else {
            echo "You Have Already Played Lucky Dip Today.<br /><br />";
            echo "<a href='city.php'>Go Back</a>";
            include("footer.php");
            die();
        }


