<?php
include 'header.php';
if (isset($_POST['submit'])) {
    $cost = round($_POST['displaymins'] / 15 * 250000, 0);
    $error = ($cost > $user_class->money) ? "You don't have enough money for that!" : $error;
    // $error = ($_POST['title'] == "") ? "You need to have a title!" : $error;
    $error = ($_POST['message'] == "") ? "You need to have a message!" : $error;
    if ($error == "") {
        $newmoney = $user_class->money - $cost;
        $time = time();
        perform_query("UPDATE `grpgusers` SET `money` = ? WHERE `id`= ?", [$newmoney, $user_class->id]);
        perform_query("INSERT INTO `ads`(`poster`, `message`, `displaymins`) VALUES (?, ?, ?)", [$user_class->id, $_POST['message'], $_POST['displaymins']]);
        echo Message("You have posted a classified ad for $" . $cost);
    } else {
        echo Message($error);
    }
}
?>
<script>
    function calcCost() {
        $('#cost').html('£' + Math.round($('input[name="displaymins"]').val() / 15 * 250000));
    }
</script>
<tr>
    <td class="contenthead">Smart Ads</td>
</tr>
<tr>
    <td class="contentcontent">
        Here you can post anything your heart desires. Cost is $250,000 for a 15 minute message, $1M for 60 minutes, and
        so on..
    </td>
</tr>
<tr>
    <td class="contentcontent">
        <form method='post'>
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
                        <textarea name='message' cols='60' rows='4'></textarea>
                    </td>
                </tr>
                <tr>
                    <td width='25%'>Minutes:</td>
                    <td width='25%'>
                        <input type='number' name='displaymins' min='3' value='15' onchange="calcCost();"> <span>Cost:
                            <span class="text-yellow" id="cost">£250000</span></span>
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
    </td>
</tr>
<?php
$result = mysql_query("SELECT * from `ads` ORDER BY `when` DESC LIMIT 10");
while ($row = mysql_fetch_array($result, mysql_ASSOC)) {
    $user_ads = new User($row['poster']);
    ?>
    <tr>
        <td class="contentcontent">
            <table width='100%'>
                <tr>
                    <td width='15%'><b>Title</b>:</td>
                    <td width='45%'><?php echo $row['title']; ?></td>
                    <td width='15%'><b>Poster</b>:</td>
                    <td width='45%'><?php echo $user_ads->formattedname ?></td>
                </tr>
                <tr>
                    <td width='100%' colspan='4'><?php echo $row['message'] ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <?php
}
?>
<?php
include 'footer.php';
?>