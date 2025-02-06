<?php
include 'header.php';
?>

	<div class='box_top'>Pet Crime</div>
						<div class='box_middle'>
							<div class='pad'>
								<?php
$jailtime = 5;
$successchance = 92;
$jailchance = 2;
$q = mysql_query("SELECT petid FROM pets WHERE userid = $user_class->id");
if (!mysql_num_rows($q))
    diefun("You don't have a pet");
$pet_class = new Pet($user_class->id);
if ($pet_class->jail)
    diefun("Your pet can't do crimes whilst in the pound");
$q = mysql_query("SELECT id, pethex FROM grpgusers WHERE id = $user_class->id");
$hex = mysql_fetch_array($q);
$_GET['id'] = isset($_GET['id']) && ctype_digit($_GET['id']) ? $_GET['id'] : null;
if (!empty($_GET['id'])) {
    $y = mysql_query("SELECT * FROM petcrimes WHERE id = {$_GET['id']}");
    if (!mysql_num_rows($y))
        diefun("That pet crime doesn't exist");
    $row = mysql_fetch_array($y);
    $chance = rand(1, 100);
    $money = (50 * $row['nerve']) + 15 * ($row['nerve'] - 1);
    $exp = (10 * $row['nerve']) + 2 * ($row['nerve'] - 1);
	$exp *= 1.0;

    $researchAddBoost = 0;
    if (isset($user_class->completeUserResearchTypesIndexedOnId[39])) {
        $researchAddBoost += 5;
    }
    if (isset($user_class->completeUserResearchTypesIndexedOnId[42])) {
        $researchAddBoost += 5;
    }
    if ($researchAddBoost > 0) {
        $resAddInc = $exp / 100 * $researchAddBoost;
        $exp = $exp + $resAddInc;
    }
    $exp = ceil($exp);

    if ($row['nerve'] > $pet_class->nerve && !pet_refill('n'))
        echo Message("Your pet doesn't have enough nerve");
    elseif ($chance <= $successchance) {
        if (isset($user_class->completeUserResearchTypesIndexedOnId[41])) {
            $money = $row['nerve'] * 100;
            mysql_query("UPDATE pets SET exp = exp + {$exp}, nerve = nerve - {$row['nerve']} WHERE userid = $user_class->id");
            mysql_query("UPDATE grpgusers SET money = money + $money WHERE id = $user_class->id");
            addToPetladder($pet_class->id, 'exp', $exp);
            echo Message("Your pet successfully managed to {$row['name']}<br />Your pet received $exp exp & you gained ${$money}.<br /><br /><a href='petcrime.php?id={$_GET['id']}'>Retry</a> | <a href='petcrime.php'>Back</a>");
        } else {
            mysql_query("UPDATE pets SET exp = exp + $exp, nerve = nerve - {$row['nerve']} WHERE userid = $user_class->id");
            addToPetladder($pet_class->id, 'exp', $exp);
            echo Message("Your pet successfully managed to {$row['name']}<br />Your  pet received $exp exp.<br /><br /><a href='petcrime.php?id={$_GET['id']}'>Retry</a> | <a href='petcrime.php'>Back</a>");
        }
    } elseif ($chance <= $successchance + $jailchance) {
        $pet_class->nerve -= $row['nerve'];
        mysql_query("UPDATE pets SET jail = ($jailtime*60), nerve = $pet_class->nerve WHERE userid = $user_class->id");
        echo Message("Your pet failed to {$row['name']}<br />Your pet was hauled off to prison for $jailtime minutes.<br /><br /><a href='petcrime.php?id={$_GET['id']}'>Retry</a> | <a href='petcrime.php'>Back</a>");
    } else {
        mysql_query("UPDATE pets SET nerve = nerve - {$row['nerve']} WHERE userid = $user_class->id");
        echo Message("Your pet failed to {$row['name']}<br /><br /><a href='petcrime.php?id={$_GET['id']}>Retry</a> | <a href='petcrime.php'>Back</a>");
    }
}
include 'includepet.php';
print '<center><br /><a href="?spend=pnerve" id="botlink">Refill Pet Nerve (10 Points)</a><br /><br />
<table id="newtables">
    <tr>
        <th width="50%">Name</th>
        <th width="25%">Nerve</th>
        <th width="25%">Action</th>
    </tr>';
$q = mysql_query("SELECT * FROM petcrimes ORDER BY nerve ASC");
while ($row = mysql_fetch_array($q))
    echo "
            <tr>
                <td>{$row['name']}</td>
                <td>", prettynum($row['nerve']), "</td>
                <td>[<a href='petcrime.php?id={$row['id']}'>do</a>]</td>
            </tr>
        ";
print "</table></center>
</td></tr>";
include 'footer.php';
