<?php
include "header.php";
?>
	
	<div class='box_top'>Slot Machine</div>
						<div class='box_middle'>
							<div class='pad'>
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
 
<div class="contenthead floaty">
<span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;">
<h4>Fruit MAchine</h4></span>
        <?php
        if ($error == "") {
            ?>
            <table width="100%">
                <tbody> 
                    <tr>
                        <td width="65%">
                            <table class="SlotsTable">
                                <tbody>
                                    <tr style="height: 80px;">
                                        <td align="center" class="BlackTD">                                                          
                                            <font color=red><?php
                                            echo $user_class->slots_left1;
                                            ?> Spins Left</font>&nbsp;&nbsp;
                                        </td>
                                        <td colspan="2" >
                                            $<input name="bet" value="<?php
                                            echo $money;
                                            ?>" 
                                                    onkeyup='document.all.money.value = this.value;' maxlength="4" size="10">&nbsp;[Max: $<?php
                                                    echo $max_cash;
                                                    ?>]
                                        </td>
                                    </tr>
                                    <tr style="height: 100px;">
                                        <td width="87px">
                                            <?php
                                            if ($Jackpot) {
                                                ?>
                                                <form method="post" style="margin-top: 4px;margin-bottom: 2px;">
                                                    <input type="hidden" name="level" value="<?php
                                                    echo $level;
                                                    ?>">              
                                                    <button type="submit" name="HighGamble" style="width: 79px; height: 32px; margin-left: 24px;">
                                                        <img src="images/Slots 2/highlive.gif">
                                                    </button>
                                                </form> 
                                                <form method="post" style="margin-top: 0;margin-bottom: 2px;">
                                                    <input type="hidden" name="level" value="<?php
                                                    echo $level;
                                                    ?>">              
                                                    <button type="submit" name="StopGamble" style="width: 79px; height: 32px; margin-left: 24px;">
                                                        <img src="images/Slots 2/stoplive.gif">
                                                    </button>
                                                </form> 
                                                <form method="post" style="margin-top: 0;margin-bottom: 0;">
                                                    <input type="hidden" name="level" value="<?php
                                                    echo $level;
                                                    ?>">              
                                                    <button type="submit" name="LowGamble" style="width: 79px; height: 32px; margin-left: 24px;">
                                                        <img src="images/Slots 2/lowlive.gif">
                                                    </button>
                                                </form> 
                                                <?php
                                            } else {
                                                ?>
                                                <img src="images/Slots 2/high.gif" style="margin-left: 25px; margin-top: 10px;"><br>
                                                <img src="images/Slots 2/stop.gif" style="margin-left: 25px; margin-top: 5px; margin-bottom: 1px;"><br>
                                                <img src="images/Slots 2/low.gif" style="margin-left: 25px;">
                                                <?php
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <img src="images/Slots 2/<?php
                                            echo $highlownum;
                                            ?>.gif" style="margin-top: 10px;">
                                        </td>
                                        <td>
                                            <img src="images/Slots 2/HighLowLevels<?php
                                            if ($Jackpot) {
                                                echo $level;
                                            }
                                            ?>.gif" style="margin-top: 7px;margin-left: 15px;">
                                        </td>
                                    </tr>
                                    <tr align="center" height="111" style="margin-top: 4px;">
                                        <td width="143">
                                            <img src="images/Slots 2/wheel<?php
                                            echo $wheel1;
                                            ?>.gif" style="margin-left: 15px;" alt="<?php
                                                 echo $wheel1;
                                                 ?>">
                                        </td>
                                        <td>
                                            <img src="images/Slots 2/wheel<?php
                                            echo $wheel2;
                                            ?>.gif" alt="<?php
                                                 echo $wheel2;
                                                 ?>">
                                        </td>
                                        <td width="143">
                                            <img src="images/Slots 2/wheel<?php
                                            echo $wheel3;
                                            ?>.gif" style="margin-right: 15px;" alt="<?php
                                                 echo $wheel3;
                                                 ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                <marquee style="font-size: 0.8em; margin-left: 30px; margin-right: 0px; margin-top: 0px;  color: #fff; font-weight: bold;">
                                    <p class="SlotsP" style="color: #fff; margin-top: 10px;"><?php
                                        echo $marquee;
                                        ?></p>
                                </marquee>
                        </td>
                    </tr>
                    <tr height="5"><td>&nbsp;</td></tr>
                    <tr>
                        <td colspan="3" align="center">
                            <form method="post">
                                <input type="hidden" name="money" value="<?php
                                echo $money;
                                ?>">            
                                <button type="submit" name="Spin" style="margin-top: 1px; width: 108px; height: 43px;"><img src="images/Slots 2/PlayEnabled.gif"></button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
        </td>
        <td style="vertical-align: middle;">
            <form method="POST">
                <table class="AutoSpinner" style="font-size: 0.8em">
                    <tbody>
                        <tr style="height: 40px;">
                            <td colspan="2" align="center">Auto Spinner&nbsp;&nbsp;</td>
                        </tr>
                        <tr style="height: 30px;">
                            <td width="35%">&nbsp;&nbsp;Spins:</td>
                            <td>&nbsp;&nbsp;<input name="NumberOfSpins" value="<?php
                                echo $user_class->slots_left1;
                                ?>"  maxlength="3" size="5"></td>
                        </tr>
                        <tr style="height: 25px;">
                            <td width="35%">&nbsp;&nbsp;Bet:<br>&nbsp;</td>
                            <td>$<input name="automoney" value="<?php
                                echo $money;
                                ?>" maxlength="4" size="10"><br>[Max: $<?php
                                        echo $max_cash;
                                        ?>]</td>
                        </tr>  
                        <tr style="height: 20px;">
                            <td align="center" colspan="2" valign="top">                                      
                                <input type="submit" name="AutoSpin" value="Auto Spin">
                            </td>  
                        </tr>  
                    </tbody>  
                </table> 
            </form>  
        </td>
    </tr>
    </tbody>
    </table>  
    <?php
} else {
    ?>
    <table style="font-size: 0.8em">
        <tr>
            <td>
                <?php
                echo $error;
                ?>
            </td>
        </tr>
        <tr>
            <td><a href="city.php">Back</a></td>
        </tr>
    </table>
    <?php
}
?>
<?php
include 'footer.php';
function pay_player_and_quit_high_low() {
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
function high_low_Win() {
    global $level, $Jackpot, $marquee;
    $_SESSION['HighLowWinnings'] = $_SESSION['HighLowPot'] * (.2 * $level);
    $marquee = "WIN - Winnings jackpot to " . format_cash($_SESSION['HighLowWinnings'] + $_SESSION['HighLowPot']);
    $level = $level + 1;
    $Jackpot = true;
    if ($level >= 6)
        pay_player_and_quit_high_low();
}
function format_cash($num) {
    return "$" . number_format($num);
}
?> 