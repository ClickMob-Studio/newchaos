<?php
include 'header.php';
if ($user_class->blocked != 0)
    diefun("You are gambling blocked for $user_class->blocked more days.");
if ($_GET['buy'] == "block")
    diefun("Are you sure you want to block yourself from gambling for 7 days? It will cost you 100 Points? <br><a href='?buy=blockyes'>Continue</a><br /><a href='?'>No thanks!</a>");
if ($_GET['buy'] == "blockyes") {
    $cost = 100;
    if ($user_class->points >= $cost) {
        $user_class->points -= $cost;
        $user_class->blocked = 7;
        $db->query("UPDATE grpgusers SET points = ?, blocked = ? WHERE id = ?");
        $db->execute(array(
            $user_class->points,
            $user_class->blocked,
            $user_class->id
        ));
        echo Message("You spent $cost Points and Have been blocked for 7 days.");
    } else
        echo Message("You don't have enough points. You need $cost Points");
}
if (isset($_POST['takebet'])) {
    security($_POST['bet_id']);
    $db->query("SELECT * FROM cash5050game WHERE id = ?");
    $db->execute(array(
        $_POST['bet_id']
    ));
    $row = $db->fetch_row(true);
	if(empty($row))
		diefun("This bet no longer exists.");
    if ($row['owner'] == $user_class->id)
        diefun("You can't take your own bet.");
    $orgamount = $row['amount'];
    $amount = $row['amount'];
    $user_cash = new User($row['owner']);
    if ($amount > $user_class->money)
        diefun("You don't have enough money to match their bet.");
    $user_class->money -= $amount;
    $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
    $db->execute(array(
        $user_class->money,
        $user_class->id
    ));
    $winner = rand(0, 1);
        $amount = round($orgamount * 2);
    if ($winner == 0) {
        echo Message("You have lost the bet.");
        $user_cash->money += $amount;
        $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
        $db->execute(array(
            $user_cash->money,
            $user_cash->id
        ));
        $db->query("INSERT INTO cash5050log VALUES ('', ?, ?, ?, ?, ?, ?, unix_timestamp(), 0)");
        $db->execute(array(
            $user_cash->id,
            $user_class->id,
            $user_cash->id,
            $amount,
            $user_cash->ip,
            $user_class->ip
        ));
        Send_Event($user_cash->id, "You won the " . prettynum($orgamount, 1) . " bet you placed!");
    } else {
        echo Message("You have won the bet and gained " . prettynum($amount, 1) . "!");
        $user_class->money += $amount;
        $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
        $db->execute(array(
            $user_class->money,
            $user_class->id
        ));
        $db->query("INSERT INTO cash5050log VALUES ('', ?, ?, ?, ?, ?, ?, unix_timestamp(), 0)");
        $db->execute(array(
            $user_cash->id,
            $user_class->id,
            $user_class->id,
            $amount,
            $user_cash->ip,
            $user_class->ip
        ));
        Send_Event($user_cash->id, "You lost the " . prettynum($orgamount, 1) . " bet you placed!");
    }
    $db->query("DELETE FROM cash5050game WHERE id = ?");
    $db->execute(array(
        $row['id']
    ));
}
if ($_POST['makebet']) {
    security($_POST['amount'], 'num');
    if ($_POST['amount'] > $user_class->money)
        diefun("You don't have that much money.");
    if ($_POST['amount'] < 1000)
        diefun("You have to bet at least $1,000.");
    if ($_POST['amount'] > 100000000)
        diefun("The maximum you can bet is $100,000,000.");
    echo Message("You have added a " . prettynum($_POST['amount'], 1) . " bet.");
    $db->query("INSERT INTO cash5050game (owner, amount) VALUES (?, ?)");
    $db->execute(array(
        $user_class->id,
        $_POST['amount']
    ));
    $user_class->money -= $_POST['amount'];
    $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
    $db->execute(array(
        $user_class->money,
        $user_class->id
    ));
}
if (isset($_POST['removebet'])) {
    security($_POST['bet_id']);
    $db->query("SELECT * FROM cash5050game WHERE id = ? AND owner = ?");
    $db->execute(array(
        $_POST['bet_id'],
        $user_class->id
    ));
    $row = $db->fetch_row(true);
    if (empty($row))
        diefun("This bet was not found.");
    $db->query("DELETE FROM cash5050game WHERE id = ?");
    $db->execute(array(
        $_POST['bet_id']
    ));
    $user_class->money += $row['amount'];
    $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
    $db->execute(array(
        $user_class->money,
        $user_class->id
    ));
    echo Message("You have removed your bet.");
}
		echo'<br />';
		echo'<a id="botlink" href="?buy=block">Gambling Block for 7 days</a><br />';
		echo'<form method="post">';
			echo'<div class="floaty" style="width:60%;">';
				echo'Amount of money to bid: ';
				echo'<input type="text" name="amount" size="10" maxlength="20" /> ';
				echo'<button type="submit" name="makebet" value="Make Bet">Make Bet</button>';
			echo'</div>';
		echo'</form>';
		echo'<table id="newtables" class="altcolors" style="width:100%;table-layout:fixed;">';
			echo'<tr>';
				echo'<th>Better</th>';
				echo'<th>Amount</th>';
				echo'<th>Bet</th>';
			echo'</tr>';
$db->query("SELECT * FROM cash5050game ORDER BY amount DESC");
$db->execute();
$rows = $db->fetch_row();
if(!count($rows)){
			echo'<tr>';
				echo'<td colspan="3">There are no money bets current.</td>';
			echo'</tr>';
} else
	foreach($rows as $row){
		$button = ($row['owner'] == $user_class->id) ? "<input type='submit' name='removebet' value='Remove Bet' />" : "<input type='submit' name='takebet' value='Take Bet' />";
			echo'<tr>';
				echo'<td>' . formatName($row['owner']) . '</td>';
				echo'<td>' . prettynum($row['amount'], 1) . '</td>';
				echo'<td>';
					echo'<form method="post">';
						echo'<input type="hidden" name="bet_id" value="' . $row['id'] . '" />';
						echo $button;
					echo'</form>';
				echo'</td>';
			echo'</tr>';
	}
		echo'</table>';
		echo'<div class="floaty" style="width:60%;text-align:center;">';
			echo'This game is simple.<br />';
			echo'Two people bet the same amount of money, then a winner is randomly picked.<br />';
			echo'The winner receives 100% of the money!<br />';
			echo'<br />';
			echo'Minimum Bet: <span style="font-weight:bold;color:red;">$1,000</span><br />';
			echo'Maximum Bet: <span style="font-weight:bold;color:green;">$100,000,000</span>';
		echo'</div>';
	echo'</td>';
echo'</tr>';
include 'footer.php';
?> 