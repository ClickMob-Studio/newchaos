<?php
include 'header.php';
?>
<div class='box_top'>Send Gold</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if (isset($_POST['sendcredits']))
            error("
    Are you sure you want to send " . prettynum($_POST['amount']) . " GOLD to " . formatName($_POST['theirid']) . "?<br /><br />
    <form method='post'>
        <input type='hidden' name='theirid' value='" . $_POST['theirid'] . "' />
        <input type='hidden' name='amount' value='" . $_POST['amount'] . "' />
        <input type='submit' name='sendcredits2' value='Continue' />
    </form><table><tr><td width='5px'></td></tr></table>
    <form method='post' action='index.php'>
        <input type='submit'  value='No Thanks!' />
    </form>");
        if (isset($_POST['sendcredits2'])) {
            $money_person = new User($_POST['theirid']);
            security($_POST['amount'], 'num');
            if ($user_class->credits < $_POST['amount'] || $_POST['amount'] == 0)
                error("You don't have enough GOLD to do that.");
            if ($user_class->id == $money_person->id)
                error("You can't send GOLD to yourself.");
            if (empty($money_person->id))
                error("You can't send GOLD to someone that doesn't exist.");
            if ($_POST['amount'] > 10000)
                error("You can only send a maximum of 10000 GOLD.");
            perform_query("INSERT INTO send_logs(fromid, toid, what, quantity) VALUES (?, ?, 'gold', ?)", array($user_class->id, $_POST['theirid'], $_POST['amount']));
            perform_query("UPDATE grpgusers SET credits = credits - ? WHERE id = ?", array($_POST['amount'], $user_class->id));
            perform_query("UPDATE grpgusers SET credits = credits + ? WHERE id = ?", array($_POST['amount'], $_POST['theirid']));
            perform_query("INSERT INTO `transferlog` (`toip`, `fromip`, `timestamp`, `to`, `from`, `credits`) VALUES (?, ?, ?, ?, ?, ?)", array($money_person->ip, $user_class->ip, time(), $money_person->id, $user_class->id, $_POST['amount']));
            Send_Event($money_person->id, "[-_USERID_-] sent you " . prettynum($_POST['amount']) . " GOLD.", $user_class->id);
            echo Message("You have successfully sent " . prettynum($_POST['amount']) . " GOLD to " . $money_person->formattedname . ".");
        }
        $creds = ($user_class->credits > 100) ? 100 : $user_class->credits;
        print '
<form method="post">
  <table id="newtables" style="width:55%;">
    <tr>
        <th colspan="2"><b>You are sending GOLD to ' . formatName($_GET['person']) . '</b></th>
    </tr>
    <tr> 
      <td width="10%" height="27"><b>Amount:</b></td>
      <td width="45%"><input name="amount" type="text" onKeyPress="return numbersonly(this, event)" size="7" value="' . $creds . '">&nbsp;&nbsp;10,000 GOLD max.</td>
    </tr>
    <tr> 
      <td colspan="2"><input type="hidden" name="theirid" value="' . $_GET['person'] . '" /><input type="submit" name="sendcredits" value="Send GOLD"></td>
    </tr>
  </table>
</form>
';
        include 'footer.php';
        function error($msg)
        {
            echo Message($msg);
            include "footer.php";
            die();
        }
        ?>