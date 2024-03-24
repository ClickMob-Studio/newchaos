<?php
include 'header.php';
if($user_class->admin){
    if(isset($_POST['numTickets'])){
        $numTickets = security($_POST['numTickets']);
        $ticketPrice = security($_POST['ticketPrice']);
        $accept = array('money','points');
        if(!in_array($_POST['prizeCurrency'], $accept))
            diefun("Error, not valid input.");
        if(!in_array($_POST['ticketCurrency'], $accept))
            diefun("Error, not valid input.");
        $prizes = explode("|",$_POST['prizes']);
        foreach($prizes as $prize)
            security($prize);
        $db->query("INSERT INTO raffle VALUES ('', ?, ?, ?, ?, ?, ?)");
        $db->execute(array(
            $numTickets,
            $_POST['ticketCurrency'],
            $ticketPrice,
            $_POST['prizes'],
            $_POST['prizeCurrency'],
            $_POST['maxtickets']
        ));
    }
    print"
        Prize format is: 1stPlace|2ndPlace|3rdPlace<br />
        Example: 10000|5000|2500<br />
    <form method='post'>
        <table id='newtables'>
            <tr>
                <th>Number of Tickets to sell:</th>
                <td><input type='text' size='4' name='numTickets' value='0' /></td>
            </tr>
            <tr>
                <th>Price of Tickets:</th>
                <td><input type='text' size='8' name='ticketPrice' value='0' /></td>
            </tr>
            <tr>
                <th>Ticket Currency:</th>
                <td>
                    <select name='ticketCurrency'>
                        <option value='money'>Money</option>
                        <option value='points'>Points</option>
                    </select>
                </td>
            </tr>
            <tr>
                <th>Max Tickets Per Player: (0 = Unlimited)</th>
                <td><input type='text' name='maxtickets' size='20' value='0' /></td>
            </tr>
            <tr>
                <th>Prizes:</th>
                <td><input type='text' name='prizes' size='20' value='10000|5000|2500' /></td>
            </tr>
            <tr>
                <th>Prize Currency:</th>
                <td>
                    <select name='prizeCurrency'>
                        <option value='money'>Money</option>
                        <option value='points'>Points</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan='2'><input type='submit' value='Create Raffle' /></td>
            </tr>
        </table>
    </form>";
}
$db->query("SELECT * FROM raffle ORDER BY id DESC LIMIT 1");
$db->execute();
if(!$db->num_rows())
    diefun("There are no open raffles at this time.");
$row = $db->fetch_row(true);
$db->query("SELECT ticketNum FROM raffleEntries WHERE rid = ?");
$db->execute(array(
    $row['id']
));
$ents = array();
$entries = $db->fetch_row();
foreach($entries as $entry)
    $ents[] = $entry['ticketNum'];
$allticks = range(1, $row['numTickets']);
$diff = array_diff($allticks, $ents);
$allticks = array_values($diff);
if(isset($_GET['buyticket'])){
    if(count($allticks) == 0)
        diefun("There are no more tickets remaining.");
	if($row['maxTickets']){
		$db->query("SELECT COUNT(*) FROM raffleEntries WHERE rid = ? AND userid = ?");
		$db->execute(array(
			$row['id'],
			$user_class->id
		));
		$myticks = $db->fetch_single();
		if(++$myticks > $row['maxTickets'])
			diefun("You have maxed out on tickets for this raffle.");
	}
    if($row['buyCurrency'] == 'money'){
        if($user_class->money < $row['buyPrice'])
            diefun("You do not have enough money to buy a ticket.");
        $db->query("INSERT INTO raffleEntries VALUES (?, ?, ?)");
        $db->execute(array(
            $row['id'],
            $allticks[rand(0, count($allticks) - 1)],
            $user_class->id
        ));
        $user_class->money -= $row['buyPrice'];
        $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
        $db->execute(array(
            $user_class->money,
            $user_class->id
        ));
    } else
    if($row['buyCurrency'] == 'points'){
        if($user_class->points < $row['buyPrice'])
            diefun("You do not have enough points to buy a ticket.");
        $db->query("INSERT INTO raffleEntries VALUES (?, ?, ?)");
        $db->execute(array(
            $row['id'],
            $allticks[rand(0, count($allticks) - 1)],
            $user_class->id
        ));
        $user_class->points -= $row['buyPrice'];
        $db->query("UPDATE grpgusers SET points = ? WHERE id = ?");
        $db->execute(array(
            $user_class->points,
            $user_class->id
        ));
    }
}
$ticketPrice = ($row['buyCurrency'] == 'money') ? prettynum($row['buyPrice'], 1) : prettynum($row['buyPrice']) ." Points";
$prizes = explode("|", $row['prizes']);
echo"
    <table id='newtables' style='table-layout:fixed;width:100%;'>
        <tr>
            <th>Total Tickets:</th>
            <td>".prettynum($row['numTickets'])."</td>
            <th rowspan='2'>Ticket Price</th>
            <td rowspan='2'>$ticketPrice</td>
        </tr>
		<tr>
			<th>Max Tickets Per Person</th>
			<td>",($row['maxTickets']) ? prettynum($row['maxTickets']) : "Unlimited","</td>
		</tr>
        <tr>
            <th>Prizes:</th>";
$co = 1;
foreach($prizes as $prize){
    $pretext = formatNum($co) . ".";
    $prize = ($row['prizeCurrency'] == 'money') ? prettynum($prize, 1) : prettynum($prize) ." Points";
    if(++$co % 4 == 1)
        print"<tr>";
    print"
            <td>$pretext $prize</td>";
    if($co % 4 == 0)
        print"</tr>";
}
if($co % 4 != 0)
    print"</tr>";
$db->query("SELECT * FROM raffleEntries WHERE rid = ? ORDER BY ticketNum ASC");
$db->execute(array(
	$row['id']
));
$rows = $db->fetch_row();
print"
    </table>
    <a href='?buyticket'><button>Buy Ticket</button></a><br />
    <table id='newtables' style='width:100%;table-layout:fixed;'>";
$co = 0;
foreach($rows as $row){
    if($co % 4 == 0)
        print"<tr>";
    print"<td>{$row['ticketNum']}. ".formatName($row['userid'])."</td>";
    $co++;
    if($co % 4 == 0)
        print"</tr>";
}
print"</table>";
include 'footer.php';
function formatNum($num){
    switch($num){
        case 1:
            return "1st";
        case 2:
            return "2nd";
        case 3:
            return "3rd";
        default:
            return $num."th";
    }
}
?>
