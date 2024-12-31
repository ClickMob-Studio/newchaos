<?php
include 'header.php';

$attackRows = array();
$trainingRows = array();
$expRows = array();

?>

<h1>Pet Ladder</h1>
<p>Welcome to the Pet Ladder, here you can earn points by trying to be the best pet owner in CC! Prizes are paid and the ladder resets daily.</p>

<p><strong>Prizes:</strong></p>
<ul>
    <lil>1st Place: 50,000 points</lil>
    <lil>2nd Place: 25,000 points</lil>
    <lil>3rd Place: 10,000 points</lil>
</ul>

<div class="row">
    <div class="col-md-4">
        <div class="table-container">
            <h2>Attacks</h2>
            <table class="new_table" id="newtables" style="width:100%;">
                <tr>
                    <th>&nbsp;</th>
                    <th>User</th>
                    <th>Points</th>
                </tr>
                <?php if (count($attackRows) > 0): ?>
                    <?php $i = 1; ?>
                    <?php foreach ($overallRows as $overallRow): ?>
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo formatName($overallRow['user_id']) ?></td>
                            <td><?php echo number_format($overallRow['overall_raids_complete'], 0) ?></td>
                        </tr>

                        <?php $i++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">
                            No Results
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div class="col-md-4">
        <div class="table-container">
            <h2>Gym</h2>
            <table class="new_table" id="newtables" style="width:100%;">
                <tr>
                    <th>&nbsp;</th>
                    <th>User</th>
                    <th>Points</th>
                </tr>
                <?php if (count($attackRows) > 0): ?>
                    <?php $i = 1; ?>
                    <?php foreach ($overallRows as $overallRow): ?>
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo formatName($overallRow['user_id']) ?></td>
                            <td><?php echo number_format($overallRow['overall_raids_complete'], 0) ?></td>
                        </tr>

                        <?php $i++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">
                            No Results
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div class="col-md-4">
        <div class="table-container">
            <h2>EXP</h2>
            <table class="new_table" id="newtables" style="width:100%;">
                <tr>
                    <th>&nbsp;</th>
                    <th>User</th>
                    <th>Points</th>
                </tr>
                <?php if (count($attackRows) > 0): ?>
                    <?php $i = 1; ?>
                    <?php foreach ($overallRows as $overallRow): ?>
                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo formatName($overallRow['user_id']) ?></td>
                            <td><?php echo number_format($overallRow['overall_raids_complete'], 0) ?></td>
                        </tr>

                        <?php $i++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">
                            No Results
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
