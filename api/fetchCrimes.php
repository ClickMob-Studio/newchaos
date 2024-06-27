<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include "../database/pdo_class.php";
include "../classes.php";

header('Content-Type: application/json');

try {
    $db->query("SELECT * FROM crimes ORDER BY nerve DESC");
    $db->execute();
    $rows = $db->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(array('success' => true, 'crimes' => $rows));
} catch (Exception $e) {
    echo json_encode(array(
        'success' => false,
        'message' => $e->getMessage()
    ));
}
?>
