<?php
include 'header.php';
$who = new User($_GET['uid']);
if ($user_class->energy <= 0)
    diefun("You don't have any energy!");
$exist = mysql_fetch_array(mysql_query("SELECT * FROM contactlist WHERE playerid = $who->id AND contactid = $user_class->id AND type = 1"));
if (!$exist)
    diefun("They do not have you on their friend's list :(.");
echo Message("You have given a high five to " . $who->formattedname . ".");
perform_query("UPDATE grpgusers SET energy = energy + 1 WHERE id = ?", [$who->id]);
perform_query("UPDATE grpgusers SET energy = energy - 1 WHERE id = ?", [$user_class->id]);
Send_Event($who->id, $user_class->formattedname . " Has high fived you! [+1 Energy]");
include 'footer.php';
?>