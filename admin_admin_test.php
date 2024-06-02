<?php
include "dbcon.php";
include "classes.php";
include "codeparser.php";
include "database/pdo_class.php";


header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the session ID from the Authorization header
$headers = apache_request_headers();
if (isset($headers['Authorization'])) {
    $session_id = str_replace('Bearer ', '', $headers['Authorization']);
    session_id($session_id);
}

session_start();

// Log incoming request data
file_put_contents('php://stderr', print_r($_POST, TRUE));
file_put_contents('php://stderr', print_r($_SESSION, TRUE));

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $data['user_id'];

        // Validate the session token and user ID
        if (!isset($_SESSION['id']) || $_SESSION['id'] !== $user_id) {
            echo json_encode(["success" => false, "message" => "Unauthorized"]);
            exit();
        }

        $user_class = new User($_SESSION['id']);

        // Assume you have a method in your User class that returns the user data
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
