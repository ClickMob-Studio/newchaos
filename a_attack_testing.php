<?php

include 'header.php';


?>

<form method="POST">
    <p>Enter the ID of a user to attack:</p>
    <input type="number" name="attack_id" />

    <input type="submit" value="Attack" />
</form>

<?php

if (isset($_POST['attack_id']) && $_POST['attack_id']) {
    $attackId = (int)$_GET['attack_id'];

    $attack_person = new User(1);

    $hitChance = 50;
    $maxDamage = 100;
    $criticalHit = 1;
    $counterAttack = 0;


    echo $attack_person->formattedname . '<br />';
    echo 'Defender Strength: ' . number_format($attack_person->moddedstrength, 0) . '<br />';
    echo 'Defender Defense: ' . number_format($attack_person->moddeddefense, 0) . '<br />';
    echo 'Defender Speed: ' . number_format($attack_person->moddedspeed, 0) . '<br />';
    echo '<br />';

    echo $user_class->formattedname . '<br />';
    echo 'Attacker Strength: ' . number_format($user_class->moddedstrength, 0) . '<br />';
    echo 'Attacker Defense: ' . number_format($user_class->moddeddefense, 0) . '<br />';
    echo 'Attacker Speed: ' . number_format($user_class->moddedspeed, 0) . '<br />';
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
            $wait = 0;
        }

        if ($yourhp > 0) {
            $damage = getAttackDamage($user_class, $attack_person);
            $theirhp = $theirhp - $damage;

            echo 'Attacker: ' . $user_class->formattedname . ' <br />';
            echo 'Damage: ' . $damage . ' <br />';
            echo 'Your HP: ' . $yourhp . ' <br />';
            echo 'Their HP: ' . $theirhp . ' <br />';
            echo '<hr />';
        }
    }

    if ($theirhp > 0) {
        echo 'Winner: ' . $attack_person->formattedname;
    } else {
        echo 'Winner: ' . $user_class->formattedname;
    }
    exit;

    echo 'done'; exit;
}



?>