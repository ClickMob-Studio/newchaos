<?php
include "dbcon.php";
include "classes.php";
include "codeparser.php";
include "database/pdo_class.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

error_reporting(0);


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
if (isset($headers['Authorization'])) {
    $session_id = str_replace('Bearer ', '', $headers['Authorization']);
    session_id($session_id);
}

session_start();

file_put_contents('php://stderr', print_r($_POST, TRUE));
file_put_contents('php://stderr', print_r($_SESSION, TRUE));

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $data['user_id'];

        if (!isset($_SESSION['id']) || $_SESSION['id'] !== $user_id) {
            echo json_encode(["success" => false, "message" => "Unauthorized"]);
            exit();
        }

        $user_class = new User($_SESSION['id']);

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
