<?php

include 'header.php';

if ($user_class->admin < 0) {
    echo 'Admin only'; exit;
}

$quest = 1;

$missions = array();

// A simple favour
$mission = array();
$mission['name'] = 'A simple favour';
$mission['description'] = 'Don Luca wants you to deliver this sealed package, asking for it to be delivered discreetly to a trusted associate, Vinny the Fish, who hangs out at a bar called The Rusty Nail. Find The Rust Nail and hand him the package, but be sure to keep an eye out for police patrols, now they know that you are working for the family they\'ll be sure to harass you if they find you.';
$mission['requirements'] = json_encode(array('vinny_the_fish_delivery' => 1));
$mission['payouts'] = json_encode(array('cash' => 100000, 'exp' => 10));
$missions[] = $mission;

// The Friendly Visit
$mission = array();
$mission['name'] = 'The Friendly Visit';
$mission['description'] = 'A small General Pharmacy owner named Marco has been late with his payments. Don Luca wants you to "remind" Marco why he needs the family\'s protection.';
$mission['requirements'] = json_encode(array('pharmacy_protection' => 1));
$mission['payouts'] = json_encode(array('cash' => 250000, 'exp' => 10));
$missions[] = $mission;

// Loose Lips
$mission = array();
$mission['name'] = 'Loose Lips';
$mission['description'] = 'Word gets back to Don Luca that a low-level thug, Jimmy “The Mouth”, has been talking to the police. Don Luca wants you to deal with him discreetly.';
$mission['requirements'] = json_encode(array('attack_player' => 952));
$mission['payouts'] = json_encode(array('cash' => 250000, 'exp' => 25));
$missions[] = $mission;

// The Money Connection
$mission = array();
$mission['name'] = 'The Money Connection';
$mission['description'] = 'Don Luca wants to know that he can trust you to be a reliable earner for the family, run some crimes and earn the family some cash.';
$mission['requirements'] = json_encode(array('crime_cash' => 25000000));
$mission['payouts'] = json_encode(array('cash' => 500000, 'exp' => 25));
$missions[] = $mission;

// Street Sweeper
$mission = array();
$mission['name'] = 'The Street Sweeper';
$mission['description'] = 'Words is getting round town that the family aren\'t keeping up to their protection for local businesses, head to the Backalley and get rid of some of the vermin lurking around and giving us a bad name.';
$mission['requirements'] = json_encode(array('backalley' => 500));
$mission['payouts'] = json_encode(array('cash' => 500000, 'exp' => 50, 'items' => array(array('id' => 277, 'quantity' => 1))));
$missions[] = $mission;

var_dump($missions);



