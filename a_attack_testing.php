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
    $attackId = (int)$_POST['attack_id'];

    $attack_person = new User($attackId);

    echo $attack_person->formattedname . '<br />';
    echo 'Defender Strength: ' . number_format($attack_person->moddedstrength, 0) . '<br />';
    echo 'Defender Defense: ' . number_format($attack_person->moddeddefense, 0) . '<br />';
    echo 'Defender Speed: ' . number_format($attack_person->moddedspeed, 0) . '<br />';
    echo 'Defender Agility: ' . number_format($attack_person->moddedagility, 0) . '<br />';
    echo '<br />';

    echo $user_class->formattedname . '<br />';
    echo 'Attacker Strength: ' . number_format($user_class->moddedstrength, 0) . '<br />';
    echo 'Attacker Defense: ' . number_format($user_class->moddeddefense, 0) . '<br />';
    echo 'Attacker Speed: ' . number_format($user_class->moddedspeed, 0) . '<br />';
    echo 'Attacker Agility: ' . number_format($user_class->moddedagility, 0) . '<br />';
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

    echo 'Your HP: ' . $yourhp . ' <br />';
    echo 'Their HP: ' . $theirhp . ' <br />';
    echo '<hr />';

    while ($yourhp > 0 && $theirhp > 0) {
        if ($wait == 0) {
            $hitChance = 50;
            if ($attack_person->moddedspeed > $user_class->moddedagility) {
                $hitChance = $hitChance + 20;
            }

            if (mt_rand(1,100) > $hitChance) {
                // Missed
                echo 'Attacker: ' . $attack_person->formattedname . ' <br />';
                echo 'MISSED<br />';
                echo 'Your HP: ' . $yourhp . ' <br />';
                echo 'Their HP: ' . $theirhp . ' <br />';
                echo '<hr />';
            } else {
                // Hit
                $damageResult = getAttackDamage($attack_person, $user_class);
                $damage = $damageResult['damage'];
                $yourhp = $yourhp - $damage;

                echo 'Attacker: ' . $attack_person->formattedname . ' <br />';
                if ($damageResult['is_critical_hit']) {
                    echo 'Damage: ' . $damage . ' *** CRITICAL HIT ***<br />';
                } else {
                    echo 'Damage: ' . $damage . ' <br />';
                }
                echo 'Your HP: ' . $yourhp . ' <br />';
                echo 'Their HP: ' . $theirhp . ' <br />';
                echo '<hr />';
            }
        } else {
            $wait = 0;
        }

        if ($yourhp > 0) {
            $hitChance = 60;
            if ($user_class->moddedspeed > $attack_person->moddedagility) {
                $hitChance = $hitChance + 20;
            }

            if (mt_rand(1,100) > $hitChance) {
                // Missed
                echo 'Attacker: ' . $user_class->formattedname . ' <br />';
                echo 'MISSED<br />';
                echo 'HIT CHANCE ' . $hitChance . '<br />';
                echo 'Your HP: ' . $yourhp . ' <br />';
                echo 'Their HP: ' . $theirhp . ' <br />';
                echo '<hr />';
            } else {
                // Hit
                $damageResult = getAttackDamage($user_class, $attack_person);
                $damage = $damageResult['damage'];
                $theirhp = $theirhp - $damage;

                echo 'Attacker: ' . $user_class->formattedname . ' <br />';
                if ($damageResult['is_critical_hit']) {
                    echo 'Damage: ' . $damage . ' *** CRITICAL HIT ***<br />';
                } else {
                    echo 'Damage: ' . $damage . ' <br />';
                }
                echo 'Your HP: ' . $yourhp . ' <br />';
                echo 'Their HP: ' . $theirhp . ' <br />';
                echo '<hr />';
            }
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