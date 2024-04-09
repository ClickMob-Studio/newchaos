<?php
exit;
include 'header.php';
if (isset($_GET['use'])) {
	$id = security($_GET['use']);
	$howmany = check_items($id);
	if($howmany){
		switch($id){
			case 4:
				$db->query("UPDATE grpgusers SET awake = ? WHERE id = ?");
				$db->execute(array(
					$user_class->maxawake,
					$user_class->id
				));
				echo Message("You successfully used an awake pill to refill your awake to 100%.");
				break;
			case 11:
			case 12:
			case 13:
			case 14:
				if($user_class->purehp >= $user_class->puremaxhp && !$user_class->hospital)
					diefun("You already have full HP and are not in the hospital.");
				$db->query("SELECT * FROM items WHERE id = ?");
				$db->execute(array(
					$id
				));
				$row = $db->fetch_row(true);
				$hosp = floor(($user_class->hospital / 100) * $row['reduce']);
				$newhosp = $user_class->hospital - $hosp;
				$newhosp = ($newhosp < 0) ? 0 : $newhosp;
				$hp = floor(($user_class->puremaxhp / 4) * $row['heal']);
				$hp = $user_class->purehp + $hp;
				$hp = ($hp > $user_class->puremaxhp) ? $user_class->puremaxhp : $hp;
				$db->query("UPDATE grpgusers SET hospital = ?, hp = ? WHERE id = ?");
				$db->execute(array(
					$newhosp,
					$hp,
					$user_class->id
				));
				echo Message("You successfully used a {$row['itemname']}.");
				break;
			case 27:
				druggie(0);
				echo Message("You successfully used some Meth. Your speed has been increased for 15 minutes.");
				break;
			case 28:
				druggie(1);
				echo Message("You successfully used some Adrenalin. Your defense has been increased for 15 minutes.");
				break;
			case 29:
				druggie(2);
				echo Message("You successfully used some PCP. Your strength has been increased for 15 minutes.");
				break;
			case 38:
				if (empty($_GET['cityid'])) {
					$db->query("SELECT id, name, levelreq FROM cities ORDER BY levelreq DESC");
					$db->execute();
					$rows = $db->fetch_row();
					$opts = "";
					foreach($rows as $row)
						$opts .= "<option value='{$row['id']}'>{$row['name']} (LVL: {$row['levelreq']})</option>";
					echo'<form method="get">';
						echo'<select name="cityid">';
							echo $opts;
						echo'</select>';
						echo'<input type="hidden" name="use" value="38" />';
						echo'<input type="submit" value="Move to City" />';
					echo'</form>';
					diefun();
				} else {
					$cid = security($_GET['cityid']);
					$db->query("SELECT * FROM cities WHERE id = ?");
					$db->execute(array(
						$cid
					));
					if($db->fetch_row()){
						$db->query("UPDATE grpgusers SET city = ? WHERE id = ?");
						$db->execute(array(
							$cid,
							$user_class->id
						));
						echo Message("You have moved cities for free!");
					} else
						diefun("City does not exist.");
				}
				break;
			case 42:
				$randnum = rand(0, 100);
				if ($randnum == 1)
					$randpoints = rand(10000, 17000);
				elseif ($randnum >= 2 && $randnum <= 15)
					$randpoints = rand(2000, 3000);
				elseif ($randnum >= 16 && $randnum <= 30)
					$randpoints = rand(500, 1000);
				elseif ($randnum >= 31 && $randnum <= 65)
					$randpoints = rand(180, 350);
				else
					$randpoints = rand(150, 250);
				$user_class->points += $randpoints;
				$db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
				$db->execute(array(
					$randpoints,
					$user_class->id
				));
				$jp = ($randpoints >= 10000) ? "You hit the <span style='color:red;font-weight:bold;'>Jackpot</span>! " : "";
				echo Message("{$jp}You have taken <span style='color:green;font-weight:bold;'>$randpoints</span> Points from the Mystery Box.");
				break;
			case 51:
				add_rm_days(30);
				echo Message("You have added 30 RY days to your account.");
				break;
			case 103:
				add_rm_days(60);
				echo Message("You have added 30 RY days to your account.");
				break;
			case 104:
				add_rm_days(90);
				echo Message("You have added 30 RY days to your account.");
				break;
		}
		Take_Item($id, $user_class->id);
	}
}
function add_rm_days($days){
	global $db, $user_class;
	$db->query("UPDATE grpgusers SET rmdays = rmdays + ? WHERE id = ?");
	$db->execute(array(
		$days,
		$user_class
	));
}
function druggie($num){
	global $db, $user_class;
	$drugs = explode("|", $user_class->drugs);
	$drugs[0] = !empty($drugs[0]) ? $drugs[0] : 0;
	$drugs[1] = !empty($drugs[1]) ? $drugs[1] : 0;
	$drugs[2] = !empty($drugs[2]) ? $drugs[2] : 0;
	$drugs[$num] = time();
	$db->query("UPDATE grpgusers SET drugs = ? WHERE id = ?");
	$db->execute(array(
		implode("|", $drugs),
		$user_class->id
	));
}
genHead("Equipped");
        echo'<table width="100%">';
            echo'<tr>';
                echo'<td width="33.3%" align="center">';
				if ($user_class->eqweapon != 0) {
					echo image_popup($user_class->weaponimg, $user_class->eqweapon);
					echo'<br />';
					echo item_popup($user_class->weaponname, $user_class->eqweapon);
					echo'<br />';
					echo'<a class="button-sm" href="equip.php?unequip=weapon">Unequip</a>';
				} else
					echo'<img width="100" height="100" src="/images/noneequipped.png" /><br /> You are not holding a weapon.';
                echo'</td>';
                echo'<td width="33.3%" align="center">';
				if ($user_class->eqarmor != 0) {
					echo image_popup($user_class->armorimg, $user_class->eqarmor);
					echo'<br />';
					echo item_popup($user_class->armorname, $user_class->eqarmor);
					echo'<br />';
					echo'<a class="button-sm" href="equip.php?unequip=armor">Unequip</a>';
				} else 
					echo'<img width="100" height="100" src="/images/noneequipped.png" /><br /> You are not wearing armour';
                echo'</td>';
                echo'<td width="33.3%" align="center">';
				if ($user_class->eqshoes != 0) {
					echo image_popup($user_class->shoesimg, $user_class->eqshoes);
					echo'<br />';
					echo item_popup($user_class->shoesname, $user_class->eqshoes);
					echo'<br />';
					echo'<a class="button-sm" href="equip.php?unequip=shoes">Unequip</a>';
				} else
					echo'<img width="100" height="100" src="/images/noneequipped.png" /><br /> You are not wearing boots.';
                echo'</td>';
            echo'</tr>';
        echo'</table>';
    echo'</td>';
echo'</tr>';
$db->query("SELECT inv.*, it.*, c.name overridename, c.image overrideimage FROM inventory inv JOIN items it ON inv.itemid = it.id LEFT JOIN customitems c ON it.id = c.itemid AND c.userid = inv.userid WHERE inv.userid = ?");
$db->execute(array(
	$user_class->id
));
$rows = $db->fetch_row();
foreach($rows as $row){
	$subtype = '';
	if(!empty($row['overrideimage']) || !empty($row['overridename'])){
		$row['image'] = $row['overrideimage'];
		$row['itemname'] = $row['overridename'];
	}
	$sell = ($row['cost'] > 0) ? "<a class='button-sm' href='sellitem.php?id=" . $row['id'] . "'>Sell</a>" : "";
	if($row['offense'] > 0 && $row['rare'] == 0)
		$type = 'weapon';
	elseif($row['defense'] > 0 && $row['rare'] == 0)
		$type = 'armor';
	elseif($row['speed'] > 0 && $row['rare'] == 0)
		$type = 'shoes';
	elseif($row['rare'] == 1){
		$type = 'rare';
		if($row['offense'])
			$subtype = 'weapon';
		if($row['defense'])
			$subtype = 'armor';
		if($row['speed'])
			$subtype = 'shoes';
	} else
		$type = 'consumable';
	gendivs($row, $type, $sell, $subtype);
}
$db->query("SELECT * FROM gang_loans gl JOIN items i ON gl.item = i.id WHERE idto = ?");
$db->execute(array(
	$user_class->id
));
$rows = $db->fetch_row();
foreach($rows as $row)
	gendivs($row, 'loans');
$master = array(
	'Weapons' => 'weapon',
	'Armor' => 'armor',
	'Shoes' => 'shoes',
	'Gang Loans' => 'loans',
	'Rares' => 'rares',
	'Consumables' => 'consumables'
);
foreach($master as $header => $var)
	if(isset($$var)){
		genHead($header);
			echo'<div class="flexcont" style="text-align:center;">';
				echo $$var;
			echo'</div>';
	}
include 'footer.php';
function gendivs($row, $type, $sell = null, $subtype = null){
	global $$type;
	$$type .= '<div class="flexele" style="flex-basis:25%;margin-bottom:12px;margin-top:12px;">';
		$$type .= image_popup($row['image'], $row['id']);
		$$type .= '<br />';
		$$type .= item_popup($row['itemname'], $row['id']) . ' [x' . $row['quantity'] . ']';
		$$type .= '<br />';
		$$type .= '<br />';
		$$type .= prettynum($row['cost'], 1);
		$$type .= '<br />';
		$$type .= '<br />';
		$$type .= $sell;
		if ($type == "consumable")
			$$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Use</a> ';
		$$type .= ' <a class="button-sm" href="putonmarket.php?id=' . $row['id'] . '">Market</a> ';
		$$type .= ' <a class="button-sm" href="senditem.php?id=' . $row['id'] . '">Send</a> ';
		if(in_array($type, array('weapon', 'armor', 'shoe')) || in_array($subtype, array('weapon', 'armor', 'shoe'))){
			$$type .= ' <a class="button-sm" href="equip.php?eq=';
			$$type .= (isset($subtype)) ? $subtype : $type;
			$$type .= '&id=' . $row['id'] . '">Equip</a> ';
		}
	$$type .= '</div>';
}
?> 