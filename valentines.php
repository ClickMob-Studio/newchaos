<?php
include 'header.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// $db->query("SELECT id, remaining, total FROM offers WHERE id = 1");
// $db->execute();
// $offer = $db->fetch_row()[0];

// if (isset($_GET['buy'])) {
//     if ($offer['remaining'] == 0) {
//         echo Message("Sorry there are no more remaining.");
//     } else if ($user_class->credits >= 100) {
//         $newcredit = $user_class->credits -= 100;
//         perform_query("UPDATE `offers` SET `remaining` = `remaining` - 1 WHERE `id` != ?", [$row['id']]);
//         $db->query("UPDATE grpgusers SET credits = credits - 100 WHERE id = ?");
//         $db->execute(
//             array(
//                 $user_class->id
//             )
//         );

//         Give_Item(155, $user_class->id, 3);

//         Send_Event($user_class->id, "You have been credited your Limited Valentines Double!.", $user_class->id);
//         $db->execute(array());
//         echo Message("You spent 100 credits on your Limited Valentines Double.");
//     } else {
//         echo Message("You don't have enough credits. You can buy some at the upgrade store.");
//     }
// }

?>

<div class="floaty">
    <h2 class="text-center mb-0" style="color:#7eff11">Valentines Event</h2>
    <h2 class="text-14 m-0">14th - 17th Feb 2023 (Ends 23:59 17th)</h2>
    <p class="text-14">Earn <?php echo item_popup('Heart\'s', 155, '#7eff11') ?> based on your activity level</p>
    <div style="text-align:left;padding:10px;">
        <p class="text-14"><?php echo item_popup('Heart\'s', 155, '#7eff11') ?> will appear in your inventory and you
            can share with another player - you will both receive a random reward (same for both).</p>
        <p class="text-14">Possible rewards:
        <ul style="list-style:none" class="text-14">
            <li>Money (Sent to Bank)</li>
            <li>Points</li>
            <li><?php echo item_popup('Attack Protection Pill', 9, '#7eff11') ?></li>
            <li><?php echo item_popup('Mug Protection Pill', 8, '#7eff11') ?></li>
            <li><?php echo item_popup('Double EXP Pill', 10, '#7eff11') ?></li>
        </ul>
        </p>
        <p class="text-14">So the more active you are the more <?php echo item_popup('Heart\'s', 155, '#7eff11') ?> you
            will earn - make sure to share the love!</p>
        <!-- <h2 class="text-center mb-0">Who will you share your <?php echo item_popup('Heart\'s', 155, '#7eff11') ?> with?</h2> -->
    </div>

    <!-- <h2 class="text-center m-0"  style="color:#7eff11">Can't wait? Need More Hearts?</h2>
    <div class="package" style="width:50%;margin:10px auto;border:1px white solid;padding:10px;">
        <p class="text-18 mt-0">Valentines Double - Limited Offer #2</p>
        <p class="text-14 m-0"><?php //echo item_popup('Shamrock', 155, '#7eff11') ?> <strong>[x3]</strong> <br/>
            Cost: 100 Credits<br/>
            <?php //echo $offer['remaining'] . ' / ' . $offer['total'] . ' Remaining'; ?>
        </p>
        <a class="cta" href="?buy=pack">[ Purchase ]</a>
    </div> -->
    <!-- <br/>
    <br/>
    <br/>
    <br/>
    <br/> -->

    <?php
    $db->query("SELECT count(*) as total, `to` FROM `eventslog` WHERE text = 'You have been awarded a Heart! Goto your Inventory to share the love!' AND timesent > 1676268000 AND timesent <= 1676678399 GROUP BY `to` ORDER BY total DESC");
    $db->execute();
    $heartsEarned = $db->fetch_row();

    $results = array();

    if (!empty($heartsEarned)) {
        $i = 0;
        $top = $heartsEarned[0]['total'];
        foreach ($heartsEarned as $earned) {
            if ($earned['total'] == $top) {
                $r = array(
                    'id' => $earned['to'],
                    'total' => $earned['total']
                );
                $results[$i][] = $r;
            } else {
                $i++;
                $r = array(
                    'id' => $earned['to'],
                    'total' => $earned['total']
                );
                $results[$i][] = $r;
                $top = $earned['total'];
                if ($i >= 2)
                    break;
            }
        }
    }

    $place = array_search($user_class->id, array_column($heartsEarned, 'to'));

    $places = array('1st', '2nd', '3rd');
    $colors = array('FFD700', 'C0C0C0', 'CD7F32');
    $rewards = array(6, 4, 2);
    $i = 0;

    echo '<div class="stats" style="width:90%;margin:0 auto;padding-bottom:30px">
        <h2>Statistics</h2>
        <h2 class="mb-0" style="color:#7eff11">Most Hearts Earned</h2>
        <p class="mt-0">(Awarded by the game)</p>
        <table class="table text-center">
            <th class="text-center">Rank</th>
            <th class="text-center">Amount</th>
            <th class="text-center">Player(s)</th>
            <th class="text-center">Reward</th>
            ';

    // <th class="text-center">Reward</th>
    
    $processed = [];

    foreach ($results as $result) {
        echo '<tr><td style="color:#' . $colors[$i] . ';font-weight:700;">' . $places[$i] . '</td>';
        echo '<td>' . $result[0]['total'] . '</td><td>';
        foreach ($result as $r) {
            $processed[] = $r['id'];
            echo formatname($r['id']) . '<br>';
        }
        echo '</td><td style="color:#' . $colors[$i] . ';font-weight:700;">' . item_popup('Heart', 155, '#7eff11') . ' [x' . $rewards[$i] . ']</td>';
        $i++;
    }
    if (!array_search($user_class->id, $processed) && !empty($results)) {
        echo '<tr><td>' . ordinal($place - 1) . '</td>';
        echo '<td>' . $heartsEarned[$place]['total'] . '</td>';
        echo '<td>' . formatname($user_class->id) . '</td>';
        //echo '<td>None</td></tr>';
    }

    echo '</table>';

    $db->query("SELECT COUNT( * ) AS total, who
        FROM  `hearts_log`
        WHERE who !=0
        GROUP BY who
        ORDER BY `total` DESC");
    $db->execute();
    $mostReceived = $db->fetch_row();

    $results = array();

    if (!empty($mostReceived)) {
        $i = 0;
        $top = $mostReceived[0]['total'];
        foreach ($mostReceived as $received) {
            if ($received['total'] == $top) {
                $r = array(
                    'id' => $received['who'],
                    'total' => $received['total']
                );
                $results[$i][] = $r;
            } else {
                $i++;
                $r = array(
                    'id' => $received['who'],
                    'total' => $received['total']
                );
                $results[$i][] = $r;
                $top = $received['total'];
                if ($i >= 2)
                    break;
            }
        }
    }

    $place = array_search($user_class->id, array_column($mostReceived, 'who'));

    $places = array('1st', '2nd', '3rd');
    $colors = array('FFD700', 'C0C0C0', 'CD7F32');
    $rewards = array(6, 4, 2);
    $i = 0;
    $processed = array();

    //<th class="text-center">Reward</th>
    
    echo '<h2 class="mb-0" style="margin-top: 10px;color:#7eff11">Top Admired Players</h2>
        <p class="mt-0">(Total Received)</p>
        <table class="table text-center">
            <th class="text-center">Rank</th>
            <th class="text-center">Amount</th>
            <th class="text-center">Player(s)</th>
            <th class="text-center">Reward</th>
            ';

    if (!empty($results)) {
        foreach ($results as $result) {
            echo '<tr><td style="color:#' . $colors[$i] . ';font-weight:700;">' . $places[$i] . '</td>';
            echo '<td>' . $result[0]['total'] . '</td>';
            echo '<td>';
            foreach ($result as $r) {
                $processed[] = $r['id'];
                echo formatname($r['id']) . '<br>';
            }
            echo '</td><td style="color:#' . $colors[$i] . ';font-weight:700;">' . item_popup('Heart', 155, '#7eff11') . ' [x' . $rewards[$i] . ']</td>';
            $i++;
        }
        if (!array_search($user_class->id, $processed)) {

            if ($place) {
                echo '<tr><td>' . ordinal($place + 1) . '</td>';
                echo '<td>' . $mostReceived[$place]['total'] . '</td>';
            } else {
                echo '<tr><td>-</td>';
                echo '<td>0</td>';
            }
            echo '<td>' . formatname($user_class->id) . '</td>';
            //echo '<td>None</td></tr>';
        }
    }


    echo '</table>';

    echo '</div></div>';

    include 'footer.php';
    ?>