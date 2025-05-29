<?php
include 'header.php';
if ($_POST['collect'] && $user_class->drugdealers > 0) {
    if ($user_class->collected == 0) {
        $newmoney = $user_class->money + (100 * $user_class->drugdealers);

        perform_query("UPDATE `grpgusers` SET `collected` = '1', `money`= ? WHERE `id`=?", [$newmoney, $_SESSION['id']]);
        $moneycollected = $user_class->drugdealers * 100;
        echo Message("You have just collected " . prettynum($moneycollected, 1) . " from your dealers.");
    } else {
        echo Message("You have either already collected or have no dealers to collect from.");
    }
}
//drug dealer stuff
if ($_POST['onedealer'] && $user_class->drugdealers + 1 <= $user_class->maxdealers) {
    if ($user_class->points > 0) {
        $newpoints = $user_class->points - 1;
        $newdrugdealers = $user_class->drugdealers + 1;
        perform_query("UPDATE `grpgusers` SET `drugdealers` = ?, `points` = ? WHERE `id` = ?", [$newdrugdealers, $newpoints, $user_class->id]);
        echo Message("You spent 1 Point and hired a drug dealer.");
    } else {
        echo Message("You have either reached your maximum drug dealers or do not have enough points.");
    }
}
if ($_POST['twodealers'] && $user_class->drugdealers + 2 <= $user_class->maxdealers) {
    if ($user_class->points > 1) {
        $newpoints = $user_class->points - 2;
        $newdrugdealers = $user_class->drugdealers + 2;
        perform_query("UPDATE `grpgusers` SET `drugdealers` = ?, `points` = ? WHERE `id` = ?", [$newdrugdealers, $newpoints, $user_class->id]);
        echo Message("You spent 2 Points and hired 2 drug dealers.");
    } else {
        echo Message("You don't have enough points.");
    }
}
if ($_POST['fivedealers'] && $user_class->drugdealers + 5 <= $user_class->maxdealers) {
    if ($user_class->points > 4) {
        $newpoints = $user_class->points - 5;
        $newdrugdealers = $user_class->drugdealers + 5;
        perform_query("UPDATE `grpgusers` SET `drugdealers` = ?, `points` = ? WHERE `id` = ?", [$newdrugdealers, $newpoints, $user_class->id]);
        echo Message("You spent 5 Points and hired 5 drug dealers.");
    } else {
        echo Message("You don't have enough points.");
    }
}
if ($_POST['tendealers'] && $user_class->drugdealers + 10 <= $user_class->maxdealers) {
    if ($user_class->points > 9) {
        $newpoints = $user_class->points - 10;
        $newdrugdealers = $user_class->drugdealers + 10;
        perform_query("UPDATE `grpgusers` SET `drugdealers` = ?, `points` = ? WHERE `id` = ?", [$newdrugdealers, $newpoints, $user_class->id]);
        echo Message("You spent 10 Points and hired 10 drug dealers.");
    } else {
        echo Message("You don't have enough points.");
    }
}
if ($_POST['fiftydealers'] && $user_class->drugdealers + 50 <= $user_class->maxdealers) {
    if ($user_class->points > 49) {
        $newpoints = $user_class->points - 50;
        $newdrugdealers = $user_class->drugdealers + 50;
        perform_query("UPDATE `grpgusers` SET `drugdealers` = ?, `points` = ? WHERE `id` = ?", [$newdrugdealers, $newpoints, $user_class->id]);
        echo Message("You spent 50 Points and hired 50 drug dealers.");
    } else {
        echo Message("You don't have enough points.");
    }
}
if ($_POST['onehundreaddealers'] && $user_class->drugdealers + 100 <= $user_class->maxdealers) {
    if ($user_class->points > 99) {
        $newpoints = $user_class->points - 100;
        $newdrugdealers = $user_class->drugdealers + 100;
        perform_query("UPDATE `grpgusers` SET `drugdealers` = ?, `points` = ? WHERE `id` = ?", [$newdrugdealers, $newpoints, $user_class->id]);
        echo Message("You spent 100 Points and hired 100 drug dealers.");
    } else {
        echo Message("You don't have enough points.");
    }
}
?>
<tr>
    <td class="contenthead">Drug Dealers</td>
</tr>
<tr>
    <td class="contentcontent">
        <?php if ($user_class->collected == '0') { ?>
            <center>
                <form method='post'>
                    <input type='submit' class='button' name='collect' value='Collect from Dealers'>
                </form><br>
            </center>
            <?php
        }
        ?>
        <center><img src="images/drugdealers.png" /></center><br><br>
        <center>
            <font color=green><b>You currently have <?php echo $user_class->drugdealers; ?> / <?php echo $user_class->maxdealers; ?> Drug Dealers</b></font>
        </center><br><br>
    </td>
</tr>
<tr>
    <td class="contenthead">Hire Dealers</td>
</tr>
<tr>
    <td class="contentcontent">
        <table border="0" bordercolor="#6d6969" cellpadding="0" cellspacing="0" width="100%">
            <tr>
                <td width="33.33%" class="contenthead">
                    <center>Dealers</center>
                </td>
                <td width="33.33%" class="contenthead">
                    <center>Cost</center>
                </td>
                <td width="33.33%" class="contenthead">
                    <center>Action</center>
                </td>
            </tr>
        </table>
        <table border="0" bordercolor="#6d6969" cellpadding="4" cellspacing="1" width="100%">
            <tr>
                <td width="33.33%" class="highlight">
                    <center>1</center>
                </td>
                <td width="33.33%" class="highlight2">
                    <center>1 Point</center>
                </td>
                <td width="33.33%" class="highlight">
                    <center>
                        <form method='post'><input type='submit' class='button' name='onedealer' value='Buy 1 Dealer'>
                        </form>
                    </center>
                </td>
            </tr>
        </table>
        <table border="0" bordercolor="#6d6969" cellpadding="4" cellspacing="1" width="100%">
            <tr>
                <td width="33.33%" class="highlight">
                    <center>2</center>
                </td>
                <td width="33.33%" class="highlight2">
                    <center>2 Points</center>
                </td>
                <td width="33.33%" class="highlight">
                    <center>
                        <form method='post'><input type='submit' class='button' name='twodealers' value='Buy 2 Dealers'>
                        </form>
                    </center>
                </td>
            </tr>
        </table>
        <table border="0" bordercolor="#6d6969" cellpadding="4" cellspacing="1" width="100%">
            <tr>
                <td width="33.33%" class="highlight">
                    <center>5</center>
                </td>
                <td width="33.33%" class="highlight2">
                    <center>5 Points</center>
                </td>
                <td width="33.33%" class="highlight">
                    <center>
                        <form method='post'><input type='submit' class='button' name='fivedealers'
                                value='Buy 5 Dealers'></form>
                    </center>
                </td>
            </tr>
        </table>
        <table border="0" bordercolor="#6d6969" cellpadding="4" cellspacing="1" width="100%">
            <tr>
                <td width="33.33%" class="highlight">
                    <center>10</center>
                </td>
                <td width="33.33%" class="highlight2">
                    <center>10 Points</center>
                </td>
                <td width="33.33%" class="highlight">
                    <center>
                        <form method='post'><input type='submit' class='button' name='tendealers'
                                value='Buy 10 Dealers'></form>
                    </center>
                </td>
            </tr>
        </table>
        <table border="0" bordercolor="#6d6969" cellpadding="4" cellspacing="1" width="100%">
            <tr>
                <td width="33.33%" class="highlight">
                    <center>50</center>
                </td>
                <td width="33.33%" class="highlight2">
                    <center>50 Points</center>
                </td>
                <td width="33.33%" class="highlight">
                    <center>
                        <form method='post'><input type='submit' class='button' name='fiftydealers'
                                value='Buy 50 Dealers'></form>
                    </center>
                </td>
            </tr>
        </table>
        <table border="0" bordercolor="#6d6969" cellpadding="4" cellspacing="1" width="100%">
            <tr>
                <td width="33.33%" class="highlight">
                    <center>100</center>
                </td>
                <td width="33.33%" class="highlight2">
                    <center>100 Points</center>
                </td>
                <td width="33.33%" class="highlight">
                    <center>
                        <form method='post'><input type='submit' class='button' name='onehundreaddealers'
                                value='Buy 100 Dealers'></form>
                    </center>
                </td>
            </tr>
        </table>
        <?php
        include 'footer.php';
        ?>