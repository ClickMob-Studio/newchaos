<?php
include_once "dbcon.php";
include_once "classes.php";
include_once "codeparser.php";
include_once "database/pdo_class.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$db->query("SELECT * FROM crimes ORDER BY nerve DESC");
$db->execute();
$crimes = $db->fetch_row();

echo json_encode(["success" => true, "crimes" => $crimes]);
exit;