<?php
include 'header.php';

?>

<div class="floaty">
    <h2 class="text-center mb-0" style="color:#7eff11">Christmas Event</h2>
    <h2 class="text-14 m-0">10 - 25th Dec 2023 (Ends 23:59 25th)</h2>
    <p class="text-14">Earn <?php echo item_popup('Snowball\'s', 198, '#7eff11') ?> based on your activity level</p>

    <div style="text-align:left;padding:10px;">
        <p class="text-14"><?php echo item_popup('Snowball\'s', 198, '#7eff11') ?> will appear in your inventory and you
            can share with another player - you will both receive a random reward (same for both).</p>
        <p class="text-14">Possible rewards:
        <ul style="list-style:none" class="text-14">
            <li>Money (Sent to Bank)</li>
            <li><b><i><u>
                            <font color=red>Points</font>
                        </u></i></b></li>
            <li><?php echo item_popup('Attack Protection Pill', 9, '#7eff11') ?></li>
            <li><?php echo item_popup('Mug Protection Pill', 8, '#7eff11') ?></li>
            <li><?php echo item_popup('Double EXP Pill', 10, '#7eff11') ?></li>
        </ul>
        </p>
        <p class="text-14">So the more active you are the more <?php echo item_popup('snowball\'s', 155, '#7eff11') ?>
            you will earn - make sure to share the love!</p>
        <!-- <h2 class="text-center mb-0">Who will you share your <?php echo item_popup('snowball\'s', 155, '#7eff11') ?> with?</h2> -->
    </div>

    <?php
    $db->query("SELECT count(*) as total, `to` FROM `eventslog` WHERE text = 'You have been awarded a snowball! Goto your Inventory to share the love!' AND timesent > 1676268000 AND timesent <= 1676678399 GROUP BY `to` ORDER BY total DESC");
    $db->execute();
    $snowballsEarned = $db->fetch_row();

    $results = array();

    if (!empty($snowballsEarned)) {
        $i = 0;
        $top = $snowballsEarned[0]['total'];
        foreach ($snowballsEarned as $earned) {
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

    $place = array_search($user_class->id, array_column($snowballsEarned, 'to'));

    $places = array('1st', '2nd', '3rd');
    $colors = array('FFD700', 'C0C0C0', 'CD7F32');
    $rewards = array(6, 4, 2);
    $i = 0;

    echo '<div class="stats" style="width:90%;margin:0 auto;padding-bottom:30px">
        <h2>Statistics</h2>
        <h2 class="mb-0" style="color:#7eff11">Most snowballs Earned</h2>
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
        echo '</td><td style="color:#' . $colors[$i] . ';font-weight:700;">' . item_popup('snowball', 155, '#7eff11') . ' [x' . $rewards[$i] . ']</td>';
        $i++;
    }
    if (!array_search($user_class->id, $processed) && !empty($results)) {
        echo '<tr><td>' . ordinal($place - 1) . '</td>';
        echo '<td>' . $snowballsEarned[$place]['total'] . '</td>';
        echo '<td>' . formatname($user_class->id) . '</td>';
        //echo '<td>None</td></tr>';
    }

    echo '</table>';

    $db->query("SELECT COUNT( * ) AS total, who
        FROM  `snowballs_log`
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
            echo '</td><td style="color:#' . $colors[$i] . ';font-weight:700;">' . item_popup('snowball', 155, '#7eff11') . ' [x' . $rewards[$i] . ']</td>';
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