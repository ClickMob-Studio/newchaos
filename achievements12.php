<?php
include 'header.php';
$levelbadges = array(
	'12' => array(
		'needed' => 1000,
		'payout' => 20000,
		'img' => 'lvl400',
		'title' => 'Testing Leveler: Get to level 1000'
	),
	'11' => array(
		'needed' => 900,
		'payout' => 20000,
		'img' => 'lvl400',
		'title' => 'Testing Leveler: Get to level 900'
	),
	'10' => array(
		'needed' => 800,
		'payout' => 20000,
		'img' => 'lvl400',
		'title' => 'Testing Leveler: Get to level 800'
	),
	'9' => array(
		'needed' => 700,
		'payout' => 20000,
		'img' => 'lvl400',
		'title' => 'Testing Leveler: Get to level 700'
	),
	'8' => array(
		'needed' => 600,
		'payout' => 20000,
		'img' => 'lvl400',
		'title' => 'Testing Leveler: Get to level 600'
	),
	'7' => array(
		'needed' => 500,
		'payout' => 20000,
		'img' => 'lvl400',
		'title' => 'Testing Leveler: Get to level 500'
	),


	'6' => array(
		'needed' => 400,
		'payout' => 20000,
		'img' => 'lvl400',
		'title' => 'Obsidian Leveler: Get to level 400'
	),
	'5' => array(
		'needed' => 300,
		'payout' => 10000,
		'img' => 'lvl300',
		'title' => 'Gold Leveler: Get to level 300'
	),
	'4' => array(
		'needed' => 200,
		'payout' => 4000,
		'img' => 'lvl200',
		'title' => 'Super Pro Leveler: Get to level 200'
	),
	'3' => array(
		'needed' => 100,
		'payout' => 2000,
		'img' => 'lvl100',
		'title' => 'Pro Leveler: Get to level 100'
	),
	'2' => array(
		'needed' => 50,
		'payout' => 1000,
		'img' => 'lvl50',
		'title' => 'Above Average Leveler: Get to level 50'
	),
	'1' => array(
		'needed' => 25,
		'payout' => 300,
		'img' => 'lvl25',
		'title' => 'Average Leveler: Get to level 25'
	)
);
$crimebadge = array(
	'12' => array(
		'needed' => 100000000,
		'payout' => 10000,
		'img' => 'crimes500k',
		'title' => 'Elite Criminal: Successfully complete 100,000,000 crimes'
	),
	'11' => array(
		'needed' => 50000000,
		'payout' => 10000,
		'img' => 'crimes500k',
		'title' => 'Elite Criminal: Successfully complete 50,000,000 crimes'
	),

	'10' => array(
		'needed' => 25000000,
		'payout' => 10000,
		'img' => 'crimes500k',
		'title' => 'Elite Criminal: Successfully complete 25,000,000 crimes'
	),

	'9' => array(
		'needed' => 10000000,
		'payout' => 10000,
		'img' => 'crimes500k',
		'title' => 'Elite Criminal: Successfully complete 10,000,000 crimes'
	),

	'8' => array(
		'needed' => 5000000,
		'payout' => 10000,
		'img' => 'crimes500k',
		'title' => 'Elite Criminal: Successfully complete 5,000,000 crimes'
	),

	'7' => array(
		'needed' => 2500000,
		'payout' => 10000,
		'img' => 'crimes500k',
		'title' => 'Elite Criminal: Successfully complete 2,500,000 crimes'
	),

	'6' => array(
		'needed' => 500000,
		'payout' => 10000,
		'img' => 'crimes500k',
		'title' => 'Elite Criminal: Successfully complete 500,000 crimes'
	),

	'5' => array(
		'needed' => 250000,
		'payout' => 5000,
		'img' => 'crimes250k',
		'title' => 'Elite Criminal: Successfully complete 250,000 crimes'
	),
	'4' => array(
		'needed' => 100000,
		'payout' => 2000,
		'img' => 'crimes100k',
		'title' => 'Elite Criminal: Successfully complete 100,000 crimes'
	),
	'3' => array(
		'needed' => 10000,
		'payout' => 500,
		'img' => 'crimes10k',
		'title' => 'Elite Criminal: Successfully complete 10,000 crimes'
	),
	'2' => array(
		'needed' => 5000,
		'payout' => 250,
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
	'12' => array(
		'needed' => 20000000000000,
		'payout' => 10000,
		'img' => 'stats2-5b',
		'title' => 'Good Workout: Successfully gain 20,000,000,000,000 in Stats'
	),
	'11' => array(
		'needed' => 5000000000000,
		'payout' => 10000,
		'img' => 'stats2-5b',
		'title' => 'Good Workout: Successfully gain 5,000,000,000,000 in Stats'
	),

	'10' => array(
		'needed' => 1000000000000,
		'payout' => 10000,
		'img' => 'stats2-5b',
		'title' => 'Good Workout: Successfully gain 1,000,000,000,000 in Stats'
	),

	'9' => array(
		'needed' => 500000000000,
		'payout' => 10000,
		'img' => 'stats2-5b',
		'title' => 'Good Workout: Successfully gain 500,000,000,000 in Stats'
	),

	'8' => array(
		'needed' => 100000000000,
		'payout' => 10000,
		'img' => 'stats2-5b',
		'title' => 'Good Workout: Successfully gain 100,000,000,000 in Stats'
	),

	'7' => array(
		'needed' => 25000000000,
		'payout' => 10000,
		'img' => 'stats2-5b',
		'title' => 'Good Workout: Successfully gain 25,000,000,000 in Stats'
	),
	'6' => array(
		'needed' => 2500000000,
		'payout' => 10000,
		'img' => 'stats2-5b',
		'title' => 'Good Workout: Successfully gain 2,500,000,000 in Stats'
	),

	'5' => array(
		'needed' => 1000000000,
		'payout' => 5000,
		'img' => 'stats1b',
		'title' => 'Good Workout: Successfully gain 1,000,000,000 in Stats'
	),
	'4' => array(
		'needed' => 100000000,
		'payout' => 2000,
		'img' => 'stats100m',
		'title' => 'Good Workout: Successfully gain 100,000,000 in Stats'
	),
	'3' => array(
		'needed' => 50000000,
		'payout' => 500,
		'img' => 'stats50m',
		'title' => 'Good Workout: Successfully gain 50,000,000 in Stats'
	),
	'2' => array(
		'needed' => 25000000,
		'payout' => 250,
		'img' => 'stats25m',
		'title' => 'Good Workout: Successfully gain 25,000,000 in Stats'
	),
	'1' => array(
		'needed' => 10000000,
		'payout' => 125,
		'img' => 'stats10m',
		'title' => 'Good Workout: Successfully gain 10,000,000 in Stats'
	)
);
$battlebadge = array(
	'12' => array(
		'needed' => 75000000,
		'payout' => 10000,
		'img' => 'kills75mill',
		'title' => 'Master Hitman: Win 75,000,000 kills'
	),

	'11' => array(
		'needed' => 25000000,
		'payout' => 10000,
		'img' => 'kills25mill',
		'title' => 'Master Hitman: Win 25,000,000 kills'
	),

	'10' => array(
		'needed' => 15000000,
		'payout' => 10000,
		'img' => 'kills15mill',
		'title' => 'Master Hitman: Win 15,000,000 kills'
	),

	'9' => array(
		'needed' => 5000000,
		'payout' => 10000,
		'img' => 'kills5mill',
		'title' => 'Master Hitman: Win 5,000,000 kills'
	),

	'8' => array(
		'needed' => 1000000,
		'payout' => 10000,
		'img' => 'kills1mill',
		'title' => 'Master Hitman: Win 1,000,000 kills'
	),

	'7' => array(
		'needed' => 750000,
		'payout' => 10000,
		'img' => 'kills750k',
		'title' => 'Master Hitman: Win 750,000 kills'
	),

	'6' => array(
		'needed' => 250000,
		'payout' => 10000,
		'img' => 'kills250k',
		'title' => 'Master Hitman: Win 250,000 kills'
	),


	'5' => array(
		'needed' => 50000,
		'payout' => 5000,
		'img' => 'kills50k',
		'title' => 'Master Hitman: Win 50,000 kills'
	),
	'4' => array(
		'needed' => 10000,
		'payout' => 2000,
		'img' => 'kills10k',
		'title' => 'Master Hitman: Win 10,000 kills'
	),
	'3' => array(
		'needed' => 1000,
		'payout' => 500,
		'img' => 'kills1000',
		'title' => 'Master Hitman: Win 1,000 kills'
	),
	'2' => array(
		'needed' => 250,
		'payout' => 250,
		'img' => 'kills250',
		'title' => 'Master Hitman: Win 250 kills'
	),
	'1' => array(
		'needed' => 100,
		'payout' => 125,
		'img' => 'kills100',
		'title' => 'Master Hitman: Win 100 kills'
	)
);
$bankbadge = array(
	'12' => array(
		'needed' => 5000000000000,
		'payout' => 7500,
		'img' => 'bank5trill',
		'title' => 'Pro Banker: Bank $5,000,000,000,000'
	),
	'11' => array(
		'needed' => 1000000000000,
		'payout' => 7500,
		'img' => 'bank1trill',
		'title' => 'Pro Banker: Bank $1,000,000,000,000'
	),

	'10' => array(
		'needed' => 75000000000,
		'payout' => 7500,
		'img' => 'bank75bill',
		'title' => 'Pro Banker: Bank $75,000,000,000'
	),

	'9' => array(
		'needed' => 50000000000,
		'payout' => 7500,
		'img' => 'bank50bill',
		'title' => 'Pro Banker: Bank $50,000,000,000'
	),

	'8' => array(
		'needed' => 25000000000,
		'payout' => 7500,
		'img' => 'bank25bill',
		'title' => 'Pro Banker: Bank $25,000,000,000'
	),

	'7' => array(
		'needed' => 10000000000,
		'payout' => 7500,
		'img' => 'bank10bill',
		'title' => 'Pro Banker: Bank $10,000,000,000'
	),


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
	'12' => array(
		'needed' => 100000,
		'payout' => 6500,
		'img' => 'mugs100k',
		'title' => 'Golden Mugger: Mugged 100,000 times'
	),
	'11' => array(
		'needed' => 100000,
		'payout' => 6500,
		'img' => 'mugs100k',
		'title' => 'Golden Mugger: Mugged 100,000 times'
	),

	'10' => array(
		'needed' => 100000,
		'payout' => 6500,
		'img' => 'mugs100k',
		'title' => 'Golden Mugger: Mugged 100,000 times'
	),

	'9' => array(
		'needed' => 100000,
		'payout' => 6500,
		'img' => 'mugs100k',
		'title' => 'Golden Mugger: Mugged 100,000 times'
	),

	'8' => array(
		'needed' => 100000,
		'payout' => 6500,
		'img' => 'mugs100k',
		'title' => 'Golden Mugger: Mugged 100,000 times'
	),

	'7' => array(
		'needed' => 100000,
		'payout' => 6500,
		'img' => 'mugs100k',
		'title' => 'Golden Mugger: Mugged 100,000 times'
	),

	'6' => array(
		'needed' => 100000,
		'payout' => 6500,
		'img' => 'mugs100k',
		'title' => 'Golden Mugger: Mugged 100,000 times'
	),

	'5' => array(
		'needed' => 50000,
		'payout' => 3000,
		'img' => 'mugs50k',
		'title' => 'Golden Mugger: Mugged 50,000 times'
	),
	'4' => array(
		'needed' => 20000,
		'payout' => 1000,
		'img' => 'mugs20k',
		'title' => 'Golden Mugger: Mugged 20,000 times'
	),
	'3' => array(
		'needed' => 5000,
		'payout' => 500,
		'img' => 'mugs5k',
		'title' => 'Obsidian Mugger: Mugged 5,000 times'
	),
	'2' => array(
		'needed' => 1000,
		'payout' => 250,
		'img' => 'mugs1k',
		'title' => 'Great Mugger: Mugged 1,000 times'
	),
	'1' => array(
		'needed' => 250,
		'payout' => 125,
		'img' => 'mugs250',
		'title' => 'Good Mugger: Mugged 250 times'
	)
);
$bustbadge = array(
	'12' => array(
		'needed' => 100000,
		'payout' => 7000,
		'img' => 'busts100k',
		'title' => 'Golden Buster: Bust 100,000 Crazies'
	),
	'11' => array(
		'needed' => 100000,
		'payout' => 7000,
		'img' => 'busts100k',
		'title' => 'Golden Buster: Bust 100,000 Crazies'
	),

	'10' => array(
		'needed' => 100000,
		'payout' => 7000,
		'img' => 'busts100k',
		'title' => 'Golden Buster: Bust 100,000 Crazies'
	),

	'9' => array(
		'needed' => 100000,
		'payout' => 7000,
		'img' => 'busts100k',
		'title' => 'Golden Buster: Bust 100,000 Crazies'
	),

	'8' => array(
		'needed' => 100000,
		'payout' => 7000,
		'img' => 'busts100k',
		'title' => 'Golden Buster: Bust 100,000 Crazies'
	),

	'7' => array(
		'needed' => 100000,
		'payout' => 7000,
		'img' => 'busts100k',
		'title' => 'Golden Buster: Bust 100,000 Crazies'
	),

	'6' => array(
		'needed' => 100000,
		'payout' => 7000,
		'img' => 'busts100k',
		'title' => 'Golden Buster: Bust 100,000 Crazies'
	),

	'5' => array(
		'needed' => 50000,
		'payout' => 3000,
		'img' => 'busts20k',
		'title' => 'Golden Buster: Bust 50,000 Crazies'
	),
	'4' => array(
		'needed' => 20000,
		'payout' => 1000,
		'img' => 'busts20k',
		'title' => 'Golden Buster: Bust 20,000 Crazies'
	),
	'3' => array(
		'needed' => 5000,
		'payout' => 500,
		'img' => 'busts5k',
		'title' => 'Obsidian Buster: Bust 5,000 Crazies'
	),
	'2' => array(
		'needed' => 1000,
		'payout' => 250,
		'img' => 'busts1k',
		'title' => 'Great Buster: Bust 1,000 Crazies'
	),
	'1' => array(
		'needed' => 250,
		'payout' => 125,
		'img' => 'busts250',
		'title' => 'Good Buster: Bust 250 Crazies'
	)
);
?>
<tr>
	<td class="contentspacer"></td>
</tr>
<tr>
	<td>
		<center><b><i><u>
						<font color=Blue>TheMafiaLife Achievements</font>
					</u></i></b></center><br>
	</td>
</tr>
<tr>
	<td class="contentcontent">
		Achievements are granted for doing well or doing hilariously bad in certain parts of the game. You don't have to
		go anywhere to be granted these badges, just simply get to the target and you will be awarded with it
		automatically.
	</td>
</tr>
<tr>
	<td class="contentspacer"></td>
</tr>
<tr>
	<td class="contenthead"></br> </br>
		<center><b><i><u>
						<font color=cyan>Your Achievements</font>
					</u></i></b></center>
	</td>
	</td>
<tr>
	<td class="contentcontent">
		<?php if ($user_class->badge != 0) { ?>
			<table width="100%" align="center">
				<tr>
					<?php
					echo (isset($user_class->badge1)) ? "<td align='center'>" . $user_class->badge1 . "</td>" : "";
					echo (isset($user_class->badge2)) ? "<td align='center'>" . $user_class->badge2 . "</td>" : "";
					echo (isset($user_class->badge4)) ? "<td align='center'>" . $user_class->badge4 . "</td>" : "";
					echo (isset($user_class->badge5)) ? "<td align='center'>" . $user_class->badge5 . "</td>" : "";
					echo (isset($user_class->badge6)) ? "<td align='center'>" . $user_class->badge6 . "</td>" : "";
					echo (isset($user_class->badge7)) ? "<td align='center'>" . $user_class->badge7 . "</td>" : "";
					echo (isset($user_class->badge8)) ? "<td align='center'>" . $user_class->badge8 . "</td>" : "";
					?>
				</tr>
			</table>
			<?php
		} else {
			echo "You haven't achieved any achievements yet.";
		}
		genHead("Collectible Achievements");
		$achs = array(
			'Levels' => 'levelbadges',
			'Crimes' => 'crimebadge',
			'Stats' => 'statbadge',
			'Kills' => 'battlebadge',
			'Bank' => 'bankbadge',
			'Mugs' => 'mugbadge',
			'Busts' => 'bustbadge'
		);
		foreach ($achs as $head => $var) {
			$cur = '';
			echo '<table id="newtables" style="width:100%;table-layout:fixed;">';
			echo '<tr>';
			echo '<th colspan="6">' . $head . '</td>';

			echo '</tr>';
			echo '<tr>';
			$counter_custom = 0;
			foreach (array_reverse($$var) as $ach) {
				if ($counter_custom % 6 == 0) {
					echo '</tr><tr>';
				}
				$counter_custom++;
				echo '<td><table><tr><td>';
				echo '<div class="ach' . $ach['img'] . '" title="' . $ach['title'] . '">
               <img src="/css/images/' . $ach["img"] . '.png"> </img>
                </div><br />';
				switch ($var) {
					case 'levelbadges':
						echo 'Get to level ' . prettynum($ach['needed']) . '.';
						break;
					case 'statbadge':
						echo 'Gain ' . prettynum($ach['needed']) . ' in the gym.';
						break;
					case 'battlebadge':
						echo 'Win ' . prettynum($ach['needed']) . ' fights.';
						break;
					case 'bankbadge':
						echo 'Bank ' . prettynum($ach['needed'], 1) . '.';
						break;
					case 'mugbadge':
						echo 'Gain ' . prettynum($ach['needed']) . ' mugs.';
						break;
					case 'bustbadge':
						echo 'Gain ' . prettynum($ach['needed']) . ' busts.';
						break;
					case 'crimebadge':
						echo 'Successfully complete ' . prettynum($ach['needed']) . ' crimes.';
						break;
				}
				echo '</td></tr><tr><th>';
				echo prettynum($ach['payout']) . ' Points';
				echo '</th></tr></table>';
				echo '</td>';
				//$cur .= '<th>' . prettynum($ach['payout']) . ' Points</th>';
			}
			echo '</tr>';
			echo '<tr>';
			echo $cur;
			echo '</tr></table>';
		}
		echo '</div>';
		include 'footer.php';
		?>