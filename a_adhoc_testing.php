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

$yourhp = $user_class->maxhp;
$theirhp = $attack_person->maxhp;

// Person being attacked, attacking user
$hitChance = 50;
$criticalHit = 1;
$counterAttack = 0;

echo 'Your HP: ' . $yourhp . ' <br />';
echo 'Their HP: ' . $theirhp . ' <br />';
echo '<hr />';

while ($yourhp > 0 && $theirhp > 0) {
    if ($wait == 0) {
        $damage = getAttackDamage($attack_person, $user_class);
        $yourhp = $yourhp - $damage;

        echo 'Attacker: ' . $attack_person->formattedname . ' <br />';
        echo 'Damage: ' . $damage . ' <br />';
        echo 'Your HP: ' . $yourhp . ' <br />';
        echo 'Their HP: ' . $theirhp . ' <br />';
        echo '<hr />';

    } else {
        $damage = getAttackDamage($user_class, $attack_person);
        $theirhp = $theirhp - $damage;

        echo 'Attacker: ' . $user_class->formattedname . ' <br />';
        echo 'Damage: ' . $damage . ' <br />';
        echo 'Your HP: ' . $yourhp . ' <br />';
        echo 'Their HP: ' . $theirhp . ' <br />';
        echo '<hr />';
    }

    $wait = 0;
}

if ($theirhp > 0) {
    echo 'Winner: ' . $attack_person->formattedname;
} else {
    echo 'Winner: ' . $user_class->formattedname;
}


var_dump($damageDifferential);
exit;

echo 'done'; exit;
?>