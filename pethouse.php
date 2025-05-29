<?php
include 'header.php';
?>

<div class='box_top'>Pet House</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $q = mysql_query("SELECT petid FROM pets WHERE userid = $user_class->id");
        if (!mysql_num_rows($q))
            diefun("You don't have a pet");
        $pet_class = new Pet($user_class->id);
        if (isset($_GET['action']) && $_GET['action'] == 'sell') {
            if ($pet_class->loaned)
                diefun("You know... " . formatName($pet_class->loaned) . " would be very disappointed in you!");
            if (!$pet_class->house)
                diefun("Your pet doesn't have a house");
            $q = mysql_query("SELECT name, cost FROM pethouses WHERE id = $pet_class->house");
            $row = mysql_fetch_array($q);
            $cost = floor($row['cost'] / 2);
            perform_query("UPDATE grpgusers SET money = money + ? WHERE id = ?", [$cost, $user_class->id]);
            perform_query("UPDATE pets SET house = 0, awake = 0, maxawake = 100 WHERE userid = ?", [$user_class->id]);
            echo Message("You've sold your pet's house for " . prettynum($cost, 1));
            $user_class->money += $cost;
            $pet_class = new Pet($user_class->id);
        }
        if (isset($_GET['buy'])) {
            security($_GET['buy']);
            if ($_GET['buy'] == $pet_class->house)
                diefun("Your pet is already living in that house");
            $q = mysql_query("SELECT name, cost, awake, buyable FROM pethouses WHERE id = {$_GET['buy']}");
            if (!mysql_num_rows($q))
                diefun("That pet house doesn't exist");
            $new = mysql_fetch_array($q);
            if (!$new['buyable'])
                diefun("That pet house can't be bought like that");
            if ($new['awake'] <= $pet_class->maxawake)
                diefun("You can't buy a house smaller than your current house without selling it first.");
            $cost = $new['cost'];
            $text = '';
            if ($pet_class->house) {
                $q = mysql_query("SELECT name, cost FROM pethouses WHERE id = $pet_class->house");
                $old = mysql_fetch_array($q);
                $cost -= ($old['cost'] / 2);
                $text .= "Your pet's old house ({$old['name']}) has been sold for half of it's value (" . prettynum($old['cost'] / 2, 1) . "). That has gone towards the new property<br />";
            }
            if ($cost > $user_class->money)
                diefun("You don't have enough cash. You need a further " . prettynum($cost - $user_class->money, 1));
            perform_query("UPDATE grpgusers SET money = money - ? WHERE id = ?", [$cost, $user_class->id]);
            perform_query("UPDATE pets SET house = ?, awake = ?, maxawake = ? WHERE userid = ?", [$_GET['buy'], $new['awake'], $new['awake'], $user_class->id]);
            echo Message($text . "You've purchased the pet house: {$new['name']}, costing you " . prettynum($cost, 1));
        }
        if ($pet_class->house) {
            $q = mysql_query("SELECT name, cost FROM pethouses WHERE id = $pet_class->house");
            $row = mysql_fetch_array($q);
            print "<tr><td class='contentspacer'></td></tr>
	<tr><td class='contenthead'>Sell House</td></tr>
	<tr><td class='contentcontent center'>
	<a href='pethouse.php?action=sell'>Sell your pet's {$row['name']} for " . prettynum($row['cost'] / 2, 1) . ".</a>
	</td></tr>";
        }
        include 'includepet.php';
        print "<center>
<table id='newtables'>
	<tr>
		<th width='45%'>Type</th>
		<th width='15%'>Awake</th>
		<th width='20%'>Cost</th>
		<th width='20%'>Purchase</th>
	</tr>";
        $q = mysql_query("SELECT id, name, awake, cost FROM pethouses WHERE buyable = 1 ORDER BY awake ASC, cost ASC");
        while ($row = mysql_fetch_array($q)) {
            echo "<tr>
				<td>{$row['name']}</td>
				<td>", prettynum($row['awake']), "</td>
				<td>", prettynum($row['cost'], 1), "</td>
				<td>", ($row['id'] != $pet_class->house) ? "<a href='pethouse.php?buy={$row['id']}'>Purchase</a>" : "<span style='text-decoration:strike-through;color:#FC0;'>Purchase</span>", "</td>
			</tr>";
        }
        print "</table></center>
</td></tr>";
        include 'footer.php';
