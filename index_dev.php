<?php
include 'header.php';
if ($user_class->firstlogin1 == 0) {
    $db->query("UPDATE grpgusers SET firstlogin1 = 1 WHERE id = ?");
    $db->execute(array(
        $user_class->id
    ));
    Send_Event2($user_class->id, "Is the latest thug on the streets.", $user_class->id);
    Send_Event1($user_class->id, "Is the latest thug on the streets.", $user_class->id);
    Send_Event($user_class->id, "<span style='color:white;'>Welcome To TrueMMO!<br>To get you started we are giving you:</span><br><span style='color:white;font-weight:bold;'>&bull;&nbsp;3 RM Days<br>&bull;&nbsp;$100,000 Cash<br>&bull;&nbsp;1,000 Points</span><br><span style='color:white;'>Before you get started please read the <b><a href='gamerules.php'>Game Rules</a></b></span>", $user_class->id);
}
echo '<div class="contenthead"><span style="margin: 0;width: 655px;height: 54px;line-height: 43px;text-transform: uppercase;color: orange;font-family: "HackingTrashed-Regular",sans-serif;font-size: 25px;text-align: left;text-indent: 25px;text-shadow: 1px 0 1px #343434;">General Information</span>';
	echo '<table id="newtables" style="width:100%;">';
		echo '<tr>';
			echo '<th width="10%">Name:</td>';
			echo '<td width="30%">';
				echo '<a href="profiles.php?id=' . $user_class->id . '">' . $user_class->formattedname . '</a>';
			echo '</td>';
			echo '<th width="10%">HP:</td>';
			echo '<td width="30%">' . prettynum($user_class->formattedhp) . '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<th width="10%">Level:</td>';
			echo '<td width="30%">' . $user_class->level . (($user_class->level >= 500) ? ' <a href="prestige.php"><span class="notify">[Prestige]</span></a>' : '') . '</td>';
			echo '<th width="10%">Energy:</td>';
			echo '<td width="30%">' . prettynum($user_class->formattedenergy) . '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<th width="10%">Money:</td>';
			echo '<td width="30%">$' . prettynum($user_class->money) . '</td>';
			echo '<th width="10%">Awake:</td>';
			echo '<td width="30%">' . prettynum($user_class->formattedawake) . '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<th width="10%">Bank:</td>';
			echo '<td width="30%">$' . prettynum($user_class->bank) . '</td>';
			echo '<th width="10%">Nerve:</td>';
			echo '<td width="30%">' . prettynum($user_class->formattednerve) . '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<th width="10%">EXP:</td>';
			echo '<td width="30%">' . prettynum($user_class->formattedexp) . '</td>';
			echo '<th width="10%">Work EXP:</td>';
			echo '<td width="30%">' . prettynum($user_class->workexp) . '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<th width="10%">RM Days:</td>';
			echo '<td width="30%">' . prettynum($user_class->rmdays) . '</td>';
			echo '<th width="10%">Activity Points:</td>';
			echo '<td width="30%"><a href="spendactivity.php">' . prettynum($user_class->apoints) . '</td>';
		echo '</tr>';
	echo '</table>';
echo '</div>';
echo '<div class="contenthead"><span style="margin: 0;width: 655px;height: 54px;line-height: 43px;text-transform: uppercase;color: orange;font-family: "HackingTrashed-Regular",sans-serif;font-size: 25px;text-align: left;text-indent: 25px;text-shadow: 1px 0 1px #343434;">Stat Information</span>';
	echo '<table id="newtables" style="width:100%;">';
		echo '<tr>';
			echo '<th width="15%">Strength:</td>';
			echo '<td>' . prettynum($user_class->strength) . '</td>';
			echo '<td>[Ranked: ' . getRank("$user_class->id", "strength") . ']</td>';
			echo '<th width="15%">Defense:</td>';
			echo '<td>' . prettynum($user_class->defense) . '</td>';
			echo '<td>[Ranked: ' . getRank("$user_class->id", "defense") . ']</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<th width="15%">Speed:</td>';
			echo '<td>' . prettynum($user_class->speed) . '</td>';
			echo '<td>[Ranked: ' . getRank("$user_class->id", "speed") . ']</td>';
			echo '<th width="15%">Total:</td>';
			echo '<td>' . prettynum($user_class->totalattrib) . '</td>';
			echo '<td>[Ranked: ' . getRank("$user_class->id", "total") . ']</td>';
		echo '</tr>';
	echo '</table>';
echo '</div>';
echo '<div class="contenthead"><span style="margin: 0;width: 655px;height: 54px;line-height: 43px;text-transform: uppercase;color: orange;font-family: "HackingTrashed-Regular",sans-serif;font-size: 25px;text-align: left;text-indent: 25px;text-shadow: 1px 0 1px #343434;">Modded Stat Information</span>';
	echo '<table id="newtables" style="width:100%;">';
		echo '<tr>';
			echo '<th width="15%">Modded Strength:</td>';
			echo '<td width="25%">' . prettynum($user_class->moddedstrength) . '</td>';
			echo '<th width="15%">Modded Defense:</td>';
			echo '<td width="25%">' . prettynum($user_class->moddeddefense) . '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<th width="15%">Modded Speed:</td>';
			echo '<td width="25%">' . prettynum($user_class->moddedspeed) . '</td>';
			echo '<th width="15%">Modded Total:</td>';
			echo '<td width="25%">' . prettynum($user_class->moddedtotalattrib) . '</td>';
		echo '</tr>';
	echo '</table>';
echo '</div>';
echo '<div class="contenthead"><span style="margin: 0;width: 655px;height: 54px;line-height: 43px;text-transform: uppercase;color: orange;font-family: "HackingTrashed-Regular",sans-serif;font-size: 25px;text-align: left;text-indent: 25px;text-shadow: 1px 0 1px #343434;">Battle Statistics</span>';
	echo '<table id="newtables" style="width:100%;">';
		echo '<tr>';
			echo '<th width="10%">Won:</td>';
			echo '<td width="30%">' . prettynum($user_class->battlewon) . '</td>';
			echo '<th width="10%">Lost:</td>';
			echo '<td width="30%">' . prettynum($user_class->battlelost) . '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<th width="10%">Total:</td>';
			echo '<td width="30%">' . prettynum($user_class->battletotal) . '</td>';
			echo '<th width="10%">Money Gain:</td>';
			echo '<td width="30%">$' . prettynum($user_class->battlemoney) . '</td>';
		echo '</tr>';
	echo '</table>';
echo '</div>';
echo '<div class="contenthead"><span style="margin: 0;width: 655px;height: 54px;line-height: 43px;text-transform: uppercase;color: orange;font-family: "HackingTrashed-Regular",sans-serif;font-size: 25px;text-align: left;text-indent: 25px;text-shadow: 1px 0 1px #343434;">Crime Statistics</span>';
	echo '<table id="newtables" style="width:100%;">';
		echo '<tr>';
			echo '<th width="10%">Succeeded:</td>';
			echo '<td width="30%">' . prettynum($user_class->crimesucceeded) . '</td>';
			echo '<th width="10%">Failed:</td>';
			echo '<td width="30%">' . prettynum($user_class->crimefailed) . '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<th width="10%">Total:</td>';
			echo '<td width="30%">' . prettynum($user_class->crimetotal) . '</td>';
			echo '<th width="10%">Money Gain:</td>';
			echo '<td width="30%">$' . prettynum($user_class->crimemoney) . '</td>';
		echo '</tr>';
	echo '</table>';
echo '</div>';

echo '<div class="contenthead"><span style="margin: 0;width: 655px;height: 54px;line-height: 43px;text-transform: uppercase;color: orange;font-family: "HackingTrashed-Regular",sans-serif;font-size: 25px;text-align: left;text-indent: 25px;text-shadow: 1px 0 1px #343434;">Bonus Statistics</span>';


echo '<table id="newtables" style="width:100%;">';
		echo '<tr>';
			echo '<th width="10%">Total Tax Paid:</td>';
			echo '<td width="30%">$' . prettynum($user_class->totaltax) . '</td>';
			echo '<th width="10%">???:</td>';
			echo '<td width="30%">' . prettynum($user_class->crimefailed) . '</td>';
		echo '</tr>';

	echo '</table>';




echo '</div>';







echo '<div class="contenthead"><span style="margin: 0;width: 655px;height: 54px;line-height: 43px;text-transform: uppercase;color: orange;font-family: "HackingTrashed-Regular",sans-serif;font-size: 25px;text-align: left;text-indent: 25px;text-shadow: 1px 0 1px #343434;">EXP Calculator</span>';
	echo '<div class="floaty">';
		echo '<div class="flexcont">';
			echo '<div class="flexele" style="border-right:thin solid #333;">';
				echo 'What level are you aiming for? <input type="text" oninput="calcEXP();" id="levelcalc" size="8" />';
			echo '</div>';
			echo '<div class="flexele">';
				echo '<span id="levelrtn">';
					echo 'You need ' . prettynum(experience($user_class->level + 1) - $user_class->exp);
					echo ' EXP to get to level ' . prettynum($user_class->level + 1) . '.';
				echo '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';
echo '<center>';
	echo '<div class="flexcont" style="margin:0 auto;">';
		echo '<div class="flexele"></div>';
		echo '<a href="achievements.php"><div class="flexele floatylinks">[Achievements]</div></a> ';
		echo '<a href="translog.php"><div class="flexele floatylinks">[Transfer Logs]</div></a> ';
		echo '<a href="attacklog.php"><div class="flexele floatylinks">[Attack Log]</div></a> ';
		echo '<a href="defenselog.php"><div class="flexele floatylinks">[Defense Log]</div></a> ';
		echo '<a href="muglog.php"><div class="flexele floatylinks">[Mug Log]</div></a>';
		echo '<a href="spylog.php"><div class="flexele floatylinks">[Spy Log]</div></a>';

		echo '<div class="flexele"></div>';
	echo '</div>';
	echo '<br />';
	echo '<br />';
	echo '<br />';
   echo '</center>';
include "footer_dev.php";
?>