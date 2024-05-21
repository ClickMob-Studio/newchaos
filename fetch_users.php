<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

// Include the database class
require 'database/pdo_class.php';


// Prepare and execute the query
$sql = "SELECT id, username FROM grpgusers";
$db->query($sql);
$result = $db->fetch_row();

// Initialize the users array
$users = array();
if (!empty($result)) {
    $users = $result;
} else {
    echo json_encode(array());
    exit;
}

// Output the result as JSON
echo json_encode($users);

?>
