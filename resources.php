<?php
$min_resources_given = 10;
$max_resources_given = 20;
$max_clockins_per_day = 4;
$clockin_every_x_seconds = 3600;	// 3600 seconds = 1 hour
include 'header.php';
if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
	$resource = get_resource_by_id($_GET['id']);
	if ($resource !== false) {
		$amnt = rand($min_resources_given, $max_resources_given);
		$rtn = add_resource($_GET['id'], $amnt);
		if ($rtn === true) {
			echo '<div class="success">You gained ' . number_format($amnt) . ' ' . $resource . '.</div>';
		} else {
			echo '<div class="error">You cannot gather anymore ' . $resource . ' from this plot for another ' . $rtn . '.</div>';
		}
	}
}
$db->query("SELECT * FROM user_resources WHERE userid = ?");
$db->execute(array(
	$user_class->id
));
$resources = $db->fetch_row(true);
if (!$resources) {
	$db->query("INSERT INTO user_resources (userid) VALUES (?)");
	$db->execute(array(
		$user_class->id
	));
	$db->query("INSERT INTO user_resource_plots (userid, resource, level, last_action, today_actions) VALUES (?, ?, 1, 0, 0)");
	$db->execute(array(
		$user_class->id,
		'stone'
	));
	$db->execute(array(
		$user_class->id,
		'wood'
	));
	$db->execute(array(
		$user_class->id,
		'iron'
	));
	$db->execute(array(
		$user_class->id,
		'food'
	));
	$db->query("INSERT INTO user_buildings (userid, building, level) VALUES (?, 'Town Hall', 1)");
	$db->execute(array(
		$user_class->id
	));
	$resources['food'] = 0;
	$resources['stone'] = 0;
	$resources['wood'] = 0;
	$resources['iron'] = 0;
	$resources['plots'] = 4;
}
$db->query("SELECT * FROM user_buildings WHERE userid = ? AND building = 'Town Hall'");
$db->execute(array(
	$user_class->id
));
$town_hall = $db->fetch_row(true);
echo '<div class="flexcont">';
echo '<div class="flexele floaty flexcont" style="margin:5px;">';
echo '<div class="flexele">';
echo '<img src="/images/townhall.png" style="width:100px;" />';
echo '</div>';
echo '<div class="flexele">';
echo 'Town Hall<br />';
echo 'Level: ' . $town_hall['level'] . '<br />';
echo '</div>';
echo '</div>';
echo '<div class="flexele" style="text-align:left;padding:5px;">';
echo 'Stone: ' . number_format($resources['stone']) . '<br />';
echo 'Wood: ' . number_format($resources['wood']) . '<br />';
echo 'Iron: ' . number_format($resources['iron']) . '<br />';
echo 'Food: ' . number_format($resources['food']) . '<br />';
echo '</div>';
echo '</div>';
echo '<div class="flexcont">';
$db->query("SELECT * FROM user_resource_plots WHERE userid = ? ORDER BY id ASC");
$db->execute(array(
	$user_class->id
));
$rows = $db->fetch_row();
foreach ($rows as $row) {
	echo '<div class="resource-plot">';
	echo '<div class="floaty" style="margin:5px;">';
	echo show_resource_button($row['id']);
	echo '</div>';
	echo '</div>';
}
echo '</div>';
include 'footer.php';
function check_resource_timer($resource)
{
	global $user_class, $db, $max_clockins_per_day, $clockin_every_x_seconds;
	$db->query("SELECT timestamp, today FROM user_resource_clockins WHERE userid = ? AND resource = ?");
	$db->execute(array(
		$user_class->id,
		$resource
	));
	$row = $db->fetch_row(true);
	if ($row) {
		if ($row['today'] >= $max_clockins_per_day) {
			return howlongtil(strtotime('tomorrow midnight'));
		} elseif ($row['timestamp'] > time() - $clockin_every_x_seconds) {
			return howlongtil($row['timestamp'] + $clockin_every_x_seconds);
		} else {
			return show_resource_button($resource);
		}
	} else {
		return show_resource_button($resource);
	}
}
function show_resource_button($id)
{
	$resource = get_resource_by_id($id);
	return '<a href="?id=' . $id . '"><button>' . resource_action($resource) . ' ' . ucfirst($resource) . '</button></a>';
}
function resource_action($resource)
{
	switch ($resource) {
		case 'stone':
		case 'iron':
			return 'Mine';
		case 'wood':
			return 'Chop';
		case 'food':
			return 'Harvest';
	}
}
function add_resource($id, $amnt)
{
	global $user_class, $db, $max_clockins_per_day, $clockin_every_x_seconds;
	$resource = get_resource_by_id($id);
	$db->query("SELECT * FROM user_resource_plots WHERE userid = ? AND id = ?");
	$db->execute(array(
		$user_class->id,
		$id
	));
	$row = $db->fetch_row(true);
	if ($row) {
		if ($row['today_actions'] >= $max_clockins_per_day) {
			return howlongtil(strtotime('tomorrow midnight'));
		} elseif ($row['last_action'] > time() - $clockin_every_x_seconds) {
			return howlongtil($row['last_action'] + $clockin_every_x_seconds);
		}
	}
	$db->query("UPDATE user_resources SET $resource = $resource + ? WHERE userid = ?");
	$db->execute(array(
		$amnt,
		$user_class->id
	));
	$db->query("UPDATE user_resource_plots SET last_action = unix_timestamp(), today_actions = today_actions + 1 WHERE userid = ? AND id = ?");
	$db->execute(array(
		$user_class->id,
		$id
	));
	return true;
}
function get_resource_by_id($id)
{
	global $db, $user_class;

	$db->query("SELECT resource FROM user_resource_plots WHERE id = ? AND userid = ?");
	$db->execute(array(
		$id,
		$user_class->id
	));
	$row = $db->fetch_row(true);
	return $row['resource'];
}