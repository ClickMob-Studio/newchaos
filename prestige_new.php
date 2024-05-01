<?php
include "header.php";

if ($user_class->admin < 1) {
    exit;
}

$userPrestigeSkills = getUserPrestigeSkills($user_class);

$prestigeUnlocks = array();
// BA Raid Tokens
$prestigeUnlocks['ba_raidtokens_unlock'] = array(
        'name' => 'BA Raid Tokens',
        'description' => 'Unlocking BA Raid Tokens will allow you to find Raid Tokens with normal Backalley searches, not just in Gold Rush!'
);
// Super Attack
$prestigeUnlocks['speed_attack_unlock'] = array(
    'name' => 'Super Attack',
    'description' => 'Unlocking Super Attack gives you access to a new feature where you can attack up to 50 players with one click of a button, helping you complete those important kill missions!'
);
// BA Gold Rush Boost
$prestigeUnlocks['ba_gold_rush_unlock'] = array(
    'name' => 'BA Gold Rush Boost',
    'description' => 'Unlocking Ba Gold Rush boost gives you a 10% boosted chance of finding Gold Rush in the Backalley'
);
// Crime Cash
$prestigeUnlocks['crime_cash_unlock'] = array(
    'name' => 'Crime Cash Boost',
    'description' => 'Unlocking Crime Cash Boost gives an extra 10% cash from all crimes you complete.'
);
// Throne Payout Boost
$prestigeUnlocks['throne_points_unlock'] = array(
    'name' => 'Throne Payout Boost',
    'description' => 'Unlocking Throne Payout Boost gives you an extra 20% payouts from all Boss/Under Boss positions held!'
);
// Travel Cost Reduction
$prestigeUnlocks['travel_cost_unlock'] = array(
    'name' => 'Travel Cost Reduction',
    'description' => 'Unlocking Travel Cost Reduction gives you a 20% reduction on all travel costs.'
);


if (isset($_GET['action']) && $_GET['action'] === 'add_unlock' && isset($_GET['unlock_type'])) {
    $unlockType = $_GET['unlock_type'];
    if (!isset($prestigeUnlocks[$unlockType])) {
        diefun('Something went wrong, please DM an Admin if this issue persists.');
    }

    if ($userPrestigeSkills['prestige_unlocks_available'] < 1) {
        diefun('You do not have any prestige unlocks available');
    }

    if ($userPrestigeSkills[$unlockType] > 0) {
        diefun('You have already activated this unlock');
    }

    $db->query('UPDATE user_prestige_skills SET ' . $unlockType . ' = 1, unlock_points_spent = unlock_points_spent + 1 WHERE user_id = ' . $user_class->id);
    $db->execute();

    echo Message("You have successfully unlocked " . $prestigeUnlocks[$unlockType]['name']);
}

?>

<div class='box_top'>Account Prestige</div>
<div class='box_middle'>
    <div class='pad'>
        <p>Welcome to Account Prestiges!</p>

        <h2>Prestige Unlocks</h2>
        <p>You currently have <?php echo $userPrestigeSkills['prestige_unlocks_available'] ?> prestige unlocks available.</p>
        <hr />
        <div class="row">
            <?php foreach ($prestigeUnlocks as $key => $prestigeUnlock): ?>
                <?php
                $divClass = 'bg-danger';
                $button = '<a href="prestige_new.php?action=add_unlock&unlock_type=' . $key .'"><button>Unlock</button></a>';
                if ($userPrestigeSkills[$key] > 0) {
                    $divClass = 'bg-success';
                    $button = '';
                }
                ?>
                <div class="col-md-4">
                    <div class="card text-white <?php echo $divClass ?> mb-3" style="min-height: 240px;">
                        <div class="card-header"><?php echo $prestigeUnlock['name'] ?></div>
                        <div class="card-body">
                            <p class="card-text">
                                <?php echo $prestigeUnlock['description'] ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <center>
                                <?php echo $button ?>
                            </center>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>


<?php
include "footer.php";
?>
