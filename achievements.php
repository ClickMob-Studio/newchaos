<?php
include 'header.php';
?>
<div class='box_top'>Achievements</div>
						<div class='box_middle'>
							<div class='pad'>
                                <?php
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
<div class="contenthead floaty">

<table id="newtables" style="width:100%;">

        <?php if ($user_class->badge != 0) { ?>
            <table width="100%" align="center">
                <tr>
                    <?php
                    echo (isset($user_class->badge1)) ? "<td align='center' style='width:100px;'>" . $user_class->badge1 . "</td>" : "";
                    echo (isset($user_class->badge2)) ? "<td align='center' style='width:100px;'>" . $user_class->badge2 . "</td>" : "";
                    echo (isset($user_class->badge4)) ? "<td align='center' style='width:100px;'>" . $user_class->badge4 . "</td>" : "";
                    echo (isset($user_class->badge5)) ? "<td align='center' style='width:100px;'>" . $user_class->badge5 . "</td>" : "";
                    echo (isset($user_class->badge6)) ? "<td align='center' style='width:100px;'>" . $user_class->badge6 . "</td>" : "";
                    echo (isset($user_class->badge7)) ? "<td align='center' style='width:100px;'>" . $user_class->badge7 . "</td>" : "";
                    echo (isset($user_class->badge8)) ? "<td align='center' style='width:100px;'>" . $user_class->badge8 . "</td>" : "";
                    echo (isset($user_class->badge9)) ? "<td align='center' style='width:100px;'>" . $user_class->badge9 . "</td>" : "";
                    ?>
                </tr>
            </table>
            <?php
        } else {
            echo "You haven't achieved any achievements yet.";
        }
genHead("Collectible Achievements");
$achs = array(
	'<h4>Levels</h4>' => 'levelbadges',
	'<h4>Crimes</h4>' => 'crimebadge',
	'<h4>Stats</h4>' => 'statbadge',
	'<h4>Kills</h4>' => 'battlebadge',
	'<h4>Bank</h4>' => 'bankbadge',
	'<h4>Mugs</h4>' => 'mugbadge',
	'<h4>Busts</h4>' => 'bustbadge',
	'<h4>Missions</h4>' => 'missionBadges'
);
foreach($achs as $head => $var){
	$cur = '';
	echo '<div class="table-container">';
	echo'<table class="new_table" id="newtables" style="width:100%;">';
		echo'<tr>';
			echo'<th colspan="999">' . $head . '</td>';

		echo'</tr>';
		echo'<tr>';
		$i = 1;
	foreach(array_reverse($$var) as $ach){
			echo'<td>';
                echo'<div class="ach' . $ach['img'] . '" title="' . $ach['title'] . '">
               <img src="css/images/'.$ach["img"].'.png" style="width:100px; height:100px;"> </img>
                </div><br />';
		switch($var){
			case 'levelbadges':
				echo'Get to level ' . prettynum($ach['needed']) . '.';
				break;
			case 'statbadge':
				echo'Gain ' . prettynum($ach['needed']) . ' in the gym.';
				break;
			case 'battlebadge':
				echo'Win ' . prettynum($ach['needed']) . ' fights.';
				break;
			case 'bankbadge':
				echo'Bank ' . prettynum($ach['needed'], 1) . '.';
				break;
			case 'mugbadge':
				echo'Gain ' . prettynum($ach['needed']) . ' mugs.';
				break;
			case 'bustbadge':
				echo'Gain ' . prettynum($ach['needed']) . ' busts.';
				break;
			case 'crimebadge':
				echo'Successfully complete ' . prettynum($ach['needed']) . ' crimes.';
				break;
            case 'missionBadges':
                echo'Successfully complete ' . prettynum($ach['needed']) . ' missions.';
                break;
		}
			echo'</td>';
		$cur .= '<th>' . prettynum($ach['payout']) . ' Points</th>';

		$i++;
	}
		echo'</tr>';
		echo'<tr>';
			echo $cur;
		echo'</tr></table></div>';
}
echo '</div>';
include 'footer.php';
?>
