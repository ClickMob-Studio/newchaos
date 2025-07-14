<?php
include "dbcon.php";
include "classes.php";
include "codeparser.php";
include "database/pdo_class.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


if (!function_exists('getallheaders')) {
    function getallheaders()
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}

$headers = getallheaders();

$session_id = null;
if (isset($headers['Authorization'])) {
    $session_id = str_replace('Bearer ', '', $headers['Authorization']);
}

if ($session_id) {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    session_id($session_id);
}

start_session_guarded();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);
        $user_id = $data['user_id'];

        $_SESSION['user_id'] = $user_id;

        $user_class = new User($_SESSION['user_id']);
        set_last_active($user_class->id);

        if (isset($user_class->id)) {
            $user_data = $user_class;

            $db->query("SELECT `crimeid`, `count` FROM crimeranks WHERE userid = ?");
            $db->execute(array($user_class->id));
            $crimeRankResults = $db->fetch_row();

            $crimeRankIndexedOnCrimeId = array();
            foreach ($crimeRankResults as $crimeRankResult) {
                $crimeRankIndexedOnCrimeId[$crimeRankResult['crimeid']] = $crimeRankResult;
            }

            $user_data->crimeRanks = $crimeRankIndexedOnCrimeId;

            $user_data->medPackCount = check_items(14, $user_class->id);

            echo json_encode(["success" => true, "user" => $user_data]);
        } else {
            throw new Exception("User data not found.");
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request method"]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => "Server error. Please try again later."]);
}
?>