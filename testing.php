<?php 
include_once "header.php";


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


$radiobutton = isset($_POST['radiobutton']) ? $_POST['radiobutton'] : 0;
$chance = explode("-", $user_class->gtachance);

if (isset($_POST['submit'])) {
    $suc = $chance[$radiobutton];
    $ran = rand(1, 45);

    if ($ran <= $suc) {
        $cars = getCars();

        $win = rand(0, count($cars) - 1);
        $selectedCar = $cars[$win];
        $img = $selectedCar['image_path'];

        echo "
        <div class='container mt-5'>
            <div class='alert alert-success text-center'>
                Nice Work! You jumped in the car and sped off, getting away with a {$selectedCar['name']}!
            </div>
            <div class='text-center'>
                <img src=\"$img\" class='img-fluid'>
            </div>
        </div>";

        $worthData = calculateWorth($selectedCar['max_worth']);
        $for = $worthData['worth'];
        $damage = $worthData['damage'];

        $rankxp = rand(8, 13);
        $db->query("UPDATE grpgusers SET exp = exp + ? WHERE id = ?");
        $db->bind(1, $rankxp);
        $db->bind(2, $user_class->id);
        $db->execute();

        if ($user_class->gang != '0') {
            $db->query("UPDATE gangs SET exp = exp + ? WHERE name = ?");
            $db->bind(1, $rankxp);
            $db->bind(2, $user_class->gang);
            $db->execute();
        }

        $db->query("INSERT INTO `garage` (`owner`, `car`, `damage`, `origion`, `location`, `worth`) 
                    VALUES (?, ?, ?, ?, ?, ?)");
        $db->bind(1, $user_class->id);
        $db->bind(2, $selectedCar['name']);
        $db->bind(3, $damage);
        $db->bind(4, $user_class->city);
        $db->bind(5, $user_class->city);
        $db->bind(6, $for);
        $db->execute();
    } else {
        echo "<div class='container mt-5'><div class='alert alert-danger text-center'>You failed and came back with sore feet!</div></div>";
        $new_rank = $user_class->exp + rand(3, 6);
        $db->query("UPDATE grpgusers SET exp = ? WHERE id = ?");
        $db->bind(1, $new_rank);
        $db->bind(2, $user_class->id);
        $db->execute();
        $reason = "GTA";
        echo "<div class='container mt-5'><div class='alert alert-danger text-center'>You got away with nothing.</div></div>";
    }

    updateChance($chance);
    $tim = time() + rand(55, 90);
    $db->query("UPDATE grpgusers SET gtachance = ?, lastgta = ? WHERE id = ?");
    $db->bind(1, implode("-", $chance));
    $db->bind(2, $tim);
    $db->bind(3, $user_class->id);
    $db->execute();

    exit();
}

function getCars() {
    global $db;
    $db->query("SELECT `name`, image_path, max_worth FROM cars");
    return $db->fetch_row();
}

function calculateWorth($max) {
    $damage = rand(0, 50);

    if ($damage == 0) {
        $worth = $max;
    } elseif ($damage == 50) {
        $worth = 0;
    } else {
        $worth = round($max / $damage * 2);
    }
    
    return array('worth' => $worth, 'damage' => $damage);
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
                            <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo "$one4"; ?>" aria-valuenow="<?php echo "$one4"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                    <div class="col-md-2 select" id="5" onclick="SelectOption(this.id);">
                        <img src="images/gta/gar.jpg" class="img-fluid">
                        <p>Break into a Garage</p>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo "$chance[4]"; ?>%" aria-valuenow="<?php echo "$chance[4]"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo "$one5"; ?>" aria-valuenow="<?php echo "$one5"; ?>" aria-valuemin="0" aria-valuemax="100"></div>
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
<?php include_once "incfiles/foot.php"; ?>
