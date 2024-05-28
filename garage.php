<?php
require "header.php";
$page = isset($_GET['page']) ? $_GET['page'] : 1;
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch cars data
$db->query("SELECT * FROM cars");
$carsData = $db->fetch_row();

$db->query("SELECT * FROM cities");
$citiesData = $db->fetch_row();

$carsList = array();
foreach ($carsData as $car) {
    $carsList[$car['name']] = $car;
}

$citiesList = array();
foreach ($citiesData as $city) {
    $citiesList[$city['id']] = $city;
}

if (isset($_POST['sell'])) {
    var_dump($_POST); // Dump the entire $_POST array for debugging
    if (isset($_POST['car']) && is_array($_POST['car'])) {
        $cars = count($_POST['car']);
        $i = 0;
        $totalmoney = 0;
        foreach ($_POST['car'] as $car) {
            $db->query("SELECT * FROM garage WHERE id = :car");
            $db->bind(':car', $car);
            $array = $db->fetch_row(true);

            if ($array['owner'] == $user_class->id) {
                $totalmoney += $array['worth'];
                $db->query("UPDATE accounts SET money=money + :worth WHERE username = :username");
                $db->bind(':worth', $array['worth']);
                $db->bind(':username', $user_class->id);
                $db->execute();
                
                $db->query("DELETE FROM garage WHERE id = :car");
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
            $db->query("SELECT * FROM garage WHERE id = :car");
            $db->bind(':car', $car);
            $array = $db->fetch_row(true);

            if ($array['damage'] == "0" && $cars == 1) {
                echo "This car doesn't need repairing.\n";
            } else {
                if ($array['damage'] > 0) {
                    $cars2 += 1;
                }

                if ($array['owner'] != $user_class->id) {
                    $error = 1;
                } else {
                    if (isset($carsList[$array['car']])) {
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
                                $db->query("UPDATE accounts SET money=money - :cost WHERE username = :username");
                                $db->bind(':cost', $cost);
                                $db->bind(':username', $user_class->id);
                                $db->execute();
                                
                                $db->query("UPDATE garage SET damage='0', worth = :value WHERE id = :car");
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
                    } else {
                        echo "Car data not found.\n";
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
            $db->query("SELECT * FROM garage WHERE id = :car");
            $db->bind(':car', $car);
            $array = $db->fetch_row(true);

            if ($array['owner'] != $user_class->id) {
                $error = 1;
            }

            if (!$error) {
                $db->query("DELETE FROM garage WHERE id = :car");
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

$limit = 15;               
$query_count = "SELECT * FROM garage WHERE owner = :username";    
$db->query($query_count);
$db->bind(':username', $user_class->id);
$result_count = $db->fetch_row();
$totalrows = count($result_count);

if (empty($page)) {
    $page = 1;
}

if ($totalrows == 0) {
    $totalrows = 1;
}
$limitvalue = $page * $limit - $limit;  
$numofpages = ceil($totalrows / $limit); 

if (isset($_POST['regid']) && isset($_POST['send'])) {
    $shipto = $_POST['shipto'];
    $db->query("SELECT * FROM garage WHERE id = :regid");
    $db->bind(':regid', $_POST['regid']);
    $car = $db->fetch_row(true);

    if ($car['manufacturing'] == "1") {
        echo "Unable to take action due to car in manufacturing status.";
    } elseif ($car['manufacturing'] != "1") {
        if ($shipto == "player") {
            if ($car['owner'] == $user_class->id) { 
                $db->query("SELECT username, status FROM accounts WHERE username = :username");
                $db->bind(':username', $_POST['username']);
                $array = $db->fetch_row(true);
                if ($fetch->location != $car['location']) {
                    echo "You have to be in the same location as the car to send it to another player.";
                } else {
                    if ($array['status'] == "Alive") {
                        $db->query("UPDATE garage SET owner = :new_owner WHERE id = :regid");
                        $db->bind(':new_owner', $array['username']);
                        $db->bind(':regid', $_POST['regid']);
                        $db->execute();

                        $db->query("INSERT INTO inbox (to, from, message, subject, date, read) VALUES (:to, :from, :message, :subject, :date, 0)");
                        $db->bind(':to', $array['username']);
                        $db->bind(':from', $user_class->id);
                        $db->bind(':message', "$user_class->id sent you a car. Check your inbox for any new additions.");
                        $db->bind(':subject', "The Garage Hideout");
                        $db->bind(':date', $date);
                        $db->execute();

                        echo "The car (". $_POST['regid']. ") has been sent to ". $_POST['username']. ".";
                    } else {
                        echo "You cannot send a car to a dead player.";
                    }
                }
            } else {
                echo "You do not own that car.";
            }
        }

        if ($shipto != "player") { 
            $country = isset($citiesList[$shipto]) ? $citiesList[$shipto]['name'] : 'Unknown';
            if ($car['owner'] == $user_class->id) {
                if ($fetch->location != $car['location']) {
                    echo "You have to be in the same location as the car to send it to another country.";
                } else {
                    $db->query("UPDATE garage SET location = :country WHERE id = :regid");
                    $db->bind(':country', $country);
                    $db->bind(':regid', $_POST['regid']);
                    $db->execute();
                    echo "The car (".$_POST['regid'].") has been sent to $country successfully.";
                }
            } else {
                echo "You do not own that car.";
            }
        }
    }
}
?>

   <style>
        .table {
            color: white;
        }
        .table-striped>tbody>tr:nth-of-type(odd) {
            color: white;
        }
    </style>

<body>
    <form id="carForm" method="post">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card mt-3">
                        <div class="card-header text-center">
                            Page Selection
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <?php if ($page != 1) {
                                        $pageprev = $page - 1;
                                        echo("<a href=\"garage.php?page=$pageprev\" class=\"btn btn-secondary\">Previous Page</a> ");
                                    } ?>
                                </div>
                                <div>
                                    <?php
                                    for ($i = 1; $i <= $numofpages; $i++) {
                                        if ($i == $page) {
                                            echo("<span class=\"btn btn-primary\">$i</span> ");
                                        } else {
                                            echo("<a href=\"garage.php?page=$i\" class=\"btn btn-secondary\">$i</a> ");
                                        }
                                    }

                                    if (($totalrows % $limit) != 0) {
                                        if ($i == $page) {
                                            echo("<span class=\"btn btn-primary\">$i</span> ");
                                        } else {
                                            echo("<a href=\"garage.php?page=$i\" class=\"btn btn-secondary\">$i</a> ");
                                        }
                                    } ?>
                                </div>
                                <div>
                                    <?php if (($totalrows - ($limit * $page)) > 0) {
                                        $pagenext = $page + 1;
                                        echo(" <a href=\"garage.php?page=$pagenext\" class=\"btn btn-secondary\">Next Page</a>");
                                    } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header text-center">
                            Your Car Garage - Holding <?php echo $totalrows; ?> Cars
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">CTRL</th>
                                        <th scope="col">ID</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Value (Repair)</th>
                                        <th scope="col">Damage</th>
                                        <th scope="col">1st Location</th>
                                        <th scope="col">Current Location</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($rows as $array) {
                                        if (isset($carsList[$array['car']])) {
                                            $car = $carsList[$array['car']];
                                            $value = $car['max_worth'];
                                            $repaircost = $value - $array['worth'];
                                            $totalvalue += $array['worth'];
                                            $totalrepair += $repaircost;

                                            echo "<tr>
                                            <td align=\"center\"><input type=\"checkbox\" name=\"car[]\" value=\"{$array['id']}\" class=\"car-checkbox\"></td>
                                            <td align=\"center\">{$array['id']}</td>
                                            <td align=\"center\">{$car['name']}</td>
                                            <td align=\"center\">&pound;" . number_format($array['worth']) . "</td>
                                            <td align=\"center\">{$array['damage']}%</td>
                                            <td align=\"center\">{$array['origion']}</td>
                                            <td align=\"center\">{$array['location']}</td>
                                            </tr>";
                                        } else {
                                            echo "<tr>
                                            <td colspan='7' class='text-danger'>Car data not found for ID: {$array['car']}</td>
                                            </tr>";
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-between mt-3">
                                <div>
                                    <button type="button" id="sellSelected" class="btn btn-danger">Sell Selected</button>
                                    <button type="button" id="removeSelected" class="btn btn-danger">Remove Selected</button>
                                </div>
                                <div>
                                    <b>This Page's Value: <?php echo "&pound;" . number_format($totalvalue); ?></b>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">
                            Ship Car
                        </div>
                        <div class="card-body">
                            <div class="mb-3 row">
                                <label for="regid" class="col-sm-2 col-form-label">Car Reg #:</label>
                                <div class="col-sm-10">
                                    <input name="regid" type="text" class="form-control" id="regid" size="31" maxlength="7">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="shipto" class="col-sm-2 col-form-label">Ship to:</label>
                                <div class="col-sm-10">
                                    <select name="shipto" class="form-select" id="shipto">
                                        <option value="player" selected>Player</option>
                                        <?php foreach ($citiesList as $id => $city) {
                                            echo "<option value='{$id}'>{$city['name']}</option>";
                                        } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="username" class="col-sm-2 col-form-label">Username (if selected player):</label>
                                <div class="col-sm-10">
                                    <input name="username" type="text" class="form-control" id="username" size="31" maxlength="30">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col text-center">
                                    <input name="send" type="submit" class="btn btn-primary" id="send" value="Ship Car">
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>

    <script>
        document.getElementById('sellSelected').addEventListener('click', function() {
            let form = document.getElementById('carForm');
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'sell';
            input.value = 'Sell Selected';
            form.appendChild(input);
            form.submit();
        });

        document.getElementById('removeSelected').addEventListener('click', function() {
            let form = document.getElementById('carForm');
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove';
            input.value = 'Remove Selected';
            form.appendChild(input);
            form.submit();
        });
    </script>
</body>
</html>
