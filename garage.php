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

if (isset($_POST['repair'])) {
    if (is_array($_POST['car'])) {
        $cars = count($_POST['car']);
        $i = 0;
        $totalmoney = 0;
        $cars2 = 0;
        foreach ($_POST['car'] as $car) {
            $db->query("SELECT * FROM garage WHERE id=:car");
            $db->bind(':car', $car);
            $array = $db->fetch_row(true);

            if ($array['damage'] == "0" && $cars == 1) {
                echo "This car doesn't need repairing.\n";
            } else {
                if ($array['damage'] > 0) {
                    $cars2 += 1;
                }

                if ($array['owner'] != $username) {
                    $error = 1;
                } else {
                    $cost = $carsList[$array['car']]['max_worth'] - $array['worth'];
                    $totalmoney += $cost;

                    if ($totalmoney > $fetch->money) {
                        if (($i + 1) == $cars) {
                            if ($cars == 1) {
                                echo "You do not have enough money to repair this car.\n";
                            } else {
                                echo "You do not have enough money to repair these cars.\n";
                            }
                        }
                    } else {
                        if (!$error) {
                            $db->query("UPDATE accounts SET money=money-:cost WHERE username=:username");
                            $db->bind(':cost', $cost);
                            $db->bind(':username', $username);
                            $db->execute();
                            
                            $db->query("UPDATE garage SET damage='0', worth=:value WHERE id=:car");
                            $db->bind(':value', $carsList[$array['car']]['max_worth']);
                            $db->bind(':car', $car);
                            $db->execute();

                            if (($i + 1) == $cars) {
                                if ($cars2 == 1) {
                                    echo "You repaired the car for &pound;" . number_format($cost) . ".\n";
                                } else {
                                    echo "You repaired " . number_format($cars2) . " cars for &pound;" . number_format($totalmoney) . ".\n";
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
                    }
                }
            }
            $i++;
        }
    }
}

if (isset($_POST['remove'])) {
    if (is_array($_POST['car'])) {
        $cars = count($_POST['car']);
        $i = 0;
        foreach ($_POST['car'] as $car) {
            $db->query("SELECT * FROM garage WHERE id=:car");
            $db->bind(':car', $car);
            $array = $db->fetch_row(true);

            if ($array['owner'] != $username) {
                $error = 1;
            }

            if (!$error) {
                $db->query("DELETE FROM garage WHERE id=:car");
                $db->bind(':car', $car);
                $db->execute();

                if (($i + 1) == $cars) {
                    if ($cars == 1) {
                        echo "The car selected has been ditched.\n";
                    } else {
                        echo "The cars selected have been ditched.\n";
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