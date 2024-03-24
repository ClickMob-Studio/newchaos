<?php
include 'header.php';
$result = mysql_query("SELECT * FROM `cars` WHERE `userid` = '" . $user_class->id . "' ORDER BY `userid` DESC");
while ($line = mysql_fetch_array($result, mysql_ASSOC)) {
    $result2 = mysql_query("SELECT * FROM `carlot` WHERE `id`='" . $line['carid'] . "'");
    $worked2 = mysql_fetch_array($result2);
    $cars .= "
		<td width='25%' align='center'>
		<img src='" . $worked2['image'] . "' width='100' height='100' style='border: 1px solid #000000'><br>
		" . car_popup($worked2['name'], $line['carid']) . "<br />
		[<a href='sellcar.php?id=" . $worked2['id'] . "'>Sell</a>]
		</td>
		";
    $howmanyitems = $howmanyitems + 1;
    if ($howmanyitems == 4) {
        $cars.= "</tr><tr>";
        $howmanyitems = 0;
    }
}
if ($cars != "") {
    ?>
    <tr><td class="contentspacer"></td></tr><tr><td class="contenthead">Your Garage</td></tr>
    <tr><td class="contentcontent">
            Here is where you store all of your sweet rides!<br /><br />
            <table width='100%'>
                <tr>
                    <?php echo $cars; ?>
                </tr>
            </table>
        </td></tr>
    <?php
} else {
    ?>
    <tr><td class="contentspacer"></td></tr><tr><td class="contenthead">Your Garage</td></tr>
    <tr><td class="contentcontent">
            You don't have any cars. If you wish to buy one, you can get one at the <a href="carlot.php">Car Lot</a>.
        </td></tr>
    <?php
}
include 'footer.php'
?>