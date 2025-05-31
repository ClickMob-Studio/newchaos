<?php
include 'header.php';
?>
<div class='box_top'>Change Leader</div>
<div class='box_middle'>
	<div class='pad'>
		<?php
		$gang_class = new Gang($user_class->gang);
		if ($user_class->gang == 0) {
			echo Message("You aren't in a gang.");
			include 'footer.php';
			die();
		}

		if ($gang_class->leader != $user_class->id) {
			echo Message("You don't have permission to be here!");
			include 'footer.php';
			die();
		}

		if (isset($_POST['submit'])) {
			$new_leader = new User($_POST['leader']);
			perform_query("UPDATE `gangs` SET `leader` = ? WHERE `id` = ?", [$new_leader->id, $gang_class->id]);

			if ($_POST['leader'] != $gang_class->leader) {
				perform_query("UPDATE `grpgusers` SET `grank` = '0', `gangleader` = '0' WHERE `id` = ?", [$gang_class->leader]);
				perform_query("UPDATE `grpgusers` SET `grank` = '1', `gangleader` = '1' WHERE `id` = ?", [$new_leader->id]);
			}
		}

		$leader = "<td width='15%'><b>Leader:</b></td><td width='35%'><select name='leader'>";

		$db->query("SELECT * FROM `grpgusers` WHERE `gang` = ? ORDER BY `gangleader` DESC");
		$db->execute([$gang_class->id]);
		$gangs = $db->fetch_row();
		foreach ($gangs as $gi) {
			$leader .= "<option value='" . $gi['id'] . "'> " . $gi['username'] . " </option>";
		}
		$leader .= "</select></td>";
		?>
		<tr>
			<td class="contentcontent">
				<table width="50%" border="0" style="margin:0 auto;">
					<form method='post'>
						<tr>
							<?php echo $leader; ?>
						</tr>
						<tr>
							<td width='15%'></td>
							<td width='35%'><input type='submit' name='submit' value='Change Leader' /></td>
						</tr>
					</form>
				</table>
			</td>
		</tr>

		<?php
		include("gangheaders.php");
		include 'footer.php';
		?>