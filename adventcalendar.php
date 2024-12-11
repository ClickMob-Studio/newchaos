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
        '2024-12-01' => 277, // Mission Pass
        '2024-12-02' => 290, // Toffee Apple
        '2024-12-03' => 283, // Gold Rush Token Chest
        '2024-12-04' => 277, // Mission Pass
        '2024-12-05' => 277, // Mission Pass
        '2024-12-06' => 271, // Sofa
        '2024-12-07' => 276, // Research Token
        '2024-12-08' => 277, // Mission Pass
        '2024-12-09' => 277, // Mission Pass
        '2024-12-10' => 277, // Mission Pass
        '2024-12-11' => 283, // Gold Rush Token Chest
        '2024-12-12' => 290, // Toffee Apple
        '2024-12-13' => 276, // Research Token
        '2024-12-14' => 283, // Gold Rush Token Chest
        '2024-12-15' => 277, // Mission Pass
        '2024-12-16' => 277, // Mission Pass
        '2024-12-17' => 277, // Mission Pass
        '2024-12-18' => 277, // Mission Pass
        '2024-12-19' => 277, // Mission Pass
        '2024-12-20' => 277, // Mission Pass
        '2024-12-21' => 277, // Mission Pass
        '2024-12-22' => 277, // Mission Pass
        '2024-12-23' => 277, // Mission Pass
        '2024-12-24' => 277, // Mission Pass
        '2024-12-25' => 277, // Mission Pass

    );

    $today = date('Y-m-d');

    $itemId = $prizesIndexedOnDate[$today];

    $db->query("INSERT INTO `advent_calendar` (`user_id`, `date_opened`, `item_awarded`) VALUES (?, ?, ?)");
    $db->execute([$userId, $today, $itemId]);

    // Logic to actually give the item to the user
    Give_Item($itemId, $userId, 1);

    return $itemId;
}

function displayCalendar($userId) {
    $today = date('j');
    echo '<div class="row">';
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
        echo "
            <div class='alert alert-success'>
                You have been awarded: 1 x " . Item_Name($item) . "
            </div> 
        ";
    } else {
        echo "<div class='alert alert-danger'>You have already opened today's calendar.</div>";
    }
}

$today = date('j');
?>


<div class='box_top'><h1>Advent Calendar</h1></div>
<div class='box_middle'>
    <div class='pad'>
        <div class="row">
            <?php for ($day = 1; $day <= 25; $day++): ?>
                <?php
                $divClass = 'bg-info';
                if ($day == $today) {
                    if (hasOpenedToday($user_class->id)) {
                        $divClass = 'bg-success';
                    } else {
                        $divClass = 'bg-danger';
                    }
                }
                ?>
                <div class="col-md-4">
                    <div class="card text-white <?php echo $divClass ?> mb-3">
                        <div class="card-body">
                            <h2 class="card-text">
                                <center>
                                    <strong>Day <?php echo $day ?></strong><br />

                                    <?php
                                    if ($day == $today) {
                                        if (hasOpenedToday($userId)) {
                                        } else {
                                            echo '<a href="?open=' . $day . '" class="btn btn-primary">Claim</a>';
                                        }
                                    }
                                    ?>
                                </center>
                            </p>
                    </div>
                    </div>
                </div>

                <?php if ($day % 3 == 1): ?>
                    </div><div class="row">
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>
