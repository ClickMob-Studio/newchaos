<?php
include 'header.php';

// TEMP FIX
$db->query("SELECT id FROM inventory WHERE quantity <= 0 AND userid = " . $user_class->id);
$db->execute();
$rows = $db->fetch_row();

foreach ($rows as $row)
{
    $db->query("DELETE FROM inventory WHERE id = " . $row['id']);
    $db->execute();
}


?>
<div class='box_top'>Inventory</div>
						<div class='box_middle'>
							<div class='pad'>
<?php

if ($user_class->gang > 0) {
    $tempItemUse = getItemTempUse($user_class->id);
    $now = time();
    if ($tempItemUse['gang_double_exp_hours'] > 0 && $tempItemUse['gang_double_exp_time'] < $now) {
        echo '
            <hr />
            <center>
             <a href="trigger_doublexp_hour.php" onclick="return confirm(\'Are you sure you want to trigger double EXP?\');"><font color=red>You have ' . $tempItemUse['gang_double_exp_hours'] . ' hours of double EXP! Click to run 1 hour of double exp.</font></a>
            </center>
            <hr />
        ';
    }
}

if (isset($_POST['move_to_cabinet'])) {
    $itemid = intval($_POST['itemid']);
    $quantity = intval($_POST['quantity']);  // Assuming you want to allow users to specify a quantity

    // Check if user has enough of the item in their inventory
    $stmt = $db->prepare("SELECT quantity FROM inventory WHERE userid = ? AND itemid = ?");
    $stmt->execute([$user_class->id, $itemid]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['quantity'] >= $quantity) {
        // Remove items from the inventory
        $new_quantity = $result['quantity'] - $quantity;
        if ($new_quantity > 0) {
            $stmt = $db->prepare("UPDATE inventory SET quantity = ? WHERE userid = ? AND itemid = ?");
            $stmt->execute([$new_quantity, $user_class->id, $itemid]);
        } else {
            $stmt = $db->prepare("DELETE FROM inventory WHERE userid = ? AND itemid = ?");
            $stmt->execute([$user_class->id, $itemid]);
        }

        // Add items to the display cabinet
        $stmt = $db->prepare("INSERT INTO display_cabinet (userid, itemid, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?");
        $stmt->execute([$user_class->id, $itemid, $quantity, $quantity]);

        echo "Item successfully added to Display Cabinet!";
    } else {
        echo "You don't have enough of this item!";
    }
}

if (isset($_POST['remove_from_cabinet'])) {
    $itemid = intval($_POST['itemid']);
    $quantity = intval($_POST['quantity']);  // Assuming you want to allow users to specify a quantity

    // Check if user has enough of the item in their display cabinet
    $stmt = $db->prepare("SELECT quantity FROM display_cabinet WHERE userid = ? AND itemid = ?");
    $stmt->execute([$user_class->id, $itemid]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['quantity'] >= $quantity) {
        // Remove items from the display cabinet
        $new_quantity = $result['quantity'] - $quantity;
        if ($new_quantity > 0) {
            $stmt = $db->prepare("UPDATE display_cabinet SET quantity = ? WHERE userid = ? AND itemid = ?");
            $stmt->execute([$new_quantity, $user_class->id, $itemid]);
        } else {
            $stmt = $db->prepare("DELETE FROM display_cabinet WHERE userid = ? AND itemid = ?");
            $stmt->execute([$user_class->id, $itemid]);
        }

        // Add items back to the inventory
        $stmt = $db->prepare("INSERT INTO inventory (userid, itemid, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?");
        $stmt->execute([$user_class->id, $itemid, $quantity, $quantity]);

        echo "Item successfully removed from Display Cabinet and added back to Inventory!";
    } else {
        echo "You don't have enough of this item in your Display Cabinet!";
    }
}

if (isset($_GET['exchangetoken'])) {
    if ($user_class->donate_token > 0) {
        $db->query("UPDATE grpgusers SET donate_token = donate_token - 1, points = points + 15000 WHERE id = ?");
        $db->execute(
            array(
                $user_class->id,
            )
        );
        $message = "You have exchanged a " . item_popup('Donation Boost Token', 156) . " for 15,000 Points";
        Send_Event($user_class->id, "You have exchanged a Donation Boost Token for 15,000 Points.", $user_class->id);
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









if (isset($_POST['snowball'])) {
    $target = security($_POST['target']);
    if ($target == $user_class->id)
        diefun("You can't throw snowballs at yourself");

    $receiver = new User($target);

    $howmany = check_items(198);
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
            Send_Event($user_class->id, "You threw a Snowball at  [-_USERID_-] you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] threw a snowball at you; you both receive " . $result, $user_class->id);

            $db->query("INSERT INTO snowball_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db->execute(
                array(
                    $user_class->id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(198, $user_class->id, 1);
            diefun('You Threw ' . formatName($target) . ' a Snowball and you both receive ' . $result);
        }

        $db->query($sql);
        $db->execute(
            array(
                $user_class->id,
                $target
            )
        );
        Send_Event($user_class->id, "You Threw a Snowball at [-_USERID_-] you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] threw a snowball at you and you both receive " . $result, $user_class->id);

        $db->query("INSERT INTO snowball_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
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

        Take_Item(198, $user_class->id, 1);
        diefun('You threw a snowball at ' . formatName($target) . ' you both receive ' . $result);
    } else {
        echo Message('You do not have any Snowballs to send');
    }
}








if (isset($_POST['Pumpkin'])) {
    $target = security($_POST['target']);
    if ($target == $user_class->id)
        diefun("You can't send Pumpkins to yourself");

    $receiver = new User($target);

    $howmany = check_items(195);
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
            Send_Event($user_class->id, "You sent [-_USERID_-] a Pumpkin and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you a Pumpkin and you both receive " . $result, $user_class->id);

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
            diefun('You sent ' . formatName($target) . ' a Pumpkin and you both receive ' . $result);
        }

        $db->query($sql);
        $db->execute(
            array(
                $user_class->id,
                $target
            )
        );
        Send_Event($user_class->id, "You sent [-_USERID_-] a Pumpkin Head and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you a Pumpkin Head and you both receive " . $result, $user_class->id);

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

        Take_Item(195, $user_class->id, 1);
        diefun('You sent ' . formatName($target) . ' a Pumpkin Head and you both receive ' . $result);
    } else {
        echo Message('You do not have any Pumpkin Heads to send');
    }
}









if (isset($_POST['shamrock'])) {
    $target = security($_POST['target']);
    if ($target == $user_class->id)
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

            $db->query("INSERT INTO shamrocks_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db->execute(
                array(
                    $user_class->id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(156, $user_class->id, 1);
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

        $db->query("INSERT INTO shamrocks_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
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

        Take_Item(156, $user_class->id, 1);
        diefun('You sent ' . formatName($target) . ' a shamrock and you both receive ' . $result);
    } else {
        echo Message('You do not have any shamrocks to send');
    }
}















if (isset($_POST['easter'])) {
    $target = security($_POST['target']);
    if ($target == $user_class->id)
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
            Send_Event($user_class->id, "You sent [-_USERID_-] a Easter Egg and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you a Easter Egg and you both receive " . $result, $user_class->id);

            $db->query("INSERT INTO easter_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db->execute(
                array(
                    $user_class->id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(157, $user_class->id, 1);
            diefun('You sent ' . formatName($target) . ' a Easter Egg and you both receive ' . $result);
        }

        $db->query($sql);
        $db->execute(
            array(
                $user_class->id,
                $target
            )
        );
        Send_Event($user_class->id, "You sent [-_USERID_-] a Easter Egg and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you a Easter Egg and you both receive " . $result, $user_class->id);

        $db->query("INSERT INTO easter_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
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
        diefun('You sent ' . formatName($target) . ' a Easter Egg and you both receive ' . $result);
    } else {
        echo Message('You do not have any Easter Egg to send');
    }
}







if (isset($_POST['fireworks'])) {
    $target = security($_POST['target']);
    if ($target == $user_class->id)
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
            Send_Event($user_class->id, "You sent [-_USERID_-] Fireworks and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you Fireworks and you both receive " . $result, $user_class->id);

            $db->query("INSERT INTO fireworks_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db->execute(
                array(
                    $user_class->id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(158, $user_class->id, 1);
            diefun('You sent ' . formatName($target) . ' Fireworks and you both receive ' . $result);
        }

        $db->query($sql);
        $db->execute(
            array(
                $user_class->id,
                $target
            )
        );
        Send_Event($user_class->id, "You sent [-_USERID_-] Fireworks and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you Fireworks and you both receive " . $result, $user_class->id);

        $db->query("INSERT INTO fireworks_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
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

        Take_Item(158, $user_class->id, 1);
        diefun('You sent ' . formatName($target) . ' Fireworks and you both receive ' . $result);
    } else {
        echo Message('You do not have any Fireworks to send');
    }
}



if (isset($_POST['presents'])) {
    $target = security($_POST['target']);
    if ($target == $user_class->id)
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
            Send_Event($user_class->id, "You sent [-_USERID_-] Christmas Presents and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you a Christmas Present and you both receive " . $result, $user_class->id);

            $db->query("INSERT INTO fireworks_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db->execute(
                array(
                    $user_class->id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(167, $user_class->id, 1);
            diefun('You sent ' . formatName($target) . ' a Christmas Present and you both receive ' . $result);
        }

        $db->query($sql);
        $db->execute(
            array(
                $user_class->id,
                $target
            )
        );
        Send_Event($user_class->id, "You sent [-_USERID_-] a Christmas Present and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you a Christmas Present and you both receive " . $result, $user_class->id);

        $db->query("INSERT INTO fireworks_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
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

        Take_Item(167, $user_class->id, 1);
        diefun('You sent ' . formatName($target) . ' a Christmas Present and you both receive ' . $result);
    } else {
        echo Message('You do not have any Christmas Presents to send');
    }
}


if (isset($_POST['rayz'])) {
    $target = security($_POST['target']);
    if ($target == $user_class->id)
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
            Send_Event($user_class->id, "You sent [-_USERID_-] Rayz and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you Rayz and you both receive " . $result, $user_class->id);

            $db->query("INSERT INTO hearts_log (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db->execute(
                array(
                    $user_class->id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(159, $user_class->id, 1);
            diefun('You sent ' . formatName($target) . ' Rayz and you both receive ' . $result);
        }

        $db->query($sql);
        $db->execute(
            array(
                $user_class->id,
                $target
            )
        );
        Send_Event($user_class->id, "You sent [-_USERID_-] Rayz and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you Rayz and you both receive " . $result, $user_class->id);

        $db->query("INSERT INTO rayz_logs (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
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

        Take_Item(159, $user_class->id, 1);
        diefun('You sent ' . formatName($target) . ' Rayz and you both receive ' . $result);
    } else {
        echo Message('You do not have any Rayz to send');
    }
}















if (isset($_POST['ghosts'])) {
    $target = security($_POST['target']);
    if ($target == $user_class->id)
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
            Send_Event($user_class->id, "You sent [-_USERID_-] Ghost and you both receive " . $result, $target);
            Send_Event($target, "[-_USERID_-] sent you Ghost and you both receive " . $result, $user_class->id);

            $db->query("INSERT INTO rayz_logs (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
            $db->execute(
                array(
                    $user_class->id,
                    $target,
                    time(),
                    $item['name']
                )
            );

            Take_Item(165, $user_class->id, 1);
            diefun('You sent ' . formatName($target) . ' Rayz and you both receive ' . $result);
        }

        $db->query($sql);
        $db->execute(
            array(
                $user_class->id,
                $target
            )
        );
        Send_Event($user_class->id, "You sent [-_USERID_-] a Ghost and you both receive " . $result, $target);
        Send_Event($target, "[-_USERID_-] sent you a Ghost and you both receive " . $result, $user_class->id);

        $db->query("INSERT INTO rayz_logs (`user_id`, `who`, `time`, `reward`) VALUES (?, ?, ?, ?)");
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

        Take_Item(165, $user_class->id, 1);
        diefun('You sent ' . formatName($target) . ' a Ghost and you both receive ' . $result);
    } else {
        echo Message('You do not have any Ghosts to send');
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
                $timeAgo = time() - 900;
                if ($user_class->last_mug_time > $timeAgo) {
                    diefun('You have performed a mug in the last 15 minutes. You\'ll need to wait before you can take this protection.');
                }

                $itemDailyLimit = getItemDailyLimit($user_class->id);
                if ($itemDailyLimit['mug_protection'] >= 4) {
                    diefun('You can only use 4 mug protections per day.');
                }

                addItemDailyLimit($user_class, 'mug_protection');

                $db->query("UPDATE grpgusers SET mprotection =  unix_timestamp() + 3600 WHERE id = ?");
                $db->execute(array(
                    $user_class->id
                ));
                echo Message("You are now protected from mugs for 1 hour.");
                break;

            
            case 9:

                $timeAgo = time() - 900;
                if ($user_class->last_attack_time > $timeAgo) {
                    diefun('You have performed an attack in the last 15 minutes. You\'ll need to wait before you can take this protection.');
                }

                $itemDailyLimit = getItemDailyLimit($user_class->id);
                if ($itemDailyLimit['attack_protection'] >= 4) {
                    diefun('You can only use 4 attack protections per day.');
                }

                addItemDailyLimit($user_class, 'attack_protection');

                $db->query("UPDATE grpgusers SET aprotection =  unix_timestamp() + 3600, king = 0, queen = 0 WHERE id = ?");
                $db->execute(array(
                    $user_class->id
                ));
                echo Message("You are now protected from attacks for 1 hour. Whilst under attack protection, you can only attack players who have been offline for longer than an hour. If you hold a Boss/Underboss position you'll have been removed from this position too.");
                break;
            case 10:
                //if($user_class->exppill > time()){
                   // echo Message("You still have time on your double exp pill");
                   // break;
                //}
                $db->query("UPDATE grpgusers SET exppill =  unix_timestamp() + 3600 WHERE id = ?");
                $db->execute(array(
                    $user_class->id
                ));
                echo Message("You will receive double exp on crimes for 1 hour.");
                break;



 case 196:
                $db->query("UPDATE grpgusers SET nightvision =  nightvision +  15 WHERE id = ?");
                $db->execute(array(
                    $user_class->id
                ));
                echo Message("You have added 15 minutes to your Night Vision!");
                break;





            case 168:
 $db->query("UPDATE grpgusers SET fbi =  fbi + 30 WHERE id = ?");
                $db->execute(array(
                    $user_class->id
                ));
                echo Message("You are now being watched by the FBI for an extra 30 Minutes!.");
                break;

            case 169:

  if ($user_class->fbitime == 0)
                    diefun("You are currently not in Fed Jail!");

$db->query("UPDATE grpgusers SET fbitime = 0 WHERE id = ?");
                $db->execute(array(
                    $user_class->id
                ));
                echo Message("You have used an Escape FBI Item and have now escaped!.");
                break;



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
            case 235:
                druggie(3);
                echo Message("You successfully used some Serenity Serum. Your strength, defense and speed has been increased for 15 minutes.");
                break;
            case 38:
                if (empty($_GET['cityid'])) {
                    $db->query("SELECT id, name, levelreq FROM cities WHERE country = 1 ORDER BY levelreq DESC");
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
                if ($randnum <= 30) {
                    $randpoints = rand(1000, 5000);
                    $user_class->points += $randpoints;

                    mysql_query("UPDATE grpgusers SET points = points + " . $randpoints . " WHERE id = " . $user_class->id);

                    echo Message("You open the mystery box and find <span style='color:green;font-weight:bold;'>$randpoints</span> Points.");
                } elseif ($randnum <= 55) {
                    $randraidtokens = mt_rand(10, 200);
                    $user_class->raitokens += $randraidtokens;

                    mysql_query("UPDATE grpgusers SET raidtokens = raidtokens + " . $randraidtokens . " WHERE id = " . $user_class->id);

                    echo Message("You open the mystery box and find <span style='color:green;font-weight:bold;'>$randraidtokens</span> Raid Tokens.");
                } elseif ($randnum <= 80) {
                    $randcash = rand(1000000, 5000000);
                    $user_class->money += $randcash;

                    mysql_query("UPDATE grpgusers SET money = money + " . $randcash . " WHERE id = " . $user_class->id);

                    echo Message("You open the mystery box and find $<span style='color:green;font-weight:bold;'>$randcash</span>.");
                } elseif ($randnum <= 95) {
                    $itemid = 252;
                    Give_Item($itemid, $user_class->id, 1);

                    echo Message("You open the mystery box and find <span style='color:green;font-weight:bold;'>1 x Raid Booster</span>.");
                } else {
                    $itemid = 163;
                    Give_Item($itemid, $user_class->id, 1);

                    echo Message("You open the mystery box and find <span style='color:green;font-weight:bold;'>1 x Police Badge</span>.");
                }

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
            case 251:
                addItemTempUse($user_class, 'raid_pass');

                echo Message("You have used your raid pass. The next raid you host will be successful.");
                break;
            case 252:
                addItemTempUse($user_class, 'raid_booster');

                echo Message("You have used your raid booster. All payouts your next raid will be boosted.");
                break;
           case 163:
              $db->query("UPDATE grpgusers SET bustpill = bustpill + 60 WHERE id = ?");
                $db->execute(array(
                           $user_class->id
                ));

                echo Message("You have added 60 Minutes to your Police Pass.");
                break;
            case 253:
                $goldRushCredits = 10;
                if (isset($user_class->completeUserResearchTypesIndexedOnId[6])) {
                    $goldRushCredits += 5;
                }
                if (isset($user_class->completeUserResearchTypesIndexedOnId[15])) {
                    $goldRushCredits += 5;
                }

                $db->query("UPDATE user_ba_stats SET gold_rush_credits = gold_rush_credits + " . $goldRushCredits . " WHERE user_id = ?");
                $db->execute(array(
                    $user_class->id
                ));

                echo Message("Head to the Backalley now and start your Gold Rush!");
                break;
            case 254:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['crime_potion_time'] > $now) {
                    diefun('You already have a crime potion active.');
                }
                if ($tempItemUse['crime_booster_time'] > $now) {
                    diefun('You cannot stack a Crime Potion & Crime Booster.');
                }

                $newTime = time() + 3600;

                addItemTempUse($user_class, 'crime_potion_time', $newTime);

                echo Message("You drank the crime potion, for the next hour you will gain an extra 10% EXP from crimes!");
                break;
            case 255:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['crime_booster_time'] > $now) {
                    diefun('You already have a crime booter active.');
                }
                if ($tempItemUse['crime_potion_time'] > $now) {
                    diefun('You cannot stack a Crime Potion & Crime Booster.');
                }

                $newTime = time() + 3600;

                addItemTempUse($user_class, 'crime_booster_time', $newTime);

                echo Message("You use the crime booster, for the next hour you will gain an extra 20% EXP from crimes!");
                break;
            case 256:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['nerve_vial_time'] > $now) {
                    diefun('You already have a nerve vial active.');
                }

                $newTime = time() + 1800;

                addItemTempUse($user_class, 'nerve_vial_time', $newTime);

                echo Message("You drink from the nerve Vial and feel a boost, for the next 30 minutes you have double nerve!");
                break;
            case 257:
                
                $tempItemUse = getItemTempUse($user_class->id);

                if ($user_class->gang < 1) {
                    diefun('Your not in a gang.');
                }

                $db->query("SELECT * FROM grpgusers WHERE gang = " . $user_class->gang);
                $db->execute();
                $uRes = $db->fetch_row();
                foreach ($uRes as $ur) {
                    $uClass = new User($ur['id']);
                    addItemTempUse($uClass, 'gang_double_exp_hours', 4);
                }

                echo Message("You swallow your double EXP pill and your whole gang feels the effects, you'll all have 4 hours of double EXP added!");
                break;
            case 276:
                $db->query("SELECT * FROM `user_research_type` WHERE `user_id` = " . $user_class->id . " AND `duration_in_days` > 0 LIMIT 1");
                $db->execute();
                $activeUserResearchType = $db->fetch_row(true);

                if ($activeUserResearchType) {
                    $db->query("UPDATE `user_research_type` SET `duration_in_days` = `duration_in_days` - 1 WHERE `duration_in_days` > 0 AND `user_id` = " . $user_class->id);
                    $db->execute();
                } else {
                    diefun('You do not have a research active at the moment');
                }

                echo Message("You use your research token and knock 1 day off of your current research time!");
                break;
            case 258:
                $db->query("UPDATE grpgusers SET points = points + 400000 WHERE id = " . $user_class->id);
                $db->execute();

                Give_Item(10, $user_class->id, 5);
                Give_Item(255, $user_class->id, 5);
                Give_Item(256, $user_class->id, 2);

                echo Message("You open your loot crate and inside find 400,000 points, 5 x Double EXPs, 5 x Crime Boosters, 2 x Nerve Vials!");
                break;
            case 277:
                $tempItemUse = getItemTempUse($user_class->id);

                addItemTempUse($user_class, 'mission_passes', 1);

                echo Message("Now you have used your mission pass, you can go to the missions page and you'll be able to reset any mission you have already completed today!");
                break;
            case 279:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['gym_protein_bar_time'] > $now) {
                    diefun('You already have a gym protein bar active.');
                }

                $newTime = time() + 900;

                addItemTempUse($user_class, 'gym_protein_bar_time', $newTime);

                echo Message("You eat the protein bar, for the next 15 minutes you will gain an extra 20% in the gym!");
                break;
            case 281:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['gym_super_pills_time'] > $now) {
                    diefun('You already have a gym super pills active.');
                }

                $newTime = time() + 900;

                addItemTempUse($user_class, 'gym_super_pills_time', $newTime);

                echo Message("You eat your gym super pills, for the next 15 minutes you will have an extra 10% awake!");
                break;
            case 282:
                $db->query("UPDATE grpgusers SET points = points + 400000 WHERE id = " . $user_class->id);
                $db->execute();

                Give_Item(279, $user_class->id, 5);
                Give_Item(281, $user_class->id, 5);
                Give_Item(278, $user_class->id, 1);

                echo Message("You open your gym crate and inside find 400,000 points, 5 x Protein Bars, 5 x Gym Super Pills, 1 x Sound System!");
                break;
            case 283:
                Give_Item(253, $user_class->id, 10);

                echo Message("You open your Gold Rush Token Chest and find 10 x Gold Rush Tokens inside.");
                break;
            case 284:
                $tempItemUse = getItemTempUse($user_class->id);
                $now = time();
                if ($tempItemUse['ghost_vacuum_time'] > $now) {
                    diefun('You already have a ghost vacuum active.');
                }

                $newTime = time() + 900;

                addItemTempUse($user_class, 'ghost_vacuum_time', $newTime);

                echo Message("You use your ghost vacuum and you feel ready to hunt ghosts for the next 15 minutes!");
                break;
            case 288:
                $expRand = ceil($user_class->maxexp / mt_rand(10000, 30000));
                if ($expRand < 10) {
                    $expRand = 10;
                }


                $db->query("UPDATE grpgusers SET exp = exp + " . $expRand . " WHERE id = " . $user_class->id);
                $db->execute();


                echo Message("You eat your Cotton Candy and gain " . number_format($expRand) . " EXP!");
                break;
            case 289:
                $moneyRand = mt_rand(500, 20000);

                $db->query("UPDATE grpgusers SET money = money + " . $moneyRand . " WHERE id = " . $user_class->id);
                $db->execute();


                echo Message("You search inside the crate and find $" . number_format($moneyRand) . "!");
                break;
            case 290:
                $tempItemUse = getItemTempUse($user_class->id);

                addItemTempUse($user_class, 'toffee_apples', 1);

                echo Message("You eat your Toffee Apple and now your ready to go and attack some City Goons.");
                break;
            case 291:
                $zombieRushCredits = 10;

                $db->query("UPDATE user_ba_stats SET zombie_rush_credits = zombie_rush_credits + " . $zombieRushCredits . " WHERE user_id = ?");
                $db->execute(array(
                    $user_class->id
                ));

                echo Message("Head to the Backalley now and start your Zombie Rush!");
                break;

case 197: // Nuke item
    // Check if the form has been submitted
    if (isset($_POST['selected_city'])) {
        $selectedCity = mysql_real_escape_string($_POST['selected_city']);

  
    // Query to get all users in the selected city
    $usersQuery = "SELECT id, money FROM users WHERE city = '$selectedCity'";
    $usersResult = mysql_query($usersQuery);

    $affectedUsers = 0;
    $totalDeductedMoney = 0;

    while ($user = mysql_fetch_assoc($usersResult)) {
        $userId = $user['id'];
        $userMoney = $user['money'];
        $deductedMoney = $userMoney * 0.2; // 20% deduction

        // Hospitalize the user
        // Replace 'hospitalize_user' with your actual hospitalization function
        hospitalize_user($userId);

        // Deduct money and update the user's money
        $newMoney = $userMoney - $deductedMoney;
        $updateMoneyQuery = "UPDATE users SET money = '$newMoney' WHERE id = '$userId'";
        mysql_query($updateMoneyQuery);

        // Send event notification
        // Replace 'send_event' with your actual event notification function
        send_event($userId, "You have been nuked!");

        $affectedUsers++;
        $totalDeductedMoney += $deductedMoney;
    }

    // Feedback to the user who used the nuke
    echo "Nuke deployed! Affected users: $affectedUsers. Total cash earned: $" . number_format($totalDeductedMoney);
    break;
    } else {
        // Form to select city
        echo "<form action='inventory.php' method='post'>"; // Replace 'inventory.php' with your actual script
        echo "<input type='hidden' name='item_id' value='197'>"; // Ensure you pass the item ID

        // Dropdown for cities
        echo "<select name='selected_city'>";
        $citiesQuery = "SELECT city_name FROM cities"; // Replace 'city_name' with your actual column name
        $citiesResult = mysql_query($citiesQuery);
        while ($city = mysql_fetch_assoc($citiesResult)) {
            echo "<option value='" . htmlspecialchars($city['city_name']) . "'>" . htmlspecialchars($city['city_name']) . "</option>";
        }
        echo "</select>";

        // Submit button
        echo "<input type='submit' value='Use Nuke'>";
        echo "</form>";
    }
    break;







case 197: // Nuke item
    // Check if the form has been submitted
    if (isset($_POST['selected_city'])) {
        $selectedCity = mysql_real_escape_string($_POST['selected_city']);

        // Debug: Show selected city
        echo "<p>Selected City ID: $selectedCity</p>";

        // Query to get all users in the selected city
        $usersQuery = "SELECT id, money FROM grpgusers WHERE city = '$selectedCity'";
        $usersResult = mysql_query($usersQuery);

        $affectedUsers = 0;
        $totalDeductedMoney = 0;

        while ($user = mysql_fetch_assoc($usersResult)) {
            $userId = $user['id'];
            $userMoney = $user['money'];
            $deductedMoney = $userMoney * 0.2; // 20% deduction

            // Hospitalize the user
            // Replace 'hospitalize_user' with your actual hospitalization function
            hospitalize_user($userId);

            // Deduct money and update the user's money
            $newMoney = $userMoney - $deductedMoney;
            $updateMoneyQuery = "UPDATE grpgusers SET money = '$newMoney' WHERE id = '$userId'";
            mysql_query($updateMoneyQuery);

            // Send event notification
            // Replace 'send_event' with your actual event notification function
            send_event($userId, "You have been nuked!");

            $affectedUsers++;
            $totalDeductedMoney += $deductedMoney;
        }

        // Feedback to the user who used the nuke
        echo "Nuke deployed! Affected users: $affectedUsers. Total cash earned: $" . number_format($totalDeductedMoney);
     } else {
        // Form to select city
        echo "<form action='inventory.php' method='post'>";
        echo "<input type='hidden' name='item_id' value='197'>";

        // Dropdown for cities
        echo "<select name='selected_city'>";

        // Debug: Ensure connection to the database
        if (!mysql_ping()) {
            echo "<option disabled>Error: Database connection lost.</option>";
        } else {
            $citiesQuery = "SELECT id, name FROM cities";
            $citiesResult = mysql_query($citiesQuery);

            // Debug: Check if cities result is valid
            if (!$citiesResult) {
                echo "<option disabled>Error: Unable to fetch cities. MySQL error: " . mysql_error() . "</option>";
            } else {
                $numCities = mysql_num_rows($citiesResult);
                if ($numCities > 0) {
                    while ($city = mysql_fetch_assoc($citiesResult)) {
                        echo "<option value='" . htmlspecialchars($city['id']) . "'>" . htmlspecialchars($city['name']) . "</option>";
                    }
                } else {
                    echo "<option disabled>No cities found.</option>";
                }
            }
        }
        echo "</select>";

        // Submit button
        echo "<input type='submit' value='Use Nuke'>";
        echo "</form>";
    }
    break;

 case 166:
    $db->query("UPDATE grpgusers SET outofjail = outofjail + 20 WHERE id = ?");
 $db->execute(array(
               $user_class->id
    ));

                echo Message("You have added 20 Minutes to your Out of Jail Pass.");
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

 case 195:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db->query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db->execute(
                    array(
                        $user_class->id,
                    )
                );
                $rows = $db->fetch_row();
                echo '<p>Who do you wish to send a Pumpkin Head to?</p>';
                echo '<form method="POST" action="?">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option value='{$row['id']}'>{$row['username']} (ID: {$row['id']})</option>";
                }
                echo '</select><br><br>';
                echo '<input type="submit" name="Pumpkin" value="Send" />';
                echo '</form>';
                echo '<h4>You will both recieve a random reward!</h4>';
                echo '</div>';
                diefun('');
                break;




 case 198:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db->query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db->execute(
                    array(
                        $user_class->id,
                    )
                );
                $rows = $db->fetch_row();
                echo '<p>Who do you wish to throw a Snowball at?</p>';
                echo '<form method="POST" action="?">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option value='{$row['id']}'>{$row['username']} (ID: {$row['id']})</option>";
                }
                echo '</select><br><br>';
                echo '<input type="submit" name="snowball" value="Send" />';
                echo '</form>';
                echo '<h4>You will both recieve a random reward!</h4>';
                echo '</div>';
                diefun('');
                break;





            case 156:
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
 break;
 case 158:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db->query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db->execute(
                    array(
                        $user_class->id,
                    )
                );
                $rows = $db->fetch_row();
                echo '<p>Who do you wish to send Fireworks to?</p>';
                echo '<form method="POST" action="?">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option value='{$row['id']}'>{$row['username']}</option>";
                }
                echo '</select><br><br>';
                echo '<input type="submit" name="fireworks" value="Send" />';
                echo '</form>';
                echo '<h4>You will both recieve a random reward!</h4>';
                echo '</div>';
                diefun('');
 break;


case 159:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db->query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db->execute(
                    array(
                        $user_class->id,
                    )
                );
                $rows = $db->fetch_row();
                echo '<p>Who do you wish to send Rayz to?</p>';
                echo '<form method="POST" action="?">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option value='{$row['id']}'>{$row['username']}</option>";
                }
                echo '</select><br><br>';
                echo '<input type="submit" name="rayz" value="Send" />';
                echo '</form>';
                echo '<h4>You will both recieve a random reward!</h4>';
                echo '</div>';
                diefun('');
 break;



case 167:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db->query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db->execute(
                    array(
                        $user_class->id,
                    )
                );
                $rows = $db->fetch_row();
                echo '<p>Who do you wish to send Christmas Present to?</p>';
                echo '<form method="POST" action="?">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option value='{$row['id']}'>{$row['username']}</option>";
                }
                echo '</select><br><br>';
                echo '<input type="submit" name="presents" value="Send" />';
                echo '</form>';
                echo '<h4>You will both recieve a random reward!</h4>';
                echo '</div>';
                diefun('');
 break;


case 165:
                echo '<div class="floaty" style="padding:15px;font-size:14px">';
                $db->query("SELECT id, username FROM grpgusers WHERE id != ? ORDER BY username");
                $db->execute(
                    array(
                        $user_class->id,
                    )
                );
                $rows = $db->fetch_row();
                echo '<p>Who do you wish to send a Ghost to?</p>';
                echo '<form method="POST" action="?">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option value='{$row['id']}'>{$row['username']}</option>";
                }
                echo '</select><br><br>';
                echo '<input type="submit" name="ghosts" value="Send" />';
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
                echo '<p>Who do you wish to send a Easter Egg to?</p>';
                echo '<form method="POST" action="?">';
                echo '<select name="target" style="margin-right: 15px;">';
                foreach ($rows as $row) {
                    echo "<option value='{$row['id']}'>{$row['username']}</option>";
                }
                echo '</select><br><br>';
                echo '<input type="submit" name="easter" value="Send" />';
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
    echo '<br /><a href="customitems.php" style="color:#00ff00;font-weight:bold;font-size:20px;"> &gt; You can rename your custom items by clicking this link. &lt; </a><br />';
genHead("<h1>Equipped</h1>");
echo '<hr>';
echo '<table width="100%">';
echo '<tr>';
echo '<td width="33.3%" align="center">';
if ($user_class->eqweapon != 0) {
    echo image_popup($user_class->weaponimg, $user_class->eqweapon);
    echo '<br />';
    echo item_popup($user_class->weaponname, $user_class->eqweapon);
    echo '<br />';
    echo '<a class="button-sm" href="equip.php?unequip=weapon">Unequip</a>';
} else
    echo '<img width="100" height="100" src="/css/images/empty.jpg" /><br /> You are not holding a weapon.';
echo '</td>';
echo '<td width="33.3%" align="center">';
if ($user_class->eqarmor != 0) {
    echo image_popup($user_class->armorimg, $user_class->eqarmor);
    echo '<br />';
    echo item_popup($user_class->armorname, $user_class->eqarmor);
    echo '<br />';
    echo '<a class="button-sm" href="equip.php?unequip=armor">Unequip</a>';
} else
    echo '<img width="100" height="100" src="/css/images/empty.jpg" /><br /> You are not wearing armour';
echo '</td>';
echo '<td width="33.3%" align="center">';
if ($user_class->eqshoes != 0) {
    echo image_popup($user_class->shoesimg, $user_class->eqshoes);
    echo '<br />';
    echo item_popup($user_class->shoesname, $user_class->eqshoes);
    echo '<br />';
    echo '<a class="button-sm" href="equip.php?unequip=shoes">Unequip</a>';
} else
    echo '<img width="100" height="100" src="/css/images/empty.jpg" /><br /> You are not wearing boots.';
echo '</td>';
echo '</tr>';
echo '</table>';
echo '</td>';
echo '</tr>';
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

    if ($row['offense'] > 0 && ($row['defense'] > 0 || $row['speed'] > 0)) {
        if ($row['offense'] > $row['defense']) {
            if ($row['offense'] > $row['speed']) {
                $type = 'weapon';
            } else {
                $type = 'shoes';
            }
        } else if ($row['defense'] > $row['speed']) {
            $type = 'armor';
        } else {
            $type = 'shoes';
        }
    } else {
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
    } elseif ($row['awake_boost'] > 0) {
            $type = 'house';
    } else
        $type = 'consumable';
    }
    gendivs($row, $type, $sell, $subtype);
}
$db->query("SELECT *, gl.id as loanid FROM gang_loans gl JOIN items i ON gl.item = i.id WHERE idto = ?");
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
    } elseif ($row['awake_boost'] > 0) {
        $type = 'house';
    } else
        $type = 'consumable';

    gendivs($row, 'loans', null, ($subtype != '') ? $subtype : $type, 1, $row['loanid']);
}
// if ($user_class->id == 1) {
echo '<div class="floaty"><h1>Specials</h1>';
if ($user_class->donate_token > 0) {
    echo '<div class="flexcont" border = "thick solid #0000FF"; style="text-align:center;position: relative;flex-flow:row wrap;">';
    echo image_popup('css/newgame/items/donate_boost.png', 156) . '<br/>';
    echo '<span class="text-14">x' . $user_class->donate_token . '</span><br/>';
    echo '<a class="text-14 text-yellow" href="store.php">Boost Donation</a><br/><br/>';
    echo '<a class="text-14 text-yellow" href="inventory.php?exchangetoken">Exchange x1 for 15,000 Points</a>
    </div>';
}
echo '</div>';
//}
$master = array(
    'Weapons' => 'weapon',
    'Armor' => 'armor',
    'Shoes' => 'shoes',
    'Gang Loans' => 'loans',
    'Rares' => 'rare',
    'Consumables' => 'consumable',
    'Home Improvements' => 'house'
);
foreach ($master as $header => $var)
    if (isset($$var)) {
        // genHead($header);
        echo '<div class="floaty">';
        echo '<h1>' . $header . '</h1>';
        echo '<hr />';
        echo '<div class="flexcont" border = "thick solid #0000FF"; style="text-align:center;position: relative;flex-flow:row wrap;">';
        echo $$var;
        echo '</div>';
        echo '</div>';
    }
include 'footer.php';
function gendivs($row, $type, $sell = null, $subtype = null, $loan = null, $loanid = null)
{
    global $$type, $user_class;
    $$type .= '<div class="flex-container" style="display:inline-block; padding:5px;">';
    $$type .= '<span class="flexele" style="flex-basis:25%;margin-bottom:12px;margin-top:12px;">';
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

    // Add buttons conditionally based on item id and type
    $buttonHtml = '';
    if (in_array($row['id'], [155, 195, 156, 157, 194, 158, 159, 165, 167])) {
        switch ($row['id']) {
            case 155:
                $buttonHtml .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Share The Love</a> ';
                break;
            case 194:
                $buttonHtml .= ' <a class="button-sm" href="raids.php">Use Speedup</a> ';
                break;
            case 195:
                $buttonHtml .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Trick Or Treat</a> ';
                break;
            case 156:
                $buttonHtml .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Share</a> ';
                break;
            case 157:
                $buttonHtml .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Send Egg</a> ';
                break;
            case 158:
                $buttonHtml .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Independence!</a> ';
                break;
            case 159:
                $buttonHtml .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Send Rayz</a> ';
                break;
            case 165:
                $buttonHtml .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Send Ghosts</a> ';
                break;
            case 167:
                $buttonHtml .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Send Christmas Present</a> ';
                break;
        }
    }
            if($loan){
                $buttonHtml .= '<a  class="button-sm" href="returnitem.php?ret=' . $loanid . '">Return to gang</a>';
            }
        

    // Append buttons if condition is met
    if (!empty($buttonHtml)) {
        $$type .= $buttonHtml;
    }
        
    // Add other buttons based on type and loan condition
    if ($type == "consumable" && !in_array($row['id'], [285, 155, 195, 156, 157, 194, 158, 159, 165, 167])) {
        $$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Use</a> ';
    }
    if ($type == "rare"  && !in_array($row['id'], [285, 155, 195, 209, 231, 210, 250, 211, 229, 230, 212, 156, 194, 68, 69, 157, 158, 159, 165, 167, 264])) {
        $$type .= ' <a class="button-sm" href="inventory.php?use=' . $row['id'] . '">Use</a> ';
    }
    if (!$loan && !in_array($row['id'], [155, 195, 156, 194, 157, 158, 159, 165, 167, 256])) {
        $$type .= ' <a class="button-sm" href="putonmarket.php?id=' . $row['id'] . '">Market</a> ';
    }
    if (!in_array($row['id'], [155, 195, 157, 194, 156, 158, 159, 167, 256])) {
        $$type .= ' <a class="button-sm" href="senditem.php?id=' . $row['id'] . '">Send</a> ';
    }

    // Equipment buttons
    if (in_array($type, array('weapon', 'armor', 'shoes')) || in_array($subtype, array('weapon', 'armor', 'shoes'))) {
        $$type .= ' <a class="button-sm" href="equip.php?eq=';
        $$type .= (!empty($subtype)) ? $subtype : $type;
        $$type .= '&id=' . $row['id'];
        $$type .= ($loan) ? '&loaned=1' : '';
        $$type .= '">Equip</a> ';
    }

    $$type .= '</span>';
    $$type .= '</div>'; // Close flex-container
}
?>

<?php if ($user_class->admin > 0): ?>
<!--    <script type="text/javascript">-->
<!--        $('a').click(function(e) {-->
<!--            e.preventDefault();-->
<!---->
<!--            alert('g');-->
<!---->
<!--            window.location.replace($(this).attr('href'));-->
<!---->
<!--        });-->
<!--    </script>-->
<?php endif; ?>

