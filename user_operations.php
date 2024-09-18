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
                    <th colspan="2">Operation</th>
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
                <?php $categories = array('crimes_cash', 'crimes_points', 'crimes_premium'); ?>

                <?php foreach ($categories as $category): ?>
                    <?php
                    $categoryForDisplay = explode('_', $category);
                    $categoryForDisplay = join($categoryForDisplay, ' ');
                    $categoryForDisplay = ucwords($categoryForDisplay);
                    ?>

                    <div class="col-md-4">
                        <table class="new_table">
                            <?php $next = $nextOperationsIndexedOnCategory[$category] ?>
                            <?php if (isset($indexedOperations[$category][$next])): ?>
                                <?php $toUse = $indexedOperations[$category][$next]; ?>
                                <tr>
                                    <th><?php echo $categoryForDisplay ?></th>
                                </tr>
                                <tr>
                                    <td>
                                        <?php if ($toUse['crimes'] > 0): ?>
                                            <?php echo number_format($toUse['crimes'], 0) ?> Crimes
                                        <?php endif; ?>

                                        <?php if ($toUse['mugs'] > 0): ?>
                                            <?php echo number_format($toUse['mugs'], 0) ?> Mugs
                                        <?php endif; ?>

                                        <?php if ($toUse['busts'] > 0): ?>
                                            <?php echo number_format($toUse['busts'], 0) ?> Busts
                                        <?php endif; ?>

                                        <?php if ($toUse['online_attacks'] > 0): ?>
                                            <?php echo number_format($toUse['online_attacks'], 0) ?> Online Attacks
                                        <?php endif; ?>

                                        <?php if ($toUse['offline_attacks'] > 0): ?>
                                            <?php echo number_format($toUse['offline_attacks'], 0) ?> Offline Attacks
                                        <?php endif; ?>

                                        <?php if ($toUse['full_energy_trains'] > 0): ?>
                                            <?php echo number_format($toUse['full_energy_trains'], 0) ?> Full Energy Trains
                                        <?php endif; ?>

                                        <?php if ($toUse['city_boss_wins'] > 0): ?>
                                            <?php echo number_format($toUse['city_boss_wins'], 0) ?> City Goon Wins
                                        <?php endif; ?>

                                        <?php if ($toUse['backalleys'] > 0): ?>
                                            <?php echo number_format($toUse['backalleys'], 0) ?> Backalley Wins
                                        <?php endif; ?>
                                        <hr />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <ul>
                                            <?php if ($toUse['money_reward'] > 0): ?>
                                                <li>$<?php echo number_format($toUse['money_reward'], 0) ?></li>
                                            <?php endif; ?>

                                            <?php if ($toUse['points_reward'] > 0): ?>
                                                <li><?php echo number_format($toUse['points_reward'], 0) ?> Points</li>
                                            <?php endif; ?>

                                            <?php if ($toUse['exp_reward'] > 0): ?>
                                                <li><?php echo number_format($toUse['exp_reward'], 0) ?>% EXP</li>
                                            <?php endif; ?>
                                        </ul>

                                        <?php if ($toUse['premium_cost'] > 0): ?>
                                            <hr />
                                            <strong>Cost:</strong> <?php echo number_format($toUse['premium_cost'], 0) ?> Credits
                                        <?php endif; ?>

                                    </td>
                                </tr>
                                <tr>
                                    <td><a class="dcSecondaryButton" href="user_operations.php?start=<?php echo $category ?>">Start Operation</a></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <th><?php echo $categoryForDisplay ?></th>
                                </tr>
                                <tr>
                                    <td>Operations Complete</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>

            <h1>Mugs</h1>
            <div class="row">
                <?php $categories = array('mugs_cash', 'mugs_points'); ?>

                <?php foreach ($categories as $category): ?>
                    <?php
                    $categoryForDisplay = explode('_', $category);
                    $categoryForDisplay = join($categoryForDisplay, ' ');
                    $categoryForDisplay = ucwords($categoryForDisplay);
                    ?>

                    <div class="col-md-6">
                        <table class="new_table">
                            <?php $next = $nextOperationsIndexedOnCategory[$category] ?>
                            <?php if (isset($indexedOperations[$category][$next])): ?>
                                <?php $toUse = $indexedOperations[$category][$next]; ?>
                                <tr>
                                    <th><?php echo $categoryForDisplay ?></th>
                                </tr>
                                <tr>
                                    <td>
                                        <?php if ($toUse['crimes'] > 0): ?>
                                            <?php echo number_format($toUse['crimes'], 0) ?> Crimes
                                        <?php endif; ?>

                                        <?php if ($toUse['mugs'] > 0): ?>
                                            <?php echo number_format($toUse['mugs'], 0) ?> Mugs
                                        <?php endif; ?>

                                        <?php if ($toUse['busts'] > 0): ?>
                                            <?php echo number_format($toUse['busts'], 0) ?> Busts
                                        <?php endif; ?>

                                        <?php if ($toUse['online_attacks'] > 0): ?>
                                            <?php echo number_format($toUse['online_attacks'], 0) ?> Online Attacks
                                        <?php endif; ?>

                                        <?php if ($toUse['offline_attacks'] > 0): ?>
                                            <?php echo number_format($toUse['offline_attacks'], 0) ?> Offline Attacks
                                        <?php endif; ?>

                                        <?php if ($toUse['full_energy_trains'] > 0): ?>
                                            <?php echo number_format($toUse['full_energy_trains'], 0) ?> Full Energy Trains
                                        <?php endif; ?>

                                        <?php if ($toUse['city_boss_wins'] > 0): ?>
                                            <?php echo number_format($toUse['city_boss_wins'], 0) ?> City Goon Wins
                                        <?php endif; ?>

                                        <?php if ($toUse['backalleys'] > 0): ?>
                                            <?php echo number_format($toUse['backalleys'], 0) ?> Backalley Wins
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <ul>
                                            <?php if ($toUse['money_reward'] > 0): ?>
                                                <li>$<?php echo number_format($toUse['money_reward'], 0) ?></li>
                                            <?php endif; ?>

                                            <?php if ($toUse['points_reward'] > 0): ?>
                                                <li><?php echo number_format($toUse['points_reward'], 0) ?> Points</li>
                                            <?php endif; ?>

                                            <?php if ($toUse['exp_reward'] > 0): ?>
                                                <li><?php echo number_format($toUse['exp_reward'], 0) ?>% EXP</li>
                                            <?php endif; ?>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td><a class="dcSecondaryButton" href="user_operations.php?start=<?php echo $category ?>">Start Operation</a></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <th><?php echo $categoryForDisplay ?></th>
                                </tr>
                                <tr>
                                    <td>Operations Complete</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>

            <h1>Busts</h1>
            <div class="row">
                <?php $categories = array('busts_cash', 'busts_points'); ?>

                <?php foreach ($categories as $category): ?>
                    <?php
                    $categoryForDisplay = explode('_', $category);
                    $categoryForDisplay = join($categoryForDisplay, ' ');
                    $categoryForDisplay = ucwords($categoryForDisplay);
                    ?>

                    <div class="col-md-6">
                        <table class="new_table">
                            <?php $next = $nextOperationsIndexedOnCategory[$category] ?>
                            <?php if (isset($indexedOperations[$category][$next])): ?>
                                <?php $toUse = $indexedOperations[$category][$next]; ?>
                                <tr>
                                    <th><?php echo $categoryForDisplay ?></th>
                                </tr>
                                <tr>
                                    <td>
                                        <?php if ($toUse['crimes'] > 0): ?>
                                            <?php echo number_format($toUse['crimes'], 0) ?> Crimes
                                        <?php endif; ?>

                                        <?php if ($toUse['mugs'] > 0): ?>
                                            <?php echo number_format($toUse['mugs'], 0) ?> Mugs
                                        <?php endif; ?>

                                        <?php if ($toUse['busts'] > 0): ?>
                                            <?php echo number_format($toUse['busts'], 0) ?> Busts
                                        <?php endif; ?>

                                        <?php if ($toUse['online_attacks'] > 0): ?>
                                            <?php echo number_format($toUse['online_attacks'], 0) ?> Online Attacks
                                        <?php endif; ?>

                                        <?php if ($toUse['offline_attacks'] > 0): ?>
                                            <?php echo number_format($toUse['offline_attacks'], 0) ?> Offline Attacks
                                        <?php endif; ?>

                                        <?php if ($toUse['full_energy_trains'] > 0): ?>
                                            <?php echo number_format($toUse['full_energy_trains'], 0) ?> Full Energy Trains
                                        <?php endif; ?>

                                        <?php if ($toUse['city_boss_wins'] > 0): ?>
                                            <?php echo number_format($toUse['city_boss_wins'], 0) ?> City Goon Wins
                                        <?php endif; ?>

                                        <?php if ($toUse['backalleys'] > 0): ?>
                                            <?php echo number_format($toUse['backalleys'], 0) ?> Backalley Wins
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <ul>
                                            <?php if ($toUse['money_reward'] > 0): ?>
                                                <li>$<?php echo number_format($toUse['money_reward'], 0) ?></li>
                                            <?php endif; ?>

                                            <?php if ($toUse['points_reward'] > 0): ?>
                                                <li><?php echo number_format($toUse['points_reward'], 0) ?> Points</li>
                                            <?php endif; ?>

                                            <?php if ($toUse['exp_reward'] > 0): ?>
                                                <li><?php echo number_format($toUse['exp_reward'], 0) ?>% EXP</li>
                                            <?php endif; ?>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td><a class="dcSecondaryButton" href="user_operations.php?start=<?php echo $category ?>">Start Operation</a></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <th><?php echo $categoryForDisplay ?></th>
                                </tr>
                                <tr>
                                    <td>Operations Complete</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>

            <h1>Online Attacks</h1>
            <div class="row">
                <?php $categories = array('online_attacks_cash', 'online_attacks_points'); ?>

                <?php foreach ($categories as $category): ?>
                    <?php
                    $categoryForDisplay = explode('_', $category);
                    $categoryForDisplay = join($categoryForDisplay, ' ');
                    $categoryForDisplay = ucwords($categoryForDisplay);
                    ?>

                    <div class="col-md-6">
                        <table class="new_table">
                            <?php $next = $nextOperationsIndexedOnCategory[$category] ?>
                            <?php if (isset($indexedOperations[$category][$next])): ?>
                                <?php $toUse = $indexedOperations[$category][$next]; ?>
                                <tr>
                                    <th><?php echo $categoryForDisplay ?></th>
                                </tr>
                                <tr>
                                    <td>
                                        <?php if ($toUse['crimes'] > 0): ?>
                                            <?php echo number_format($toUse['crimes'], 0) ?> Crimes
                                        <?php endif; ?>

                                        <?php if ($toUse['mugs'] > 0): ?>
                                            <?php echo number_format($toUse['mugs'], 0) ?> Mugs
                                        <?php endif; ?>

                                        <?php if ($toUse['busts'] > 0): ?>
                                            <?php echo number_format($toUse['busts'], 0) ?> Busts
                                        <?php endif; ?>

                                        <?php if ($toUse['online_attacks'] > 0): ?>
                                            <?php echo number_format($toUse['online_attacks'], 0) ?> Online Attacks
                                        <?php endif; ?>

                                        <?php if ($toUse['offline_attacks'] > 0): ?>
                                            <?php echo number_format($toUse['offline_attacks'], 0) ?> Offline Attacks
                                        <?php endif; ?>

                                        <?php if ($toUse['full_energy_trains'] > 0): ?>
                                            <?php echo number_format($toUse['full_energy_trains'], 0) ?> Full Energy Trains
                                        <?php endif; ?>

                                        <?php if ($toUse['city_boss_wins'] > 0): ?>
                                            <?php echo number_format($toUse['city_boss_wins'], 0) ?> City Goon Wins
                                        <?php endif; ?>

                                        <?php if ($toUse['backalleys'] > 0): ?>
                                            <?php echo number_format($toUse['backalleys'], 0) ?> Backalley Wins
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <ul>
                                            <?php if ($toUse['money_reward'] > 0): ?>
                                                <li>$<?php echo number_format($toUse['money_reward'], 0) ?></li>
                                            <?php endif; ?>

                                            <?php if ($toUse['points_reward'] > 0): ?>
                                                <li><?php echo number_format($toUse['points_reward'], 0) ?> Points</li>
                                            <?php endif; ?>

                                            <?php if ($toUse['exp_reward'] > 0): ?>
                                                <li><?php echo number_format($toUse['exp_reward'], 0) ?>% EXP</li>
                                            <?php endif; ?>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td><a class="dcSecondaryButton" href="user_operations.php?start=<?php echo $category ?>">Start Operation</a></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <th><?php echo $categoryForDisplay ?></th>
                                </tr>
                                <tr>
                                    <td>Operations Complete</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>

            <h1>City Goons Cash</h1>
            <div class="row">
                <?php $categories = array('city_boss_cash', 'city_boss_points'); ?>

                <?php foreach ($categories as $category): ?>
                    <?php
                    $categoryForDisplay = explode('_', $category);
                    $categoryForDisplay = join($categoryForDisplay, ' ');
                    $categoryForDisplay = ucwords($categoryForDisplay);
                    ?>

                    <div class="col-md-6">
                        <table class="new_table">
                            <?php $next = $nextOperationsIndexedOnCategory[$category] ?>
                            <?php if (isset($indexedOperations[$category][$next])): ?>
                                <?php $toUse = $indexedOperations[$category][$next]; ?>
                                <tr>
                                    <th><?php echo $categoryForDisplay ?></th>
                                </tr>
                                <tr>
                                    <td>
                                        <?php if ($toUse['crimes'] > 0): ?>
                                            <?php echo number_format($toUse['crimes'], 0) ?> Crimes
                                        <?php endif; ?>

                                        <?php if ($toUse['mugs'] > 0): ?>
                                            <?php echo number_format($toUse['mugs'], 0) ?> Mugs
                                        <?php endif; ?>

                                        <?php if ($toUse['busts'] > 0): ?>
                                            <?php echo number_format($toUse['busts'], 0) ?> Busts
                                        <?php endif; ?>

                                        <?php if ($toUse['online_attacks'] > 0): ?>
                                            <?php echo number_format($toUse['online_attacks'], 0) ?> Online Attacks
                                        <?php endif; ?>

                                        <?php if ($toUse['offline_attacks'] > 0): ?>
                                            <?php echo number_format($toUse['offline_attacks'], 0) ?> Offline Attacks
                                        <?php endif; ?>

                                        <?php if ($toUse['full_energy_trains'] > 0): ?>
                                            <?php echo number_format($toUse['full_energy_trains'], 0) ?> Full Energy Trains
                                        <?php endif; ?>

                                        <?php if ($toUse['city_boss_wins'] > 0): ?>
                                            <?php echo number_format($toUse['city_boss_wins'], 0) ?> City Goon Wins
                                        <?php endif; ?>

                                        <?php if ($toUse['backalleys'] > 0): ?>
                                            <?php echo number_format($toUse['backalleys'], 0) ?> Backalley Wins
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <ul>
                                            <?php if ($toUse['money_reward'] > 0): ?>
                                                <li>$<?php echo number_format($toUse['money_reward'], 0) ?></li>
                                            <?php endif; ?>

                                            <?php if ($toUse['points_reward'] > 0): ?>
                                                <li><?php echo number_format($toUse['points_reward'], 0) ?> Points</li>
                                            <?php endif; ?>

                                            <?php if ($toUse['exp_reward'] > 0): ?>
                                                <li><?php echo number_format($toUse['exp_reward'], 0) ?>% EXP</li>
                                            <?php endif; ?>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td><a class="dcSecondaryButton" href="user_operations.php?start=<?php echo $category ?>">Start Operation</a></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <th><?php echo $categoryForDisplay ?></th>
                                </tr>
                                <tr>
                                    <td>Operations Complete</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>

            <h1>Backalley Wins Cash</h1>
            <div class="row">
                <?php $categories = array('backalley_cash', 'backalley_points'); ?>

                <?php foreach ($categories as $category): ?>
                    <?php
                    $categoryForDisplay = explode('_', $category);
                    $categoryForDisplay = join($categoryForDisplay, ' ');
                    $categoryForDisplay = ucwords($categoryForDisplay);
                    ?>

                    <div class="col-md-6">
                        <table class="new_table">
                            <?php $next = $nextOperationsIndexedOnCategory[$category] ?>
                            <?php if (isset($indexedOperations[$category][$next])): ?>
                                <?php $toUse = $indexedOperations[$category][$next]; ?>
                                <tr>
                                    <th><?php echo $categoryForDisplay ?></th>
                                </tr>
                                <tr>
                                    <td>
                                        <?php if ($toUse['crimes'] > 0): ?>
                                            <?php echo number_format($toUse['crimes'], 0) ?> Crimes
                                        <?php endif; ?>

                                        <?php if ($toUse['mugs'] > 0): ?>
                                            <?php echo number_format($toUse['mugs'], 0) ?> Mugs
                                        <?php endif; ?>

                                        <?php if ($toUse['busts'] > 0): ?>
                                            <?php echo number_format($toUse['busts'], 0) ?> Busts
                                        <?php endif; ?>

                                        <?php if ($toUse['online_attacks'] > 0): ?>
                                            <?php echo number_format($toUse['online_attacks'], 0) ?> Online Attacks
                                        <?php endif; ?>

                                        <?php if ($toUse['offline_attacks'] > 0): ?>
                                            <?php echo number_format($toUse['offline_attacks'], 0) ?> Offline Attacks
                                        <?php endif; ?>

                                        <?php if ($toUse['full_energy_trains'] > 0): ?>
                                            <?php echo number_format($toUse['full_energy_trains'], 0) ?> Full Energy Trains
                                        <?php endif; ?>

                                        <?php if ($toUse['city_boss_wins'] > 0): ?>
                                            <?php echo number_format($toUse['city_boss_wins'], 0) ?> City Goon Wins
                                        <?php endif; ?>

                                        <?php if ($toUse['backalleys'] > 0): ?>
                                            <?php echo number_format($toUse['backalleys'], 0) ?> Backalley Wins
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <ul>
                                            <?php if ($toUse['money_reward'] > 0): ?>
                                                <li>$<?php echo number_format($toUse['money_reward'], 0) ?></li>
                                            <?php endif; ?>

                                            <?php if ($toUse['points_reward'] > 0): ?>
                                                <li><?php echo number_format($toUse['points_reward'], 0) ?> Points</li>
                                            <?php endif; ?>

                                            <?php if ($toUse['exp_reward'] > 0): ?>
                                                <li><?php echo number_format($toUse['exp_reward'], 0) ?>% EXP</li>
                                            <?php endif; ?>
                                        </ul>

                                    </td>
                                </tr>
                                <tr>
                                    <td><a class="dcSecondaryButton" href="user_operations.php?start=<?php echo $category ?>">Start Operation</a></td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <th><?php echo $categoryForDisplay ?></th>
                                </tr>
                                <tr>
                                    <td>Operations Complete</td>
                                </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?php
require "footer.php";
?>