<?php

(PHP_SAPI !== 'cli' || isset($_SERVER['HTTP_USER_AGENT'])) && die('');
$link = mysql_connect('127.0.0.1', 'chaoscity_co', '3lrKBlrfMGl2ic14');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("game");

// include 'dbcon.php';
// include 'classes.php';
// include 'database/pdo_class.php';
// $result = mysql_query("SELECT * FROM `grpgusers`");
// while ($line = mysql_fetch_assoc($result)) {
//     $updates_user = new User($line['id']);
//     $result = mysql_query("SELECT `id` FROM `grpgusers`") or die(mysql_error());
//     while ($line = mysql_fetch_assoc($result)) {
//         $updates_user = new User($line['id']);
//         if ($updates_user->rmdays > 0) {
//             if ($updates_user->donations >= 200)
//                 $mul = .4;
//             elseif ($updates_user->donations >= 100)
//                 $mul = .35;
//             elseif ($updates_user->donations >= 50)
//                 $mul = .3;
//             else
//                 $mul = .2;
//         } else {
//             if ($updates_user->donations >= 200)
//                 $mul = .35;
//             elseif ($updates_user->donations >= 100)
//                 $mul = .3;
//             elseif ($updates_user->donations >= 50)
//                 $mul = .25;
//             else
//                 $mul = .15;
//         }
//         if ($updates_user->lastactive > time() - 3600)
//             mysql_query("INSERT INTO snapshot VALUES('',$updates_user->id,$updates_user->level,$updates_user->money,$updates_user->bank,$updates_user->points,$updates_user->rmdays,$updates_user->apoints,unix_timestamp())");
//         mysql_query("UPDATE grpgusers SET awake = LEAST(awake + ($updates_user->maxawake * $mul),$updates_user->maxawake),
//          energy = LEAST(energy + ($updates_user->maxenergy * $mul),$updates_user->maxenergy),
//          nerve = LEAST(nerve + ($updates_user->maxnerve * $mul),$updates_user->maxnerve),
//          hp = LEAST(hp + ($updates_user->maxhp * .25),$updates_user->maxhp)
//          WHERE `id` = $updates_user->id");
//     }
// }
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
