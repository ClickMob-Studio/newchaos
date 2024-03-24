<?php
include 'header.php';

// $banned = [35, 208, 26];
// if (in_array($user_class->id, $banned)) {
//     header('tos.php');
// }

if (isset($_POST['submit'])) {
    $cost = round($_POST['displaymins'] / 60 * 250000, 0);
    if (isset($_POST['glowText']) && $_POST['glowText'] == 'true') {
        $glow_cost = 1000000;  // Set Y to the cost of the glowing text feature
        $cost += $glow_cost;
    }

    if ($cost > $user_class->bank) {
        $error = "You don't have enough money in your bank for that!";
    } elseif (empty($_POST['message'])) {
        $error = "You need to have a message!";
    } else {
        $error = "";
    }

    if ($error == "") {
        $newmoney = $user_class->bank - $cost;
        $time = time();
        $newsql = mysql_query("UPDATE `grpgusers` SET `bank` = '" . $newmoney . "' WHERE `id`= '" . $user_class->id . "'");
        $result = mysql_query("INSERT INTO `ads`(`timestamp`,`poster`, `message`, `displaymins`, `glow`) VALUES ('" . $time . "', $user_class->id, '" . $_POST['message'] . "', '" . $_POST['displaymins'] . "', '" . (isset($_POST['glowText']) && $_POST['glowText'] == 'true' ? '1' : '0') . "')");
        echo Message("You have posted a classified ad for $" . $cost);
    } else {
        echo Message($error);
    }
}


?>

<script>
    function calcCost() {
        $('#cost').html('£' + Math.round($('input[name="displaymins"]').val() / 60 * 250000));
    }
</script>

<h2>Shoutbox</h2>
<p>Here you can post anything your heart desires. Cost is $250,000 for a 60 minute message, $1M for 4 hours, and so on..</p>

<form method='post' style='margin: 15px 0;'>
    <table width='100%'>
        <!-- <tr>
            <td width='25%'>Title:</td>
            <td width='25%'>
                <input type='text' name='title'  size='40' maxlength='100'>
            </td>
        </tr> -->
        <tr>
            <td width='25%'>Message:</td>
            <td width='25%'>
                <textarea name='message' cols='60' rows='4' maxlength='115'></textarea>
            </td>
        </tr>
        <tr>
            <td width='25%'>Minutes:</td>
            <td width='25%'>
                <input type='number' name='displaymins' min='3' value='60' oninput="calcCost();"> <span>Cost: <span class="text-yellow" id="cost">£250000</span></span>
            </td>




        </tr>
   <tr>
            <td width='25%'>Add glowing effect to your message (Costs additional $1,000,000):</td>
            <td width='25%'>
<input type="checkbox" id="glowText" name="glowText" value="true">
            </td>




        </tr>

        <tr>
            <td width='25%'>Submit:</td>
            <td width='25%'>
                <input type='submit' name='submit' value='Post'>
            </td>
        </tr>
    </table>
</form>

<h2>Current Ads</h2>

<?php
// $result = mysql_query("SELECT * FROM `ads` WHERE TIMESTAMPDIFF(MINUTE, NOW(), `timestamp`) + `displaymins` > 0 AND `flagcount` < 3 ORDER BY `timestamp` DESC");
$result = mysql_query("SELECT * FROM `ads` WHERE `timestamp` + `displaymins` * 60 > ".time()." ORDER BY `timestamp` DESC");
if (!mysql_num_rows($result)) {
    ?>
        <div class="floaty">
            <div class="flexcont">
                <div class="flexele" style="flex:3;padding:10px;word-break:break-word;">No messages at the moment! Use the form above to add one!</div>
            </div>
        </div>
    <?php
} else {   
    while ($row = mysql_fetch_array($result)) {
        $user_ads = New User($row['poster']);

        if ($user_ads->avatar == "")
            $user_ads->avatar = "/images/no-avatar.png";
    ?>
        <div class="floaty">
            <div class="flexcont" style="text-align:center;">
                <div class="flexele"><?php echo $row['timestamp'] ?></div>
                <div class="flexele"></div>
                <div class="flexele"></div>
                <div class="flexele" style="text-align:right;">
                    <a href="#" style="color:red;" onclick="reportAd(<?php echo $row['id'] ?>); return false;">
                        <img width="16" height="16" src="/css/images/icons/exclamation-mark_16.png" alt="Report" />
                    </a>
                    <!-- <input type="button" value="Report" onclick="reportAd(<?php echo $row['id'] ?>); return false;"> -->
                </div>
            </div>
            <hr style="border:0;border-top:thin solid #333;">
            <div class="flexcont">
                <div class="flexele" style="border-right:thin solid #333;text-align:center;">
                    <img src="<?php echo $user_ads->avatar ?>" height="75" width="75" style="border:1px solid #666666">
                    <br>
                    <?php echo $user_ads->formattedname ?>
                </div>
                <div class="flexele" style="flex:3;padding:10px;word-break:break-word;"><?php echo $row['message'] ?></div>
            </div>
        </div>
    <?php
    }
}
?>

<?php
include 'footer.php';
?>

<!-- <tr>
    <td class="contentcontent">
        <table width='100%'>
            <tr>
                <td width='15%'><?php echo $user_ads->formattedname ?></td>
                <td width='75%'><?php echo $row['message']; ?></td>
                <td width='10%'><input type="button" data-id="<?php echo $row['id']; ?>" value="Report"></td>
            </tr>
            <tr>
                <td width='100%' colspan='4'><?php echo $row['message'] ?></td>
            </tr>
        </table>
    </td>
</tr>