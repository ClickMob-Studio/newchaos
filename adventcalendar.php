<?php
include 'header.php';

function hasOpenedToday($userId) {
    global $db;
    $today = date('Y-m-d');
    $db->query("SELECT COUNT(*) FROM `advent_calendar` WHERE `user_id` = ? AND `date_opened` = ?");
    $db->execute([$userId, $today]);
    return $db->fetch_row()[0] > 0;
}

function awardItem($userId) {
    global $db;
    $today = date('Y-m-d');
    $item = "Christmas Gift"; // Replace with logic to select an item
    $db->query("INSERT INTO `advent_calendar` (`user_id`, `date_opened`, `item_awarded`) VALUES (?, ?, ?)");
    $db->execute([$userId, $today, $item]);
    // Logic to actually give the item to the user
    Give_Item($item, $userId, 1);
    return $item;
}

function displayCalendar($userId) {
    $today = date('j');
    echo '<div class="calendar">';
    for ($day = 1; $day <= 25; $day++) {
        if ($day == $today) {
            if (hasOpenedToday($userId)) {
                echo "<div class='day opened'>Day $day: Already opened</div>";
            } else {
                echo "<div class='day today'><a href='?open=$day'>Open Day $day</a></div>";
            }
        } else {
            echo "<div class='day'>Day $day</div>";
        }
    }
    echo '</div>';
}

if (isset($_GET['open']) && $_GET['open'] == date('j')) {
    if (!hasOpenedToday($user_class->id)) {
        $item = awardItem($user_class->id);
        echo "You have been awarded: $item";
    } else {
        echo "You have already opened today's calendar.";
    }
}

displayCalendar($user_class->id);

include 'footer.php';
?>
