<?php
require "ajax_header.php";
$response = array('message' => '');

if (isset($_POST['car_id'])) {
    $carId = $_POST['car_id'];

    // Fetch car data
    $db->query("SELECT * FROM garage WHERE id = :car_id");
    $db->bind(':car_id', $carId);
    $array = $db->fetch_row(true);

    if ($array && $array['owner'] == $user_class->id) {
        $totalmoney = $array['worth'];
        $db->query("UPDATE accounts SET money=money + :worth WHERE username = :username");
        $db->bind(':worth', $array['worth']);
        $db->bind(':username', $user_class->id);
        $db->execute();
        
        $db->query("DELETE FROM garage WHERE id = :car_id");
        $db->bind(':car_id', $carId);
        $db->execute();

        $response['message'] = "You sold the car for &pound;" . number_format($array['worth']) . ".";
    } else {
        $response['message'] = "You do not own this car.";
    }
} else {
    $response['message'] = "No car ID provided.";
}

echo json_encode($response);
?>
