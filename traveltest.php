<?php
include 'header.php';

if ($user_class->jail > 0)
    diefun("You can't travel while in Prison!");
if ($user_class->hospital > 0)
	diefun("You can't travel while in Hospital!");


$mydiscount = mysql_fetch_array(mysql_query("SELECT discount FROM carlot c JOIN usercars ON carid = c.id WHERE userid = $user_class->id ORDER BY carid DESC LIMIT 1"));
$discount = 100;
$discount -= isset($mydiscount) ? $mydiscount[0] : 0;
if (isset($_GET['go'])) {
	security($_GET['go']);
    $error = ($_GET['go'] == $user_class->city) ? "You are already there." : $error;
    $result = mysql_query("SELECT * FROM cities WHERE id = {$_GET['go']}");
    $worked = mysql_fetch_array($result);
	if($worked['pres'] && !$user_class->prestige)
		diefun("You do not have access to this city.");
    $cost = $worked['price'] * $discount / 100;
    $error = ($worked['name'] == "") ? "That city doesn't exist." : $error;
    $error = ($user_class->level < $worked['levelreq']) ? "You are not a high enough level to go there!." : $error;
    $error = ($user_class->prestige < $worked['pres']) ? "You are not a high enough Prestige to go there!." : $error;

    $error = ($user_class->country != $worked['country']) ? "That city isn't in this country." : $error;
    $error = ($user_class->money < $cost) ? "You dont have enough money to go there." : $error;
    $error = ($worked['rmonly'] == 1 && $user_class->rmdays < 1) ? "You need to be a Respected Soldier to go there." : $error;

    if (!isset($error)) {
        $newmoney = $user_class->money - $cost;
        $result = mysql_query("UPDATE grpgusers SET city = {$_GET['go']}, money = $newmoney WHERE id = $user_class->id");
        echo Message("You successfully paid " . prettynum($cost, 1) . " for a Plane ticket and got to your destination.");
    } else
        echo Message($error);
}
echo "<style type='text/css'>
.rm {
   vertical-align:3px;
   font-weight:600;
}
</style>
<style>
    .small-column {
        width: 10%;
    }
    .large-column {
        width: 50%;
    }
</style>

<center> Welcome to the Travel Page! Here you can travel to all your favourite destinations and aquire new items</center>
<br>
<center> Unsure what items are in what citys and what benefits they give? Go to our itempedia page to find out what items are in what citys!</center>
<br>

	<table id='newtables' style='width:100%;table-layout:fixed;'>
		<tr>
		<th class='small-column'>Name</th>
        <th class='small-column'>Cost</th>
        <th class='small-column'>Level Required</th>
        <th class='small-column'>Prestige Required</th>
        <th class='small-column'>Population</th>

<th>Description</th>
		</tr>";
$result = mysql_query("SELECT * FROM cities WHERE country = $user_class->country AND id != 24 ORDER BY pres ASC, levelreq ASC");
while ($line = mysql_fetch_array($result)) {
    $population1 = mysql_query("SELECT * FROM grpgusers WHERE city = {$line['id']}");
    $population = mysql_num_rows($population1);
    $cost = $line['price'];
    if ($line['rmonly'] == 1)
        echo "<tr><td><a href='travel.php?go={$line['id']}'>{$line['name']}</a> <a href='rmstore.php' class='rm' style='color:yellow;'>RY ONLY</a></td><td>" . prettynum($cost * $discount / 100, 1) . "</td><td>{$line['levelreq']}</td><td>{$line['pres']}</td><td>$population</td></tr>";
    else
        echo "<tr><td><a href='travel.php?go={$line['id']}'>{$line['name']}</a></td><td>" . prettynum($cost * $discount / 100, 1) . "</td><td>{$line['levelreq']}</td><td>{$line['pres']}</td><td>$population</td> <td>{$line['description']}</td></tr>";
}
echo "</table>";
include 'footer.php';
?>