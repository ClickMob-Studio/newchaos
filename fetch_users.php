<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
$servername = "localhost";
$username = "chaoscit_user";
$password = "3lrKBlrfMGl2ic14";
$dbname = "chaoscit_game";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT id, username FROM grpgusers";
$result = $conn->query($sql);

$users = array();
if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    $users[] = $row;
  }
} else {
  echo json_encode([]);
  exit;
}

echo json_encode($users);

$conn->close();
?>
