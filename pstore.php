<?php
include 'header.php';
if (isset($_GET['buy'])) {
    $resultnew = mysql_query("SELECT * from `items` WHERE `id` = '" . $_GET['buy'] . "' and `buyable` = '2'");
    $worked = mysql_fetch_array($resultnew);
    if ($worked['id'] != "") {
        if ($user_class->money >= $worked['cost']) {
            $newmoney = $user_class->money - $worked['cost'];
            perform_query("UPDATE `grpgusers` SET `money` = ? WHERE `id`= ?", [$newmoney, $user_class->id]);
            Give_Item($_GET['buy'], $user_class->id);//give the user their item they bought
            echo Message("You have purchased a " . $worked['itemname'] . ".");
        } else {
            echo Message("You do not have enough money to buy a " . $worked['itemname'] . ".");
        }
    } else {
        echo Message("That isn't a real item.");
    }
}
$result = mysql_query("SELECT * FROM `items`");
$howmanyitems = 0;
while ($line = mysql_fetch_array($result, mysql_ASSOC)) {
    if ($line['offense'] > 0 && $line['buyable'] == 2 && $line['city'] == $user_class->city) {
        $weapons .= "
		<td width='25%' align='center'>
						<img src='" . $line['image'] . "' width='100' height='100' style='border: 0px solid #000000'><br>
						" . item_popup($line['itemname'], $line['id']) . "<br>
						$" . prettynum($line['cost']) . "<br>
						[<a href='wstore.php?buy=" . $line['id'] . "'>Buy</a>]
					</td>
		";
        $howmanyitems = $howmanyitems + 1;
        if ($howmanyitems == 4) {
            $weapons .= "</tr><tr height='15'></tr><tr>";
            $howmanyitems = 0;
        }
    }
}
if ($weapons != "") {
    if ($armour != "") {
        if ($shoes != "") {
            ?>
            <tr>
                <td class="contentspacer"></td>
            </tr>
            <tr>
                <div class="contenthead">Prestige Store</div>
            </tr>
            <tr>
                <td class="contentcontent">
                    <table width='100%'>
                        <tr>
                            <?php echo $weapons; ?>
                        </tr>
                        <tr>
                            <?php echo $armour; ?>
                        </tr>
                        <tr>
                            <?php echo $shoes; ?>
                        </tr>
                    </table>
                </td>
            </tr>
            <?php
        }
    }
}
include 'footer.php'
    ?>