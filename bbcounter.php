<?php
include("header.php");
if (isset($_POST['resetref'])) {
	perform_query("UPDATE `grpgusers` SET `newkillswon` = '0' , `newkillslost` = '0' ,`newdefendswon` = '0', `newdefendslost` = '0',  `newdonator` = '0',`newhospital123` = '0' ,`newbusts` = '0'");
	echo Message("The bb counters have been reset.");
}
if (isset($_POST['resetexp'])) {
	perform_query("UPDATE `grpgusers` SET `newkillswon` = '0' , `newkillslost` = '0' ,`newdefendswon` = '0', `newdefendslost` = '0',  `newdonator` = '0',`newhospital123` = '0' ,`newbusts` = '0' WHERE `id`= ?", [$line['id']]);
	echo Message("All Bloodbath counters have been reset.");
}
?>
<form method="post"><input type="submit" name="resetexp" value="Reset" size="5" /></form>

<?php

include("footer.php");

?>