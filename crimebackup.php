<?php

include 'header.php';
$db->query("UPDATE grpgusers SET crimes = 'crime' WHERE id = ?");
$db->execute(array(
    $user_class->id
));

if ($user_class->jail > 0) {
    diefun("You can't do crimes if you're in prison!");
}
if ($user_class->hospital > 0) {
    diefun("You can't do crimes if you're in hospital!");
}
if (!empty($_GET['action']) && $_GET['action'] == 'crime') {
    if (isset($_GET['nonce']) && !empty($_GET['nonce'])) {
        if ($_GET['nonce'] != $_SESSION['crimenonce']) {
            $nonce = md5(uniqid(rand(), true));
            $_SESSION['crimenonce'] = $nonce;
            echo Message("Invalid Request - Please try again");
            exit();
        }
    } else {
        $nonce = md5(uniqid(rand(), true));
        $_SESSION['crimenonce'] = $nonce;
        echo Message("Invalid Request - Please try again");
        exit();
    }

    $id = security($_GET['id']);

$result2 = mysql_query("SELECT * FROM gamebonus WHERE id = 1");
    $worked = mysql_fetch_array($result2);


    $db->query("SELECT * FROM crimes WHERE id = ?");
    $db->execute(array(
        $id
    ));
    $row = $db->fetch_row(true);
    if (empty($row)) {
        diefun("Crime does not exist.");
    }


    $nerve = $row['nerve'];
    $time = floor(($nerve - ($nerve * 0.5)) * 6);
    $name = $row['name'];
    $stext = 'You successfully managed to ' . $name;
    $ftext = 'You failed to ' . $name;
    $chance = rand(0, 100);

    $money = ((50 * $nerve) + 15 * ($nerve - 1)) * 1;
    $exp = ((10 * $nerve) + 8 * ($nerve - 1)) * 1.5;


 $result2 = mysql_query("SELECT * FROM gamebonus WHERE id = 1");
    $worked = mysql_fetch_array($result2);


    if ($worked['Time'] > 0) {
        $exp *= 2;
        $money *= 1;
        $chance = 100;
    }

    //if ($user_class->id == 150) {
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
    //}

    /*if ($user_class->exppill >= time() & $user_class->id == 0) {
           $chance = 100;
          $money *= 1.0;
          $exp *= 2.4;
      }
          else if ($user_class->id == 0) {
                  $money *= 1.0;
          $exp *= 1.2;
      }*/


    if ($user_class->exppill >= time()) {
        $chance = 100;
        $money *= 1.0;
        $exp *= 2.0;
    }

    if ($user_class->prestige > 0) {
        $exp = max((((0.20 * $user_class->prestige) * $exp) + $exp), $exp);
        // $expBonus = (($user_class->prestige_exp * 20) / 100) + 1;
        // $exp *= $expBonus;
    }

    $gtax = 0;
    if ($user_class->gang != 0) {
        $gang_class = new Gang($user_class->gang);
        if ($gang_class->tax > 0) {
            $gtax = $money * ($gang_class->tax / 100);
        }
    }

    $money = $money - $gtax;

    if ($user_class->nerve < $nerve) {
        refill('n');
    }
    if ($user_class->nerve >= $nerve) {
        if ($chance < 5) {
            echo "$ftext<br /><br />";
            $user_class->nerve -= $nerve;
            $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ? WHERE id = ?");
            $db->execute(array(
                $nerve,
                $user_class->id
            ));
        } elseif ($chance < 7) {
            echo "$ftext You were hauled off to jail for 5 minutes.<br /><br />";
            $user_class->nerve -= $nerve;
            $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, caught = caught + 1, jail = 300, nerve = nerve - ? WHERE id = ?");
            $db->execute(array(
                $nerve,
                $user_class->id
            ));
        } else {
            mission('c');
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
            gangContest(array('crimes' => 1, 'exp' => $exp));
            bloodbath('crimes', $user_class->id, $nerve / $user_class->level);
            $user_class->money += $money;
            $user_class->nerve -= $nerve;
            $totaltax = $gtax;


            $db->query("UPDATE gangs SET moneyvault = moneyvault + ? WHERE id = ?");
            $db->execute(array(
                $gtax,
                $user_class->gang
            ));
            if ($gtax > 0) {
                echo "$stext<br />You received $exp exp and \$$money. (Gang Tax: \$$gtax)<br /><br />You have $user_class->nerve nerve left!";
            } else {
                echo "$stext<br />You received $exp exp and \$$money.<br /><br />You have $user_class->nerve nerve left!";
            }
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
        }
    } else {
        echo "<B>You don't have enough nerve for that crime.<br /><br /><br /><br /><a href='crime.php'></a>";
    }
}
genHead("Crimes");
echo'<center>';
echo'<a href="?spend=refnerve&crime"><img src="images/wand.png" /><button>Refill Nerve</button></a>';
echo'</center>';
$result2 = mysql_query("SELECT * FROM gamebonus WHERE ID = 1");
    $worked = mysql_fetch_array($result2);


if ($worked['Time'] > 0) {
    echo '<br><br><span class="pulsate" style="color:green;font-weight:bold;display:block;text-align:center;font-size:1.3em;">Crimes are currently giving Double EXP Payouts You have' . $worked['Time'] . ' minutes left!!</span><br />';
}

echo '<center>';
echo '<a href="newcrimes.php">';
echo '<span style="font-size:2em;"> &gt; Go to the Speed Crimes Page. &lt; </span>';
echo '</a>';
echo '</center>';
$max = ($user_class->nerref == 2) ? $user_class->maxnerve : $user_class->nerve;
$c = 1;
$db->query("SELECT * FROM crimes where nerve <= ? ORDER BY nerve ASC");
$db->execute(array(
    $max
));
$nonce = md5(uniqid(rand(), true));
$_SESSION['crimenonce'] = $nonce;
$rows = $db->fetch_row();
foreach (array_chunk($rows, 4, true) as $inner_rows) {
    foreach ($inner_rows as $row) {
        echo '<div class="crime">';
        echo $row['name'] . '<br />';
        echo $row['nerve'] . ' Nerve<br />';
        echo '<a href="?action=crime&id=' . $row['id'] . '&nonce=' . $nonce . '"><button>Do Crime</button></a>';
        echo '</div>';
    }
    echo '<div class="clear"></div>';
}
echo '</td>';
echo '</tr>';
include 'footer.php';
