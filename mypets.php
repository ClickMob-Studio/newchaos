<?php
include 'header.php';
include 'includepet.php';
?>

	<div class='box_top'>My Pets</div>
						<div class='box_middle'>
							<div class='pad'>
								<?php
$_GET['pet'] = isset($_GET['pet']) && ctype_digit($_GET['pet']) ? $_GET['pet'] : null;
$q = mysql_query("SELECT userid FROM petmarket WHERE userid = $user_class->id");
if (mysql_num_rows($q))
    diefun("Your pet is on the market");
$q = mysql_query("SELECT petid FROM pets WHERE userid = $user_class->id");
if (!mysql_num_rows($q))
    header('location: petshop.php');
if (isset($_GET['name']) && $_GET['name'] == 'change' && !empty($_GET['pet'])) {
    $q = mysql_query("SELECT pname FROM pets WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
    if (!mysql_num_rows($q))
        diefun("Either that pet doesn't exist, or it's not yours");
    $name = mysql_result($q, 0, 0);
    if (array_key_exists('name', $_POST)) {
        $_POST['name'] = isset($_POST['name']) ? trim($_POST['name']) : null;
        if (empty($_POST['name']))
            diefun("You didn't enter a valid name");
        if (strlen($_POST['name']) < 3)
            diefun("Your pet's name must be at least 3 characters");
        if (strlen($_POST['name']) > 10)
            diefun("Your pet's name can be no longer than 10 characters");
        mysql_query("UPDATE pets SET pname = '{$_POST['name']}' WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
        echo Message("You've changed your pet's name");
    } elseif (array_key_exists('cn', $_POST)) {
        if (strlen($_POST['startcolor']) != 6)
            diefun("Error.");
        if (strlen($_POST['endcolor']) != 6)
            diefun("Error.");
        $colors[] = $_POST['startcolor'];
        $colors[] = $_POST['endcolor'];
        mysql_query("UPDATE pets SET coloredname = '" . implode("|", $colors) . "' WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
        echo Message("You've changed your pet's gradient name.");
    } elseif (array_key_exists('buycolor', $_POST)) {
        if ($user_class->credits < 3)
            diefun("You do not have enough credits.");
        $colors[] = "FF0000";
        $colors[] = "FF0000";
        mysql_query("UPDATE pets SET coloredname = '" . implode("|", $colors) . "' WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
        mysql_query("UPDATE grpgusers SET credits = credits - 3 WHERE id = $user_class->id");
        echo Message("You've added a colored name to your pet.");
    } else {
        $petinfo = new Pet($user_class->id);
        print"<form action='mypets.php?pet={$_GET['pet']}&amp;name=change' method='post'>
			<strong>New Name:</strong> <input type='text' name='name' placeholder='$name' /><br />
			<input type='submit' name='submit' value='Change Pet Name' />
		</form>";
        if ($petinfo->coloredname != "FFFFFF|FFFFFF") {
            $colors = explode("|", $petinfo->coloredname);
            print"
                <script type='text/javascript' src='js/cp/jscolor.js'></script>
                <form method='post'>
                    <table id='newtables' style='width:100%;'>
                        <tr>
                            <th colspan='4'>Choose your pet's Gradient Name</td>
                        </tr>
                        <tr>
                            <th>Starting Colour</th>
                            <td><input type='text' class='color' value='{$colors[0]}' name='startcolor'></td>
                            <th>Ending Colour</th>
                            <td><input type='text' class='color' value='{$colors[1]}' name='endcolor'></td>
                        </tr>
                        <tr>
                            <td colspan='4' class='center'><input type='submit' name='cn' value='Save Preferences' /></td>
                        </tr>
                    </table>
                </form>
            ";
        } else {
            print"<br /><br />
                <form method='post'>
                    <input type='submit' name='buycolor' value='Buy Pet Colored Name (3 Credits)' /> (One time buy)
                </form>
            <br />";
        }
    }
}
if (array_key_exists('avi', $_POST)) {
	$avi = $_POST['avi'];
	if(!getimagesize($avi) && $avi != '')
		diefun("Invalid image detected.");
	if($avi == ''){
		$db->query("SELECT picture FROM petshop ps JOIN pets p ON ps.id = p.petid WHERE userid = ?");
		$db->execute(array(
			$user_class->id
		));
		$avi = $db->fetch_single();
	}
	$db->query("UPDATE pets SET avi = ? WHERE userid = ?");
	$db->execute(array(
		$avi,
		$user_class->id
	));
	mysql_query("UPDATE pets SET avi = '" . implode("|", $colors) . "' WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
	echo Message("You've changed your pet's avatar.");
} elseif(isset($_GET['avi'])){
		$petinfo = new Pet($user_class->id);
        print"<form action='mypets.php' method='post'>
			<strong>New Avatar:</strong> <input type='text' name='avi' placeholder='$petinfo->avi' /><br />
			<input type='submit' name='submit' value='Change Pet Avatar' />
		</form>";
}
$_GET['use'] = isset($_GET['use']) && ctype_digit($_GET['use']) ? $_GET['use'] : null;
if (!empty($_GET['use'])) {
    $q = mysql_query("SELECT itemname FROM items WHERE id = {$_GET['use']}");
    if (!mysql_num_rows($q))
        diefun("That item doesn't exist");
    $item = mysql_result($q, 0, 0);
    $q = mysql_query("SELECT quantity FROM inventory WHERE userid = $user_class->id AND itemid = {$_GET['use']}");
    if (!mysql_num_rows($q))
        diefun("You don't own that item");
    switch ($_GET['use']) {
        case 14:
            mysql_query("UPDATE grpgusers SET awake = $user_class->maxawake WHERE id = $user_class->id");
            Take_Item($_GET['use'], $user_class->id);
            echo Message("You've popped an awake pill");
            break;
        default:
            diefun("lulwut");
            break;
    }
}
if (isset($_GET['leash']) && !empty($_GET['pet'])) {
    $_GET['leash'] = isset($_GET['leash']) && in_array($_GET['leash'], array(
                0,
                1
            )) ? $_GET['leash'] : 0;
    $q = mysql_query("SELECT * FROM pets WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
    if (!mysql_num_rows($q))
        diefun("Either that pet doesn't exist or it's not yours");
    $row = mysql_fetch_array($q);
    if ($row['leash'] == 1 && $_GET['leash'] == 1)
        diefun("This pet is already leashed.");
    if ($row['leash'] == 0 && $_GET['leash'] == 0)
        diefun("This pet is already unleashed.");
    mysql_query("UPDATE pets SET leash = {$_GET['leash']} WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
    $opts = array(
        0 => 'unleashed',
        1 => 'leashed'
    );
    echo Message("You've {$opts[$_GET['leash']]} your pet");
}

if (isset($_GET['raid_leash']) && !empty($_GET['pet'])) {
    $_GET['raid_leash'] = isset($_GET['raid_leash']) && in_array($_GET['raid_leash'], array(
        0,
        1
    )) ? $_GET['raid_leash'] : 0;
    $q = mysql_query("SELECT * FROM pets WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
    if (!mysql_num_rows($q))
        diefun("Either that pet doesn't exist or it's not yours");
    $row = mysql_fetch_array($q);
    if ($row['raid_leash'] == 1 && $_GET['raid_leash'] == 1)
        diefun("This pet is already joining you in raids.");
    if ($row['raid_leash'] == 0 && $_GET['raid_leash'] == 0)
        diefun("This pet is already not joining you in raids.");
    mysql_query("UPDATE pets SET raid_leash = {$_GET['raid_leash']} WHERE userid = $user_class->id AND petid = {$_GET['pet']}");
    $opts = array(
        0 => 'unleashed for raids',
        1 => 'leashed for raids'
    );
    echo Message("You've {$opts[$_GET['leash']]} your pet");
}
print'<script type="text/javascript">
function leash(value,pets) {
	if(value!="")
		window.location="mypets.php?leash="+value+"&pet="+pets;
}

function raidLeash(value,pets) {
	if(value!="")
		window.location="mypets.php?raid_leash="+value+"&pet="+pets;
}
</script>';
$q = mysql_query("SELECT * FROM pets WHERE userid = $user_class->id ORDER BY petid ASC");
print"<tr><td class='contentcontent'>
<table id='newtables' style='width:100%;'>
	<tr>
		<th colspan='6'>My Pet</th>
	</tr>";
while ($row = mysql_fetch_array($q)) {
    $y = mysql_query("SELECT * FROM petshop WHERE id = {$row['petid']}");
    $pet = mysql_fetch_array($y);
    $petinfo = new Pet($user_class->id);
    echo "<tr class='center'>
			<td><img src='$petinfo->avi' width='100' height='100'> <br /><br /> <a href='mypets.php?avi' id='botlink'>Change Avatar</a><br /><br /></td>
			<td><br /><b>Pet Type:</b> {$pet['name']}<br /><br /><b>Pet Name:</b> ", $petinfo->formatName(), " <br /><br /> <a href='mypets.php?name=change&amp;pet={$row['petid']}' id='botlink'>Change Name</a><br /><br /></td>
			<td><br /><a href='loanpet.php' id='botlink'>Loan Pet</a>
				<br /><br /></td>
			<td>
				<b>Strength:</b> ", prettynum($row['str']), "<br /><br />
				<b>Speed:</b> ", prettynum($row['spe']), "<br /><br />
				<b>Defense:</b> ", prettynum($row['def']), "
			</td>
			<td>
				<select name='leash' onchange='javascript:leash(this.value,{$row['petid']});'>
					<option value='1'", ($row['leash']) ? " selected='selected'" : '', ">Leash</option>
					<option value='0'", (!$row['leash']) ? " selected='selected'" : '', ">Unleash</option>
				</select>
			</td>
			<td>
                <select name='raid_leash' onchange='javascript:raidLeash(this.value,{$row['petid']});'>
                    <option value='1'", ($row['raid_leash']) ? " selected='selected'" : '', ">Leash for Raids</option>
                    <option value='0'", (!$row['raid_leash']) ? " selected='selected'" : '', ">Unleash for Raids</option>
                </select>
            </td>
		</tr>
	</table>";
}
include 'footer.php';
