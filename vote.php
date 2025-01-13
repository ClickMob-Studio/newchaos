<?php
ob_start();  // Start output buffering
include 'header.php';
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
?>
<div class='box_top'>Vote</div>
						<div class='box_middle'>
							<div class='pad'>
                                <?php


$sites = array(
   //"xtremetop100" => "https://www.xtremetop100.com/in.php?site=1132375705&user_id=" . $user_class->id . "&script_callback=xtremetop100",
   //"ArenaTop100" => "https://www.arena-top100.com/index.php?a=in&u=ChaosCity&id=" . $user_class->id . "&secret=ArenaTop100",
   //"bbogd" => "https://bbogd.com/vote/chaos/" . $user_class->id,
   "mmohub" => "https://mmohub.com/site/951/vote/" . $user_class->id,
//   "mpogtop" => "https://mpogtop.com/in/1712459252",
//    "top100arena" => "http://www.top100arena.com/in.asp?id=100478",
//    "topgamesites" => "http://www.topgamesites.net/mmorpg",
//    "xtremetop" => "http://www.xtremetop100.com/in.php?site=1132375476",
//	"gtop100" => "https://gtop100.com/topsites/MMORPG-And-MPOG/sitedetails/Mafia-Lords-103275?vote=1",
);

if (isset($_GET['vote']) && array_key_exists($_GET['vote'], $sites)) {
//	$db->query("SELECT * FROM votes WHERE userid = ? AND site = ?");
//	$db->execute(array(
//		$user_class->id,
//		$_GET['vote']
//	));
//	$voted = $db->fetch_row(true);
//    if (!empty($voted))
//        diefun("You have already voted there for today!");
//	$db->query("INSERT INtO votes VALUES (NULL, ?, ?)");
//	$db->execute(array(
//		$user_class->id,
//		$_GET['vote']
//	));
//	$db->query("UPDATE grpgusers SET points = points + 1000, votetokens = votetokens + 100, money = money + 100000 WHERE id = ?");
//	$db->execute(array(
//		$user_class->id
//	));
    header('Location: ' . $sites[$_GET['vote']]);
}
echo'<div class="floaty" style="margin:5px;width:75%;>';
	echo'<span style="color:red;font-weight:bold;">Vote Tokens Availible: <span style="color:#FFA500;"> ' . prettynum($user_class->votetokens) . ' Vote Points</span></br></span>';

	echo'<span style="color:red;font-weight:bold;">Each vote gives you: <span style="color:#FFA500;">100 Vote Points</span></BR></BR></span>';

	echo'<span style="color:red;font-weight:bold;"><a href="voteshop.php">CLICK HERE FOR VOTE SHOP</a></span>';


	echo'</div>';
foreach ($sites as $name => $link) {
	$db->query("SELECT * FROM votes WHERE userid = ? AND site = ?");
	$db->execute(array(
		$user_class->id,
		$name
	));
	if($db->fetch_row(true)){
		$opc = "background:rgba(51, 51, 51, .75);opacity:.5;";
		$vt = 'Yes!';
		$color = 'green';
	}else{
		$opc = "";
		$vt = 'No?';
		$color = 'red';
	}
	$db->query("SELECT COUNT(*) FROM votes WHERE site = ?");
	$db->execute(array(
		$name
	));
	$vts = $db->fetch_single();
	echo'<div class="floaty flexcont" style="margin:5px;width:75%;padding:0;' . $opc .'">';
		echo'<div class="flexele" style="border-right:thin solid #333;line-height:30px;">';
			echo'<a target="_new" href="?vote=' . $name . '">';
				echo'<span style="color:red;font-weight:bold;">' . $name . '</span>';
			echo'</a>';
		echo'</div>';
		echo'<div class="flexele" style="border-right:thin solid #333;line-height:30px;">';
			echo'<span style="color:' . $color . ';">Did you vote? ' . $vt . '</span>';
		echo'</div>';
		echo'<div class="flexele">';
			echo'<span style="color:#FFA500;">Votes Today: ' . $vts . '</span><br />';
		echo'</div>';
	echo'</div>';
	}
include 'footer.php';
