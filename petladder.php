<?php
include 'header.php';

$attackRows = $db->query("SELECT * FROM petladder WHERE attacks > 0 ORDER BY attacks DESC LIMIT 10");
$attackRows = $db->fetch_row();

$trainingRows = $db->query("SELECT * FROM petladder WHERE gym > 0 ORDER BY gym DESC LIMIT 10");
$trainingRows = $db->fetch_row();

$expRows = $db->query("SELECT * FROM petladder WHERE exp > 0 ORDER BY exp DESC LIMIT 10");
$expRows = $db->fetch_row();

?>

<h1>Pet Ladder</h1>
<p>Welcome to the Pet Ladder, here you can earn points by trying to be the best pet owner in CC! Prizes are paid and the
    ladder resets hourly.</p>

<p><strong>Prizes:</strong></p>
<ul>
    <li>1st Place: 3,000 points</li>
    <li>2nd Place: 1,500 points</li>
    <li>3rd Place: 500 points</li>
</ul>

<div class="row">
    <div class="col-md-4">
        <div class="table-container">
            <h2>Attacks</h2>
            <table class="new_table" id="newtables" style="width:100%;">
                <tr>
                    <th>&nbsp;</th>
                    <th>Pet</th>
                    <th>User</th>
                    <th>Points</th>
                </tr>
                <?php if (count($attackRows) > 0): ?>
                    <?php $i = 1; ?>
                    <?php foreach ($attackRows as $attackRow): ?>
                        <?php
                        $pet = $db->query("SELECT * FROM pets WHERE id = " . $attackRow['pet_id'] . " LIMIT 1");
                        $pet = $db->fetch_row(true);
                        ?>

                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo formatName($pet['userid']) ?></td>
                            <td><?php echo $pet['pname'] ?></td>
                            <td><?php echo number_format($attackRow['attacks'], 0) ?></td>
                        </tr>

                        <?php $i++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">
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
                    <th>Pet</th>
                    <th>User</th>
                    <th>Points</th>
                </tr>
                <?php if (count($trainingRows) > 0): ?>
                    <?php $i = 1; ?>
                    <?php foreach ($trainingRows as $trainingRow): ?>
                        <?php
                        $pet = $db->query("SELECT * FROM pets WHERE id = " . $trainingRow['pet_id'] . " LIMIT 1");
                        $pet = $db->fetch_row(true);
                        ?>

                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo formatName($pet['userid']) ?></td>
                            <td><?php echo $pet['pname'] ?></td>
                            <td><?php echo number_format($trainingRow['gym'], 0) ?></td>
                        </tr>

                        <?php $i++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">
                            No Results
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div class="col-md-4">
        <div class="table-container">
            <h2>Crime EXP</h2>
            <table class="new_table" id="newtables" style="width:100%;">
                <tr>
                    <th>&nbsp;</th>
                    <th>Pet</th>
                    <th>User</th>
                    <th>Points</th>
                </tr>
                <?php if (count($expRows) > 0): ?>
                    <?php $i = 1; ?>
                    <?php foreach ($expRows as $expRow): ?>
                        <?php
                        $pet = $db->query("SELECT * FROM pets WHERE id = " . $expRow['pet_id'] . " LIMIT 1");
                        $pet = $db->fetch_row(true);
                        ?>

                        <tr>
                            <td><?php echo $i ?></td>
                            <td><?php echo formatName($pet['userid']) ?></td>
                            <td><?php echo $pet['pname'] ?></td>
                            <td><?php echo number_format($expRow['exp'], 0) ?></td>
                        </tr>

                        <?php $i++; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4">
                            No Results
                        </td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>