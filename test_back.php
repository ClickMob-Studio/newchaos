<?php 
require "header.php";


if ($user_class->rmdays >= 1)
        $multiply = 0.04;
    else
        $multiply = 0.02;
    $addmul = $ptsadd = 0;
    if ($user_class->donations >= 50) {
        $addmul = .02;
        $ptsadd = 75;
    }
    if ($user_class->donations >= 100) {
        $addmul = .03;
        $ptsadd = 120;
    }
    if ($user_class->donations >= 200) {
        $addmul = .05;
        $ptsadd = 150;
    }
    // if($user_class->bankboost > 0){
    //     $percentage = $user_class->bankboost * 10;
    //     $line['bank']
    // }
    $multiply += $addmul;
    if ($user_class->bank >= 15000000)
    $interest = ceil(15000000 * $interest);
else
    $interest = ceil($user_class->bank * $interest);

    echo $intrest;