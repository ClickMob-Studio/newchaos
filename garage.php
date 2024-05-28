<?php 
require_once "header.php"; 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$page = isset($_GET['page']) ? $_GET['page'] : 1;


// Fetch cars data
$db->query("SELECT * FROM cars");
$carsData = $db->fetch_row();

$db->query("SELECT * FROM cities");
$citiesData = $db->fetch_row();

$carsList = array();
foreach ($carsData as $car) {
    $carsList[$car['id']] = $car;
}

$citiesList = array();
foreach ($citiesData as $city) {
    $citiesList[$city['id']] = $city;
}

if (isset($_POST['sell'])) {
    if (is_array($_POST['car'])) {
        $cars = count($_POST['car']);
        $i = 0;
        $totalmoney = 0;
        foreach ($_POST['car'] as $car) {
            $db->query("SELECT * FROM garage WHERE id=:car");
            $db->bind(':car', $car);
            $array = $db->fetch_row(true);

            if ($array['owner'] == $username) {
                $totalmoney += $array['worth'];
                $db->query("UPDATE accounts SET money=money+:worth WHERE username=:username");
                $db->bind(':worth', $array['worth']);
                $db->bind(':username', $username);
                $db->execute();
                
                $db->query("DELETE FROM garage WHERE id=:car");
                $db->bind(':car', $car);
                $db->execute();

                if (($i + 1) == $cars) {
                    if ($cars == 1) {
                        echo "You sold the car for &pound;" . number_format($array['worth']) . "";
                    } else {
                        echo "You sold $cars cars for &pound;" . number_format($totalmoney) . ".";
                    }
                }
            } else {
                if (($i + 1) == $cars) {
                    if ($cars == 1) {
                        echo "You do not own this car.\n";
                    } else {
                        echo "You do not own these cars.\n";
                    }
                }
            }
            $i++;
        }
    }
}
exit;