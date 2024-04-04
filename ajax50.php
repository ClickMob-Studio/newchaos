<?php

include "ajax_header.php";


$IP = (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
$mins = array(
	'cash' => 10000,
	'points' => 100,
	'credits' => 10
);
$user_class = new user($_SESSION['id']);
if(isset($_POST['amnt'])){
	$amnt = security($_POST['amnt']);
	$curr = (in_array($_POST['curr'], array('cash', 'points', 'credits'))) ? $_POST['curr'] : die("error|fuck off");
	if($amnt < $mins[$curr])
		die("error|You must bet atleast " . (prettynum($mins[$curr], ($curr == 'cash' ? 1: 0))) . ".");
	$dbcol = dbcol($curr);
	if($amnt > $user_class->{$dbcol})
		die("error|You do not have enough on hand.");
	$db->query("UPDATE grpgusers SET $dbcol = $dbcol - ? WHERE id = ?");
	$db->execute(array(
		$amnt,
		$user_class->id
	));
	$db->query("INSERT INTO fiftyfifty (userid, amnt, currency, timestamp, betterip) VALUES (?, ?, ?, unix_timestamp(), ?)");
	$db->execute(array(
		$user_class->id,
		$amnt,
		$curr,
		$IP
	));
	$i = $db->insert_id();
	echo'success|' . $i . '|';
	echo'<div class="floaty" id="bet' . $i . '" style="margin:3px;">';
		echo formatName($user_class->id);
		echo'<hr style="border:0;border-bottom:thin solid #333;" />';
		echo prettynum($amnt, ($curr == 'cash' ? 1: 0));
		echo'<br />';
		echo'<button onclick="remove(' . $i . ');">Remove Bet</button>';
	echo'</div>';
}
if(isset($_POST['take'])){
	$take = security($_POST['take']);
	$db->query("SELECT * FROM fiftyfifty WHERE id = ?");
	$db->execute(array(
		$take
	));
	$row = $db->fetch_row(true);
	if(empty($row))
		die("error|This bet no longer exists.");
	$dbcol = dbcol($row['currency']);
	if($row['amnt'] > $user_class->$dbcol)
		die("error|You do not have enough funds to take this bet.");
	if($row['userid'] == $user_class->id)
		die("error|You cannot take your own bets.");

	$db->query("INSERT INTO fiftyfiftylogs (better, taker, winner, timestamp, amnt, betterip, matcherip) VALUES (?, ?, ?, unix_timestamp(), ?, ?, ?)");
	$random = mt_rand(1,2);
	if ($random === 1) {
		$db->execute(array(
			$row['userid'],
			$user_class->id,
			$user_class->id,
			$row['amnt'],
			$row['betterip'],
			$IP
		));
		$form = ($row['currency'] == 'cash') ? prettynum($row['amnt'], 1) : prettynum($row['amnt']) . ' ' . $row['currency'];
		$db->query("UPDATE grpgusers SET $dbcol = $dbcol + ? WHERE id = ?");
		$db->execute(array(
			$row['amnt'],
			$user_class->id
		));
		Send_Event($row['userid'], "Your bet of $form has been taken, and you lost.");
		$won = 1;
	} else {
		$db->execute(array(
			$row['userid'],
			$user_class->id,
			$row['userid'],
			$row['amnt'],
			$row['betterip'],
			$IP
		));
		$form = ($row['currency'] == 'cash') ? prettynum($row['amnt'], 1) : prettynum($row['amnt']) . ' ' . $row['currency'];
		$wins = ($row['currency'] == 'cash') ? prettynum($row['amnt'] * 2, 1) : prettynum($row['amnt'] * 2) . ' ' . $row['currency'];
		$db->query("UPDATE grpgusers SET $dbcol = $dbcol + ? WHERE id = ?");
		$db->execute(array(
			($row['amnt'] * 2),
			$row['userid']
		));
		$db->execute(array(
			-$row['amnt'],
			$user_class->id
		));
		Send_Event($row['userid'], "Your bet of $form has been taken, and you won $wins!");
		$won = 0;
	}
	$db->query("DELETE FROM fiftyfifty WHERE id = ?");
	$db->execute(array(
		$take
	));
	echo'take|', ($won == 1) ? 'success' : 'error' , '|You have ' , ($won == 1) ? 'won' : 'lost' , ' the bet for ' . $form;
}
if(isset($_POST['takeaway'])){
	$remove = security($_POST['takeaway']);
	$db->query("SELECT * FROM fiftyfifty WHERE id = ?");
	$db->execute(array(
		$remove
	));
	$row = $db->fetch_row(true);
	if($row['userid'] != $user_class->id)
		die("error|This bet is not owned by you.");
	$dbcol = dbcol($row['currency']);
	$db->query("UPDATE grpgusers SET $dbcol = $dbcol + ? WHERE id = ?");
	$db->execute(array(
		$row['amnt'],
		$user_class->id
	));
	$db->query("DELETE FROM fiftyfifty WHERE id = ?");
	$db->execute(array(
		$remove
	));
}
if(isset($_POST['update'])){
    $new = array(
        'cash' => '',
        'points' => '',
        'credits' => ''
    );
    $newids = $delids = array();
    $ids = $_POST['update'];
    $idsarr = explode(",", $ids); // Existing known IDs
    $db->query("SELECT * FROM fiftyfifty");
    $db->execute();
    $rows = $db->fetch_row();
    foreach($rows as $row){
        $newids[] = $row['id'];
        if(!in_array($row['id'], $idsarr)){
            // New bet added, append its HTML to the appropriate currency section
            $new[$row['currency']] .= '<div id="' . $curr . 'bets">';
                $new[$row['currency']] .= "<td>" . formatName($row['userid']);
                $new[$row['currency']] .= '<hr style="border:0;border-bottom:thin solid #333;" />';
                $new[$row['currency']] .= "<td>" . prettynum($row['amnt'], ($row['currency'] == 'cash' ? 1: 0))."</td>";
                $new[$row['currency']] .= '<br />';
                if($user_class->id == $row['userid'])
                    $new[$row['currency']] .= '<td><button onclick="takeaway(' . $row['id'] . ');">Remove Bet</button></td>';
                else
                    $new[$row['currency']] .= '<td><button onclick="take(' . $row['id'] . ');">Take Bet</button></td>';
            $new[$row['currency']] .= '</div>';
        
        }
    }
    foreach($idsarr as $idssub){
        if(!in_array($idssub, $newids)){
            // Bet has been removed, add its ID to the deletions list
            $delids[] = $idssub;
        }
    }
    echo $new['cash'] . '|' . $new['points'] . '|' . $new['credits'] . '|' . implode(",", $delids) . '|' . implode(",", $newids) . '|' . prettynum($user_class->money) . '|' . prettynum($user_class->points) . '|' . prettynum($user_class->credits);
}

function dbcol($input){
	return str_replace('cash', 'money', $input);
}
?>