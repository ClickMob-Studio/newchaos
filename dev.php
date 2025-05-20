<?php
include 'header.php';
echo '<link rel="stylesheet" href="css/bars-1to10.css?' . uniqid() . '">';

if ($user_class->id != 174 && $user_class->id != 1 && $user_class->id != 135) {
    header('location: index.php');
}

if ($_GET['test'] == 'val') {


    Send_Event(296, "You have been awarded 6 Heart's for coming 1st in the Valentines 2023 Event for `Most Hearts Earned`");
    Give_Item(155, 296, 6);

    Send_Event(422, "You have been awarded 6 Heart's for coming 1st in the Valentines 2023 Event for `Most Hearts Earned`");
    Give_Item(155, 422, 6);

    Send_Event(437, "You have been awarded 4 Heart's for coming 2nd in the Valentines 2023 Event for `Most Hearts Earned`");
    Give_Item(155, 437, 4);

    Send_Event(766, "You have been awarded 2 Heart's for coming 3rd in the Valentines 2023 Event for `Most Hearts Earned`");
    Give_Item(155, 766, 2);

    Send_Event(422, "You have been awarded 6 Heart's for coming 1st in the Valentines 2023 Event for `Top Admired Player`");
    Give_Item(155, 422, 6);

    Send_Event(152, "You have been awarded 4 Heart's for coming 2nd in the Valentines 2023 Event for `Top Admired Player`");
    Give_Item(155, 152, 4);

    Send_Event(437, "You have been awarded 2 Heart's for coming 3rd in the Valentines 2023 Event for `Top Admired Player`");
    Give_Item(155, 437, 2);

    // $winners = [296, 422, 437, 766];
    // $prize = [6, 6, 4, 2];

    // for ($i=0; $i < 5; $i++) {
    //     $me = new User($winners[$i]);
    //     Send_Event(174, $me->formattedname . " appears behind you.");
    // }

    // $me = new User(174);
    // Send_Event(174, $me->formattedname . " appears behind you.");
}

if ($_GET['test'] == 'interest') {
    $person_class = new User(422);
    print_pre($person_class->rmdays);
    if ($person_class->rmdays >= 1)
        $multiply = 0.04;
    else
        $multiply = 0.02;
    $addmul = $ptsadd = 0;
    print_pre($multiply);
    if ($person_class->donations >= 50) {
        $addmul = .02;
        $ptsadd = 75;
    }
    if ($person_class->donations >= 100) {
        $addmul = .03;
        $ptsadd = 120;
    }
    if ($person_class->donations >= 200) {
        $addmul = .05;
        $ptsadd = 150;
    }
    print_pre($addmul);
    print_pre($ptsadd);
    $multiply += $addmul;
    print_pre($multiply);
    if ($person_class->bank >= 15000000)
        $interest = ceil(15000000 * $multiply);
    else
        $interest = ceil($line['bank'] * $multiply);
    $newmoney = round($line['bank'] + $interest);
    // mysql_query("UPDATE grpgusers SET bank = $newmoney, points = points + $ptsadd WHERE id = {$line['id']}");
    echo "You have earned " . prettynum($interest, 1) . " for your bank";
    // Send_Event($line['id'], "You have earned " . prettynum($interest, 1) . " for your bank", $line['id']);
    exit();
}

if ($_GET['test'] == 'ladder') {

    $used = [];
    $attackLadderRes = mysql_query("SELECT * FROM `attackladder` ORDER BY `spot` ASC");
    while ($row = mysql_fetch_array($attackLadderRes)) {
        $used[] = $row['spot'];
    }
    print_r($used);
    $new_spot = current(array_diff(range($used[0], 10), $used));
    print_r($new_spot);
    exit();

    $ladderRewards = [150, 100, 100, 100, 100, 100, 100, 100, 100, 100];

    $attackLadderRes = mysql_query("SELECT * FROM `attackladder` ORDER BY `spot` ASC");
    while ($row = mysql_fetch_array($attackLadderRes)) {
        if ((time() - $row['last_attack']) > 14400) {
            // mysql_query("DELETE FROM attackladder WHERE `user` = '{$row['user']}'");
            // Send_Event($row['user'], "[-_USERID_-] You were removed from the Attack Ladder due to inactivity.", $row['user']);
            echo "remove " . $row['user'] . " " . $row['last_attack'] . " " . time() . "<br>";
        } else {
            // mysql_query("UPDATE `grpgusers` SET `points` = `points` + " . $ladderRewards[$row['spot'] - 1] . " WHERE `id` = '{$row['user']}' LIMIT 1") or mysql_error();
            // Send_Event($row['user'], "[-_USERID_-] you are ranked ".$row['spot']." in the attack ladder and you've been rewarded ".$ladderRewards[$row['spot'] - 1]." points ", $row['user']);
            echo "award " . $row['user'] . " " . ordinal($row['spot']) . "<br>";
        }
    }

    //mysql_query("SET @counter := 0; UPDATE `attackladder` SET `attackladder`.`spot` = (@counter := @counter + 1) ORDER BY `attackladder`.`spot` ASC");

}

if ($_GET['test'] == '1111') {

    $dsn = 'mysql:host=localhost;dbname=aa;charset=utf8';
    try {
        $_db = new PDO($dsn, 'aa_user', 'ovShOg&iLtat');
    } catch (PDOException $e) {
        exit('<p><strong>CONSTRUCT ERROR</strong></p>' . $e->getMessage());
    }

    $row = $_db->query("SELECT * FROM bans b, ads a")->fetch(PDO::FETCH_NAMED);
    print_r($row);
    exit();
}

if ($_GET['test'] == 'daily') {

    $ignoreGangs = "11, 31"; // admin gangs to exclude
    $db->query("SELECT * FROM gangs WHERE id NOT IN ($ignoreGangs) ORDER BY `dailyKills` DESC LIMIT 1");
    $db->execute();
    $topKills = $db->fetch_row()[0]['id'];

    $db->query("SELECT * FROM gangs WHERE id NOT IN ($ignoreGangs) ORDER BY `dailyCrimes` DESC LIMIT 1");
    $db->execute();
    $topCrimes = $db->fetch_row()[0]['id'];

    $db->query("SELECT * FROM gangs WHERE id NOT IN ($ignoreGangs) ORDER BY `dailyBusts` DESC LIMIT 1");
    $db->execute();
    $topBusts = $db->fetch_row()[0]['id'];

    $db->query("SELECT * FROM gangs WHERE id NOT IN ($ignoreGangs) ORDER BY `dailyMugs` DESC LIMIT 1");
    $db->execute();
    $topMugs = $db->fetch_row()[0]['id'];

    $_ids = compact("topKills", "topCrimes", "topBusts", "topMugs");

    foreach ($_ids as $_id) {
        $db->query("UPDATE gangs SET respect = respect + 100 WHERE id = ?");
        $db->execute(
            array(
                $_id
            )
        );
    }

    Gang_Event($topKills, "Respect Gang Of The Day - Kills +100 Respect", 0);
    Gang_Event($topCrimes, "Respect Gang Of The Day - Crimes +100 Respect", 0);
    Gang_Event($topMugs, "Respect Gang Of The Day - Mugs +100 Respect", 0);
    Gang_Event($topBusts, "Respect Gang Of The Day -Busts +100 Respect", 0);

    $db->query("UPDATE gangs SET dailyCrimes = 0, dailyKills = 0, dailyBusts = 0, dailyMugs = 0");
    $db->execute();

    exit();
}

if ($_GET['test'] == 'm') {
    print_r($m->get('citynames.1'));
    if ($m->get('citynames.1')) {
        echo "M: " . $m->get('citynames.1');
    } else {
        $db->query("SELECT `name` FROM cities WHERE id = ?");
        $row['city'] = 1;
        $db->execute([$row['city']]);
        $city = $db->fetch_single();
        print_r($city);
        $m->set('citynames.1', $city, false, 60);
    }
}
;

if ($_GET['test'] == 'otd') {


    // $db->query("SELECT id, username FROM `grpgusers` WHERE lastactive > 1631818800");
    // $db->execute();
    // $rows = $db->fetch_row();

    // foreach ($rows as $row) {
    //     Give_Item(10, $row['id']);
    //     $db->query("UPDATE grpgusers SET donate_token = donate_token + 1 WHERE id = ?");
    //     $db->execute([
    //         $row['id']
    //     ]);
    //     Send_Event($row['id'], "You have been credited +1 Donation Token", 1);
    //     Send_Event($row['id'], "You have been credited +1 Double EXP Pill (1 Hour). You can find it <a href='inventory.php'><font color=yellow><b>[Here]</b></font></a>", 1);
    //     Send_Event($row['id'], "Double Crime EXP Activated - Recent Speed Crimes Changes Temporally Removed!", 1);
    // }

    $db->query("SELECT id, tamt FROM grpgusers WHERE `tamt` > 0 ORDER BY `tamt` DESC LIMIT 1");
    $db->execute();
    $row = $db->fetch_row(true);
    print_r($row);
    exit();

}

// exit();

// $result = [
//     'failed' => 0,
//     'success' => 0,
//     'jailed' => 0
// ];

// for ($i=0; $i < 1000; $i++) {
//     $chance = rand(0, 100);
//     if ($chance < 5) {
//         $result['failed']++;
//     } else if ($chance < 7) {
//         $result['jailed']++;
//     } else {
//         $result['success']++;
//     }
// }

// var_dump($result);

// exit();

if ($_GET['test'] == 'timeleft') {
    //$m->set('mug_995', time() + 30, 30);
    $tl = abs($m->get('mug_995') - time());
    var_dump(timeLeft($tl));
    echo formatname(1) . " can be mugged again in " . timeLeft($tl);
}

if ($_GET['test'] == 'normal') {
    $id = 1;
    $db->query("SELECT COUNT(*) FROM chat_rating WHERE post_id = $id AND rating_action = 'like'");
    $db->execute();
    $rows = $db->fetch_single();
    print_r($rows);
    exit();
}

if ($_GET['test'] == 'bbpay') {

    $loop = array(
        'level' => array(
            10000,
            5000,
            2500
        ),
        'crimes' => array(
            10000,
            5000,
            2500
        ),
        'referrals' => array(
            10000,
            5000,
            2500
        ),
        'attackswon' => array(
            10000,
            5000,
            2500
        ),
        'attackslost' => array(
            10000,
            5000,
            2500
        ),
        'defendwon' => array(
            10000,
            5000,
            2500
        ),
        'defendlost' => array(
            10000,
            5000,
            2500
        ),
        'busts' => array(
            10000,
            5000,
            2500
        ),
        'mugs' => array(
            10000,
            5000,
            2500
        ),
        'donator' => array(
            30,
            20,
            10
        )
    );

    $db->query("SELECT * FROM bloodbath WHERE id = 15");
    $db->execute();
    $row = $db->fetch_row(true);

    $bloodbath = unserialize($row['winners']);

    $types = ['level', 'referrals', 'mugs', 'attackswon', 'attackslost', 'defendwon', 'defendlost', 'busts', 'donator', 'crimes'];

    $winners = array();

    foreach ($types as $type) {
        $stack = array();
        foreach ($bloodbath as $value) {
            if ($value['userid'] == 1 || $value['userid'] == 174)
                continue;
            $stack[$value['userid']] = $value[$type];
            //$winners[$type][$value['userid']] = $value[$type];
        }
        arsort($stack);
        array_reverse($stack);
        $stack = array_slice($stack, 0, 3, true);
        $winners[$type] = $stack;
    }

    echo "<pre>";
    print_r($winners);
    echo "</pre>";

    foreach ($winners as $type => $arr) {
        $prizes = $loop[$type];
        $i = 0;
        foreach ($arr as $a => $v) {
            $db->query("SELECT username FROM grpgusers WHERE id = $a");
            $db->execute();
            $row = $db->fetch_row(true);
            $payout[$type][$row['username']] = $prizes[$i];
            $i++;
        }
    }
    print_pre($payout);

    exit();

}

if ($_GET['test'] == 'tamt') {
    $db->query("SELECT id FROM grpgusers WHERE `tamt` > 0 ORDER BY `tamt` DESC LIMIT 1");
    $db->execute();
    $row = $db->fetch_row(true);
    print_r($row);
}

if ($_GET['test'] == 'phpinfo') {
    phpinfo();
    die();
}

if ($_GET['test'] == 'userclass') {
    print_r($user_class);
    die();
}

if ($_GET['test'] == 'bust') {

    $db->query("SELECT id, jail FROM grpgusers WHERE jail > 0 AND id <> $user_class->id ORDER BY jail ASC");
    $db->execute();
    $rows = $db->fetch_row();

    $rowJailed = array_map(function ($a) {
        return $a['id'];
    }, $rows);

    print_r($rowJailed);

    die();

    $id = $_GET['user'];

    $cells = $m->get('cells');
    if ($cells) {
        echo "<pre>";
        print_r($cells);
        echo "</pre>";
        $_cells = array();
        foreach ($cells as $k => $v) {
            if ($v['user'] == $id)
                continue;

            $_cells[$k] = $v;
        }
        echo "<pre>";
        print_r($_cells);
        echo "</pre>";
        $m->set('cells', $_cells, false);
    }

}

if ($_GET['test'] == 'jail') {

    $ignore = array($user_class->id);
    $ignore = implode(',', $ignore);

    $db->query("SELECT id, jail FROM grpgusers WHERE jail > 0 AND id NOT IN ($ignore) ORDER BY jail ASC");
    $db->execute();
    $rows = $db->fetch_row();

    $rowJailed = array_map(function ($a) {
        return $a['id'];
    }, $rows);

    $available_cells = range(0, 11);

    print_pre($rowJailed);
    print_pre($rows);
    print_pre($available_cells);

    $cells = $m->get('cells');
    var_dump($cells);
    if ($cells) {
        print_pre($cells);
        $cells = array_values($cells);
        $cells_count = count($cells);
        for ($i = 0; $i < $cells_count; $i++) {
            if (!in_array($cells[$i]['id'], $rowJailed))
                unset($cells[$i]);

            $jailed[] = $cells[$i]['id'];
            unset($available_cells[$cells[$i]['cell']]);
        }
        print_pre($cells);
    } else {
        $cells = array();
        //$m->delete('cells');
    }

    print_pre($available_cells);

    foreach ($rows as $row) {
        if (in_array($row['id'], $jailed))
            continue;

        $cell = array_rand($available_cells);
        $cells[] = array(
            'id' => $row['id'],
            'username' => str_replace('</a>', '', preg_replace('/<a[^>]*>/', '', formatName($row['id']))),
            'cell' => $cell
        );
        unset($available_cells[$cell]);
    }
    print_pre($available_cells);
    print_pre($cells);

    $m->set('cells', $cells, false);

    echo "<pre>";
    echo json_encode($cells);
    echo "</pre>";

    echo '<table style="width:75%;table-layout:fixed;text-align:center;margin:0 auto;">';
    echo '<tr style="height:80px;">';
    echo '<td class="cells" id="cell_0">Empty Cell</td>';
    echo '<td class="cells" id="cell_1">Empty Cell</td>';
    echo '<td class="cells" id="cell_2">Empty Cell</td>';
    echo '</tr>';
    echo '<tr style="height:80px;">';
    echo '<td class="cells" id="cell_3">Empty Cell</td>';
    echo '<td class="cells" id="cell_4">Empty Cell</td>';
    echo '<td class="cells" id="cell_5">Empty Cell</td>';
    echo '</tr>';
    echo '<tr style="height:80px;">';
    echo '<td class="cells" id="cell_6">Empty Cell</td>';
    echo '<td class="cells" id="cell_7">Empty Cell</td>';
    echo '<td class="cells" id="cell_8">Empty Cell</td>';
    echo '</tr>';
    echo '<tr style="height:80px;">';
    echo '<td class="cells" id="cell_9">Empty Cell</td>';
    echo '<td class="cells" id="cell_10">Empty Cell</td>';
    echo '<td class="cells" id="cell_11">Empty Cell</td>';
    echo '</tr>';
    echo '</table>';
    ?>

    <script>
        //clear_cells()
        var data = <?php echo json_encode($cells); ?>;
        let jailers = data;
        console.log(jailers);
        for (jailer in jailers) {
            $("#cell_" + jailers[jailer]["cell"]).html(jailers[jailer]["username"])
            $("#cell_" + jailers[jailer]["cell"]).attr("data-id", jailers[jailer]["id"]);
        }
    </script>
    <?
    echo "test";
}

if ($_GET['test'] == 'email') {
    mail("dai007uk@googlemail.com", "Test", "Hello!");
}

if ($_GET['test'] == 'award') {

    $players = array(
        339 => 6,
        317 => 2,
        2 => 2,
        45 => 2,
        199 => 6,
        21 => 4,
        207 => 2
    );
    //   echo "<pre>";
    //   print_r($players);
    //   echo "</pre>";
    foreach ($players as $key => $value) {
        Send_Event($key, "Valentines Day Reward");
        Give_Item(155, $key, $value);
    }
}

if ($_GET['test'] == 'bb') {

    $db->query("SELECT * FROM bloodbath WHERE id = 15");
    $db->execute();
    $rows = $db->fetch_row();
    $winners = unserialize($rows[0]['winners']);
    // foreach ($winners as $row) {
    //     $info[] = $row;
    // }
    echo '<pre>';
    print_r($winners);
    echo '</pre>';

}

if ($_GET['test'] == 'give') {
    // Give_Item(151, 150, 1);

    $me = new User(174);
    Send_Event(174, $me->formattedname . " appears behind you.");
    //Send_Event(174, "You have been fucked in the ass by " . $me->formattedname . " I bet that hurt - but you did enjoy it!");
    //Send_Event(235, $me->formattedname . " appears behind you.");
}

if ($_GET['test'] == 'poll') {

    $db->query("SELECT * FROM polls LIMIT 1");
    $db->execute();
    $row = $db->fetch_row(true);

    $title = $row['title'];
    $choices = unserialize($row['options']);
    $end = $row['finish'];
    print $title;
    print $end;
    print_r($choices);

    echo '<div class="floaty headerpoll">
        <h3>TrueMMO Poll</h3>
        <p>' . $title . '</p>
        <form id="poll">
            <input type="hidden" id="pollid" value="' . $pollId . '">
            <div class="radiobuttons" style="display: inline-grid;">';

    foreach ($choices as $key => $value) {
        echo '<label><input type="radio" name="radioq" id="' . $key . '">' . $value . '</label>';
    }

    echo '
        </div>
        <div class="clear"></div>
        <button id="pollSubmit">Submit</button>
        <div class="clear"></div>
        </form>
        </div>';

    exit();

}

if ($user_class->id == 150 && $_GET['test'] == 'bj') {

    $test = 1547227718;
    if ($test > time()) {
        echo 'good';
    }

    require_once("class.Game.php");
    // Establish defaults
    $gameOver = 0;
    $game = new Game();		// Create a new deck and start a new game
    /**clear all session variables if user plays again**/
    if (isset($_GET['again'])) {
    }

    require_once 'includes/functions.php';
    start_session_guarded();

    if (!isset($_GET['hit']) && !isset($_GET['stand'])) {
        /**initial deal**/
        $userHand[0] = $game->dealCard();
        $dealerHand[0] = $game->dealCard();
        $userHand[1] = $game->dealCard();
        $dealerHand[1] = $game->dealCard();
        $_SESSION['userHand'] = $userHand;
        $_SESSION['dealerHand'] = $dealerHand;
        $_SESSION['dHandValue'] = $game->getHandValue($_SESSION['dealerHand']);
    } else if (isset($_GET['hit'])) {
        $_SESSION['userHand'][sizeof($_SESSION['userHand'])] = $game->dealCard();
        $_SESSION['userValue'] = $game->getHandValue($_SESSION['userHand']);
        $_SESSION['dHandValue'] = $game->getHandValue($_SESSION['dealerHand']);
        $_SESSION['uHandValue'] = $game->getHandValue($_SESSION['userHand']);
        // Auto-stand if at 21
        if ($_SESSION['userValue'] == 21)
            header("Location: dev.php?stand=stand");
        // Check if, by hitting, the game has ended
        $gameOver = $game->winCheck($_SESSION['userValue'], $_SESSION['dHandValue'], 0);
    } else if (isset($_GET['stand'])) {
        while ($_SESSION['dHandValue'] < 17) {
            $_SESSION['dealerHand'][sizeof($_SESSION['dealerHand'])] = $game->dealCard();
            $_SESSION['dHandValue'] = $game->getHandValue($_SESSION['dealerHand']);
            $_SESSION['uHandValue'] = $game->getHandValue($_SESSION['userHand']);
        }
        $gameOver = $game->winCheck($_SESSION['uHandValue'], $_SESSION['dHandValue'], 1);
    }
    ?>

    <html>

    <head>
        <style type="text/css">
            body {
                margin: 0px;
            }
        </style>
    </head>

    <body>
        <h2 style='text-align:center;'>Blackjack</h2>
        <div align='center' style="background-color:beige; padding:5px; width:300px; margin:auto;">
            <div style="text-decoration:underline; font-weight:bold;">Your Hand is:</div><br />
            <?php
            // Show cards
            for ($i = 0; $i < sizeof($_SESSION['userHand']); $i++) {
                echo $game->translateCard($_SESSION['userHand'][$i]) . "<br />";
            }
            echo "<div style='text-decoration:underline; font-weight:bold;'><br /><br />Your opponents visible cards: </div><br />";
            if ($gameOver == 0) {
                for ($j = 1; $j < sizeof($_SESSION['dealerHand']); $j++) {
                    echo $game->translateCard($_SESSION['dealerHand'][$j]) . "<br />";
                }
            } else {
                for ($j = 0; $j < sizeof($_SESSION['dealerHand']); $j++) {
                    echo $game->translateCard($_SESSION['dealerHand'][$j]) . "<br />";
                }
            }

            echo "<br /><br />";
            /**game is not over; reload screen like normal**/
            if ($gameOver == 0) {
                echo '<form style=\'text-align:center\' action=\'dev.php\' method=\'get\'>
                          <input type=\'submit\' name=\'hit\' value=\'hit\'/><br />
                          <input type=\'submit\' name=\'stand\' value=\'stand\'/></form>';
            } /**Victory conditions are met; print final screen**/ else {
                echo 'Your final score was: ' . $_SESSION['uHandValue'] . '<br /> Your opponents final score was: ' . $_SESSION['dHandValue'] . '
                <form style=\'text-align:center\' action=\'dev.php\' method=\'get\'>
                <input type=\'submit\' name=\'again\' value=\'Play Again\'/></form>';
            } ?>
        </div>
    </body>

    </html>

    <?php
    exit();
}

// $me = new User(4);
// Send_Event(4, "You have been fucked in the ass by " . $me->formattedname . " I bet that hurt - but you did enjoy it!");

$db->query("SELECT * FROM bloodbath WHERE id = 108");
$db->execute();
$row = $db->fetch_row(true);

$bloodbath = unserialize($row['winners']);

$types = ['level', 'referrals', 'mugs', 'attackswon', 'attackslost', 'defendwon', 'defendlost', 'sspies', 'busts', 'donator', 'crimes'];

$winners = array();

foreach ($types as $type) {
    $stack = array();
    foreach ($bloodbath as $value) {
        $stack[$value['userid']] = $value[$type];
        //$winners[$type][$value['userid']] = $value[$type];
    }
    arsort($stack);
    array_reverse($stack);
    $stack = array_slice($stack, 0, 3, true);
    $winners[$type] = $stack;
}

echo "<pre>";
print_r($winners);
echo "</pre>";
exit();

$levels = array();

arsort($levels);
array_reverse($levels);
$levels = array_slice($levels, 0, 3);

echo "<pre>";
print_r($levels);
echo "</pre>";

exit();

function bloodbath_winners($array)
{
    $winners = array();
    $level = 0;
    $referrals = 0;
    $attackswon = 0;
    $attackslost = 0;
    $defendwon = 0;
    $defendlost = 0;
    $spies = 0;
    $busts = 0;
    $mugs = 0;
    $donator = 0;
    $crimes = 0.00;
    foreach ($array as $key => $value) {

        if ($value['level'] > $level) {
            $level = $value['level'];
            $winners['level'] = $value['userid'];
            //echo $value['userid'] . ' ' . $level . '<br>';
        }

        // if ($value['defendlost'] > $defendlost) {
        //     $defendlost = $value['defendlost'];
        //     $winners['defendlost'] = $key;
        // }

        // $maxLevel = $value['level'] > $maxLevel ? $value['level'] : $maxLevel;
        // $maxKey = $value['level'] > $maxKey ? $key : $maxKey;
    }
    print_r($winners);
}
;

bloodbath_winners($bloodbath);

function print_pre($p)
{
    echo "<pre>";
    print_r($p);
    echo "</pre>";
}

include 'footer.php';