<?php


require_once("header.php");

class _poll {

public function _back() { echo '
<a href="'.$_SERVER['PHP_SELF'].'"]Back</a>'; exit($this->endpage());  }

public function _fetchUser($user) {
    global $db;
    $db->query("SELEcT * FROM grpgusers WHERE id = ?");
    $db->execute(array(
        $user
    ));
    return $db->fetch_row(true);
}

public function _fetchPoll() {
    global $db;
    $db->query("SELEcT * FROM `poll`");
    return $db->fetch_row(true);
}


public function _index() {

$user = $this->_fetchUser($_SESSION['id']);
$poll = $this->_fetchPoll();

if ($user['admin'] == 1) { 
	echo "<a href='?x=admin'>[Admin Panel]</a>";
}
if (empty($poll['ID'])) { echo 'No poll running atm.'; $this->_back(); } 

if (time() > $poll['end'] && $user['admin'] != 1) { echo 'The Poll has ended!'; }
else {
		$ended = (time() > $poll['end']) ? '<font color="red">The Poll has Ended</font>' : '';
        echo Message("You can only vote for one option on the poll");
		echo sprintf('%s 

		<table width="300px">
		<tr><td>
		<h1>Question: %s </h1></td></tr>
		<tr><td>
		%s </td><td> %u </td><td> <a href="?x=vote&ID=1">[Vote]</a></td></tr>
		<tr><td>
		%s </td><td> %s </td><td> <a href="?x=vote&ID=2">[Vote]</a></td></tr>
		<tr><td>
		Ends: %s</td></tr>
		</table>', 
		$ended,
		$poll['question'], 
		$poll['1'], $poll['1_r'], 
		$poll['2'], $poll['2_r'] * 5, 
		date('Y-m-d', $poll['end']));
	}
}


public function _vote() {
global $db;
$poll = $this->_fetchPoll();
if (time() > $poll['end']) { echo 'Poll has ended!'; $this->_back(); }
$db->query("SELECT `id` FROM `poll_votes` WHERE `userid`= ? ");
$db->execute(array(
    $_SESSION['id']
));
$check = $db->fetch_row(true);

if (!empty($check['id'])) { echo 'You have already voted!'; $this->_back(); }

$ID = ($_GET['ID'] < 1 || $_GET['ID'] > 4) ? 0 : $_GET['ID'].'_r';

$SQL = sprintf("UPDATE `poll` SET `%s` = `%s` + 1", $ID, $ID);
mysql_query($SQL);

$SQL_insert = sprintf("INSERT INTO `poll_votes` (`ID`, `userid`, `option`) VALUES
(NULL, %u, %u)", $_SESSION['id'], $ID);
mysql_query($SQL_insert);

echo sprintf('You have successfully voted for option %u !', $ID);

$this->_back();
}

public function _admin() {

if (isset($_POST['submit'])) {

$check = $this->_fetchPoll('ID');
if (!empty($check['ID'])) { echo 'Error - A poll already exists!'; $this->_back(); }

$end_timestamp = time() + ((preg_match('[^0-9]', $_POST['end'])) ? 1 : $_POST['end'] * 24 * 60 * 60);

$SQL = sprintf("INSERT INTO `poll` (`ID`, `question`, `1`, `2`, `3`, `4`, `1_r`, `2_r`, `3_r`, `4_r`, `end`) VALUES
(NULL, '%s', '%s', '%s', '%s', '%s', 0, 0, 0, 0, %u)",
mysql_real_escape_string($_POST['question']),
mysql_real_escape_string($_POST['1']), mysql_real_escape_string($_POST['2']),
mysql_real_escape_string($_POST['3']), mysql_real_escape_string($_POST['4']),
$end_timestamp);
mysql_query($SQL) OR die('Error Inserting Poll');

echo 'Poll Created Successfully';
$this->_back();

}

elseif (isset($_GET['del']) && $_GET['del'] == 1) {

mysql_query("TRUNCATE TABLE `poll`"); 
mysql_query("TRUNCATE TABLE `poll_votes`"); 
echo '
Poll Deleted';
$this->_back();
}
else {

echo sprintf('<table>
<tr>
<td>Create a Poll...</td></tr> 
<tr><td>
<form action="%1$s?x=admin" method="POST">
Question: </td><td> <textarea name="question"></textarea></td></tr>
<tr><td>
Option 1: </td><td> <input type="text" name="1" /></td></tr>
<tr><td>
Option 2: </td><td> <input type="text" name="2" /></td></tr>
<tr><td>
Option 3: </td><td> <input type="text" name="3" /></td></tr>
<tr><td>
Option 4: </td><td> <input type="text" name="4" /></td></tr>
<tr><td>
End In: </td><td> <input type="text" name="end" />(days)</td></tr>
<tr><td>
<input type="submit" name="submit" value="Create" />
</form>


<a href="%1$s?x=admin&del=1"][b][Delete current Poll][/b]</a>
</td>
</tr>
</table>', $_SERVER['PHP_SELF']);
}

}

}

$po = new _poll();

echo '<h1>Poll</h1>
';

switch($_GET['x']) {
case 'vote': $po->_vote(); break;
case 'admin': $po->_admin(); break;
default: $po->_index(); break;
}

?>
