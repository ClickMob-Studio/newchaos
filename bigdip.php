<?php
include 'header.php';

if ($user_class->prestige < 3) {
    echo Message("You need to be prestige 3 to be here!.");
    include 'footer.php';
    die();
}


echo '<h3>Big Dip</h3>';
echo '<hr>';
echo '<tr><td class="contentcontent">';
if ($_GET['dip'] == 1) {
    if ($user_class->luckydip2 == 1) {
        if ($user_class->money >= 1000000) {
            $randnum = rand(0, 10000000);
            if ($randnum != 0) {
                echo "You dipped into the bag and pulled out $" . prettynum($randnum) . "!<br /><br />";
                echo "<a href='bigdip.php'>Go Back</a>";
                $newmoney = $user_class->money + $randnum - 1000000;
                perform_query("UPDATE `grpgusers` SET `money` = ?, `luckydip2` = '0' WHERE `id`= ?", [$newmoney, $user_class->id]);
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
if ($user_class->luckydip2 == 1) {
    echo "Welcome to the lucky dip. It costs $1,000,000 to play and you could win up to $10,000,000!<br /><br />";
    echo "<a href='bigdip.php?dip=1'>Dip It</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='city.php'>No Thanks!</a>";
    echo '</td></tr>';
} else {
    echo "You Have Already Played The Big Dipper Today.<br /><br />";
    echo "<a href='city.php'>Go Back</a>";
    include("footer.php");
    die();
}
include 'footer.php';
?>