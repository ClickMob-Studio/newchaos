<?php
include("header.php");
if(isset($_POST['resetref'])) {
	$result = mysql_query("UPDATE `grpgusers` SET `killcomp` = '0'");
	echo Message("The kill counts have been reset.");
}
if(isset($_POST['resetexp'])) {
	$result = mysql_query("UPDATE `grpgusers` SET `expcount` = '0'");
	echo Message("The exp counts have been reset.");
}
?>
<div class="floaty">
    <h2 class="text-center mb-0" style="color:#7eff11">Valentines Day Event</h2>
    <h2 class="text-14 m-0">4th - 15th Feb 2022 (Ends 23:59 15th)</h2>
    <p class="text-14">Earn <?php echo item_popup('Heart\'s', 155, '#7eff11') ?> based on your activity level</p>
        <img src='https://themafialife.com/css/newgame/items//heart.png' width="100px">
    <div style="text-align:left;padding:10px;">
        <p class="text-14"><?php echo item_popup('Heart\'s', 155, '#7eff11') ?> will appear in your inventory and you can share with another player - you will both receive a random reward (same for both).</p>
        <p class="text-14">Possible rewards:
            <ul style="list-style:none" class="text-14">
                <li>Money (Sent to Bank)</li>
                <li>Points</li>
                <li><?php echo item_popup('Attack Protection Pill', 9, '#7eff11') ?></li>
                <li><?php echo item_popup('Mug Protection Pill', 8, '#7eff11') ?></li>
                <li><?php echo item_popup('Double EXP Pill', 10, '#7eff11') ?></li>
            </ul></p>
        <p class="text-14">So the more active you are the more <?php echo item_popup('Heart\'s', 155, '#7eff11') ?> you will earn - make sure to share the love!</p>
        <!-- <h2 class="text-center mb-0">Who will you share your <?php echo item_popup('Heart\'s', 155, '#7eff11') ?> with?</h2> -->
    </div>

    <!-- <h2 class="text-center m-0"  style="color:#7eff11">Can't wait? Need More Hearts?</h2>
    <div class="package" style="width:50%;margin:10px auto;border:1px white solid;padding:10px;">
        <p class="text-18 mt-0">Valentines Double - Limited Offer #2</p>
        <p class="text-14 m-0"><?php echo item_popup('Shamrock', 155, '#7eff11') ?> <strong>[x3]</strong> <br/>
            Cost: 100 Credits<br/>
            <?php echo $offer['remaining'] . ' / ' . $offer['total'] . ' Remaining';?>
        </p>
        <a class="cta" href="?buy=pack">[ Purchase ]</a>
    </div> -->
    <!-- <br/>
    <br/>
    <br/>
    <br/>
    <br/> -->

<table width="100%" style="border: 1px solid #444444;" cellpadding="4" cellspacing="0">
<tr>
<td style="border-right: 1px solid #444444;">
<center><b><u>Pumpkin Counts</u></b></center><br />
<table width="100%">
<tr>
<td><b>#</b></td>
<td><b>Username</b></td>
<td><b>Pumpkins Count</b></td>
</tr>
<?php
$result = mysql_query("SELECT * FROM `grpgusers` ORDER BY `halloween` DESC LIMIT 25");
$rank = 0;
while($line = mysql_fetch_array($result)) {
$rank++;
$user_name = new User($line['id']);
echo '<tr><td width="10%">'.$rank.'.</td><td width="55%">'.$user_name->formattedname.'</td><td width="35%">'.prettynum($line['halloween']).'  <img src="https://cdn.discordapp.com/attachments/983144300806299668/1031280107437957231/ezgif.com-gif-maker_13.png">
</td></tr>';



}
?>
</table>









</table>

</td></tr>
<?php

include("footer.php");
?>