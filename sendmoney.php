<?php
include 'header.php';
?>
<div class='box_top'>Send Money</div><div class='box_middle'>
							<div class='pad'>
                                <?php
if ($_POST['sendmoney'] != "" && $user_class->level > 24) {
    // Verify integers
    $_POST['amount'] = (isset($_POST['amount']) && is_numeric($_POST['amount'])) ? intval($_POST['amount']) : 0;
    $_POST['theirid'] = (isset($_POST['theirid']) && is_numeric($_POST['theirid'])) ? intval($_POST['theirid']) : 0;
    $money_person = new User($_POST['theirid']);
    echo Message("Are you sure you want to send $" . prettynum($_POST['amount']) . " to " . $money_person->formattedname . "?<br /><br /><form method='post'><input type='hidden' name='theirid' value='" . $_POST['theirid'] . "' /><input type='hidden' name='amount' value='" . $_POST['amount'] . "' /><input type='submit' name='sendmoney2' value='Continue' /></form><table><tr><td width='5px'></td></tr></table><form method='post' action='index.php'><input type='submit'  value='No Thanks!' /></form>");
    include("footer.php");
    die();
}
if ($_POST['sendmoney2'] != "" && $user_class->level > 24) {
    $money_person = new User($_POST['theirid']);
    //Validate money
    $notallowed = array('$', '-', '_', '+', '=', '<', '>');
    $_POST['amount'] = str_replace($notallowed, "", $_POST['amount']);
    $_POST['amount'] = (isset($_POST['amount']) && is_numeric($_POST['amount'])) ? intval($_POST['amount']) : 0;
    //End
    // Validate $_POST['theirid'] is an integer
    $_POST['theirid'] = (isset($_POST['theirid']) && is_numeric($_POST['theirid'])) ? intval($_POST['theirid']) : 0;
    $real = mysql_query("SELECT * FROM `grpgusers` WHERE `id` = '" . $_POST['theirid'] . "'");
    $check = mysql_num_rows($real);
    if ($user_class->money >= $_POST['amount'] && $_POST['amount'] >= 50000) { // Changed condition here
        if ($user_class->id != $money_person->id) {
            if ($check > 0) {
                if ((time() - $money_person->lastactive) < 86400 || $user_class->admin == 1) {
                    if ($_POST['amount'] <= 1000000000) {
                        // $newmoney = $user_class->money - $_POST['amount'];
                        // $result = mysql_query("UPDATE `grpgusers` SET `money` = '" . $newmoney . "' WHERE `id`='" . $_SESSION['id'] . "'");
                        // $newmoney = $money_person->money + $_POST['amount'];
                        // $result = mysql_query("UPDATE `grpgusers` SET `money` = '" . $newmoney . "' WHERE `id`='" . $_POST['theirid'] . "'");
                        $result = mysql_query("UPDATE `grpgusers` SET `money` = `money` - '" . $_POST['amount'] . "' WHERE `id`='" . $_SESSION['id'] . "'");
                        $result = mysql_query("UPDATE `grpgusers` SET `money` = `money` + '" . $_POST['amount'] . "' WHERE `id`='" . $_POST['theirid'] . "'");

                        $result = mysql_query("INSERT INTO `transferlog` (`toip`, `fromip`, `timestamp`, `to`, `from`, `money`)" . "VALUES ('" . $money_person->ip . "', '" . $user_class->ip . "', '" . time() . "', '" . $money_person->id . "', '" . $user_class->id . "', '" . $_POST['amount'] . "')");
                        Send_Event($money_person->id, "[-_USERID_-] sent you $" . prettynum($_POST['amount']) . ".", $user_class->id);
                        echo Message("You have successfully sent $" . prettynum($_POST['amount']) . " to " . $money_person->formattedname . ".");
                    } else {
                        echo Message("You can only send a maximum of $1,000,000,000.");
                    }
                } else {
                    echo Message("You can only send money to those who have been active in the last day");
                }
            } else { // Invalid ID
                echo Message("You can't send money to someone that doesn't exist.");
            }
        } else { // Sending to self
            echo Message("You can't send money to yourself.");
        }
    } else { // Not enough cash
        echo Message("You can only send a minimum of $50,000 at one time."); // Added message here
    }
}
?>

<tr>
    <td class="contentcontent">
        <?php
        if ($user_class->level <= "24") {
        ?>
            <center>
                <font size="3px">
                    <font color=orange>You must be at least level 25 to send money<br>
                        <font color=orange>You must be Registered for 1 day before sending money</font>
                    </font>
            </center>
        <?php
        }
        ?>
        <br>
        <?php
        $send_class = new User($_GET['person']);
        ?>
        You are sending money to <?php echo $send_class->formattedname; ?>.</b><br /><br />
        <form name='login' method='post' action='sendmoney.php?person=<?php echo $_GET['person']; ?>'>
            <table>
                <tr>
                    <td width='10%' height='27'><b>Amount:</b></td>
                    <td width='45%'>
                        <input name='amount' type='text' onKeyPress="return numbersonly(this, event)" size='9' value='<?php echo ($user_class->money > 1000000000) ? 1000000000 : $user_class->money; ?>'>&nbsp;&nbsp;$1,000,000,000 max.
                    </td>
                </tr>
                <tr>
                    <td height="5px"></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input type="hidden" name="theirid" value="<?php echo $_GET['person']; ?>" /><input type='submit' name='sendmoney' value='Send Money'>
                    </td>
                </tr>
            </table>
        </form>
    </td>
    <?php
    include 'footer.php';
    ?>
