<?php
include 'header.php';
$error = 0;
if ($_GET['action'] == "shoot" && $_GET['n'] > 0 && $_GET['n'] < 5) {
    if ($user_class->roulette == 1) {
        if ($user_class->hospital == 0) {
            if ($user_class->jail == 0) {
                $random1 = rand($_GET['n'], 5);
                $random2 = rand($_GET['n'], 5);
                if ($random1 == $random2) { // Hospital
                    $hosp = 300;
                    perform_query("UPDATE `grpgusers` SET hospital = ?, `roulette` = '0', `hhow` = 'roulette' WHERE `id` = ?", [$hosp, $user_class->id]);
                    echo Message("The bullet was in your cylinder and you were sent to hospital for 5 minutes!");
                } else { // Give Cash
                    if ($_GET['n'] == 1) {
                        $random3 = rand(0, 15000);
                    } else if ($_GET['n'] == 2) {
                        $random3 = rand(0, 30000);
                    } else if ($_GET['n'] == 3) {
                        $random3 = rand(0, 45000);
                    } else if ($_GET['n'] == 4) {
                        $random3 = rand(0, 60000);
                    }
                    $newmoney = $user_class->money + $random3;
                    perform_query("UPDATE `grpgusers` SET money = ?, `roulette` = '0' WHERE `id` = ?", [$newmoney, $user_class->id]);
                    echo Message("Success! You were granted $" . prettynum($random3) . " for your bravery!");
                }
            } else {
                if ($error = 0) {
                    echo Message("You can't play The Boom Chamber if your in prison!");
                }
            }
        } else {
            if ($error = 0) {
                echo Message("You can't play The Boom Chamber if your in hospital!");
            }
        }
    } else {
        if ($error = 0) {
            echo Message("You have already played The Boom Chamber today!");
        }
    }
}
?>
<?php
if ($user_class->roulette == 1) {
    if ($user_class->hospital == 0) {
        if ($user_class->jail == 0) {
            ?>
            <tr>
                <td class="contentspacer"></td>
            </tr>
            <tr>
                <td class="contenthead">The BOOM Chamber</td>
            </tr>
            <tr>
                <td class="contentcontent">
                    Welcome to The Boom Chamber! Its so easy to play, all you have to do is pick how many bullets you would like to
                    play with! If you live you will be granted a prize. And remember, the more bullets, the higher the prize!
                    However if your shot you will goto hospital for 5 minutes!<br /><br />
                    <a href="thechamber.php?action=shoot&n=1">Use 1 Bullet</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
                        href="thechamber.php?action=shoot&n=2">Use 2 Bullets</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
                        href="thechamber.php?action=shoot&n=3">Use 3 Bullets</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a
                        href="thechamber.php?action=shoot&n=4">Use 4 Bullets</a>&nbsp;&nbsp;|&nbsp;&nbsp;
                </td>
            </tr>
            </td>
            </tr>
            <?php
            include 'footer.php';
            die();
        } else {
            echo Message("You can't play The Boom Chamber if your in prison!");
            $error = 1;
        }
    } else {
        echo Message("You can't play The Boom Chamber if your in hospital!");
        $error = 1;
    }
} else {
    echo Message("You have already played The Boom Chamber today!");
    $error = 1;
}
include 'footer.php';
?>