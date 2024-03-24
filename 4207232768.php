<?php
include "ajax_header.php";
mysql_select_db('aa', mysql_connect('localhost', 'aa_user', 'GmUq38&SVccVSpt'));
$user_class = new User($_SESSION['id']);

// if($m->get('crime.' . $user_class->id . time()))
// 	$m->increment('crime.' . $user_class->id . time());
// else
//     $m->set('crime.'.$user_class->id . time(), 1, MEMCACHE_COMPRESSED);
// if($m->get('crime.'.$user_class->id . time()) > 100)
//     die("Error, going too fast.");


// if ($user_class->id == 174) {
//     $lastCrime = $m->get('crime.' . $user_class->id);
//     $m->set('crime.' . $user_class->id, time());
//     if ($lastCrime) {
//         if ((time() - $lastCrime) < 1) {
//             die("Error, going too fast.");
//         }
//     }
// }

$file = '/var/www/logs/speedcrimes.txt';
$current = $user_class->id . " | " . time() . "\n";
file_put_contents($file, $current, FILE_APPEND | LOCK_EX);

$lcl = $m->get('lastcrimeload.' . $user_class->id);
$lpl = $m->get('lastpageload.' . $user_class->id);
if ($lpl > $lcl)
    die("Error doing crime.");
if ($user_class->jail)
	die('You are in prison.');
if ($user_class->hospital)
	die('You are in the hospital.');
if(isset($_POST['id'])){
    $id = security($_POST['id']);
    $db->query("SELECT * FROM crimes WHERE id = ?");
    $db->execute(array(
        $id
    ));
    $row = $db->fetch_row(true);
    if (empty($row))
        die();
    $nerve = $row['nerve'];
    $time = floor(($nerve - ($nerve * 0.5)) * 6);
    $name = $row['name'];
    $stext = 'You successfully managed to ' . $name;
    $ftext = 'You failed to ' . $name;
    $chance = rand(0, 100);
    $money = (50 * $nerve) + 15 * ($nerve - 1);
    $exp = ((10 * $nerve) + 8 * ($nerve - 1)) * 1.5;

        $crimeexpbonus = 0;
		if ($user_class->crimeexpboost > 1) {
			$crimeexpbonus += 0.2;
			$crimeexpbonus += ($user_class->crimeexpboost - 1) * 0.0333;
		} else if ($user_class->crimeexpboost == 1) {
			$crimeexpbonus = 0.2;
		} else {
			$crimeexpbonus = 0;
		}
		$bonus = $exp * $crimeexpbonus;
		$exp = round($exp + $bonus, 2);


    if ($user_class->prestige > 0) {
        $exp *= (.10 * $user_class->prestige) + 1;
    }
	if ($user_class->exppill >= time()) {
        $exp *= 2.0;
		$chance = 100;
    }
	if (time() < 1630761484) {
        $exp *= 2;
        $chance = 100;
    }
	if ($user_class->nerve < $nerve)
        refill('n');
    if ($user_class->nerve >= $nerve) {
        if ($chance < 5) {
            $user_class->nerve -= $nerve;
            $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ? WHERE id = ?");
            $db->execute(array(
                $nerve,
                $user_class->id
            ));
            die($ftext.".|".number_format($user_class->points)."|".number_format($user_class->money)."|".number_format($user_class->level)."|".  genBars());
        } else if ($chance < 7) {
            $user_class->nerve -= $nerve;
            $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ?, caught = caught + 1, jail = 300 WHERE id = ?");
            $db->execute(array(
                $nerve,
                $user_class->id
            ));
            die("$ftext. You were hauled off to jail for 5 minutes.|".number_format($user_class->points)."|".number_format($user_class->money)."|".number_format($user_class->level)."|".  genBars());
        } else {
			if ($nerve >= 50)
                $which = "crimes50";
            elseif ($nerve >= 25)
                $which = "crimes25";
            elseif ($nerve >= 10)
                $which = "crimes10";
            elseif ($nerve >= 5)
                $which = "crimes5";
            else
                $which = "crimes1";
            newmissions($which);
            mission('c');
            gangContest(array('crimes' => 1, 'exp' => $exp));
            bloodbath('crimes', $user_class->id, $nerve / $user_class->level);

            $gtax = 0;
            if ($user_class->gang != 0) {
                $gang_class = new Gang($user_class->gang);
                if ($gang_class->tax > 0) {
                    $gtax = $money * ($gang_class->tax / 100);
                }
            }

            $money = $money - $gtax;
            $totaltax = $gtax;

            $user_class->money += $money;
            $user_class->nerve -= $nerve;
            $db->query("UPDATE grpgusers SET loth = loth + ?, exp = exp + ?, crimesucceeded = crimesucceeded + 1, crimemoney = crimemoney + ?, money = money + ?, nerve = nerve - ?, todaysexp = todaysexp + ?, expcount = expcount + ?, totaltax = totaltax + ? WHERE id = ?");
            $db->execute(array(
                $exp,
                $exp,
                $money,
                $money,
                $nerve,
                $exp,
                $exp,
$totaltax,
                $user_class->id
            ));

			$db->query("UPDATE gangs SET moneyvault = moneyvault + ? WHERE id = ?");
			$db->execute(array(
				$gtax,
				$user_class->gang
			));
			if ($gtax > 0) {
                die("$stext. You received $exp exp and \$$money.(Gang Tax: \$$gtax)|".number_format($user_class->points)."|".number_format($user_class->money)."|".number_format($user_class->level)."|".  genBars());
			} else {
                die("$stext. You received $exp exp and \$$money.|".number_format($user_class->points)."|".number_format($user_class->money)."|".number_format($user_class->level)."|".  genBars());
			}
        }
    } else {
        echo "<b>You don't have enough nerve for that crime.</b>";
    }
}