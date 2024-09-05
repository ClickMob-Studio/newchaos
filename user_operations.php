<?php
include 'header.php';

$db->query("SELECT * FROM `operations`");
$db->execute();
$operations = $db->fetch_row();

$indexedOperations = array();
foreach ($operations as $operation) {
    if (!isset($indexedOperations[$operation['category']])) {
        $indexedOperations[$operation['category']] = array();
    }
    $indexedOperations[$operation['category']][$operation['id']] = $operation;
}

$db->query("SELECT * FROM `user_operations` WHERE `user_id` = ? AND (`is_complete` = 0 OR `is_complete` IS NULL) AND (`is_skipped` = 0 OR `is_skipped` IS NULL) ORDER BY `id` DESC LIMIT 1");
$db->execute(array($user_class->id));
$currentUserOperation = $db->fetch_row(true);
?>

<h1>Operations</h1>

<div class="row">
    <div class="col-md-12">
        <?php if ($currentUserOperation): ?>
            <table class="new_table" id="newtables" style="width:100%;">
                <tr>
                    <th colspan="2">Operation #<?php echo $currentUserOperation['id'] ?></th>
                </tr>
                <!-- CRIMES -->
                <tr>
                    <th>
                        Crimes
                    </th>
                    <td>
                        0/<?php echo number_format($currentUserOperation['crimes'], 0) ?>
                    </td>
                </tr>

                <!-- MUGS -->
                <tr>
                    <th>
                        Mugs
                    </th>
                    <td>
                        0/<?php echo number_format($currentUserOperation['mugs'], 0) ?>
                    </td>
                </tr>

                <!-- BUSTS -->
                <tr>
                    <th>
                        Busts
                    </th>
                    <td>
                        0/<?php echo number_format($currentUserOperation['busts'], 0) ?>
                    </td>
                </tr>

                <!-- ONLINE ATTACKS -->
                <tr>
                    <th>
                        Online Attacks
                    </th>
                    <td>
                        0/<?php echo number_format($currentUserOperation['online_attacks'], 0) ?>
                    </td>
                </tr>

                <!-- OFFLINE ATTACKS -->
                <tr>
                    <th>
                        Offline Attacks
                    </th>
                    <td>
                        0/<?php echo number_format($currentUserOperation['offline_attacks'], 0) ?>
                    </td>
                </tr>

                <!-- FULL ENERGY TRAINS -->
                <tr>
                    <th>
                        Full Energy Trains
                    </th>
                    <td>
                        0/<?php echo number_format($currentUserOperation['full_energy_trains'], 0) ?>
                    </td>
                </tr>

                <!-- CITY BOSS WINS -->
                <tr>
                    <th>
                        City Boss Wins
                    </th>
                    <td>
                        0/<?php echo number_format($currentUserOperation['city_boss_wins'], 0) ?>
                    </td>
                </tr>

                <!-- BACKALLEY WINS -->
                <tr>
                    <th>
                        Backalley Wins
                    </th>
                    <td>
                        0/<?php echo number_format($currentUserOperation['backalley_wins'], 0) ?>
                    </td>
                </tr>

                <!-- RAIDS -->
                <tr>
                    <th>
                        Raids
                    </th>
                    <td>
                        0/<?php echo number_format($currentUserOperation['raids'], 0) ?>
                    </td>
                </tr>
            </table>
        <?php else: ?>
            <h1>Crimes</h1><hr />
            <div class="row">
                <div class="col-md-6">
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php
require "footer.php";
?>