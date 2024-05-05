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
    } elseif (strlen($_POST['message']) > 90) {
        $error = "Your message can't be longer than 90 characters long";
    } else {
        $db->query("SELECT COUNT(*) FROM `ads` WHERE `poster` = :userid AND `timestamp` + (`displaymins` * 60) > :current_time");
        $db->bind(':userid', $user_class->id);
        $db->bind(':current_time', time());
        $activeAdsCount = $db->fetch_single();

        if ($activeAdsCount >= 3) {
            $error = "You can only have three active ads at a time.";
        } else {
            $error = "";
        }
    }

    if ($error == "") {
        $newmoney = $user_class->bank - $cost;
        $time = time();
    
        $db->query("UPDATE `grpgusers` SET `bank` = :newmoney WHERE `id`= :userid");
        $db->bind(':newmoney', $newmoney);
        $db->bind(':userid', $user_class->id);
        $db->execute();

        $db->query("INSERT INTO `ads`(`timestamp`, `poster`, `message`, `displaymins`, `glow`) VALUES (:time, :userid, :message, :displaymins, :glow)");
        $db->bind(':time', $time);
        $db->bind(':userid', $user_class->id);
        $db->bind(':message', $_POST['message']);
        $db->bind(':displaymins', $_POST['displaymins']);
        $db->bind(':glow', isset($_POST['glowText']) && $_POST['glowText'] == 'true' ? 1 : 0);
        $db->execute();

        echo Message("You have posted a classified ad for $" . number_format($cost));
    } else {
        echo Message($error);
    }
}
?>

<script>
function calcCost() {
    var cost = Math.round($('input[name="displaymins"]').val() / 60 * 250000);
    $('#cost').html('$' + cost.toLocaleString('en-US'));
}
</script>
<style>.img-thumbnail {
    background-color: transparent !important;
}
</style>
<h1>Shoutbox</h1>
<p>Here you can post anything your heart desires. Cost is $250,000 for a 60 minute message, $1M for 4 hours, and so on..</p>

<form method='post' style='margin: 15px 0;' class="row" accept-charset="UTF-8">
    <div class="mb-3 col-md-6">
        <label for="message" class="form-label">Message:</label>
        <textarea class="form-control" name="message" id="message" rows="4" maxlength="90"></textarea>
        <div id="characterCount">0/90</div>

    </div>
    <div class="mb-3 col-md-6">
        <label for="displaymins" class="form-label">Minutes:</label>
        <input type="number" class="form-control" name="displaymins" id="displaymins" min="3" value="60" oninput="calcCost();"> 
        <div>Cost: <span class="text-warning" id="cost">$250,000</span></div>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary" name="submit">Post</button>
    </div>
</form>
<script>
function countCharacters() {
  var message = document.getElementById('message');
  var counter = document.getElementById('characterCount');
  var count = message.value.length;
  
  counter.textContent = count + '/90';
}
</script>
<h1>Current Ads</h1>

<?php
$db->query("SELECT * FROM `ads` WHERE `timestamp` + `displaymins` * 60 > :current_time ORDER BY `timestamp` DESC");
$db->bind(':current_time', time());
$result = $db->fetch_row();
if (!$result) {
    echo '<div class="alert alert-info">No messages at the moment! Use the form above to add one!</div>';
} else {
    foreach ($result as $row) {
        $user_ads = new User($row['poster']);
        $user_ads->avatar = $user_ads->avatar ?: "/images/no-avatar.png";
        ?>
        <div class="d-flex align-items-center my-2">
            <img src="<?php echo $user_ads->avatar ?>" class="img-thumbnail me-3" alt="User Avatar" style="width: 50px; height: 50px; object-fit: cover;">
            <div>
                <p class="mb-0"><?php echo $user_ads->formattedname; ?>: <?php echo howlongago($row['timestamp']) ?> ago - <?php echo $row['message'] ?></p>
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
