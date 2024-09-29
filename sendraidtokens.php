<?php
exit;
include 'header.php';
if (isset($_POST['sendraidtokens']))
    error("
    Are you sure you want to send " . prettynum($_POST['amount']) . " raidtokens to " . formatName($_POST['theirid']) . "?<br /><br />
    <form method='post'>
        <input type='hidden' name='theirid' value='" . $_POST['theirid'] . "' />
        <input type='hidden' name='amount' value='" . $_POST['amount'] . "' />
        <input type='submit' name='sendraidtokens2' value='Continue' />
    </form><table><tr><td width='5px'></td></tr></table>
    <form method='post' action='index.php'>
        <input type='submit'  value='No Thanks!' />
    </form>");
if (isset($_POST['sendraidtokens2'])) {
    $money_person = new User($_POST['theirid']);
    security($_POST['amount'], 'num');
    if ($user_class->raidtokens < $_POST['amount'] || $_POST['amount'] == 0)
        error("You don't have enough raidtokens to do that.");
    if ($user_class->id == $money_person->id)
        error("You can't send raidtokens to yourself.");
    if (empty($money_person->id))
        error("You can't send raidtokens to someone that doesn't exist.");
    if ($_POST['amount'] > 100)
        error("You can only send a maximum of 100 raidtokens.");
    mysql_query("UPDATE grpgusers SET raidtokens = raidtokens - {$_POST['amount']} WHERE id = $user_class->id");
    mysql_query("UPDATE grpgusers SET raidtokens = raidtokens + {$_POST['amount']} WHERE id = {$_POST['theirid']}");
    mysql_query("INSERT INTO transferlog (toip, fromip, timestamp, `to`, `from`, raidtokens)VALUES('$money_person->ip', '$user_class->ip', unix_timestamp(), $money_person->id, $user_class->id, {$_POST['amount']}')");
    Send_Event($money_person->id, "[-_USERID_-] sent you " . prettynum($_POST['amount']) . " raidtokens.", $user_class->id);
    echo Message("You have successfully sent " . prettynum($_POST['amount']) . " raidtokens to " . $money_person->formattedname . ".");
}
$creds = ($user_class->raidtokens > 100) ? 100 : $user_class->raidtokens;
print '
<form method="post">
  <table id="newtables" style="width:55%;">
    <tr>
        <th colspan="2"><b>You are sending raidtokens to ' . formatName($_GET['person']) . '</b></th>
    </tr>
    <tr> 
      <td width="10%" height="27"><b>Amount:</b></td>
      <td width="45%"><input name="amount" type="text" onKeyPress="return numbersonly(this, event)" size="7" value="' . $creds . '">&nbsp;&nbsp;100 raidtokens max.</td>
    </tr>
    <tr> 
      <td colspan="2"><input type="hidden" name="theirid" value="' . $_GET['person'] . '" /><input type="submit" name="sendraidtokens" value="Send raidtokens"></td>
    </tr>
  </table>
</form>
</div>
';
include 'footer.php';
function error($msg) {
    echo Message($msg);
    include "footer.php";
    die();
}
?>