<?php
include "header.php";
$level = 500;
$points = 1000000;
$cash = 0;
$exp = 0;
$expneeded = experience($level + 1);
$runs = 0;
$certs = 0;
while($points > 0){
	$rand = rand(1, 10000);
	if($rand <= 2500){
		$certs++;
		continue;
	}elseif($rand <= 3625){
		$exp += floor(min(rand(1, 5) * $expneeded / 100, 2500));
		$cash += floor(rand(5, 25) * ($level + 2));
		$points += rand(5, 15);
	}elseif($rand <= 9625){
		$exp += floor(min(rand(1, 5) * $expneeded / 100, 2500));
		$cash += MIN(floor(rand(5, 25) * ($level + 2)), 10000);
	}else{
		$exp += floor(min(rand(1, 5) * $expneeded / 100, 2500));
		$points += rand(5, 15);
	}
	if($exp >= $expneeded){
		$level++;
		$exp -= $expneeded;
		$expneeded = experience($level + 1);
	}
	$points -= 2;
	if($runs++ > 100000){
		break;
	}
}
print"
Level : $level<br />
Money : $cash<br />
Points : $points<br />
Certs : $certs
";
include "footer.php";
?>