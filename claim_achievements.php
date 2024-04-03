<?php
include 'header.php';

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
            }
        }
//        foreach ($crimebadge as $number => $badgers) {
//            if ($this->crimesucceeded >= $badgers['needed'] && $this->badgesex[1] == $number - 1) {
//                // $db->execute(array(
//                // 	$badgers['payout'],
//                // 	$id
//                // ));
//                mysql_query("UPDATE grpgusers SET points = points + ".$badgers['payout']." WHERE id = ".$this->id);
//                Send_Event($this->id, "You have received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " Crimes.", $this->id);
//                Send_Event1($this->id, "Has just received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " Crimes.", $this->id);
//                $this->badgesex[1] = $number;
//            }
//            if (!isset($this->badge2) && $this->crimesucceeded >= $badgers['needed']) {
//                $this->badge2 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img src="css/images/'.$badgers["img"].'.png"></img></div>';
//                $this->badge = 1;
//            }
//        }
//        foreach ($statbadge as $number => $badgers) {
//            if ($this->totalattrib >= $badgers['needed'] && $this->badgesex[2] == $number - 1) {
//                // $db->execute(array(
//                // 	$badgers['payout'],
//                // 	$id
//                // ));
//                mysql_query("UPDATE grpgusers SET points = points + ".$badgers['payout']." WHERE id = ".$this->id);
//                Send_Event($this->id, "You have received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " in Stats.", $this->id);
//                Send_Event1($this->id, "Has just received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " in Stats.", $this->id);
//                $this->badgesex[2] = $number;
//            }
//            if (!isset($this->badge5) && $this->totalattrib >= $badgers['needed']) {
//                $this->badge5 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img src="css/images/'.$badgers["img"].'.png"></img></div>';
//                $this->badge = 1;
//            }
//        }
//        foreach ($battlebadge as $number => $badgers) {
//            if ($this->battlewon >= $badgers['needed'] && $this->badgesex[3] == $number - 1) {
//                // $db->execute(array(
//                // 	$badgers['payout'],
//                // 	$id
//                // ));
//                mysql_query("UPDATE grpgusers SET points = points + ".$badgers['payout']." WHERE id = ".$this->id);
//                Send_Event($this->id, "You have received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " kills.", $this->id);
//                Send_Event1($this->id, "Has just received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " kills.", $this->id);
//                $this->badgesex[3] = $number;
//            }
//            if (!isset($this->badge4) && $this->battlewon >= $badgers['needed']) {
//                $this->badge4 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img src="css/images/'.$badgers["img"].'.png"></img></div>';
//                $this->badge = 1;
//            }
//        }
//        foreach ($bankbadge as $number => $badgers) {
//            if (($this->banklog >= $badgers['needed'] || $this->bank >= $badgers['needed']) && $this->badgesex[4] == $number - 1) {
//                // $db->execute(array(
//                // 	$badgers['payout'],
//                // 	$id
//                // ));
//                mysql_query("UPDATE grpgusers SET points = points + ".$badgers['payout']." WHERE id = ".$this->id);
//                Send_Event($this->id, "You have received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " bank.", $this->id);
//                Send_Event1($this->id, "Has just received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " bank.", $this->id);
//                $this->badgesex[4] = $number;
//            }
//            if (!isset($this->badge6) && $this->banklog >= $badgers['needed']) {
//                $this->badge6 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img src="css/images/'.$badgers["img"].'.png"></img></div>';
//                $this->badge = 1;
//            }
//        }
//        foreach ($mugbadge as $number => $badgers) {
//            if ($this->mugsucceeded >= $badgers['needed'] && $this->badgesex[5] == $number - 1) {
//                // $db->execute(array(
//                // 	$badgers['payout'],
//                // 	$id
//                // ));
//                mysql_query("UPDATE grpgusers SET points = points + ".$badgers['payout']." WHERE id = ".$this->id);
//                Send_Event($this->id, "You have received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " mugs.", $this->id);
//                Send_Event1($this->id, "Has just received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " mugs.", $this->id);
//                $this->badgesex[5] = $number;
//            }
//            if (!isset($this->badge7) && $this->mugsucceeded >= $badgers['needed']) {
//                $this->badge7 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img src="css/images/'.$badgers["img"].'.png"></img></div>';
//                $this->badge = 1;
//            }
//        }
//        foreach ($bustbadge as $number => $badgers) {
//            if ($this->busts >= $badgers['needed'] && $this->badgesex[6] == $number - 1) {
//                // $db->execute(array(
//                // 	$badgers['payout'],
//                // 	$id
//                // ));
//                mysql_query("UPDATE grpgusers SET points = points + ".$badgers['payout']." WHERE id = ".$this->id);
//                Send_Event($this->id, "You have received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " busts.", $this->id);
//                Send_Event1($this->id, "Has just received {$badgers['payout']} Points for Reaching " . prettynum($badgers['needed']) . " busts.", $this->id);
//                $this->badgesex[6] = $number;
//            }
//            if (!isset($this->badge8) && $this->busts >= $badgers['needed']) {
//                $this->badge8 = '<div class="ach' . $badgers['img'] . '" title="' . $badgers['title'] . '"><img src="css/images/'.$badgers["img"].'.png"></img></div>';
//                $this->badge = 1;
//            }
//        }
//        $this->badgesfinal = implode(",", $this->badgesex);
//        if ($this->badgesfinal != $this->badges){
//            $db->query("UPDATE grpgusers SET badges = ? WHERE id = ?");
//            $db->execute(array(
//                $this->badgesfinal,
//                $id
//            ));
//        }

        if (!$somethingClaimed) {
            echo "
                    <div class='alert alert-danger'>
                        You do not have any achievements to claim.
                    </div>
                ";
        } else {
            mysql_query("UPDATE grpgusers set badges_claimed = badges WHERE id = " . $user_class->id);
        }
        ?>
    </div>
</div>

