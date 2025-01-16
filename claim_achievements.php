<?php

include 'header.php';

if ($user_class->admin < 1) {
    echo "
                    <div class='alert alert-danger'>
                        Currently undergoing some updates, back shortly!
                    </div>
                ";
    exit;
}

error_reporting(E_ALL);

$badgesex = explode(",", $user_class->badges);
$badgesclaimedex = explode(",", $user_class->badges_claimed);
$levelbadges = array(
    '6' => array(
        'needed' => 1000,
        'payout' => 125000,
        'img' => 'lvl400',
        'title' => 'Obsidian Leveler: Get to level 400'
    ),
    '5' => array(
        'needed' => 750,
        'payout' => 40000,
        'img' => 'lvl300',
        'title' => 'Gold Leveler: Get to level 300'
    ),
    '4' => array(
        'needed' => 500,
        'payout' => 25000,
        'img' => 'lvl200',
        'title' => 'Super Pro Leveler: Get to level 200'
    ),
    '3' => array(
        'needed' => 250,
        'payout' => 12500,
        'img' => 'lvl100',
        'title' => 'Pro Leveler: Get to level 100'
    ),
    '2' => array(
        'needed' => 125,
        'payout' => 7500,
        'img' => 'lvl50',
        'title' => 'Above Average Leveler: Get to level 50'
    ),
    '1' => array(
        'needed' => 50,
        'payout' => 5000,
        'img' => 'lvl25',
        'title' => 'Average Leveler: Get to level 25'
    )
);
$crimebadge = array(
    '14' => array(
        'needed' => 100000000,
        'payout' => 125000,
        'img' => 'crimes100m',
        'title' => 'Elite Criminal: Successfully complete 100,000,000 crimes'
    ),
    '13' => array(
        'needed' => 50000000,
        'payout' => 100000,
        'img' => 'crimes50m',
        'title' => 'Elite Criminal: Successfully complete 50,000,000 crimes'
    ),
    '12' => array(
        'needed' => 25000000,
        'payout' => 80000,
        'img' => 'crimes25m',
        'title' => 'Elite Criminal: Successfully complete 25,000,000 crimes'
    ),
    '11' => array(
        'needed' => 10000000,
        'payout' => 70000,
        'img' => 'crimes10m',
        'title' => 'Elite Criminal: Successfully complete 10,000,000 crimes'
    ),
    '10' => array(
        'needed' => 5000000,
        'payout' => 60000,
        'img' => 'crimes5m',
        'title' => 'Elite Criminal: Successfully complete 5,000,000 crimes'
    ),
    '9' => array(
        'needed' => 2000000,
        'payout' => 55000,
        'img' => 'crimes2m',
        'title' => 'Elite Criminal: Successfully complete 2,000,000 crimes'
    ),
    '8' => array(
        'needed' => 1500000,
        'payout' => 50000,
        'img' => 'crimes1.5m',
        'title' => 'Elite Criminal: Successfully complete 1,500,000 crimes'
    ),
    '7' => array(
        'needed' => 1000000,
        'payout' => 40000,
        'img' => 'crimes1m',
        'title' => 'Elite Criminal: Successfully complete 1,000,000 crimes'
    ),
    '6' => array(
        'needed' => 500000,
        'payout' => 30000,
        'img' => 'crimes500k',
        'title' => 'Elite Criminal: Successfully complete 500,000 crimes'
    ),
    '5' => array(
        'needed' => 250000,
        'payout' => 10000,
        'img' => 'crimes250k',
        'title' => 'Elite Criminal: Successfully complete 250,000 crimes'
    ),
    '4' => array(
        'needed' => 100000,
        'payout' => 4000,
        'img' => 'crimes100k',
        'title' => 'Elite Criminal: Successfully complete 100,000 crimes'
    ),
    '3' => array(
        'needed' => 10000,
        'payout' => 2000,
        'img' => 'crimes10k',
        'title' => 'Elite Criminal: Successfully complete 10,000 crimes'
    ),
    '2' => array(
        'needed' => 5000,
        'payout' => 750,
        'img' => 'crimes5k',
        'title' => 'Elite Criminal: Successfully complete 5,000 crimes'
    ),
    '1' => array(
        'needed' => 1000,
        'payout' => 125,
        'img' => 'crimes1k',
        'title' => 'Elite Criminal: Successfully complete 1,000 crimes'
    )
);
$statbadge = array(
    '6' => array(
        'needed' => 2500000000,
        'payout' => 100000,
        'img' => 'stats2-5b',
        'title' => 'Good Workout: Successfully gain 2,500,000,000 in Stats'
    ),
    '5' => array(
        'needed' => 1000000000,
        'payout' => 50000,
        'img' => 'stats1b',
        'title' => 'Good Workout: Successfully gain 1,000,000,000 in Stats'
    ),
    '4' => array(
        'needed' => 100000000,
        'payout' => 20000,
        'img' => 'stats100m',
        'title' => 'Good Workout: Successfully gain 100,000,000 in Stats'
    ),
    '3' => array(
        'needed' => 50000000,
        'payout' => 12500,
        'img' => 'stats50m',
        'title' => 'Good Workout: Successfully gain 50,000,000 in Stats'
    ),
    '2' => array(
        'needed' => 25000000,
        'payout' => 5000,
        'img' => 'stats25m',
        'title' => 'Good Workout: Successfully gain 25,000,000 in Stats'
    ),
    '1' => array(
        'needed' => 10000000,
        'payout' => 1250,
        'img' => 'stats10m',
        'title' => 'Good Workout: Successfully gain 10,000,000 in Stats'
    )
);
$battlebadge = array(
    '12' => array(
        'needed' => 10000000,
        'payout' => 250000,
        'img' => 'kills100m',
        'title' => 'Master Hitman: Win 10,000,000 kills'
    ),
    '11' => array(
        'needed' => 7500000,
        'payout' => 225000,
        'img' => 'kills7.5m',
        'title' => 'Master Hitman: Win 7,500,000 kills'
    ),
    '10' => array(
        'needed' => 5000000,
        'payout' => 200000,
        'img' => 'kills5m',
        'title' => 'Master Hitman: Win 5,000,000 kills'
    ),
    '9' => array(
        'needed' => 2500000,
        'payout' => 175000,
        'img' => 'kills2.5m',
        'title' => 'Master Hitman: Win 2,500,000 kills'
    ),
    '8' => array(
        'needed' => 1000000,
        'payout' => 150000,
        'img' => 'kills1m',
        'title' => 'Master Hitman: Win 1,000,000 kills'
    ),
    '7' => array(
        'needed' => 500000,
        'payout' => 125000,
        'img' => 'kills500k',
        'title' => 'Master Hitman: Win 500,000 kills'
    ),
    '6' => array(
        'needed' => 250000,
        'payout' => 100000,
        'img' => 'kills250k',
        'title' => 'Master Hitman: Win 250,000 kills'
    ),
    '5' => array(
        'needed' => 50000,
        'payout' => 50000,
        'img' => 'kills50k',
        'title' => 'Master Hitman: Win 50,000 kills'
    ),
    '4' => array(
        'needed' => 10000,
        'payout' => 20000,
        'img' => 'kills10k',
        'title' => 'Master Hitman: Win 10,000 kills'
    ),
    '3' => array(
        'needed' => 1000,
        'payout' => 5000,
        'img' => 'kills1000',
        'title' => 'Master Hitman: Win 1,000 kills'
    ),
    '2' => array(
        'needed' => 250,
        'payout' => 2500,
        'img' => 'kills250',
        'title' => 'Master Hitman: Win 250 kills'
    ),
    '1' => array(
        'needed' => 100,
        'payout' => 1250,
        'img' => 'kills100',
        'title' => 'Master Hitman: Win 100 kills'
    )
);
$bankbadge = array(
    '6' => array(
        'needed' => 1000000000,
        'payout' => 7500,
        'img' => 'bank1bill',
        'title' => 'Pro Banker: Bank $1,000,000,000'
    ),
    '5' => array(
        'needed' => 500000000,
        'payout' => 5000,
        'img' => 'bank500mill',
        'title' => 'Pro Banker: Bank $500,000,000'
    ),
    '4' => array(
        'needed' => 100000000,
        'payout' => 5000,
        'img' => 'bank100mill',
        'title' => 'Pro Banker: Bank $100,000,000'
    ),
    '3' => array(
        'needed' => 25000000,
        'payout' => 500,
        'img' => 'bank25mill',
        'title' => 'Pro Banker: Bank $25,000,000'
    ),
    '2' => array(
        'needed' => 5000000,
        'payout' => 250,
        'img' => 'bank5mill',
        'title' => 'Pro Banker: Bank $5,000,000'
    ),
    '1' => array(
        'needed' => 1000000,
        'payout' => 125,
        'img' => 'bank1mill',
        'title' => 'Pro Banker: Bank $1,000,000'
    )
);
$mugbadge = array(
    '10' => array(
        'needed' => 2500000,
        'payout' => 200000,
        'img' => 'mugs2.5m',
        'title' => 'Golden Mugger: Mugged 2,500,000 times'
    ),
    '9' => array(
        'needed' => 1000000,
        'payout' => 150000,
        'img' => 'mugs1m',
        'title' => 'Golden Mugger: Mugged 1,000,000 times'
    ),
    '8' => array(
        'needed' => 500000,
        'payout' => 100000,
        'img' => 'mugs500k',
        'title' => 'Golden Mugger: Mugged 500,000 times'
    ),
    '7' => array(
        'needed' => 250000,
        'payout' => 75000,
        'img' => 'mugs250k',
        'title' => 'Golden Mugger: Mugged 250,000 times'
    ),
    '6' => array(
        'needed' => 100000,
        'payout' => 65000,
        'img' => 'mugs100k',
        'title' => 'Golden Mugger: Mugged 100,000 times'
    ),
    '5' => array(
        'needed' => 50000,
        'payout' => 30000,
        'img' => 'mugs50k',
        'title' => 'Golden Mugger: Mugged 50,000 times'
    ),
    '4' => array(
        'needed' => 20000,
        'payout' => 10000,
        'img' => 'mugs20k',
        'title' => 'Golden Mugger: Mugged 20,000 times'
    ),
    '3' => array(
        'needed' => 5000,
        'payout' => 5000,
        'img' => 'mugs5k',
        'title' => 'Obsidian Mugger: Mugged 5,000 times'
    ),
    '2' => array(
        'needed' => 1000,
        'payout' => 2500,
        'img' => 'mugs1k',
        'title' => 'Great Mugger: Mugged 1,000 times'
    ),
    '1' => array(
        'needed' => 250,
        'payout' => 1250,
        'img' => 'mugs250',
        'title' => 'Good Mugger: Mugged 250 times'
    )
);
$bustbadge = array(
    '13' => array(
        'needed' => 5000000,
        'payout' => 200000,
        'img' => 'busts5m',
        'title' => 'Golden Buster: Bust 5,000,000 Mobsters'
    ),
    '12' => array(
        'needed' => 2000000,
        'payout' => 170000,
        'img' => 'busts2m',
        'title' => 'Golden Buster: Bust 2,000,000 Mobsters'
    ),
    '11' => array(
        'needed' => 1000000,
        'payout' => 150000,
        'img' => 'busts1m',
        'title' => 'Golden Buster: Bust 1,000,000 Mobsters'
    ),
    '10' => array(
        'needed' => 750000,
        'payout' => 140000,
        'img' => 'busts750k',
        'title' => 'Golden Buster: Bust 750,000 Mobsters'
    ),
    '9' => array(
        'needed' => 500000,
        'payout' => 120000,
        'img' => 'busts500k',
        'title' => 'Golden Buster: Bust 500,000 Mobsters'
    ),
    '8' => array(
        'needed' => 300000,
        'payout' => 100000,
        'img' => 'busts300k',
        'title' => 'Golden Buster: Bust 300,000 Mobsters'
    ),
    '7' => array(
        'needed' => 200000,
        'payout' => 80000,
        'img' => 'busts200k',
        'title' => 'Golden Buster: Bust 200,000 Mobsters'
    ),
    '6' => array(
        'needed' => 100000,
        'payout' => 70000,
        'img' => 'busts100k',
        'title' => 'Golden Buster: Bust 100,000 Mobsters'
    ),
    '5' => array(
        'needed' => 50000,
        'payout' => 30000,
        'img' => 'busts20k',
        'title' => 'Golden Buster: Bust 50,000 Mobsters'
    ),
    '4' => array(
        'needed' => 20000,
        'payout' => 10000,
        'img' => 'busts20k',
        'title' => 'Golden Buster: Bust 20,000 Mobsters'
    ),
    '3' => array(
        'needed' => 5000,
        'payout' => 5000,
        'img' => 'busts5k',
        'title' => 'Obsidian Buster: Bust 5,000 Mobsters'
    ),
    '2' => array(
        'needed' => 1000,
        'payout' => 2500,
        'img' => 'busts1k',
        'title' => 'Great Buster: Bust 1,000 Mobsters'
    ),
    '1' => array(
        'needed' => 250,
        'payout' => 1250,
        'img' => 'busts250',
        'title' => 'Good Buster: Bust 250 Mobsters'
    )
);
$missionBadges = getMissionBadges();

?>

<div class='box_top'>Achievements</div>
<div class='box_middle'>
    <div class='pad'>
        <?php

        $somethingClaimed = false;
        foreach ($levelbadges as $number => $badgers) {
            if ($user_class->level >= $badgers['needed'] && $badgesclaimedex[0] == $number - 1) {
                $somethingClaimed = true;

                echo "
                    <div class='alert alert-success'>
                        You have successfully claimed " . number_format($badgers['payout'], 0) . " points for reaching level " . prettynum($badgers['needed']) . ".
                    </div>
                ";

                $user_class->addPoints($user_class->id, $badgers['payout']);
                $badgesclaimedex[0] = $number;

                Send_Event(2, $user_class->id . ' claimed level ' . $number, 2);
            }
        }
        foreach ($crimebadge as $number => $badgers) {
            if ($user_class->crimesucceeded >= $badgers['needed'] && $badgesclaimedex[1] == $number - 1) {
                $somethingClaimed = true;

                echo "
                    <div class='alert alert-success'>
                        You have successfully claimed " . number_format($badgers['payout'], 0) . " points for reaching " . prettynum($badgers['needed']) . " Crimes.
                    </div>
                ";

                $user_class->addPoints($user_class->id, $badgers['payout']);
                $badgesclaimedex[1] = $number;

                Send_Event(2, $user_class->id . ' claimed crime ' . $number, 2);
            }
        }
        foreach ($statbadge as $number => $badgers) {
            if ($user_class->totalattrib >= $badgers['needed'] && $badgesclaimedex[2] == $number - 1) {
                $somethingClaimed = true;

                echo "
                    <div class='alert alert-success'>
                        You have successfully claimed " . number_format($badgers['payout'], 0) . " points for reaching " . prettynum($badgers['needed']) . " in Stats.
                    </div>
                ";

                $user_class->addPoints($user_class->id, $badgers['payout']);
                $badgesclaimedex[2] = $number;

                Send_Event(2, $user_class->id . ' claimed stat ' . $number, 2);
            }
        }

        foreach ($battlebadge as $number => $badgers) {
            if ($user_class->battlewon >= $badgers['needed'] && $badgesclaimedex[3] == $number - 1) {
                $somethingClaimed = true;

                echo "
                    <div class='alert alert-success'>
                        You have successfully claimed " . number_format($badgers['payout'], 0) . " points for reaching " . prettynum($badgers['needed']) . " kills.
                    </div>
                ";

                $user_class->addPoints($user_class->id, $badgers['payout']);
                $badgesclaimedex[3] = $number;

                Send_Event(2, $user_class->id . ' claimed kill ' . $number, 2);
            }
        }
        foreach ($bankbadge as $number => $badgers) {
            if (($user_class->banklog >= $badgers['needed'] || $user_class->bank >= $badgers['needed']) && $badgesclaimedex[4] == $number - 1) {
                $somethingClaimed = true;

                echo "
                    <div class='alert alert-success'>
                        You have successfully claimed " . number_format($badgers['payout'], 0) . " points for reaching $" . prettynum($badgers['needed']) . " bank.
                    </div>
                ";

                $user_class->addPoints($user_class->id, $badgers['payout']);
                $badgesclaimedex[4] = $number;

                Send_Event(2, $user_class->id . ' claimed banks ' . $number, 2);
            }
        }
        foreach ($mugbadge as $number => $badgers) {
            if ($user_class->mugsucceeded >= $badgers['needed'] && $badgesclaimedex[5] == $number - 1) {
                $somethingClaimed = true;

                echo "
                    <div class='alert alert-success'>
                        You have successfully claimed " . number_format($badgers['payout'], 0) . " points for reaching " . prettynum($badgers['needed']) . " mugs.
                    </div>
                ";

                $user_class->addPoints($user_class->id, $badgers['payout']);
                $badgesclaimedex[5] = $number;

                Send_Event(2, $user_class->id . ' claimed mug ' . $number, 2);
            }
        }
        foreach ($bustbadge as $number => $badgers) {
            if ($user_class->busts >= $badgers['needed'] && $badgesclaimedex[6] == $number - 1) {
                $somethingClaimed = true;

                echo "
                    <div class='alert alert-success'>
                        You have successfully claimed " . number_format($badgers['payout'], 0) . " points for reaching " . prettynum($badgers['needed']) . " busts.
                    </div>
                ";

                $user_class->addPoints($user_class->id, $badgers['payout']);
                $badgesclaimedex[6] = $number;

                Send_Event(2, $user_class->id . ' claimed bust ' . $number, 2);
            }
        }
        foreach ($missionBadges as $number => $badgers) {
            $missionsCount = $this->mission_count;

            if ($missionsCount >= $badgers['needed'] && $badgesclaimedex[7] == $number - 1) {
                $somethingClaimed = true;

                echo "
                    <div class='alert alert-success'>
                        You have successfully claimed " . number_format($badgers['payout'], 0) . " points for reaching " . prettynum($badgers['needed']) . " missions.
                    </div>
                ";

                $user_class->addPoints($user_class->id, $badgers['payout']);
                $badgesclaimedex[7] = $number;

                Send_Event(2, $user_class->id . ' claimed missions ' . $number, 2);
            }
        }

        if (!$somethingClaimed) {
            echo "
                    <div class='alert alert-danger'>
                        You do not have any achievements to claim.
                    </div>
                ";
        } else {
            $claimedbadgesfinal = implode(",", $badgesclaimedex);

            $db->query("UPDATE grpgusers SET badges_claimed = ? WHERE id = ?");
            $db->execute(array(
                $claimedbadgesfinal,
                $user_class->id
            ));

            //mysql_query("UPDATE grpgusers set badges_claimed = badges WHERE id = " . $user_class->id);
        }
        ?>
    </div>
</div>

