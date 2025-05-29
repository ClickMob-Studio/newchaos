<?php
include 'header.php';
if (isset($_GET['buy'])) {
    $resultnew = mysql_query("SELECT * from `planes` WHERE `id` = '" . $_GET['buy'] . "'");
    $worked = mysql_fetch_array($resultnew);
    $resultcheck = mysql_query("SELECT * FROM `hangar` WHERE `userid` = '" . $user_class->id . "' AND `planeid` = '" . $_GET['buy'] . "'");
    $check = mysql_num_rows($resultcheck);
    if ($worked['id'] != "") {
        if ($check == 0) {
            if ($user_class->money >= $worked['cost']) {
                $newmoney = $user_class->money - $worked['cost'];
                perform_query("UPDATE `grpgusers` SET `money` = ? WHERE `id`= ?", [$newmoney, $user_class->id]);
                perform_query("INSERT INTO `hangar` VALUES(?, ?)", [$user_class->id, $_GET['buy']]);
                echo Message("You have purchased a " . $worked['name'] . ".");
            } else {
                echo Message("You do not have enough money to buy a " . $worked['name'] . ".");
            }
        } else {
            echo Message("You have already purchased a " . $worked['name'] . ".");
        }
    } else {
        echo Message("That isn't a real item.");
    }
}
$result = mysql_query("SELECT * FROM `planes`");
$howmanyitems = 0;
while ($line = mysql_fetch_array($result, mysql_ASSOC)) {
    if ($line['buyable'] == 1) {
        $cars .= "	
		<td width='25%' align='center'>
	
						<img src='" . $line['image'] . "' width='100' height='100' style='border: 1px solid #000000'><br>
						" . plane_popup($line['name'], $line['id']) . "<br>
						$" . prettynum($line['cost']) . "<br>
						[<a href='planeshop.php?buy=" . $line['id'] . "'>Buy</a>]
					</td>
		";
        $howmanyitems = $howmanyitems + 1;
        if ($howmanyitems == 4) {
            $cars .= "</tr><tr height='15'></tr><tr>";
            $howmanyitems = 0;
        }
    }
}
?>
<tr>
    <td class="contentspacer"></td>
</tr>
<tr>
    <td class="contenthead">Airplane Shop</td>
</tr>
<tr>
    <td class="contentcontent">
        <?php
        if ($user_class->level >= "500") {
            ?>
            <center>
                <font size="1px">
                    <font color=white>WARNING! You have not got your pilot License Yet, You must go and do lessons to earn
                        it, You can do lessons here --> <a href="flightschool.php">Flight School</a></b></font>
                </font>
                </font>
            </center>
            <?php
        }
        ?>
        Welcome to the Airplane Shop! Take your pick from any of the airplanes we have in the shop.<br /><br />
        <?php
        echo "
<table width='100%'>
				<tr>
				" . $cars . "
				</tr>
			</table>
</td></tr>";
        include 'footer.php'
            ?>