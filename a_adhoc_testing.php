<?php

include 'header.php';

$attack_person = new User(825);

$hitChance = 50;
$maxDamage = 100;
$criticalHit = 1;
$counterAttack = 0;

echo 'Defender Strength: ' . $attack_person->moddedstrength . '<br />';
echo 'Defender Defense: ' . $attack_person->moddeddefense . '<br />';

echo 'Attacker Strength: ' . $user_class->moddedstrength . '<br />';
echo 'Attacker Defense: ' . $user_class->moddeddefense . '<br />';

echo '<hr />';

$damageDifferential = ($attack_person->moddedstrength - $user_class->moddeddefense) / $user_class->moddeddefense;

var_dump($damageDifferential);
exit;

echo 'done'; exit;
?>