<?php

(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die('');
$link = mysql_connect('127.0.0.1', 'chaoscity_co', '3lrKBlrfMGl2ic14');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("game");

mysql_query("UPDATE pets
SET
nerve = LEAST(
    LEVEL +4,
    CEIL((nerve +((LEVEL +4) * .2)))
),
hp = LEAST(
    LEVEL * 50,
    CEIL((hp +((LEVEL * 50) * .25)))
),
energy = LEAST(
    LEVEL +9,
    CEIL((energy +((LEVEL +9) * .2)))
),
awake = LEAST(
    CEIL(awake +(maxawake * .2)),
    maxawake
)", $link) or mysql_error();

$result = mysql_query("SELECT `id` FROM `grpgusers` WHERE `is_jail_bot` = 1");
while ($line = mysql_fetch_array($result)) {
    mysql_query("UPDATE `grpgusers` SET `jail` = 300 WHERE `id` = " . $line['id']);

    Send_Event(2, "Jail Bots Ran");
}

print "worked";
mysql_close($link);
