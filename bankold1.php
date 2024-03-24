<?php
include 'header.php';
?>
<style>
    /* Default styles for the bank containers */
.bank-container {
    width: 47%; /* Default width for desktop */
    float: left;
    margin: 0;
}

/* Media query for mobile devices */
@media only screen and (max-width: 767px) {
    .bank-container {
        width: 100%; /* Full width on mobile */
        float: none; /* Clear the float */
        margin: 20px 0; /* Add margin to separate sections on mobile */
    }
}</style>
<?php
$rel_user = new User($user_class->relplayer);

if (isset($_POST['sdeposit'])) {
    if ($_POST['sid'] == $rel_user->id) {

        $amount = $_POST['damount'];

        if ($amount > $rel_user->money) {
            echo "They do not have that much money on hand";
        } else {
            $amount2 = round($amount - (($amount / 100) * 2));
            $amount3 = round($amount - (($amount / 100) * 98));
            $rel_user->bank += $amount2;
            $rel_user->money -= $amount;
            $notice = ("Money deposited with a 2% fee of $$amount3 taken.");
            mysql_query("UPDATE grpgusers SET bank = $rel_user->bank, money = $rel_user->money WHERE id = $rel_user->id");
            if ($amount > 0)
                mysql_query("INSERT INTO bank_log VALUES('', $rel_user->id, $amount, 'mdep', $rel_user->bank, unix_timestamp())");
            if ($rel_user->bank > $rel_user->banklog)
                mysql_query("UPDATE grpgusers SET banklog = $rel_user->bank WHERE id = $rel_user->id");

            Send_Event($rel_user->id, $user_class->formattedname . " has deposited $" . number_format($amount) . " into your bank account.");
            Send_Event($user_class->id, "You have deposited $" . number_format($amount) . " into $rel_user->formattedname's bank account");

            echo $notice;
        }
    } else {
        echo "You do not have access to this persons money!";
    }
    include 'footer.php';
    die();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    if ($_GET['action'] == 'sdeposit') {
        if ($_GET['id'] == $rel_user->id) {

            echo "
            <div class='floaty' style='width:50%;margin:0 auto;'>
            <h2>" . $rel_user->formattedname . "</h2><br>
            There will be a 2% Deposit Fee
            <div style='clear:both'></div>
            <hr style='border:0;border-bottom: thin solid #333;' />
            <table width='50%'>
                <tr>
                    <td width='20%'><b>Money On Hand:</b></td>
                    <td width='20%'>" . prettynum($rel_user->money, 1) . "</td>
                </tr>
            </table>
            <br />
            <form method='post' action='?'>
                <input type='text' name='damount' value='$rel_user->money' size='10' maxlength='20' />
                <input type='hidden' name='sid' value='" . $rel_user->id . "'>
                <input type='submit' style='width:75px;' name='sdeposit' value='Deposit' />
                <input type='hidden' name='type' value='money' />
            </form>
        </div>";
        } else {
            echo "You do not have access to this persons money!";
        }
    }
    include 'footer.php';
    die();
}

if ((isset($_GET['dep']) || isset($_POST['deposit']))) {
    if (isset($_GET['dep']))
        $_POST['type'] = 'money';
    else
        $_POST['damount'] = security($_POST['damount'], 'num');
    $amount = (isset($_GET['dep'])) ? $user_class->money : $_POST['damount'];
    $type = ($_POST['type'] == 'money') ? 'money' : 'points';
    if ($amount > $user_class->$type)
        $notice = ("You do not have that much $type.");
    else {
        if ($type == 'money') {
            $amount2 = round($amount - (($amount / 100) * 2));
            $amount3 = round($amount - (($amount / 100) * 98));
            $user_class->bank += $amount2;
            $user_class->money -= $amount;
            $notice = ("Money deposited with a 2% fee of $$amount3 taken.");
            mysql_query("UPDATE grpgusers SET bank = $user_class->bank, money = $user_class->money WHERE id = $user_class->id");
            if ($amount > 0)
                mysql_query("INSERT INTO bank_log VALUES('', $user_class->id, $amount, 'mdep', $user_class->bank, unix_timestamp(), $user_class->money)");
            if ($user_class->bank > $user_class->banklog)
                mysql_query("UPDATE grpgusers SET banklog = $user_class->bank WHERE id = $user_class->id");
        } else {
            $user_class->pbank += $amount;
            $user_class->points -= $amount;
            $notice = ("Points deposited.");
            mysql_query("UPDATE grpgusers SET pbank = $user_class->pbank, points = $user_class->points WHERE id = $user_class->id");
            if ($amount > 0)
                mysql_query("INSERT INTO bank_log VALUES('', $user_class->id, $amount, 'pdep', $user_class->pbank, unix_timestamp(), $user_class->points)");
        }
    }
}

if ((isset($_GET['dep']) || isset($_POST['deposit_shared']))) {
    if (isset($_GET['dep'])) {
        $_POST['type'] = 'money';
    } else {
        $_POST['damount'] = security($_POST['damount'], 'num');
        $amount = (isset($_GET['dep'])) ? $user_class->money : $_POST['damount'];
    }
    $amount = (isset($_GET['dep'])) ? $user_class->money : $_POST['damount'];
    $type = ($_POST['type'] == 'money') ? 'money' : 'points';
    if ($amount > $user_class->$type) {
        $notice = ("You do not have that much $type.");
    } else {
        if ($_POST['type'] == 'money') {
            $amount2 = round($amount - (($amount / 100) * 2));
            $amount3 = round($amount - (($amount / 100) * 98));
            $user_class->shared_bank += $amount2;
            $user_class->money -= $amount;
            $notice = ("Money deposited with a 2% fee of $$amount3 taken.");
            mysql_query("UPDATE grpgusers SET shared_bank = $user_class->shared_bank, money = $user_class->money WHERE id = $user_class->id");
            mysql_query("UPDATE grpgusers SET shared_bank = $user_class->shared_bank WHERE id = $user_class->relplayer");
            mysql_query("UPDATE grpgusers SET sharedcontribution = $user_class->sharedcontribution + $amount WHERE id = $user_class->id");

            if ($amount > 0) {
                Send_Event($user_class->relplayer, "" . $user_class->formattedname . " Has Deposited $amount Leaving you with a total of $" . $user_class->shared_bank . " in your shared account!");
                //mysql_query("INSERT INTO bank_log VALUES('', $user_class->id, $amount, 'mdep', $user_class->shared_bank, unix_timestamp())");
            }
            if ($user_class->shared_bank > $user_class->banklog) {
                //mysql_query("UPDATE grpgusers SET banklog = $user_class->shared_bank WHERE id = $user_class->id");
            }
        }
    }
}

if (isset($_POST['withdraw'])) {
    $amount = security($_POST['wamount'], 'num');
    $type = ($_POST['type'] == 'money') ? array(
        'money',
        'bank'
    ) : array(
        'points',
        'pbank'
    );
    if ($amount > $user_class->$type[1])
        $notice = ("You do not have that much {$type[0]} in the bank.");
    else {
        $notice = (ucfirst($type[0]) . " withdrawn.");
        $user_class->$type[1] -= $amount;
        $user_class->$type[0] += $amount;
        mysql_query("UPDATE grpgusers SET {$type[1]} = " . $user_class->{$type[1]} . ", {$type[0]} = " . $user_class->{$type[0]} . " WHERE id = $user_class->id");
        if ($amount > 0) {
            $which = ($_POST['type'] == 'money') ? "mwith" : "pwith";
            $whichhand = ($which == "mwith") ? $user_class->money : $user_class->points;
            mysql_query("INSERT INTO bank_log VALUES('', $user_class->id, $amount, '$which', " . $user_class->{$type[1]} . ", unix_timestamp(), $whichhand)");
        }
    }
}


if (isset($_POST['withdraw_shared'])) {
    $amount = security($_POST['wamount'], 'num');
    $type = ($_POST['type'] == 'money') ? array(
        'money',
        'shared_bank'
    ) : array(
        'points',
        'pbank'
    );
    if ($amount > $user_class->$type[1])
        $notice = ("You do not have that much {$type[0]} in the bank.");
    else {
        $notice = (ucfirst($type[0]) . " withdrawn.");
        $user_class->$type[1] -= $amount;
        $user_class->$type[0] += $amount;
        mysql_query("UPDATE grpgusers SET {$type[1]} = " . $user_class->{$type[1]} . ", {$type[0]} = " . $user_class->{$type[0]} . " WHERE id = $user_class->id");
        mysql_query("UPDATE grpgusers SET sharedcontribution = $user_class->sharedcontribution - $amount, money = $user_class->money WHERE id = $user_class->id");

        Send_Event($user_class->relplayer, "" . $user_class->formattedname . " Has withdrawn $amount Leaving you with a total of $" . $user_class->shared_bank . "!");

        mysql_query("UPDATE grpgusers SET {$type[1]} = " . $user_class->{$type[1]} . " WHERE id = $user_class->relplayer");

        if ($amount > 0) {
            $which = ($_POST['type'] == 'money') ? "mwith" : "pwith";
            //  mysql_query("INSERT INTO bank_log VALUES('', $user_class->id, $amount, '$which', " . $user_class->{$type[1]} . ", unix_timestamp())");
        }
    }
}

if ($user_class->rmdays > 0) {
    $interest = 0.04;
    //$interest += $user_class->bankboost / 10;
    $rate = ($interest * 100) . "%";
} else {
    $interest = .02;
    //$interest += $user_class->bankboost / 10;
    $rate = ($interest * 100) . "%";
}
if ($user_class->bank >= 15000000)
    $interest = ceil(15000000 * $interest);
else
    $interest = ceil($user_class->bank * $interest);

$interest += $interest * ($user_class->bankboost / 10);
$bi = mysql_fetch_array(mysql_query("SELECT * FROM banksettings WHERE userid = $user_class->id"));
if (empty($bi)) {
    $bi['limit'] = 25;
    $bi['format'] = 'us';
    $bi['show'] = 'all';
}
echo "$notice<br /><br />
<h3>Your Bank</h3>
<hr>
<div class='bank-container'>
    <span style='font-size:24px;float:left;color:green;'><img src='../images/bankmoney.png'/></span><br /><br />
    You will be charged a 2% Deposit Fee
	<div style='clear:both;'></div>
	<hr style='border:0;border-bottom: thin solid #333;' />
    <table width='100%'>
        <tr>
            <td width='20%'><b>Bank:</b></td>
            <td width='20%'>" . prettynum($user_class->bank, 1) . "</td>
            <td width='30%'></td>
        </tr>
        <tr>
            <td width='20%'><b>Daily Interest:</b></td>
            <td width='20%'>" . prettynum($interest, 1) . "</td>
            <td width='30%'></td>
        </tr>
        <tr>
            <td width='20%'><b>Interest Rate:</b></td>
            <td width='20%'>$rate</td>
            <td width='30%'>(Max: $15,000,000)</td>
        </tr>
    </table>
    <br /><br />
    <form method='post' action='?'><input type='text' name='wamount' value='0' size='10' maxlength='20' />
        <input type='submit' style='width:75px;' name='withdraw' value='Withdraw' />
        <input type='hidden' name='type' value='money' />
    </form>
    <br>
    <form method='post' action='?'><input type='text' name='damount' value='$user_class->money' size='10' maxlength='20' />
        <input type='submit' style='width:75px;' name='deposit' value='Deposit' />
        <input type='hidden' name='type' value='money' />
    </form>
</div>
<div class='bank-container'>
    <span style='font-size:24px;float:left;color:lightblue;'><img src='../images/bankpoints.png'/></span>
	<div style='clear:both;'></div>
	<hr style='border:0;border-bottom: thin solid #333;' />
    <table width='100%'>
        <tr>
            <td width='20%'><b>Points:</b></td>
            <td width='20%'>" . prettynum($user_class->pbank) . "</td>
            <td width='30%'></td>
        </tr>
        <tr>
            <td><br /></td>
        </tr>
        <tr>
            <td><br /></td>
        </tr>
    </table>
    <br /><br />
    <form method='post' action='?'><input type='r' name='wamount' value='0' size='10' maxlength='20' />
        <input type='submit' style='width:75px;' name='withdraw' value='Withdraw' />
        <input type='hidden' name='type' value='points' />
    </form>
    <br>
    <form method='post' action='?'><input type='text' name='damount' value='$user_class->points' size='10' maxlength='20' />
        <input type='submit' style='width:75px;' name='deposit' value='Deposit' />
        <input type='hidden' name='type' value='points' />
    </form>
</div>";

if ($user_class->relationship > 0) {

    echo "<div class='floaty' style='width:47%;float:left;margin:20px 0 0 0;'>
    <span style='font-size:24px;float:left;color:green;'><img src='../images/bankmoney.png'/></span><br /><br />
    You will be charged a 2% Deposit Fee<br />
You are currently sharing this Vault with your partner " . formatName($user_class->relplayer) . "

	<div style='clear:both;'></div>
	<hr style='border:0;border-bottom: thin solid #333;' />
    <table width='100%'>
        <tr>
            <td width='20%'><b>Shared Bank Balance:</b></td>
            <td width='20%'>" . prettynum($user_class->shared_bank, 1) . "</td>
            <td width='30%'>Contributions</td>
        </tr>
<tr>
            <td width='20%'><b>" . formatName($user_class->id) . "
</b></td>
            <td width='20%'></td>
            <td width='30%'>" . prettynum($user_class->sharedcontribution, 1) . "</td>
        </tr>

<tr>
            <td width='20%'><b>" . formatName($user_class->relplayer) . "
</b></td>
            <td width='20%'></td>
            <td width='30%'>" . prettynum($rel_user->sharedcontribution, 1) . "


</td>
        </tr>

    </table>
    <br /><br />
    <form method='post' action='?'><input type='text' name='wamount' value='0' size='10' maxlength='20' />
        <input type='submit' style='width:75px;' name='withdraw_shared' value='Withdraw' />
        <input type='hidden' name='type' value='money' />
    </form>
    <br>
    <form method='post' action='?'><input type='text' name='damount' value='$user_class->money' size='10' maxlength='20' />
        <input type='submit' style='width:75px;' name='deposit_shared' value='Deposit' />
        <input type='hidden' name='type' value='money' />
    </form>
</div>";
}

echo "<div style='clear:both;'></div>
<br />
<br />
Show <input type='text' value='0' id='limit' size='3' maxlength='3' onkeyup='updateBankLog();' /> Transactions |
Date Format
    <select id='format' onchange='updateBankLog();'>
        <option value='us'", ($bi['format'] == 'us') ? " selected" : "", ">US Format</option>
        <option value='uk'", ($bi['format'] == 'uk') ? " selected" : "", ">Non-US Format</option>
    </select> |
Show
    <select id='show' onchange='updateBankLog();'>
        <option value='all'", ($bi['show'] == 'all') ? " selected" : "", ">All Transactions</option>
        <option value='money'", ($bi['show'] == 'money') ? " selected" : "", ">Show only Money Transactions</option>
        <option value='points'", ($bi['show'] == 'points') ? " selected" : "", ">Show only Points Transactions</option>
        <option value='withs'", ($bi['show'] == 'withs') ? " selected" : "", ">Show only Withdraws</option>
        <option value='deps'", ($bi['show'] == 'deps') ? " selected" : "", ">Show only Deposits</option>
    </select>
<br />
<br />
<div id='banklog'>
    " . banklog($bi['limit'], $bi['show'], $bi['format']) . "
</div>";

print <<<TEXT
<script>
    function updateBankLog(){
        $.post("ajax_banklog.php", {'limit':$("#limit").val(),'format':$("#format").val(),'show':$("#show").val()}, function (callback){
            $("#banklog").html(callback);
        });
    }
</script>
TEXT;
include 'footer.php';
