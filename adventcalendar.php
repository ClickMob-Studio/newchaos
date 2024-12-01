<?php
include 'header.php';

function hasOpenedToday($userId) {
    global $db;
    $today = date('Y-m-d');
    $db->query("SELECT COUNT(id) as account FROM `advent_calendar` WHERE `user_id` = ? AND `date_opened` = ?");
    $db->execute([$userId, $today]);

    return (int)$db->fetch_row()[0]['account'] > 0;
}

function awardItem($userId) {
    global $db;

    $prizesIndexedOnDate = array(
        '2021-12-01' => 277, // Mission Pass
        '2021-12-02' => 290, // Toffee Apple
        '2021-12-03' => 283, // Gold Rush Token Chest
        '2021-12-04' => 277, // Mission Pass
        '2021-12-05' => 277, // Mission Pass
        '2021-12-06' => 277, // Mission Pass
        '2021-12-07' => 277, // Mission Pass
        '2021-12-08' => 277, // Mission Pass
        '2021-12-09' => 277, // Mission Pass
        '2021-12-10' => 277, // Mission Pass
        '2021-12-11' => 277, // Mission Pass
        '2021-12-12' => 277, // Mission Pass
        '2021-12-13' => 277, // Mission Pass
        '2021-12-14' => 277, // Mission Pass
        '2021-12-15' => 277, // Mission Pass
        '2021-12-16' => 277, // Mission Pass
        '2021-12-17' => 277, // Mission Pass
        '2021-12-18' => 277, // Mission Pass
        '2021-12-19' => 277, // Mission Pass
        '2021-12-20' => 277, // Mission Pass
        '2021-12-21' => 277, // Mission Pass
        '2021-12-22' => 277, // Mission Pass
        '2021-12-23' => 277, // Mission Pass
        '2021-12-24' => 277, // Mission Pass
        '2021-12-25' => 277, // Mission Pass

    );

    $today = date('Y-m-d');

    $itemId = $prizesIndexedOnDate[$today];
    echo $itemId; exit;

    $db->query("INSERT INTO `advent_calendar` (`user_id`, `date_opened`, `item_awarded`) VALUES (?, ?, ?)");
    $db->execute([$userId, $today, $itemId]);

    // Logic to actually give the item to the user
    Give_Item($itemId, $userId, 1);

    return $itemId;
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
        echo "You have been awarded: 1 x " . Item_Name($item);
    } else {
        echo "You have already opened today's calendar.";
    }
}

displayCalendar($user_class->id);

include 'footer.php';
?>
