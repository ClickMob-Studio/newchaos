<?php
include 'header.php';

$db->query("SELECT * FROM `user_comp_leaderboard` ORDER BY `overall_vampire_teeth` DESC LIMIT 20");
$db->execute();
$rows = $db->fetch_row();

$userCompLeaderboard = getUserCompLeaderboard($user_class->id);

$milestones = array(
    100 => 1000,
    1000 => 5000,
    10000 => 15000,
    25000 => 300000,
    50000 => 750000,
    75000 => 1250000
);

// MILESTONE
if (isset($_GET['action']) && $_GET['action'] === 'milestone') {

    $mileCollected = 0;
    foreach ($milestones as $mile => $prize) {
        if ($userCompLeaderboard['vampire_teeth_milestone_collected'] < $mile && $userCompLeaderboard['overall_vampire_teeth'] >= $mile) {
            $mileCollected = $mile;

            Send_Event($user_class->id, 'You collected your ' . number_format($mile, 0) . ' vampire teeth milestone and claimed ' . number_format($prize, 0) . ' points.');

            $db->query("UPDATE grpgusers SET points = points + " . $prize . " WHERE id = " . $user_class->id);
            $db->execute();

            $db->query("UPDATE user_comp_leaderboard SET vampire_teeth_milestone_collected = " . $mile . " WHERE user_id = " . $user_class->id);
            $db->execute();
        }
    }

    if ($mileCollected > 0) {
        echo Message('You have successfully collected your milestone prizes, check your events to see which ones you claimed.');
    } else {
        echo Message('You had no milestone prizes to collect.');
    }
}
?>

<div class='box_top'><h1>Halloween Contest Leaderboard</h1></div>
<div class='box_middle'>
    <div class='pad'>
        <br />
        <center>
            <p>
                This Halloween, the underworld gets even darker! In this contest, you are tasked with trick-or-treating on rival profiles to collect rare vampire teeth.
            </p>

            <p>
                The contest will run until 01/11.
            </p>

            <p>Enjoy!</p>
            <br /><br /><hr />

        </center>

        <center>
            <h2>Daily Leaderboard</h2>
            <p><strong>Prizes:</strong></p>
            <ul>
                <li><strong>1st: 1,000,000 points, 10 x Dracula Blood Bags & 10 x Ghost Vacuums</strong></li>
                <li><strong>2nd: 500,000 points, 5 x Dracula Blood Bags & 5 x Ghost Vacuums</strong></li>
                <li><strong>3rd: 100,000 points</strong></li>
            </ul>
        </center>



        <table id="newtables" style="width:100%; text-align: left;">
            <tr>
                <th><b>Position</b></th>
                <th><b>Player</b></th>
                <th><b>Count</b></th>
            </tr>
            <?php if (count($rows) > 0): ?>
                <?php $i = 1; ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo formatName($row['user_id']); ?></td>
                        <td><?php echo number_format($row['overall_vampire_teeth']); ?></td>
                    </tr>
                    <?php
                    $i++;
                endforeach;
                ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No Data Yet!</td>
                </tr>
            <?php endif; ?>
        </table>
        <!--


        <hr />
        <h2>Milestone Payments</h2>
        <p>You currently have <?php echo number_format($userCompLeaderboard['overall_vampire_teeth']) ?> Vampire Teeth.</p>
        <div class="row">
            <div class="col-md-12">
                <ul>
                    <?php foreach ($milestones as $teeth => $points): ?>
                        <li><?php echo number_format($teeth, 0) ?> Vampire Teeth: <?php echo number_format($points, 0) ?> points</li>
                    <?php endforeach; ?>
                </ul>

                <a href="user_comp_leaderboard.php?action=milestone"><button>Collect Milestones</button></a>
            </div>
        </div>
        -->

        <?php if ($user_class->admin > 0): ?>
            <?php
            $tradeIns = array();
            $tradeIns[50] = 288;
            $tradeIns[100] = 255;
            $tradeIns[250] = 257;
            $tradeIns[500] = 253;
            $tradeIns[1000] = 285;
            $tradeIns[5000] = 271;
            $tradeIns[10000] = 278;
            $tradeIns[20000] = 203;

            ?>
            <hr />
            <center>
                <h2>Trade In</h2>

                <table id="newtables" style="width:100%; text-align: left;">
                    <thead>
                        <tr>
                            <th>Vampire Teeth</th>
                            <th>Reward</th>
                            <th>Trade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tradeIns as $cost => $itemId): ?>
                            <tr>
                                <td><?php echo number_format($cost); ?></td>
                                <td><?php echo Item_Name($itemId) ?></td>
                                <td><a href="#">Buy</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </center>

        <?php endif; ?>
    </div>
</div>

<?php
include 'footer.php';
?>
