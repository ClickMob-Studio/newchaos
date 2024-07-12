<?php

include 'header.php';

$attack_person = new User(825);

$hitChance = 50;
$maxDamage = 100;
$criticalHit = 1;
$counterAttack = 0;

$damageDifferential = ($attack_person->moddedstrength - $user_class->moddeddefense) / $user_class->moddeddefense;

var_dump($damageDifferential);
exit;

echo 'done'; exit;
?>