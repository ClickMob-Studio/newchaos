<?php
include 'header.php';

$db->query("SELECT * FROM `user_comp_leaderboard` ORDER BY `overall_vampire_teeth` DESC");
$db->execute();
$rows = $db->fetch_row();
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
            <p><strong>Prizes (paid to each gang member):</strong></p>
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
    </div>
</div>

<?php
include 'footer.php';
?>
