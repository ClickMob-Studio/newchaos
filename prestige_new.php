<?php
include "header.php";

$prestigeUserSkills = getUserPrestigeSkills($user_class->id);

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

?>

<div class='box_top'>Account Prestige</div>
<div class='box_middle'>
    <div class='pad'>
        <p>Welcome to Account Prestiges!</p>

        <h2>Prestige Unlocks</h2>
        <div class="row">
            <?php foreach ($prestigeUnlocks as $key => $prestigeUnlock): ?>
                <div class="col-md-4">
                    <div class="card text-white bg-danger mb-3" style="min-height: 170px;">
                        <div class="card-header"><?php echo $prestigeUnlock['name'] ?></div>
                        <div class="card-body">
                            <p class="card-text">
                                <?php echo $prestigeUnlock['description'] ?>
                            </p>
                        </div>
                        <div class="card-footer">
                            <center>
                                <a href="#"><button>Unlock</button></a>
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
