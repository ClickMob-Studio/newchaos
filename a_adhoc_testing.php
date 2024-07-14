<?php

include 'header.php';

$attack_person = new User(825);

$hitChance = 50;
$maxDamage = 100;
$criticalHit = 1;
$counterAttack = 0;

echo 'Defender Strength: ' . $attack_person->moddedstrength . '<br />';
echo 'Defender Defense: ' . $attack_person->moddeddefense . '<br />';
echo 'Defender Speed: ' . $attack_person->moddedspeed . '<br />';
echo '<br />';

echo 'Attacker Strength: ' . $user_class->moddedstrength . '<br />';
echo 'Attacker Defense: ' . $user_class->moddeddefense . '<br />';
echo 'Attacker Speed: ' . $user_class->moddedspeed . '<br />';
echo '<br />';

echo '<hr />';

$userspeed = $user_class->moddedspeed;
$attackspeed = $attack_person->moddedspeed;
$wait = ($userspeed > $attackspeed) ? 1 : 0;

echo 'Wait: ' . $wait . '<br />';
echo '<br />';

echo '<hr />';

$yourhp = $user_class->hp;
$theirhp = $attack_person->hp;

$wait = 0;

while ($yourhp > 0 && $theirhp > 0) {
    if ($wait == 0) {
        $hitChance = 50;
        $maxDamage = 100;
        if ($attack_person->moddedstrength > $user_class->moddeddefense) {
            $maxDamage = $maxDamage * 2;
        }
        $criticalHit = 1;
        $counterAttack = 0;

        $damageDifferential = ($attack_person->moddedstrength - $user_class->moddeddefense) / $user_class->moddeddefense * 100;
        echo $damageDifferential; exit;

    } else {
    }

//    $damage = round($attack_person->moddedstrength) - $user_class->moddeddefense;
//    $damage = ($damage < 1) ? 1 : $damage;
//    if ($wait == 0) {
//        $yourhp = $yourhp - $damage;
//        $number++;
//        $rtn[] = $number . ":&nbsp;" . $attack_person->formattedname . " hit you for " . prettynum($damage) . " damage using their " . $attack_person->weaponname . ". <br>";
//    } else {
//        $wait = 0;
//    }
//    if ($yourhp > 0) {
//        $damage = round($user_class->moddedstrength) - $attack_person->moddeddefense;
//        $damage = ($damage < 1) ? 1 : $damage;
//        $theirhp = $theirhp - $damage;
//        $number++;
//        $rtn[] = $number . ":&nbsp;" . "You hit " . $attack_person->formattedname . " for " . prettynum($damage) . " damage using your " . $user_class->weaponname . ". <br>";
//    }
}


var_dump($damageDifferential);
exit;

echo 'done'; exit;
?>