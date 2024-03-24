<?php
include 'header.php';
if (CheckCourse($user_class->id)== 0) {
    if (isset($_GET['start'])) {
        $start = security($_GET['start']);
        $db->query("SELECT * FROM courses WHERE id = ?");
        $db->execute(array(
            $start
        ));
        $core = $db->fetch_row(true);
        if (!empty($core)) {
            if ($user_class->gcses < $core['needed'])
                diefun("You don't have the required diplomas for this course.");
            elseif ($user_class->money < $core['cost'])
                diefun("You don't have enough money to take this course.");
            else {
                $finish = time() + $core['duration'] * 86400;
                $user_class->money -= $core['cost'];
                $db->query("INSERT INTO uni (playerid, courseid, finish) VALUES (?, ?, ?)");
                $db->execute(array(
                    $user_class->id,
                    $start,
                    $finish
                ));
                $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
                $db->execute(array(
                    $user_class->money,
                    $user_class->id
                ));
                echo Message("You have successfully started a course. It will finish in {$core['duration']} days.");
            }
        } else
            echo Message("Thats not a real course.");
    }
echo'<h3>TML Server Bonuses</h3>';
		echo'<hr>';
	echo'<div class="floaty">';









		
		echo'&bull; Welcome to the TML Bonus Pots! <br />';
		echo'&bull;Here you can choose to contribute towards a server wide bonus! <br />';
		echo'&bull;Once the Target is hit for the bonus, 1 Hour will be added to that bonus time.<br />';
		echo'<br />';
		echo'<hr style="border:0;border-bottom:thin solid #333;" />';
		echo'<table id="newtables" class="altcolors" style="width:100%;">';
			echo'<tr>';
				echo'<th>Title</th>';
				echo'<th>Description</th>';
				echo'<th>Target</th>';
				echo'<th>Current</th>';
				echo'<th>Time</th>';
				echo'<th>Amount</th>';
							echo'</tr>';
			$db->query("SELECT * FROM gamebonus ORDER BY id ASC");
			$db->execute();
			$rows = $db->fetch_row();
			foreach($rows as $row){
			echo'<tr>';
				echo'<td>' . $row['Title'] . '</td>';
				echo'<td>' . $row['Description'] . '</td>';
				echo'<td>$' . prettynum ($row['Target']) . '</td>';
				echo'<td> $' . prettynum ($row['Current']) . ' </td>';
				echo'<td>' . $row['Time'] . '</td>';
				echo'<td>                    <form method="post">    <input type="text" name="damount" value="5000" size="10" maxlength="20">    <input type="submit" name="deposit" value="Donate Money">
                   </form>

</td>';
						echo'</tr>';
			}
		echo'</table>';
	echo'</div>';
} else {
    $db->query("SELECT * FROM uni WHERE playerid = ?");
    $db->execute(array(
        $user_class->id
    ));
    $uni = $db->fetch_row(true);
    $db->query("SELECT * FROM courses WHERE id = ?");
    $db->execute(array(
        $uni['courseid']
    ));
    $core = $db->fetch_row(true);
	echo'<div class="floaty">';
		echo'<span style="color:red;font-weight:bold;">MeanStreets University</span>';
		echo'<hr style="border:0;border-bottom:thin solid #333;" />';
		echo'Welcome to the Mean Streets University! ';
		echo'Here you can choose courses to take over a period of time to gain Strength, Speed, Defense and Diplomas. ';
		echo'Each course will require a certain number of diplomas and will cost some money.<br />';
		echo'<br />';
		echo'<b>You are currently taking a course in the university.</b><br />';
		echo'<hr style="border:0;border-bottom:thin solid #333;" />';
		echo'<table id="newtables" class="altcolors" style="width:90%;margin:auto;">';
			echo'<tr>';
				echo'<td colspan="2"><b><a href="completeuni.php">Please click here to complete your course if you have finished</a>';
			echo'</tr>';
			echo'<tr>';
				echo'<td><b>Course:</th>';
				echo'<td>' . $core['name'] . '</td>';
			echo'</tr>';
			echo'<tr>';
				echo'<td><b>Time Left:</th>';
				echo'<td>' . howlongtil($uni['finish']) . '</td>';
			echo'</tr>';
		echo'</table>';
	echo'</div>';
}
include 'footer.php';
?>