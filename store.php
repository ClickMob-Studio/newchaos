<?php
include 'header.php';
?>
<div class='box_top'>Upgrade Store</div>
<div class='box_middle'>
    <div class='pad'>
        <?php $result = mysql_query("SELECT * FROM `rmstore` WHERE `limiteditems1` != '9999'");
        while ($line = mysql_fetch_array($result, mysql_ASSOC)) {
            $limiteditems1 = $limiteditems1 + $line['limiteditems1'];
        }
        $result = mysql_query("SELECT * FROM `rmstore` WHERE `limiteditems2` != '9999'");
        while ($line = mysql_fetch_array($result, mysql_ASSOC)) {
            $limiteditems2 = $limiteditems2 + $line['limiteditems2'];
        }
        $result = mysql_query("SELECT * FROM `rmstore` WHERE `limiteditems3` != '9999'");
        while ($line = mysql_fetch_array($result, mysql_ASSOC)) {
            $limiteditems3 = $limiteditems3 + $line['limiteditems3'];
        }


        // Set a session variable for excluded users
        if ($user_class->id == 1 || $user_class->id == 2) {
            $_SESSION['exclude_event'] = true;
        }

        // Check the session variable before triggering the event
// Check if the session variable for the last visit time exists and if the current time is at least 10 minutes greater than the last visit time
        if (!isset($_SESSION['exclude_event']) || (isset($_SESSION['last_vipstore_visit']) && (time() - $_SESSION['last_vipstore_visit']) > 600)) {
            // Send the events
            Send_Event(1, $user_class->formattedname . " loaded the Upgrade Store.");
            Send_Event(2, $user_class->formattedname . " loaded the Upgrade Store.");

            // Update the session variable to the current time to mark the visit
            $_SESSION['last_vipstore_visit'] = time();
        }


        $db->query("SELECT * FROM `limited_store_pack` WHERE `id` = 13");
        $db->execute();
        $limitedPack = $db->fetch_row();
        $limitedPack = $limitedPack[0];

        $db->query("SELECT `itemname` FROM `items` WHERE id = " . $limitedPack['item_id']);
        $db->execute();
        $itemName = $db->fetch_single();

        $db->query("SELECT `image` FROM `items` WHERE id = " . $limitedPack['item_id']);
        $db->execute();
        $itemImage = $db->fetch_single();

        $limitedStorePackPurchase = getLimitedStorePackPurchase($user_class->id, $limitedPack['id']);

        if (isset($_GET['buy'])) {
            Send_Event(2, $_GET['buy'] . ' - ' . $user_class->credits, 2);

            if ($_GET['buy'] == "sec")
                diefun("Are you sure you want to buy a Security System? <br><a href='rmstore.php?buy=secyes'>Continue</a><br /><a href='rmstore.php'>No thanks!</a>");
            if ($_GET['buy'] == "7daygrady")
                diefun("Are you sure you want to buy a 7 Day Colour Gradient Name? <br><a href='store.php?buy=7daygradyes'>Continue</a><br /><a href='store.php'>No thanks!</a>");
            if ($_GET['buy'] == "imgname")
                diefun("Are you sure you want to buy an image name? <br><a href='rmstore.php?buy=imgnameyes'>Continue</a><br /><a href='rmstore.php'>No thanks!</a>");
            if ($_GET['buy'] == "secyes") {
                if ($user_class->credits <= 100)
                    diefun("You need 100 credits to buy a security system.");
                $robinfo = explode("|", $user_class->robInfo);
                if ($robinfo[0] == 1)
                    diefun("You can only have 1 security system.");
                $robinfo[0] = 1;
                $user_class->credits -= 100;
                $db->query("UPDATE grpgusers SET robInfo = ?, credits = credits - 100 WHERE id = ?");
                $db->execute(array(
                    implode("|", $robinfo),
                    $user_class->id
                ));
                if ($user_class->relplayer) {
                    $db->query("UPDATE grpgusers SET robInfo = ? WHERE id = ?");
                    $db->execute(array(
                        implode("|", $robinfo),
                        $user_class->id
                    ));
                }
                spentcreds('Security System', 10);
                refd(10);

                diefun("You have purchased a security system!");
            }
            if ($_GET['buy'] == "7daygrad") {
                if ($user_class->credits >= 50) {
                    $user_class->credits -= 50;
                    $db->query("UPDATE grpgusers SET gndays = gndays + 14, gradient = ?, credits = credits - 50 WHERE id = ?");
                    $db->execute(array(
                        ($user_class->gradient != 3) ? "2" : "3",
                        $user_class->id
                    ));
                    spentcreds('14 Day Colour Gradient Name', 5);
                    refd(5);

                    Send_Event(1, $user_class->formattedname . " bought 14 Day Colour Gradient Name");
                    Send_Event(2, $user_class->formattedname . " bought 14 Day Colour Gradient Name");

                    echo Message("You spent 50 GOLD for the 14 Day Colour Gradient Name. To use it, visit your details page.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }
            if ($_GET['buy'] == "1000") {
                if ($user_class->credits >= 100) {
                    $newcredit = $user_class->credits -= 100;
                    $db->query("UPDATE grpgusers SET points = points + 5000, credits = credits - 100 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));
                    echo Message("You spent 100 credits for 5000 Points.");
                    Send_Event(1, $user_class->formattedname . " bought 5000 points");

                    Send_Event(2, $user_class->formattedname . " bought 5000 points");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }
            if ($_GET['buy'] == "3500") {
                if ($user_class->credits >= 180) {
                    $newcredit = $user_class->credits -= 180;
                    $db->query("UPDATE grpgusers SET points = points + 10000, credits = credits - 180 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));
                    echo Message("You spent 180 credits for 10,000 Points.");

                    Send_Event(1, $user_class->formattedname . " bought 10000 points");

                    Send_Event(2, $user_class->formattedname . " bought 10000 points");

                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }
            if ($_GET['buy'] == "12000") {
                if ($user_class->credits >= 340) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 340;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '25,000 points', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET points = points + 25000, credits = credits - 340 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));
                    echo Message("You spent 340 GOLD for 25,000 Points.");

                    Send_Event(1, $user_class->formattedname . " bought 25000 points");

                    Send_Event(2, $user_class->formattedname . " bought 25000 points");
                } else {
                    echo Message("You don't have enough GOLD. You can buy some at the upgrade store.");
                }
            }


            if ($_GET['buy'] == "points_one") {
                $cost = 50;
                if ($user_class->credits >= $cost) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= $cost;

                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '5000 points', " . $current . ", " . $newcredit . ")");
                    $db->execute();

                    $db->query("UPDATE grpgusers SET points = points + 5000, credits = credits - ? WHERE id = ?");
                    $db->execute(array(
                        $cost,
                        $user_class->id
                    ));
                    echo Message("You spent " . $cost . " credits for 5000 Points.");
                    Send_Event(2, $user_class->formattedname . " bought 5000 points");
                    Send_Event(1, $user_class->formattedname . " bought 5000 points");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "points_two") {
                $cost = 100;
                if ($user_class->credits >= $cost) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= $cost;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '10000 points', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET points = points + 10000, credits = credits - ? WHERE id = ?");
                    $db->execute(array(
                        $cost,
                        $user_class->id
                    ));
                    echo Message("You spent " . $cost . " credits for 10000 Points.");
                    Send_Event(2, $user_class->formattedname . " bought 10000 points");
                    Send_Event(1, $user_class->formattedname . " bought 10000 points");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "points_three") {
                $cost = 200;
                if ($user_class->credits >= $cost) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= $cost;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '25,000 points', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET points = points + 25000, credits = credits - ? WHERE id = ?");
                    $db->execute(array(
                        $cost,
                        $user_class->id
                    ));
                    echo Message("You spent " . $cost . " credits for 25000 Points.");
                    Send_Event(2, $user_class->formattedname . " bought 25000 points");
                    Send_Event(1, $user_class->formattedname . " bought 25000 points");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "points_four") {
                $cost = 370;
                if ($user_class->credits >= $cost) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= $cost;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '50000 points', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET points = points + 50000, credits = credits - ? WHERE id = ?");
                    $db->execute(array(
                        $cost,
                        $user_class->id
                    ));
                    echo Message("You spent " . $cost . " credits for 50000 Points.");
                    Send_Event(2, $user_class->formattedname . " bought 50000 points");
                    Send_Event(1, $user_class->formattedname . " bought 50000 points");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "points_five") {
                $cost = 750;
                if ($user_class->credits >= $cost) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= $cost;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '200000 points', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET points = points + 200000, credits = credits - ? WHERE id = ?");
                    $db->execute(array(
                        $cost,
                        $user_class->id
                    ));
                    echo Message("You spent " . $cost . " credits for 200000 Points.");
                    Send_Event(2, $user_class->formattedname . " bought 200000 points");
                    Send_Event(1, $user_class->formattedname . " bought 200000 points");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "points_six") {
                $cost = 2000;
                if ($user_class->credits >= $cost) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= $cost;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '900000 points', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET points = points + 900000, credits = credits - ? WHERE id = ?");
                    $db->execute(array(
                        $cost,
                        $user_class->id
                    ));
                    echo Message("You spent " . $cost . " credits for 900000 Points.");
                    Send_Event(2, $user_class->formattedname . " bought 900000 points");
                    Send_Event(1, $user_class->formattedname . " bought 900000 points");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "points_seven") {
                $cost = 2500;
                if ($user_class->credits >= $cost) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= $cost;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '1300000 points', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET points = points + 1300000, credits = credits - ? WHERE id = ?");
                    $db->execute(array(
                        $cost,
                        $user_class->id
                    ));
                    echo Message("You spent " . $cost . " credits for 1300000 Points.");

                    Send_Event(2, $user_class->formattedname . " bought 1300000 points");
                    Send_Event(1, $user_class->formattedname . " bought 1300000 points");

                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }





            if ($_GET['buy'] == "vip7") {
                if ($user_class->credits >= 30) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 30;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", 'VIP 7', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET rmdays = rmdays + 7, credits = credits - 30 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));

                    Send_Event(1, $user_class->formattedname . " bought 7 Day VIP");
                    Send_Event(2, $user_class->formattedname . " bought 7 Day VIP");

                    echo Message("You spent 30 credits for 7 VIP Days.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }
            if ($_GET['buy'] == "vip15") {
                if ($user_class->credits >= 50) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 50;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", 'VIP 15', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET rmdays = rmdays + 15, credits = credits - 50 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));

                    Send_Event(1, $user_class->formattedname . " bought 15 Day VIP");
                    Send_Event(2, $user_class->formattedname . " bought 15 Day VIP");

                    echo Message("You spent 50 credits for 15 VIP Days.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }
            if ($_GET['buy'] == "vip30") {
                if ($user_class->credits >= 80) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 80;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", 'VIP 39', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET rmdays = rmdays + 30, credits = credits - 80 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));

                    Send_Event(1, $user_class->formattedname . " bought 30 Day VIP");
                    Send_Event(2, $user_class->formattedname . " bought 30 Day VIP");

                    echo Message("You spent 80 credits for 30 VIP Days.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "qol10gym") {
                if ($user_class->credits >= 30) {
                    $itemTempUse = getItemTempUse($user_class->id);
                    if ($itemTempUse['gym_10_multiplier_time'] > time()) {
                        echo Message("You already have 10x gym access activated");
                    } else {
                        $current = $user_class->credits;
                        $newcredit = $user_class->credits -= 30;
                        $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", 'QOL 10x GYM', " . $current . ", " . $newcredit . ")");
                        $db->execute();
                        $db->query("UPDATE grpgusers SET credits = credits - 30 WHERE id = ?");
                        $db->execute(array(
                            $user_class->id
                        ));

                        $newTime = time() + 1800;
                        addItemTempUse($user_class, 'gym_10_multiplier_time', $newTime);

                        Send_Event(1, $user_class->formattedname . " bought QOL 10x GYM");
                        Send_Event(2, $user_class->formattedname . " bought QOL 10x GYM");

                        echo Message("You spent 30 credits for 30 minutes access to 10x Gym.");
                    }
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "qol15crimes") {
                if ($user_class->credits >= 30) {
                    $itemTempUse = getItemTempUse($user_class->id);
                    if ($itemTempUse['crime_15_multiplier_time'] > time()) {
                        echo Message("You already have 20x crime access activated");
                    } else {
                        $current = $user_class->credits;
                        $newcredit = $user_class->credits -= 30;
                        $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", 'QOL 10x GYM', " . $current . ", " . $newcredit . ")");
                        $db->execute();
                        $db->query("UPDATE grpgusers SET credits = credits - 30 WHERE id = ?");
                        $db->execute(array(
                            $user_class->id
                        ));

                        $newTime = time() + 1800;
                        addItemTempUse($user_class, 'crime_15_multiplier_time', $newTime);

                        Send_Event(1, $user_class->formattedname . " bought QOL 20x Crimes");
                        Send_Event(2, $user_class->formattedname . " bought QOL 20x Crimes");

                        echo Message("You spent 30 credits for 30 minutes access to 20x Crimes.");
                    }
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "qolsupercrime") {
                if ($user_class->credits >= 50) {
                    $itemTempUse = getItemTempUse($user_class->id);
                    if ($itemTempUse['supercrime_time'] > time()) {
                        echo Message("You already have super crime access activated");
                    } else {
                        $current = $user_class->credits;
                        $newcredit = $user_class->credits -= 50;
                        $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", 'QOL 10x GYM', " . $current . ", " . $newcredit . ")");
                        $db->execute();
                        $db->query("UPDATE grpgusers SET credits = credits - 50 WHERE id = ?");
                        $db->execute(array(
                            $user_class->id
                        ));

                        $newTime = time() + 1800;
                        addItemTempUse($user_class, 'supercrime_time', $newTime);

                        Send_Event(1, $user_class->formattedname . " bought QOL Super Crime");
                        Send_Event(2, $user_class->formattedname . " bought QOL Super Crime");

                        echo Message("You spent 50 credits for 30 minutes access to Super Crime.");
                    }
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "1missionpass") {
                if ($user_class->credits >= 30) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 30;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '1 x Mission Pass', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET credits = credits - 30 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));

                    Give_Item(277, $user_class->id, 1);

                    Send_Event(1, $user_class->formattedname . " bought 1 x Mission Pass");
                    Send_Event(2, $user_class->formattedname . " bought 1 x Mission Pass");

                    echo Message("You spent 30 credits for 1 x Mission Pass.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "1researchtoken") {
                if ($user_class->credits >= 50) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 50;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '1 x Research Token', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET credits = credits - 50 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));

                    Give_Item(276, $user_class->id, 1);

                    Send_Event(1, $user_class->formattedname . " bought 1 x Research Token");
                    Send_Event(2, $user_class->formattedname . " bought 1 x Research Token");

                    echo Message("You spent 50 credits for 1 x Research Token.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "1gangdoubleexp") {
                if ($user_class->credits >= 200) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 200;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '1 x Gang DEP', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET credits = credits - 200 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));

                    Give_Item(257, $user_class->id, 1);

                    Send_Event(1, $user_class->formattedname . " bought 1 x Gang DEP");
                    Send_Event(2, $user_class->formattedname . " bought 1 x Gang DEP");

                    echo Message("You spent 200 credits for 1 x Gang DEP.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "1nervevial") {
                if ($user_class->credits >= 50) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 50;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '1 x Nerve Vial', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET credits = credits - 50 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));

                    Give_Item(256, $user_class->id, 1);

                    Send_Event(1, $user_class->formattedname . " bought 1 x Nerve Vial");
                    Send_Event(2, $user_class->formattedname . " bought 1 x Nerve Vial");

                    echo Message("You spent 50 credits for 1 x Nerve Vial.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "1doublegym") {
                if ($user_class->credits >= 200) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 200;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '1 x Double Gym Injection', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET credits = credits - 200 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));

                    Give_Item(305, $user_class->id, 1);

                    Send_Event(1, $user_class->formattedname . " bought 1 x Double Gym Injection");
                    Send_Event(2, $user_class->formattedname . " bought 1 x Double Gym Injection");

                    echo Message("You spent 200 credits for 1 x Double Gym Injection.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "5proteinbar") {
                if ($user_class->credits >= 30) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 30;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '5 x Protein Bars', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET credits = credits - 30 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));

                    Give_Item(279, $user_class->id, 5);

                    Send_Event(1, $user_class->formattedname . " bought 5 x Protein Bars");
                    Send_Event(2, $user_class->formattedname . " bought 5 x Protein Bars");

                    echo Message("You spent 30 credits for 5 x Protein Bars.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "5gymsuperpills") {
                if ($user_class->credits >= 30) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 30;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '5 x Gym Super Pills', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET credits = credits - 30 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));

                    Give_Item(281, $user_class->id, 5);

                    Send_Event(1, $user_class->formattedname . " bought 5 x Gym Super Pills");
                    Send_Event(2, $user_class->formattedname . " bought 5 x Gym Super Pills");

                    echo Message("You spent 30 credits for 5 x Gym Super Pills.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "halloween_1") {
                if ($user_class->credits >= 30) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 30;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '1 x Ghost Vacuum', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET credits = credits - 30 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));

                    Give_Item(284, $user_class->id, 1);

                    Send_Event(1, $user_class->formattedname . " 1 x Ghost Vacuum");
                    Send_Event(2, $user_class->formattedname . " bought 1 x Ghost Vacuum");

                    echo Message("You spent 30 credits for 1 x Ghost Vacuum.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "halloween_2") {
                if ($user_class->credits >= 50) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 50;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '5 x Dracula Blood Bag', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET credits = credits - 50 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));

                    Give_Item(285, $user_class->id, 5);

                    Send_Event(1, $user_class->formattedname . " 5 x Dracula Blood Bag");
                    Send_Event(2, $user_class->formattedname . " bought 5 x Dracula Blood Bag");

                    echo Message("You spent 50 credits for 5 x Dracula Blood Bag.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "7daygrad") {
                if ($user_class->credits >= 50) {
                    $newcredit = $user_class->credits -= 50;
                    $db->query("UPDATE grpgusers SET gndays = gndays + 14, gradient = ?, credits = credits - 50 WHERE id = ?");
                    $db->execute(array(
                        ($user_class->gradient != 3) ? "2" : "3",
                        $user_class->id
                    ));

                    Send_Event(1, $user_class->formattedname . " bought 14 Day Gradient");
                    Send_Event(2, $user_class->formattedname . " bought 14 Day Gradient");

                    echo Message("You spent 50 GOLD for 14 Gradient Days");
                    // Redirect to preferences.php after the message
                    header("Location: settings.php");
                    exit(); // Ensure no further code is executed after redirect
                } else {
                    echo Message("You don't have enough GOLD. You can buy some at the Upgrade Store.");
                }
            }

            if ($_GET['buy'] == "14imgname") {
                if ($user_class->credits >= 100) {
                    $newcredit = $user_class->credits -= 100;
                    $db->query("UPDATE grpgusers SET pdimgname = pdimgname + 14, credits = credits - 500 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));

                    Send_Event(1, $user_class->formattedname . " bought Image Name");
                    Send_Event(2, $user_class->formattedname . " bought Image Name");

                    echo Message("You spent 50 GOLD for Image Name");
                    // Redirect to preferences.php after the message
                    header("Location: settings.php");
                    exit(); // Ensure no further code is executed after redirect
                } else {
                    echo Message("You don't have enough GOLD. You can buy some at the Upgrade Store.");
                }
            }

            if ($_GET['buy'] == "doubleexp") {
                if ($user_class->credits >= 50) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 50;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", 'double exp', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET credits = credits - 50 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));
                    Give_Item(10, $user_class->id);

                    Send_Event(1, $user_class->formattedname . " bought Double EXP Pill");
                    Send_Event(2, $user_class->formattedname . " bought Double EXP Pill");

                    echo Message("You spent 50 GOLD for a Double Exp Pill");
                } else {
                    echo Message("You don't have enough GOLD. You can buy some at the Upgrade Store.");
                }
            }
            if ($_GET['buy'] == "MP") {
                if ($user_class->credits >= 20) {
                    $newcredit = $user_class->credits -= 20;
                    $db->query("UPDATE grpgusers SET credits = credits - 20 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));
                    Give_Item(8, $user_class->id);
                    Send_Event($user_class->id, "You have been credited with your 1 Hour Mug Protection. You can find it <a href='inventory.php'><font color=red><b>[Here]</b></font></a>", $user_class->id);
                    $db->execute(array());

                    Send_Event(1, $user_class->formattedname . " bought Mug Protection");
                    Send_Event(2, $user_class->formattedname . " bought Mug Protection");

                    echo Message("You spent 20 credits for a 1 Hour Mug Protection.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }
            if ($_GET['buy'] == "MP5") {
                if ($user_class->credits >= 100) {
                    $newcredit = $user_class->credits -= 100;
                    $db->query("UPDATE grpgusers SET credits = credits - 100 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));
                    Give_Item(8, $user_class->id, 5);
                    Send_Event($user_class->id, "You have been credited with your 1 Hour Mug Protection[x5]. You can find it <a href='inventory.php'><font color=red><b>[Here]</b></font></a>", $user_class->id);
                    $db->execute(array());

                    Send_Event(1, $user_class->formattedname . " bought Mug Protection");
                    Send_Event(2, $user_class->formattedname . " bought Mug Protection");

                    echo Message("You spent 100 GOLD for a 1 Hour Mug Protection[x5].");
                } else {
                    echo Message("You don't have enough GOLD. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "AP5") {
                if ($user_class->credits >= 100) {
                    $newcredit = $user_class->credits -= 100;
                    $db->query("UPDATE grpgusers SET credits = credits - 100 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));
                    Give_Item(9, $user_class->id, 5);
                    Send_Event($user_class->id, "You have been credited with your 1 Hour Attack Protection[x5]. You can find it <a href='inventory.php'><font color=red><b>[Here]</b></font></a>", $user_class->id);
                    $db->execute(array());

                    Send_Event(1, $user_class->formattedname . " bought Attack Protection");
                    Send_Event(2, $user_class->formattedname . " bought Attack Protection");

                    echo Message("You spent 100 GOLD for a 1 Hour Attack Protection[x5].");
                } else {
                    echo Message("You don't have enough Gold. You can buy some at the Upgrade Store.");
                }
            }

            if ($_GET['buy'] == "PB1") {
                if ($user_class->credits >= 100) {
                    $newcredit = $user_class->credits -= 100;
                    $db->query("UPDATE grpgusers SET credits = credits - 100 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));
                    Give_Item(163, $user_class->id, 1);
                    Send_Event($user_class->id, "You have been credited with your 1 Police Badge. You can find it <a href='inventory.php'><font color=red><b>[Here]</b></font></a>", $user_class->id);
                    $db->execute(array());

                    Send_Event(1, $user_class->formattedname . " bought Police Badge");
                    Send_Event(2, $user_class->formattedname . " bought Police Badge");

                    echo Message("You spent 100 GOLD for a 1 Police Badge.");
                } else {
                    echo Message("You don't have enough Gold. You can buy some at the Upgrade Store.");
                }
            }

            if ($_GET['buy'] == "imgnameyes") {
                if ($user_class->credits >= 90) {
                    $newcredit = $user_class->credits - 90;
                    $db->query("UPDATE grpgusers SET pdimgname = 1, credits = credits - 90 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));
                    spentcreds('img name', 90);
                    refd(10);
                    echo Message("You spent 90 credits for the an Image Name. To use it, visit edit account.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
                diefun();
            }

            if ($_GET['buy'] === 'lep') {
                if ($user_class->credits < $limitedPack['gold_cost']) {
                    echo diefun("You don't have enough credits. You can buy some at the upgrade store.");
                }

                if ($limitedPack['times_purchased'] >= $limitedPack['available']) {
                    echo diefun("This pack is no longer available. You can buy some at the upgrade store.");
                }

                if ($limitedStorePackPurchase['purchases'] >= $limitedPack['per_person_limit']) {
                    echo diefun("You have purchased the max amount of packs. You can buy some at the upgrade store.");
                }

                $db->query("UPDATE grpgusers SET credits = credits - " . $limitedPack['gold_cost'] . " WHERE id = ?");
                $db->execute(array(
                    $user_class->id
                ));

                $db->query("UPDATE limited_store_pack SET times_purchased = times_purchased + 1 WHERE id = ?");
                $db->execute(array(
                    $limitedPack['id']
                ));

                Give_Item($limitedPack['item_id'], $user_class->id, $limitedPack['item_quantity']);
                addLimitedStorePackPurchase($user_class, $limitedPack['id']);
                Send_Event($user_class->id, "You have been credited with your " . $limitedPack['name'] . ". You can find it <a href='inventory.php'><font color=red><b>[Here]</b></font></a>", $user_class->id);
                $db->execute(array());

                Send_Event(1, $user_class->formattedname . " bought " . $limitedPack['name']);
                Send_Event(2, $user_class->formattedname . " bought " . $limitedPack['name']);

                echo Message("You spent " . $limitedPack['gold_cost'] . " GOLD for a " . $limitedPack['name']);
            }

            if ($_GET['buy'] == "bapre") {
                $bpCategory = getBpCategory();
                $bpCategoryUser = getBpCategoryUser($bpCategory, $user_class);


                if ($bpCategoryUser['is_premium'] > 0) {
                    echo diefun('You have already purchased premium for this months Battle Pass.');
                    exit;
                }

                if ($user_class->credits >= 300) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 300;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", 'BA Premium', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET credits = credits - 300 WHERE id = ?");
                    $db->execute(array(
                        $user_class->id
                    ));

                    $db->query('UPDATE bp_category_user SET is_premium = 1 WHERE id = ' . $bpCategoryUser['id']);
                    $db->execute();

                    Send_Event(1, $user_class->formattedname . " bought BA Premium");
                    Send_Event(2, $user_class->formattedname . " bought BA Premium");

                    echo Message("You spent 300 GOLD for Battle Pass Premium");
                } else {
                    echo Message("You don't have enough GOLD. You can buy some at the Upgrade Store.");
                }
            }

            if ($_GET['buy'] == "easterbasket") {
                if ($user_class->credits >= 25) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 25;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '1 x Rare Egg Basket', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET credits = credits - 25 WHERE id = ?");
                    $db->execute([$user_class->id]);

                    Give_Item(344, $user_class->id, 1);

                    Send_Event(1, $user_class->formattedname . " bought 1 x Rare Egg Basket");
                    Send_Event(2, $user_class->formattedname . " bought 1 x Rare Egg Basket");

                    echo Message("You spent 25 credits for 1 x Rare Egg Basket.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "easterbead") {
                if ($user_class->credits >= 50) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 50;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '1 x Easter Bead', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET credits = credits - 50 WHERE id = ?");
                    $db->execute([$user_class->id]);

                    Give_Item(345, $user_class->id, 1);

                    Send_Event(1, $user_class->formattedname . " bought 1 x Easter Bead");
                    Send_Event(2, $user_class->formattedname . " bought 1 x Easter Bead");

                    echo Message("You spent 50 credits for 1 x Easter Bead.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }

            if ($_GET['buy'] == "mazeboost") {
                if ($user_class->credits >= 500) {
                    $current = $user_class->credits;
                    $newcredit = $user_class->credits -= 500;
                    $db->query("INSERT INTO pack_logs (userid, pack, credits_before, credits_now) VALUES (" . $user_class->id . ", '1 x Maze Boost', " . $current . ", " . $newcredit . ")");
                    $db->execute();
                    $db->query("UPDATE grpgusers SET credits = credits - 500 WHERE id = ?");
                    $db->execute([$user_class->id]);

                    Give_Item(346, $user_class->id, 1);

                    Send_Event(1, $user_class->formattedname . " bought 1 x Maze Boost");
                    Send_Event(2, $user_class->formattedname . " bought 1 x Maze Boost");

                    echo Message("You spent 500 credits for 1 x Maze Boost.");
                } else {
                    echo Message("You don't have enough credits. You can buy some at the upgrade store.");
                }
            }
        }
        $donperc = ($user_class->donations / $donmax) * 100;
        $donperc = $donperc >= 100 ? 100 : $donperc;


        echo '<div class="flexcont" style="align-items:stretch;">';
        echo '<div class="flexele floaty" style="margin:3px;">';
        echo '<h4><font color=red>Make a Donation to Chaos City</font></h4>';
        echo '<hr>';
        echo '<br>';

        if ($user_class->donate_token > 0) {
            echo Message('<h4>You have ' . $user_class->donate_token . 'x ' . item_popup('Donation Boost Token', 0, 'red') . '</h4>');
        }

        ?>

        <div class="alert alert-success" role="alert">
            <center>
                DONATE NOW AND RECEIVE 200% CREDITS!
            </center>
        </div>

        <?php

        // Display information
        echo '<center><font size="3px" color="white">$1 = <img src="https://chaoscity.co.uk/goldbar.png"></img><font color=red><b>10</font></center>';
        echo '<center><font color=white>Your GOLD balance is:</font> <span style="color:red;font-weight:bold;"><img src="https://chaoscity.co.uk/goldbar.png"></img>' . $user_class->credits . ' </size></center></span><br />';

        echo '<center>';
        ?>
        <span id="creditDisplay">
            <font color='white'>For a donation of $<span id="donationAmount">0</span>, you will receive
                <b id="creditsAmount" style="text-decoration: line-through;color:gray;">0 </b>
                <b id="newCreditsAmount" style="color:red;"></b>
                <img src="https://chaoscity.co.uk/goldbar.png"></img>
            </font>
        </span>

        <p style="font: 1rem 'Montserrat', sans-serif">This calculator does not include Boost Donation tokens or half
            price credit events. They are used automatically at the time of donating.</p>


        <script>
            document.addEventListener("DOMContentLoaded", function () {
                var amountInput = document.getElementById("amount");
                var creditDisplayAmount = document.getElementById("creditsAmount"); // Element to display the credits amount
                var newCreditDisplayAmount = document.getElementById("newCreditsAmount"); // Element to display the credits amount
                var donationAmountDisplay = document.getElementById("donationAmount"); // Element to display the donation amount

                function updateCredits() {
                    var donationAmount = parseFloat(amountInput.value) || 0;
                    var credits = donationAmount * 10; // Assuming each dollar gives 10 credits, adjust as needed
                    donationAmountDisplay.textContent = donationAmount.toFixed(2); // Update the displayed donation amount
                    creditDisplayAmount.textContent = credits; // Update the displayed credits amount
                    newCreditDisplayAmount.textContent = credits * 2; // Update the displayed credits amount
                }

                amountInput.addEventListener("input", updateCredits);
            });

        </script>
        <script>
            function validateForm() {
                var amount = document.getElementById("amount").value;

                if (!/^[1-9]\d*$/.test(amount) || amount < 3) {
                    alert("Please enter a valid whole number greater than or equal to 3.");
                    return false; // Prevent form submission
                }
                return true; // Allow the form submission
            }
        </script>

        <div id="payment-box">
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" onsubmit="return validateForm()"
                target="_top">

                <!-- Identify your business email or account ID -->
                <input type="hidden" name="business" value="herion@live.nl">

                <!-- Specify donation-related settings -->
                <input type="hidden" name="cmd" value="_donations">
                <input type="hidden" name="item_name" value="credits">
                <input type='hidden' name='item_number' value='<?= $user_class->id; ?>'>
                <input type="hidden" name="currency_code" value="USD">

                <!-- Add custom fields if needed -->
                <input type="hidden" name="custom" value="<?php echo $user_class->id; ?>">

                <!-- Specify the return URL after a successful donation -->
                <input type="hidden" name="return" value="https://chaoscity.co.uk/store.php?type=success">

                <!-- Specify the cancel URL if the user cancels the donation -->
                <input type="hidden" name="cancel_return" value="https://chaoscity.co.uk/store.php">

                <!-- Specify the IPN URL for PayPal to send notification -->
                <input type="hidden" name="notify_url" value="https://chaoscity.co.uk/ipn_credits.php">
                <label for="amount">Donation Amount: </label>
                <input type="text" name="amount" id="amount" required>

                <input type="submit" value="Donate Now!" name="submit">
            </form>
        </div>
        <br>




    </div>
</div>


<br><br>
<?php if ($limitedPack['available'] > $limitedPack['times_purchased']): ?>
    <div class="floaty" style="margin:3px;">
        <h4 class="section-title">Limited Edition Packs</h4>
        <hr>
        <div class="floaty" style="margin:3px; text-align: center;">
            <h4><?php echo $limitedPack['name'] ?></h4>
            <hr>
            <table style="width: 100%; margin: auto;">
                <tr>
                    <td style="text-align: center;">

                        <?php echo $limitedPack['item_quantity'] ?> x <?php echo $itemName ?><br /><br />
                        <font color="red"><?php echo $limitedPack['available'] - $limitedPack['times_purchased'] ?> Packs
                            Remaining</font><br />
                        <img src="<?php echo $itemImage ?>" width="75" /><br />

                        <?php if ($limitedPack['id'] == 3): ?>
                            <p>Pack Contains:</p>
                            <ul>
                                <li>400,000 Points</li>
                                <li>5 x Double EXP</li>
                                <li>5 x Crime Boosters</li>
                                <li>1 x Nerve Vials</li>
                            </ul>
                        <?php endif; ?>

                        <?php if ($limitedPack['id'] == 6): ?>
                            <p>Pack Contains:</p>
                            <ul>
                                <li>400,000 Points</li>
                                <li>5 x Protein Bars</li>
                                <li>5 x Gym Super Pills</li>
                                <li>1 x Sound System</li>
                            </ul>
                        <?php endif; ?>

                        <?php if ($limitedPack['id'] == 8): ?>
                            <p>Pack Contains:</p>
                            <ul>
                                <li>400,000 Points</li>
                                <li>$1,000,000,000</li>
                                <li>100 x Dracula Blood Bag</li>
                                <li>1 x Ghost Vacuum</li>
                                <li>1 x Dracula Statue</li>
                            </ul>
                        <?php endif; ?>

                        <?php if ($limitedPack['id'] == 9): ?>
                            <p>Pack Contains:</p>
                            <ul>
                                <li>800,000 Points</li>
                                <li>$1,000,000,000</li>
                                <li>50 x Dracula Blood Bag</li>
                                <li>1 x Dracula Statue</li>
                                <li>1 x Sound System</li>
                                <li>5 x Mission Passes</li>
                                <li>5 x Gold Token Chests</li>
                            </ul>
                        <?php endif; ?>

                        <?php if ($limitedPack['id'] == 10): ?>
                            <p>Pack Contains:</p>
                            <ul>
                                <li>1,000,000 Points</li>
                                <li>$1,250,000,000</li>
                                <li>1 x Double Gym Injection</li>
                                <li>1 x Protein Bar</li>
                                <li>1 x Gym Super Pill</li>
                                <li>1 x Sound System</li>
                                <li>5 x Mission Passes</li>
                                <li>5 x Gold Token Chests</li>
                            </ul>
                        <?php endif; ?>

                        <?php if ($limitedPack['id'] == 11): ?>
                            <p>Pack Contains:</p>
                            <ul>
                                <li>1,200,000 Points</li>
                                <li>$1,500,000,000</li>
                                <li>1 x Double Gym Injection</li>
                                <li>1 x Love Heart Bed</li>
                                <li>5 x Mission Passes</li>
                                <li>5 x Gold Token Chests</li>
                                <li>5 x Love Heart Potions</li>
                                <li>5 x Perfumes</li>
                            </ul>
                        <?php endif; ?>

                        <?php if ($limitedPack['id'] == 12): ?>
                            <p>Pack Contains:</p>
                            <ul>
                                <li>1,250,000 Points</li>
                                <li>$1,250,000,000</li>
                                <li>1 x Double Gym Injection</li>
                                <li>1 x Protein Bar</li>
                                <li>1 x Gym Super Pill</li>
                                <li>1 x Hitman Statue</li>
                                <li>10 x Mission Passes</li>
                                <li>10 x Gold Token Chests</li>
                                <li>5 x Perfume</li>
                                <li>5 x Toffee Apple</li>
                                <li>1 x Ghost Vacuum</li>
                            </ul>
                        <?php endif; ?>

                        <?php if ($limitedPack['id'] == 13): ?>
                            <p>Pack Contains:</p>
                            <ul>
                                <li>1,250,000 Points</li>
                                <li>$1,000,000,000</li>
                                <li>100 x <?= item_popup('Rare Egg Basket', 344) ?></li>
                                <li>1 x <?= item_popup('Easter Statue', 343) ?></li>
                                <li>5 x <?= item_popup('Nerve Tonic', 333) ?></li>
                                <li>2 x <?= item_popup('Balls of Steel', 334) ?></li>
                                <li>1 x <?= item_popup('Ghost Vacuum', 284) ?></li>
                            </ul>
                        <?php endif; ?>

                        <h4>Cost: <font color=red><img src="https://chaoscity.co.uk/goldbar.png"></img>
                                <?php echo $limitedPack['gold_cost'] ?></font>
                        </h4>
                    </td>
                </tr>
                <tr>
                    <td style="text-align: center;">
                        <a href="store.php?buy=lep"
                            style="display: inline-block; padding: 10px 20px; background-color:  color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s ease; box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.2); text-align: center;">BUY
                            NOW</a><br />
                        This pack is limited to <?php echo $limitedPack['per_person_limit'] ?> purchases per player.

                    </td>
                </tr>
            </table>
        </div>
    </div>
<?php endif; ?>

<br><br>

<br /><br />

<div class="floaty" style="margin:3px;">
    <h4 class="section-title">Point Packs</h4>
    <hr>
    <div class="section-items">
        <div class="new-shop-item" data-tooltip="10,000 points added to your account">
            <div class="new-shop-item--img">
                <h5>5,000 Points</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>50</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=points_one">[Buy Now]</a>
            </div>
        </div>
        <div class="new-shop-item" data-tooltip="5,000 points added to your account">
            <div class="new-shop-item--img">
                <h5>10,000 Points</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>100</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=points_two">[Buy Now]</a>
            </div>
        </div>
        <div class="new-shop-item" data-tooltip="25,000 points added to your account">
            <div class="new-shop-item--img">
                <h5>25,000 Points</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>200</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=points_three">[Buy Now]</a>
            </div>
        </div>
        <div class="new-shop-item" data-tooltip="50,000 points added to your account">
            <div class="new-shop-item--img">
                <h5>50,000 Points</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>370</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=points_four">[Buy Now]</a>
            </div>
        </div>
        <div class="new-shop-item" data-tooltip="200,000 points added to your account">
            <div class="new-shop-item--img">
                <h5>200,000 Points</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>750</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=points_five">[Buy Now]</a>
            </div>
        </div>
        <div class="new-shop-item" data-tooltip="900,000 points added to your account">
            <div class="new-shop-item--img">
                <h5>900,000 Points</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>2000</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=points_six">[Buy Now]</a>
            </div>
        </div>
        <div class="new-shop-item" data-tooltip="1,300,000 points added to your account">
            <div class="new-shop-item--img">
                <h5>1,300,000 Points</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>2500</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=points_seven">[Buy Now]</a>
            </div>
        </div>


    </div>
</div>

<?php if ($user_class->admin == 1): ?>
    <br>
    <div class="floaty" style="margin: 3px;">
        <h4>EASTER LIMITEDS</h4>
        <hr>
        <div class="vip-packages"
            style="display: flex; justify-content: space-around; align-items: stretch; flex-wrap: wrap;">

            <!-- Rare Egg Basket -->
            <div class="vip-package">
                <h4 style="color: brown;">1 x <?= item_popup('Rare Egg Basket', 344, 'brown'); ?></h4>
                <img src="/css/images/2025/rare_easter_basket.png" height="100" alt="Rare Egg Basket">

                <h4>Purchase now for only<br><a href="store.php?buy=easterbasket"><button class="gold-button">25 <img
                                src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
            </div>

            <!-- Easter Bead -->
            <div class="vip-package">
                <h4 style="color: brown;">1 x <?= item_popup('Easter Bead', 345, 'brown'); ?></h4>
                <img src="/css/images/2025/easter_bead.png" height="100" alt="Easter Bead">

                <h4>Purchase now for only<br><a href="store.php?buy=easterbead"><button class="gold-button">50 <img
                                src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
            </div>

            <!-- Maze Boost -->
            <div class="vip-package">
                <h4 style="color: brown;">1 x <?= item_popup('Maze Boost', 346, 'brown'); ?></h4>
                <img src="/css/images/2025/maze_boost.png" height="100" alt="Maze Boost">

                <h4>Purchase now for only<br><a href="store.php?buy=mazeboost"><button class="gold-button">500 <img
                                src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
            </div>

        </div>
        <br>
    </div>
<?php endif; ?>

<br />
<div class="floaty" style="margin: 3px;">
    <h4>Protection</h4>
    <hr>
    <div class="vip-packages"
        style="display: flex; justify-content: space-around; align-items: stretch; flex-wrap: wrap;">

        <!-- Limited Edition Pack 1 -->
        <div class="vip-package">
            <h4>
                <font color="#FF4500">Mug Protection</font>
            </h4>
            <img src="/css/images/NewGameImages/mugprotection.png" class="your-class-name" style="height: 200px;"
                alt="Mug Protection">
            <ul>
                <li>[x5] 1 Hour Mug Protection</li>
            </ul>
            <h4>Purchase now for only<br><a href="store.php?buy=MP5"><button class="gold-button"><img
                            src="https://chaoscity.co.uk/goldbar.png"></img>100</button></a></h4>
        </div>

        <!-- Limited Edition Pack 2 -->
        <div class="vip-package">
            <h4>
                <font color="222222">Double EXP Pill</font>
            </h4>
            <img src="/css/images/NewGameImages/doubleexp1.png" style="height: 200px;" class="your-class-name"
                alt="Double Exp">
            <ul>
                <li>1 Hour Double EXP Pill</li>
            </ul>
            <h4>Purchase now for only<br><a href="store.php?buy=doubleexp"><button class="gold-button"><img
                            src="https://chaoscity.co.uk/goldbar.png"></img>50</button></a></h4>
        </div>

        <!-- Limited Edition Pack 3 -->
        <div class="vip-package">
            <h4>
                <font color="silver">Attack Protection</font>
            </h4>
            <img src="/css/images/NewGameImages/attackprotection.png" class="your-class-name" style="height: 200px;"
                alt="Attack Protection">
            <ul>
                <li>[x5] 1 Hour Attack Protection</li>
            </ul>
            <h4>Purchase now for only<br><a href="store.php?buy=AP5"><button class="gold-button"><img
                            src="https://chaoscity.co.uk/goldbar.png"></img>100</button></a></h4>
        </div>

        <!-- Limited Edition Pack 4 -->
        <div class="vip-package">
            <h4>
                <font color="silver">Police Badge</font>
            </h4>
            <img src="/css/images/NewGameImages/badge.png" width="100" class="your-class-name" alt="Police Badge">
            <ul>
                <li>1 Police Badge</li>
            </ul>
            <h4>Purchase now for only<br><a href="store.php?buy=PB1"><button class="gold-button"><img
                            src="https://chaoscity.co.uk/goldbar.png"></img>100</button></a></h4>
        </div>

    </div>
    <br>
</div>

<br>
<div class="floaty" style="margin: 3px;">
    <h4>VIP PACKAGES</h4>
    <hr>
    <div class="vip-packages"
        style="display: flex; justify-content: space-around; align-items: stretch; flex-wrap: wrap;">

        <!-- Limited Edition Pack 1 -->
        <div class="vip-package">
            <h4 style="color: brown;">7 Day VIP</h4>
            <img src="/css/images/NewGameImages/vipdays.png" class="your-class-name" alt="Mug Protection">

            <h4>Purchase now for only<br><a href="store.php?buy=vip7"><button class="gold-button">30 <img
                            src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
        </div>

        <!-- Limited Edition Pack 2 -->
        <div class="vip-package">
            <h4 style="color: silver;">15 Day VIP</h4>
            <img src="/css/images/NewGameImages/vipdays.png" class="your-class-name" alt="Double Exp">

            <h4>Purchase now for only<br><a href="store.php?buy=vip15"><button class="gold-button">50 <img
                            src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
        </div>

        <!-- Limited Edition Pack 3 -->
        <div class="vip-package">
            <h4 style="color: gold;">30 Day VIP</h4>
            <img src="/css/images/NewGameImages/vipdays.png" class="your-class-name" alt="Attack Protection">

            <h4>Purchase now for only<br><a href="store.php?buy=vip30"><button class="gold-button">80 <img
                            src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
        </div>

    </div>
    <br>
</div>

<br /><br />

<br>
<div class="floaty" style="margin: 3px;">
    <h4>QUALITY OF LIFE UPGRADES</h4>
    <hr>
    <div class="quality-of-life-upgrades"
        style="display: flex; justify-content: space-around; align-items: stretch; flex-wrap: wrap;">

        <!-- 10x Super Gym -->
        <div class="vip-package">
            <h4 style="color: brown;">30 mins of 10x Super Gym</h4>
            <p>Unlock 30 mins of the 10x Super Gym, allowing you to complete trains 10x quicker.</p>

            <h4>Purchase now for only<br><a href="store.php?buy=qol10gym"><button class="gold-button">30 <img
                            src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
        </div>

        <!-- 15x Crimes -->
        <div class="vip-package">
            <h4 style="color: brown;">30 mins of 20x Crimes</h4>
            <p>Unlock 30 mins of the 20x Crimes, allowing you to complete crimes 20x quicker.</p>

            <h4>Purchase now for only<br><a href="store.php?buy=qol15crimes"><button class="gold-button">30 <img
                            src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
        </div>

        <!-- Super Crime -->
        <!--            <div class="vip-package">-->
        <!--                <h4 style="color: brown;">30 mins of Super Crime</h4>-->
        <!--                <p>Unlock 30 mins of access to Super Crime, a special crime that earns you more EXP than the standard crimes.</p>-->
        <!---->
        <!--                <h4>Purchase now for only<br><a href="store.php?buy=qolsupercrime"><button class="gold-button">50 <img src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>-->
        <!--            </div>-->

    </div>
    <br>
</div>

<br /><br /><br />

<div class="floaty" style="margin: 3px;">
    <h4>ITEMS</h4>
    <hr>
    <div class="items-upgrades"
        style="display: flex; justify-content: space-around; align-items: stretch; flex-wrap: wrap;">
        <!-- Mission Pass -->
        <div class="vip-package">
            <h4 style="color: brown;">1 x Mission Pass</h4>
            <img src="/css/images/NewGameImages/mission-pass.png" height="100" alt="Mission Pass">

            <h4>Purchase now for only<br><a href="store.php?buy=1missionpass"><button class="gold-button">30 <img
                            src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
        </div>

        <!-- 1 x Research Token -->
        <div class="vip-package">
            <h4 style="color: brown;">1 x Research Token</h4>
            <img src="/css/images/NewGameImages/research-token.png" height="100" alt="Research Token">

            <h4>Purchase now for only<br><a href="store.php?buy=1researchtoken"><button class="gold-button">50 <img
                            src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
        </div>

        <!-- 1 x Gang Double EXP -->
        <div class="vip-package">
            <h4 style="color: brown;">1 x Gang Double EXP</h4>
            <img src="/css/images/NewGameImages/gang-dep.png" height="100" alt="Gang Double EXP">

            <h4>Purchase now for only<br><a href="store.php?buy=1gangdoubleexp"><button class="gold-button">200 <img
                            src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
        </div>
    </div>
    <br>
    <div class="items-upgrades"
        style="display: flex; justify-content: space-around; align-items: stretch; flex-wrap: wrap;">

        <div class="vip-package">
            <h4 style="color: brown;">5 x Protein Bars</h4>
            <img src="/css/images/NewGameImages/gym-protein-bar.png" height="100" alt="Protein Bar">

            <h4>Purchase now for only<br><a href="store.php?buy=5proteinbar"><button class="gold-button">30 <img
                            src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
        </div>

        <div class="vip-package">
            <h4 style="color: brown;">5 x Gym Super Pills</h4>
            <img src="/css/images/NewGameImages/gym-super-pills.png" height="100" alt="Gym Super Pills">

            <h4>Purchase now for only<br><a href="store.php?buy=5gymsuperpills"><button class="gold-button">30 <img
                            src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
        </div>

        <div class="vip-package">
            <h4 style="color: brown;">1 x Nerve Vial</h4>
            <img src="/css/images/NewGameImages/nerve-vial.png" height="100" alt="Nerve Vial">

            <h4>Purchase now for only<br><a href="store.php?buy=1nervevial"><button class="gold-button">50 <img
                            src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
        </div>
    </div>
    <br />
    <div class="items-upgrades"
        style="display: flex; justify-content: space-around; align-items: stretch; flex-wrap: wrap;">

        <div class="vip-package">
            <h4 style="color: brown;">1 x Double Gym Injection</h4>
            <img src="/css/images/NewGameImages/double-gym-injection.png" height="100" alt="Double Gym Injection">

            <h4>Purchase now for only<br><a href="store.php?buy=1doublegym"><button class="gold-button">200 <img
                            src="https://chaoscity.co.uk/goldbar.png" alt="Gold bar"></button></a></h4>
        </div>
    </div>
</div>

<br /><br />


<?php
$bpCategory = getBpCategory();
$bpCategoryUser = getBpCategoryUser($bpCategory, $user_class);
?>

<?php if ($bpCategoryUser['is_premium'] < 1): ?>
    <div class="floaty" style="margin:3px; text-align: center;">
        <h4>BATTLE PASS PREMIUM</h4>
        <hr>
        <table style="width: 100%; margin: auto;">
            <tr>
                <td style="text-align: center;">
                    <br />
                    <p>
                        Purchase this months Battle Pass Premium to gain access to more challenges and prizes in the Battle
                        Pass.
                    </p>
                    <br />
                    <p>
                        <small style="font-size: .5em;">NOTE: THIS PURCHASE ONLY APPLIES TO THE CURRENT MONTHS BATTLE
                            PASS.</small>
                    </p>
                    <h4>Cost: <font color=red><img src="https://chaoscity.co.uk/goldbar.png"></img> 300</font>
                    </h4>

                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    <a href="store.php?buy=bapre"
                        style="display: inline-block; padding: 10px 20px; background-color:  color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s ease; box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.2); text-align: center;">BUY
                        NOW</a>

                </td>
            </tr>
        </table>
    </div>
    <br /><br />
<?php endif; ?>


<div class="floaty" style="margin:3px; text-align: center;">
    <h4>14 DAY GRADIENT NAME</h4>
    <hr>
    <table style="width: 100%; margin: auto;">
        <tr>
            <td style="text-align: center;">

                <h4>Example</h4>
                <h4 class="gradient-text">Gradient NAME</h4>
                <h4>Cost: <font color=red><img src="https://chaoscity.co.uk/goldbar.png"></img> 50</font>
                </h4>

            </td>
        </tr>
        <tr>
            <td style="text-align: center;">
                <a href="store.php?buy=7daygrad"
                    style="display: inline-block; padding: 10px 20px; background-color:  color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s ease; box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.2); text-align: center;">BUY
                    NOW</a>

            </td>
        </tr>
    </table>
</div>
<br /><br />

<div class="floaty" style="margin:3px; text-align: center;">
    <h4>IMAGE NAME</h4>
    <hr>
    <table style="width: 100%; margin: auto;">
        <tr>
            <td style="text-align: center;">

                <h4>Example</h4> <img
                    src="https://cdn.discordapp.com/attachments/1225698401732268114/1229718368899301456/PSX_20240416_020108.png?ex=6630b37a&is=661e3e7a&hm=7701a12b7c977cc41049878c2536a7c8e8ecc874a9550715bb63514d49b96f63&"
                    style="max-width:95px; max-height:50px;"><br />
                <h4>Cost: <font color=red><img src="https://chaoscity.co.uk/goldbar.png"></img> 500</font>
                </h4>

            </td>
        </tr>
        <tr>
            <td style="text-align: center;">
                <a href="store.php?buy=14imgname"
                    style="display: inline-block; padding: 10px 20px; background-color:  color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background-color 0.3s ease; box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.2); text-align: center;">BUY
                    NOW</a>

            </td>
        </tr>
    </table>
</div>



<style>
    .gradient-text {
        font-size: 24px;
        /* Adjust the font size as needed */
        background: linear-gradient(to right, red, black, orange, red);
        /* Transition from red, through black and orange, back to red */
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
        display: inline-block;
        /* Needed to apply the gradient on text */
        text-shadow: 0 0 8px red;
        /* Red glow around the text */
        letter-spacing: 5px;
        /* Adds spacing between letters */
    }

    .buy-now-button {
        display: inline-block;
        padding: 10px 20px;
        background-color: #111111;
        /* Dark grey background */
        color: white;
        /* black text */
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .buy-now-button:hover {
        background-color: #0e0e0e;
        /* Slightly darker on hover */
    }

    .section {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin: 20px;
    }

    .section-title {
        color: #fff;
        text-align: center;
        margin-bottom: 20px;
    }

    .section-items {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
        gap: 20px;
        /* Adjusts the space between the items */
    }

    .new-shop-item {
        background-color:
            /* Dark background */
            color: white;
        /* padding: 20px; */
        /* border-radius: 10px; */
        /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); */
        width: 200px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .vip-package ul {
        list-style-type: none;

    }

    .cta {
        cursor: pointer;
        margin: 0 auto;
        margin-top: 10px;
        margin-bottom: 10px;
        padding: 0 10px;
        line-height: 24px;
        text-align: center;
        border-radius: 5px;
        border: none;
        background: #2a2729;
        text-transform: uppercase;
        color: #bcbcbc;
        font-size: 11px;
        box-shadow: 0 0 3px #000000;
    }

    .new-shop-item--img h5,
    .new-shop-item--price span {
        color: white;
        /* Gold color for emphasis */
        font-weight: bold;
    }

    .new-shop-item--price {
        display: flex;
        align-items: center;
        margin: 10px 0;
    }

    .new-shop-item--price img {
        margin-right: 5px;
    }


    .vip-packages {
        display: flex;
        justify-content: space-around;
        align-items: stretch;
        flex-wrap: wrap;
        gap: 20px;
        /* Adds space between the packages */
    }

    .vip-package {
        text-align: center;
        background-color:
            /* Dark background */
            color: white;
        /* padding: 20px; */
        /* border-radius: 10px; */
        /* box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.2); */
        width: 30%;
    }

    .vip-package img {
        max-width: 100%;
        /* Ensure images fit within the container */
        border-radius: 10px;
        /* Optional: rounded corners for images */
    }

    .vip-package ul {
        padding-left: 20px;
        /* Indent for the benefit list */
    }

    .vip-package p {
        font-size: 18px;
        /* Larger font size for cost */
        color: gold;
        /* Gold color for emphasis */
    }

    .gold-button {
        background-color: #111111;
        border: 2px solid yellow;
        color: yellow;
        padding: 10px 20px;
        border-radius: 10px;
        cursor: pointer;
        transition: background-color 0.3s, box-shadow 0.3s;
        font-size: 16px;
        box-shadow: 0 0 transparent;
        /* Set box-shadow to transparent by default */
    }

    .gold-button:hover {
        background-color: #111111;
        color: #111111;
        box-shadow: 0 0 10px 2px gold;
        /* Adjusted box-shadow size with glow effect */
    }
</style>

<!-- <h3 class="section-header">Packages</h3> -->

<br>

<br>



<?php
echo '<div class="floaty" style="margin:3px;">';
echo '<h4>By donating to Chaos City you are agreeing to the following terms:</h4>';
echo '<hr>';
echo '<ul class="donate_rules">';
echo '<li><font color=white>Strictly NO Refunds</li>';
echo '<li><font color=white>If you do not receive your package, please contact support.</li>';
echo '<li>Purchasing packages does not mean you can break the game rules, your account will still be Banned!</li>';
echo '<li>If you try refunding your money through paypal, we will ban your account.</li>';
echo '</div>';

// echo '<table id="donatetables" class="donate" style="width:100%;">';
// echo '<tr>';
// echo ' <th id="headerRow2">Package</th>';
// echo ' <th id="dottedRow3">Cost</th>';
// echo ' <th id="dottedRow4">Buy</th>';
// echo '</tr>';

// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>Point Pack</font></u><br>[5,000 points added to your account]</font></td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;100</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=1000">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>Point Pack</font></u><br>[10,000 points added to your account]</font></td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;180</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=3500">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>Point Pack</font></u><br>[25,000 points added to your account]</font></td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;340</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=12000">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>Point Pack</font></u><br>[50,000 points added to your account]</font></td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;650</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=35000">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>Point Pack</font></u><br>[125,000 points added to your account]</font></td>';
// echo '<div><td id="dottedRow">&nbsp;&nbsp;<img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;1200</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=85000">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>Point Pack</font></u><br>[300,000 points added to your account]</font></td>';
// echo '<div><td id="dottedRow">&nbsp;&nbsp;<img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;2600</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=200000">[Buy Now]</a></td>';
// echo '</tr>';


// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>RM Pack(s)</font></u><br>[You will recieve 10x 30day RM packs]</td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;270</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=rm3010">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>RM Pack(s)</font></u><br>[You will recieve 10x 60day RM packs]</td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;540</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=rm6010">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>RM Pack(s)</font></u><br>[You will recieve 10x 90day RM packs]</td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;810</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=rm9010">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>Image Name</u><br> Preview: <img src="images/twist.png"/></td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;90</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=imgname">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>Gradient name</u><br>Preview: <span style="color:#990A00;text-shadow: 0 0 10px #E80000;">S</span><span style="color:#A32900;text-shadow: 0 0 10px #EF2501;">m</span><span style="color:#AD4800;text-shadow: 0 0 10px #F74A02;">O</span><span style="color:#B86800;text-shadow: 0 0 10px #FF7003;">k</span><span style="color:#B86800;text-shadow: 0 0 10px #FF7003;">e</span><span style="color:#B87C2E;text-shadow: 0 0 10px #FF9342;">d</span><span style="color:#B9915D;text-shadow: 0 0 10px #FFB781;">O</span><span style="color:#B9A58B;text-shadow: 0 0 10px #FFDBC0;">n</span><span style="color:#BABABA;text-shadow: 0 0 10px #FFFFFF;">e</span></td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;30</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=2gradientyes">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>1 Hour Mug Protection</font></u><br>[*When used you cannot be mugged for 1 hour*]</font></td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;20</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=MP">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>1 Hour Mug Protection x5</font></u><br>[*When used you cannot be mugged for 1 hour*]</font></td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;80</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=MP5">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>1 Hour Attack Protection</font></u><br>[*When used you cannot be attacked for 1 hour*]</font></td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;20</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=AP">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>1 Hour Attack Protection x5</font></u><br>[*When used you cannot be attacked for 1 hour*]</font></td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;80</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=AP5">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>Double EXP</font></u><br>[*When used you will have 1 hour of Double EXP*]</font></td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;50</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=DE">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>Double EXP x5</font></u><br>[*When used you will have 1 hour of Double EXP*]</font></td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;220</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=DE5">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>Security System</font></u><br>[Security System for you and your spouse]</font></td>';
// echo '<div><td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;100</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=sec">[Buy Now]</a></td>';
// echo '</tr>';
// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>Custom Item set 80%</u></font><br>[Customise Name & Set a picture for all 3 modifiers]</font></td>';
// echo '<div><td id="dottedRow">&nbsp;&nbsp;<img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;500</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=custom">[Buy Now]</a></td>';
// echo '</tr>';

// echo '<tr style="text-align:center;height:50px;">';
// echo '<td id="dottedRow1"><font color=yellow><u>+25 Nerve & Energy Boosters <font color=red>[NEW]</font></u></font><br>[1 of each Nerve and energy booster]</font></td>';
// echo '<div><td id="dottedRow">&nbsp;&nbsp;<img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;500</b></font></td>';
// echo '<td id="dottedRow2"><a class="cta" href="?buy=boosters">[Buy Now]</a></td>';
// echo '</tr>';








// echo '</table>';




echo '</div>';




include 'footer.php';
function refd($creds)
{
    global $db, $user_class;
    $pts = $creds * 10;
    $db->query("SELECT * FROM referrals WHERE referred = ?");
    $db->execute(array(
        $user_class->id
    ));
    $ref = $db->fetch_array(true);
    if (!empty($ref)) {
        $refid = $ref['referrer'];
        $db->query("UPDATE grpgusers SET points = points + $pts WHERE id = ?");
        $db->execute(array(
            $user_class->id
        ));
        Send_Event($refid, "You have been credited $pts points because you referred [-_USERID_-], and they bought something from the RY store.", $refid);
    }
}
function spentcreds($name, $creds)
{
    global $db, $user_class;
    $db->query("INSERT INTO spentcredits (timestamp, spender, spent, amount) VALUES (unix_timestamp(), ?, ?, ?)");
    $db->execute(array(
        $user_class->id,
        $name,
        $creds
    ));
}
?>