<?php
include 'header.php';
$mypet = new Pet($user_class->id);
$error = ($mypet->energypercent < 25) ? "Your pet needs atleast 25% energy to attack." : $error;
$error = ($mypet->hppercent < 25) ? "Your pet needs atleast 25% HP to attack someone." : $error;
$error = ($mypet->jail > 0) ? "Your pet can't attack if they are in prison." : $error;
$error = ($mypet->hospital > 0) ? "Your pet can't attack someone if they are in the hospital." : $error;
$error = ($_GET['attack'] == "") ? "You didn't choose a pet to attack." : $error;
$error = ($_GET['attack'] == $user_class->id) ? "You can't attack your own pet." : $error;
$attack_person = new User($_GET['attack']);
$theirpet = new Pet($attack_person->id);
$error = ($attack_person->city != $user_class->city) ? "You must be in the same city as the pet you're attacking!" : $error;
$error = ($theirpet->hospital > 0) ? "You can't attack a pet thats is in hospital." : $error;
$error = ($theirpet->jail > 0) ? "You can't attack a pet that is in the pound." : $error;
$error = ($theirpet->hppercent < 25) ? "They Need Over 25% HP to be attacked." : $error;
$error = ($attack_person->admin == 1) ? "You can't attack an admin" : $error;
if (isset($error))
    diefun($error . "<br /><br /><a href='index.php'>Home</a>");
$yourhp = $mypet->hp;
$theirhp = $theirpet->hp;
genHead("Pet Fight");
print "<br />You are in a fight with " . $theirpet->formatName() . ".<br /><br />";
$userspeed = $mypet->speed;
$attackspeed = $theirpet->speed;
$wait = ($userspeed > $attackspeed) ? 1 : 0;
$number = 0;
while ($yourhp > 0 && $theirhp > 0) {
    $damage = round($theirpet->strength) - $mypet->defense;
    $damage = ($damage < 1) ? 1 : $damage;
    if ($wait == 0) {
        $yourhp = $yourhp - $damage;
        $number++;
        echo $number . ":&nbsp;" . $theirpet->formatName() . " hit you for " . prettynum($damage) . " damage. <br>";
    } else
        $wait = 0;
    if ($yourhp > 0) {
        $damage = round($mypet->strength) - $theirpet->defense;
        $damage = ($damage < 1) ? 1 : $damage;
        $theirhp = $theirhp - $damage;
        $number++;
        echo $number . ":&nbsp;" . "You hit " . $theirpet->formatName() . " for " . prettynum($damage) . " damage. <br>";
    }
}
$mypet->energy -= floor(($mypet->energy / 100) * 25);
if ($theirhp <= 0) {
    $expwon = 100 - (100 * ($mypet->level - $theirpet->level));
    $expwon = ($expwon < 10) ? 10 : $expwon;
    $expwon = ($expwon > 12000) ? 12000 : $expwon;
    $newexp = $expwon + $mypet->exp;
    $expwon1 = $expwon;
    perform_query("UPDATE pets SET exp = exp + ?, hp = ?, energy = ?, attacksWon = attacksWon + 1 WHERE userid = ?", [$expwon, $yourhp, $mypet->energy, $user_class->id]);
    perform_query("UPDATE pets SET hospital = 300, hp = 0, attacksLost = attacksLost + 1 WHERE userid = ?", [$attack_person->id]);
    Send_Event($attack_person->id, $mypet->formatName() . " attacked you and won! They gained " . prettynum($expwon) . " exp.");
    addToPetladder($mypet->id, 'attacks', 1);
    echo Message("You attacked " . $theirpet->formatName() . " and won! You gain " . prettynum($expwon) . " exp.");
}
if ($yourhp <= 0) {
    $expwon = 100 - (100 * ($theirpet->level - $mypet->level));
    $expwon = ($expwon < 10) ? 10 : $expwon;
    $expwon = ($expwon > 12000) ? 12000 : $expwon;
    $expwon2 = $expwon;
    perform_query("UPDATE pets SET exp = exp + ?, hp = ?, attacksWon = attacksWon + 1 WHERE userid = ?", [$expwon, $theirhp, $attack_person->id]);
    perform_query("UPDATE pets SET hospital = 300, hp = 0, energy = ?, attacksLost = attacksLost + 1 WHERE userid = ?", [$mypet->energy, $user_class->id]);
    Send_Event($attack_person->id, $mypet->formatName() . " attacked you and lost! You gained " . prettynum($expwon) . " exp.");
    echo Message($theirpet->formatName() . " won the battle!");
}
echo "</td></tr>";
include 'footer.php';
?>