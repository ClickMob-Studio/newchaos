<?php
include 'header.php';

if (checkCaptchaRequired($user_class)) {
	header('Location: captcha.php?token=' . $user_class->macro_token . '&page=search');
}

if (isset($_GET['forced_captcha']) && $_GET['forced_captcha'] == 'yes') {
	mysql_query('UPDATE `grpgusers` SET `captcha_timestamp` = 0 WHERE `id` = ' . $user_class->id);

	header('Location: captcha.php?token=' . $user_class->macro_token . '&page=search');
}

?>
<div class='box_top'>Search Players</div>
<div class='box_middle'>
	<div class='pad'>
		<?php
		if (!isset($_GET['reset'])) {
			if (isset($_POST['newsearch'])) {
				$params = filter_input(INPUT_COOKIE, 'searching', FILTER_SANITIZE_STRING);
				$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
				perform_query("INSERT INTO searches (userid, params, name) VALUES(?, ?, ?)", [$user_class->id, $params, $name]);
			}
			if (empty($_POST['actsearch']) && isset($_COOKIE['searching'])) {
				$search = unserialize($_COOKIE['searching']);
				$_POST['id'] = 479995426;
			}
			if ($_POST['id'] != 479995426 && !empty($_POST['actsearch'])) {
				$search['id'] = abs((int) $_POST['id']);
				$search['name'] = $_POST['Name'];
				$search['money'] = abs((int) $_POST['money']);
				$search['level'] = abs((int) $_POST['level']);
				$search['level2'] = abs((int) $_POST['level2']);
				$search['lastactive'] = abs((int) $_POST['lastactive']);
				$search['lastactive2'] = abs((int) $_POST['lastactive2']);
				$search['attack'] = abs((int) $_POST['attack']);
				$search['location'] = abs((int) $_POST['location']);
				$search['gang'] = abs((int) $_POST['gang']);
				$search['online'] = abs((int) $_POST['online']);
			}
			if (isset($_POST['loadsearch'])) {
				$err = mysql_fetch_array(mysql_query("SELECT * FROM searches WHERE id = {$_POST['searchid']} AND userid = $user_class->id"));
				if (isset($err))
					$search = unserialize($err['params']);
			}
			if (isset($_POST['delsearch'])) {
				perform_query("DELETE FROM searches WHERE id = ? AND userid = ?", [$_POST['searchid'], $user_class->id]);
			}
		}


		echo "
<script>
	if(window.history.replaceState) {
	window.history.replaceState( null, null, window.location.href );

	}
	</script>

	<div class='flexcont'>
		<div class='flexele'>
			<form method='post' action='search.php'>
				<input type='hidden' name='actsearch' value='1' />
					<table id='table' style='margin:auto;'>
						<tr>
							<th>ID:</th>
			<div id='searchResults'></div>				<td><input type='text' name='id' value='{$search['id']}'></td>
						</tr>
						<tr>
    <th>Name:</th>
    <td><input type='text' id='nameSearch' name='Name' value='{$search['name']}'><div id='searchResults'></div></td>

</tr>						<tr>
							<th>Level:</th>
							<td>
								<input type='text' name='level' size='7' maxlength='10' value='{$search['level']}'> to
								<input type='text' name='level2' size='7' maxlength='10' value='{$search['level2']}'>
							</td>
						</tr>
						<tr>
							<th>Last Active:</th>
							<td>
								<input type='text' name='lastactive' size='4' maxlength='10' value='{$search['lastactive']}'> to
								<input type='text' name='lastactive2' size='4' maxlength='10' value='{$search['lastactive2']}'> days
							</td>
						</tr>
						<tr>
							<th>Money:</th>
							<td><input type='text' name='money' size='12' maxlength='16' value='{$search['money']}'> and more</td>
						</tr>
						<tr>
							<th>Location:</th>
							<td>
								<select name='location'>
									<option value='0'>Any</option>";
		$query_string = "SELECT id,name FROM cities ORDER BY name ASC";
		$result_id = mysql_query($query_string);
		while ($row = mysql_fetch_row($result_id)) {
			$select = ($search['location'] == $row[0]) ? ' selected' : '';
			echo "<option value='$row[0]'$select>$row[1]</option>";
		}
		echo "
								</select>
							</td>
						</tr>
						<tr>
							<th>Gang:</th>
							<td>
								<select name=gang>
									<option value=0>Any</option>";
		$query_string = "SELECT id,name FROM gangs ORDER BY name ASC";
		$result_id = mysql_query($query_string);
		while ($row = mysql_fetch_row($result_id)) {
			$select = ($search['gang'] == $row[0]) ? ' selected' : '';
			echo "<option value=$row[0]$select>$row[1]</option>";
		}
		$select = ($search['gang'] == 999999) ? ' selected' : '';
		echo "
									<option " . $select . " value=999999>No Gang</option>
								</select>
							</td>
						</tr>
						<tr>
							<th>Online:</th>
							<td>
								<select name='online'>";
		foreach (array(0 => 'any', 1 => 'Yes', 2 => 'No') as $num => $val) {
			$select = ($search['online'] == $num) ? ' selected' : '';
			print "<option value='$num'$select>$val</option>";
		}
		print "
								</select>
							</td>
						</tr>
						<tr>
							<th>Attackable:</th>
							<td>
								<select name=attack>";
		foreach (array(0 => 'any', 1 => 'Yes', 2 => 'No') as $num => $val) {
			$select = ($search['attack'] == $num) ? ' selected' : '';
			print "<option value='$num'$select>$val</option>";
		}
		print "
								</select>
							</td>
						</tr>
						<tr>
							<td colspan='2'><input type=submit name=search value=Search></td>
						</tr>
					</table>
				</form>
			</div>";
		?>
		<style>
			.flex-container {
				display: flex;
				flex-direction: row;
				justify-content: space-between;
				margin: 0 auto;
				padding: 20px;
			}

			@media (max-width: 768px) {
				.flex-container {
					flex-direction: column;
					align-items: center;
				}

				.flex-container form,
				.flex-container a button {
					margin-bottom: 20px;
					/* Adds spacing between forms on mobile */
				}
			}
		</style>

		<div class='flex-container'>
			<form method='post'>
				Load Search: <select name='searchid'>
					<?php
					$searches = mysql_query("SELECT * FROM searches WHERE userid = {$user_class->id}");
					while ($searchy = mysql_fetch_array($searches))
						echo "<option value='{$searchy['id']}'>{$searchy['name']}</option>";
					?>
				</select>
				<input type='hidden' name='loadsearch' value='doet' />
				<input type='submit' value='Load Search' />
			</form>

			<form method='post'>
				Delete Search: <select name='searchid'>
					<?php
					$searches = mysql_query("SELECT * FROM searches WHERE userid = {$user_class->id}");
					while ($searchy = mysql_fetch_array($searches))
						echo "<option value='{$searchy['id']}'>{$searchy['name']}</option>";
					?>
				</select>
				<input type='hidden' name='delsearch' value='doet' />
				<input type='submit' value='Delete Search' />
			</form>

			<form method='post'>
				Name: <input type='text' name='name' />
				<input value='Save Last Search' name='newsearch' type='submit' />
			</form>

			<a href='?reset'><button>Reset Search Form</button></a>
		</div>

		<?php
		if (isset($_POST['id'])) {
			$protime = time();
			setcookie("searching", serialize($search), time() + (86400 * 30), "/");
			$sql = "id != 0";
			if (!empty($search['id']) && $search['id'] != 0)
				$sql = " id = '{$search['id']}' ";
			if (!empty($search['name']))
				$sql .= " AND username like \"%{$search['name']}%\" ";
			if ($search['level'] > 0 || $search['level2'] > 0)
				$sql .= (!empty($sql)) ? " AND (`level` >= {$search['level']} AND `level` <= {$search['level2']}) " : " (`level` >= {$search['level']} AND `level` <= {$search['level2']}) ";
			if (!empty($search['lastactive']) && !empty($search['lastactive2'])) {
				$la = $search['lastactive'] * 86400;
				$la2 = $search['lastactive2'] * 86400;
				$sql .= (!empty($sql)) ? " AND (`lastactive` >= {$la} AND `lastactive` <= {$la2}) " : " (`lastactive` >= {$la} AND `lastactive` <= {$la2}) ";
			}
			if (!empty($search['location']) && $search['location'] != 0)
				$sql .= " AND city = '{$search['location']}' AND eqarmor <> 43";
			if (!empty($search['gang']) && $search['gang'] != 0 && $search['gang'] != 999999)
				$sql .= " AND gang = '{$search['gang']}' ";
			if (!empty($search['gang']) && $search['gang'] == 999999)
				$sql .= " AND gang = 0";
			if (!empty($search['money']))
				$sql .= " AND money > '{$search['money']}' ";
			if ($search['attack'] == 1)
				$sql .= " AND hospital = 0 AND jail = 0 AND aprotection < {$protime} AND (gang <> $user_class->gang || gang = 0)  AND admin < 1 AND hp > (50*level)/4 AND id <> $user_class->id ";
			else if ($search['attack'] == 2)
				$sql .= " AND hospital > 0";
			$time = time() - 900;
			if ($search['online'] == 1)
				$sql .= " AND lastactive > '{$time}' ";
			else if ($search['online'] == 2)
				$sql .= " AND lastactive < '{$time}' ";
			echo '
		<table id="newtables"  style="width:100%;">
			<tr>
				<th width="30%">Name</th>
				<th>Level</th>
				<th>Money</th>
				<th>Online</th>
				<th><a href="?spend=refenergy" style="color:orange;">[R Energy]</a></th>
				<th>&nbsp;</th>
				<th><a href="?spend=refnerve" style="color:orange;">[R Nerve]</a></th>
				<th>Hos</th>
			</tr>';
			if ($user_class->id == 174) {
				echo "<pre>";
				print_r($sql);
				echo "</pre>";
			}

			// Determine the limit based on user_class->rmdays
			$limit = ($user_class->rmdays > 0) ? 20 : 10;

			$query = mysql_query("SELECT * FROM `grpgusers` WHERE {$sql} ORDER BY rand() DESC LIMIT $limit");
			if (mysql_num_rows($query) == 0) {
				echo '<tr><th colspan="7">No-one matched your search.</th></tr>';
				echo '</table>';
			} else {
				$csrf = md5(uniqid(rand(), TRUE));
				$_SESSION['csrf'] = $csrf;
				while ($line = mysql_fetch_array($query)) {
					$userfound = new User($line['id']);
					echo "
        <tr>
            <td>$userfound->formattedname</td>
            <td>{$line['level']}</td>
            <td>" . prettynum($line['money'], 1) . "</td>
            <td>$userfound->formattedonline</td>";
					if ($userfound->id != 1 && $userfound->id != 2) {
						echo '<td><a class="btn btn-primary ajax-link" href="ajax_attack_v2.php?attack=' . $userfound->id . '&csrf=' . $csrf . '">Attack</a></td>';
						echo '<td><a class="btn btn-primary ajax-link" href="ajax_mug.php?mug=' . $userfound->id . '&token=' . $user_class->macro_token . '">Mug</a></td>';
					} else {
						echo "<td></td><td></td><td></td>";
					}
					echo "<td>" . (($userfound->hospital > 0) ? $userfound->hospital / 60 : '-') . "</td>
        </tr>";
				}
				echo "
</table>
</tr>
</td>";
			}
		}
		?>

		<form method="post" action="search.php" id="refreshForm">
			<input type='hidden' name='actsearch'
				value='<?php echo isset($_POST['actsearch']) ? $_POST['actsearch'] : ''; ?>' />
			<input type='hidden' name='id' value='<?php echo isset($search['id']) ? $search['id'] : ''; ?>' />
			<input type='hidden' name='Name' value='<?php echo isset($search['name']) ? $search['name'] : ''; ?>' />
			<input type='hidden' name='level' value='<?php echo isset($search['level']) ? $search['level'] : ''; ?>' />
			<input type='hidden' name='level2'
				value='<?php echo isset($search['level2']) ? $search['level2'] : ''; ?>' />
			<input type='hidden' name='lastactive'
				value='<?php echo isset($search['lastactive']) ? $search['lastactive'] : ''; ?>' />
			<input type='hidden' name='lastactive2'
				value='<?php echo isset($search['lastactive2']) ? $search['lastactive2'] : ''; ?>' />
			<input type='hidden' name='money' value='<?php echo isset($search['money']) ? $search['money'] : ''; ?>' />
			<input type='hidden' name='location'
				value='<?php echo isset($search['location']) ? $search['location'] : ''; ?>' />
			<input type='hidden' name='gang' value='<?php echo isset($search['gang']) ? $search['gang'] : ''; ?>' />
			<input type='hidden' name='online'
				value='<?php echo isset($search['online']) ? $search['online'] : ''; ?>' />
			<input type='hidden' name='attack'
				value='<?php echo isset($search['attack']) ? $search['attack'] : ''; ?>' />
			<input type='submit' value='Refresh Search' class='btn btn-primary' />
		</form>


		<script type="text/javascript">
			let clickCount = 0;

			document.addEventListener("DOMContentLoaded", function () {
				document.body.addEventListener('click', function (evt) {
					clickCount = clickCount + 1;
					if (clickCount > 1500) {
						window.location.href = "/search.php?forced_captcha=yes";
					}
				}, true);
			});

		</script>

		<?php
		echo '<script>$(".aha").on("click", function(e) {
	if (e.shiftKey) {
        e.preventDefault();
    }
	if (e.hasOwnProperty("originalEvent") && e.originalEvent.isTrusted) {
		var href = $(this).data("href");
		if (href) {
			location.href = href;
		}
	}
});</script>';

		include 'footer.php';

