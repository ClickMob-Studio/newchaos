<?php
header('Content-Type: application/json; charset=utf-8');

include_once "../database/pdo_class.php";

echo $_SERVER['REQUEST_METHOD'];
exit();

$db->query("SELECT * FROM grpgusers WHERE id = ? LIMIT 1");
$db->execute(array(
    $_POST['id']
));
$row = $db->fetch_row()[0];

//$user_class = new User($_POST['id']);

$result = array(
    'name' => $row['username'],
    'avatar' => $row['avatar'],
    'level' => $row['level']
);

echo json_encode($result);