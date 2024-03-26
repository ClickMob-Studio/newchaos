<?php
include 'header.php';
?>
	
	<div class='box_top'>Cash Lottery</div>
						<div class='box_middle'>
							<div class='pad'>
								<?php
$tickCost = 250000;
$db->query("SELECT SUM(tickets) FROM cashlottery");
$db->execute();
$numlotto = $db->fetch_single();
$amountlotto = $numlotto * $tickCost;

$db->query("SELECT sum(tickets) FROM cashlottery WHERE userid = ?");
$db->execute(array(
    $user_class->id
));
$numlotto = $db->fetch_single();
$numlotto = ($numlotto > 0) ? $numlotto : 0;
$allowed = 50 - $numlotto;
$default = min(round($user_class->money / 250000), $allowed);

if (isset($_POST['buy'])) {
    $quantity = $_POST['amount'];
    $cost = $quantity * $tickCost;

    if (($numlotto + $quantity) > 50) {
        echo Message("You cannot purchase that many tickets.  You can only purchase another " . $allowed);
    } else if ($numlotto >= 50) {
        echo Message("You have already bought 50 tickets today.");
    } else if ($quantity < 1) {
        echo Message("You must purchase at least 1 ticket");
    } else if ($quantity > 50) {
        echo Message("You can only purchase a maximum of 50 tickets");
    } else if ($user_class->money < $cost) {
        echo Message("You need " . prettynum($cost, 1) . " to buy a ticket.");
    } else {
        $user_class->money -= $cost;

        $db->query("INSERT INTO cashlottery (userid, tickets) VALUES (?, ?) ON DUPLICATE KEY UPDATE tickets = tickets + $quantity");
        $db->execute(array(
            $user_class->id,
            $quantity
        ));

        $db->query("UPDATE grpgusers SET money = money - ? WHERE id = ?");
        $db->execute(array(
            $cost,
            $user_class->id
        ));
        $numlotto += $quantity;

        echo Message("You have bought " . $quantity . " lottery tickets.");

    }

}
?>


<center>
    <h2 class="mb-0">Current Prize</h2>
    <span id="lottery-prize"><?php echo prettynum($amountlotto,1) ?></span>

    <div>
        <p>Ticket Cost: <strong><?php echo prettynum($tickCost, 1); ?> each</strong><br>
        You can purchase a maximum of <strong>50</strong> tickets per day<br>
        Prize increases with ticket purchases<br>
        Lottery will be drawn at the end of the day and you will recieve the full prize pot</p>
    </div>

    <div>
        <p>You currently have <?php echo $numlotto ?> tickets</p>
        <?php
            if($numlotto < 50) {
                echo '<form method="post">
                <input type="text" name="amount" style="width:50px" value="' . $default .'">
                <input type="submit" name="buy" value="Buy Tickets">
            </form>';
            }
        ?>
    </div>

</center>
<br/>
<br/>

<?php
// echo'<h3>The Lottery</h3>';
// 	echo'<hr>';
// echo ""
//  . "<center>Do you want to buy a ticket for the daily lottery?<br />"
//  . "You can buy <span style='color:red;font-weight:bold;'>50</span> tickets a day for <span style='color:red;font-weight:bold;'>" . prettynum($tickCost, 1) . "</span> a ticket.<br />"
//  . "The more people that enter, the more that the winner will win.<br />"
//  . " If your ticket is drawn at the end of the day, you win all of the ticket revenue!
//      <br><br><a href='?buy=ticket'><button>Buy Ticket</button></a><br /><br />";
// $db->query("SELECT COUNT(*) FROM cashlottery");
// $db->execute();
// $numlotto = $db->fetch_single();
// $amountlotto = $numlotto * $tickCost;
// echo "There have been <span style='color:red;font-weight:bold;'>" . prettynum($numlotto) . "</span> Lotto Tickets bought today.<br>";
// echo "Lotto is currently worth <span style='color:red;font-weight:bold;'>" . prettynum($amountlotto,1) . "</span>.";
echo""
 . "<table id='newtables'>"
 . "<tr>"
 . "<th>Winner</th>"
 . "<th>Won?</th>"
 . "</tr>";
$db->query("SELECT * FROM mlottowinners ORDER BY id DESC LIMIT 31");
$db->execute();
$rows = $db->fetch_row();
foreach ($rows as $row)
    echo""
    . "<tr>"
    . "<td>" . formatName($row['userid']) . "</td>"
    . "<td>" . number_format($row['won']) . "</td>"
    . "</tr>";
echo '</table></td></tr>';
include 'footer.php';
?>