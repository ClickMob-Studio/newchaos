<?php
include 'header.php';

//Send_Event(1, 'Spend Points loaded by ' . $user_class->id, 1);
//Send_Event(2, 'Spend Points loaded by ' . $user_class->id, 2);

if (isset($_GET['spend']) && $_GET['spend'] == "energy") {
    if ($user_class->points >= 10) {
        if ($user_class->energy == $user_class->maxenergy) {
            echo Message("Your energy is already full up!");
        } else {
            $newpoints = $user_class->points - 10;
            $result = mysql_query("UPDATE `grpgusers` SET `energy` = '" . $user_class->maxenergy . "', `points`='" . $newpoints . "' WHERE `id`='" . $_SESSION['id'] . "'");
            echo Message("You spent 10 points and refilled your energy.");
        }
    } else {
        echo Message("You don't have enough points to do that.");
    }
}
//if ($_GET['spend'] == "nerve") {
//
//    if ($user_class->points >= 10) {
//        if ($user_class->nerve == $user_class->maxnerve) {
//            echo Message("Your nerve is already full up!");
//        } else {
//            $newpoints = $user_class->points - 10;
//            $newnerve = ($user_class->maxnerve > 100) ? $user_class->nerve + 100 : $user_class->maxnerve;
//            $newnerve = ($newnerve > $user_class->maxnerve) ? $user_class->maxnerve : $newnerve;
//            $result = mysql_query("UPDATE `grpgusers` SET `nerve` = '" . $newnerve . "', `points`='" . $newpoints . "' WHERE `id`='" . $_SESSION['id'] . "'");
//            echo Message("You spent 10 points and refilled your nerve.");
//        }
//    } else {
//        echo Message("You don't have enough points to do that.");
//    }
//}



if (isset($_GET['spend']) && $_GET['spend'] == "bail") {
    if ($user_class->points >= 10) {
        if ($user_class->jail == 0) {
            echo Message("You're not in jail!");
        } else {
            $newpoints = $user_class->points - 10;
            $newjail = 0;
            $newnerve = ($newnerve > $user_class->maxnerve) ? $user_class->maxnerve : $newnerve;
            $result = mysql_query("UPDATE `grpgusers` SET `jail` = '" . $newjail . "', `points`='" . $newpoints . "' WHERE `id`='" . $_SESSION['id'] . "'");
            echo Message("You spent 10 points and bailed yourself from jail.");
        }
    } else {
        echo Message("You don't have enough points to do that.");
    }
}



if (isset($_GET['spend']) &&  $_GET['spend'] == "awake") {
    if ($user_class->awakepercent == 100) {
        echo Message("Your awake is already full up!");
    } else if ($user_class->points == 0) {
        echo Message("You don't have enough points to do that.");
    } else {
        $points_to_use = $user_class->points;
        $points_to_use = floor(($points_to_use > (100 - $user_class->awakepercent)) ? (100 - $user_class->awakepercent) : $points_to_use);
        $awake_to_digits = floor($user_class->directmaxawake * ($points_to_use / 100));
        $newawake = floor($user_class->directawake + $awake_to_digits);
        $newawake = ($newawake > $user_class->directmaxawake) ? $user_class->directmaxawake : $newawake;
        $newpoints = $user_class->points - $points_to_use;
        $result = mysql_query("UPDATE `grpgusers` SET `awake` = '" . $newawake . "', `points`='" . $newpoints . "' WHERE `id`='" . $user_class->id . "'");
        echo Message("You have refilled your awake by " . $points_to_use . "%.");
    }
}
//Admin Section
if (isset($_GET['admin']) && $_GET['admin'] == "hosp") {
    if ($user_class->admin == 1) {
        if ($user_class->hospital == 0) {
            echo Message("You're not in the hospital.");
        } else {
            $result = mysql_query("UPDATE `grpgusers` SET `hospital` = '0' AND `hp` = '" . $user_class->puremaxhp . "' WHERE `id`='" . $_SESSION['id'] . "'");
            echo Message("You used your corruption powers to get out of hospital.");
        }
    }
}
if (isset($_GET['admin']) && $_GET['admin'] == "prison") {
    if ($user_class->admin == 1) {
        if ($user_class->jail == 0) {
            echo Message("You're not in prison.");
        } else {
            $result = mysql_query("UPDATE `grpgusers` SET `jail` = '0' WHERE `id`='" . $_SESSION['id'] . "'");
            echo Message("You used your corruption powers to get out of prison.");
        }
    }
}
if (isset($_GET['admin']) &&  $_GET['admin'] == "energy") {
    if ($user_class->admin == 1) {
        if ($user_class->energy == $user_class->maxenergy) {
            echo Message("Your energy is already full.");
        } else {
            $result = mysql_query("UPDATE `grpgusers` SET `energy` = '$user_class->maxenergy' WHERE `id`='" . $_SESSION['id'] . "'");
            echo Message("You used your corruption powers to refill your energy.");
        }
    }
}
if (isset($_GET['admin']) && $_GET['admin'] == "nerve") {
    if ($user_class->admin == 1) {
        if ($user_class->nerve == $user_class->maxnerve) {
            echo Message("Your nerve is already full.");
        } else {
            $result = mysql_query("UPDATE `grpgusers` SET `nerve` = '$user_class->maxnerve' WHERE `id`='" . $_SESSION['id'] . "'");
            echo Message("You used your corruption powers to refill your energy.");
        }
    }
}
if (isset($_GET['admin']) && $_GET['admin'] == "awake") {
    if ($user_class->admin == 1) {
        if ($user_class->awake == $user_class->maxawake) {
            echo Message("Your awake is already full.");
        } else {
            $result = mysql_query("UPDATE `grpgusers` SET `awake` = '$user_class->maxawake' WHERE `id`='" . $_SESSION['id'] . "'");
            echo Message("You used your corruption powers to refill your awake.");
        }
    }
}
if (isset($_GET['admin']) && $_GET['admin'] == "money") {
    if ($user_class->admin == 1) {
        $newpoints = $user_class->points - 1;
        $newmoney = $user_class->money + 1000;
        $result = mysql_query("UPDATE `grpgusers` SET `points` = '$newpoints', `money` = '$newmoney' WHERE `id`='" . $_SESSION['id'] . "'");
        echo Message("You used your corruption powers to get some quick cash.");
    }
}
?>
<tr><td class="contentspacer"></td></tr><tr><td class="contenthead">Point Shop</td></tr>
<tr><td class="contentcontent">
        <?php if ($user_class->admin == 1) { ?>
            Welcome to the Admin Point Shop, here you can refill your energy etc for corruption purposes.<br /><br />
            <a href='spendpoints.php?admin=energy'>Refill Energy</a><br />
            <a href='spendpoints.php?admin=awake'>Refill Awake</a><br />
            <a href='spendpoints.php?admin=nerve'>Refill Nerve</a><br />
            <a href='spendpoints.php?admin=hosp'>Get Out of Hospital</a><br />
            <a href='spendpoints.php?admin=prison'>Get Out of Prison</a><br />
            <a href='spendpoints.php?admin=money'>Sell One Points For $1,000</a><br />
        <?php } else { ?>
            Welcome to the Point Shop, here you can spend your points on various things.<br /><br />
            <a href='spendpoints.php?spend=energy'>Refill Energy</a> - 10 Points<br />
            <a href='spendpoints.php?spend=awake'>Refill Awake</a> - 1 points [per 1%]<br />
            <a href='spendpoints.php?spend=nerve'>Refill Nerve</a> - 10 Points [100 nerve max]<br />
        <?php } ?>
    </td></tr>
<?php
include 'footer.php';
?>