<?php
include 'header.php';
if (isset($_GET['buy'])) {
    $db->query("SELECT * FROM items WHERE id = ? AND buyable = 1");
    $db->execute([$_GET['buy']]);
    $worked = $db->fetch_row();
    if ($worked['id'] != "") {
        if ($user_class->money >= $worked['cost']) {
            $newmoney = $user_class->money - $worked['cost'];
            perform_query("UPDATE `grpgusers` SET `money` = ? WHERE `id`= ?", [$newmoney, $user_class->id]);
            Give_Item($_GET['buy'], $user_class->id);//give the user their item they bought
            echo Message("You have purchased some " . $worked['itemname'] . ".");
        } else {
            echo Message("You do not have enough money to buy any " . $worked['itemname'] . ".");
        }
    } else {
        echo Message("That isn't a real item.");
    }
}

$db->query("SELECT * FROM items");
$db->execute();
$items = $db->fetch_row();
$howmanyitems = 0;
foreach ($items as $line) {
    if ($line['drugstime'] > 0 && $line['buyable'] == 1) {
        $drugs .= "

		<td width='25%' align='center'>

						<img src='" . $line['image'] . "' width='100' height='100' style='border: 1px solid #000000'><br>
						" . drug_popup($line['itemname'], $line['id']) . "<br>
						$" . prettynum($line['cost']) . "<br>
						[<a href='drugdealer.php?buy=" . $line['id'] . "'>Buy</a>]
					</td>
		";
        $howmanyitems = $howmanyitems + 1;
        if ($howmanyitems == 4) {
            $drugs .= "</tr><tr height='15'></tr><tr>";
            $howmanyitems = 0;
        }
    }
}

if ($drugs != "") {
    ?>
    <tr>
        <td class="contentspacer"></td>
    </tr>
    <tr>
        <td class="contenthead">Drug Dealer</td>
    </tr>
    <tr>
        <td class="contentcontent">
            <table width='100%'>
                <tr>
                    <?php echo $drugs; ?>
                </tr>
            </table>
        </td>
    </tr>
    <?php
}
include 'footer.php'
    ?>