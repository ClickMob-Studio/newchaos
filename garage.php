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
$query = "SELECT * FROM garage WHERE owner = :username ORDER BY `id` DESC LIMIT :limitvalue, :limit";
$db->query($query);
$db->bind(':username', $user_class->id);
$db->bind(':limitvalue', $limitvalue, PDO::PARAM_INT);
$db->bind(':limit', $limit, PDO::PARAM_INT);
$rows = $db->fetch_row();

?>

<!DOCTYPE html>
<html>
<head>
    <style>
        .card-header, .card-body {
            color: white;
        }
        .btn-primary, .btn-secondary {
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card mt-3" style="background: transparent;">
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

                <div class="card mt-3" style="background: transparent;">
                    <h1>
                        Your Car Garage - Holding <?php echo $totalrows; ?> Cars
                    </h1>
                    <div class="card-body">
                        <?php foreach ($rows as $array) {
                            if (isset($carsList[$array['car']])) {
                                $car = $carsList[$array['car']];
                                $value = $car['max_worth'];
                                $repaircost = $value - $array['worth'];
                                $totalvalue += $array['worth'];
                                $totalrepair += $repaircost;
                                ?>
                                <div class="card mb-3" id="car-<?php echo $array['id']; ?>">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-4">
                                                <strong>ID:</strong> <?php echo $array['id']; ?>
                                            </div>
                                            <div class="col-4">
                                                <strong>Name:</strong> <?php echo $car['name']; ?>
                                            </div>
                                            <div class="col-4">
                                                <strong>Value (Repair):</strong> &pound;<span class="car-worth"><?php echo number_format($array['worth']); ?></span>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4">
                                                <strong>Damage:</strong> <?php echo $array['damage']; ?>%
                                            </div>
                                            <div class="col-4">
                                                <strong>1st Location:</strong> <?php echo getCityNameByID($array['origion']); ?>
                                            </div>
                                            <div class="col-4">
                                                <strong>Current Location:</strong> <?php echo getCityNameByID($array['location']); ?>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12 text-center">
                                            <a href="#" class="sell-link btn btn-danger" data-id="<?php echo $array['id']; ?>">Sell</a>
                                             <a href="#" class="repair-link btn btn-warning" data-id="<?php echo $array['id']; ?>">Repair</a>
                                             </div>
                                            </div>

                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="card mb-3">
                                    <div class="card-body text-danger">
                                        Car data not found for ID: <?php echo $array['car']; ?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?php } ?>

                        <div class="d-flex justify-content-between mt-3">
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
                        <form id="shipForm" method="post">
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
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
$(document).ready(function() {
    $('.sell-link').on('click', function(e) {
        e.preventDefault();
        var carId = $(this).data('id');

        $.ajax({
            url: 'ajax_sell_car.php',
            type: 'POST',
            dataType: 'json',
            data: { car_id: carId },
            success: function(response) {
                if (response.message) {
                    $('#car-' + carId).remove();
                    $('#messages').html(response.message);
                } else {
                    alert('An unexpected error occurred. Please try again.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('An error occurred: ' + textStatus);
            }
        });
    });

    $('.repair-link').on('click', function(e) {
        e.preventDefault();
        var carId = $(this).data('id');

        $.ajax({
            url: 'ajax_repair_car.php',
            type: 'POST',
            dataType: 'json',
            data: { car_id: carId },
            success: function(response) {
                if (response.message) {
                    $('#car-' + carId + ' .car-worth').text('£' + response.new_worth.toLocaleString());
                    $('#messages').html(response.message);
                } else {
                    alert('An unexpected error occurred. Please try again.');
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('An error occurred: ' + textStatus);
            }
        });
    });
});
</script>

