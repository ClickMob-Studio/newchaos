<?php
require "ajax_header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_class = new User($_SESSION['id']);
    $garageId = $_POST['garage_id'];

    $db->query("SELECT * FROM garage WHERE id = :garage_id AND owner = :username");
    $db->bind(':garage_id', $garageId);
    $db->bind(':username', $user_class->id);
    $car = $db->fetch_row(true);

    if ($car) {
      
        $db->query("SELECT * FROM cars WHERE name = :car_name");
        $db->bind(':car_name', $car['car']);
        $carDetails = $db->fetch_row(true);

        if ($carDetails) {
         
            $repairCost = $carDetails['max_worth'] - $car['worth'];

  
            $db->query("SELECT bank FROM grpguser WHERE id = :user_id");
            $db->bind(':user_id', $user_class->id);
            $userBank = $db->fetch_row(true);

            if ($userBank && $userBank['bank'] >= $repairCost) {
            
                $newBankBalance = $userBank['bank'] - $repairCost;
                $db->query("UPDATE grpguser SET bank = :new_bank_balance WHERE id = :user_id");
                $db->bind(':new_bank_balance', $newBankBalance);
                $db->bind(':user_id', $user_class->id);
                $db->execute();

               
                $db->query("UPDATE garage SET worth = :max_worth WHERE id = :garage_id AND owner = :username");
                $db->bind(':max_worth', $carDetails['max_worth']);
                $db->bind(':garage_id', $garageId);
                $db->bind(':username', $user_class->id);
                $db->execute();

                echo json_encode([
                    'message' => 'Car repaired successfully.',
                    'new_worth' => $carDetails['max_worth'],
                    'garage_id' => $garageId
                ]);
            } else {
                echo json_encode(['message' => 'Insufficient funds to repair the car.']);
            }
        } else {
            echo json_encode(['message' => 'Car details not found.']);
        }
    } else {
        echo json_encode(['message' => 'Car not found or you do not own this car.']);
    }
} else {
    echo json_encode(['message' => 'Invalid request method.']);
}
?>
