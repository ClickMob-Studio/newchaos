<?php
include 'ajax_header.php';
echo '
<style>
*{
	background:#333;
	color:#fff;
	text-decoration: none;
}
</style>
';
$db->query("SELECT * FROM pms WHERE `to` IN (21, 43) AND `from` IN (21, 43)");
$db->execute();
$rows = $db->fetch_row();
foreach ($rows as $r) {
	echo '<hr />';
	echo formatName($r['from']);
	echo '<br /><br />';
	echo BBCodeParse(strip_tags($r['msgtext']));
	echo '<hr />';

}
/*
$db->query("SELECT ip, id FROM grpgusers");
$db->execute();
$rows = $db->fetch_row();
foreach($rows as $r){
	print formatName($r['id']) . " " . gethostbyaddr($r['ip']) . " <br /> ";
}
/*
$rtn = '';
for($i = 405; $i <= 425; $i++){
	$rtn .= '[tag]' . $i . '[/tag] ';
}
print $rtn;
/*
$colors = array(
 'ff0000','ff1900','ff3300'
,'ff4c00','ff6600','ff7f00'
,'ff9900','ffb200','ffcc00'
,'ffe500','ffff00','bfff00'
,'80ff00','40ff00','00ff00'
,'00ff33','00ff66','00ff99'
,'00ffcc','00ffff','00ccff'
,'0099ff','0066ff','0033ff'
,'0000ff','1c00ff','3800ff'
,'5300ff','6f00ff','8b00ff'
,'8b00ff','8b00ff','8b00ff'
,'8b00ff','6f00ff','5300ff'
,'3800ff','1c00ff','0000ff'
,'0033ff','0066ff','0099ff'
,'00ccff','00ffff','00ffcc'
,'00ff99','00ff66','00ff33'
,'00ff00','40ff00','80ff00'
,'bfff00','ffff00','ffe500'
,'ffcc00','ffb200','ff9900'
,'ff7f00','ff6600','ff4c00'
,'ff3300','ff1900','ff0000');

$colors = array(
'ff0000','ff0d00','ff1900',
'ff2600','ff3300','ff4000',
'ff4c00','ff5900','ff6600',
'ff7200','ff7f00','ff8b00',
'ff9600','ffa200','ffae00',
'ffb900','ffc500','ffd000',
'ffdc00','ffe800','fff300',
'ffff00','e6ff00','ccff00',
'b3ff00','99ff00','80ff00',
'66ff00','4dff00','33ff00',
'1aff00','00ff00','00ff1a',
'00ff33','00ff4d','00ff66',
'00ff80','00ff99','00ffb3',
'00ffcc','00ffe6','00ffff',
'00e8ff','00d1ff','00b9ff',
'00a2ff','008bff','0074ff',
'005dff','0046ff','002eff',
'0017ff','0000ff','0e00ff',
'1c00ff','2a00ff','3800ff',
'4600ff','5300ff','6100ff',
'6f00ff','7d00ff','8b00ff'
);
$db->query("UPDATE grpgusers SET uninfo = ?, username = ? WHERE id = ?");
for($i = 405, $j = 0; $i <= 425; $i++, $j += 3){
	$input = '1|000000|700|yes|3~' . $colors[$j] . ',' . $colors[$j + 1] . ',' . $colors[$j + 2] . '|0|3';
	$num = $i - 404;
	$name = 'RoBoT ' . $num;
	$db->execute(array(
		$input,
		$name,
		$i
	));
}

//$un = 'TeddYBeaR';

$rand = rand(72421, 72421);
for($i = 1; $i < $rand; $i++)
	Send_Event(329, $user_class->formattedname . " has just bitch slapped you on the back of the head!", 9);
print $rand;
/*
print nameGen_mike($user_class->gndays, $user_class->donatordays, $user_class->uninfo, $user_class->username);

/*
echo'<span style="font-weight:700;font-style:italic;font-size:24px;">';
for($i = 0; $i < strlen($un); $i++){
	echo'<span style="color:' . $fill[$i] . ';text-shadow: 0 0 17px ' . $glow[$i] . ';">' . $un[$i] . '</span>';
}
echo'</span><br />';
echo'<span style="font-weight:700;font-style:italic;">';
for($i = 0; $i < strlen($un); $i++){
	echo'<span style="color:' . $fill[$i] . ';text-shadow: 0 0 2px ' . $glow[$i] . ';">' . $un[$i] . '</span>';
}
echo'</span><br />';*/
/*
function gradient($from_color, $to_color, $graduations = 10) {
	$graduations--;
	$startcol = str_replace("#", "", $from_color);
	$endcol = str_replace("#", "", $to_color);
	$RedOrigin = hexdec(substr($startcol, 0, 2));
	$GrnOrigin = hexdec(substr($startcol, 2, 2));
	$BluOrigin = hexdec(substr($startcol, 4, 2));
	if ($graduations >= 2) { // for at least 3 colors
		$GradientSizeRed = (hexdec(substr($endcol, 0, 2)) - $RedOrigin) / $graduations; //Graduation Size Red
		$GradientSizeGrn = (hexdec(substr($endcol, 2, 2)) - $GrnOrigin) / $graduations;
		$GradientSizeBlu = (hexdec(substr($endcol, 4, 2)) - $BluOrigin) / $graduations;
		for ($i = 0; $i <= $graduations; $i++) {
			$RetVal[$i] = strtoupper("#" . str_pad(dechex($RedOrigin + ($GradientSizeRed * $i)), 2, '0', STR_PAD_LEFT) .
					str_pad(dechex($GrnOrigin + ($GradientSizeGrn * $i)), 2, '0', STR_PAD_LEFT) .
					str_pad(dechex($BluOrigin + ($GradientSizeBlu * $i)), 2, '0', STR_PAD_LEFT));
		}
	} elseif ($graduations == 1) { // exactlly 2 colors
		$RetVal[] = $from_color;
		$RetVal[] = $to_color;
	} else { // one color
		$RetVal[] = $from_color;
	}
	return $RetVal;
}
function nameGen_mike($gndays, $donatordays, $uninfo, $username) {
	$uninfo = explode("|", $uninfo);
	$out = explode("~", $uninfo[4]);
	if ($gndays > 0) {
		$gnparts = $uninfo[0];
		$glowparts = $uninfo[6];
		$glows = explode(",", $out[1]);
		$gn = explode("~", $uninfo[1]);
		switch($gnparts){
			case 3:
				$half = (int) ((strlen($username) / 2));
				$left = substr($username, 0, $half);
				$right = substr($username, $half);
				for ($i = 0; $i < 3; $i++)
					$gn[$i] = empty($gn[$i]) ? "000000" : $gn[$i];
				$gnarray = array_merge(gradient($gn[0], $gn[1], strlen($left)), gradient($gn[1], $gn[2], strlen($right)));
				break;
			case 2:
				$gnarray = gradient($gn[0], $gn[1], strlen($username));
				break;
			default:
				for($i = 0; $i < strlen($username); $i++)
					$gnarray[] = $gn[0];
				break;
		}
		switch($glowparts){
			case 3:
				$half = (int) ((strlen($username) / 2));
				$left = substr($username, 0, $half);
				$right = substr($username, $half);
				for ($i = 0; $i < 3; $i++)
					$glows[$i] = empty($glows[$i]) ? "000000" : $glows[$i];
				$glowsarray = array_merge(gradient($glows[0], $glows[1], strlen($left)), gradient($glows[1], $glows[2], strlen($right)));
				break;
			case 2:
				$glowsarray = gradient($glows[0], $glows[1], strlen($username));
				break;
			default:
				for($i = 0; $i < strlen($username); $i++)
					$glowsarray[] = $glows[0];
				break;
		}
		$len = strlen($username);
		$un = '';
		for($i = 0; $i < $len; $i++){
			$un .= '<span style="color:' . $gnarray[$i] . ';text-shadow: 0 0 ' . $out[0] . 'px ' . $glowsarray[$i] . ';">' . $username[$i] . '</span>';
		}
		$bold = "font-weight:{$uninfo[2]};";
		$italic = ($uninfo[3] == 'yes') ? "font-style:italic;" : "";
		$spacing = ($uninfo[5] != 'normal') ? "letter-spacing:{$uninfo[5]}px;" : "";
		$title = "UN: {$gndays}, RY: {$donatordays}";
		return "<span title=\"{$title}\" style=\"{$bold}{$italic}{$spacing}\">" . $un . "</span>";
	} else if ($donatordays > 0) {
		$days = "RY: {$donatordays}";
		return "<span class=\"rm\" title=\"$days\">$username</span>";
	} else
		return "<span class=\"user\">$username</span>";
}
/*
for($i = 1; $i < 119; $i++)
	$totexp += experience($i);
print $totexp;
for($i = 0; $i < 756; $i++){
	perform_query("UPDATE grpgusers SET rating = rating - 1 WHERE id = 2");
	Send_Event(2, "You have been rated <font color=red><b>Down</b></font> by " . $user_class->formattedname . ". Rate them back now!", 9);
}
/*
$rand = rand(1, 500);
for($i = 0; $i < 12500; $i++){
	Send_Event(864, $user_class->formattedname . " sent you 1 point!");
}
$db->query("UPDATE grpgusers SET points = points + 12500 WHERE id = 864");
$db->execute();
print $rand;
/*
$db->query("SELECT id FROM grpgusers");
$db->execute();
$rows = $db->fetch_row();
	$db->query("INSERT INTO ofthes (userid) VALUES (?)");
foreach($rows as $row){
	$db->execute(array(
		$row['id']
	));
}
/*
	$ids[] = $row['id'];
$db->query("SELECT MAX(id) FROM grpgusers");
$db->execute();
$max = $db->fetch_single();
$everyone = range(1, $max);
$missing = array_diff($everyone, $ids);
//print_r($missing);
print implode(',',$missing);
/*$db->query("SELECT id FROM grpgusers WHERE lastactive > unix_timestamp() - 86400");
$db->execute();
$rows = $db->fetch_row();
$db->query("UPDATE grpgusers SET points = points + 1000 WHERE id = ?");
foreach($rows as $row){
	Send_event($row['id'], "You have been awarded 1,000 points in compensation for today's events.");
	$db->execute(array(
		$row['id']
	));
}*/

include "footer.php";