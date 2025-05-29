<?php
include("header.php");
if ($user_class->chase == 0) {
    echo Message("You have already played the car chase game today.");
    include("footer.php");
    die();
}
if ($user_class->hospital > 0) {
    echo Message("You can't play the car chase game in hospital.");
    include("footer.php");
    die();
}
if ($user_class->jail > 0) {
    echo Message("You can't play the car chase game in prison.");
    include("footer.php");
    die();
}
$goarray = array("up", "left", "right", "down");
if ($_GET['go'] == $goarray['0'] || $_GET['go'] == $goarray['1'] || $_GET['go'] == $goarray['2'] || $_GET['go'] == $goarray['3']) {
    $dir = rand(1, 4);
    switch ($dir) {
        case 1:
            $dir = "up";
            break;
        case 2:
            $dir = "left";
            break;
        case 3:
            $dir = "right";
            break;
        case 4:
            $dir = "down";
            break;
    }
    if ($_GET['go'] == $dir) {
        $newmoney = $user_class->money + 50000;
        perform_query("UPDATE `grpgusers` SET `money` = ?, `chase` = '0' WHERE `id` = ?", [$newmoney, $user_class->id]);
        echo Message("You turned " . $_GET['go'] . " and received $50,000.");
    } else {
        echo Message("Your car has crashed! Please come back tommorow.");
        perform_query("UPDATE `grpgusers` SET `chase` = '0' WHERE `id` = ?", [$user_class->id]);
    }
}
?>
<tr>
    <td class="contentspacer"></td>
</tr>
<tr>
    <td class="contenthead">Car Chase</td>
</tr>
<tr>
    <td class="contentcontent">
        <center>Welcome to Car Chase. You can play this once every day. The idea of the game is to guess which way the
            car is going to go. If you guess right, you will receive $50,000! If not, your game will end.
            <br /><br />
            <table width="40%" align="center" cellspacing="0">
                <tr>
                    <td align="center"></td>
                    <td align="center"><a href="chase.php?go=up"><img src="images/up.gif" border="0" /></a></td>
                    <td align="center"></td>
                </tr>
                <tr>
                    <td height="60px" valign="middle" align="center"><a href="chase.php?go=left"><img
                                src="images/left.gif" border="0" /></a></td>
                    <td height="60px" valign="middle" align="center"></td>
                    <td height="60px" valign="middle" align="center"><a href="chase.php?go=right"><img
                                src="images/right.gif" border="0" /></a></td>
                </tr>
                <tr>
                    <td align="center"></td>
                    <td align="center"><a href="chase.php?go=down"><img src="images/down.gif" border="0" /></a></td>
                    <td align="center"></td>
                </tr>
            </table>
    </td>
</tr>
<?php
include("footer.php");
?>