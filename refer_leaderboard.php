<?php
include 'header.php';
$timestamp = 1712401200;
$query = "SELECT COUNT(r.id) AS ref_count, u.id AS id FROM referrals AS r LEFT JOIN grpgusers AS u ON r.referrer = u.id WHERE r.id > 56 AND r.credited = 1 AND u.admin < 1 GROUP BY u.id ORDER BY ref_count DESC";
$result = mysql_query($query);
?>
<div class='box_top'>Referral Competition</div>
<div class='box_middle'>
    <div class='pad'>
        <br />
        <center>
            <p>Mobsters, it's time to run a little competition to help us grow Chaos City, so welcome to our Referral Competition.</p>

            <p><strong>Rules:</strong></p>
            <ul>
                <li>The competition will run until 15th April.</li>
                <li>Referrals must be validated by an Admin before they are counted.</li>
                <li>Only referrals after 06/04/2024 12:00 will count.</li>
            </ul>

            <p><strong>Prizes:</strong></p>
            <ul>
                <li><strong>1st: 1,000 gold</strong></li>
                <li><strong>2nd: 400 gold</strong></li>
                <li><strong>3rd: 100 gold</strong></li>
            </ul>

        </center>

        <table id="newtables" style="width:100%; text-align: left;">
            <tr>
                <th><b>Position</b></th>
                <th><b>Mobster</b></th>
                <th><b>Count</b></th>
            </tr>
            <?php if (mysql_num_rows($result) > 0): ?>
                <?php $i = 1; ?>
                <?php while ($row = mysql_fetch_assoc($result)): ?>
                    <?php $rfuser = new User($row['id']); ?>

                    <tr>
                        <td><?php echo $i; ?></td>
                        <td><?php echo $rfuser->formattedname; ?></td>
                        <td><?php echo $row['ref_count']; ?></td>
                    </tr>
                <?php endwhile;?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No Referrals Yet!</td>
                </tr>
            <?php endif; ?>
        </table>

    </div>
</div>

<?php
include 'footer.php';
?>