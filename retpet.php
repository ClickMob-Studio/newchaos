<?php
include "header.php";
$q = mysql_query("SELECT * FROM pets WHERE loaned = $user_class->id");
$r = mysql_fetch_assoc($q);
if ($r['userid']) {
    perform_query("UPDATE pets SET userid = ?, loaned = 0 WHERE loaned = ?", [$user_class->id, $user_class->id]);
    Send_Event($r['userid'], formatName($user_class->id) . " has taken back their pet.");
    diefun("You have taken back your pet.");
} else {
    diefun("Sorry, you do not have any pets loaned out.");
}
include "footer.php";
?>