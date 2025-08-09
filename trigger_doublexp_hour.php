<?php
include 'header.php';

$tempItemUse = getItemTempUse($user_class->id);
$now = time();
if ($tempItemUse['gang_double_exp_time'] > $now) {
    diefun('You already have a gang double exp active.');
}
if ($tempItemUse['gang_double_exp_hours'] < 1) {
    diefun('You do not have any double exp hours remaining to run.');
}


$db->query("SELECT * FROM gamebonus WHERE ID = 1 LIMIT 1");
$db->execute();
$bonus_row = $db->fetch_row(true);

if ($bonus_row['Time'] > 0) {
    diefun('You can\'t trigger a gang double EXP with a server wide double EXP.');

}

if ($user_class->gang < 1) {
    diefun('Your not in a gang.');
}

$newTime = time() + 3600;
addItemTempUse($user_class, 'gang_double_exp_time', $newTime);
perform_query("UPDATE item_temp_use SET gang_double_exp_hours = gang_double_exp_hours - 1 WHERE user_id = ?", [$user_class->id]);

echo Message("You have activated 1 hour of Gang Double EXP time!");

include 'footer.php';