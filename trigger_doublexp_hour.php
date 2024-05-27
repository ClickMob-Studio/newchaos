<?php
include 'header.php';

$tempItemUse = getItemTempUse($user_class->id);
$now = time();
if ($tempItemUse['gang_double_exp_time'] > $now) {
    diefun('You already have a gang double exp active.');
}

if ($user_class->gang < 1) {
    diefun('Your not in a gang.');
}

$newTime = time() + 3600;
addItemTempUse($user_class->id, 'gang_double_exp_time', $newTime);

mysql_query('UPDATE item_temp_use SET gang_double_exp_hour = gang_double_exp_hour - 1 WHERE user_id = ' . $user_class->id);


echo Message("You have activated 1 hour of Gang Double EXP time!");

include 'footer.php';