 <?php
include 'header.php';
// When adding Items to packs, it is formatted like this... ITEMID:QTY ... for multiple items seperate them by a "|" like this... ITEMID:QTY|ITEMID:QTY
$packs = array(
    array(
        "Name" => "30 Day Respected Yobster Pack",
        "RY Days" => 30,
        "Money" => 150000,
        "Points" => 500,
        "Cost" => 3,
        "Items" => "None"
    ),
    array(
        "Name" => "60 Day Respected Yobster Pack",
        "RY Days" => 60,
        "Money" => 300000,
        "Points" => 1000,
        "Cost" => 6,
        "Items" => "None"
    ),
    array(
        "Name" => "90 Day Respected Yobster Pack",
        "RY Days" => 90,
        "Money" => 450000,
        "Points" => 1500,
        "Cost" => 9,
        "Items" => "None"
    ),
    array(
        "Name" => "500 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 500,
        "Cost" => 2,
        "Items" => "None"
    ),
    array(
        "Name" => "2000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 2000,
        "Cost" => 4,
        "Items" => "None"
    ),
    array(
        "Name" => "4500 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 4500,
        "Cost" => 9,
        "Items" => "None"
    ),
    array(
        "Name" => "9000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 9000,
        "Cost" => 15,
        "Items" => "None"
    ),
    array(
        "Name" => "13000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 13000,
        "Cost" => 20,
        "Items" => "None"
    ),
    array(
        "Name" => "20000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 20000,
        "Cost" => 32,
        "Items" => "None"
    ),
    array(
        "Name" => "30000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 30000,
        "Cost" => 45,
        "Items" => "None"
    ),
    array(
        "Name" => "70000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 70000,
        "Cost" => 100,
        "Items" => "None"
    ),
  array(
        "Name" => "200000 Points",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 200000,
        "Cost" => 200,
        "Items" => "None"
    ),

    array(
        "Name" => "10 Awake Pills",
        "RY Days" => 0,
        "Money" => 0,
        "Points" => 0,
        "Cost" => 5,
        "Items" => "4:10"
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
    )


);
if(isset($_GET['buy'])){
	if($_GET['buy'] == "sec")
		diefun("Are you sure you want to buy a Security System? <br><a href='rmstore.php?buy=secyes'>Continue</a><br /><a href='rmstore.php'>No thanks!</a>");
	if ($_GET['buy'] == "2gradient")
		diefun("Are you sure you want to buy a 30 Day Colour Gradient Name? <br><a href='rmstore.php?buy=2gradientyes'>Continue</a><br /><a href='rmstore.php'>No thanks!</a>");
	if($_GET['buy'] == "secyes"){
		if($user_class->credits <= 10)
			diefun("You need 10 credits to buy a security system.");
		$robinfo = explode("|",$user_class->robInfo);
		if($robinfo[0] == 1)
			diefun("You can only have 1 security system.");
		$robinfo[0] = 1;
		$user_class->credits -= 10;
		mysql_query("UPDATE grpgusers SET robInfo = '".implode("|",$robinfo)."', credits = credits - 10 WHERE id = $user_class->id");
		if($user_class->relplayer)
			mysql_query("UPDATE grpgusers SET robInfo = '".implode("|",$robinfo)."' WHERE id = $user_class->relplayer");
		diefun("You have purchased a security system!");
	}
	if ($_GET['buy'] == "2gradientyes") {
		if ($user_class->credits >= 3) {
			$newcredit = $user_class->credits - 3;
			$grad = ($user_class->gradient != 3) ? "2" : "3";
			$result    = mysql_query("UPDATE `grpgusers` SET gndays = gndays + 30, `gradient` = $grad, `timeschanged` = '0', `credits` = '" . $newcredit . "' WHERE `id`='" . $_SESSION['id'] . "'");
			$result    = mysql_query("INSERT INTO `spentcredits` (timestamp, spender, spent, amount)" . "VALUES ('" . $time . "', '" . $user_class->id . "', '30 Day Colour Gradient Name', '3')");
			echo Message("You spent 3 credits for the 30 Day Colour Gradient Name. To use it, visit your details page.");
			$result        = mysql_query("SELECT * FROM `referrals` WHERE `referred` = '" . $user_class->id . "'");
			$line          = mysql_fetch_array($result);
			$cp_user       = new User($line['referrer']);
			$referred_user = new User($line['referred']);
			$newpoints     = $cp_user->points + 30;
			$result        = mysql_query("UPDATE `grpgusers` SET `points` = '" . $newpoints . "' WHERE `id`='" . $cp_user->id . "'");
			Send_Event($cp_user->id, "You have been credited 30 points because you referred " . $referred_user->formattedname . " and they bought something from the RM store.");
		} else {
			echo Message("You don't have enough credits. You can buy some at the upgrade store.");
		}
	}
}
if (isset($_GET['buy']) AND !isset($_GET['confirm'])) {
    $pack = $_GET['buy'];
    echo Message("Are you sure you want to buy a {$packs[$pack]['Name']}? <br><a href='?buy={$_GET['buy']}&confirm=yes'>Continue</a><br />
    <a href='rmstore.php'>No thanks!</a>");
    include 'footer.php';
    die();
}
if (isset($_GET['buy']) AND isset($_GET['confirm'])) {
    $pack = $_GET['buy'];
    if ($user_class->credits >= $packs[$pack]['Cost']) {
        $newcredit = $user_class->credits - $packs[$pack]['Cost'];
        $time      = time();
        $result    = mysql_query("UPDATE `grpgusers` SET rmdays = rmdays + {$packs[$pack]['RY Days']}, points = points + {$packs[$pack]['Points']}, money = money + {$packs[$pack]['Money']}, credits = credits - {$packs[$pack]['Cost']} WHERE `id` = $user_class->id");
        $result    = mysql_query("INSERT INTO `spentcredits` (timestamp, spender, spent, amount)" . "VALUES (unix_timestamp(), $user_class->id, {$packs[$pack]['Name']}, {$packs[$pack]['Cost']})");
        if ($packs[$pack]['Items'] != 0) {
            $items = explode("|", $packs[$pack]['Items']);
            foreach ($items as $item) {
                $itqty = explode(":", $item);
                Give_Item($itqty[0], $user_class->id, $itqty[1]);
            }
        }
        echo Message("You spent {$packs[$pack]['Cost']} credits for {$packs[$pack]['Name']}.");
        Send_Event($user_class->id, "You have been credited a {$packs[$pack]['Name']}. This Pack Has been Applied", $user_class->id);
        $result    = mysql_query("SELECT * FROM `referrals` WHERE `referred` = $user_class->id");
        $line      = mysql_fetch_array($result);
        $cp_user   = new User($line['referrer']);
        $addpoints = $packs[$pack]['Cost'] * 10;
        $result    = mysql_query("UPDATE `grpgusers` SET `points` = points + $addpoints WHERE `id` = $cp_user->id ");
        Send_Event($cp_user->id, "You have been credited $addpoints points because you referred " . $user_class->formattedname . " and they bought something from the RM store.");
    } else
        echo Message("You don't have enough credits. You can buy some at the upgrade store.");
}
if($user_class->donations >=200){
$donmax = 200;
$status = "SuperSponser";
}elseif($user_class->donations >=100){
$donmax = 200;
$status = "Sponser+";
}elseif($user_class->donations >=50){
$donmax = 100;
$status = "Sponser";
}else{
$donmax = 50;
$status = "Yobster";
}

if($user_class->donations >=100)
$status = "SuperSponser";
$donperc = ($user_class->donations / $donmax) * 100;
$donperc = $donperc >= 100 ? 100 : $donperc;
?>
<tr><td class="contentspacer"></td></tr><tr><td class="contenthead">Credit Balance</td></tr>
<tr><td class="contentcontent">
<center>1 Credit = $1</center>
<center>Your credits balance is: $<font color=white><b><?php
echo $user_class->credits;
?></center></font></b>
</tr></td>
<tr><td class="contentspacer"></td></tr><tr><td class="contenthead">Become A Respected Yobster</td></tr>
<center><table class= 'contentindexhome' width='90%'>
<tr>
<td align='center'> You are not a Respected Yobster<br />
Look what you are missing out on....<br /><br />
<b style='color: red; font-size: 14px'>Upgrade Now<br />
From as little as $3</b>
</td>
<td><ul>
<li>4% Bank Interest instead of 2% each day</li>
<li>Energy and Nerve refills twice as quickly</li>
<li>Awake and Health refills twice as quick</li>
<li>More points when voting</li>
<li>Friends and Blacklist</li>
<li>Different Coloured name</li>
<li>Receive money and points with your RY Days</li>
</td>
</tr>
</table>
<tr><div class="contenthead" style='width:100%;'>Buy Credits</div></tr>
<tr><div class="contentcontent"><br /><center><span style='font-weight:bold;color:red;'>My Donation Bar:</span></center>
<table style='width:100%;margin-left:-10px;'><tr><td width='1%'>$<?php echo $user_class->donations ?></td>
<td><div class='progress-bar blue stripes' style='height:20px;width:100%;'><span style='width: <?php echo $donperc ?>%;height:20px;'></td>
<td width='1%'>$<?php echo $donmax ?></td></tr></table>
<center><span style='font-weight:bold;color:green;'>Current Status: <?php echo $status ?> </span></center><br />
<table style='margin:0 auto;width:75%;text-align:center;'>
<tr style="background-color:RGB(42,103,137);height:25px;">
<td>Donation Benefits</td><td colspan='3'>Donation Title</td>
</tr>
<tr class='colour1' style='height:25px;'><td> - </td><td>Sponser</td><td>Sponser+</td><td>SuperSponser</td></tr>
<tr class='colour2' style='height:25px;'><td>Refills:</td><td>10% quicker</td><td>15% quicker</td><td>20% quicker</td></tr>
<tr class='colour1' style='height:25px;'><td>Gym Trains:</td><td>10% better</td><td>15% better</td><td>20% better</td></tr>
<tr class='colour2' style='height:25px;'><td>Crime Gains:</td><td>10% better</td><td>15% better</td><td>20% better</td></tr>
<tr class='colour1' style='height:25px;'><td>Bank Interest:</td><td>2% better</td><td>3% better</td><td>5% better</td></tr>
<tr class='colour2' style='height:25px;'><td>Daily Bonus:</td><td>75 points</td><td>120 points</td><td>150 points</td></tr>
</table>
<br /><center>If you do not receieve your credits, Please message a member of staff or create a support ticket.<br />
<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target=_blank class="style8">
<input type="text" name="amount" value="100"/>
<input type="hidden" name="cmd" value="_xclick">
<input type="hidden" name="currency_code" value="USD">
<input type="hidden" name=custom value=<?php
echo $user_class->id;
?>>
<input type="hidden" name="business" value="jamesmafiagaming@gmail.com">
<input type="hidden" name="item_number" value="1">
<input type="hidden" name="item_name" value="Donation to Yob City">
<input type="hidden" name="notify_url" value="http://yobcity.com/ipn_credits.php">
<br>
<div class="dvd"></div>
<br>
<input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online.">
<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1">
</form>
</center>
</div></tr>
<table class=contentindex style='width:100%;table-layout:auto;'>
<tr class=contentheader><th style="width:16%;">Package</th><th>RY days</th><th>Money</th><th>Points</th><th>Items</th><th>Cost</th><th>Buy</th>
</tr>
<?php
$co = 0;
foreach ($packs as $pack) {
    $num     = (!isset($num) || $num == 2) ? 1 : 2;
    $itfinal = "";
    foreach (array(
        'RY Days',
        'Money',
        'Points',
        'Items'
    ) as $att)
        $pack[$att] = ($pack[$att] == 0) ? "-" : $pack[$att];
    if ($pack['Items'] != 0) {
        $items = explode("|", $pack['Items']);
        foreach ($items as $item) {
            $itqty = explode(":", $item);
            $name  = mysql_fetch_array(mysql_query("SELECT itemname FROM items WHERE id = {$itqty[0]}"));
            $itfinal .= $name['itemname'] . " [x" . $itqty[1] . "]<br />";
        }
    } else {
        $itfinal = "-";
    }
    print "<tr class='colour{$num}' style='text-align:center;height:50px;'><td>{$pack['Name']}</td><td>{$pack['RY Days']}</td><td>" . prettynum($pack['Money'], 1) . "</td><td>" . prettynum($pack['Points']) . "</td><td>{$itfinal}</td><td>\${$pack['Cost']}</td><td><a href='?buy=" . $co++ . "'><b>BUY</b></a></td></tr>";
}
echo"<tr class='colour",$num == 1 ? 2 : 1,"' style='text-align:center;height:50px;'><td>Security System</td><td>-</td><td>-</td><td>-</td><td>Security System for<br /> you and your spouse!</td><td>\$10</td><td><a href='?buy=sec'><b>BUY</b></a></td></tr>";
?>
</table>
<table class=contentindex style='width:100%;'>
<tr class=contentheader><th>Name</th><th>Style</th><th>Description</th><th>Cost</th><th>Buy</th>
<tr class='colour1'>
<td class="textl" valign="middle">30 Day Gradient name</td>
<td class="textm" valign="middle">
<font color="#ab07c0">S</font><font color="#981eb8">p</font><font color="#8535b0">i</font><font color="#724ba8">r</font><font color="#6062a1">a</font><font color="#4d7999">l</font>
</td>
<td class="textm" valign="middle">Any range of colours. Can be 2 or 3 colours.</td>
<td class="textm" valign="middle">$3.00</td>
<td class='textc' align='center' valign='middle'><a href="rmstore.php?buy=2gradient"><b>BUY</b></a></td>
</tr>
<tr class='colour2'>
<td class="textl" valign="middle">Picture name</td>
<td class="textm" valign="middle">
<img src="images/twist.png"/>
</td>
<td class="textm" valign="middle">Can be totally customised, any colour, style or background</td>
<td class="textm" valign="middle">$10.00</td>
<td class='textc' align='center' valign='middle'>BUY</td>
</tr>
</table>
<br><table class='contentcontent' width='95%'><tr><td>
<br><font color="red"><strong><br>PLEASE READ:</strong> <ul>
<table>
<tr><td align=left>
<br>By donating to YobCity you are agreeing to the following terms::
<br>1. No refunds will be given as the game runs on donations
<br>2. Just because you have bought a package from us doesn't mean you can go around breaking rules. So be warned, you can still get banned for breaking them.
<br>3. If you don't get your package, please contact <font color="red">support@yobcity.com </font>with the paypal receipt number
<br>4. If you try refunding your money through paypal, we will ban your account.<br>
<br>5. * Limited edition pack items are excluded from buy one get one free offers, although any points, money and RY Days are doubled.
<br /><br />Username-specific conditions:
<ul>
<li>Shown names are just examples</li>
<li>All names can be customised to your taste.</li>
<li>You have the final say on the design of the name</li>
<li>Usernames can take about 2-3 days to be designed.</li>
<li>After your final decision concerning the username it cannot be changed unless you buy a new username.</li>
<li>We will always try our best to meet your requirments, but cannot promise it is doable.</li>
<li>If you do not like any of the proposals, you can keep your normal name, however money will not be refunded.</li>
</ul> </table></td></tr></table>
<?php
include 'footer.php';
?> 