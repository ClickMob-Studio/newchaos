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
        .table {
            color: white;
        }
        .table-striped>tbody>tr:nth-of-type(odd) {
            color: white;
        }
    </style>
</head>
<body>
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
                                    <th scope="col">ID</th>
                                    <th scope="col">Name</th>
                                    <th scope="col">Value (Repair)</th>
                                    <th scope="col">Damage</th>
                                    <th scope="col">1st Location</th>
                                    <th scope="col">Current Location</th>
                                    <th scope="col">Actions</th>
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

                                        echo "<tr id='car-{$array['id']}'>
                                        <td align=\"center\">{$array['id']}</td>
                                        <td align=\"center\">{$car['name']}</td>
                                        <td align=\"center\">&pound;" . number_format($array['worth']) . "</td>
                                        <td align=\"center\">{$array['damage']}%</td>
                                        <td align=\"center\">{$array['origion']}</td>
                                        <td align=\"center\">{$array['location']}</td>
                                        <td align=\"center\"><a href=\"#\" class=\"sell-link\" data-id=\"{$array['id']}\">Sell</a></td>
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
            url: 'sell_car.php',
            type: 'POST',
            dataType: 'json', // Ensure the response is parsed as JSON
            data: { car_id: carId },
            success: function(response) {
                if (response.message) {
                    $('#car-' + carId).remove();
                    alert(response.message);
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
</body>
</html>
