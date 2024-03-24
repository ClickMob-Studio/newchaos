<?php
include 'header.php';
// When adding Items to packs, it is formatted like this... ITEMID:QTY ... for multiple items seperate them by a "|" like this... ITEMID:QTY|ITEMID:QTY
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
        "Items" => "42:5"),
    array(
        "Name" => "<font color=gold>Mystery Box[x10] </font>",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 0,
        "Cost" => 9,
        "Items" => "42:10"),
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
        if ($user_class->credits <= 10)
            diefun("You need 10 credits to buy a security system.");
        $robinfo = explode("|", $user_class->robInfo);
        if ($robinfo[0] == 1)
            diefun("You can only have 1 security system.");
        $robinfo[0] = 1;
        $user_class->credits -= 10;
		$db->query("UPDATE grpgusers SET robInfo = ?, credits = credits - 10 WHERE id = ?");
		$db->execute(array(
			implode("|", $robinfo),
			$user_class->id
		));
        if ($user_class->relplayer){
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
        if ($user_class->credits >= 3) {
            $user_class->credits -= 3;
			$db->query("UPDATE grpgusers SET gndays = gndays + 30, gradient = ?, credits = credits - 3 WHERE id = ?");
			$db->execute(array(
				($user_class->gradient != 3) ? "2" : "3",
				$user_class->id
			));
			spentcreds('30 Day Colour Gradient Name', 3);
			refd(3);
            echo Message("You spent 3 credits for the 30 Day Colour Gradient Name. To use it, visit your details page.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }





if ($_GET['buy'] == "1000") {
        if ($user_class->credits >= 30) {            

$newcredit = $user_class->credits -= 30;


			$db->query("UPDATE grpgusers SET points = points + 1000, credits = credits - 30 WHERE id = ?");
				$db->execute(array(
				$user_class->id
			));
	
            echo Message("You spent 30 credits for 1000 Points.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }

if ($_GET['buy'] == "3500") {
        if ($user_class->credits >= 70) {            

$newcredit = $user_class->credits -= 70;


			$db->query("UPDATE grpgusers SET points = points + 3500, credits = credits - 70 WHERE id = ?");
				$db->execute(array(
				$user_class->id
			));
	
            echo Message("You spent 70 credits for 3,500 Points.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }


if ($_GET['buy'] == "12000") {
        if ($user_class->credits >= 190) {            

$newcredit = $user_class->credits -= 190;


			$db->query("UPDATE grpgusers SET points = points + 12000, credits = credits - 190 WHERE id = ?");
				$db->execute(array(
				$user_class->id
			));
	
            echo Message("You spent 190 credits for 12,000 Points.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }


if ($_GET['buy'] == "35000") {
        if ($user_class->credits >= 550) {            

$newcredit = $user_class->credits -= 550;


			$db->query("UPDATE grpgusers SET points = points + 35000, credits = credits - 550 WHERE id = ?");
				$db->execute(array(
				$user_class->id
			));
	
            echo Message("You spent 550 credits for 35,000 Points.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }


if ($_GET['buy'] == "85000") {
        if ($user_class->credits >= 1000) {            

$newcredit = $user_class->credits -= 1000;


			$db->query("UPDATE grpgusers SET points = points + 85000, credits = credits - 1000 WHERE id = ?");
				$db->execute(array(
				$user_class->id
			));
	
            echo Message("You spent 1,000 credits for 85,000 Points.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }

if ($_GET['buy'] == "200000") {
        if ($user_class->credits >= 2200) {            

$newcredit = $user_class->credits -= 2200;


			$db->query("UPDATE grpgusers SET points = points + 200000, credits = credits - 2200 WHERE id = ?");
				$db->execute(array(
				$user_class->id
			));
	
            echo Message("You spent 2,200 credits for 200,000 Points.");
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

                         Give_Item(51, $user_class->id);//give the user their item they bought
                        Send_Event($user_class->id, "You have been credited with your 30 Day RM pack.", $user_class->id);
				$db->execute(array(
			));
	
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

                         Give_Item(103, $user_class->id);//give the user their item they bought
                        Send_Event($user_class->id, "You have been credited with your 60 Day RM pack.", $user_class->id);
				$db->execute(array(
			));
	
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

                         Give_Item(104, $user_class->id);//give the user their item they bought
                        Send_Event($user_class->id, "You have been credited with your 90 Day RM pack.", $user_class->id);
				$db->execute(array(
			));
	
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

                         Give_Item(51, $user_class->id, 10);//give the user their item they bought
                        Send_Event($user_class->id, "You have been credited with your 90 Day RM pack[x10].", $user_class->id);
				$db->execute(array(
			));
	
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

                         Give_Item(104, $user_class->id, 10);//give the user their item they bought
                        Send_Event($user_class->id, "You have been credited with your 90 Day RM pack[x10].", $user_class->id);
				$db->execute(array(
			));
	
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

                         Give_Item(103, $user_class->id, 10);//give the user their item they bought
                        Send_Event($user_class->id, "You have been credited with your 60 Day RM pack[x10].", $user_class->id);
				$db->execute(array(
			));
	
            echo Message("You spent 540 credits for a 60 day RM pack[x10].");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
    }










    if ($_GET['buy'] == "imgnameyes") {
        if ($user_class->credits >= 10) {
            $newcredit = $user_class->credits - 10;
			$db->query("UPDATE grpgusers SET pdimgname = 1, credits = credits - 10 WHERE id = ?");
			$db->execute(array(
				$user_class->id
			));
			spentcreds('img name', 10);
			refd(10);
            echo Message("You spent 10 credits for the an Image Name. To use it, visit your details page.");
        } else {
            echo Message("You don't have enough credits. You can buy some at the upgrade store.");
        }
		diefun();
    }
}
$donperc = ($user_class->donations / $donmax) * 100;
$donperc = $donperc >= 100 ? 100 : $donperc;
echo'<div class="flexcont" style="align-items:stretch;">';
	echo'<div class="flexele floaty" style="margin:3px;">';
		echo'Make a Donation to Mean Streets';
		echo'<hr style="border:0;border-bottom:thin solid #333;" />';
		echo'<font color=white>Your credits balance is:</font> <span style="color:yellow;font-weight:bold;">' . $user_class->credits . ' Credit(s)</span><br />';
		echo'<font color=yellow><b>10 Credit(s) = $1</font>';


echo'<br />';
echo'<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target=_blank class="style8">';
			echo'<input type="text" name="amount" value="30"/>';
			echo'<input type="hidden" name="cmd" value="_xclick">';
			echo'<input type="hidden" name="currency_code" value="USD">';
			echo'<input type="hidden" name="custom" value="' . $user_class->id . '">';
			echo'<input type="hidden" name="business" value="yobcity@hotmail.com">';
			echo'<input type="hidden" name="item_number" value="1">';
			echo'<input type="hidden" name="item_name" value="Donation to MeanStreets">';
			echo'<input type="hidden" name="notify_url" value="http://yobcity.com/ipn_credits.php">';
			echo'<br />';
			echo'<br />';
			echo'<input type="image" width="190px" height="60px" src="images/donate.png" border="0" name="submit">';
		echo'</form>';
	echo'</div>';



	echo'</div>';
		echo'</div>';
echo'</div>';
		
echo'</div>';
if(!$user_class->rmdays){
	echo'<table id="donatetables" style="width:100%;">';
	echo'<tr>';
		echo'<tr>';
		echo' <td id="headerRow2">Respected Mobster Upgrades</td>';
		echo' <td id="dottedRow3"></td>';
		echo' <td id="dottedRow3"></td>';
		echo' <td id="dottedRow3"></td>';
		echo' <td id="dottedRow4"></td>';

	echo'</tr>';

        echo'<tr>';
            echo'<td id="dottedRow1">&nbsp;</td>';
            echo'<td id="dottedRow" height="70" valign="middle"><b>Free Member</b></td>';
            echo'<td id="dottedRow" valign="top"><img src="images/30day.png" border="0"></td>';
            echo'<td id="dottedRow" valign="top"><img src="images/60day.png" border="0"></td>';
            echo'<td id="dottedRow2" valign="top"><img src="images/90day.png" border="0"></td>';
        echo'</tr>';
        echo'<tr>';
            echo'<td id="dottedRow1">Money Bonus (One Time)</td>';
            echo'<td id="dottedRow">-</td>';
            echo'<td id="dottedRow">$150,000</td>';
            echo'<td id="dottedRow">$300,000</td>';
            echo'<td id="dottedRow2">$450,000</td>';
        echo'</tr>';
        echo'<tr>';
            echo'<td id="dottedRow1">Points Bonus (One Time)</td>';
            echo'<td id="dottedRow">-</td>';
            echo'<td id="dottedRow">750</td>';
            echo'<td id="dottedRow">1500</td>';
            echo'<td id="dottedRow2">2500</td>';
        echo'</tr>';
        echo'<tr>';
            echo'<td id="dottedRow1">Energy Regain (Per 5 Mins)</td>';
            echo'<td id="dottedRow">10%</td>';
            echo'<td id="dottedRow">20%</td>';
            echo'<td id="dottedRow">20%</td>';
            echo'<td id="dottedRow2">20%</td>';
        echo'</tr>';
        echo'<tr>';
            echo'<td id="dottedRow1">Bank Interest</td>';
            echo'<td id="dottedRow">2%</td>';
            echo'<td id="dottedRow">4%</td>';
            echo'<td id="dottedRow">4%</td>';
            echo'<td id="dottedRow2">4%</td>';
        echo'</tr>';
        echo'<tr>';
            echo'<td id="dottedRow1">Max Bank Interest</td>';
            echo'<td id="dottedRow">$300,000</td>';
            echo'<td id="dottedRow">$600,000</td>';
            echo'<td id="dottedRow">$600,000</td>';
            echo'<td id="dottedRow2">$600,000</td>';
        echo'</tr>';
        echo'<tr>';
            echo'<td id="dottedRow1">Inventory Size</td>';
            echo'<td id="dottedRow">100</td>';
            echo'<td id="dottedRow">300</td>';
            echo'<td id="dottedRow">300</td>';
            echo'<td id="dottedRow2">300</td>';
        echo'</tr>';
        echo'<tr>';
            echo'<td id="dottedRow1">Cost</td>';
            echo'<td id="dottedRow">Free</td>';
            echo'<td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;30</b></font></td>';
           echo'<td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;60</b></font></td>';
          echo'<td id="dottedRow2"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;90</b></font></td>';


      echo'</tr>';
        echo'<tr>';
            echo'<td id="dottedRow7">&nbsp;</td>';
            echo'<td id="dottedRow5">&nbsp;</td>';
            echo'<td id="dottedRow5"><a href="?buy=rm30">[Buy Now]</a></td>';
            echo'<td id="dottedRow5"><a href="?buy=rm60">[Buy Now]</a></td>';
            echo'<td id="dottedRow6"><a href="?buy=rm90">[Buy Now]</a></td>';
        echo'</tr>';
    echo'</table>';
    echo'</div>	';
echo'</div>';
}
	echo'</br>';
echo'<table id="donatetables" style="width:100%;">';
	echo'<tr>';
		echo' <td id="headerRow2">Package</td>';
		echo' <td id="dottedRow3">Cost</td>';
		echo' <td id="dottedRow4">Buy</td>';
	echo'</tr>';
           	echo'<tr style="text-align:left;height:50px;">';
		echo'<td id="dottedRow1">10x 30 Day RM Pack<br><font color=silver>[You will recieve 10x 30day RM packs]</td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;270</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=rm3010"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">10x 60 Day RM Pack<br><font color=silver>[You will recieve 10x 60day RM packs]</td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;540</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=rm6010"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">10x 90 Day RM Pack<br><font color=silver>[You will recieve 10x 90day RM packs]</td>';

		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;810</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=rm9010"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">30 Day Image Name<br> Preview: <img src="images/twist.png"/></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;90</b></font></td>';
		echo'<td id="dottedRow2"><font color=red><b><s>[Buy Now]</s></b></font></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">30 Day Gradient name<br>Preview: <span style="color:#990A00;text-shadow: 0 0 10px #E80000;">S</span><span style="color:#A32900;text-shadow: 0 0 10px #EF2501;">m</span><span style="color:#AD4800;text-shadow: 0 0 10px #F74A02;">O</span><span style="color:#B86800;text-shadow: 0 0 10px #FF7003;">k</span><span style="color:#B86800;text-shadow: 0 0 10px #FF7003;">e</span><span style="color:#B87C2E;text-shadow: 0 0 10px #FF9342;">d</span><span style="color:#B9915D;text-shadow: 0 0 10px #FFB781;">O</span><span style="color:#B9A58B;text-shadow: 0 0 10px #FFDBC0;">n</span><span style="color:#BABABA;text-shadow: 0 0 10px #FFFFFF;">e</span></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;50</b></font></td>';
		echo'<td id="dottedRow2"><font color=red><b><s>[Buy Now]</s></b></font></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">1,000 Point Pack<br><font color=green>[1,000 points added to your account]</font></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;30</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=1000"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">3,500 Point Pack<br><font color=green>[3,500 points added to your account]</font></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;70</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=3500"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">12,000 Point Pack<br><font color=green>[12,000 points added to your account]</font></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;190</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=12000"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">35,000 Point Pack<br><font color=green>[35,000 points added to your account]</font></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;550</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=35000"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">85,000 Point Pack<br><font color=green>[85,000 points added to your account]</font></td>';
		echo'<div><td id="dottedRow">&nbsp;&nbsp;<img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;1000</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=85000"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">200,000 Point Pack<br><font color=green>[200,000 points added to your account]</font></td>';
		echo'<div><td id="dottedRow">&nbsp;&nbsp;<img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;2200</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=200000"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">500,000 Point Pack<br><font color=green>[500,000 points added to your account]</font></td>';
		echo'<div><td id="dottedRow">&nbsp;&nbsp;<img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;5000</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=500000"><b>[Buy Now]</b></a></td>';
	echo'</tr>';

echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">5 Awake Pills<br><font color=red>[Gives you 100% awake]</font></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;50</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=sec"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">10 Awake Pills<br><font color=red>[Gives you 100% awake]</font></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;80</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=sec"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">50 Awake Pills<br><font color=red>[Gives you 100% awake]</font></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;320</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=sec"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">1 Hour Mug Protection<br><font color=orange>[*When used you cannot be mugged for 1 hour*]</font></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;20</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=sec"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">1 Hour Mug Protection x5<br><font color=orange>[*When used you cannot be mugged for 1 hour*]</font></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;80</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=sec"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">1 Hour Attack Protection<br><font color=yellow>[*When used you cannot be attacked for 1 hour*]</font></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;20</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=sec"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">1 Hour Attack Protection x5<br><font color=yellow>[*When used you cannot be attacked for 1 hour*]</font></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;&nbsp;&nbsp;80</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=sec"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">Double EXP<br><font color=lightblue>[*When used you will have 1 hour of Double EXP*]</font></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;120</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=sec"><b>[Buy Now]</b></a></td>';
	echo'</tr>';
echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">Double EXP x5<br><font color=lightblue>[*When used you will have 1 hour of Double EXP*]</font></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;490</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=sec"><b>[Buy Now]</b></a></td>';
	echo'</tr>';


echo'<tr style="text-align:center;height:50px;">';
		echo'<td id="dottedRow1">Security System<br><font color=pink>[Security System for you and your spouse]</font></td>';
		echo'<div><td id="dottedRow"><img src="images/coin.png"><font size="3" color="yellow"><b>&nbsp;100</b></font></td>';
		echo'<td id="dottedRow2"><a href="?buy=sec"><b>[Buy Now]</b></a></td>';
	echo'</tr>';

echo'</table>';
echo'<div class="floaty" style="margin:3px;">';
	echo'<span style="color:yellow;font-weight:bold;">By donating to MeanStreets you are agreeing to the following terms:</span>';
	echo'<hr style="border:0;border-bottom:thin solid #333;" />';
	echo'<ol>';
		echo'<li>No refunds will be given as the game runs on donations</li>';
		echo'<li>Just because you have bought a package from us doesn\'t mean you can go around breaking rules. So be warned, you can still get banned for breaking them.</li>';
		echo'<li>If you don\'t get your package, please contact <span style="color:red;">support@meanstreets.com </span>with the paypal receipt number</li>';
		echo'<li>If you try refunding your money through paypal, we will ban your account.</li>';
		echo'<li>* Limited edition pack items are excluded from buy one get one free offers, although any points, money and RM Days are doubled.</li>';
	echo'</ol>';
echo'</div>';
include 'footer.php';
function refd($creds){
	global $db, $user_class;
	$pts = $creds * 10;
	$db->query("SELECT * FROM referrals WHERE referred = ?");
	$db->execute(array(
		$user_class->id
	));
	$ref = $db->fetch_array(true);
	if(!empty($ref)){
		$refid = $ref['referrer'];
		$db->query("UPDATE grpgusers SET points = points + $pts WHERE id = ?");
		$db->execute(array(
			$user_class->id
		));
		Send_Event($refid, "You have been credited $pts points because you referred [-_USERID_-], and they bought something from the RY store.", $refid);
	}
}
function spentcreds($name, $creds){
	global $db, $user_class;
	$db->query("INSERT INTO spentcredits (timestamp, spender, spent, amount) VALUES (unix_timestamp(), ?, ?, ?)");
	$db->execute(array(
		$user_class->id,
		$name,
		$creds
	));
}
?>