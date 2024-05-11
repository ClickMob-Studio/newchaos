<?php
require "header.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$result3 = mysql_query("SELECT * FROM grpgusers ORDER BY id ASC");
while ($line = mysql_fetch_array($result3)) {
    $person_class = new User($line['id']);
    // Calculate the base interest rate based on remaining membership days
if ($person_class->rmdays >= 1) {
    $interest = 0.04;  // 4% interest rate if membership days are 1 or more
} else {
    $interest = 0.02;  // 2% interest rate otherwise
}

// Adjust interest rate based on donations
$addmul = $ptsadd = 0;
if ($person_class->donations >= 50) {
    $addmul = 0.02;
    $ptsadd = 75;
}
if ($person_class->donations >= 100) {
    $addmul = 0.03;
    $ptsadd = 120;
}
if ($person_class->donations >= 200) {
    $addmul = 0.05;
    $ptsadd = 150;
}

// Increase the interest rate by the adjustments from donations
$interest += $addmul;

// Apply bank boost if it's set and greater than zero
if ($person_class->bankboost > 0) {
    $interest += ($interest * ($person_class->bankboost / 100));  // Adjusting the interest rate by bankboost
}

// Calculate the effective interest amount based on the user's bank balance
if ($person_class->bank >= 15000000) {
    $interest = ceil(15000000 * $interest);  // Interest capped at a bank amount of 30 million
} else {
    $interest = ceil($person_class->bank * $interest);  // Interest based on the actual bank balance
}
    $newmoney = round($line['bank'] + $interest);
    mysql_query("UPDATE grpgusers SET bank = $newmoney, points = points + $ptsadd WHERE id = {$line['id']}");
    Send_Event($line['id'], "You have earned " . prettynum($interest, 1) . " for your bank", $line['id']);
}
echo "complete";

echo $interest;
?>
