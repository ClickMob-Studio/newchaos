<?php 
include_once "header.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//$radiobutton = $_POST['radiobutton'] ?? 0;


//$chance = explode("-", $user_class->gtachance);

// if ($user_class->lastgta > time()) {
//     $left = $user_class->lastgta - time();
//     echo "<div class='container mt-5'>
//             <div class='alert alert-warning text-center'>
//                 You must lay low for <span id='gta'></span>
//                 <script type='text/javascript'>setTimer('gta','$left', { 0: function () { window.location = 'gta2.php' }});</script>
//                 seconds before you can commit another GTA!<br>
//                 <br><strong>Tired of waiting to do your next GTA? Steroids can be taken by using credits if you wish to be die-hard and not have to wait in-between each GTA for plenty of cars n rank!</strong>
//             </div>
//           </div>";
//     exit();
// }

if ($radiobutton == "0") {
    echo "<div class='container mt-5'><div class='alert alert-danger text-center'>Error!</div></div>";
    die();
}

if ($_POST['submit']) {
    $suc = $chance[$radiobutton];
    $ran = rand(1, 45);

    if ($ran <= $suc) {
        $cars = array(
            'Renault Clio Sport', 'Audi A3', 'BMW M3', 'Cadillac Escalade', 'Nissan Skyline', 
            'Porsche 911', 'GT 40', 'Lamborghini Murcielago', 'Ferrari Enzo', 'TVR Speed 12', 
            'Mclaren F1', 'Bugatti Veyron', 'Mercedes SLK McLaren'
        );

        $win = rand(0, 11);
        $img = getImageForCar($cars[$win]);

        echo "<link href='style.css' rel='stylesheet' type='text/css'><br>
        <div class='container mt-5'>
            <div class='alert alert-success text-center'>
                Nice Work! You jumped in the car and sped off, getting away with a $cars[$win]!
            </div>
            <div class='text-center'>
                <img src=\"$img\" class='img-fluid'>
            </div>
        </div>";

        $db->query("SELECT * FROM mission WHERE username = ? AND mission = '2'");
        $db->bind(1, $username);
        if ($db->num_rows() > 0) {
            $db->query("UPDATE mission SET unit = unit + 1 WHERE username = ?");
            $db->bind(1, $username);
            $db->execute();
        }

        $db->query("UPDATE account_info SET gtas = gtas + 1 WHERE username = ?");
        $db->bind(1, $username);
        $db->execute();

        if ($user_class->steroids != '0') {
            $db->query("UPDATE accounts SET steroids = steroids - 1 WHERE username = ?");
            $db->bind(1, $username);
            $db->execute();
        }

        $for = calculateWorth($cars[$win]);
        $rankxp = rand(8, 13);
        $db->query("UPDATE accounts SET rankpoints = rankpoints + ? WHERE username = ?");
        $db->bind(1, $rankxp);
        $db->bind(2, $username);
        $db->execute();

        if ($user_class->crew != '0') {
            $db->query("UPDATE gangs SET exp = exp + ? WHERE name = ?");
            $db->bind(1, $rankxp);
            $db->bind(2, $user_class->gang);
            $db->execute();
        }

        $db->query("INSERT INTO `garage` (`owner`, `car`, `damage`, `origion`, `location`, `worth`) 
                    VALUES (?, ?, ?, ?, ?, ?)");
        $db->bind(1, $username);
        $db->bind(2, $cars[$win]);
        $db->bind(3, $damage);
        $db->bind(4, $user_class->city);
        $db->bind(5, $user_class->city);
        $db->bind(6, $for);
        $db->execute();
    } else {
        echo "<div class='container mt-5'><div class='alert alert-danger text-center'>You failed and came back with sore feet!</div></div>";
        $new_rank = $user_class->exp + rand(3, 6);
        $db->query("UPDATE accounts SET rankpoints = ? WHERE username = ?");
        $db->bind(1, $new_rank);
        $db->bind(2, $username);
        $db->execute();
        $db->query("UPDATE account_info SET gtas = gtas + 1 WHERE username = ?");
        $db->bind(1, $username);
        $db->execute();
        $reason = "GTA";
        require_once "incfiles/failed.php";
        echo "<div class='container mt-5'><div class='alert alert-danger text-center'>You got away with nothing.</div></div>";
    }

    updateChance($chance);
    $tim = time() + rand(55, 90);
    $db->query("UPDATE accounts SET gtachance = ?, lastgta = ? WHERE username = ?");
    $db->bind(1, implode("-", $chance));
    $db->bind(2, $tim);
    $db->bind(3, $username);
    $db->execute();

    if ($user_class->steroids != '0') {
        $db->query("UPDATE accounts SET lastgta = 0 WHERE username = ?");
        $db->bind(1, $username);
        $db->execute();
        $db->query("UPDATE accounts SET steroids = steroids - 1 WHERE username = ?");
        $db->bind(1, $username);
        $db->execute();
    }

    exit();
}

function getImageForCar($car) {
    $carImages = array(
        "Renault Clio Sport" => "images/cars/renaultcliosport.jpeg",
        "Audi A3" => "images/cars/audia3.jpg",
        "BMW M3" => "images/cars/bmw-m3.jpg",
        "Cadillac Escalade" => "images/cars/escalade.gif",
        "Nissan Skyline" => "images/cars/nissan.jpg",
        "Porsche 911" => "images/cars/porsche.jpg",
        "GT 40" => "images/cars/fordgt40.jpg",
        "Lamborghini Murcielago" => "images/cars/land.jpg",
        "Ferrari Enzo" => "images/cars/ferrarienzo.jpg",
        "TVR Speed 12" => "images/cars/tvr12.jpg",
        "Mclaren F1" => "images/cars/mcf1.jpg",
        "Bugatti Veyron" => "images/cars/BuggatiVeyron.jpg",
        "Mercedes SLK McLaren" => "images/cars/mercedes.jpg"
    );
    return $carImages[$car];
}

function calculateWorth($car) {
    $maxValues = array(
        "Renault Clio Sport" => 5000,
        "Audi A3" => 6000,
        "BMW M3" => 15000,
        "Cadillac Escalade" => 30000,
        "Nissan Skyline" => 40000,
        "Porsche 911" => 55000,
        "GT 40" => 80000,
        "Lamborghini Murcielago" => 110000,
        "Ferrari Enzo" => 170000,
        "TVR Speed 12" => 210000,
        "Mclaren F1" => 250000,
        "Bugatti Veyron" => 300000,
        "Mercedes SLK McLaren" => 330000
    );

    $damage = rand(0, 50);
    $max = $maxValues[$car];

    if ($damage == 0) {
        return $max;
    } elseif ($damage == 50) {
        return 0;
    } else {
        return round($max / $damage * 2);
    }
}

function updateChance(&$chance) {
    for ($i = 0; $i < count($chance); $i++) {
        if ($chance[$i] > 50) {
            $chance[$i] = 40;
        }
        $chance[$i]++;
        if ($chance[$i] > 50) {
            $chance[$i] = 40;
        }
    }
    $newrates = implode("-", $chance);
}
?>

    <form name="form1" method="post" action="">
        <input type="hidden" name="radiobutton" id="select" value="0">
        <div class="container mt-5">
            <div class="card">
                <div class="card-header text-center">
                    Grand Theft Auto
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 select" id="1" onclick="SelectOption(this.id);">
                            <img src="images/gta/rmh.jpg" class="img-fluid">
                            <p>Steal from rich house</p>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo "$chance[0]"; ?>%" aria-valuenow="<?php echo "$chance[0]"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo "$one1"; ?>%" aria-valuenow="<?php echo "$one1"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-md-2 select" id="2" onclick="SelectOption(this.id);">
                            <img src="images/gta/streets.jpg" class="img-fluid">
                            <p>Steal from the streets</p>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo "$chance[1]"; ?>%" aria-valuenow="<?php echo "$chance[1]"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo "$one2"; ?>%" aria-valuenow="<?php echo "$one2"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-md-2 select" id="3" onclick="SelectOption(this.id);">
                            <img src="images/gta/dealer.jpg" class="img-fluid">
                            <p>Steal from Dealership</p>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo "$chance[2]"; ?>%" aria-valuenow="<?php echo "$chance[2]"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo "$one3"; ?>%" aria-valuenow="<?php echo "$one3"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-md-2 select" id="4" onclick="SelectOption(this.id);">
                            <img src="images/gta/show.jpg" class="img-fluid">
                            <p>Steal from Showroom</p>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo "$chance[3]"; ?>%" aria-valuenow="<?php echo "$chance[3]"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo "$one4"; ?>%" aria-valuenow="<?php echo "$one4"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="col-md-2 select" id="5" onclick="SelectOption(this.id);">
                            <img src="images/gta/gar.jpg" class="img-fluid">
                            <p>Break into a Garage</p>
                            <div class="progress">
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo "$chance[4]"; ?>%" aria-valuenow="<?php echo "$chance[4]"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo "$one5"; ?>%" aria-valuenow="<?php echo "$one5"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" name="submit" class="btn btn-primary">Commit GTA!</button>
                </div>
            </div>
        </div>
    </form>
    <div class="container mt-5">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-1">
                        <img src="../images/questionmark.jpg" width="49" height="46" class="img-fluid">
                    </div>
                    <div class="col-md-11">
                        <p>This page is the Grand Theft Auto. Here you can commit a "GTA" which is where you try and rob a car from some unsuspecting person. When you start your percentages are on 0 but the more practice you do the higher the percentages go. You have to lay low for 2 minutes between each GTA to avoid attention from the pigs. When you steal a car it may have been damaged as you tried to get away. After you successfully steal a car it goes into your garage.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
