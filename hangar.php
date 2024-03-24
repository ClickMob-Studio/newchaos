<?php
include 'header.php';
$result = mysql_query("SELECT * FROM `hangar` WHERE `userid` = '" . $user_class->id . "' ORDER BY `userid` DESC");
while ($line = mysql_fetch_array($result, mysql_ASSOC)) {
    $result2 = mysql_query("SELECT * FROM `planes` WHERE `id`='" . $line['planeid'] . "'");
    $worked2 = mysql_fetch_array($result2);
    $cars .= "
		<td width='25%' align='center'>
		<img src='" . $worked2['image'] . "' width='100' height='100' style='border: 1px solid #000000'><br>
		" . plane_popup($worked2['name'], $line['planeid']) . "<br />
		[<a href='sellplane.php?id=" . $worked2['id'] . "'>Sell</a>]
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
    <tr><td class="contentspacer"></td></tr><tr><td class="contenthead">Your Hangar</div></tr>
    <tr><td class="contentcontent">
            Here is where you store all of your airplanes.<br /><br />
            <table width='100%'>
                <tr>
                    <?php echo $cars; ?>
                </tr>
            </table>
        </td></tr>
    <?php
} else {
    ?>
    <tr><td class="contentspacer"></td></tr><tr><td class="contenthead">Your Hangar</div></tr>
    <tr><td class="contentcontent">
            You don't have any airplanes. If you wish to buy one, you can get one at the <a href="planeshop.php">Airplane Shop</a>.
        </td></tr>
    <?php
}
include 'footer.php'
?>