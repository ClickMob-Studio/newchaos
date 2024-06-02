<?php
include "dbcon.php";
include "classes.php";
include "codeparser.php";
include "database/pdo_class.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!function_exists('getallheaders')) {
    function getallheaders() {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

// Get the session ID from the Authorization header
$headers = getallheaders();
file_put_contents('php://stderr', "Headers: " . print_r($headers, true)); // Log headers

if (isset($headers['Authorization'])) {
    $session_id = str_replace('Bearer ', '', $headers['Authorization']);
    file_put_contents('php://stderr', "Session ID from header: " . $session_id); // Log session ID
    session_id($session_id);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

file_put_contents('php://stderr', "Session after start: " . print_r($_SESSION, true)); // Log session data

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $data['user_id'];
        file_put_contents('php://stderr', "User ID from POST: " . $user_id); // Log user ID

        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $user_id) {
            file_put_contents('php://stderr', "Unauthorized: Session ID: {$_SESSION['user_id']}, User ID: {$user_id}"); // Log unauthorized reason
            echo json_encode(["success" => false, "message" => "Unauthorized"]);
            exit();
        }

        $user_class = new User($_SESSION['user_id']);
        if (isset($user_class->id)) {
            $user_data = $user_class;
            echo json_encode(["success" => true, "user" => $user_data]);
        } else {
            throw new Exception("User data not found.");
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
    }
} catch (Exception $e) {
    file_put_contents('php://stderr', 'Error: ' . $e->getMessage());
    echo json_encode(["success" => false, "message" => "Server error. Please try again later."]);
}
?>
