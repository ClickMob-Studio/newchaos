<?php
include 'header.php';

if (isset($_POST['submit'])) {
    $cost = round($_POST['displaymins'] / 60 * 250000, 0);
    if (isset($_POST['glowText']) && $_POST['glowText'] == 'true') {
        $glow_cost = 1000000;
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
    $('#cost').html('�' + Math.round($('input[name="displaymins"]').val() / 60 * 250000));
}
</script>

<h2>Shoutbox</h2>
<p>Here you can post anything your heart desires. Cost is $250,000 for a 60 minute message, $1M for 4 hours, and so on..</p>

<form method='post' style='margin: 15px 0;' class="row">
    <div class="mb-3 col-md-6">
        <label for="message" class="form-label">Message:</label>
        <textarea class="form-control" name="message" id="message" rows="4" maxlength="115"></textarea>
    </div>
    <div class="mb-3 col-md-6">
        <label for="displaymins" class="form-label">Minutes:</label>
        <input type="number" class="form-control" name="displaymins" id="displaymins" min="3" value="60" oninput="calcCost();"> 
        <div>Cost: <span class="text-warning" id="cost">�250000</span></div>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary" name="submit">Post</button>
    </div>
</form>

<h2>Current Ads</h2>

<?php
$result = mysql_query("SELECT * FROM `ads` WHERE `timestamp` + `displaymins` * 60 > ".time()." ORDER BY `timestamp` DESC");
if (!mysql_num_rows($result)) {
    echo '<div class="alert alert-info">No messages at the moment! Use the form above to add one!</div>';
} else {   
    while ($row = mysql_fetch_array($result)) {
        $user_ads = New User($row['poster']);
        $user_ads->avatar = $user_ads->avatar ?: "/images/no-avatar.png";
        ?>
        <div class="d-flex align-items-center my-2">
            <img src="<?php echo $user_ads->avatar ?>" class="img-thumbnail me-3" alt="User Avatar" style="width: 50px; height: 50px; object-fit: cover;">
            <div>
                <p class="mb-0"><?php echo howlongago($row['timestamp']) ?> ago - <?php echo $row['message'] ?></p>
            </div>
            <div class="ms-auto">
                <a href="#" class="btn btn-danger btn-sm" onclick="reportAd(<?php echo $row['id'] ?>); return false;">Report</a>
            </div>
        </div>
        <?php
    }
}
include 'footer.php';
?>

