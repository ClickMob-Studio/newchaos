<?php
include 'header.php';
?>
<div class='box_top'>Send Points</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if (isset($_POST['sendpoints']) && $_POST['sendpoints'] != "" && $user_class->level > 9) {
            // Verify integers
            $_POST['amount'] = (isset($_POST['amount']) && is_numeric($_POST['amount'])) ? intval($_POST['amount']) : 0;
            $_POST['theirid'] = (isset($_POST['theirid']) && is_numeric($_POST['theirid'])) ? intval($_POST['theirid']) : 0;
            $money_person = new User($_POST['theirid']);
            echo Message("Are you sure you want to send " . prettynum($_POST['amount']) . " points to " . $money_person->formattedname . "?<br /><br /><form method='post'><input type='hidden' name='theirid' value='" . $_POST['theirid'] . "' /><input type='hidden' name='amount' value='" . $_POST['amount'] . "' /><input type='submit' name='sendpoints2' value='Continue' /></form><table><tr><td width='5px'></td></tr></table><form method='post' action='index.php'><input type='submit'  value='No Thanks!' /></form>");
            include("footer.php");
            die();
        }
        if (isset($_POST['sendpoints2']) && $_POST['sendpoints2'] != "" && $user_class->level > 9) {
            $money_person = new User($_POST['theirid']);
            //Validate Points
            $notallowed = array('$', '-', '_', '+', '=', '<', '>');
            $_POST['amount'] = str_replace($notallowed, "", $_POST['amount']);
            $_POST['amount'] = (isset($_POST['amount']) && is_numeric($_POST['amount'])) ? intval($_POST['amount']) : 0;
            //End
            // Validate $_POST['theirid'] is an integer
            $_POST['theirid'] = (isset($_POST['theirid']) && is_numeric($_POST['theirid'])) ? intval($_POST['theirid']) : 0;
            $db->query("SELECT * FROM `grpgusers` WHERE `id` = ?");
            $db->execute([$_POST['theirid']]);
            $result = $db->fetch_row(true);
            $check = count($result);
            if ($user_class->points >= $_POST['amount'] && $_POST['amount'] > 0) {
                if ($user_class->id != $money_person->id) {
                    if ($check > 0) {
                        if ($_POST['amount'] <= 500000) {
                            perform_query("INSERT INTO send_logs(fromid, toid, what, quantity) VALUES (?, ?, 'points', ?)", [$user_class->id, $_POST['theirid'], $_POST['amount']]);
                            perform_query("UPDATE `grpgusers` SET `points` = `points` - ? WHERE `id` = ?", [$_POST['amount'], $_SESSION['id']]);
                            perform_query("UPDATE `grpgusers` SET `points` = `points` + ? WHERE `id` = ?", [$_POST['amount'], $_POST['theirid']]);
                            perform_query("INSERT INTO `transferlog` (`toip`, `fromip`, `timestamp`, `to`, `from`, `points`)" . "VALUES (?, ?, ?, ?, ?, ?)", [$money_person->ip, $user_class->ip, time(), $money_person->id, $user_class->id, $_POST['amount']]);
                            Send_Event($money_person->id, "[-_USERID_-] sent you " . prettynum($_POST['amount']) . " points.", $user_class->id);
                            echo Message("You have successfully sent " . prettynum($_POST['amount']) . " points to " . $money_person->formattedname . ".");
                        } else {
                            echo Message("You can only send a maximum of 500,000 points.");
                        }
                    } else { // Invalid ID
                        echo Message("You can't send points to someone that doesn't exist.");
                    }
                } else { // Sending to self
                    echo Message("You can't send points to yourself.");
                }
            } else { // Not enough cash
                echo Message("You don't have enough points to do that.");
            }
        }
        ?>

        <tr>
            <div class="contentcontent">
        <tr>
            <?php
            if ($user_class->level <= "24") {
                ?>
                <center>
                    <font size="3px">
                        <font color=orange>You must be at least level 10 to send points<br>
                            <font size="3px">
                                <font color=orange>You must be Registered for 1 day before sending points</font></b>
                            </font>
                        </font>
                </center>
                <?php
            }
            ?>
            <br>
            <?php
            $send_class = new User($_GET['person']);
            ?>
            <b>You are sending points to <?php echo $send_class->formattedname; ?>.</b><br /><br />
            <form name='login' method='post' action='sendpoints.php?person=<?php echo $_GET['person']; ?>'>
        <tr>
            <td width='10%' height='27'><b>Amount:</b></td>
            <td width='45%'>
                <input name='amount' type='text' onKeyPress="return numbersonly(this, event)" size='7'
                    value='<?php echo ($user_class->points > 500000) ? 500000 : $user_class->points; ?>'>&nbsp;&nbsp;500,000
                points max.
            </td>
        </tr>
        <tr>
            <td height="5px"></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>
                <input type="hidden" name="theirid" value="<?php echo $_GET['person']; ?>" /><input type='submit'
                    name='sendpoints' value='Send Points'>
            </td>
        </tr>
        </table>
        </form>
    </div>
    <?php
    include 'footer.php';
    ?>