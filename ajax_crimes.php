<?php

exit();
include "ajax_header.php";
//mysql_select_db('game', mysql_connect('localhost', 'chaoscity_co', '3lrKBlrfMGl2ic14'));
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

// if ($user_class->id == 192) {
//     session_destroy();
// }

// $file = '/var/www/logs/speedcrimes.txt';
// $current = $user_class->id . " | " . time() . "\n";
// file_put_contents($file, $current, FILE_APPEND | LOCK_EX);

$lcl = $m->get('lastcrimeload.' . $user_class->id);
$lpl = $m->get('lastpageload.' . $user_class->id);
if ($lpl > $lcl) {
    //http_response_code(403);
    die("Error doing crime.");
}
if ($user_class->jail) {
    $current = $user_class->id . " | " . time() . " | PRISON\n";
    //file_put_contents($file, $current, FILE_APPEND | LOCK_EX);
    //http_response_code(403);
    die('You are in prison.');
}
if ($user_class->hospital) {
    $current = $user_class->id . " | " . time() . " | HOSPITAL\n";
    //file_put_contents($file, $current, FILE_APPEND | LOCK_EX);
    //http_response_code(403);
    die('You are in the hospital.');
}

if (isset($_POST['save'])) {
    $crime = security($_POST['save']);
    $m->set('crimesave' . $user_class->id, $crime);
}

if (isset($_POST['id'])) {
    $id = security($_POST['id']);

    if (!$row = $m->get('crimes.' . $id)) {
        $db->query("SELECT * FROM crimes WHERE id = ?");
        $db->execute(array(
            $id
        ));
        $row = $db->fetch_row(true);
        $m->set('crimes.' . $id, $row, false, 120);
    }

    if (empty($row)) {
        die();
    }
    $nerve = $row['nerve'];
    $time = floor(($nerve - ($nerve * 0.5)) * 6);
    $name = $row['name'];
    $stext = 'You successfully managed to ' . $name;
    $ftext = 'You failed to ' . $name;
    $chance = rand(0, 100);
    $money = ((50 * $nerve) + 15 * ($nerve - 1)) * 1;
    $exp = ((10 * $nerve) + 8 * ($nerve - 1)) * 3;

    $crimeexpbonus = 0;
    if ($user_class->crimeexpboost > 1) {
        $crimeexpbonus += 0.2;
        $crimeexpbonus += ($user_class->crimeexpboost - 1) * 0.0333;
    } elseif ($user_class->crimeexpboost == 1) {
        $crimeexpbonus = 0.2;
    } else {
        $crimeexpbonus = 0;
    }
    $bonus = $exp * $crimeexpbonus;
    $exp = round($exp + $bonus, 2);


    
    if ($user_class->exppill >= time()) {
        $exp *= 2.0;
        $chance = 100;
    }

$result2 = mysql_query("SELECT * FROM gamebonus WHERE id = 1");
    $worked = mysql_fetch_array($result2);

 if ($worked['Time'] > 0) {
        $exp *= 2;
        $money *= 1;
        $chance = 100;
    }



    if (time() < 	1668988799) {
        $exp *= 2;
        $chance = 100;
    }
    if ($user_class->nerve < $nerve) {
        refill('n');
    }
    if ($user_class->nerve >= $nerve) {
        if ($chance < 5) {
            $user_class->nerve -= $nerve;
            $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ? WHERE id = ?");
            $db->execute(array(
                $nerve,
                $user_class->id
            ));
            die($ftext.".|".number_format($user_class->points)."|".number_format($user_class->money)."|".number_format($user_class->level)."|".  genBars());
        } elseif ($chance < 7) {
            $user_class->nerve -= $nerve;
            $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ?, caught = caught + 1, jail = 300 WHERE id = ?");
            $db->execute(array(
                $nerve,
                $user_class->id
            ));
            die("$ftext. You were hauled off to jail for 5 minutes.|".number_format($user_class->points)."|".number_format($user_class->money)."|".number_format($user_class->level)."|".  genBars());
        } else {
            if ($nerve >= 50) {
                $which = "crimes50";
            } elseif ($nerve >= 25) {
                $which = "crimes25";
            } elseif ($nerve >= 10) {
                $which = "crimes10";
            } elseif ($nerve >= 5) {
                $which = "crimes5";
            } else {
                $which = "crimes1";
            }
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
		if ($user_class->prestige > 0) {
    for ($i = 1; $i <= $user_class->prestige; $i++) {
       $exp *= 1.20; // Increase $exp by 20% for each point of prestige
    }
}
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
        $current = $user_class->id . " | " . time() . " | NO NERVE\n";
        //file_put_contents($file, $current, FILE_APPEND | LOCK_EX);
        echo "<b>You don't have enough nerve for that crime.</b>";
    }
}
