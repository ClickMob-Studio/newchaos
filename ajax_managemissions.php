<?php
include "ajax_header.php";
$user_class = new User($_SESSION['id']);
if ($user_class->admin != 1 && $user_class->id != 9 && $user_class->id != 21)
	die("Error detected.");
$oo = $po = '';
$oo .= '<select id="oo">';
$oo .= '<option value="kills">Kills</option>';
$oo .= '<option value="busts">Busts</option>';
$oo .= '<option value="mugs">Mugs</option>';
$oo .= '<option value="crimes1">1+ Nerve Crimes</option>';
$oo .= '<option value="crimes5">5+ Nerve Crimes</option>';
$oo .= '<option value="crimes10">10+ Nerve Crimes</option>';
$oo .= '<option value="crimes25">25+ Nerve Crimes</option>';
$oo .= '<option value="crimes50">50+ Nerve Crimes</option>';
$oo .= '</select>';
$po .= '<select id="po">';
$po .= '<option value="money">Money</option>';
$po .= '<option value="points">Points</option>';
$po .= '<option value="exp">EXP</option>';
$po .= '</select>';
if (isset($_POST['addmissionform'])) {
	echo '<div class="floaty" style="margin:2px;">';
	echo '<ul>';
	echo '<li>Mission Name :: Name the players will see when they start the mission.</li>';
	echo '<li>Mission Type :: Missions are grouped by their mission type, so missions of the same type will display only once on the missions page. The lowest mission that the player has yet to complete will be dispalyed for that mission type!</li>';
	echo '</ul>';
	echo '<table id="newtables">';
	echo '<tr>';
	echo '<td>Mission Name</td>';
	echo '<td colspan="2"><input type="text" id="missionname" /></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td>Mission Type</td>';
	echo '<td colspan="2"><input type="text" id="missiontype" /></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td rowspan="4">';
	echo 'Add Objective<br />';
	echo '<br />';
	echo '<button onclick="addobjective();">Add Objective</button>';
	echo '</td>';
	echo '<td>What to do?</td>';
	echo '<td>' . $oo . '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td>How many to do?</td>';
	echo '<td><input type="text" id="howmanytodo" /></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td>Payout Currency?</td>';
	echo '<td>' . $po . '</td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td>Payout?</td>';
	echo '<td><input type="text" id="missionpayout" /> <button onclick="suggest();">Suggest</button></td>';
	echo '</tr>';
	echo '<tr>';
	echo '<td><button onclick="addmissionfinal();">Add Mission</button></td>';
	echo '<td colspan="2"></td>';
	echo '</tr>';
	echo '</table>';
	echo '</div>';
	echo '<div class="floaty" style="margin:2px;text-align:left;">Objectives DB Input :: <span id="dbinput"></span></div>';
}
if (isset($_POST['addobjective'])) {
	$payout = security($_POST['missionpayout']);
	$howmany = security($_POST['howmanytodo']);
	$oo = $_POST['oo'];
	$po = $_POST['po'];
	if (empty($payout) || empty($howmany) || empty($oo) || empty($po))
		die();
	echo $oo . '|' . $howmany . '|' . $payout . '|' . $po . ';';
}
if (isset($_POST['addmissionfinal'])) {
	$name = $_POST['name'];
	$type = $_POST['type'];
	$obj = rtrim($_POST['objective'], ';');
	if (empty($name) || empty($type) || empty($obj))
		die('error|<div class="floaty" style="margin:2px;background:rgba(128,0,0,.25);">Cannot leave fields empty!</div>');
	$db->query("SELECT MAX(id) FROM newmissions WHERE type = ?");
	$db->execute(array(
		$type
	));
	$unlock = $db->fetch_single();
	if (empty($unlock))
		$unlock = 0;
	$db->query("INSERT INTO newmissions VALUES ('', ?, ?, ?, ?)");
	$db->execute(array(
		$obj,
		$type,
		$name,
		$unlock
	));
	echo 'success|<div class="floaty" style="margin:2px;background:rgba(0,128,0,.25);">Mission Added to the Game!</div>';
}
if (isset($_POST['suggest'])) {
	$obj = $_POST['obj'];
	$howmany = $_POST['howmany'];
	$whatcur = $_POST['whatcur'];
	switch ($obj) {
		case 'kills':
			$priceper = 2.5;
			break;
		case 'crimes1':
			$priceper = .1;
			break;
		case 'crimes5':
			$priceper = .5;
			break;
		case 'crimes10':
			$priceper = 1;
			break;
		case 'crimes25':
			$priceper = 2.5;
			break;
		case 'crimes50':
			$priceper = 5;
			break;
		case 'mugs':
			$priceper = 1;
			break;
		case 'busts':
			$priceper = 1;
			break;
	}
	switch ($whatcur) {
		case 'points':
			$mul = 1;
			break;
		case 'money':
			$mul = rand(2500, 2750);
			break;
		case 'exp':
			$mul = 600;
			break;
	}
	$percent = 1 + (rand(5000, 15000) / 100000);
	echo prettynum(ceil($percent * $mul * $priceper * $howmany));
}
if (isset($_POST['editmissionform'])) {

}
if (isset($_POST['deletemissionform'])) {

}
?>