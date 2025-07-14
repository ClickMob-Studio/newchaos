<?php
include 'header.php';
$who = new User($_GET['uid']);
if ($user_class->energy <= 0)
    diefun("You don't have any energy!");

$db->query("SELECT * FROM contactlist WHERE playerid = ? AND contactid = ? AND type = 1");
$db->execute([$user_class->id, $who->id]);
$row = $db->fetch_row(true);
if (!$row)
    diefun("They do not have you on their friend's list :(.");

echo Message("You have given a high five to " . $who->formattedname . ".");
perform_query("UPDATE grpgusers SET energy = energy + 1 WHERE id = ?", [$who->id]);
perform_query("UPDATE grpgusers SET energy = energy - 1 WHERE id = ?", [$user_class->id]);
Send_Event($who->id, $user_class->formattedname . " Has high fived you! [+1 Energy]");

include 'footer.php';