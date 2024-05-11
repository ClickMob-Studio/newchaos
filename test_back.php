<?php
require "header.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Calculate the base interest rate based on remaining membership days
if ($user_class->rmdays >= 1) {
    $interest = 0.04;  // 4% interest rate if membership days are 1 or more
} else {
    $interest = 0.02;  // 2% interest rate otherwise
}

// Adjust interest rate based on donations
$addmul = $ptsadd = 0;
if ($user_class->donations >= 50) {
    $addmul = 0.02;
    $ptsadd = 75;
}
if ($user_class->donations >= 100) {
    $addmul = 0.03;
    $ptsadd = 120;
}
if ($user_class->donations >= 200) {
    $addmul = 0.05;
    $ptsadd = 150;
}

// Increase the interest rate by the adjustments from donations
$interest += $addmul;

// Apply bank boost if it's set and greater than zero
if ($user_class->bankboost > 0) {
    $interest += ($interest * ($user_class->bankboost / 100));  // Adjusting the interest rate by bankboost
}

// Calculate the effective interest amount based on the user's bank balance
if ($user_class->bank >= 15000000) {
    $interest = ceil(15000000 * $interest);  // Interest capped at a bank amount of 30 million
} else {
    $interest = ceil($user_class->bank * $interest);  // Interest based on the actual bank balance
}

echo $interest;
?>
