<?php

include 'header.php';

$result1 = $mysql->query("SELECT * FROM `family_list` WHERE `userid` = '".$user_class->id."' OR `Soldier` = '".$user_class->id."' OR `Boss` = '".$user_class->id."' OR `Capo` = '".$user_class->id."' OR `Consigliere` = '".$user_class->id."' LIMIT 1");
if (mysql_num_rows($result1) > 0) {
    $worked1 = mysql_fetch_array($result1);
    if ($worked1['userid'] != $user_class->id) {
        echo Message("Only your <b>Main Gangsta</b> can use the Casino.<br /><br /><a href='city.php'>Back</a>");
        include 'footer.php';
        die();
    }
}

if ($_POST['spinroulette'] == "Gamble") {
    $allowedtoplay = true;
    $errormessage = "";
    $totalplay = 0;
    $i = 0;
    while ($i++ < 37) {
       if (($_POST[$i] != "") && ($_POST[$i] > 10000)) {
           $allowedtoplay = false;
           $errormessage = "You are not allowed to bet more than $10,000 on a number<br>";
           continue;
       }
       $_POST[$i] = check_number($_POST[$i]);
    }
    $_POST['z'] = check_number($_POST['z']);
    $_POST['e'] = check_number($_POST['e']);
    $_POST['o'] = check_number($_POST['o']);
    $_POST['b'] = check_number($_POST['b']);
    $_POST['r'] = check_number($_POST['r']);
    $_POST['ek'] = check_number($_POST['ek']);
    $_POST['tk'] = check_number($_POST['tk']);
    $_POST['dk'] = check_number($_POST['dk']);
    $_POST['ett'] = check_number($_POST['ett']);
    $_POST['dtv'] = check_number($_POST['dtv']);
    $_POST['vtz'] = check_number($_POST['vtz']);
    $_POST['eta'] = check_number($_POST['eta']);
    $_POST['ntz'] = check_number($_POST['ntz']);
    if (($_POST['z'] != "") && ($_POST['z'] > 10000) && $allowedtoplay) {
           $allowedtoplay = false;
           $errormessage = "You are not allowed to bet more than $10,000 on a number<br>";
    }

    $totalplay += $_POST['1'] + $_POST['2'] + $_POST['3'] + $_POST['4'] + $_POST['5'] + $_POST['6'] + $_POST['7'] + $_POST['8'] + $_POST['9'] + $_POST['10'] + $_POST['11'] + $_POST['12'] + $_POST['13'] + $_POST['14'] + $_POST['15'] + $_POST['16'] + $_POST['17'] + $_POST['18'] + $_POST['19'] + $_POST['20'] + $_POST['21'] + $_POST['22'] + $_POST['23'] + $_POST['24'] + $_POST['25'] + $_POST['26'] + $_POST['27'] + $_POST['28'] + $_POST['29'] + $_POST['30'] + $_POST['31'] + $_POST['32'] + $_POST['33'] + $_POST['34'] + $_POST['35'] + $_POST['36'] + $_POST['z'] + $_POST['ek'] + $_POST['tk'] + $_POST['dk'] + $_POST['ett'] + $_POST['dtv'] + $_POST['vtz'] + $_POST['eta'] + $_POST['ntz'] + $_POST['e'] + $_POST['o'] + $_POST['b'] + $_POST['r'];

    if (($totalplay > 100000) || ($totalplay < 0)) {
           $allowedtoplay = false;
           $errormessage .= "Maximum bet is $100,000";
    }

    if ($user_class->money < $totalplay) {
           $allowedtoplay = false;
           $errormessage .= "You don't have thatmuch cash on you. Lower your bet.";
    }

    if ($allowedtoplay) {
        $resultpay = $mysql->query("UPDATE `grpgusers` SET money=money-".$totalplay." WHERE `id` ='".$user_class->id."' AND `money` >= '".$totalplay."' LIMIT 1");
        if (mysql_affected_rows() > 0) {
            $user_class->money -= $totalplay;
           $black = array(2, 4, 6, 8, 10, 11, 13, 15, 17, 20, 22, 24, 26, 28, 29, 31, 33, 35);
           $red = array(1, 3, 5, 7, 9, 12, 14, 16, 18, 19, 21, 23, 25, 27, 30, 32, 34, 36);
           $first1 = array(1, 4, 7, 10, 13, 16, 19, 22, 25, 28, 31, 34);
           $second2 = array(2, 5, 8, 11, 14, 17, 20, 23, 26, 29, 32, 35);
           $third3 = array(3, 6, 9, 12, 15, 18, 21, 24, 27, 30, 33, 36);
           $number_result = rand(0, 36);
           $youwon = 0;
           if (($_POST[$number_result] != "") && ($_POST[$number_result] > 0)) { $youwon += $_POST[$number_result] * 36; }
              elseif (($_POST['z'] != "0") && ($number_result == "0") && ($_POST['z'] > 0)) { $youwon += $_POST['z'] * 36; }

           if (in_array($number_result, $first1) && ($_POST['ek'] != "") && ($_POST['ek'] > 0)) { $youwon += $_POST['ek'] * 3; }
           if (in_array($number_result, $second2) && ($_POST['tk'] != "") && ($_POST['tk'] > 0)) { $youwon += $_POST['tk'] * 3; }
           if (in_array($number_result, $third3) && ($_POST['dk'] != "") && ($_POST['dk'] > 0)) { $youwon += $_POST['dk'] * 3; }

           if (($number_result >=1) && ($number_result <= 12) && ($_POST['ett'] != "")) { $youwon += $_POST['ett'] * 3; }
           if (($number_result >=13) && ($number_result <= 24) && ($_POST['dtv'] != "")) { $youwon += $_POST['dtv'] * 3; }
           if (($number_result >=25) && ($number_result <= 36) && ($_POST['vtz'] != "")) { $youwon += $_POST['vtz'] * 3; }

           if (($number_result >=1) && ($number_result <= 18) && ($_POST['eta'] != "")) { $youwon += $_POST['eta'] * 2; }
           if (($number_result >=19) && ($number_result <= 36) && ($_POST['ntz'] != "")) { $youwon += $_POST['ntz'] * 2; }

           if ((($number_result % 2) == 0) && ($_POST['e'] != "")) { $youwon += $_POST['e'] * 2; }
              elseif ((($number_result % 2) != 0) && ($_POST['o'] != "")) { $youwon += $_POST['o'] * 2; }

           if (in_array($number_result, $black) && ($_POST['b'] != "")) { $youwon += $_POST['b'] * 2; }
              elseif (in_array($number_result, $red) && ($_POST['r'] != "")) { $youwon += $_POST['r'] * 2; }

           $rmoney = $user_class->money + $youwon;
           $resultwon = $mysql->query("UPDATE `grpgusers` SET money=money+".$youwon." WHERE `id`='".$user_class->id."'");
           $user_class->money += $youwon;
           $resultlog = $mysql->query("INSERT INTO `log_roulette` VALUES ('', '".$user_class->id."', '".$user_class->money."','".$totalplay."', '".time()."', '".$rmoney."')");
        }
     }
}

echo '<div class="contenthead">European Roulette</div><!--contenthead-->';
echo '<div class="contentcontent">';
?>

<form method="post" action="roulette.php">
<center>
<table border="1" bordercolor="#71512A" cellpadding="2" cellspacing="0">
<tbody><tr>
<td colspan="2" class="tableheader" align="center">
<?php
if ($allowedtoplay) {
?>
<img border="0" src="images_roulette/<?php echo $number_result; ?>.gif">
<?php

}
?>
</td>
</tr>
<tr>
<td colspan="2" bgcolor="black" height="1" align="center"><font color="#FFFF00"><b>
<?php
if (($allowedtoplay) && ($youwon >= 0)) { echo "You bet $". number_format($totalplay) ." and you won: $" . number_format($youwon); }
   else { echo $errormessage; }
?>
</b></font></td>

</tr>
<tr>
<td colspan="2" align="center">
Maximum Bet: <b>$100,000</b> | Maximum Bet on numbers: <b>$10,000</b>
</td>
</tr>
<tr>
<td valign="top" width="260" align="center">
<img border="0" src="images/mk_roulette_black_bg.gif" width="277" height="551"></td>
<td align="center">

<table width="95%" border="0" bordercolor="black" cellpadding="2" cellspacing="0">
<tbody><tr>
	<td colspan="3"></td>
	<td colspan="2">0:<font color="#333333"> </font><input size="5" name="z" type="text"></td>
</tr>
<tr>
	<td rowspan="2">1-18:<br><input size="5" name="eta" type="text"></td>
	<td rowspan="4">1-12:<br><input size="5" name="ett" type="text"></td>

	<td>1:<font color="#333333"> </font><input size="5" name="1" type="text"></td>
	<td>2:<font color="#333333"> </font><input size="5" name="2" type="text"></td>
	<td>3:<font color="#333333"> </font><input size="5" name="3" type="text"></td>
</tr>
<tr>
	<td>4:<font color="#333333"> </font><input size="5" name="4" type="text"></td>
	<td>5:<font color="#333333"> </font><input size="5" name="5" type="text"></td>

	<td>6:<font color="#333333"> </font><input size="5" name="6" type="text"></td>
</tr>
<tr>
	<td rowspan="2">Even:<br><input size="5" name="e" type="text"></td>
	<td>7:<font color="#333333"> </font><input size="5" name="7" type="text"></td>
	<td>8:<font color="#333333"> </font><input size="5" name="8" type="text"></td>
	<td>9:<font color="#333333"> </font><input size="5" name="9" type="text"></td>

</tr>
<tr>
	<td>10:<input size="5" name="10" type="text"></td>
	<td>11:<input size="5" name="11" type="text"></td>
	<td>12:<input size="5" name="12" type="text"></td>
</tr>
<tr>
	<td rowspan="2">Black:<br><input size="5" name="b" type="text"></td>
	<td rowspan="4">13-24:<br><input size="5" name="dtv" type="text"></td>

	<td>13:<input size="5" name="13" type="text"></td>
	<td>14:<input size="5" name="14" type="text"></td>
	<td>15:<input size="5" name="15" type="text"></td>
</tr>
<tr>
	<td>16:<input size="5" name="16" type="text"></td>
	<td>17:<input size="5" name="17" type="text"></td>

	<td>18:<input size="5" name="18" type="text"></td>
</tr>
<tr>
	<td rowspan="2">Red:<br><input size="5" name="r" type="text"></td>
	<td>19:<input size="5" name="19" type="text"></td>
	<td>20:<input size="5" name="20" type="text"></td>
	<td>21:<input size="5" name="21" type="text"></td>

</tr>
<tr>
	<td>22:<input size="5" name="22" type="text"></td>
	<td>23:<input size="5" name="23" type="text"></td>
	<td>24:<input size="5" name="24" type="text"></td>
</tr>
<tr>
	<td rowspan="2">Odd:<br><input size="5" name="o" type="text"></td>
	<td rowspan="4">25-36:<br><input size="5" name="vtz" type="text"></td>

	<td>25:<input size="5" name="25" type="text"></td>
	<td>26:<input size="5" name="26" type="text"></td>
	<td>27:<input size="5" name="27" type="text"></td>
</tr>
<tr>
	<td>28:<input size="5" name="28" type="text"></td>
	<td>29:<input size="5" name="29" type="text"></td>

	<td>30:<input size="5" name="30" type="text"></td>
</tr>
<tr>
	<td rowspan="2">19-36:<br><input size="5" name="ntz" type="text"></td>
	<td>31:<input size="5" name="31" type="text"></td>
	<td>32:<input size="5" name="32" type="text"></td>
	<td>33:<input size="5" name="33" type="text"></td>

</tr>
<tr>
	<td>34:<input size="5" name="34" type="text"></td>
	<td>35:<input size="5" name="35" type="text"></td>
	<td>36:<input size="5" name="36" type="text"></td>
</tr>
<tr>
	<td colspan="2"></td>
	<td align="center">1st :</td>

	<td align="center">2nd :</td>
	<td align="center">3rd :</td>
</tr>
<tr>
	<td colspan="2"></td>
	<td><font color="#000000">00:</font><input size="5" name="ek" type="text"></td>
	<td><font color="#000000">00:</font><input size="5" name="tk" type="text"></td>
	<td><font color="#000000">00:</font><input size="5" name="dk" type="text"></td>

</tr>
<tr>
		<td colspan="5" align="center"><input name="spinroulette" value="Gamble" type="submit">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input value="" type="reset"></td>
		</tr>
</tbody></table>


</td>
</tr>
</tbody></table>

</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->


<div class="contenthead">Roulette Rules</div><!--contenthead-->
<div class="contentcontent">
<table border="0" width="100%" cellspacing="0" cellpadding="0" id="table1">
	<tr>
		<td width="15%"><b>Bet Type</b></td>
		<td width="75%"><b>Description</b></td>
		<td width="10%"><p align="center"><b>Payout</b></td>
	</tr>
	<tr>
		<td>Straight Up</td>
		<td>A bet directly on a single number</td>
		<td align="center">35 to 1</td>
	</tr>
	<tr>
		<td>Column Bet</td>
		<td>A bet covering 12 numbers, just under a third of the table</td>
		<td align="center">2 to 1</td>
	</tr>
	<tr>
		<td>Dozen Bet</td>
		<td>A bet covering a set of 12 numbers: low (1-12), mid (13-24) and high
		(25-36)</td>
		<td align="center">2 to 1</td>
	</tr>
	<tr>
		<td>Color Bet</td>
		<td>A bet on either Red or Black</td>
		<td align="center">1 to 1</td>
	</tr>
	<tr>
		<td>Even/Odd Bet</td>
		<td>A bet on either Even or Odd</td>
		<td align="center">1 to 1</td>
	</tr>
	<tr>
		<td>Low/High Bet</td>
		<td>A bet on half the numbers either 1-18 or 19-36</td>
		<td align="center">1 to 1</td>
	</tr>
	</table>
</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->

<?php

include 'footer.php';
?>