<?php
require "header.php";
if ($user_class->admin < 1) {
    exit();
}

if (isset($_POST['userid']))
    $db->query("SELECT a.timestamp AS attack_time, u.timestamp AS user_log_time FROM user_logs u JOIN attacklog a ON u.timestamp = a.timestamp AND a.attacker = ? WHERE u.user_id = ?");
$db->execute([$_POST['userid'], $_POST['userid']]);

$rows = $db->fetch_row();
$count = 1;

foreach ($rows as $row) {
    echo $count . ") Attack Time: " . $row['attack_time'] . " BA Time: " . $row['user_log_time'];
    echo "<br>";
}

?>

<form method="post">
    <input type="text" name="userid">
    <input type="submit" name="submit">
</form>