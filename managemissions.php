<?php
include"gmheader.php";
echo'<div class="floaty" style="margin:2px;">Manage Missions</div>';
echo'<div class="flexcont">';
	echo'<div class="flexele floatylinks" onclick="addmission();">Add Mission</div>';
	echo'<div class="flexele floatylinks">Edit Mission</div>';
	echo'<div class="flexele floatylinks">Delete Mission</div>';
echo'</div>';
echo'<div id="rtn"></div>';
echo'<div id="errorsuccess"></div>';
include"footer.php";
?>