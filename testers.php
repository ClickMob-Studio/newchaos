<?php
echo'<!DOCTYPE HTML>';
echo'<html>';
	echo'<head>';
		echo'<style>';
			echo'input{';
				echo'background:#000;';
				echo'border:red thin solid;';
				echo'color:red;';
			echo'}';
			echo'*{';
				echo'font-size:1.05em;';
			echo'}';
		echo'</style>';
	echo'</head>';
	echo'<body style="background:#000;color:red;">';
if(isset($_POST['level'])){
	$cur = $start = $_POST['level'];
	$finish = $_POST['levelto'];
	$bunny = $_POST['bunnylevel'];
	$exp = $cides = 0;
	$needed = experience($cur);
	while($cur < $finish){
		$exp += gained($cur, $bunny);
		if($exp >= $needed){
			$cur++;
			$exp -= $needed;
			$needed = experience($cur);
		}
		$cides++;
	}
	echo'<div style="border-radius:10px;background:rgba(0,128,0,.75);padding:10px;color:white;width:600px;text-align:center;margin:auto;">';
		echo "It will take " . number_format($cides) . " cides to get a level $start to level $finish, using a level $bunny bunny.";
	echo'</div>';
}

		echo'<form method="post" style="margin:auto;">';
			echo'<table style="margin:auto;">';
				echo'<tr>';
					echo'<td>Customer\'s Starting Level:</td>';
					echo'<td><input type="text" name="level" /></td>';
				echo'</tr>';
				echo'<tr>';
					echo'<td>Customer\'s Desired Level:</td>';
					echo'<td><input type="text" name="levelto" /></td>';
				echo'</tr>';
				echo'<tr>';
					echo'<td>Your Level:</td>';
					echo'<td><input type="text" name="bunnylevel" /></td>';
				echo'</tr>';
				echo'<tr>';
					echo'<td colspan="2" style="text-align:center;"><input type="submit" value="Calculate" /></td>';
				echo'</tr>';
			echo'</table>';
		echo'</form>';
	echo'</body>';
echo'</html>';
function gained($cur, $bunny){
	return max(min((100 * ($bunny - $cur)), 12000), 10);
}

function experience($L) {
    $a = 0;
    $end = 0;
    for ($x = 1; $x < $L; $x++)
        $a += round($x + 1500 * pow(4, ($x / 190)));
    if ($x >= 200)
        $a *= 2;
    if ($L >= 300)
        $a *= 2;
    if ($L >= 400)
        $a *= 2;
    if ($L >= 500)
        $a *= 2;
    if ($L >= 600)
        $a *= 2;
    if ($L >= 700)
        $a *= 2;
    if ($L >= 800)
        $a *= 2;
    if ($L >= 900)
        $a *= 2;
    if ($L >= 1000)
        $a *= 2;
    return round($a / 4);
}
?>