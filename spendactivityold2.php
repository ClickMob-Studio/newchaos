<?php
include 'header.php';
$pts = array(
	array("first",50,40),
	array("second",250,180),
	array("third",500,350),
	array("forth",1000,650),
	array("fifth",5000,3000),
	array("sixth",15000,8000),
	array("seventh",30000,15000),
	array("eighth",50000,24000),
	array("ninth",100000,45000),

);
if(isset($_GET['spend'])){
	foreach($pts as $pt)
		if($_GET['spend'] == $pt[0]) points($pt[1], $pt[2]);
	foreach($rys as $ry)
		if($_GET['spend'] == $ry[0]) points($ry[1], $ry[2]);
	foreach($money as $mo)
		if($_GET['spend'] == $mo[0]) money($mo[1], $mo[2]);
}
genHead("Activity Store - You currently have <span style='color:#3ab997;font-weight:bold;'>".prettynum($user_class->apoints)."</span> Activity Points");
?>
<center>
Welcome to the Activity Store, here you can spend your Activity Points on various things.<br /><br />
<table id="newtables">
	<tr>
		<th colspan="3">Point Packs</th>
	</tr>
<?php
foreach($pts as $pt)
	print"
	<tr>
		<td>".prettynum($pt[1])." Point Pack</td>
		<td>".prettynum($pt[2])." Activity Points</td>
		<td><button class='ycbutton' style='padding:2px 10px;' onClick='actConfirm({$pt[1]},\"Points Pack\",{$pt[2]},\"spendactivity.php?spend={$pt[0]}\");'>Buy</button></a></td>
	</tr>";
?>
</table>
</center>

<table id="newtables">
	<tr>
		<th colspan="3">Item Packs</th>
	</tr>
	<tr>
		<td>1 Double Exp Pill</td>
		<td>5000  Activity Points</td>
		<td><a class='ycbutton' href="?buy=DE">[Buy Now]</a></a></td>
	</tr>
</table>
</center>


</td></tr>
<?php
include 'footer.php';
function points($points, $apoints){
	global $user_class;
	if($user_class->apoints < $apoints)
		echo Message("You do not have enough Activity Points to buy this package.");
	else{
		$user_class->points += $points;
		$user_class->apoints -= $apoints;
		mysql_query("UPDATE grpgusers SET points = $user_class->points, apoints = $user_class->apoints WHERE id = $user_class->id");
		echo Message("You have paid ".prettynum($apoints)." Activity Points for ".prettynum($points)." points.");
	}
}
function money($money, $apoints){
	global $user_class;
	if($user_class->apoints < $apoints)
		echo Message("You do not have enough Activity Points to buy this package.");
	else{
		$user_class->money += $money;
		$user_class->apoints -= $apoints;
		mysql_query("UPDATE grpgusers SET money = $user_class->money, apoints = $user_class->apoints WHERE id = $user_class->id");
		echo Message("You have paid ".prettynum($apoints)." Activity Points for $".prettynum($money).".");
	}
}
  if ($_GET['buy'] == "DE") {
        if ($user_class->apoints >= 5000) {
            $newcredit = $user_class->apoints -= 5000;
            $db->query("UPDATE grpgusers SET apoints = apoints - 5000 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(10, $user_class->id);
            Send_Event($user_class->id, "You have been credited with your 1 Hour Double EXP pack. You can find it <a href='inventory.php'><font color=yellow><b>[Here]</b></font></a>", $user_class->id);
            $db->execute(array());
            echo Message("You spent 5000 activity points for a Double EXP pack.");
        } else {
            echo Message("You don't have enough Activity Points. You can earn more by being active!");
        }
    }


function rydays($days, $apoints){
	global $user_class;
	if($user_class->apoints < $apoints)
		echo Message("You do not have enough Activity Points to buy this package.");
	else{
		$user_class->rmdays += $days;
		$user_class->apoints -= $apoints;
		mysql_query("UPDATE grpgusers SET rmdays = $user_class->rmdays, apoints = $user_class->apoints WHERE id = $user_class->id");
		echo Message("You have paid ".prettynum($apoints)." Activity Points for ".prettynum($days)." RY Day(s).");
	}
}
?>