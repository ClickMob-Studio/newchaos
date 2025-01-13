<?php
include 'header.php';

?>
<div class='box_top'>Search The City</div>
						<div class='box_middle'>
							<div class='pad'>
                                <?php
if ($user_class->searchdowntown == 0) {
    diefun("You have already searched the city as much as you can, check back in an hour for more searches.");
}

$totalpts = $total = $totalRaidTokens = 0;
$totalCraftingItems = 0;
$rows = "";

for ($i = 1; $i <= 100; $i++) {
    payoutChristmasGift($user_class->id);
    $row .= '<tr>';
    $row .= '<th>' . $i . '.</th>';
    $row .= '<td>';
    $randnum = rand(1, 10000);

    if ($randnum <= 7500) {
        $points = rand(8, 17);
        $totalpts += $points;
        $row .= "<span style='color:#4C4CFF;'>You found $points points!</span>";
    } elseif ($randnum <= 7700) {
        $db->query("SELECT * FROM items WHERE category = 'crafting' AND searchable > 0");
        $db->execute();
        $craftingItems = $db->fetch_row();

        $craftingItem = $craftingItems[rand(0, count($craftingItems) - 1)];

        Give_Item($craftingItem['id'], $user_class->id, 1);

        $totalCraftingItems++;

        $row .= "<span style='color:orange;'>You found a " . $craftingItem['itemname'] . "!</span>";
    } elseif ($randnum <= 9500) {
        $money = rand(10, 64) * ($user_class->level + 2);

        $researchMoneyBoost = 0;
        if (isset($user_class->completeUserResearchTypesIndexedOnId[14])) {
            $researchMoneyBoost += 25;
        }
        if (isset($user_class->completeUserResearchTypesIndexedOnId[37])) {
            $researchMoneyBoost += 25;
        }
        if (isset($user_class->completeUserResearchTypesIndexedOnId[47])) {
            $researchMoneyBoost += 25;
        }
        if ($researchMoneyBoost > 0) {
            $resMInc = $money / 100 * $researchMoneyBoost;
            $money = $money + $resMInc;
        }
        $money = ceil($money);

        $row .= "<span style='color:green;'>You found " . prettynum($money, 1) . "!</span>";
        $total += $money;
    } elseif ($randnum <= 9650) { // 1% chance for Raid Tokens
        $raidTokens = rand(1, 2);
        $row .= "<span style='color:purple;'>You found $raidTokens Raid Tokens!</span>";
        $totalRaidTokens += $raidTokens;
    } elseif ($randnum < 10000) {
        $row .= "<span style='color:red;'>You found nothing.</span>";
    } else {
        $row .= "<span style='color:gold;font-size:2em;'>You have found a Police Pass [1 Hour]!</span>";
        Give_Item(163, $user_class->id, 1);
    }

    $row .= '</td>';
    $row .= '</tr>';
}

echo '<table id="newtables">';
echo '<tr>';
echo '<th colspan="2">You found a total of <span style="color:green;">' . prettynum($total, 1) . '</span>, <span style="color:#4C4CFF;">' . $totalpts . ' points</span>, <span style="color:yellow;">' . $totalCraftingItems . ' Crafting Items</span>, and <span style="color:purple;">' . $totalRaidTokens . ' Raid Tokens</span> searching the city!</th>';
echo '</tr>';
echo $row;
echo '</table>';

$user_class->money += $total;
$user_class->points += $totalpts;
$user_class->raidtokens += $totalRaidTokens; // Assuming you have a column "raidtokens" in your user table

$db->query("UPDATE grpgusers SET money = ?, points = ?, searchdowntown = 0, raidtokens = raidtokens + ? WHERE id = ?");
$db->execute(array(
    $user_class->money,
    $user_class->points,
    $totalRaidTokens,
    $user_class->id
));

include 'footer.php';
?>
