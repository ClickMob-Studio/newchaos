<?php
require "ajax_header.php";

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['regid']) && isset($_POST['send'])) {
    $regid = $_POST['regid'];
    $shipto = $_POST['shipto'];
    $user_class = new User($_SESSION['id']);
    $date = date('Y-m-d H:i:s');

    $db->query("SELECT * FROM garage WHERE id = :regid");
    $db->bind(':regid', $regid);
    $car = $db->fetch_row(true);

    if ($car['manufacturing'] == "1") {
        $response['message'] = "Unable to take action due to car in manufacturing status.";
    } elseif ($car['manufacturing'] != "1") {
        if ($shipto == "player") {
            if ($car['owner'] == $user_class->id) {
                $db->query("SELECT id, username, city FROM grpgusers WHERE username = :username");
                $db->bind(':username', $_POST['username']);
                $array = $db->fetch_row(true);

                if ($array['city'] != $car['location']) {  
                    $response['message'] = "You have to be in the same location as the car to send it to another player.";
                } else {
                    $db->query("UPDATE garage SET owner = :new_owner WHERE id = :regid");
                    $db->bind(':new_owner', $array['id']);
                    $db->bind(':regid', $regid);
                    $db->execute();
                    Send_Event($array['id'], "You have received a car from " . $user_class->formattedname);

                    $response['message'] = "The car ($regid) has been sent to {$_POST['username']}.";
                }
            } else {
                $response['message'] = "You do not own that car.";
            }
        } else {
            $country = "";
            switch ($shipto) {
                case "1": $country = "England"; break;
                case "2": $country = "Germany"; break;
                case "3": $country = "France"; break;
                case "4": $country = "Spain"; break;
                case "5": $country = "China"; break;
                case "6": $country = "Italy"; break;
                case "7": $country = "Russia"; break;
                default: $response['message'] = "Invalid country selection."; break;
            }

            if (empty($response['message'])) {
                if ($car['owner'] == $user_class->id) {
                    if ($user_class->city != $car['location']) {
                        $response['message'] = "You have to be in the same location as the car to send it to another country.";
                    } else {
                        $db->query("UPDATE garage SET location = :location WHERE id = :regid");
                        $db->bind(':location', $country);
                        $db->bind(':regid', $regid);
                        $db->execute();

                        $response['message'] = "The car ($regid) has been sent to $country successfully.";
                    }
                } else {
                    $response['message'] = "You do not own that car.";
                }
            }
        }
    }
} else {
    $response['message'] = 'Invalid request.';
}

echo json_encode($response);
?>
