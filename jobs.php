<?php
include 'header.php';
?>
	
	<div class='box_top'>Jobs</div>
						<div class='box_middle'>
							<div class='pad'>
								<?php
if ($user_class->fbitime > 0) {
    diefun("You can't do work if you're in FBI Jail!");
}


$quer = mysql_query("SELECT * FROM jobinfo WHERE userid = ". $user_class->id);

if(mysql_num_rows($quer) < 1){
    mysql_query("INSERT INTO jobinfo VALUES (userid, total, points) VALUES (".$user_class->id.", 0, 0, 0)");
   
    $jobinfo['userid'] = $user_class->id;
    $jobinfo['dailyClockins'] = $jobinfo['lastClockin'] = $jobinfo['addedPercent'] = 0;
} else
    $jobinfo = mysql_fetch_assoc($quer);
if(isset($_GET['clockin'])){
    if($jobinfo['lastClockin'] < time() - 3600)
        diefun("You have already clocked in less than an hour ago.");
    if($user_class->dailyClockins >= 8)
        diefun("You have already clocked in 8 times today.");
    $jobinfo['lastClockin'] = time();
    $jobinfo['dailyClockins']++;
    $db->query("UPDATE jobinfo SET lastClockin = ?, dailyClockins = ? WHERE userid = ?");
    $db->execute(array(
        $jobinfo['lastClockin'],
        $jobinfo['dailyClockins'],
        $user_class->id
    ));
    $db->query("SELECT money FROM jobs WHERE id = ?");
    $db->execute(array(
        $user_class->job
    ));
    $pay = $db->fetch_single();
    $pay *= (1 + ($jobinfo['addedPercent'] / 100));
    $db->query("SELECT points FROM jobs WHERE id = ?");
    $db->execute(array(
        $user_class->job
    ));
    $pay2 = $db->fetch_single();
    $pay2 = $pay2;

    $user_class->money += $pay;

    $user_class->points += $pay2;
    $db->query("UPDATE grpgusers SET money = ?, points = ?, dailyClockins = dailyClockins + 1, jobcis = jobcis + 1, jobMoney = jobMoney + ?, raidtokens =raidtokens +2  WHERE id = ?");
    $db->execute(array(
        $user_class->money,
  $user_class->points,
        $pay,
        $user_class->id
    ));
    $m->delete('clockin.'.$user_class->id);
}
if (isset($_GET['action']) AND $_GET['action'] == "quit") {
    $user_class->job = 0;
    $db->query("UPDATE grpgusers SET job = ? WHERE id = ?");
    $db->execute(array(
        $user_class->job,
        $user_class->id
    ));
    $db->query("UPDATE jobinfo SET  dailyClockins = 0 WHERE userid = ?");
    $db->execute(array(
        $user_class->id
    ));
}
if (isset($_GET['take'])) {
    $db->query("SELECT * FROM jobs WHERE id = ?");
    $db->execute(array(
        $_GET['take']
    ));
    if(!$db->num_rows())
        diefun("This job is not available.");
    $row = $db->fetch_row(true);
    if (($row['level'] > $user_class->level) || ($row['total'] > $user_class->total) || ($row['prestige'] > $user_class->prestige))
        diefun("You don't have the needed skills or level to take this job.<br />");
    $user_class->job = $_GET['take'];
    $db->query("UPDATE grpgusers SET job = ? WHERE id = ?");
    $db->execute(array(
        $user_class->job,
        $user_class->id
    ));
    $db->query("UPDATE jobinfo SET lastClockin = 0, dailyClockins = 0 WHERE userid = ?");
    $db->execute(array(
        $user_class->id
    ));
}
if ($user_class->job != 0) {
    $db->query("SELECT * FROM jobs WHERE id = ?");
    $db->execute(array(
        $user_class->job
    ));
    $row = $db->fetch_row(true);
	echo'<div style="background:rgba(0,0,0,.25);width:50%;margin:0 auto;text-align:center;padding: 15px;">';
		echo'You are currently a ' . $row['name'] . '<br />';
		echo'You make <span style="color:green;">' . prettynum($row['money'] * (1 + ($jobinfo['addedPercent'] / 100)), 1) . ' </span> &  ' . prettynum($row['points']) . ' Points Per Hour<br />';
		echo'<br />';
		echo'You last clocked in <span style="color:red">' , ($jobinfo['lastClockin'] == 0) ? 'never' : date('h:i:s a', $jobinfo['lastClockin']) , '</span>.<br />';
		echo'You clocked in <span style="color:red">' . $user_class->dailyClockins . '</span> time' , ($user_class->dailyClockins == 1) ? '' : 's' , ' today.<br />';
		echo'<br />';
		echo'<a href="jobs.php?clockin"><button>Clockin</button></a> <a href="jobs.php?action=quit"><button>Quit Job</button></a>';
	echo'</div>';
}

echo'<div class="floaty">';

	echo'<table id="newtables" style="width:97%;table-layout:fixed; text-align:center;">';
        echo'<tr>';
           
            echo'<h4>Hourly Payment</h4>';

        echo'<tr>';
                    echo'<th>Job Desc</th>';

            echo'<th>Level</th>';

            echo'<th>Total Stats</th>';
  echo'<th>Cash</th>';
  echo'<th>Points</th>';
    echo'<th>Raid Tokens</th>';
        echo'<th>Actions</th>';


        echo'</tr>';
	$db->query("SELECT * FROM jobs ORDER BY money ASC");
	$db->execute();
	$rows = $db->fetch_row();
	foreach($rows as $row){
			echo'<tr>';
				echo'<td>' . $row['name'] . '</td>';
				echo'<td>' . prettynum($row['level']) . '</td>';

				echo'<td>' . prettynum($row['total']) . '</td>';
				echo'<td>' . prettynum($row['money'],1) . '</td>';
				echo'<td>' . prettynum($row['points']) . '</td>';
				echo'<td>' . prettynum($row['raidtoken']) . '</td>';



				echo'<td>' , ($row['id'] > $user_class->job) ? '<a href="jobs.php?take=' . $row['id'] . '">Take Job</a>' : '' , '</td>';
			echo'</tr>';
	}
	echo'</table>';
echo'</div>';
include 'footer.php';
?>