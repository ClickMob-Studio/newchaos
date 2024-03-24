<?php
include 'header.php';
?>

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


// Trigger the Send_Event function
Send_Event(1, $user_class->formattedname . " loaded the RM Store page.");


$packs = array(
    array(
        "Name" => "30 Day Respected Yobster Pack",
        "Cost" => 3,
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 0,
        "Items" => "51:1"
    ),
    array(
        "Name" => "60 Day Respected Yobster Pack",
        "Cost" => 6,
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 0,
        "Items" => "103:1"
    ),
    array(
        "Name" => "90 Day Respected Yobster Pack",
        "Cost" => 9,
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 0,
        "Items" => "104:1"
    ),
    array(
        "Name" => "<font color=orange><b>Premium Summer Pack</b></font>",
        "RY Days" => 0,
        "Money" => 50000000,
        "Points" => 75000,
        "Cost" => 97,
        "Items" => "None"
    ),
    array(
        "Name" => "<a href=forum.php?topic=351>Custom item set (65%) ",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 0,
        "Cost" => 50,
        "Items" => "105:1|106:1|107:1"
    ),
    array(
        "Name" => "800 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 500,
        "Cost" => 2,
        "Items" => "None"
    ),
    array(
        "Name" => "2,500 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 2500,
        "Cost" => 4,
        "Items" => "None"
    ),
    array(
        "Name" => "7,500 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 7500,
        "Cost" => 9,
        "Items" => "None"
    ),
    array(
        "Name" => "15,000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 15000,
        "Cost" => 15,
        "Items" => "None"
    ),
    array(
        "Name" => "22,000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 22000,
        "Cost" => 20,
        "Items" => "None"
    ),
    array(
        "Name" => "36,000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 36000,
        "Cost" => 32,
        "Items" => "None"
    ),
    array(
        "Name" => "58,000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 58000,
        "Cost" => 45,
        "Items" => "None"
    ),
    array(
        "Name" => "120,000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 120000,
        "Cost" => 100,
        "Items" => "None"
    ),
    array(
        "Name" => "450,000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 450000,
        "Cost" => 200,
        "Items" => "None"
    ),
    array(
        "Name" => "900,000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 900000,
        "Cost" => 400,
        "Items" => "None"
    ),
    array(
        "Name" => "2,000,000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 2000000,
        "Cost" => 850,
        "Items" => "None"
    ),
    array(
        "Name" => "10 Awake Pills ",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 0,
        "Cost" => 5,
        "Items" => "4:10"
    ),
    array(
        "Name" => "<font color=red>Nerve Booster + 50</font>",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 0,
        "Cost" => 25,
        "Items" => "68:1"
    ),
    array(
        "Name" => "<font color=red>Energy Booster + 50</font> ",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 0,
        "Cost" => 25,
        "Items" => "69:1"
    ),
    array(
        "Name" => "<font color=gold>Mystery Box[x5]</font>",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 0,
        "Cost" => 5,
        "Items" => "42:5"
    ),
    array(
        "Name" => "<font color=gold>Mystery Box[x10] </font>",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 0,
        "Cost" => 9,
        "Items" => "42:10"
    ),
    array(
        "Name" => "$10,000,000",
        "RY Days" => 0,
        "Money" => 10000000,
        "Points" => 0,
        "Cost" => 7,
        "Items" => "None"
    ),
    array(
        "Name" => "$20,000,000",
        "RY Days" => 0,
        "Money" => 20000000,
        "Points" => 0,
        "Cost" => 12,
        "Items" => "None"
    ),
    array(
        "Name" => "$60,000,000",
        "RY Days" => 0,
        "Money" => 60000000,
        "Points" => 0,
        "Cost" => 32,
        "Items" => "None"
    ),
    array(
        "Name" => "<font color=blue>Invisibility Cloak</font> ",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 0,
        "Cost" => 15,
        "Items" => "43:1"
    )
);
if (isset($_GET['buy'])) {
    if ($_GET['buy'] == "sec")
        diefun("Are you sure you want to buy a Security System? <br><a href='rmstore.php?buy=secyes'>Continue</a><br /><a href='rmstore.php'>No thanks!</a>");
    if ($_GET['buy'] == "2gradient")
        diefun("Are you sure you want to buy a 30 Day Colour Gradient Name? <br><a href='rmstore.php?buy=2gradientyes'>Continue</a><br /><a href='rmstore.php'>No thanks!</a>");
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
    if ($_GET['buy'] == "2gradientyes") {
        if ($user_class->credits >= 30) {
            $user_class->credits -= 30;
            $db->query("UPDATE grpgusers SET gndays = gndays + 30, gradient = ?, credits = credits - 30 WHERE id = ?");
            $db->execute(array(
                ($user_class->gradient != 3) ? "2" : "3",
                $user_class->id
            ));
            spentcreds('30 Day Colour Gradient Name', 3);
            refd(3);
            echo Message("You spent 30 credits for the 30 Day Colour Gradient Name. To use it, visit your details page.");
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
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "12000") {
        if ($user_class->credits >= 340) {
            $newcredit = $user_class->credits -= 340;
            $db->query("UPDATE grpgusers SET points = points + 25000, credits = credits - 340 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            echo Message("You spent 340 credits for 25,000 Points.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "limitedpack") {
        if ($user_class->limiteditems1 == 0) {
            echo Message("This Pack Is No Longer Available.");
        } else if ($user_class->credits >= 175) {
            $newcredit = $user_class->credits -= 175;
            $result    = mysql_query("UPDATE `grpgusers` SET `limiteditems1` = limiteditems1 - 1 WHERE `limiteditems1` != '0'");
                         $db->query("UPDATE grpgusers SET  credits = credits - 175, raidtokens = raidtokens + 1, points = points + 200000 WHERE id = ?");

                $db->execute(array(
                               $user_class->id
            ));
            Give_Item(42, $user_class->id, 1);
            Give_Item(163, $user_class->id, 1);
            Give_Item(167, $user_class->id, 1);
            


                                            











            Send_Event($user_class->id, "You have been credited your New Years Standard Donation Package!.", $user_class->id);
            $db->execute(array());
            echo Message("You spent 175 credits on your New Years Standard Package!");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }



if ($_GET['buy'] == "limitedpack2") {
        if ($user_class->limiteditems2 == 0) {
            echo Message("This Pack Is No Longer Available.");
        } else if ($user_class->credits >= 1750) {
            $newcredit = $user_class->credits -= 1750;
            $result    = mysql_query("UPDATE `grpgusers` SET `limiteditems2` = limiteditems2 - 1 WHERE `limiteditems2` != '0'");
                         $db->query("UPDATE grpgusers SET  points = points + 2500000,  raidtokens = raidtokens + 10,bank = bank + 0, credits = credits - 1750 WHERE id = ?");

                $db->execute(array(
                               $user_class->id
            ));
             Give_Item(42, $user_class->id, 5);
            Give_Item(163, $user_class->id, 5);
            Give_Item(167, $user_class->id, 5);




                                           
                                            


                          
  



            Send_Event($user_class->id, "You have been credited your New Years Itermidiate Package!.", $user_class->id);
            $db->execute(array());
            echo Message("You spent 1750 credits on your New Years Intermediate Package.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }





if ($_GET['buy'] == "limitedpack3") {
        if ($user_class->limiteditems3 == 0) {
            echo Message("This Pack Is No Longer Available.");
        } else if ($user_class->credits >= 17500) {
            $newcredit = $user_class->credits -= 17500;
            $result    = mysql_query("UPDATE `grpgusers` SET `limiteditems3` = limiteditems3 - 1 WHERE `limiteditems3` != '0'");
                         $db->query("UPDATE grpgusers SET  points = points + 27500000, raidtokens = raidtokens + 100, credits = credits - 17500 WHERE id = ?");

                $db->execute(array(
                               $user_class->id
            ));

                                            
                                             
            Give_Item(42, $user_class->id, 50);
            Give_Item(163, $user_class->id, 50);
            Give_Item(167, $user_class->id, 50);









            Send_Event($user_class->id, "You have been credited your New Years Extreme Package!.", $user_class->id);
            $db->execute(array());
            echo Message("You spent 17,500 credits on your New Years Extreme Package.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }



if ($_GET['buy'] == "freebie") {
        if ($user_class->claimed == 1) {
            echo Message("You have already Claimed your Freebie.");
        } else if ($user_class->claimed == 0) {
            $newcredit = $user_class->credits -= 0;
            $result    = mysql_query("UPDATE `grpgusers` SET `claimed` = 1 WHERE id = ?");
                         $db->query("UPDATE grpgusers SET  points = points + 10000, raidtokens = raidtokens + 1, claimed = 1, credits = credits - 0, donate_token = donate_token + 1 WHERE id = ?");

                $db->execute(array(
                               $user_class->id
            ));
            Give_Item(10, $user_class->id, 1);


                        






            Send_Event($user_class->id, "You have been credited your Free Package 10,000 Points & a Double Exp Pill!</br> You've also received a double donation token and 1 Raid Tokens! <a href='rmstore.php'>Click Here To Spend them in our store!</a>.", $user_class->id);
            $db->execute(array());
            echo Message("Happy Levelling!. You have been credited 10,000 Points & a Double Exp Pill.</br> You've also received a double donation token and 1 Raid Tokens! <a href='rmstore.php'>Click Here To Spend them in our store</a> ");
        } else {
            echo Message("You have already claimed your Free Package");
        }
    }












    if ($_GET['buy'] == "35000") {
        if ($user_class->credits >= 650) {
            $newcredit = $user_class->credits -= 650;
            $db->query("UPDATE grpgusers SET points = points + 50000, credits = credits - 650 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            echo Message("You spent 650 credits for 50,000 Points.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "85000") {
        if ($user_class->credits >= 1200) {
            $newcredit = $user_class->credits -= 1200;
            $db->query("UPDATE grpgusers SET points = points + 125000, credits = credits - 1200 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            echo Message("You spent 1200 credits for 125,000 Points.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "200000") {
        if ($user_class->credits >= 2600) {
            $newcredit = $user_class->credits -= 2600;
            $db->query("UPDATE grpgusers SET points = points + 300000, credits = credits - 2600 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            echo Message("You spent 2,600 credits for 300,000 Points.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "bb") {
        if ($user_class->credits >= 80) {
            $newcredit = $user_class->credits -= 80;
            $db->query("UPDATE grpgusers SET points = points + 3500, credits = credits - 80 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(51, $user_class->id);
            echo Message("You spent 80 credits for teh basic builder.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "cc") {
        if ($user_class->credits >= 160) {
            $newcredit = $user_class->credits -= 160;
            $db->query("UPDATE grpgusers SET gndays = gndays + 30, gradient = ?, credits = credits - 50 WHERE id = ?");
            $db->execute(array(
                ($user_class->gradient != 3) ? "2" : "3",
                $user_class->id
            ));
            Give_Item(10, $user_class->id);
            Give_Item(51, $user_class->id);
            echo Message("You spent 160 credits for the coloured climber.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "ss") {
        if ($user_class->credits >= 190) {
            $newcredit = $user_class->credits -= 190;
            $db->query("UPDATE grpgusers SET points = points + 5000, credits = credits - 190 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(51, $user_class->id);
            Give_Item(10, $user_class->id);
            echo Message("You spent 190 credits for 5,000 Points a Double Exp Pill and a 30Day RM pack.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "ip") {
        if ($user_class->credits >= 280) {
            $newcredit = $user_class->credits -= 280;
            $db->query("UPDATE grpgusers SET points = points + 15000, credits = credits - 280 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(9, $user_class->id);
            Give_Item(10, $user_class->id);
            Give_Item(103, $user_class->id);
            echo Message("You spent 190 credits for the Booster pack.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "ep") {
        if ($user_class->credits >= 750) {
            $newcredit = $user_class->credits -= 750;
            $db->query("UPDATE grpgusers SET points = points + 45000, credits = credits - 750 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(9, $user_class->id);
            Give_Item(10, $user_class->id);
            Give_Item(8, $user_class->id);
            Give_Item(104, $user_class->id);
            echo Message("You spent 750 credits for the expert pack");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "mtp") {
        if ($user_class->credits >= 1200) {
            $newcredit = $user_class->credits -= 1200;
            $db->query("UPDATE grpgusers SET points = points + 100000, credits = credits - 1200 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(9, $user_class->id);
            Give_Item(10, $user_class->id);
            Give_Item(8, $user_class->id);
            echo Message("You spent 1200 credits for the mediocre pack");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "ultimate") {
        if ($user_class->credits >= 2500) {
            $newcredit = $user_class->credits -= 2500;
            $db->query("UPDATE grpgusers SET points = points + 200000, credits = credits - 2500 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(9, $user_class->id, 3);
            Give_Item(10, $user_class->id, 5);
            Give_Item(8, $user_class->id, 3);
            echo Message("You spent 2500 credits for the ultimate pack");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "500000") {
        if ($user_class->credits >= 5000) {
            $newcredit = $user_class->credits -= 5000;
            $db->query("UPDATE grpgusers SET points = points + 500000, credits = credits - 5000 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            echo Message("You spent 5,000 credits for 500,000 Points.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "rm30") {
        if ($user_class->credits >= 30) {
            $newcredit = $user_class->credits -= 30;
            $db->query("UPDATE grpgusers SET credits = credits - 30 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(51, $user_class->id);
            Send_Event($user_class->id, "You have been credited with your 30 Day RM pack.", $user_class->id);
            $db->execute(array());
            echo Message("You spent 30 credits for a 30 day RM pack.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "rm60") {
        if ($user_class->credits >= 60) {
            $newcredit = $user_class->credits -= 60;
            $db->query("UPDATE grpgusers SET credits = credits - 60 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(103, $user_class->id);
            Send_Event($user_class->id, "You have been credited with your 60 Day RM pack.", $user_class->id);
            $db->execute(array());
            echo Message("You spent 60 credits for a 60 day RM pack.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "rm90") {
        if ($user_class->credits >= 90) {
            $newcredit = $user_class->credits -= 90;
            $db->query("UPDATE grpgusers SET credits = credits - 90 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(104, $user_class->id);
            Send_Event($user_class->id, "You have been credited with your 90 Day RM pack.", $user_class->id);
            $db->execute(array());
            echo Message("You spent 90 credits for a 90 day RM pack.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "rm3010") {
        if ($user_class->credits >= 270) {
            $newcredit = $user_class->credits -= 270;
            $db->query("UPDATE grpgusers SET credits = credits - 270 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(51, $user_class->id, 10);
            Send_Event($user_class->id, "You have been credited with your 90 Day RM pack[x10].", $user_class->id);
            $db->execute(array());
            echo Message("You spent 270 credits for a 90 day RM pack[x10].");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "rm9010") {
        if ($user_class->credits >= 810) {
            $newcredit = $user_class->credits -= 810;
            $db->query("UPDATE grpgusers SET credits = credits - 810 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(104, $user_class->id, 10);
            Send_Event($user_class->id, "You have been credited with your 90 Day RM pack[x10].", $user_class->id);
            $db->execute(array());
            echo Message("You spent 810 credits for a 90 day RM pack[x10].");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "rm6010") {
        if ($user_class->credits >= 540) {
            $newcredit = $user_class->credits -= 540;
            $db->query("UPDATE grpgusers SET credits = credits - 540 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(103, $user_class->id, 10);
            Send_Event($user_class->id, "You have been credited with your 60 Day RM pack[x10].", $user_class->id);
            $db->execute(array());
            echo Message("You spent 540 credits for a 60 day RM pack[x10].");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
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
            Send_Event($user_class->id, "You have been credited with your 1 Hour Mug Protection. You can find it <a href='inventory.php'><font color=yellow><b>[Here]</b></font></a>", $user_class->id);
            $db->execute(array());
            echo Message("You spent 20 credits for a 1 Hour Mug Protection.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "MP5") {
        if ($user_class->credits >= 80) {
            $newcredit = $user_class->credits -= 80;
            $db->query("UPDATE grpgusers SET credits = credits - 80 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(8, $user_class->id, 5);
            Send_Event($user_class->id, "You have been credited with your 1 Hour Mug Protection[x5]. You can find it <a href='inventory.php'><font color=yellow><b>[Here]</b></font></a>", $user_class->id);
            $db->execute(array());
            echo Message("You spent 80 credits for a 1 Hour Mug Protection[x5].");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
if ($_GET['buy'] == "MB") {
        if ($user_class->credits >= 30) {
            $newcredit = $user_class->credits -= 30;
            $db->query("UPDATE grpgusers SET credits = credits - 30 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(42, $user_class->id);
            Send_Event($user_class->id, "You have been credited with a 4th of July Box(s). You can find it <a href='inventory.php'><font color=yellow><b>[Here]</b></font></a>", $user_class->id);
            $db->execute(array());
            echo Message("You spent 30 credits for a 4th of July Box.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }

if ($_GET['buy'] == "MB10") {
        if ($user_class->credits >= 280) {
            $newcredit = $user_class->credits -= 280;
            $db->query("UPDATE grpgusers SET credits = credits - 280 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(42, $user_class->id, 10);
            Send_Event($user_class->id, "You have been credited with 10 4th of July Box(s). You can find it <a href='inventory.php'><font color=yellow><b>[Here]</b></font></a>", $user_class->id);
            $db->execute(array());
            echo Message("You spent 280 credits for 10 4th of July Box(s).");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
if ($_GET['buy'] == "MB100") {
        if ($user_class->credits >= 2200) {
            $newcredit = $user_class->credits -= 2200;
            $db->query("UPDATE grpgusers SET credits = credits - 2200 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(42, $user_class->id, 100);
            Send_Event($user_class->id, "You have been credited with 100 4th of July Box(s). You can find it <a href='inventory.php'><font color=yellow><b>[Here]</b></font></a>", $user_class->id);
            $db->execute(array());
            echo Message("You spent 2200 credits for 100 4th of July Box(s).");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }




    if ($_GET['buy'] == "AP") {
        if ($user_class->credits >= 20) {
            $newcredit = $user_class->credits -= 20;
            $db->query("UPDATE grpgusers SET credits = credits - 20 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(9, $user_class->id);
            Send_Event($user_class->id, "You have been credited with your 1 Hour Attack Protection. You can find it <a href='inventory.php'><font color=yellow><b>[Here]</b></font></a>", $user_class->id);
            $db->execute(array());
            echo Message("You spent 20 credits for a 1 Hour Attack Protection.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "AP5") {
        if ($user_class->credits >= 80) {
            $newcredit = $user_class->credits -= 80;
            $db->query("UPDATE grpgusers SET credits = credits - 80 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(9, $user_class->id, 5);
            Send_Event($user_class->id, "You have been credited with your 1 Hour Attack Protection[x5]. You can find it <a href='inventory.php'><font color=yellow><b>[Here]</b></font></a>", $user_class->id);
            $db->execute(array());
            echo Message("You spent 80 credits for a 1 Hour Attack Protection[x5].");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "DE") {
        if ($user_class->credits >= 50) {
            $newcredit = $user_class->credits -= 50;
            $db->query("UPDATE grpgusers SET credits = credits - 50 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(10, $user_class->id);
            Send_Event($user_class->id, "You have been credited with your 1 Hour Double EXP pack. You can find it <a href='inventory.php'><font color=yellow><b>[Here]</b></font></a>", $user_class->id);
            $db->execute(array());
            echo Message("You spent 50 credits for a Double EXP pack.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "DE5") {
        if ($user_class->credits >= 220) {
            $newcredit = $user_class->credits -= 220;
            $db->query("UPDATE grpgusers SET credits = credits - 220 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(10, $user_class->id, 5);
            Send_Event($user_class->id, "You have been credited with your 1 Hour Double EXP pack[x5]. You can find it <a href='inventory.php'><font color=yellow><b>[Here]</b></font></a>", $user_class->id);
            $db->execute(array());
            echo Message("You spent 220 credits for a 5 Double EXP pack(s).");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
    if ($_GET['buy'] == "custom") {
        if ($user_class->credits >= 500) {
            $newcredit = $user_class->credits -= 500;
            $db->query("UPDATE grpgusers SET credits = credits - 500 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(105, $user_class->id);
            Give_Item(106, $user_class->id);
            Give_Item(107, $user_class->id);
            Send_Event($user_class->id, "You have been credited with your custom item set. You can find it <a href='inventory.php'><font color=yellow><b>[Here]</b></font></a>", $user_class->id);
            $db->execute(array());
            echo Message("You spent 500 credits for a custom item set.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }
if ($_GET['buy'] == "boosters") {
        if ($user_class->credits >= 500) {
            $newcredit = $user_class->credits -= 500;
            $db->query("UPDATE grpgusers SET credits = credits - 500 WHERE id = ?");
            $db->execute(array(
                $user_class->id
            ));
            Give_Item(68, $user_class->id);
            Give_Item(69, $user_class->id);
            Send_Event($user_class->id, "You have been credited with your +25 Nerve & Energy Boosters. You can find it <a href='inventory.php'><font color=yellow><b>[Here]</b></font></a>", $user_class->id);
            $db->execute(array());
            echo Message("You spent 500 credits for your Nerve & Energy Boosters.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
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
}
$donperc = ($user_class->donations / $donmax) * 100;
$donperc = $donperc >= 100 ? 100 : $donperc;
echo '<div class="flexcont" style="align-items:stretch;">';
echo '<div class="flexele floaty" style="margin:3px;">';
echo '<h2>Make a Donation to TheMafiaLife</h2>';


if ($user_class->donate_token > 0) {
    echo '<h2>You have ' . $user_class->donate_token . 'x ' .  item_popup('Donation Boost Token', 156, 'yellow') . '</h2>';
}

// Display information
echo '<center><font size="3px" color="white">$1 = 10 Credits</font></center>';
echo '<center><font color=white>Your credits balance is:</font> <span style="color:yellow;font-weight:bold;">' . $user_class->credits . ' Credit(s)</size></center></span><br />';

echo '<center>';
?>
<span id="creditDisplay"><font color='white'>For a donation of $0, you will receive 0 credits.</font></span>




<script>
document.addEventListener("DOMContentLoaded", function() {
    var amountInput = document.getElementById("amount");
    var boostCheckbox = document.getElementById("boost");
    var creditDisplay = document.getElementById("creditDisplay");

    function updateCredits() {
        var donationAmount = parseFloat(amountInput.value) || 0;
        var credits = donationAmount * 10;
        if (boostCheckbox.checked) {
            credits *= 2;
        }
        creditDisplay.innerHTML = "<font color='white'>For a donation of $" + donationAmount.toFixed(2) + ", you will receive " + credits + " credits.</font>";
    }

    amountInput.addEventListener("input", updateCredits);
    boostCheckbox.addEventListener("change", updateCredits);
});

</script>
 <script>
        function validateForm() {
            // Get the donation amount from the input field
            var amount = document.getElementById("amount").value;

            // Check if the amount is a valid integer
            if (!/^[1-9]\d*$/.test(amount)) {
                //alert("Please enter a valid whole number greater than 0.");
                return true; // Prevent form submission
            }
            if(amount < 3){
               // alert("The smallest amount for donation is $3")
                return true;
            }
            // If the amount is valid, allow the form submission
            return true;
        }
    </script>

<div id="payment-box">
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" onsubmit="return validateForm()" target="_top">

    <!-- Identify your business email or account ID -->
    <input type="hidden" name="business" value="ExcelledGaming@outlook.com">
    
    <!-- Specify donation-related settings -->
    <input type="hidden" name="cmd" value="_donations">
    <input type="hidden" name="item_name" value="credits">
    <input type='hidden' name='item_number' value='<?= $user_class->id; ?>'>
    <input type="hidden" name="currency_code" value="USD">
    
    <!-- Add custom fields if needed -->
    <input type="hidden" name="custom" value="<?php echo $user_class->id; ?>">
    
    <!-- Specify the return URL after a successful donation -->
    <input type="hidden" name="return" value="https://s2.themafialife.com/rmstore.php?type=success">
    
    <!-- Specify the cancel URL if the user cancels the donation -->
    <input type="hidden" name="cancel_return" value="https://s2.themafialife.com/rmstore.php">
    
    <!-- Specify the IPN URL for PayPal to send notification -->
    <input type="hidden" name="notify_url" value="https://s2.themafialife.com/ipn_credits.php">
    <label for="amount">Donation Amount: </label>
    <input type="text" name="amount" id="amount" required>
    
                                    <input type="submit" value="Donate Now!"  name="submit">
                                </td>
                            </form>
</div>
<br>




</div>
</div>
<br><br>
<div class="pulsate" style="font-family:Creepster;font-size: 2.5em;color:#FF0000;text-align: center;margin-bottom: 20px;margin-top: -20px;">Merry Xmas & A Happy New Year Enjoy 30% Off All Packages</div>
<?php
echo '<div id="pricing-table" class="clear">';
    echo '<div class="plan">';
             echo '<h5><font color=green>New Years Standard Package</font></h5>'; //<span><s>300</s></br>Credits</span>
        echo '<ul>';
            echo '<li><b><span style="color:#000000">200,000 Points</span></b></li>';


         echo '<li><b><span style="color:#000000"> ' .  item_popup('Mystery Box', 42, 'green') . ' [x1]</span></b></li>';

         echo '<li><b><span style="color:#000000"> ' .  item_popup('Police Badge', 163, 'green') . ' [x1]</span></b></li>';

         echo '<li><b><span style="color:#000000"> ' .  item_popup('Christmas Present', 167, 'green') . ' [x1]</span></b></li>';



         echo '<li><b><font color=red> Raid Token[x1]</font></b></li>';


       


        echo '</ul>';
            echo '<li> <b><span style="color:#000000">' . $user_class->limiteditems1 . ' / 20 Remaining</span></b></li>';
            echo '<div class="price"> <img src="css/images/coin.png"><span><s>250</s> <font color=green>175</font></span></div>';

        echo '<a class="signup" href="?buy=limitedpack">Buy Now</a>';     echo '</div>';
 echo '<div class="plan">'; //id="most-popular"
         echo '<h5><font color=green>New Years Intermediate Package</font></h5>';//<span></br>Credits</span>
        echo '<ul>';
           echo '<li><b><span style="color:#000000">2,500,000 Points</span></b></li>';

  

      echo '<li><b><span style="color:#000000"> ' .  item_popup('Mystery Box', 42, 'green') . ' [x5]</span></b></li>';

         echo '<li><b><span style="color:#000000"> ' .  item_popup('Police Badge', 163, 'green') . ' [x5]</span></b></li>';

         echo '<li><b><span style="color:#000000"> ' .  item_popup('Christmas Present', 167, 'green') . ' [x5]</span></b></li>';

         echo '<li><b><font color=red> Raid Token[x10]</font></b></li>';




        echo '</ul>';
            echo '<li><b><span style="color:#000000">' . $user_class->limiteditems2 . ' / 10 Remaining</span></b></li>';
            echo '<div class="price"> <img src="css/images/coin.png"><span><s>2500</s> <font color=green>1750</font></span></div>';
        echo '<a class="signup" href="?buy=limitedpack2">Buy Now</a>';     echo '</div>';
echo '<div class="plan">';
        echo '<h5><font color=green>New Years Extreme Package</font></h5>'; //<span>750</br>Credits</span>
        echo '<ul>';
            echo '<li><b><span style="color:#000000">27,500,000 Points</span></b></li>';

 
        echo '<li><b><span style="color:#000000"> ' .  item_popup('Mystery Box', 42, 'green') . ' [x50]</span></b></li>';

         echo '<li><b><span style="color:#000000"> ' .  item_popup('Police Badge', 163, 'green') . ' [x50]</span></b></li>';

         echo '<li><b><span style="color:#000000"> ' .  item_popup('Christmas Present', 167, 'green') . ' [x50]</span></b></li>';


         echo '<li><b><font color=red> Raid Token[x100]</font></b></li>';





    



               echo '</ul>';
            echo '<li><b><span style="color:#000000">' . $user_class->limiteditems3 . ' / 5 Remaining</span></b></li>';
            echo '<div class="price"> <img src="css/images/coin.png"><span><s>25000</s> <font color=green>17500</font></span></div>';

        echo '<a class="signup" href="?buy=limitedpack3">Buy Now</a>';

    echo '</div>';
echo '</div>';

if (!$user_class->id < 10000) {
  ?>
<table id="newtables" class="donate" style="width:100%;">
    <tr>
        <th colspan="100" align="center">Respected Mobster Upgrades</th>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td id="dottedRow" height="70" valign="middle"><b>Free Member</b></td>
        <td id="dottedRow" class="avatar" valign="top"><img src="images/30day.png" border="0"></td>
        <td id="dottedRow" class="avatar" valign="top"><img src="images/60day.png" border="0"></td>
        <td id="dottedRow2" class="avatar" valign="top"><img src="images/90day.png" border="0"></td>
    </tr>
    <tr>
        <td id="dottedRow1">Money Bonus (One Time)</td>
        <td id="dottedRow">-</td>
        <td id="dottedRow">$150,000</td>
        <td id="dottedRow">$300,000</td>
        <td id="dottedRow2">$450,000</td>
    </tr>
    <tr>
        <td id="dottedRow1">Points Bonus (One Time)</td>
        <td id="dottedRow">-</td>
        <td id="dottedRow">750</td>
        <td id="dottedRow">1500</td>
        <td id="dottedRow2">2500</td>
    </tr>
    <tr>
        <td id="dottedRow1">Energy Regain (Per 5 Mins)</td>
        <td id="dottedRow">10%</td>
        <td id="dottedRow">20%</td>
        <td id="dottedRow">20%</td>
        <td id="dottedRow2">20%</td>
    </tr>
    <tr>
        <td id="dottedRow1">Bank Interest</td>
        <td id="dottedRow">2%</td>
        <td id="dottedRow">4%</td>
        <td id="dottedRow">4%</td>
        <td id="dottedRow2">4%</td>
    </tr>
    <tr>
        <td id="dottedRow1">Max Bank Interest</td>
        <td id="dottedRow">$300,000</td>
        <td id="dottedRow">$600,000</td>
        <td id="dottedRow">$600,000</td>
        <td id="dottedRow2">$600,000</td>
    </tr>
    <tr>
        <td id="dottedRow1">Inventory Size</td>
        <td id="dottedRow">100</td>
        <td id="dottedRow">300</td>
        <td id="dottedRow">300</td>
        <td id="dottedRow2">300</td>
    </tr>
    <tr>
        <td id="dottedRow1">Cost</td>
        <td id="dottedRow">Free</td>
        <td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;30</b></font></td>
        <td id="dottedRow"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;60</b></font></td>
        <td id="dottedRow2"><img src="css/images/coin.png"><font size="3" color="yellow"><b>&nbsp;90</b></font></td>
    </tr>
    <tr>
        <td id="dottedRow7">&nbsp;</td>
        <td id="dottedRow5">&nbsp;</td>
        <td id="dottedRow5"><a class="cta" href="?buy=rm30">[Buy Now]</a></td>
        <td id="dottedRow5"><a class="cta" href="?buy=rm60">[Buy Now]</a></td>
        <td id="dottedRow6"><a class="cta" href="?buy=rm90">[Buy Now]</a></td>
    </tr>
</table>

<?php
}
echo '</br>';
?>
<style>
    .section-header {
        background: #111;
        border: 1px solid rgba(255,255,255,0.5);
        color: #fff;
        font-family: monospace;
        font-size: 18px;
        margin: 0 0 18px;
        padding: 6px 0;
        text-align: center;
        text-transform: uppercase;
        box-shadow: 0 8px 6px -6px black;
        text-shadow: 0px 1px black;
    }
    .section {
        border: 1px solid rgba(255,255,255,0.5);
        margin: 0 0 25px;
        width: 100%;
        box-shadow: 0 8px 6px -6px black;
    }
    .section-row {
        display: flex;
        margin: 0 -9px;
    }
    .section-row > .section {
        margin: 0 9px 18px;
    }
    .section-title {
        background: #111;
        border-bottom: 1px solid rgba(255,255,255,0.5);
        color: #fff;
        font-family: monospace;
        font-size: 14px;
        margin: 0;
        padding: 6px 0;
        text-align: center;
        text-transform: uppercase;
    }
    .section-items {
        padding: 12px 9px;
        display: flex;
        flex-wrap: wrap;
    }
    .new-shop-item {
        width: 100px;
        margin: 0 auto;
        text-align: center;
        background: #000;
        border: 3px solid #111;
    }

 /* .new-shop-item.has-tooltip:hover::before{
        bottom: 120px;
    }

    .new-shop-item.has-tooltip:hover::after{
        bottom: 100px;
    } */

    .new-shop-item--img {
        background: url('https://themafialife.com/css/images/empty.jpg') center;
        background-repeat: no-repeat;
        width: 100%;
        min-height: 100px;
        max-width: 250px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
        .new-shop-item--img h5 {
            margin: 0;
            text-transform: uppercase;
            font-family: monospace;
            font-size: 24px;
            text-shadow: 0px 0px 5px rgb(200,0,0);
        }
            @media (max-width: 767px) {
                #pricing-table{
                    width:100%
                }
                #pricing-table .plan {
        /* Additional styles to make each plan display on a separate line */
        margin-bottom: 20px; /* Adjust spacing between plans */
        width:100%;
        float:none;
    }
}
    .new-shop-item-img-preview {
        background: rgba(0,0,0,0.85);
        width: 120%;
        margin: 0 -10px;
        padding: 3px 0;
        border: 1px solid #fff;
        height: 21px;
        line-height: 21px;
    }

    .new-shop-item--price {
        display: flex;
        justify-content: center;
        padding: 6px;
        border-bottom: 3px solid #111;
    }
        .new-shop-item--price span {
            line-height: 25px;
            margin-left: 12px;
            font-weight: 700;
            font-size: 24px;
        }

    .new-shop-item--buy {
        padding: 6px;
    }
</style>

<!-- <h3 class="section-header">Packages</h3> -->

<div class="section">
    <h4 class="section-title">Point Packs</h4>
    <div class="section-items">
        <div class="new-shop-item" data-tooltip="5,000 points added to your account">
            <div class="new-shop-item--img">
                <h5>5,000 Points</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>100</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=1000">[Buy Now]</a>
            </div>
        </div>
        <div class="new-shop-item" data-tooltip="10,000 points added to your account">
            <div class="new-shop-item--img">
                <h5>10,000 Points</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>180</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=3500">[Buy Now]</a>
            </div>
        </div>
        <div class="new-shop-item" data-tooltip="25,000 points added to your account">
            <div class="new-shop-item--img">
                <h5>25,000 Points</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>340</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=12000">[Buy Now]</a>
            </div>
        </div>
        <div class="new-shop-item" data-tooltip="50,000 points added to your account">
            <div class="new-shop-item--img">
                <h5>50,000 Points</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>650</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=35000">[Buy Now]</a>
            </div>
        </div>
        <div class="new-shop-item" data-tooltip="125,000 points added to your account">
            <div class="new-shop-item--img">
                <h5>125,000 Points</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>1200</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=85000">[Buy Now]</a>
            </div>
        </div>
        <div class="new-shop-item" data-tooltip="300,00 points added to your account">
            <div class="new-shop-item--img">
                <h5>300,000 Points</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>2600</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=200000">[Buy Now]</a>
            </div>
        </div>

    </div>
</div>

<div class="section-row">
    <div class="section" style="flex-basis: 60%">
        <h4 class="section-title">Respected Mobster [RM] Packs</h4>
        <div class="section-items">

            <div class="new-shop-item" data-tooltip="You will recieve 10x 30 day RM Packs">
                <div class="new-shop-item--img">
                    <h5>10x 30day</h5>
                </div>
                <div class="new-shop-item--price">
                    <img src="css/images/coin.png">
                    <span>270</span>
                </div>
                <div class="new-shop-item--buy">
                    <a class="cta" href="?buy=rm3010">[Buy Now]</a>
                </div>
            </div>

            <div class="new-shop-item" data-tooltip="You will recieve 10x 60 day RM Packs">
                <div class="new-shop-item--img">
                    <h5>10x 60day</h5>
                </div>
                <div class="new-shop-item--price">
                    <img src="css/images/coin.png">
                    <span>540</span>
                </div>
                <div class="new-shop-item--buy">
                    <a class="cta" href="?buy=rm6010">[Buy Now]</a>
                </div>
            </div>

            <div class="new-shop-item" data-tooltip="You will recieve 10x 90 day RM Packs">
                <div class="new-shop-item--img">
                    <h5>10x 90day</h5>
                </div>
                <div class="new-shop-item--price">
                    <img src="css/images/coin.png">
                    <span>810</span>
                </div>
                <div class="new-shop-item--buy">
                    <a class="cta" href="?buy=rm9010">[Buy Now]</a>
                </div>
            </div>

        </div>
    </div>
    <div class="section" style="flex-basis: 41%">
        <h4 class="section-title">Cosmetics</h4>
        <div class="section-items">

            <div class="new-shop-item" data-tooltip="You will recieve the Image Name variant">
                <div class="new-shop-item--img">
                    <h5>Image Name</h5>
                    <div class="new-shop-item-img-preview">
                        <img src="images/twist.png">
                    </div>
                </div>
                <div class="new-shop-item--price">
                    <img src="css/images/coin.png">
                    <span>90</span>
                </div>
                <div class="new-shop-item--buy">
                    <a class="cta" href="?buy=imgname">[Buy Now]</a>
                </div>
            </div>
            <div class="new-shop-item" data-tooltip="You will recieve the Gradient Name variant">
                <div class="new-shop-item--img">
                    <h5>Gradient Name</h5>
                    <div class="new-shop-item-img-preview">
                    <font color="yellow"><span style="color:#990A00;text-shadow: 0 0 10px #E80000;">S</span><span style="color:#A32900;text-shadow: 0 0 10px #EF2501;">m</span><span style="color:#AD4800;text-shadow: 0 0 10px #F74A02;">O</span><span style="color:#B86800;text-shadow: 0 0 10px #FF7003;">k</span><span style="color:#B86800;text-shadow: 0 0 10px #FF7003;">e</span><span style="color:#B87C2E;text-shadow: 0 0 10px #FF9342;">d</span><span style="color:#B9915D;text-shadow: 0 0 10px #FFB781;">O</span><span style="color:#B9A58B;text-shadow: 0 0 10px #FFDBC0;">n</span><span style="color:#BABABA;text-shadow: 0 0 10px #FFFFFF;">e</span></font>
                    </div>
                </div>
                <div class="new-shop-item--price">
                    <img src="css/images/coin.png">
                    <span>30</span>
                </div>
                <div class="new-shop-item--buy">
                    <a class="cta" href="?buy=2gradientyes">[Buy Now]</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <h4 class="section-title">Protection</h4>
    <div class="section-items">

        <div class="new-shop-item" data-tooltip="When used you cannot be mugged for 1 hour">
            <div class="new-shop-item--img">
                <h5>1x 1HR Mug Protect</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>20</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=MP">[Buy Now]</a>
            </div>
        </div>
        <div class="new-shop-item" data-tooltip="When used you cannot be mugged for 1 hour">
            <div class="new-shop-item--img">
                <h5>5x 1HR Mug Protect</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>80</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=MP5">[Buy Now]</a>
            </div>
        </div>
        <div class="new-shop-item" data-tooltip="When used you cannot be attacked for 1 hour">
            <div class="new-shop-item--img">
                <h5>1x 1HR Attack Protect</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>20</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=AP">[Buy Now]</a>
            </div>
        </div>
        <div class="new-shop-item" data-tooltip="When used you cannot be attacked for 1 hour">
            <div class="new-shop-item--img">
                <h5>5x 1HR Attack Protect</h5>
            </div>
            <div class="new-shop-item--price">
                <img src="css/images/coin.png">
                <span>80</span>
            </div>
            <div class="new-shop-item--buy">
                <a class="cta" href="?buy=AP5">[Buy Now]</a>
            </div>
        </div>

    </div>
</div>

<div class="section-row">
    <div class="section" style="flex-basis: 41%">
        <h4 class="section-title">Miscellaneous</h4>
        <div class="section-items">

            <div class="new-shop-item" data-tooltip="Security System for you and your spouse">
                <div class="new-shop-item--img">
                    <h5>Security System</h5>
                </div>
                <div class="new-shop-item--price">
                    <img src="css/images/coin.png">
                    <span>100</span>
                </div>
                <div class="new-shop-item--buy">
                    <a class="cta" href="?buy=sec">[Buy Now]</a>
                </div>
            </div>

            <div class="new-shop-item" data-tooltip="Customise Name & Set a picture for all 3 modifiers">
                <div class="new-shop-item--img">
                    <h5>Custom Item Set 80%</h5>
                </div>
                <div class="new-shop-item--price">
                    <img src="css/images/coin.png">
                    <span>500</span>
                </div>
                <div class="new-shop-item--buy">
                    <a class="cta" href="?buy=custom">[Buy Now]</a>
                </div>
            </div>

        </div>
    </div>
    <div class="section" style="flex-basis: 60%">
        <h4 class="section-title">Boosters</h4>
        <div class="section-items">

            <div class="new-shop-item" data-tooltip="When used you will have 1 hour of Double EXP">
                <div class="new-shop-item--img">
                    <h5>1x Double EXP</h5>
                </div>
                <div class="new-shop-item--price">
                    <img src="css/images/coin.png">
                    <span>50</span>
                </div>
                <div class="new-shop-item--buy">
                    <a class="cta" href="?buy=DE">[Buy Now]</a>
                </div>
            </div>
            <div class="new-shop-item" data-tooltip="When used you will have 1 hour of Double EXP">
                <div class="new-shop-item--img">
                    <h5>5x Double EXP</h5>
                </div>
                <div class="new-shop-item--price">
                    <img src="css/images/coin.png">
                    <span>220</span>
                </div>
                <div class="new-shop-item--buy">
                    <a class="cta" href="?buy=DE5">[Buy Now]</a>
                </div>
            </div>
            <div class="new-shop-item" data-tooltip="25x Nerve Boosters + 25x Energy Boosters">
                <div class="new-shop-item--img">
                    <h5>25x N+E Booster</h5>
                </div>
                <div class="new-shop-item--price">
                    <img src="css/images/coin.png">
                    <span>500</span>
                </div>
                <div class="new-shop-item--buy">
                    <a class="cta" href="?buy=boosters">[Buy Now]</a>
                </div>
            </div>

        </div>
    </div>
</div>


<?php
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
echo '<div class="floaty" style="margin:3px;">';
echo '<h3>By donating to TheMafiaLife you are agreeing to the following terms:</h3>';
echo '<hr>';
echo '<ul class="donate_rules">';
echo '<li><font color=white>No refunds will be given as the game runs on donations</li>';
echo '<li>Just because you have bought a package from us doesn\'t mean you can go around breaking rules. So be warned, you can still get banned for breaking them.</li>';
echo '<li>If you don\'t get your package, please contact <a href="mailto:support@themafialife.com"><span style="color:red;">support@themafialife.com</span></a> with the paypal receipt number</li>';
echo '<li>If you try refunding your money through paypal, we will ban your account.</li>';
echo '</div>';



echo '</div>';

echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';


include 'footer.php';
function refd($creds) {
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
function spentcreds($name, $creds) {
    global $db, $user_class;
    $db->query("INSERT INTO spentcredits (timestamp, spender, spent, amount) VALUES (unix_timestamp(), ?, ?, ?)");
    $db->execute(array(
        $user_class->id,
        $name,
        $creds
    ));
}
?>