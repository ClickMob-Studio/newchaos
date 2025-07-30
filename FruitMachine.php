<?php
include "header.php";
?>

<h1>Slot Machine</h1>
<div class='container my-4'>
    <?php
    if (isset($_POST['bet']))
        security($_POST['bet'], 'num');
    if (isset($_POST['NumberOfSpins']))
        security($_POST['NumberOfSpins'], 'num');
    if (isset($_POST['automoney']))
        security($_POST['automoney'], 'num');
    $MAX_LEVEL_FOR_BETS = 30;
    $LEVEL_MULTIPLIER = 25;
    $BASE_MONEY = 500;
    $MAX_SLOT_SPINS = 250;
    $marquee = "Click play to spin  /  Get 3 the same on winline for Hi/Low Gamble";
    $max_cash = $BASE_MONEY + (($user_class->level < $MAX_LEVEL_FOR_BETS) ? $user_class->level : $MAX_LEVEL_FOR_BETS) * $LEVEL_MULTIPLIER;
    $money = ($user_class->money >= $max_cash) ? $max_cash : $user_class->money;
    $error = "";
    $Jackpot = false;
    $redirect = "FruitMachine.php";
    if (isset($_POST['AutoSpin']))
        $_POST['money'] = $_POST['automoney'];
    if (isset($_POST['money'])) {
        if (!is_numeric($_POST['money'])) {
            $money = 0;
            $error = "No-one likes a smart arse - bet an actual amount";
        } elseif (intval($_POST['money']) <= 0) {
            $money = 0;
            $error = "You can't bet nothing!";
        } else {
            $bet = intval($_POST['money']);
            if ($bet > $max_cash)
                $error = "You can't bet more than the maximum";
            elseif ($bet > $user_class->money)
                $error = "You can't bet more than you have!";
            else
                $money = $bet;
        }
    }
    if ($user_class->slots_left1 <= 0) {
        $error = "No More Slot Turns Left. Try again tomorrow.";
        $redirect = "city.php";
    }
    if ($error == "") {
        if (isset($_POST['level']) && is_numeric($_POST['level']))
            $level = (int) $_POST['level'];
        else
            $level = "";
        if (isset($_POST['Spin'])) {
            $highlownum = rand(1, 9);
            $_SESSION['PreviousHighLow'] = $highlownum;
            $wheel1 = rand(1, 7);
            $wheel2 = rand(1, 7);
            $wheel3 = rand(1, 7);
            if ($wheel1 == $wheel2 && $wheel3 == $wheel1)
                $Winnings = $money * 5;
            elseif ($wheel1 == $wheel2 || $wheel1 == $wheel3 || $wheel3 == $wheel2)
                $Winnings = $money * 3;
            else
                $Winnings = 0;
            $_SESSION['CanDoHighLow'] = null;
            if ($wheel1 == $wheel2 && $wheel3 != $wheel1)
                $marquee = "WIN - WIN - LOSS - You Won: " . format_cash($Winnings);
            elseif ($wheel1 == $wheel3 && $wheel2 != $wheel3)
                $marquee = "WIN - LOSS - WIN - You Won: " . format_cash($Winnings);
            elseif ($wheel2 == $wheel3 && $wheel2 != $wheel1)
                $marquee = "LOSS - WIN - WIN - You Won: " . format_cash($Winnings);
            elseif ($wheel1 == $wheel2 && $wheel3 == $wheel2) {
                $marquee = "WIN - WIN - WIN - Jackpot!! You Won: " . format_cash($Winnings);
                $_SESSION['CanDoHighLow'] = time();
                $_SESSION['HighLowPot'] = $Winnings;
                $_SESSION['HighLowWinnings'] = 0;
                $_SESSION['WheelSpins'] = $wheel1 . '|' . $wheel2 . '|' . $wheel3;
                $level = 1;
                $Jackpot = true;
            } else
                $marquee = "Bummer - You Loser!";
            $user_class->money += ($Winnings - $money);
            $user_class->slots_left1 -= 1;
            $db->query("update grpgusers set money = ?, slots_left1 = ? where id = ?");
            $db->execute(array(
                $user_class->money,
                $user_class->slots_left1,
                $user_class->id
            ));
            $money = ($user_class->money >= $bet) ? $bet : $user_class->money;
        } elseif (isset($_POST['HighGamble'])) {
            if (isset($_SESSION['CanDoHighLow']) && (((time() - $_SESSION['CanDoHighLow']) / 60) < 5)) {
                $highlownum = rand(1, 9);
                if ($highlownum >= (int) $_SESSION['PreviousHighLow'])
                    high_low_win();
                else {
                    $Jackpot = false;
                    $user_class->money -= $_SESSION['HighLowPot'];
                    $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
                    $db->execute(array(
                        $user_class->money,
                        $user_class->id
                    ));
                    $marquee = "Uh-Oh - Better luck next time!";
                }
                $_SESSION['PreviousHighLow'] = $highlownum;
                $Wheels = explode('|', $_SESSION['WheelSpins']);
                $wheel1 = $Wheels[0];
                $wheel2 = $Wheels[1];
                $wheel3 = $Wheels[2];
            } else
                header("Location: FruitMachine.php");
        } elseif (isset($_POST['LowGamble'])) {
            if (isset($_SESSION['CanDoHighLow']) && (((time() - $_SESSION['CanDoHighLow']) / 60) < 5)) {
                $highlownum = rand(1, 9);
                if ($highlownum <= (int) $_SESSION['PreviousHighLow'])
                    high_low_win();
                else {
                    $Jackpot = false;
                    $user_class->money -= $_SESSION['HighLowPot'];
                    $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
                    $db->execute(array(
                        $user_class->money,
                        $user_class->id
                    ));
                    $marquee = "Uh-Oh - Better luck next time!";
                }
                $_SESSION['PreviousHighLow'] = $highlownum;
                $Wheels = explode('|', $_SESSION['WheelSpins']);
                $wheel1 = $Wheels[0];
                $wheel2 = $Wheels[1];
                $wheel3 = $Wheels[2];
            } else
                header("Location: SlotMachine.php?show_blurb=" . $show_blurb);
        } elseif (isset($_POST['StopGamble'])) {
            $Wheels = explode('|', $_SESSION['WheelSpins']);
            $wheel1 = $Wheels[0];
            $wheel2 = $Wheels[1];
            $wheel3 = $Wheels[2];
            $highlownum = (int) $_SESSION['PreviousHighLow'];
            pay_player_and_quit_high_low();
        } elseif (isset($_POST['AutoSpin'])) {
            $error = "";
            if (isset($_POST['NumberOfSpins'])) {
                if (!is_numeric($_POST['NumberOfSpins'])) {
                    $spins = 0;
                    $error = "Invalid Amount of Spins";
                } elseif (intval($_POST['NumberOfSpins']) < 0) {
                    $spins = 0;
                    $error = "Invalid Amount of Spins";
                } elseif (intval($_POST['NumberOfSpins']) > $user_class->slots_left1)
                    $spins = $user_class->slots_left1;
                else
                    $spins = intval($_POST['NumberOfSpins']);
                if ($spins > $MAX_SLOT_SPINS)
                    $error = "Invalid Amount of Spins";
                if ($spins * $money > $user_class->money)
                    $error = "You don't have enough cash to cover these spins!";
                if ($error == "") {
                    $highlownum = rand(1, 9);
                    $Winnings = 0;
                    for ($i = 0; $i < $spins; $i++) {
                        $wheel1 = rand(1, 7);
                        $wheel2 = rand(1, 7);
                        $wheel3 = rand(1, 7);
                        if ($wheel1 == $wheel2 || $wheel2 == $wheel3 || $wheel1 == $wheel3)
                            $Winnings += $money * 3;
                        elseif ($wheel1 == $wheel2 && $wheel2 == $wheel3)
                            $Winnings += $money * 5;
                    }
                    $user_class->slots_left1 -= $spins;
                    $user_class->money += ($Winnings - ($spins * $money));
                    $db->query("UPDATE grpgusers SET money = ?, slots_left1 = ? WHERE id = ?");
                    $db->execute(array(
                        $user_class->money,
                        $user_class->slots_left1,
                        $user_class->id
                    ));
                    $marquee = "Congratulations you won " . format_cash($Winnings);
                }
            }
        } else {
            $highlownum = 9;
            $wheel1 = 1;
            $wheel2 = 1;
            $wheel3 = 1;
            unset($_SESSION['CanDoHighLow']);
            unset($_SESSION['HighLowPot']);
        }
    } else
        $marquee = $error;
    ?>

    <div class="row">
        <?php if ($error == ""): ?>
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">Slot Machine</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <p><strong>Spins Left:</strong> <?= $user_class->slots_left1; ?></p>
                                <p>Bet: <input class="form-control" type="text" name="bet" value="<?= $money; ?>"
                                        onkeyup='document.all.money.value = this.value;'></p>
                            </div>
                            <div class="col-md-8">
                                <img src="images/Slots 2/<?= $highlownum; ?>.gif" class="img-fluid">
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-4">
                                <img src="images/Slots 2/wheel<?= $wheel1; ?>.gif" class="img-fluid">
                            </div>
                            <div class="col-4">
                                <img src="images/Slots 2/wheel<?= $wheel2; ?>.gif" class="img-fluid">
                            </div>
                            <div class="col-4">
                                <img src="images/Slots 2/wheel<?= $wheel3; ?>.gif" class="img-fluid">
                            </div>
                        </div>
                        <form method="post" class="mt-3">
                            <input type="hidden" name="money" value="<?= $money; ?>">
                            <button type="submit" name="Spin" class="btn btn-primary">Spin</button>
                        </form>
                        <marquee class="mt-3" style="font-size: 0.8em; color: #000; font-weight: bold;">
                            <?= $marquee; ?>
                        </marquee>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">Auto Spinner</div>
                    <div class="card-body">
                        <form method="post">
                            <div class="mb-3">
                                <label for="NumberOfSpins" class="form-label">Spins</label>
                                <input type="text" class="form-control" name="NumberOfSpins"
                                    value="<?= $user_class->slots_left1; ?>" maxlength="3">
                            </div>
                            <div class="mb-3">
                                <label for="automoney" class="form-label">Bet</label>
                                <input type="text" class="form-control" name="automoney" value="<?= $money; ?>"
                                    maxlength="4">
                            </div>
                            <button type="submit" name="AutoSpin" class="btn btn-primary">Auto Spin</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-danger" role="alert"><?= $error; ?></div>
                <a href="city.php" class="btn btn-secondary">Back</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
include 'footer.php';

function pay_player_and_quit_high_low()
{
    global $user_class, $Jackpot, $marquee, $db;
    unset($_SESSION['CanDoHighLow']);
    $user_class->money += $_SESSION['HighLowWinnings'];
    $db->query("UPDATE grpgusers SET money = money + ? WHERE id = ?");
    $db->execute(array(
        $_SESSION['HighLowWinnings'],
        $user_class->id
    ));
    $marquee = "Congratulations you won " . format_cash($_SESSION['HighLowWinnings'] + $_SESSION['HighLowPot']);
    unset($_SESSION['HighLowPot']);
    unset($_SESSION['HighLowWinnings']);
    $Jackpot = false;
}

function high_low_win()
{
    global $level, $Jackpot, $marquee;
    $_SESSION['HighLowWinnings'] = $_SESSION['HighLowPot'] * (.2 * $level);
    $marquee = "WIN - Winnings jackpot to " . format_cash($_SESSION['HighLowWinnings'] + $_SESSION['HighLowPot']);
    $level = $level + 1;
    $Jackpot = true;
    if ($level >= 6)
        pay_player_and_quit_high_low();
}

function format_cash($num)
{
    return "$" . number_format($num);
}
?>