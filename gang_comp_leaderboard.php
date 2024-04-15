<?php
include 'header.php';
$db->query("SELECT * FROM `gang_comp_leaderboard` ORDER BY `daily_missions_complete` DESC");
$db->execute();
$dailyRows = $db->fetch_row();

$db->query("SELECT * FROM `gang_comp_leaderboard` ORDER BY `weekly_missions_complete` DESC");
$db->execute();
$weeklyRows = $db->fetch_row();
?>

<div class='box_top'><h1>Competition Leaderboard</h1></div>
<div class='box_middle'>
    <div class='pad'>
        <br />
        <center>
            <p>
                Mobsters, it's time to gang up and show what you & the homies are made of! This weeks competition
                will involve working with your gang to complete more missions than any other gang, with daily and
                overall prizes!
            </p>

            <p>
                The competition will run until 19/04. Daily prizes are paid automatically at rollover.
            </p>

            <p>Enjoy!</p>

        </center>

        <center>
            <h2>Daily Leaderboard</h2>
        </center>

        <p><strong>Prizes (paid to each gang member):</strong></p>
        <ul>
            <li><strong>1st: 20,000 points, 1 x Police Badge & 1 x Mystery Box</strong></li>
            <li><strong>2nd: 1 x Mystery Box</strong></li>
        </ul>

        <table id="newtables" style="width:100%; text-align: left;">
            <tr>
                <th><b>Position</b></th>
                <th><b>Gang</b></th>
                <th><b>Count</b></th>
            </tr>
            <?php if (count($dailyRows) > 0): ?>
                <?php $i = 1; ?>
                <?php foreach ($dailyRows as $row): ?>
                    <?php $gang_class = new Gang($row['gang_id']); ?>

                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $gang_class->name; ?></td>
                        <td><?php echo $row['daily_missions_complete']; ?></td>
                    </tr>
                    <?php
                    $i++;
                endforeach;
                ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No Missions Complete Yet!</td>
                </tr>
            <?php endif; ?>
        </table>

        <br /><br /><hr />
        <center>
            <h2>Overall Leaderboard</h2>
        </center>


        <p><strong>Prizes (paid to each gang member):</strong></p>
        <ul>
            <li><strong>1st: 50,000 points, 5 x Police Badges & 5 x Mystery Boxes & 1 x Advanced Booster</strong></li>
            <li><strong>2nd: 25,000 points, 1 x Police Badge, 1 x Mystery Box & 1 x Heroic Booster</strong></li>
            <li><strong>3rd: 10,000 points, 1 x Mystery Box & 1 x Exotic Booster</strong></li>
        </ul>

        <table id="newtables" style="width:100%; text-align: left;">
            <tr>
                <th><b>Position</b></th>
                <th><b>Gang</b></th>
                <th><b>Count</b></th>
            </tr>
            <?php if (count($weeklyRows) > 0): ?>
                <?php $i = 1; ?>
                <?php foreach ($weeklyRows as $row): ?>
                    <?php $gang_class = new Gang($row['gang_id']); ?>

                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $gang_class->name; ?></td>
                        <td><?php echo $row['daily_missions_complete']; ?></td>
                    </tr>
                    <?php
                    $i++;
                endforeach;
                ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No Missions Complete Yet!</td>
                </tr>
            <?php endif; ?>
        </table>

    </div>
</div>

<?php
include 'footer.php';
?>
