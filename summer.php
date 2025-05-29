<?php
include 'header.php';
?>

<div class="floaty">
    <h2 class="text-center mb-0" style="color:#7eff11">The Summer Event</h2>
    <h2 class="text-14 m-0">18th - 3rd September 2022 (Ends 23:59 30th)</h2>
    <p class="text-14">Earn <?php echo item_popup('Rayz', 159, '#7eff11') ?> based on your activity level</p>

    <div style="text-align:left;padding:10px;">
        <p class="text-14"><?php echo item_popup('Rayz', 159, '#7eff11') ?> will appear in your inventory and you can
            share with another player - you will both receive a random reward (same for both).</p>
        <p class="text-14">Possible rewards:
        <ul style="list-style:none" class="text-14">
            <li>Money (Sent to Bank)</li>
            <li>Points</li>
            <li><?php echo item_popup('Attack Protection Pill', 9, '#7eff11') ?></li>
            <li><?php echo item_popup('Mug Protection Pill', 8, '#7eff11') ?></li>
            <li><?php echo item_popup('Double EXP Pill', 10, '#7eff11') ?></li>
        </ul>
        </p>
        <p class="text-14">So the more active you are the more <?php echo item_popup('Rayz', 159, '#7eff11') ?> you will
            earn - make sure to share those Rayz!!</p>
        <!-- <h2 class="text-center mb-0">Who will you share your <?php echo item_popup('Rayz', 159, '#7eff11') ?> with?</h2> -->
    </div>

    <!-- <h2 class="text-center m-0"  style="color:#7eff11">Can't wait? Need More shamrocks?</h2>
    <div class="package" style="width:50%;margin:10px auto;border:1px white solid;padding:10px;">
        <p class="text-18 mt-0">Shamrock Double - Limited Offer #2</p>
        <p class="text-14 m-0"><?php echo item_popup('Rayz', 159, '#7eff11') ?> <strong>[x3]</strong> <br/>
            Cost: 100 Credits<br/>
            <?php echo $offer['remaining'] . ' / ' . $offer['total'] . ' Remaining'; ?>
        </p>
        <a class="cta" href="?buy=pack">[ Purchase ]</a>
    </div> -->
    <!-- <br/>
    <br/>
    <br/>
    <br/>
    <br/> -->

    <?php
    $db->query("SELECT count(*) as total, `to` FROM `eventslog` WHERE text = 'You have been awarded a Ray! Goto your Inventory to share the rewards!' GROUP BY `to` ORDER BY total DESC");
    $db->execute();
    $shamrocksEarned = $db->fetch_row();

    $results = array();

    $i = 0;
    $top = $shamrocksEarned[0]['total'];
    foreach ($shamrocksEarned as $earned) {
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

    $place = array_search($user_class->id, array_column($shamrocksEarned, 'to'));

    $places = array('1st', '2nd', '3rd');
    $colors = array('FFD700', 'C0C0C0', 'CD7F32');
    $rewards = array(6, 4, 2);
    $i = 0;

    echo '<div class="stats" style="width:90%;margin:0 auto;padding-bottom:30px">
        <h2>Statistics</h2>
        <h2 class="mb-0" style="color:#7eff11">Most Rayz Earned</h2>
        <p class="mt-0">(Awarded by the game)</p>
        <table class="table text-center">
            <th class="text-center">Rank</th>
            <th class="text-center">Amount</th>
            <th class="text-center">Player(s)</th>
            ';

    // <th class="text-center">Reward (Shared)</th>
    
    foreach ($results as $result) {
        echo '<tr><td style="color:#' . $colors[$i] . ';font-weight:700;">' . $places[$i] . '</td>';
        echo '<td>' . $result[0]['total'] . '</td><td>';
        foreach ($result as $r) {
            $processed[] = $r['id'];
            echo formatname($r['id']) . '<br>';
        }
        //echo '</td><td style="color:#' . $colors[$i] . ';font-weight:700;">' . item_popup('Rayz', 157, '#7eff11') . ' [x' . $rewards[$i] . ']</td>';
        $i++;
    }
    if (!array_search($user_class->id, $processed)) {
        echo '<tr><td>' . ordinal($place - 1) . '</td>';
        echo '<td>' . $shamrocksEarned[$place]['total'] . '</td>';
        echo '<td>' . formatname($user_class->id) . '</td>';
        //echo '<td>None</td></tr>';
    }

    echo '</table>';

    $db->query("SELECT COUNT( * ) AS total, who
        FROM  `rayz_logs`
        WHERE who !=0
        GROUP BY who
        ORDER BY `total` DESC");
    $db->execute();
    $mostReceived = $db->fetch_row();

    $results = array();

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

    $place = array_search($user_class->id, array_column($mostReceived, 'who'));

    $places = array('1st', '2nd', '3rd');
    $colors = array('FFD700', 'C0C0C0', 'CD7F32');
    $rewards = array(6, 4, 2);
    $i = 0;
    $processed = array();

    //<th class="text-center">Reward (Shared)</th>
    
    echo '<h2 class="mb-0" style="margin-top: 10px;color:#7eff11">Top Admired Players</h2>
        <p class="mt-0">(Total Received)</p>
        <table class="table text-center">
            <th class="text-center">Rank</th>
            <th class="text-center">Amount</th>
            <th class="text-center">Player(s)</th>
            ';

    foreach ($results as $result) {
        echo '<tr><td style="color:#' . $colors[$i] . ';font-weight:700;">' . $places[$i] . '</td>';
        echo '<td>' . $result[0]['total'] . '</td>';
        echo '<td>';
        foreach ($result as $r) {
            $processed[] = $r['id'];
            echo formatname($r['id']) . '<br>';
        }
        //echo '</td><td style="color:#' . $colors[$i] . ';font-weight:700;">' . item_popup('Rayz', 159, '#7eff11') . ' [x' . $rewards[$i] . ']</td>';
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

    echo '</table>';

    echo '</div></div>';

    function ordinal($number)
    {
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if ((($number % 100) >= 11) && (($number % 100) <= 13))
            return $number . 'th';
        else
            return $number . $ends[$number % 10];
    }

    include 'footer.php';
    ?>