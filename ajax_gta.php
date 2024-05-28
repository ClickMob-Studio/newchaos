<?php
include "ajax_header.php";
$user_class = new User($_SESSION['id']);
$radiobutton = isset($_POST['radiobutton']) ? $_POST['radiobutton'] : 0;
$chance = explode("-", $user_class->gtachance);

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
    echo "<div class='container mt-5'><div class='alert alert-danger text-center'>You got away with nothing.</div></div>";
}

updateChance($chance);
$tim = time() + rand(55, 90);
$db->query("UPDATE grpgusers SET gtachance = ?, lastgta = ? WHERE id = ?");
$db->bind(1, implode("-", $chance));
$db->bind(2, $tim);
$db->bind(3, $user_class->id);
$db->execute();

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