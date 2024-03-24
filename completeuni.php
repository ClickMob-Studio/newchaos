<?php
include("header.php");
$db->query("SELECT * FROM uni WHERE playerid = ?");
$db->execute(array(
    $user_class->id
));
$uni = $db->fetch_row(true);
if (!empty($uni)) {
    $db->query("SELECT * FROM courses WHERE id = ?");
    $db->execute(array(
        $uni['courseid']
    ));
    $core = $db->fetch_row(true);
    if (time() >= $uni['finish']) {
        $newstrength = $user_class->strength + $core['strength'];
        $newdefense = $user_class->defense + $core['defense'];
        $newspeed = $user_class->speed + $core['speed'];
        $newgcse = $user_class->gcse + $core['gcse'];
        $db->query("UPDATE grpgusers SET strength = ?, defense = ?, speed = ?, gcses = ? WHERE id = ?");
        $db->execute(array(
            $newstrength,
            $newdefense,
            $newspeed,
            $newgcse,
            $user_class->id
        ));
        $db->query("DELETE FROM uni WHERE playerid = ?");
        $db->execute(array(
            $user_class->id
        ));
        echo Message("You have successfully completed your course at the university and your statistics have been added.");
    } else
        echo Message("Your course hasn't finished yet.");
} else
    echo Message("You don't have a course running at the university.");
include("footer.php");
?>