<?php
include 'header.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// if ($user_class->id == 174) {
//     $user_class = new User(84);
// }

if (isset($_GET['exchangetoken'])) {
    if ($user_class->donate_token > 0) {
        $db->query("UPDATE grpgusers SET donate_token = donate_token - 1, points = points + 1000 WHERE id = ?");
        $db->execute(
            array(
                $user_class->id,
            )
        );
        $message = "You have exchanged a " . item_popup('Donation Boost Token', 156) . " for 1,000 Points";
        Send_Event($user_class->id, "You have exchanged a Donation Boost Token for 1,000 Points.", $user_class->id);
        diefun($message);
    } else {
        diefun('Sorry you do not have any tokens to exchange');
    }
}

if (isset($_POST['heart'])) {
    $target = security($_POST['target']);
    if ($target == $user_class->id)
        diefun("You can't send hearts to yourself");

    $receiver = new User($target);

    $howmany = check_items(155);
    if ($howmany) {

        $bank = false;
        $rnd = mt_rand(0, 1);
        switch ($rnd) {
            case 0:
                $rnd = mt_rand(0, 6);
                if ($rnd == 0) {
                    $amount = mt_rand(7500000, 15000000);
                } else {
                    $amount = mt_rand(500000, 3500000);
                }
                $reward = array(
                    'type' => "money",
                    'amount' => $amount
                );
                $sql = "UPDATE grpgusers SET `bank` = `bank` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $bank = true;
                $result = "$" . number_format($reward['amount'], 0);
                $bankDetails = array(
                    array(
                        'userid' => $user_class->id,
                        'amount' => $reward['amount'],
                        'newbalance' => $user_class->bank = $user_class->bank + $reward['amount']
                    ),
                    array(
                        'userid' => $receiver->id,
                        'amount' => $reward['amount'],
                        'newbalance' => $receiver->bank = $receiver->bank + $reward['amount']
                    )
                );
                break;
            case 1:
                $rnd = mt_rand(0, 6);
                if ($rnd == 0) {
                    $amount = mt_rand(4000, 6000);
                } else {
                    $amount = mt_rand(500, 3000);
                }
                $reward = array(
                    'type' => "points",
                    'amount' => $amount
                );
                $sql = "UPDATE grpgusers SET `points` = `points` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $result = number_format($reward['amount'], 0) . " points";
                break;
        }

        $rnd = (mt_rand(0, 1000) / 10);
        if ($rnd < 10) {
            $items = array(
                array(
                    'id' => 8,
                    'name' => 'a Mug Protection Pill'
                ),
                array(
                    'id' => 9,
                    'name' => 'an Attack Protection Pill'
                ),
                array(
                    'id' => 10,
                    'name' => 'a Double EXP Pill'
                )
            );
            $rnd = mt_rand(0, count($items) - 1);
            $item = $items[$rnd];
            Give_Item($item['id'], $user_class->id);
            Give_Item($item['id'], $target);
            $result = $item['name'];
            Send_Event($user_class->id, "You sent [-_USERID_-] a Heart and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you a Heart and you both receive " . $result, $user_class->id);

            $db->query("INSERT INTO hearts_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db->execute(
                array(
                    $user_class->id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(155, $user_class->id, 1);
            diefun('You sent ' . formatName($target) . ' a Heart and you both receive ' . $result);
        }

        $db->query($sql);
        $db->execute(
            array(
                $user_class->id,
                $target
            )
        );
        Send_Event($user_class->id, "You sent [-_USERID_-] a Heart and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you a Heart and you both receive " . $result, $user_class->id);

        $db->query("INSERT INTO hearts_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
        $db->execute(
            array(
                $user_class->id,
                $target,
                time(),
                $result
            )
        );

        if ($bank) {
            foreach ($bankDetails as $_bank) {
                $db->query("INSERT INTO bank_log (`userid`, `amount`, `action`, `newbalance`, `timestamp`) VALUES (?, ?, ?, ?, ?)");
                $db->execute(
                    array(
                        $_bank['userid'],
                        $_bank['amount'],
                        'mdep',
                        $_bank['newbalance'],
                        time()
                    )
                );
            }
        }

        Take_Item(155, $user_class->id, 1);
        diefun('You sent ' . formatName($target) . ' a Heart and you both receive ' . $result);
    } else {
        echo Message('You do not have any Heart to send');
    }
}

if (isset($_POST['shamrock'])) {
    $target = security($_POST['target']);
    if ($target == $user_class->id)
        diefun("You can't send shamrocks to yourself");

    $receiver = new User($target);

    $howmany = check_items(157);
    if ($howmany) {

        $bank = false;
        $rnd = mt_rand(0, 1);
        switch ($rnd) {
            case 0:
                $rnd = mt_rand(0, 6);
                if ($rnd == 0) {
                    $amount = mt_rand(7500000, 15000000);
                } else {
                    $amount = mt_rand(500000, 3500000);
                }
                $reward = array(
                    'type' => "money",
                    'amount' => $amount
                );
                $sql = "UPDATE grpgusers SET `bank` = `bank` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $bank = true;
                $result = "$" . number_format($reward['amount'], 0);
                $bankDetails = array(
                    array(
                        'userid' => $user_class->id,
                        'amount' => $reward['amount'],
                        'newbalance' => $user_class->bank = $user_class->bank + $reward['amount']
                    ),
                    array(
                        'userid' => $receiver->id,
                        'amount' => $reward['amount'],
                        'newbalance' => $receiver->bank = $receiver->bank + $reward['amount']
                    )
                );
                break;
            case 1:
                $rnd = mt_rand(0, 6);
                if ($rnd == 0) {
                    $amount = mt_rand(4000, 6000);
                } else {
                    $amount = mt_rand(500, 3000);
                }
                $reward = array(
                    'type' => "points",
                    'amount' => $amount
                );
                $sql = "UPDATE grpgusers SET `points` = `points` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $result = number_format($reward['amount'], 0) . " points";
                break;
        }

        $rnd = (mt_rand(0, 1000) / 10);
        if ($rnd < 10) {
            $items = array(
                array(
                    'id' => 8,
                    'name' => 'a Mug Protection Pill'
                ),
                array(
                    'id' => 9,
                    'name' => 'an Attack Protection Pill'
                ),
                array(
                    'id' => 10,
                    'name' => 'a Double EXP Pill'
                )
            );
            $rnd = mt_rand(0, count($items) - 1);
            $item = $items[$rnd];
            Give_Item($item['id'], $user_class->id);
            Give_Item($item['id'], $target);
            $result = $item['name'];
            Send_Event($user_class->id, "You sent [-_USERID_-] a shamrock and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you a shamrock and you both receive " . $result, $user_class->id);

            $db->query("INSERT INTO hearts_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db->execute(
                array(
                    $user_class->id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(157, $user_class->id, 1);
            diefun('You sent ' . formatName($target) . ' a shamrock and you both receive ' . $result);
        }

        $db->query($sql);
        $db->execute(
            array(
                $user_class->id,
                $target
            )
        );
        Send_Event($user_class->id, "You sent [-_USERID_-] a shamrock and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you a shamrock and you both receive " . $result, $user_class->id);

        $db->query("INSERT INTO hearts_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
        $db->execute(
            array(
                $user_class->id,
                $target,
                time(),
                $result
            )
        );

        if ($bank) {
            foreach ($bankDetails as $_bank) {
                $db->query("INSERT INTO bank_log (`userid`, `amount`, `action`, `newbalance`, `timestamp`) VALUES (?, ?, ?, ?, ?)");
                $db->execute(
                    array(
                        $_bank['userid'],
                        $_bank['amount'],
                        'mdep',
                        $_bank['newbalance'],
                        time()
                    )
                );
            }
        }

        Take_Item(157, $user_class->id, 1);
        diefun('You sent ' . formatName($target) . ' a shamrock and you both receive ' . $result);
    } else {
        echo Message('You do not have any shamrocks to send');
    }
}

if (isset($_POST['bomb']) || isset($_POST['bombc'])) {

    $anon_cost = 0;

    if (isset($_POST['bomb'])) {
        $target = security($_POST['target']);
        if ($target == $user_class->id)
            diefun("You can't target yourself");

        $target_player = new User($target);
        if ($target_player->hospital > 0)
            diefun("You can't bomb someone who is in hospital");
    }

    if ($_POST['anon'] == 1) {
        if ($user_class->money < $anon_cost)
            diefun('For an anonymous attack you need $' . number_format($anon_cost, 0));

        // $db->query("UPDATE grpgusers SET money = money - ? WHERE id = ?");
        // $db->execute(
        //     array(
        //         $anon_cost,
        //         $user_class->id
        //     )
        // );
    }

    $anon = ($_POST['anon'] == 1) ? 'abombed' : 'bombed';
    $who = ($_POST['anon'] == 1) ? 'Someone' : '[-_USERID_-]';

    $item = security($_POST['item']);

    if ($user_class->jail > 0)
        diefun("You can't do that while in jail");

    if ($user_class->hospital > 0)
        diefun("You can't do that while in hospital");

    $item_stats = array(
        151 => array(
            'hospital' => 600,
            'exp' => 5000
        ),
        152 => array(
            'hospital' => 1200,
            'exp' => 8000
        ),
        154 => array(
            'hospital' => 600,
            'exp' => 1000
        )
    );

    $city_bomb = 154;

    $howmany = check_items($item);
    if ($howmany) {

        if ($item == $city_bomb) {
            // City Bomb


            $active = time() - 3600;
            $db->query("SELECT g.id, username, b.protection, g.money FROM grpgusers g LEFT JOIN bomb_protections b ON g.id = b.user_id WHERE hospital = 0 AND city = ? AND lastactive >= ? AND g.id != ? AND g.admin != 1");
            $db->execute(
                array(
                    $user_class->city,
                    $active,
                    $user_class->id
                )
            );
            $players_in_city = $db->fetch_row();
            $total = count($players_in_city);

            foreach ($players_in_city as $player) {
                if ($player['protection'] == 1) {
                    $db->query("DELETE FROM bomb_protections WHERE user_id = ?");
                    $db->execute(
                        array(
                            $player['id']
                        )
                    );
                    Send_Event($player, "{$who} detonated a city bomb, luckily you had bomb protection and avoided the blast.", $user_class->id);
                } else {
                    $players[] = $player['id'];
                    $money = 0;
                    $money = floor($player['money'] * 0.5);

                    $total_money += max($money, 0);

                    $db->query("UPDATE grpgusers SET money = money - ?, hospital = ?, hwho = ?, hhow = ? WHERE id = ?");
                    $db->execute(
                        array(
                            $money,
                            $item_stats[$item]['hospital'],
                            $user_class->id,
                            $anon,
                            $player['id']
                        )
                    );
                    Send_Event($player['id'], "{$who} blew you up with a city bomb, you are now in hospital for " . $item_stats[$item]['hospital'] / 60 . " minutes and they stole $" . prettynum($money) . ".", $user_class->id);
                }
            }



            $db->query("INSERT INTO bomb_log (attacker, defender, anon, success, item_id, `time`) VALUES (?, ?, ?, ?, ?, ?)");
            $db->execute(
                array(
                    $user_class->id,
                    serialize($players),
                    $_POST['anon'],
                    1,
                    $item,
                    time()
                )
            );

            $exp = count($players) * $item_stats[$item]['exp'];
            Send_Event($user_class->id, "You detonated a city bomb and killed " . prettynum(count($players)) . " players, gained " . prettynum($exp) . " exp and stole $" . prettynum($total_money) . ".");

            $outcome = "You detonated a city bomb and killed " . prettynum(count($players)) . " players, gained " . prettynum($exp) . " exp and stole $" . prettynum($total_money);

            $db->query("UPDATE grpgusers SET money = money + ?, `exp` = `exp` + ? WHERE id = ?");
            $db->execute(
                array(
                    $total_money,
                    $exp,
                    $user_class->id,
                )
            );
        } else {

            $db->query("SELECT * FROM bomb_protections WHERE user_id = ?");
            $db->execute(
                array(
                    $target
                )
            );
            $bomb_protection = $db->fetch_row(true);

            if ($bomb_protection) {

                $outcome = "You were unsucessful in attempting to blow up " . formatName($target) . " as they were using bomb protection";
                Send_Event($target, "{$who} tried to blow you up, luckily you had bomb protection and avoided the blast.", $user_class->id);
                $success = 'failed|protection';

                $db->query("DELETE FROM bomb_protections WHERE user_id = ?");
                $db->execute(
                    array(
                        $target
                    )
                );

                $chance = mt_rand(0, 1);
                if ($chance === 0) {
                    $db->query("UPDATE grpgusers SET hospital = ?, hhow = ?, hwho = ? WHERE id = ?");
                    $db->execute(
                        array(
                            $item_stats[$item]['hospital'] * 1.5,
                            $anon,
                            $user_class->id,
                            $user_class->id
                        )
                    );
                    $time = ($item_stats[$item]['hospital'] * 1.5) / 60;
                    $outcome .= " and ended up blowing up yourself in the process!<br> You are hospitalised for " . $time . " minutes";
                    $success = 'failed|protection|self';
                }
            } else {
                $chance = mt_rand(0, 1);
                if ($chance === 0) {
                    $outcome = "You failed to blow up " . formatName($target);
                    $success = 'failed';
                } else {
                    $time = $item_stats[$item]['hospital'] / 60;
                    $outcome = "You blew up " . formatName($target) . " and they are hospitalised for " . $time . " minutes";
                    Send_Event($target, "{$who} blew you up - you are hospitalised for " . $time . " minutes", $user_class->id);
                    $success = 'success';

                    $db->query("UPDATE grpgusers SET hospital = ?, hhow = ?, hwho = ? WHERE id = ?");
                    $db->execute(
                        array(
                            $item_stats[$item]['hospital'],
                            $anon,
                            $user_class->id,
                            $target
                        )
                    );

                    $chance = mt_rand(0, 1);
                    if ($chance === 0) {
                        $time = ($item_stats[$item]['hospital'] * 1.5) / 60;
                        $outcome .= "<br>However.. you didn't get away fast enough and you suffered major injuries<br>You are hospitalised for " . $time . " minutes";
                        $success = 'success|self';

                        $db->query("UPDATE grpgusers SET hospital = ?, hhow = ?, hwho = ? WHERE id = ?");
                        $db->execute(
                            array(
                                $item_stats[$item]['hospital'] * 1.5,
                                $anon,
                                $user_class->id,
                                $user_class->id
                            )
                        );
                    }
                }
            }
            $db->query("INSERT INTO bomb_log (attacker, defender, anon, success, item_id, `time`) VALUES (?, ?, ?, ?, ?, ?)");
            $db->execute(
                array(
                    $user_class->id,
                    $target,
                    $_POST['anon'],
                    $success,
                    $item,
                    time()
                )
            );
        }

        Take_Item($item, $user_class->id, 1);
        echo Message($outcome);
    } else {
        diefun('Sorry but you do not have that item');
    }
}

if (isset($_GET['use'])) {
    $id = security($_GET['use']);
    $howmany = check_items($id);
    if ($howmany) {
        switch ($id) {
            case 4:
                $db->query("UPDATE grpgusers SET awake = ? WHERE id = ?");
                $db->execute(array(
                    $user_class->maxawake,
                    $user_class->id
                ));
                echo Message("You successfully used an awake pill to refill your awake to 100%.");
                break;
            case 8:
                $db->query("UPDATE grpgusers SET mprotection =  unix_timestamp() + 3600 WHERE id = ?");
                $db->execute(array(
                    $user_class->id
                ));
                echo Message("You are now protected from mugs for 1 hour.");
                break;

            case 9:
                $db->query("UPDATE grpgusers SET aprotection =  unix_timestamp() + 3600 WHERE id = ?");
                $db->execute(array(
                    $user_class->id
                ));
                echo Message("You are now protected from attacks for 1 hour.");
                break;

            case 10:
                $db->query("UPDATE grpgusers SET exppill =  unix_timestamp() + 3600 WHERE id = ?");
                $db->execute(array(
                    $user_class->id
                ));
                echo Message("You will receive double exp on crimes for 1 hour.");
                break;

            case 11:
            case 12:
            case 13:
            case 14:
                if ($user_class->purehp >= $user_class->puremaxhp && !$user_class->hospital)
                    diefun("You already have full HP and are not in the hospital.");

                if ($user_class->hhow == "bombed" || $user_class->hhow == "cbombed" || $user_class->hhow == "abombed")
                    diefun("These won't help you when you are in bits.. you are going to have to wait it out.");

                $db->query("SELECT * FROM items WHERE id = ?");
                $db->execute(array(
                    $id
                ));
                $row = $db->fetch_row(true);
                $hosp = floor(($user_class->hospital / 100) * $row['reduce']);
                $newhosp = $user_class->hospital - $hosp;
                $newhosp = ($newhosp < 0) ? 0 : $newhosp;
                $hp = floor(($user_class->puremaxhp / 4) * $row['heal']);
                $hp = $user_class->purehp + $hp;
                $hp = ($hp > $user_class->puremaxhp) ? $user_class->puremaxhp : $hp;
                $db->query("UPDATE grpgusers SET hospital = ?, hp = ? WHERE id = ?");
                $db->execute(array(
                    $newhosp,
                    $hp,
                    $user_class->id
                ));
                echo Message("You successfully used a {$row['itemname']}.");
                break;
            case 27:
                druggie(0);
                echo Message("You successfully used some Meth. Your speed has been increased for 15 minutes.");
                break;
            case 28:
                druggie(1);
                echo Message("You successfully used some Adrenalin. Your defense has been increased for 15 minutes.");
                break;
            case 29:
                druggie(2);
                echo Message("You successfully used some PCP. Your strength has been increased for 15 minutes.");
                break;
            case 38:
                if (empty($_GET['cityid'])) {
                    $db->query("SELECT id, name, levelreq FROM cities WHERE pres = 0 ORDER BY levelreq DESC");
                    $db->execute();
                    $rows = $db->fetch_row();
                    $opts = "";
                    foreach ($rows as $row)
                        $opts .= "<option value='{$row['id']}'>{$row['name']} (LVL: {$row['levelreq']})</option>";
                    echo '<form method="get">';
                    echo '<select name="cityid">';
                    echo $opts;
                    echo '</select>';
                    echo '<input type="hidden" name="use" value="38" />';
                    echo '<input type="submit" value="Move to City" />';
                    echo '</form>';
                    diefun();
                } else {
                    $cid = security($_GET['cityid']);
                    $db->query("SELECT * FROM cities WHERE id = ? AND pres = 0");
                    $db->execute(array(
                        $cid
                    ));
                    if ($db->fetch_row()) {
                        $db->query("UPDATE grpgusers SET city = ? WHERE id = ?");
                        $db->execute(array(
                            $cid,
                            $user_class->id
                        ));
                        echo Message("You have moved cities for free!");
                    } else
                        diefun("City does not exist.");
                }
                break;
            case 42:
                $randnum = rand(0, 100);
                if ($randnum == 1)
                    $randpoints = rand(5000, 30000);
                elseif ($randnum >= 2 && $randnum <= 15)
                    $randpoints = rand(2000, 5000);
                elseif ($randnum >= 16 && $randnum <= 30)
                    $randpoints = rand(1000, 3000);
                elseif ($randnum >= 31 && $randnum <= 65)
                    $randpoints = rand(500, 2000);
                else
                    $randpoints = rand(250, 2000);
                $user_class->points += $randpoints;
                $db->query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $db->execute(array(
                    $randpoints,
                    $user_class->id
                ));
                $jp = ($randpoints >= 10000) ? "You hit the <span style='color:red;font-weight:bold;'>Jackpot</span>! " : "";
                echo Message("{$jp}You have taken <span style='color:#00ff00;font-weight:bold;'>$randpoints</span> Points from the Mystery Box.");
                break;
            case 51:
                add_rm_days(30, 150000, 750);
                echo Message("You have added 30 RM days to your account.");
                break;
            case 103:
                add_rm_days(60, 300000, 1500);
                echo Message("You have added 60 RM days to your account.");
                break;
            case 104:
                add_rm_days(90, 450000, 2500);
                echo Message("You have added 90 RM days to your account.");
                break;
            case 151:
            case 152:
                $db->query("SELECT * FROM items WHERE id = ?");
                $db->execute(
                    array(
                        $id
                    )
                );
                $item = $db->fetch_row(true);
                $item_name = $item['itemname'];
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $gang_id = ($user_class->gang != 0) ? $user_class->gang : -1;
                $db->query("SELECT id, username FROM grpgusers WHERE id != ? AND hospital = 0 AND jail = 0 AND gang != ? AND admin != 1 ORDER BY username");
                $db->execute(
                    array(
                        $user_class->id,
                        $gang_id
                    )
                );
                $rows = $db->fetch_row();
                echo '<p>You are about to use a ' . item_popup($item_name, $id) . '<br>Please choose your target below:</p>';
                echo '<form method="POST" action="?">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option value='{$row['id']}'>{$row['username']}</option>";
                }
                echo '</select><br><br>';
                echo '<input type="hidden" name="item" value="' . $id . '" />';
                echo '<label for="anon">Anonymous: (Cost: $0) </label><br>';
                echo '<select name="anon" id="anon">';
                echo '<option value="0">No</option>';
                echo '<option value="1">Yes</option>';
                echo '</select><br/><br/>';
                echo '<input type="submit" name="bomb" value="Bomb Player" />';
                echo '</form>';
                echo '<h4>There is a chance that you may fail and end up blowing up yourself!</h4>';
                echo '</div>';
                diefun();
                break;
            case 154:
                $db->query("SELECT * FROM items WHERE id = ?");
                $db->execute(
                    array(
                        $id
                    )
                );
                $item = $db->fetch_row(true);
                $item_name = $item['itemname'];
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                echo '<p>You are about to use a ' . item_popup($item_name, $id) . '<br>Please confirm your action below:</p>';
                echo '<form method="POST" action="?">';
                echo '<input type="hidden" name="item" value="' . $id . '" />';
                echo '<label for="anon">Anonymous: </label>';
                echo '<select name="anon" id="anon">';
                echo '<option value="0">No</option>';
                echo '<option value="1">Yes</option>';
                echo '</select><br/><br/>';
                echo '<input type="submit" name="bombc" value="Bomb City" />';
                echo '</form>';
                echo '<h4>There is a chance that you may fail and end up blowing up yourself aswell!</h4>';
                echo '</div>';
                diefun();
                break;
            case 155:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db->query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db->execute(
                    array(
                        $user_class->id,
                    )
                );
                $rows = $db->fetch_row();
                echo '<p>Who do you wish to send a Heart to?</p>';
                echo '<form method="POST" action="?">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option value='{$row['id']}'>{$row['username']} (ID: {$row['id']})</option>";
                }
                echo '</select><br><br>';
                echo '<input type="submit" name="heart" value="Send" />';
                echo '</form>';
                echo '<h4>You will both recieve a random reward!</h4>';
                echo '</div>';
                diefun('');
                break;
            case 157:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db->query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db->execute(
                    array(
                        $user_class->id,
                    )
                );
                $rows = $db->fetch_row();
                echo '<p>Who do you wish to send a shamrock to?</p>';
                echo '<form method="POST" action="?">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option value='{$row['id']}'>{$row['username']}</option>";
                }
                echo '</select><br><br>';
                echo '<input type="submit" name="shamrock" value="Send" />';
                echo '</form>';
                echo '<h4>You will both recieve a random reward!</h4>';
                echo '</div>';
                diefun('');
        }
        Take_Item($id, $user_class->id);
    }
}
function add_rm_days($days, $money, $points)
{
    global $db, $user_class;
    $db->query("UPDATE grpgusers SET rmdays = rmdays + ?, bank = bank + $money, points = points + $points WHERE id = ?");
    $db->execute(array(
        $days,
        $user_class->id
    ));
}
function druggie($num)
{
    global $db, $user_class;
    $drugs = explode("|", $user_class->drugs);
    $drugs[0] = !empty($drugs[0]) ? $drugs[0] : 0;
    $drugs[1] = !empty($drugs[1]) ? $drugs[1] : 0;
    $drugs[2] = !empty($drugs[2]) ? $drugs[2] : 0;
    $drugs[$num] = time();
    $db->query("UPDATE grpgusers SET drugs = ? WHERE id = ?");
    $db->execute(array(
        implode("|", $drugs),
        $user_class->id
    ));
}
if (check_items(105) || check_items(106) || check_items(107) || $user_class->eqweapon == 105 || $user_class->eqarmor == 106 || $user_class->eqshoes == 107)
    echo '<center><br /><a href="customitems.php" style="color:#00ff00;font-weight:bold;font-size:20px;"> &gt; You can rename your custom items by clicking this link. &lt; </a><br /><br /></center>';

?>

    <center>
    <div style="background-image:url('/images/inventory2.png');width:350px;height:438px;position:relative;">
        <?php if ($user_class->eqweapon != 0) { ?>
            <div style='position:absolute;left:17px;top:142px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><a href='equip.php?unequip=weapon'><img src="<?= $user_class->weaponimg ?>" alt="<?= $user_class->weaponname ?>" title="<?= $user_class->weaponname ?>" border="1"></a><br />[<a href='equip.php?unequip=weapon'>unequip</a>]</div>
        <?php } else { ?>
            <div style='position:absolute;left:63px;top:182px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><b>x</b></div>
        <?php } ?>

        <?php if ($user_class->eqarmor != 0) { ?>
            <div style='position:absolute;left:126px;top:142px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><a href='equip.php?unequip=armor'><img src="<?= $user_class->armorimg ?>" alt="<?= $user_class->armorimg ?>" title="<?= $user_class->armorname ?>" border="1"></a><br />[<a href='equip.php?unequip=armor'>unequip</a>]</div>
        <?php } else { ?>
            <div style='position:absolute;left:173px;top:182px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><b>x</b></div>
        <?php } ?>

        <?php if ($user_class->eqshoe != 0) { ?>
            <div style='position:absolute;left:126px;top:326px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><a href='equip.php?unequip=shoes'><img src="<?= $user_class->shoeimg ?>" alt="<?= $user_class->shoename ?>" title="<?= $user_class->shoename ?>" border="1"></a><br />[<a href='equip.php?unequip=shoes'>unequip</a>]</div>
        <?php } else { ?>
            <div style='position:absolute;left:173px;top:363px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><b>x</b></div>
        <?php } ?>

        <?php if ($user_class->eqgloves != 0) { ?>
            <div style='position:absolute;left:236px;top:142px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><a href='equip.php?unequip=gloves'><img src="<?= $user_class->glovesimg ?>" alt="<?= $user_class->glovesname ?>" title="<?= $user_class->glovesname ?>" border="1"></a><br />[<a href='equip.php?unequip=gloves'>unequip</a>]</div>
        <?php } else { ?>
            <div style='position:absolute;left:283px;top:182px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><b>x</b></div>
        <?php } ?>

        <?php if ($user_class->eqhat != 0) { ?>
            <div style='position:absolute;left:126px;top:18px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><a href='equip.php?unequip=hat'><img src="<?= $user_class->hatimg ?>" alt="<?= $user_class->hatname ?>" title="<?= $user_class->hatname ?>" border="1"></a><br />[<a href='equip.php?unequip=hat'>unequip</a>]</div>
        <?php } else { ?>
            <div style='position:absolute;left:175px;top:58px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><b>x</b></div>
        <?php } ?>

        <?php if (($user_class->eqweapon == 12) && ($user_class->eqarmor == 70) && ($user_class->eqshoe == 110) && ($user_class->eqgloves == 150) && ($user_class->eqhat == 134)) { ?>
            <div style='position:absolute;left:240px;top:30px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="images/set_street_punk.png" alt="Street Punk FULL Set" title="Street Punk FULL Set" border="0"></div>
        <?php } elseif (($user_class->eqweapon == 17) && ($user_class->eqarmor == 89) && ($user_class->eqshoe == 116) && ($user_class->eqgloves == 156) && ($user_class->eqhat == 130)) { ?>
            <div style='position:absolute;left:240px;top:30px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="images/set_silent_assassin.png" alt="Silent Assassin FULL Set" title="Silent Assassin FULL Set" border="0"></div>
        <?php } elseif (($user_class->eqweapon == 29) && ($user_class->eqarmor == 80) && ($user_class->eqshoe == 113) && ($user_class->eqgloves == 159) && ($user_class->eqhat == 133)) { ?>
            <div style='position:absolute;left:240px;top:30px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="images/set_classic_gangster.png" alt="Classic Gangster FULL Set" title="Classic Gangster FULL Set" border="0"></div>
        <?php } elseif (($user_class->eqweapon == 27) && ($user_class->eqarmor == 82) && ($user_class->eqshoe == 119) && ($user_class->eqgloves == 158) && ($user_class->eqhat == 138)) { ?>
            <div style='position:absolute;left:240px;top:30px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="images/set_combat_marine.png" alt="Combat Marine FULL Set" title="Combat Marine FULL Set" border="0"></div>
        <?php } elseif (($user_class->eqweapon == 48) && ($user_class->eqarmor == 90) && ($user_class->eqshoe == 120) && ($user_class->eqgloves == 160) && ($user_class->eqhat == 140)) { ?>
            <div style='position:absolute;left:240px;top:30px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="images/set_halloween.png" alt="Halloween FULL Set" title="Halloween FULL Set" border="0"></div>
        <?php } elseif (($user_class->eqweapon == 49) && ($user_class->eqarmor == 91) && ($user_class->eqshoe == 121) && ($user_class->eqgloves == 161) && ($user_class->eqhat == 141)) { ?>
            <div style='position:absolute;left:240px;top:30px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="images/set_valentines_day.png" alt="Valentine's Day FULL Set" title="Valentine's Day FULL Set" border="0"></div>
        <?php } elseif (($user_class->eqweapon == 50) && ($user_class->eqarmor == 92) && ($user_class->eqshoe == 122) && ($user_class->eqgloves == 162) && ($user_class->eqhat == 142)) { ?>
            <div style='position:absolute;left:240px;top:30px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="images/set_christmas.png" alt="Christmas FULL Set" title="Christmas FULL Set" border="0"></div>
        <?php } ?>

        <?php

            // $resultcar = $mysql->query("SELECT carid FROM `cars` WHERE `userid` = '".$user_class->id."' and `isonmarket` = '0' ORDER BY `carid` DESC LIMIT 1");
            // $workedcar = mysql_fetch_array($resultcar);
            // if ($workedcar['carid'] != "") {
            //     $resultcar2 = $mysql->query("SELECT * FROM carlot WHERE id = '".$workedcar['carid']."'");
            //     $workedcar2 = mysql_fetch_array($resultcar2);
            if (1 == 2) {
        ?>
            <div style='position:absolute;left:236px;top:326px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="<?= $workedcar2['image'] ?>" alt="<?= $workedcar2['name'] ?>" title="<?= $workedcar2['name'] ?>" border="0"></div>
        <?php  } else { ?>
            <div style='position:absolute;left:283px;top:357px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><b>x</b></div>
        <?php } ?>

        <?php

            // $resultpet = $mysql->query("SELECT type FROM `grpgpets` WHERE `userid` = '".$user_class->id."' and `isonmarket` = '0' and `leashed` = '1'");
            // $workedpet = mysql_fetch_array($resultpet);
            // if ($workedpet['type'] != "") {
            //     $resultpet2 = $mysql->query("SELECT * FROM petstore WHERE name = '".$workedpet['type']."'");
            //     $workedpet2 = mysql_fetch_array($resultpet2);
            if (1 == 2) {
        ?>
            <div style='position:absolute;left:18px;top:326px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><img src="<?= $workedpet2['image'] ?>" alt="<?= $workedpet2['name'] ?>" title="<?= $workedpet2['name'] ?>" border="0"></div>
        <?php  } else { ?>
            <div style='position:absolute;left:63px;top:357px;filter:alpha(opacity=85);-moz-opacity:0.85;opacity:0.85;'><b>x</b></div>
        <?php } ?>

    </div>
    </center>

<?php


$db->query("SELECT inv.*, it.*, c.name overridename, c.image overrideimage FROM inventory inv JOIN items it ON inv.itemid = it.id LEFT JOIN customitems c ON it.id = c.itemid AND c.userid = inv.userid WHERE inv.userid = ?");
$db->execute(array(
    $user_class->id
));
$rows = $db->fetch_row();
foreach ($rows as $row) {
    $subtype = '';
    if (!empty($row['overrideimage']) || !empty($row['overridename'])) {
        $row['image'] = $row['overrideimage'];
        $row['itemname'] = $row['overridename'];
    }
    $sell = ($row['cost'] > 0) ? "<a class='button-sm' href='sellitem.php?id=" . $row['id'] . "'>Sell</a>" : "";
    if ($row['offense'] > 0 && $row['rare'] == 0)
        $type = 'weapon';
    elseif ($row['defense'] > 0 && $row['rare'] == 0)
        $type = 'armor';
    elseif ($row['speed'] > 0 && $row['rare'] == 0)
        $type = 'shoes';
    elseif ($row['rare'] == 1) {
        $type = 'rare';
        if ($row['offense'])
            $subtype = 'weapon';
        if ($row['defense'])
            $subtype = 'armor';
        if ($row['speed'])
            $subtype = 'shoes';
    } else
        $type = 'consumable';
    gendivs($row, $type, $sell, $subtype);
}
$db->query("SELECT * FROM gang_loans gl JOIN items i ON gl.item = i.id WHERE idto = ?");
$db->execute(array(
    $user_class->id
));
$rows = $db->fetch_row();
foreach ($rows as $row) {
    if ($row['offense'] > 0 && $row['rare'] == 0)
        $type = 'weapon';
    elseif ($row['defense'] > 0 && $row['rare'] == 0)
        $type = 'armor';
    elseif ($row['speed'] > 0 && $row['rare'] == 0)
        $type = 'shoes';
    elseif ($row['rare'] == 1) {
        $type = 'rare';
        if ($row['offense'])
            $subtype = 'weapon';
        if ($row['defense'])
            $subtype = 'armor';
        if ($row['speed'])
            $subtype = 'shoes';
    } else
        $type = 'consumable';

    gendivs($row, 'loans', null, ($subtype != '') ? $subtype : $type, 1);
}
// if ($user_class->id == 1) {
echo '<div class="floaty"><p class="text-14">Specials</p>';
if ($user_class->donate_token > 0) {
    echo image_popup('css/newgame/items/donate_boost.png', 156) . '<br/>';
    echo '<span class="text-14">x' . $user_class->donate_token . '</span><br/>';
    echo '<a class="text-14 text-yellow" href="rmstore.php">Boost Donation</a><br/><br/>';
    echo '<a class="text-14 text-yellow" href="inventory.php?exchangetoken">Exchange x1 for 1,000 Points</a>';
}
echo '</div>';
//}
$master = array(
    'Weapons' => 'weapon',
    'Armor' => 'armor',
    'Shoes' => 'shoes',
    'Gang Loans' => 'loans',
    'Rares' => 'rare',
    'Consumables' => 'consumable'
);
foreach ($master as $header => $var)
    if (isset($$var)) {
        // genHead($header);
        echo '<div class="floaty">';
        echo '<b>' . $header . '</b>';
        echo '<hr />';
        echo '<div class="flexcont" style="text-align:center;position: relative;flex-flow:row wrap;">';
        echo $$var;
        echo '</div>';
        echo '</div>';
    }
include 'footer.php';
function gendivs($row, $type, $sell = null, $subtype = null, $loan = null)
{
    global $$type;
    $$type .= '<div class="flexele" style="flex-basis:25%;margin-bottom:12px;margin-top:12px;">';
    $$type .= image_popup($row['image'], $row['id']);
    $$type .= '<br />';
    $$type .= item_popup($row['itemname'], $row['id']) . ' [x' . $row['quantity'] . ']';
    $$type .= '<br />';
    $$type .= '<br />';
    if ($row['cost'] > 0) {
        $$type .= prettynum($row['cost'], 1);
        $$type .= '<br />';
        $$type .= '<br />';
    }
    $$type .= $sell;
    if ($type == "consumable" && $row['id'] != 155 && $row['id'] != 157)
        $$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Use</a> ';
    if (!$loan && $row['id'] != 155 && $row['id'] != 157)
        $$type .= ' <a class="button-sm" href="putonmarket.php?id=' . $row['id'] . '">Market</a> ';

    if ($row['id'] != 155 && $row['id'] != 157)
        $$type .= ' <a class="button-sm" href="senditem.php?id=' . $row['id'] . '">Send</a> ';

    if ($row['id'] == 155)
        $$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Share The Love</a> ';

    if ($row['id'] == 157)
        $$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Share</a> ';

    echo "<div style='display:none;'>";
    echo $subtype . " " . $type;
    echo "</div>";

    if (in_array($type, array('weapon', 'armor', 'shoes')) || in_array($subtype, array('weapon', 'armor', 'shoes'))) {
        $$type .= ' <a class="button-sm" href="equip.php?eq=';
        $$type .= (!empty($subtype)) ? $subtype : $type;
        $$type .= '&id=' . $row['id'];
        $$type .= ($loan) ? '&loaned=1' : '';
        $$type .= '">Equip</a> ';
    }
    $$type .= '</div>';
}
