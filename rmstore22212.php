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
if (isset($_GET['buy']) AND ! isset($_GET['confirm'])) {
    $pack = $_GET['buy'];
    echo Message("Are you sure you want to buy a {$packs[$pack]['Name']}? <br><a href='?buy={$_GET['buy']}&confirm=yes'>Continue</a><br />
    <a href='rmstore.php'>No thanks!</a>");
    include 'footer.php';
    die();
}
if (isset($_GET['buy']) AND isset($_GET['confirm'])) {
    $pack = $_GET['buy'];
    if ($user_class->credits >= $packs[$pack]['Cost']) {
        $user_class->credits -= $packs[$pack]['Cost'];
		$db->query("UPDATE grpgusers SET points = points + ?, credits = credits - ? WHERE id = ?");
		$db->execute(array(
			$packs[$pack]['Points'],
			$packs[$pack]['Cost'],
			$user_class->id
		));
        if ($packs[$pack]['Items'] != 0) {
            $items = explode("|", $packs[$pack]['Items']);
            foreach ($items as $item) {
                $itqty = explode(":", $item);
                Give_Item($itqty[0], $user_class->id, $itqty[1]);
            }
        }
        echo Message("You spent {$packs[$pack]['Cost']} credits for {$packs[$pack]['Name']}.");
        Send_Event($user_class->id, "You have been credited a {$packs[$pack]['Name']}.", $user_class->id);
		spentcreds($packs[$pack]['Name'], $packs[$pack]['Cost']);
        refd(10);
    } else
        echo Message("You don't have enough credits. You can buy some at the upgrade store.");
}
if ($user_class->donations >= 200) {
    $donmax = 200;
    $status = "SuperSponser";
} elseif ($user_class->donations >= 100) {
    $donmax = 200;
    $status = "Sponser+";
} elseif ($user_class->donations >= 50) {
    $donmax = 100;
    $status = "Sponser";
} else {
    $donmax = 50;
    $status = "Yobster";
}
$donperc = ($user_class->donations / $donmax) * 100;
$donperc = $donperc >= 100 ? 100 : $donperc;
echo'<div class="flexcont" style="align-items:stretch;">';
	echo'<div class="flexele floaty" style="margin:3px;">';
		echo'Credit Balance - <font color=red><b>x2 Sale</b></font>';
		echo'<hr style="border:0;border-bottom:thin solid #333;" />';
		echo'<font color=green><b>2 Credit = $1</font><br />';
		echo'Your credits balance is: <span style="color:white;font-weight:bold;">$' . $user_class->credits . '</span>';
	echo'</div>';
	echo'<div class="flexele floaty" style="flex:2;margin:3px;">';
		echo'My Donation Bar | <span style="font-weight:bold;color:green;">Current Status: ' . $status . '</span>';
		echo'<hr style="border:0;border-bottom:thin solid #333;" />';
		echo'<div class="flexcont">';
			echo'<div class="flexele">';
				echo'$' . (!$user_class->donations ? 0 : $user_class->donations);
			echo'</div>';
			echo'<div class="flexele" style="flex:10;">';
				echo'<div class="progress-bar blue stripes" style="height:20px;width:100%;">';
					echo'<span style="width: ' . $donperc . '%;height:20px;"></span>';
				echo'</div>';
			echo'</div>';
			echo'<div class="flexele">';
				echo'$' . $donmax;
			echo'</div>';
		echo'</div>';
	echo'</div>';
echo'</div>';
echo'<div class="flexcont" style="align-items:stretch;">';
	echo'<div class="flexele floaty" style="margin:3px;">';
		echo'Donate Today!';
		echo'<hr style="border:0;border-bottom:thin solid #333;" />';
		echo'<br />';
		echo'<br />';
		echo'<br />';
		echo'<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target=_blank class="style8">';
			echo'<input type="text" name="amount" value="100"/>';
			echo'<input type="hidden" name="cmd" value="_xclick">';
			echo'<input type="hidden" name="currency_code" value="USD">';
			echo'<input type="hidden" name="custom" value="' . $user_class->id . '">';
			echo'<input type="hidden" name="business" value="yobcity@hotmail.com">';
			echo'<input type="hidden" name="item_number" value="1">';
			echo'<input type="hidden" name="item_name" value="Donation to Yob City">';
			echo'<input type="hidden" name="notify_url" value="http://yobcity.com/ipn_credits.php">';
			echo'<br />';
			echo'<br />';
			echo'<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit">';
		echo'</form>';
	echo'</div>';
	echo'<div class="flexele floaty" style="flex:2;margin:3px;">';
		echo'<table id="donatetables" style="width:95%;">';
			echo'<tr>';
				echo'<th>Donation Benefits</th>';
				echo'<th colspan="3">Donation Title</th>';
			echo'</tr>';
			echo'<tr>';
				echo'<td> - </td>';
				echo'<td>Sponser</td>';
				echo'<td>Sponser+</td>';
				echo'<td>SuperSponser</td>';
			echo'</tr>';
			echo'<tr>';
				echo'<td>Refills:</td>';
				echo'<td>10% quicker</td>';
				echo'<td>15% quicker</td>';
				echo'<td>20% quicker</td>';
			echo'</tr>';
			echo'<tr>';
				echo'<td>Gym Trains:</td>';
				echo'<td>10% better</td>';
				echo'<td>15% better</td>';
				echo'<td>20% better</td>';
			echo'</tr>';
			echo'<tr>';
				echo'<td>Crime Gains:</td>';
				echo'<td>10% better</td>';
				echo'<td>15% better</td>';
				echo'<td>20% better</td>';
			echo'</tr>';
			echo'<tr>';
				echo'<td>Bank Interest:</td>';
				echo'<td>2% better</td>';
				echo'<td>3% better</td>';
				echo'<td>5% better</td>';
			echo'</tr>';
			echo'<tr>';
				echo'<td>Daily Bonus:</td>';
				echo'<td>75 points</td>';
				echo'<td>120 points</td>';
				echo'<td>150 points</td>';
			echo'</tr>';
		echo'</table>';
	echo'</div>';
echo'</div>';
if(!$user_class->rmdays){
	echo'<div class="floaty" style="margin:3px;">';
		echo'Become a Respect Yobster!';
		echo'<hr style="border:0;border-bottom:thin solid #333;" />';
		echo'<table width="90%">';
			echo'<tr>';
				echo'<td align="center"> You are not a Respected Yobster<br />';
					echo'Look what you are missing out on....<br /><br />';
					echo'<b style="color: red; font-size: 14px">Upgrade Now<br />';
						echo'From as little as $3</b>';
				echo'</td>';
				echo'<td>';
					echo'<ul>';
						echo'<li>4% Bank Interest instead of 2% each day</li>';
						echo'<li>Energy refills 5% faster!</li>';
						echo'<li>Nerve refills 5% faster!</li>';
						echo'<li>Awake refills 5% faster!</li>';
						echo'<li>Health refills 5% faster!</li>';
						echo'<li>Different Coloured name</li>';
						echo'<li>Receive money and points with your RY Days</li>';
					echo'</ul>';
				echo'</td>';
			echo'</tr>';
		echo'</table>';
	echo'</div>';
}
echo'<table id="donatetables" style="width:100%;">';
	echo'<tr>';
		echo'<th>Package</th>';
		echo'<th>Money</th>';
		echo'<th>Points</th>';
		echo'<th>Items</th>';
		echo'<th>Cost</th>';
		echo'<th>Buy</th>';
	echo'</tr>';
            $co = 0;
            foreach ($packs as $pack) {
                $num = (!isset($num) || $num == 2) ? 1 : 2;
                $itfinal = "";
                foreach (array('Money', 'Points', 'Items') as $att)
                    $pack[$att] = ($pack[$att] == 0) ? "-" : $pack[$att];
                if ($pack['Items'] != 0) {
                    $items = explode("|", $pack['Items']);
                    foreach ($items as $item) {
                        $itqty = explode(":", $item);
                        $name = mysql_fetch_array(mysql_query("SELECT itemname FROM items WHERE id = {$itqty[0]}"));
                        $itfinal .= $name['itemname'] . " [x" . $itqty[1] . "]<br />";
                    }
                } else
                    $itfinal = "-";
                echo'<tr style="text-align:center;height:50px;">';
					echo'<td>' . $pack['Name'] . '</td>';
					echo'<td>' . prettynum($pack['Money'], 1) . '</td>';
					echo'<td>' . prettynum($pack['Points']) . '</td>';
					echo'<td>' . $itfinal . '</td>';
					echo'<td>' . prettynum($pack['Cost'], 1) . '</td>';
					echo'<td><a href="?buy=' . $co++ . '"><b>BUY</b></a></td>';
				echo'</tr>';
            }
	echo'<tr style="text-align:center;height:50px;">';
		echo'<td>Security System</td>';
		echo'<td colspan="3">Security System for you and your spouse!</td>';
		echo'<td>$10</td>';
		echo'<td><a href="?buy=sec"><b>BUY</b></a></td>';
	echo'</tr>';
echo'</table>';
echo'<table id="donatetables" style="width:100%;">';
	echo'<tr>';
		echo'<th>Name</th>';
		echo'<th>Style</th>';
		echo'<th>Description</th>';
		echo'<th>Cost</th>';
		echo'<th>Buy</th>';
	echo'<tr style="height:50px;">';
		echo'<td>30 Day Gradient name</td>';
		echo'<td>';
			echo formatName(146, 1);
		echo'</td>';
		echo'<td>Any range of colours. Can be 2 or 3 colours.</td>';
		echo'<td>$3.00</td>';
		echo'<td><a href="rmstore.php?buy=2gradient"><b>BUY</b></a></td>';
	echo'</tr>';
	echo'<tr style="height:50px;">';
		echo'<td>Picture name</td>';
		echo'<td>';
			echo'<img src="images/twist.png"/>';
		echo'</td>';
		echo'<td>Can be totally customised, any colour, style or background</td>';
		echo'<td>$10.00</td>';
		echo'<td><a href="rmstore.php?buy=imgname"><b>BUY</b></a></td>';
	echo'</tr>';
echo'</table>';
echo'<div class="floaty" style="margin:3px;">';
	echo'<span style="color:red;font-weight:bold;">By donating to YobCity you are agreeing to the following terms:</span>';
	echo'<hr style="border:0;border-bottom:thin solid #333;" />';
	echo'<ol>';
		echo'<li>No refunds will be given as the game runs on donations</li>';
		echo'<li>Just because you have bought a package from us doesn\'t mean you can go around breaking rules. So be warned, you can still get banned for breaking them.</li>';
		echo'<li>If you don\'t get your package, please contact <span style="color:red;">support@yobcity.com </span>with the paypal receipt number</li>';
		echo'<li>If you try refunding your money through paypal, we will ban your account.</li>';
		echo'<li>* Limited edition pack items are excluded from buy one get one free offers, although any points, money and RY Days are doubled.</li>';
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