<?php
include 'header.php';

$result = mysql_query("SELECT * FROM `gameevents`");
$worked = mysql_fetch_array($result);

$cash_lottery_class = new User($worked['cashlotteryid']);
$cash_lottery = str_replace("[-_USER_-]", $cash_lottery_class->formattedname, $worked['cashlottery']);

$pts_lottery_class = new User($worked['ptslotteryid']);
$pts_lottery = str_replace("[-_USER_-]", $pts_lottery_class->formattedname, $worked['ptslottery']);

$hitman_class = new User($worked['tophitmanid']);
$hitman = str_replace("[-_USER_-]", $hitman_class->formattedname, $worked['tophitman']);

$leveler_class = new User($worked['toplevelerid']);
$leveler = str_replace("[-_USER_-]", $leveler_class->formattedname, $worked['topleveler']);
?>

<tr>
    <td class="contentspacer"></td>
</tr>
<tr>
    <td class="contenthead">Game Events</td>
</tr>
<tr>
    <td class="contentcontent">
        <b>Cash Lottery</b>
        <ul type="circle">
            <?php echo $cash_lottery; ?>
        </ul>

        <br /><br />

        <b>Points Lottery</b>
        <ul type="circle">
            <?php echo $pts_lottery; ?>
        </ul>

        <br /><br />

        <b>Top Hitman</b>
        <ul type="circle">
            <?php echo $hitman; ?>
        </ul>
        <br /><br />

        <b>Top Leveler</b>
        <ul type="circle">
            <?php echo $leveler; ?>
        </ul>
    </td>
</tr>
<?php

perform_query("UPDATE `grpgusers` SET `gameevents` = '1' WHERE `id` = ?", [$user_class->id]);

include 'footer.php';
?>