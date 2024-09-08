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
    $indexedOperations[$operation['category']][] = $operation;
}

$db->query("SELECT * FROM `user_operations` WHERE `user_id` = ? AND (`is_complete` = 0 OR `is_complete` IS NULL) AND (`is_skipped` = 0 OR `is_skipped` IS NULL) ORDER BY `id` DESC LIMIT 1");
$db->execute(array($user_class->id));
$currentUserOperation = $db->fetch_row(true);

$currentOperation = null;
if ($currentUserOperation) {
    $db->query("SELECT * FROM operations WHERE id = " . $currentUserOperation['operations_id'] . " LIMIT 1");
    $db->execute();
    $currentOperation = $db->fetch_row(true);
}

$nextOperationsIndexedOnCategory = array();
foreach ($indexedOperations as $category => $operations) {
    $db->query("
        SELECT uo.* FROM 
            user_operations AS uo
            LEFT JOIN operations AS o ON uo.operations_id = o.id
        WHERE 
            uo.user_id = ? 
            AND (uo.is_complete = 1 OR uo.is_skipped = 1)
            AND o.category = ?        
        ORDER BY 
            uo.id DESC
        ");
    $db->execute(array($user_class->id, $category));
    $lastUserOperations = $db->fetch_row();

    $nextUserOperation = count($lastUserOperations);

    $nextOperationsIndexedOnCategory[$category] = $nextUserOperation;
}
print_r($nextOperationsIndexedOnCategory);

if (isset($_GET['start'])) {
    $validOptions = array(
        'crimes_cash',
        'crimes_points',
        'mugs_cash',
        'mugs_points',
        'busts_cash',
        'busts_points',
        'online_attacks_cash',
        'online_attacks_points',
        'city_boss_cash',
        'city_boss_points',
        'backalley_cash',
        'backalley_points',
    );

    if (!in_array($_GET['start'], $validOptions)) {
        diefun('Something went wrong, if this issue persists please DM an admin. <a href="user_operations.php">Go Back</a>');
        exit;
    }

    if ($currentUserOperation) {
        diefun('You already have an active operation. <a href="user_operations.php">Go Back</a>');
        exit;
    }
    $start = $_GET['start'];
    $next = $nextOperationsIndexedOnCategory[$start];

    if (isset($indexedOperations[$start][$next])) {
        $operationToUse = $indexedOperations[$start][$next];

        $db->query('
            INSERT INTO 
                user_operations (user_id, operations_id)
            VALUES 
                (?,?)
        ');
        $db->execute(array($user_class->id, $operationToUse['id']));

        header("Location: user_operations.php");
    } else {
        diefun('You have completed all operations for this category. <a href="user_operations.php">Go Back</a>');
        exit;
    }
}
?>

<h1>Operations</h1><hr />

<div class="row">
    <div class="col-md-12">
        <?php if ($currentUserOperation): ?>
            <table class="new_table" id="newtables" style="width:100%;">
                <tr>
                    <th colspan="2">Operation #<?php echo $currentOperation['id'] ?></th>
                </tr>
                <!-- CRIMES -->
                <tr>
                    <th>
                        Crimes
                    </th>
                    <td>
                        <?php echo number_format($currentUserOperation['crimes'], 0) ?>/<?php echo number_format($currentOperation['crimes'], 0) ?>
                    </td>
                </tr>

                <!-- MUGS -->
                <tr>
                    <th>
                        Mugs
                    </th>
                    <td>
                        <?php echo number_format($currentUserOperation['mugs'], 0) ?>/<?php echo number_format($currentOperation['mugs'], 0) ?>
                    </td>
                </tr>

                <!-- BUSTS -->
                <tr>
                    <th>
                        Busts
                    </th>
                    <td>
                        <?php echo number_format($currentUserOperation['busts'], 0) ?>/<?php echo number_format($currentOperation['busts'], 0) ?>
                   </td>
                </tr>

                <!-- ONLINE ATTACKS -->
                <tr>
                    <th>
                        Online Attacks
                    </th>
                    <td>
                        <?php echo number_format($currentUserOperation['online_attacks'], 0) ?>/<?php echo number_format($currentOperation['online_attacks'], 0) ?>
                    </td>
                </tr>

                <!-- CITY BOSS WINS -->
                <tr>
                    <th>
                        City Goons Wins
                    </th>
                    <td>
                        <?php echo number_format($currentUserOperation['city_boss_wins'], 0) ?>/<?php echo number_format($currentOperation['city_boss_wins'], 0) ?>
                    </td>
                </tr>

                <!-- BACKALLEY WINS -->
                <tr>
                    <th>
                        Backalley Wins
                    </th>
                    <td>
                        <?php echo number_format($currentUserOperation['backalley_wins'], 0) ?>/<?php echo number_format($currentOperation['backalley_wins'], 0) ?>
                    </td>
                </tr>
            </table>
        <?php else: ?>
            <h1>Crimes</h1>
            <div class="row">
                <div class="col-md-6">
                    <table class="new_table">
                        <?php $next = $nextOperationsIndexedOnCategory['crimes_cash'] ?>
                        <?php if (isset($indexedOperations['crimes_cash'][$next])): ?>
                            <?php $toUse = $indexedOperations['crimes_cash'][$next]; ?>
                            <tr>
                                <th>Crime Cash #<?php echo $toUse['id'] ?></th>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['crimes'], 0) ?> Crimes</td>
                            </tr>
                            <tr>
                                <td>$<?php echo number_format($toUse['money_reward'], 0) ?></td>
                            </tr>
                            <tr>
                                <td><a class="dcSecondaryButton" href="user_operations.php?start=crimes_cash">Start Operation</a></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>Crime Cash</th>
                            </tr>
                            <tr>
                                <td>Operations Complete</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="new_table">
                        <?php $next = $nextOperationsIndexedOnCategory['crimes_points'] ?>
                        <?php if (isset($indexedOperations['crimes_points'][$next])): ?>
                            <?php $toUse = $indexedOperations['crimes_points'][$next]; ?>
                            <tr>
                                <th>Crime Points #<?php echo $toUse['id'] ?></th>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['crimes'], 0) ?> Crimes</td>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['points_reward'], 0) ?> Points</td>
                            </tr>
                            <tr>
                                <td><a class="dcSecondaryButton" href="user_operations.php?start=crimes_points">Start Operation</a></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>Crime Points</th>
                            </tr>
                            <tr>
                                <td>Operations Complete</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <h1>Mugs</h1>
            <div class="row">
                <div class="col-md-6">
                    <table class="new_table">
                        <?php $next = $nextOperationsIndexedOnCategory['mugs_cash'] ?>
                        <?php if (isset($indexedOperations['mugs_cash'][$next])): ?>
                            <?php $toUse = $indexedOperations['mugs_cash'][$next]; ?>
                            <tr>
                                <th>Mugs Cash #<?php echo $toUse['id'] ?></th>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['mugs'], 0) ?> Mugs</td>
                            </tr>
                            <tr>
                                <td>$<?php echo number_format($toUse['money_reward'], 0) ?></td>
                            </tr>
                            <tr>
                                <td><a class="dcSecondaryButton" href="user_operations.php?start=mugs_cash">Start Operation</a></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>Mugs Cash</th>
                            </tr>
                            <tr>
                                <td>Operations Complete</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="new_table">
                        <?php $next = $nextOperationsIndexedOnCategory['mugs_points'] ?>
                        <?php if (isset($indexedOperations['mugs_points'][$next])): ?>
                            <?php $toUse = $indexedOperations['mugs_points'][$next]; ?>
                            <tr>
                                <th>Mugs Points #<?php echo $toUse['id'] ?></th>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['mugs'], 0) ?> Mugs</td>
                            </tr>
                            <tr>
                                <td>$<?php echo number_format($toUse['points_reward'], 0) ?></td>
                            </tr>
                            <tr>
                                <td><a class="dcSecondaryButton" href="user_operations.php?start=mugs_points">Start Operation</a></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>Mugs Points</th>
                            </tr>
                            <tr>
                                <td>Operations Complete</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <h1>Busts</h1>
            <div class="row">
                <div class="col-md-6">
                    <table class="new_table">
                        <?php $next = $nextOperationsIndexedOnCategory['busts_cash'] ?>
                        <?php if (isset($indexedOperations['busts_cash'][$next])): ?>
                            <?php $toUse = $indexedOperations['busts_cash'][$next]; ?>
                            <tr>
                                <th>Busts Cash #<?php echo $toUse['id'] ?></th>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['busts'], 0) ?> Busts</td>
                            </tr>
                            <tr>
                                <td>$<?php echo number_format($toUse['money_reward'], 0) ?></td>
                            </tr>
                            <tr>
                                <td><a class="dcSecondaryButton" href="user_operations.php?start=busts_cash">Start Operation</a></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>Busts Cash</th>
                            </tr>
                            <tr>
                                <td>Operations Complete</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="new_table">
                        <?php $next = $nextOperationsIndexedOnCategory['busts_points'] ?>
                        <?php if (isset($indexedOperations['busts_points'][$next])): ?>
                            <?php $toUse = $indexedOperations['busts_points'][$next]; ?>
                            <tr>
                                <th>Busts Points #<?php echo $toUse['id'] ?></th>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['busts'], 0) ?> Busts</td>
                            </tr>
                            <tr>
                                <td>$<?php echo number_format($toUse['points_reward'], 0) ?></td>
                            </tr>
                            <tr>
                                <td><a class="dcSecondaryButton" href="user_operations.php?start=busts_points">Start Operation</a></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>Busts Points</th>
                            </tr>
                            <tr>
                                <td>Operations Complete</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <h1>Online Attacks</h1>
            <div class="row">
                <div class="col-md-6">
                    <table class="new_table">
                        <?php $next = $nextOperationsIndexedOnCategory['online_attacks_cash'] ?>
                        <?php if (isset($indexedOperations['online_attacks_cash'][$next])): ?>
                            <?php $toUse = $indexedOperations['online_attacks_cash'][$next]; ?>
                            <tr>
                                <th>Online Attacks Cash #<?php echo $toUse['id'] ?></th>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['online_attacks'], 0) ?> Online Attacks</td>
                            </tr>
                            <tr>
                                <td>$<?php echo number_format($toUse['money_reward'], 0) ?></td>
                            </tr>
                            <tr>
                                <td><a class="dcSecondaryButton" href="user_operations.php?start=online_attacks_cash">Start Operation</a></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>Online Attacks Cash</th>
                            </tr>
                            <tr>
                                <td>Operations Complete</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="new_table">
                        <?php $next = $nextOperationsIndexedOnCategory['online_attacks_points'] ?>
                        <?php if (isset($indexedOperations['online_attacks_points'][$next])): ?>
                            <?php $toUse = $indexedOperations['online_attacks_points'][$next]; ?>
                            <tr>
                                <th>Online Attacks Points #<?php echo $toUse['id'] ?></th>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['online_attacks'], 0) ?> Online Attacks</td>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['points_reward'], 0) ?> Points</td>
                            </tr>
                            <tr>
                                <td><a class="dcSecondaryButton" href="user_operations.php?start=online_attacks_points">Start Operation</a></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>Online Attacks Points</th>
                            </tr>
                            <tr>
                                <td>Operations Complete</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <h1>City Goons Cash</h1>
            <div class="row">
                <div class="col-md-6">
                    <table class="new_table">
                        <?php $next = $nextOperationsIndexedOnCategory['city_boss_cash'] ?>
                        <?php if (isset($indexedOperations['city_boss_cash'][$next])): ?>
                            <?php $toUse = $indexedOperations['city_boss_cash'][$next]; ?>
                            <tr>
                                <th>City Goons Cash #<?php echo $toUse['id'] ?></th>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['city_boss_wins'], 0) ?> City Goon Wins</td>
                            </tr>
                            <tr>
                                <td>$<?php echo number_format($toUse['money_reward'], 0) ?></td>
                            </tr>
                            <tr>
                                <td><a class="dcSecondaryButton" href="user_operations.php?start=city_boss_cash">Start Operation</a></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>City Goons Cash</th>
                            </tr>
                            <tr>
                                <td>Operations Complete</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="new_table">
                        <?php $next = $nextOperationsIndexedOnCategory['city_boss_points'] ?>
                        <?php if (isset($indexedOperations['city_boss_points'][$next])): ?>
                            <?php $toUse = $indexedOperations['city_boss_points'][$next]; ?>
                            <tr>
                                <th>City Goons Points #<?php echo $toUse['id'] ?></th>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['city_boss_wins'], 0) ?> City Goon Wins</td>
                            </tr>
                            <tr>
                                <td>$<?php echo number_format($toUse['points_reward'], 0) ?></td>
                            </tr>
                            <tr>
                                <td><a class="dcSecondaryButton" href="user_operations.php?start=city_boss_points">Start Operation</a></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>City Goons Points</th>
                            </tr>
                            <tr>
                                <td>Operations Complete</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <h1>Backalley Wins Cash</h1>
            <div class="row">
                <div class="col-md-6">
                    <table class="new_table">
                        <?php $next = $nextOperationsIndexedOnCategory['backalley_cash'] ?>
                        <?php if (isset($indexedOperations['backalley_cash'][$next])): ?>
                            <?php $toUse = $indexedOperations['backalley_cash'][$next]; ?>
                            <tr>
                                <th>City Goons Cash #<?php echo $toUse['id'] ?></th>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['backalleys'], 0) ?> Backalley wins</td>
                            </tr>
                            <tr>
                                <td>$<?php echo number_format($toUse['money_reward'], 0) ?></td>
                            </tr>
                            <tr>
                                <td><a class="dcSecondaryButton" href="user_operations.php?start=backalley_cash">Start Operation</a></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>Backalley Wins Cash</th>
                            </tr>
                            <tr>
                                <td>Operations Complete</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="new_table">
                        <?php $next = $nextOperationsIndexedOnCategory['backalley_points'] ?>
                        <?php if (isset($indexedOperations['backalley_points'][$next])): ?>
                            <?php $toUse = $indexedOperations['backalley_points'][$next]; ?>
                            <tr>
                                <th>Backalley Points #<?php echo $toUse['id'] ?></th>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['backalleys'], 0) ?> Backalley Wins</td>
                            </tr>
                            <tr>
                                <td><?php echo number_format($toUse['points_reward'], 0) ?> Points</td>
                            </tr>
                            <tr>
                                <td><a class="dcSecondaryButton" href="user_operations.php?start=backalley_points">Start Operation</a></td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <th>Backalley Points</th>
                            </tr>
                            <tr>
                                <td>Operations Complete</td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php
require "footer.php";
?>