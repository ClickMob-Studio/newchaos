<?php
exit;
include 'header.php';
$opsach = array(
	10 => 1000,
	25 => 1500,
	50 => 2000,
	75 => 2500,
	100 => 3000,
	125 => 3500
);
echo'<div class="floaty" style="margin:2px;">';
	echo'<span style="color:red;font-weight:bold;">Operations</span>';
if (isset($_GET['finish'])) {
    $db->query("SELECT * FROM missions_in_progress WHERE userid = ? AND status = 'inprogress'");
	$db->execute(array(
		$user_class->id
	));
	$f = $db->fetch_row(true);
    if (!empty($f)) {
        $reqs = explode(";", $f['requirements']);
        $done = explode("|", $f['done']);
        $co = 0;
        $finished = true;
        foreach ($reqs as $req) {
            $the = explode("|", $req);
            if ($done[$co] < $the[1])
                $finished = false;
            $co++;
        }
        if ($finished) {
            $db->query("UPDATE missions_in_progress SET status = 'completed' WHERE userid = ? AND status = 'inprogress'");
			$db->execute(array(
				$user_class->id
			));
			$db->query("INSERT INTO statistics (operations, userid) VALUES (1,?) ON DUPLICATE KEY UPDATE operations=operations+1");
			$db->execute(array(
				$user_class->id
			));
        }
    }
}
if (isset($_GET['start'])) {
    $start = security($_GET['start']);
    $db->query("SELECT * FROM missions_in_progress WHERE userid = ? AND (status = 'inprogress' OR mid = ?)");
	$db->execute(array(
		$user_class->id,
		$start
	));
    $r = $db->fetch_row(true);
    if (!empty($r) && $r['mid'] == $start)
        diefun("You already did this mission.");
    if (!empty($r) && $r['mid'] != $start)
        diefun("You are already doing a mission.");
    $db->query("SELECT * FROM newmissions WHERE id = ?");
	$db->execute(array(
		$start
	));
    $r = $db->fetch_row(true);
    if($r['tounlock'] > 0){
        $db->query("SELECT * FROM missions_in_progress WHERE userid = ? AND mid = ?");
		$db->execute(array(
			$user_class->id,
			$r['tounlock']
		));
        if(!$db->fetch_row(true))
            diefun("You are going out of order.");
    }
    $db->query("INSERT INTO missions_in_progress (mid, userid, requirements, timestamp) VALUES(?, ?, ?, unix_timestamp())");
	$db->execute(array(
		$start,
		$user_class->id,
		$r['requirements']
	));
    header("Location: operations.php");
}
$db->query("SELECT * FROM missions_in_progress WHERE userid = ? AND status = 'inprogress'");
$db->execute(array(
	$user_class->id
));
$f = $db->fetch_row(true);
if (!empty($f)) {
	echo'<hr style="border:0;border-bottom:thin solid #333;" />';
	echo'<table id="newtables" style="width:98%;table-layout:fixed">';
        echo'<tr>';
            echo'<th>What to do?</th>';
            echo'<th>Done so far</th>';
            echo'<th>How many Required</th>';
            echo'<th>Payout</th>';
        echo'</tr>';
    $reqs = explode(";", $f['requirements']);
    $done = explode("|", $f['done']);
    $finished = true;
    foreach ($reqs as $index => $req) {
        $the = explode("|", $req);
        $pay = ($the[3] == 'money') ? prettynum($the[2], 1) : number_format($the[2]) . " " . $the[3];
        if ($done[$index] < $the[1])
            $finished = false;
        echo"
        <tr>
            <th>" . missiontype($the[0]) . "</th>
            <td>", ($done[$index] > $the[1]) ? number_format($the[1]) : number_format($done[$index]), "</td>
            <td>" . number_format($the[1]) . "</td>
            <td>$pay</td>
        </tr>";
    }
    if ($finished) {
        print"
            <tr>
                <th colspan='4'><a href='?finish'>Finish this Mission!</a></th>
            </tr>";
    }
	echo'</table>';
echo'</div>';
} else {
    $db->query("SELECT * FROM missions_in_progress WHERE userid = ? AND status = 'inprogress'");
	$db->execute(array(
		$user_class->id
	));
    $check = $db->fetch_row(true);
    $db->query("SELECT *, (SELECT status FROM missions_in_progress m WHERE m.mid = n.id AND userid = ?) AS status FROM newmissions n");
	$db->execute(array(
		$user_class->id
	));
    $lasttype = "";
	$rows = $db->fetch_row();
	foreach($rows as $r){
        if ($r['type'] == $lasttype)
            continue;
        if ($r['status'] == 'completed')
            continue;
        $lasttype = $r['type'];
        $r['requirements'] = explode(";", $r['requirements']);
        foreach ($r['requirements'] as $req) {
            $req = explode("|", $req);
            switch ($req[3]) {
                case 'points':
                    $pay = number_format($req[2]) . " Points";
                    break;
                case 'exp':
                    $pay = number_format($req[2]) . " EXP";
                    break;
                case 'money':
                    $pay = prettynum($req[2], 1);
                    break;
            }
			$reqs['obj'] = missiontype($req[0]) . ' [x' . number_format($req[1]) . ']';
			$reqs['pay'] = '<span style="color:green;">[+' . $pay . ']</span>';
        }
		//echo'</div>';
		echo'<div class="floaty flexcont" style="width:95%;margin:2px;">';
			echo'<div class="flexele" style="border-right:thin solid #333;">';
				echo ucfirst($r['name']);
			echo'</div>';
			echo'<div class="flexele" style="border-right:thin solid #333;">';
				echo $reqs['obj'];
			echo'</div>';
			echo'<div class="flexele" style="border-right:thin solid #333;">';
				echo $reqs['pay'];
			echo'</div>';
			echo'<div class="flexele">';
				echo '<a href="?start=' . $r['id'] . '">[Start]</a>';
			echo'</div>';
		echo'</div>';
    }
//	$db->query("SELECT operations FROM statistics WHERE userid = ?");
//	$db->execute(array(
//		$user_class->id
//	));
//	$ops = $db->fetch_single();
//	echo'<div class="floaty">';
//		echo'<span style="color:red;font-weight:bold;">Operation Achievement Progress</span>';
//		echo'<hr style="border:0;border-bottom:thin solid #333;" />';
//		echo'<table id="newtables" style="table-layout:fixed;width:100%;">';
//		$cur = 0;
//		foreach($opsach as $req => $pay){
//			if($ops >= $req || $cur)
//				$opc = ' style="opacity:.33;"';
//			else{
//				$opc = '';
//				$cur = 1;
//			}
//			echo'<tr' . $opc . '>';
//				echo'<th>Complete ' . $req . ' Operations</th>';
//				echo'<td>' . min($ops, $req) . ' / ' . $req . '</td>';
//				echo'<td>' . progbar(($ops / $req) * 100) . '</td>';
//				echo'<td>' . prettynum($pay) . ' Points</td>';
//			echo'</tr>';
//		}
//		echo'</table>';
//	echo'</div>';
}
include"footer.php";
function progbar($perc){
	$perc = min($perc, 100);
	$rtn = '';
	$rtn = '<div class="progress-bar blue stripes" style="height:22px;width:99%;">';
		$rtn .= '<span style="width:' . $perc . '%;text-align:left;height:22px;line-height:22px;text-indent:5px;background:rgba(128,0,0,.75);">';
			$rtn .= floor($perc) . '%';
		$rtn .= '</span>';
	$rtn .= '</div>';
	return $rtn;
}
?>