<?php
include 'header.php';
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// if ($user_class->id == 174) {
//     $user_class = new User(84);
// }

if (isset($_GET['exchangetoken'])) {
    if ($user_class-&gt;donate_token &gt; 0) {
        $db-&gt;query("UPDATE grpgusers SET donate_token = donate_token - 1, points = points + 1000 WHERE id = ?");
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
            )
        );
        $message = "You have exchanged a " . item_popup('Donation Boost Token', 156) . " for 1,000 Points";
        Send_Event($user_class-&gt;id, "You have exchanged a Donation Boost Token for 1,000 Points.", $user_class-&gt;id);
        diefun($message);
    } else {
        diefun('Sorry you do not have any tokens to exchange');
    }
}

if (isset($_POST['heart'])) {
    $target = security($_POST['target']);
    if ($target == $user_class-&gt;id)
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
                    'type' =&gt; "money",
                    'amount' =&gt; $amount
                );
                $sql = "UPDATE grpgusers SET `bank` = `bank` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $bank = true;
                $result = "$" . number_format($reward['amount'], 0);
                $bankDetails = array(
                    array(
                        'userid' =&gt; $user_class-&gt;id,
                        'amount' =&gt; $reward['amount'],
                        'newbalance' =&gt; $user_class-&gt;bank = $user_class-&gt;bank + $reward['amount']
                    ),
                    array(
                        'userid' =&gt; $receiver-&gt;id,
                        'amount' =&gt; $reward['amount'],
                        'newbalance' =&gt; $receiver-&gt;bank = $receiver-&gt;bank + $reward['amount']
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
                    'type' =&gt; "points",
                    'amount' =&gt; $amount
                );
                $sql = "UPDATE grpgusers SET `points` = `points` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $result = number_format($reward['amount'], 0) . " points";
                break;
        }

        $rnd = (mt_rand(0, 1000) / 10);
        if ($rnd &lt; 10) {
            $items = array(
                array(
                    'id' =&gt; 8,
                    'name' =&gt; 'a Mug Protection Pill'
                ),
                array(
                    'id' =&gt; 9,
                    'name' =&gt; 'an Attack Protection Pill'
                ),
                array(
                    'id' =&gt; 10,
                    'name' =&gt; 'a Double EXP Pill'
                )
            );
            $rnd = mt_rand(0, count($items) - 1);
            $item = $items[$rnd];
            Give_Item($item['id'], $user_class-&gt;id);
            Give_Item($item['id'], $target);
            $result = $item['name'];
            Send_Event($user_class-&gt;id, "You sent [-_USERID_-] a Heart and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you a Heart and you both receive " . $result, $user_class-&gt;id);

            $db-&gt;query("INSERT INTO hearts_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db-&gt;execute(
                array(
                    $user_class-&gt;id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(155, $user_class-&gt;id, 1);
            diefun('You sent ' . formatName($target) . ' a Heart and you both receive ' . $result);
        }

        $db-&gt;query($sql);
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
                $target
            )
        );
        Send_Event($user_class-&gt;id, "You sent [-_USERID_-] a Heart and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you a Heart and you both receive " . $result, $user_class-&gt;id);

        $db-&gt;query("INSERT INTO hearts_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
                $target,
                time(),
                $result
            )
        );

        if ($bank) {
            foreach ($bankDetails as $_bank) {
                $db-&gt;query("INSERT INTO bank_log (`userid`, `amount`, `action`, `newbalance`, `timestamp`) VALUES (?, ?, ?, ?, ?)");
                $db-&gt;execute(
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

        Take_Item(155, $user_class-&gt;id, 1);
        diefun('You sent ' . formatName($target) . ' a Heart and you both receive ' . $result);
    } else {
        echo Message('You do not have any Heart to send');
    }
}

if (isset($_POST['shamrock'])) {
    $target = security($_POST['target']);
    if ($target == $user_class-&gt;id)
        diefun("You can't send shamrocks to yourself");

    $receiver = new User($target);

    $howmany = check_items(156);
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
                    'type' =&gt; "money",
                    'amount' =&gt; $amount
                );
                $sql = "UPDATE grpgusers SET `bank` = `bank` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $bank = true;
                $result = "$" . number_format($reward['amount'], 0);
                $bankDetails = array(
                    array(
                        'userid' =&gt; $user_class-&gt;id,
                        'amount' =&gt; $reward['amount'],
                        'newbalance' =&gt; $user_class-&gt;bank = $user_class-&gt;bank + $reward['amount']
                    ),
                    array(
                        'userid' =&gt; $receiver-&gt;id,
                        'amount' =&gt; $reward['amount'],
                        'newbalance' =&gt; $receiver-&gt;bank = $receiver-&gt;bank + $reward['amount']
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
                    'type' =&gt; "points",
                    'amount' =&gt; $amount
                );
                $sql = "UPDATE grpgusers SET `points` = `points` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $result = number_format($reward['amount'], 0) . " points";
                break;
        }

        $rnd = (mt_rand(0, 1000) / 10);
        if ($rnd &lt; 10) {
            $items = array(
                array(
                    'id' =&gt; 8,
                    'name' =&gt; 'a Mug Protection Pill'
                ),
                array(
                    'id' =&gt; 9,
                    'name' =&gt; 'an Attack Protection Pill'
                ),
                array(
                    'id' =&gt; 10,
                    'name' =&gt; 'a Double EXP Pill'
                )
            );
            $rnd = mt_rand(0, count($items) - 1);
            $item = $items[$rnd];
            Give_Item($item['id'], $user_class-&gt;id);
            Give_Item($item['id'], $target);
            $result = $item['name'];
            Send_Event($user_class-&gt;id, "You sent [-_USERID_-] a shamrock and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you a shamrock and you both receive " . $result, $user_class-&gt;id);

            $db-&gt;query("INSERT INTO shamrocks_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db-&gt;execute(
                array(
                    $user_class-&gt;id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(156, $user_class-&gt;id, 1);
            diefun('You sent ' . formatName($target) . ' a shamrock and you both receive ' . $result);
        }

        $db-&gt;query($sql);
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
                $target
            )
        );
        Send_Event($user_class-&gt;id, "You sent [-_USERID_-] a shamrock and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you a shamrock and you both receive " . $result, $user_class-&gt;id);

        $db-&gt;query("INSERT INTO shamrocks_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
                $target,
                time(),
                $result
            )
        );

        if ($bank) {
            foreach ($bankDetails as $_bank) {
                $db-&gt;query("INSERT INTO bank_log (`userid`, `amount`, `action`, `newbalance`, `timestamp`) VALUES (?, ?, ?, ?, ?)");
                $db-&gt;execute(
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

        Take_Item(156, $user_class-&gt;id, 1);
        diefun('You sent ' . formatName($target) . ' a shamrock and you both receive ' . $result);
    } else {
        echo Message('You do not have any shamrocks to send');
    }
}















if (isset($_POST['easter'])) {
    $target = security($_POST['target']);
    if ($target == $user_class-&gt;id)
        diefun("You can't send Easter Eggs to yourself");

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
                    'type' =&gt; "money",
                    'amount' =&gt; $amount
                );
                $sql = "UPDATE grpgusers SET `bank` = `bank` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $bank = true;
                $result = "$" . number_format($reward['amount'], 0);
                $bankDetails = array(
                    array(
                        'userid' =&gt; $user_class-&gt;id,
                        'amount' =&gt; $reward['amount'],
                        'newbalance' =&gt; $user_class-&gt;bank = $user_class-&gt;bank + $reward['amount']
                    ),
                    array(
                        'userid' =&gt; $receiver-&gt;id,
                        'amount' =&gt; $reward['amount'],
                        'newbalance' =&gt; $receiver-&gt;bank = $receiver-&gt;bank + $reward['amount']
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
                    'type' =&gt; "points",
                    'amount' =&gt; $amount
                );
                $sql = "UPDATE grpgusers SET `points` = `points` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $result = number_format($reward['amount'], 0) . " points";
                break;
        }

        $rnd = (mt_rand(0, 1000) / 10);
        if ($rnd &lt; 10) {
            $items = array(
                array(
                    'id' =&gt; 8,
                    'name' =&gt; 'a Mug Protection Pill'
                ),
                array(
                    'id' =&gt; 9,
                    'name' =&gt; 'an Attack Protection Pill'
                ),
                array(
                    'id' =&gt; 10,
                    'name' =&gt; 'a Double EXP Pill'
                )
            );
            $rnd = mt_rand(0, count($items) - 1);
            $item = $items[$rnd];
            Give_Item($item['id'], $user_class-&gt;id);
            Give_Item($item['id'], $target);
            $result = $item['name'];
            Send_Event($user_class-&gt;id, "You sent [-_USERID_-] a Easter Egg and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you a Easter Egg and you both receive " . $result, $user_class-&gt;id);

            $db-&gt;query("INSERT INTO easter_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db-&gt;execute(
                array(
                    $user_class-&gt;id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(157, $user_class-&gt;id, 1);
            diefun('You sent ' . formatName($target) . ' a Easter Egg and you both receive ' . $result);
        }

        $db-&gt;query($sql);
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
                $target
            )
        );
        Send_Event($user_class-&gt;id, "You sent [-_USERID_-] a Easter Egg and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you a Easter Egg and you both receive " . $result, $user_class-&gt;id);

        $db-&gt;query("INSERT INTO easter_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
                $target,
                time(),
                $result
            )
        );

        if ($bank) {
            foreach ($bankDetails as $_bank) {
                $db-&gt;query("INSERT INTO bank_log (`userid`, `amount`, `action`, `newbalance`, `timestamp`) VALUES (?, ?, ?, ?, ?)");
                $db-&gt;execute(
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

        Take_Item(157, $user_class-&gt;id, 1);
        diefun('You sent ' . formatName($target) . ' a Easter Egg and you both receive ' . $result);
    } else {
        echo Message('You do not have any Easter Egg to send');
    }
}







if (isset($_POST['fireworks'])) {
    $target = security($_POST['target']);
    if ($target == $user_class-&gt;id)
        diefun("You can't send Fireworks to yourself");

    $receiver = new User($target);

    $howmany = check_items(158);
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
                    'type' =&gt; "money",
                    'amount' =&gt; $amount
                );
                $sql = "UPDATE grpgusers SET `bank` = `bank` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $bank = true;
                $result = "$" . number_format($reward['amount'], 0);
                $bankDetails = array(
                    array(
                        'userid' =&gt; $user_class-&gt;id,
                        'amount' =&gt; $reward['amount'],
                        'newbalance' =&gt; $user_class-&gt;bank = $user_class-&gt;bank + $reward['amount']
                    ),
                    array(
                        'userid' =&gt; $receiver-&gt;id,
                        'amount' =&gt; $reward['amount'],
                        'newbalance' =&gt; $receiver-&gt;bank = $receiver-&gt;bank + $reward['amount']
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
                    'type' =&gt; "points",
                    'amount' =&gt; $amount
                );
                $sql = "UPDATE grpgusers SET `points` = `points` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $result = number_format($reward['amount'], 0) . " points";
                break;
        }

        $rnd = (mt_rand(0, 1000) / 10);
        if ($rnd &lt; 10) {
            $items = array(
                array(
                    'id' =&gt; 8,
                    'name' =&gt; 'a Mug Protection Pill'
                ),
                array(
                    'id' =&gt; 9,
                    'name' =&gt; 'an Attack Protection Pill'
                ),
                array(
                    'id' =&gt; 10,
                    'name' =&gt; 'a Double EXP Pill'
                )
            );
            $rnd = mt_rand(0, count($items) - 1);
            $item = $items[$rnd];
            Give_Item($item['id'], $user_class-&gt;id);
            Give_Item($item['id'], $target);
            $result = $item['name'];
            Send_Event($user_class-&gt;id, "You sent [-_USERID_-] Fireworks and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you Fireworks and you both receive " . $result, $user_class-&gt;id);

            $db-&gt;query("INSERT INTO fireworks_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db-&gt;execute(
                array(
                    $user_class-&gt;id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(158, $user_class-&gt;id, 1);
            diefun('You sent ' . formatName($target) . ' Fireworks and you both receive ' . $result);
        }

        $db-&gt;query($sql);
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
                $target
            )
        );
        Send_Event($user_class-&gt;id, "You sent [-_USERID_-] Fireworks and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you Fireworks and you both receive " . $result, $user_class-&gt;id);

        $db-&gt;query("INSERT INTO fireworks_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
                $target,
                time(),
                $result
            )
        );

        if ($bank) {
            foreach ($bankDetails as $_bank) {
                $db-&gt;query("INSERT INTO bank_log (`userid`, `amount`, `action`, `newbalance`, `timestamp`) VALUES (?, ?, ?, ?, ?)");
                $db-&gt;execute(
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

        Take_Item(158, $user_class-&gt;id, 1);
        diefun('You sent ' . formatName($target) . ' Fireworks and you both receive ' . $result);
    } else {
        echo Message('You do not have any Fireworks to send');
    }
}



if (isset($_POST['presents'])) {
    $target = security($_POST['target']);
    if ($target == $user_class-&gt;id)
        diefun("You can't send Christmas Presents to yourself");

    $receiver = new User($target);

    $howmany = check_items(167);
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
                    'type' =&gt; "money",
                    'amount' =&gt; $amount
                );
                $sql = "UPDATE grpgusers SET `bank` = `bank` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $bank = true;
                $result = "$" . number_format($reward['amount'], 0);
                $bankDetails = array(
                    array(
                        'userid' =&gt; $user_class-&gt;id,
                        'amount' =&gt; $reward['amount'],
                        'newbalance' =&gt; $user_class-&gt;bank = $user_class-&gt;bank + $reward['amount']
                    ),
                    array(
                        'userid' =&gt; $receiver-&gt;id,
                        'amount' =&gt; $reward['amount'],
                        'newbalance' =&gt; $receiver-&gt;bank = $receiver-&gt;bank + $reward['amount']
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
                    'type' =&gt; "points",
                    'amount' =&gt; $amount
                );
                $sql = "UPDATE grpgusers SET `points` = `points` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $result = number_format($reward['amount'], 0) . " points";
                break;
        }

        $rnd = (mt_rand(0, 1000) / 10);
        if ($rnd &lt; 10) {
            $items = array(
                array(
                    'id' =&gt; 8,
                    'name' =&gt; 'a Mug Protection Pill'
                ),
                array(
                    'id' =&gt; 9,
                    'name' =&gt; 'an Attack Protection Pill'
                ),
                array(
                    'id' =&gt; 10,
                    'name' =&gt; 'a Double EXP Pill'
                )
            );
            $rnd = mt_rand(0, count($items) - 1);
            $item = $items[$rnd];
            Give_Item($item['id'], $user_class-&gt;id);
            Give_Item($item['id'], $target);
            $result = $item['name'];
            Send_Event($user_class-&gt;id, "You sent [-_USERID_-] Christmas Presents and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you a Christmas Present and you both receive " . $result, $user_class-&gt;id);

            $db-&gt;query("INSERT INTO fireworks_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db-&gt;execute(
                array(
                    $user_class-&gt;id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(167, $user_class-&gt;id, 1);
            diefun('You sent ' . formatName($target) . ' a Christmas Present and you both receive ' . $result);
        }

        $db-&gt;query($sql);
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
                $target
            )
        );
        Send_Event($user_class-&gt;id, "You sent [-_USERID_-] a Christmas Present and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you a Christmas Present and you both receive " . $result, $user_class-&gt;id);

        $db-&gt;query("INSERT INTO fireworks_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
                $target,
                time(),
                $result
            )
        );

        if ($bank) {
            foreach ($bankDetails as $_bank) {
                $db-&gt;query("INSERT INTO bank_log (`userid`, `amount`, `action`, `newbalance`, `timestamp`) VALUES (?, ?, ?, ?, ?)");
                $db-&gt;execute(
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

        Take_Item(167, $user_class-&gt;id, 1);
        diefun('You sent ' . formatName($target) . ' a Christmas Present and you both receive ' . $result);
    } else {
        echo Message('You do not have any Christmas Presents to send');
    }
}









if (isset($_POST['rayz'])) {
    $target = security($_POST['target']);
    if ($target == $user_class-&gt;id)
        diefun("You can't send Rayz to yourself");

    $receiver = new User($target);

    $howmany = check_items(159);
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
                    'type' =&gt; "money",
                    'amount' =&gt; $amount
                );
                $sql = "UPDATE grpgusers SET `bank` = `bank` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $bank = true;
                $result = "$" . number_format($reward['amount'], 0);
                $bankDetails = array(
                    array(
                        'userid' =&gt; $user_class-&gt;id,
                        'amount' =&gt; $reward['amount'],
                        'newbalance' =&gt; $user_class-&gt;bank = $user_class-&gt;bank + $reward['amount']
                    ),
                    array(
                        'userid' =&gt; $receiver-&gt;id,
                        'amount' =&gt; $reward['amount'],
                        'newbalance' =&gt; $receiver-&gt;bank = $receiver-&gt;bank + $reward['amount']
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
                    'type' =&gt; "points",
                    'amount' =&gt; $amount
                );
                $sql = "UPDATE grpgusers SET `points` = `points` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $result = number_format($reward['amount'], 0) . " points";
                break;
        }

        $rnd = (mt_rand(0, 1000) / 10);
        if ($rnd &lt; 10) {
            $items = array(
                array(
                    'id' =&gt; 8,
                    'name' =&gt; 'a Mug Protection Pill'
                ),
                array(
                    'id' =&gt; 9,
                    'name' =&gt; 'an Attack Protection Pill'
                ),
                array(
                    'id' =&gt; 10,
                    'name' =&gt; 'a Double EXP Pill'
                )
            );
            $rnd = mt_rand(0, count($items) - 1);
            $item = $items[$rnd];
            Give_Item($item['id'], $user_class-&gt;id);
            Give_Item($item['id'], $target);
            $result = $item['name'];
            Send_Event($user_class-&gt;id, "You sent [-_USERID_-] Rayz and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you Rayz and you both receive " . $result, $user_class-&gt;id);

            $db-&gt;query("INSERT INTO hearts_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db-&gt;execute(
                array(
                    $user_class-&gt;id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(159, $user_class-&gt;id, 1);
            diefun('You sent ' . formatName($target) . ' Rayz and you both receive ' . $result);
        }

        $db-&gt;query($sql);
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
                $target
            )
        );
        Send_Event($user_class-&gt;id, "You sent [-_USERID_-] Rayz and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you Rayz and you both receive " . $result, $user_class-&gt;id);

        $db-&gt;query("INSERT INTO rayz_logs (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
                $target,
                time(),
                $result
            )
        );

        if ($bank) {
            foreach ($bankDetails as $_bank) {
                $db-&gt;query("INSERT INTO bank_log (`userid`, `amount`, `action`, `newbalance`, `timestamp`) VALUES (?, ?, ?, ?, ?)");
                $db-&gt;execute(
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

        Take_Item(159, $user_class-&gt;id, 1);
        diefun('You sent ' . formatName($target) . ' Rayz and you both receive ' . $result);
    } else {
        echo Message('You do not have any Rayz to send');
    }
}















if (isset($_POST['ghosts'])) {
    $target = security($_POST['target']);
    if ($target == $user_class-&gt;id)
        diefun("You can't send Ghosts to yourself");

    $receiver = new User($target);

    $howmany = check_items(165);
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
                    'type' =&gt; "money",
                    'amount' =&gt; $amount
                );
                $sql = "UPDATE grpgusers SET `bank` = `bank` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $bank = true;
                $result = "$" . number_format($reward['amount'], 0);
                $bankDetails = array(
                    array(
                        'userid' =&gt; $user_class-&gt;id,
                        'amount' =&gt; $reward['amount'],
                        'newbalance' =&gt; $user_class-&gt;bank = $user_class-&gt;bank + $reward['amount']
                    ),
                    array(
                        'userid' =&gt; $receiver-&gt;id,
                        'amount' =&gt; $reward['amount'],
                        'newbalance' =&gt; $receiver-&gt;bank = $receiver-&gt;bank + $reward['amount']
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
                    'type' =&gt; "points",
                    'amount' =&gt; $amount
                );
                $sql = "UPDATE grpgusers SET `points` = `points` + " . $reward['amount'] . " WHERE id IN (?, ?)";
                $result = number_format($reward['amount'], 0) . " points";
                break;
        }

        $rnd = (mt_rand(0, 1000) / 10);
        if ($rnd &lt; 10) {
            $items = array(
                array(
                    'id' =&gt; 8,
                    'name' =&gt; 'a Mug Protection Pill'
                ),
                array(
                    'id' =&gt; 9,
                    'name' =&gt; 'an Attack Protection Pill'
                ),
                array(
                    'id' =&gt; 10,
                    'name' =&gt; 'a Double EXP Pill'
                )
            );
            $rnd = mt_rand(0, count($items) - 1);
            $item = $items[$rnd];
            Give_Item($item['id'], $user_class-&gt;id);
            Give_Item($item['id'], $target);
            $result = $item['name'];
            Send_Event($user_class-&gt;id, "You sent [-_USERID_-] Ghost and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you Ghost and you both receive " . $result, $user_class-&gt;id);

            $db-&gt;query("INSERT INTO rayz_logs (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db-&gt;execute(
                array(
                    $user_class-&gt;id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(165, $user_class-&gt;id, 1);
            diefun('You sent ' . formatName($target) . ' Rayz and you both receive ' . $result);
        }

        $db-&gt;query($sql);
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
                $target
            )
        );
        Send_Event($user_class-&gt;id, "You sent [-_USERID_-] a Ghost and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you a Ghost and you both receive " . $result, $user_class-&gt;id);

        $db-&gt;query("INSERT INTO rayz_logs (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
        $db-&gt;execute(
            array(
                $user_class-&gt;id,
                $target,
                time(),
                $result
            )
        );

        if ($bank) {
            foreach ($bankDetails as $_bank) {
                $db-&gt;query("INSERT INTO bank_log (`userid`, `amount`, `action`, `newbalance`, `timestamp`) VALUES (?, ?, ?, ?, ?)");
                $db-&gt;execute(
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

        Take_Item(165, $user_class-&gt;id, 1);
        diefun('You sent ' . formatName($target) . ' a Ghost and you both receive ' . $result);
    } else {
        echo Message('You do not have any Ghosts to send');
    }
}








if (isset($_POST['bomb']) || isset($_POST['bombc'])) {

    $anon_cost = 0;

    if (isset($_POST['bomb'])) {
        $target = security($_POST['target']);
        if ($target == $user_class-&gt;id)
            diefun("You can't target yourself");

        $target_player = new User($target);
        if ($target_player-&gt;hospital &gt; 0)
            diefun("You can't bomb someone who is in hospital");
    }

    if ($_POST['anon'] == 1) {
        if ($user_class-&gt;money &lt; $anon_cost)
            diefun('For an anonymous attack you need $' . number_format($anon_cost, 0));

        // $db-&gt;query("UPDATE grpgusers SET money = money - ? WHERE id = ?");
        // $db-&gt;execute(
        //     array(
        //         $anon_cost,
        //         $user_class-&gt;id
        //     )
        // );
    }

    $anon = ($_POST['anon'] == 1) ? 'abombed' : 'bombed';
    $who = ($_POST['anon'] == 1) ? 'Someone' : '[-_USERID_-]';

    $item = security($_POST['item']);

    if ($user_class-&gt;jail &gt; 0)
        diefun("You can't do that while in jail");

    if ($user_class-&gt;hospital &gt; 0)
        diefun("You can't do that while in hospital");

    $item_stats = array(
        151 =&gt; array(
            'hospital' =&gt; 600,
            'exp' =&gt; 5000
        ),
        152 =&gt; array(
            'hospital' =&gt; 1200,
            'exp' =&gt; 8000
        ),
        154 =&gt; array(
            'hospital' =&gt; 600,
            'exp' =&gt; 1000
        )
    );

    $city_bomb = 154;

    $howmany = check_items($item);
    if ($howmany) {

        if ($item == $city_bomb) {
            // City Bomb


            $active = time() - 3600;
            $db-&gt;query("SELECT g.id, username, b.protection, g.money FROM grpgusers g LEFT JOIN bomb_protections b ON g.id = b.user_id WHERE hospital = 0 AND city = ? AND lastactive &gt;= ? AND g.id != ? AND g.admin != 1");
            $db-&gt;execute(
                array(
                    $user_class-&gt;city,
                    $active,
                    $user_class-&gt;id
                )
            );
            $players_in_city = $db-&gt;fetch_row();
            $total = count($players_in_city);

            foreach ($players_in_city as $player) {
                if ($player['protection'] == 1) {
                    $db-&gt;query("DELETE FROM bomb_protections WHERE user_id = ?");
                    $db-&gt;execute(
                        array(
                            $player['id']
                        )
                    );
                    Send_Event($player, "{$who} detonated a city bomb, luckily you had bomb protection and avoided the blast.", $user_class-&gt;id);
                } else {
                    $players[] = $player['id'];
                    $money = 0;
                    $money = floor($player['money'] * 0.5);

                    $total_money += max($money, 0);

                    $db-&gt;query("UPDATE grpgusers SET money = money - ?, hospital = ?, hwho = ?, hhow = ? WHERE id = ?");
                    $db-&gt;execute(
                        array(
                            $money,
                            $item_stats[$item]['hospital'],
                            $user_class-&gt;id,
                            $anon,
                            $player['id']
                        )
                    );
                    Send_Event($player['id'], "{$who} blew you up with a city bomb, you are now in hospital for " . $item_stats[$item]['hospital'] / 60 . " minutes and they stole $" . prettynum($money) . ".", $user_class-&gt;id);
                }
            }



            $db-&gt;query("INSERT INTO bomb_log (attacker, defender, anon, success, item_id, `time`) VALUES (?, ?, ?, ?, ?, ?)");
            $db-&gt;execute(
                array(
                    $user_class-&gt;id,
                    serialize($players),
                    $_POST['anon'],
                    1,
                    $item,
                    time()
                )
            );

            $exp = count($players) * $item_stats[$item]['exp'];
            Send_Event($user_class-&gt;id, "You detonated a city bomb and killed " . prettynum(count($players)) . " players, gained " . prettynum($exp) . " exp and stole $" . prettynum($total_money) . ".");

            $outcome = "You detonated a city bomb and killed " . prettynum(count($players)) . " players, gained " . prettynum($exp) . " exp and stole $" . prettynum($total_money);

            $db-&gt;query("UPDATE grpgusers SET money = money + ?, `exp` = `exp` + ? WHERE id = ?");
            $db-&gt;execute(
                array(
                    $total_money,
                    $exp,
                    $user_class-&gt;id,
                )
            );
        } else {

            $db-&gt;query("SELECT * FROM bomb_protections WHERE user_id = ?");
            $db-&gt;execute(
                array(
                    $target
                )
            );
            $bomb_protection = $db-&gt;fetch_row(true);

            if ($bomb_protection) {

                $outcome = "You were unsucessful in attempting to blow up " . formatName($target) . " as they were using bomb protection";
                Send_Event($target, "{$who} tried to blow you up, luckily you had bomb protection and avoided the blast.", $user_class-&gt;id);
                $success = 'failed|protection';

                $db-&gt;query("DELETE FROM bomb_protections WHERE user_id = ?");
                $db-&gt;execute(
                    array(
                        $target
                    )
                );

                $chance = mt_rand(0, 1);
                if ($chance === 0) {
                    $db-&gt;query("UPDATE grpgusers SET hospital = ?, hhow = ?, hwho = ? WHERE id = ?");
                    $db-&gt;execute(
                        array(
                            $item_stats[$item]['hospital'] * 1.5,
                            $anon,
                            $user_class-&gt;id,
                            $user_class-&gt;id
                        )
                    );
                    $time = ($item_stats[$item]['hospital'] * 1.5) / 60;
                    $outcome .= " and ended up blowing up yourself in the process!<br/> You are hospitalised for " . $time . " minutes";
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
                    Send_Event($target, "{$who} blew you up - you are hospitalised for " . $time . " minutes", $user_class-&gt;id);
                    $success = 'success';

                    $db-&gt;query("UPDATE grpgusers SET hospital = ?, hhow = ?, hwho = ? WHERE id = ?");
                    $db-&gt;execute(
                        array(
                            $item_stats[$item]['hospital'],
                            $anon,
                            $user_class-&gt;id,
                            $target
                        )
                    );

                    $chance = mt_rand(0, 1);
                    if ($chance === 0) {
                        $time = ($item_stats[$item]['hospital'] * 1.5) / 60;
                        $outcome .= "<br/>However.. you didn't get away fast enough and you suffered major injuries<br/>You are hospitalised for " . $time . " minutes";
                        $success = 'success|self';

                        $db-&gt;query("UPDATE grpgusers SET hospital = ?, hhow = ?, hwho = ? WHERE id = ?");
                        $db-&gt;execute(
                            array(
                                $item_stats[$item]['hospital'] * 1.5,
                                $anon,
                                $user_class-&gt;id,
                                $user_class-&gt;id
                            )
                        );
                    }
                }
            }
            $db-&gt;query("INSERT INTO bomb_log (attacker, defender, anon, success, item_id, `time`) VALUES (?, ?, ?, ?, ?, ?)");
            $db-&gt;execute(
                array(
                    $user_class-&gt;id,
                    $target,
                    $_POST['anon'],
                    $success,
                    $item,
                    time()
                )
            );
        }

        Take_Item($item, $user_class-&gt;id, 1);
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
                $db-&gt;query("UPDATE grpgusers SET awake = ? WHERE id = ?");
                $db-&gt;execute(array(
                    $user_class-&gt;maxawake,
                    $user_class-&gt;id
                ));
                echo Message("You successfully used an awake pill to refill your awake to 100%.");
                break;
            case 8:
                $db-&gt;query("UPDATE grpgusers SET mprotection =  unix_timestamp() + 3600 WHERE id = ?");
                $db-&gt;execute(array(
                    $user_class-&gt;id
                ));
                echo Message("You are now protected from mugs for 1 hour.");
                break;

            
 case 9:
                $db-&gt;query("UPDATE grpgusers SET aprotection =  unix_timestamp() + 3600 WHERE id = ?");
                $db-&gt;execute(array(
                    $user_class-&gt;id
                ));
                echo Message("You are now protected from attacks for 1 hour.");
                break;





            case 10:
                $db-&gt;query("UPDATE grpgusers SET exppill =  unix_timestamp() + 3600 WHERE id = ?");
                $db-&gt;execute(array(
                    $user_class-&gt;id
                ));
                echo Message("You will receive double exp on crimes for 1 hour.");
                break;

            case 168:
 $db-&gt;query("UPDATE grpgusers SET fbi =  fbi + 30 WHERE id = ?");
                $db-&gt;execute(array(
                    $user_class-&gt;id
                ));
                echo Message("You are now being watched by the FBI for an extra 30 Minutes!.");
                break;

            case 169:

  if ($user_class-&gt;fbitime == 0)
                    diefun("You are currently not in Fed Jail!");

$db-&gt;query("UPDATE grpgusers SET fbitime = 0 WHERE id = ?");
                $db-&gt;execute(array(
                    $user_class-&gt;id
                ));
                echo Message("You have used an Escape FBI Item and have now escaped!.");
                break;



            case 13:
            case 14:
                if ($user_class-&gt;purehp &gt;= $user_class-&gt;puremaxhp &amp;&amp; !$user_class-&gt;hospital)
                    diefun("You already have full HP and are not in the hospital.");

                if ($user_class-&gt;hhow == "bombed" || $user_class-&gt;hhow == "cbombed" || $user_class-&gt;hhow == "abombed")
                    diefun("These won't help you when you are in bits.. you are going to have to wait it out.");

                $db-&gt;query("SELECT * FROM items WHERE id = ?");
                $db-&gt;execute(array(
                    $id
                ));
                $row = $db-&gt;fetch_row(true);
                $hosp = floor(($user_class-&gt;hospital / 100) * $row['reduce']);
                $newhosp = $user_class-&gt;hospital - $hosp;
                $newhosp = ($newhosp &lt; 0) ? 0 : $newhosp;
                $hp = floor(($user_class-&gt;puremaxhp / 4) * $row['heal']);
                $hp = $user_class-&gt;purehp + $hp;
                $hp = ($hp &gt; $user_class-&gt;puremaxhp) ? $user_class-&gt;puremaxhp : $hp;
                $db-&gt;query("UPDATE grpgusers SET hospital = ?, hp = ? WHERE id = ?");
                $db-&gt;execute(array(
                    $newhosp,
                    $hp,
                    $user_class-&gt;id
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
                    $db-&gt;query("SELECT id, name, levelreq FROM cities WHERE country = 1 ORDER BY levelreq DESC");
                    $db-&gt;execute();
                    $rows = $db-&gt;fetch_row();
                    $opts = "";
                    foreach ($rows as $row)
                        $opts .= "<option id']}'="" value="{$row[">{$row['name']} (LVL: {$row['levelreq']})</option>";
                    echo '<form method="get">';
                    echo '<select name="cityid">';
                    echo $opts;
                    echo '</select>';
                    echo '<input name="use" type="hidden" value="38"/>';
                    echo '<input type="submit" value="Move to City"/>';
                    echo '</form>';
                    diefun();
                } else {
                    $cid = security($_GET['cityid']);
                    $db-&gt;query("SELECT * FROM cities WHERE id = ? AND pres = 0");
                    $db-&gt;execute(array(
                        $cid
                    ));
                    if ($db-&gt;fetch_row()) {
                        $db-&gt;query("UPDATE grpgusers SET city = ? WHERE id = ?");
                        $db-&gt;execute(array(
                            $cid,
                            $user_class-&gt;id
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
                elseif ($randnum &gt;= 2 &amp;&amp; $randnum &lt;= 15)
                    $randpoints = rand(2000, 5000);
                elseif ($randnum &gt;= 16 &amp;&amp; $randnum &lt;= 30)
                    $randpoints = rand(1000, 3000);
                elseif ($randnum &gt;= 31 &amp;&amp; $randnum &lt;= 65)
                    $randpoints = rand(500, 2000);
                else
                    $randpoints = rand(250, 2000);
                $user_class-&gt;points += $randpoints;
                $db-&gt;query("UPDATE grpgusers SET points = points + ? WHERE id = ?");
                $db-&gt;execute(array(
                    $randpoints,
                    $user_class-&gt;id
                ));
                $jp = ($randpoints &gt;= 10000) ? "You hit the <span style="color:red;font-weight:bold;">Jackpot</span>! " : "";
                echo Message("{$jp}You have taken <span style="color:#00ff00;font-weight:bold;">$randpoints</span> Points from the Mystery Box.");
                break;
            case 51:
                add_rm_days(30, 150000, 750);
                echo Message(";.30 RM days to your account.");
                break;
            case 103:
                add_rm_days(60, 300000, 1500);
                echo Message("You have added 60 RM days to your account.");
                break;
            case 104:
                add_rm_days(90, 450000, 2500);
                echo Message("You have added 90 RM days to your account.");
                break;
               case 163:
    $db-&gt;query("UPDATE grpgusers SET bustpill = bustpill + 60 WHERE id = ?");
 $db-&gt;execute(array(
               $user_class-&gt;id
    ));

                echo Message("You have added 60 Minutes to your Police Pass.");
                break;




 case 166:
    $db-&gt;query("UPDATE grpgusers SET outofjail = outofjail + 20 WHERE id = ?");
 $db-&gt;execute(array(
               $user_class-&gt;id
    ));

                echo Message("You have added 20 Minutes to your Out of Jail Pass.");
                break;


            case 151:
            case 152:
                $db-&gt;query("SELECT * FROM items WHERE id = ?");
                $db-&gt;execute(
                    array(
                        $id
                    )
                );
                $item = $db-&gt;fetch_row(true);
                $item_name = $item['itemname'];
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $gang_id = ($user_class-&gt;gang != 0) ? $user_class-&gt;gang : -1;
                $db-&gt;query("SELECT id, username FROM grpgusers WHERE id != ? AND hospital = 0 AND jail = 0 AND gang != ? AND admin != 1 ORDER BY username");
                $db-&gt;execute(
                    array(
                        $user_class-&gt;id,
                        $gang_id
                    )
                );
                $rows = $db-&gt;fetch_row();
                echo '<p>You are about to use a ' . item_popup($item_name, $id) . '<br/>Please choose your target below:</p>';
                echo '<form action="?" method="POST">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option id']}'="" value="{$row[">{$row['username']}</option>";
                }
                echo '</select><br/><br/>';
                echo '<input name="item" type="hidden" value="' . $id . '"/>';
                echo '<label for="anon">Anonymous: (Cost: $0) </label><br/>';
                echo '<select id="anon" name="anon">';
                echo '<option value="0">No</option>';
                echo '<option value="1">Yes</option>';
                echo '</select><br><br>';
                echo '<input name="bomb" type="submit" value="Bomb Player"/>';
                echo '</br></br></form>';
                echo '<h4>There is a chance that you may fail and end up blowing up yourself!</h4>';
                echo '</div>';
                diefun();
                break;
            case 154:
                $db-&gt;query("SELECT * FROM items WHERE id = ?");
                $db-&gt;execute(
                    array(
                        $id
                    )
                );
                $item = $db-&gt;fetch_row(true);
                $item_name = $item['itemname'];
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                echo '<p>You are about to use a ' . item_popup($item_name, $id) . '<br/>Please confirm your action below:</p>';
                echo '<form action="?" method="POST">';
                echo '<input name="item" type="hidden" value="' . $id . '"/>';
                echo '<label for="anon">Anonymous: </label>';
                echo '<select id="anon" name="anon">';
                echo '<option value="0">No</option>';
                echo '<option value="1">Yes</option>';
                echo '</select><br><br>';
                echo '<input name="bombc" type="submit" value="Bomb City"/>';
                echo '</br></br></form>';
                echo '<h4>There is a chance that you may fail and end up blowing up yourself aswell!</h4>';
                echo '</div>';
                diefun();
                break;
            case 155:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db-&gt;query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db-&gt;execute(
                    array(
                        $user_class-&gt;id,
                    )
                );
                $rows = $db-&gt;fetch_row();
                echo '<p>Who do you wish to send a Heart to?</p>';
                echo '<form action="?" method="POST">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option id']}'="" value="{$row[">{$row['username']} (ID: {$row['id']})</option>";
                }
                echo '</select><br/><br/>';
                echo '<input name="heart" type="submit" value="Send"/>';
                echo '</form>';
                echo '<h4>You will both recieve a random reward!</h4>';
                echo '</div>';
                diefun('');
                break;
            case 156:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db-&gt;query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db-&gt;execute(
                    array(
                        $user_class-&gt;id,
                    )
                );
                $rows = $db-&gt;fetch_row();
                echo '<p>Who do you wish to send a shamrock to?</p>';
                echo '<form action="?" method="POST">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option id']}'="" value="{$row[">{$row['username']}</option>";
                }
                echo '</select><br/><br/>';
                echo '<input name="shamrock" type="submit" value="Send"/>';
                echo '</form>';
                echo '<h4>You will both recieve a random reward!</h4>';
                echo '</div>';
                diefun('');
 break;
 case 158:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db-&gt;query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db-&gt;execute(
                    array(
                        $user_class-&gt;id,
                    )
                );
                $rows = $db-&gt;fetch_row();
                echo '<p>Who do you wish to send Fireworks to?</p>';
                echo '<form action="?" method="POST">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option id']}'="" value="{$row[">{$row['username']}</option>";
                }
                echo '</select><br/><br/>';
                echo '<input name="fireworks" type="submit" value="Send"/>';
                echo '</form>';
                echo '<h4>You will both recieve a random reward!</h4>';
                echo '</div>';
                diefun('');
 break;


case 159:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db-&gt;query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db-&gt;execute(
                    array(
                        $user_class-&gt;id,
                    )
                );
                $rows = $db-&gt;fetch_row();
                echo '<p>Who do you wish to send Rayz to?</p>';
                echo '<form action="?" method="POST">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option id']}'="" value="{$row[">{$row['username']}</option>";
                }
                echo '</select><br/><br/>';
                echo '<input name="rayz" type="submit" value="Send"/>';
                echo '</form>';
                echo '<h4>You will both recieve a random reward!</h4>';
                echo '</div>';
                diefun('');
 break;



case 167:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db-&gt;query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db-&gt;execute(
                    array(
                        $user_class-&gt;id,
                    )
                );
                $rows = $db-&gt;fetch_row();
                echo '<p>Who do you wish to send Christmas Present to?</p>';
                echo '<form action="?" method="POST">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option id']}'="" value="{$row[">{$row['username']}</option>";
                }
                echo '</select><br/><br/>';
                echo '<input name="presents" type="submit" value="Send"/>';
                echo '</form>';
                echo '<h4>You will both recieve a random reward!</h4>';
                echo '</div>';
                diefun('');
 break;


case 165:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db-&gt;query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db-&gt;execute(
                    array(
                        $user_class-&gt;id,
                    )
                );
                $rows = $db-&gt;fetch_row();
                echo '<p>Who do you wish to send a Ghost to?</p>';
                echo '<form action="?" method="POST">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option id']}'="" value="{$row[">{$row['username']}</option>";
                }
                echo '</select><br/><br/>';
                echo '<input name="ghosts" type="submit" value="Send"/>';
                echo '</form>';
                echo '<h4>You will both recieve a random reward!</h4>';
                echo '</div>';
                diefun('');
 break;


            case 157:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db-&gt;query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db-&gt;execute(
                    array(
                        $user_class-&gt;id,
                    )
                );
                $rows = $db-&gt;fetch_row();
                echo '<p>Who do you wish to send a Easter Egg to?</p>';
                echo '<form action="?" method="POST">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option id']}'="" value="{$row[">{$row['username']}</option>";
                }
                echo '</select><br/><br/>';
                echo '<input name="easter" type="submit" value="Send"/>';
                echo '</form>';
                echo '<h4>You will both recieve a random reward!</h4>';
                echo '</div>';
                diefun('');

        }
        Take_Item($id, $user_class-&gt;id);
    }
}
function add_rm_days($days, $money, $points)
{
    global $db, $user_class;
    $db-&gt;query("UPDATE grpgusers SET rmdays = rmdays + ?, bank = bank + $money, points = points + $points WHERE id = ?");
    $db-&gt;execute(array(
        $days,
        $user_class-&gt;id
    ));
}
function druggie($num)
{
    global $db, $user_class;
    $drugs = explode("|", $user_class-&gt;drugs);
    $drugs[0] = !empty($drugs[0]) ? $drugs[0] : 0;
    $drugs[1] = !empty($drugs[1]) ? $drugs[1] : 0;
    $drugs[2] = !empty($drugs[2]) ? $drugs[2] : 0;
    $drugs[$num] = time();
    $db-&gt;query("UPDATE grpgusers SET drugs = ? WHERE id = ?");
    $db-&gt;execute(array(
        implode("|", $drugs),
        $user_class-&gt;id
    ));
}
if (check_items(105) || check_items(106) || check_items(107) || $user_class-&gt;eqweapon == 105 || $user_class-&gt;eqarmor == 106 || $user_class-&gt;eqshoes == 107)
    echo '<br><a href="customitems.php" style="color:#00ff00;font-weight:bold;font-size:20px;"> &gt; You can rename your custom items by clicking this link. &lt; </a><br>';
genHead("<h3>Equipped</h3>");
echo '<hr/>';
echo '<table width="100%">';
echo '<tr>';
echo '<td align="center" width="33.3%">';
if ($user_class-&gt;eqweapon != 0) {
    echo image_popup($user_class-&gt;weaponimg, $user_class-&gt;eqweapon);
    echo '<br>';
    echo item_popup($user_class-&gt;weaponname, $user_class-&gt;eqweapon);
    echo '<br>';
    echo '<a class="button-sm" href="equip.php?unequip=weapon">Unequip</a>';
} else
    echo '<img height="100" src="/css/images/empty.jpg" width="100"/><br> You are not holding a weapon.';
echo '</br></br></br></td>';
echo '<td align="center" width="33.3%">';
if ($user_class-&gt;eqarmor != 0) {
    echo image_popup($user_class-&gt;armorimg, $user_class-&gt;eqarmor);
    echo '<br>';
    echo item_popup($user_class-&gt;armorname, $user_class-&gt;eqarmor);
    echo '<br>';
    echo '<a class="button-sm" href="equip.php?unequip=armor">Unequip</a>';
} else
    echo '<img height="100" src="/css/images/empty.jpg" width="100"/><br> You are not wearing armour';
echo '</br></br></br></td>';
echo '<td align="center" width="33.3%">';
if ($user_class-&gt;eqshoes != 0) {
    echo image_popup($user_class-&gt;shoesimg, $user_class-&gt;eqshoes);
    echo '<br>';
    echo item_popup($user_class-&gt;shoesname, $user_class-&gt;eqshoes);
    echo '<br>';
    echo '<a class="button-sm" href="equip.php?unequip=shoes">Unequip</a>';
} else
    echo '<img height="100" src="/css/images/empty.jpg" width="100"/><br> You are not wearing boots.';
echo '</br></br></br></td>';
echo '</tr>';
echo '</table>';
echo '</br></br>';
echo '';
$db-&gt;query("SELECT inv.*, it.*, c.name overridename, c.image overrideimage FROM inventory inv JOIN items it ON inv.itemid = it.id LEFT JOIN customitems c ON it.id = c.itemid AND c.userid = inv.userid WHERE inv.userid = ?");
$db-&gt;execute(array(
    $user_class-&gt;id
));
$rows = $db-&gt;fetch_row();
foreach ($rows as $row) {
    $subtype = '';
    if (!empty($row['overrideimage']) || !empty($row['overridename'])) {
        $row['image'] = $row['overrideimage'];
        $row['itemname'] = $row['overridename'];
    }
    $sell = ($row['cost'] &gt; 0) ? "<a "'="" .="" class="button-sm" href='sellitem.php?id=" . $row[' id']="">Sell</a>" : "";

    if ($row['offense'] &gt; 0 &amp;&amp; ($row['defense'] &gt; 0 || $row['speed'] &gt; 0)) {
        if ($row['offense'] &gt; $row['defense']) {
            if ($row['offense'] &gt; $row['speed']) {
                $type = 'weapon';
            } else {
                $type = 'shoes';
            }
        } else if ($row['defense'] &gt; $row['speed']) {
            $type = 'armor';
        } else {
            $type = 'shoes';
        }
    } else {
        if ($row['offense'] &gt; 0 &amp;&amp; $row['rare'] == 0)
        $type = 'weapon';
    elseif ($row['defense'] &gt; 0 &amp;&amp; $row['rare'] == 0)
        $type = 'armor';
    elseif ($row['speed'] &gt; 0 &amp;&amp; $row['rare'] == 0)
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
    }
    gendivs($row, $type, $sell, $subtype);
}
$db-&gt;query("SELECT * FROM gang_loans gl JOIN items i ON gl.item = i.id WHERE idto = ?");
$db-&gt;execute(array(
    $user_class-&gt;id
));
$rows = $db-&gt;fetch_row();
foreach ($rows as $row) {
    if ($row['offense'] &gt; 0 &amp;&amp; $row['rare'] == 0)
        $type = 'weapon';
    elseif ($row['defense'] &gt; 0 &amp;&amp; $row['rare'] == 0)
        $type = 'armor';
    elseif ($row['speed'] &gt; 0 &amp;&amp; $row['rare'] == 0)
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
// if ($user_class-&gt;id == 1) {
echo '<div class="floaty"><p class="text-14">Specials</p>';
if ($user_class-&gt;donate_token &gt; 0) {
    echo image_popup('css/newgame/items/donate_boost.png', 156) . '<br>';
    echo '<span class="text-14">x' . $user_class-&gt;donate_token . '</span><br>';
    echo '<a class="text-14 text-yellow" href="rmstore.php">Boost Donation</a><br><br>';
    echo '<a class="text-14 text-yellow" href="inventory.php?exchangetoken">Exchange x1 for 1,000 Points</a>';
}
echo '</br></br></br></br></div>';
//}
$master = array(
    'Weapons' =&gt; 'weapon',
    'Armor' =&gt; 'armor',
    'Shoes' =&gt; 'shoes',
    'Gang Loans' =&gt; 'loans',
    'Rares' =&gt; 'rare',
    'Consumables' =&gt; 'consumable'
);
foreach ($master as $header =&gt; $var)
    if (isset($$var)) {
        // genHead($header);
        echo '<div class="floaty">';
        echo '<b>' . $header . '</b>';
        echo '<hr>';
        echo '<div ;="" border="thick solid #0000FF" class="flexcont" style="text-align:center;position: relative;flex-flow:row wrap;">';
        echo $$var;
        echo '</div>';
        echo '</hr></div>';
    }
include 'footer.php';
function gendivs($row, $type, $sell = null, $subtype = null, $loan = null)
{
    global $$type;
    $$type .= '<div class="flexele" style="flex-basis:25%;margin-bottom:12px;margin-top:12px;">';
    $$type .= image_popup($row['image'], $row['id']);
    $$type .= '<br>';
    $$type .= item_popup($row['itemname'], $row['id']) . ' [x' . $row['quantity'] . ']';
    $$type .= '<br>';
    $$type .= '<br>';
    if ($row['cost'] &gt; 0) {
        $$type .= prettynum($row['cost'], 1);
        $$type .= '<br/>';
        $$type .= '<br/>';
    }
    $$type .= $sell;
    if ($type == "consumable" &amp;&amp; $row['id'] != 155 &amp;&amp; $row['id'] != 156 &amp;&amp; $row['id'] != 157 &amp;&amp; $row['id'] != 158 &amp;&amp; $row['id'] != 159  &amp;&amp; $row['id'] != 165 &amp;&amp; $row['id'] != 167)
        $$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Use</a> ';
if ($type == "rare" &amp;&amp; $row['id'] != 155 &amp;&amp; $row['id'] != 156 &amp;&amp; $row['id'] != 68 &amp;&amp; $row['id'] != 69 &amp;&amp; $row['id'] != 157 &amp;&amp; $row['id'] != 158 &amp;&amp; $row['id'] != 159  &amp;&amp; $row['id'] != 165 &amp;&amp; $row['id'] != 167)
        $$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Use</a> ';

    if (!$loan &amp;&amp; $row['id'] != 155 &amp;&amp; $row['id'] != 156 &amp;&amp; $row['id'] != 157 &amp;&amp; $row['id'] != 158 &amp;&amp; $row['id'] != 159  &amp;&amp; $row['id'] != 165 &amp;&amp; $row['id'] != 167)
        $$type .= ' <a class="button-sm" href="putonmarket.php?id=' . $row['id'] . '">Market</a> ';

    if ($row['id'] != 155 &amp;&amp; $row['id'] != 157 &amp;&amp; $row['id'] != 156 &amp;&amp; $row['id'] != 158 &amp;&amp; $row['id'] != 159 &amp;&amp; $row['id'] != 167)
        $$type .= ' <a class="button-sm" href="senditem.php?id=' . $row['id'] . '">Send</a> ';

    if ($row['id'] == 155)
        $$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Share The Love</a> ';

    if ($row['id'] == 156)
        $$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Share</a> ';
if ($row['id'] == 157)
        $$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Send Egg</a> ';
if ($row['id'] == 158)
        $$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Independance!</a> ';
if ($row['id'] == 159)
        $$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Send Rayz</a> ';
if ($row['id'] == 165)
        $$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Send Ghosts</a> ';
if ($row['id'] == 167)
        $$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Send Christmas Present</a> ';









    echo "<div style="display:none;">";
    echo $subtype . " " . $type;
    echo "</div>";

    if (in_array($type, array('weapon', 'armor', 'shoes')) || in_array($subtype, array('weapon', 'armor', 'shoes'))) {
        $$type .= ' <a class="button-sm" href="equip.php?eq=';
        $$type .= (!empty($subtype)) ? $subtype : $type;
        $$type .= '&amp;id=' . $row['id'];
        $$type .= ($loan) ? '&amp;loaned=1' : '';
        $$type .= '">Equip</a> ';
    }
    $$type .= '</br></br></br></div>';
}
<div id="mainImageContainer"><img alt="Inventory Image" src="/images/inventory.png"/></div>