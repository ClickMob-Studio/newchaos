<?php
include "dbcon.php";
include "classes.php";
include "codeparser.php";
include "database/pdo_class.php";
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Custom log file for debugging
$log_file = '/home/chaoscit/api_error.log'; // Update this path as needed

// Function to get all headers if not already defined
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

// Log received headers
$headers = getallheaders();
file_put_contents($log_file, "Headers: " . print_r($headers, true) . "\n", FILE_APPEND);

// Get the session ID from the Authorization header
$session_id = null;
if (isset($headers['Authorization'])) {
    $session_id = str_replace('Bearer ', '', $headers['Authorization']);
    file_put_contents($log_file, "Session ID from header: " . $session_id . "\n", FILE_APPEND);
}

if ($session_id) {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close(); // Close the current session if already started
    }

    session_id($session_id); // Set the session ID
}

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start the session with the given session ID
}

file_put_contents($log_file, "Session after start: " . print_r($_SESSION, true) . "\n", FILE_APPEND);

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $data['user_id'];
        file_put_contents($log_file, "User ID from POST: " . $user_id . "\n", FILE_APPEND);

        // Check if the session variable exists and matches the provided user ID
        if (!isset($_SESSION['user_id'])) {
            file_put_contents($log_file, "Unauthorized: No session user_id found\n", FILE_APPEND);
            echo json_encode(["success" => false, "message" => "Unauthorized"]);
            exit();
        }

        if ($_SESSION['user_id'] != $user_id) {
            file_put_contents($log_file, "Unauthorized: Session ID mismatch. Session user_id: {$_SESSION['user_id']}, Provided user_id: {$user_id}\n", FILE_APPEND);
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
    file_put_contents($log_file, 'Error: ' . $e->getMessage() . "\n", FILE_APPEND);
    echo json_encode(["success" => false, "message" => "Server error. Please try again later."]);
}
?>
