<?php
include"header.php";
echo'<div class="collegebox">';
	echo'<div class="floaty" style="margin:0 auto;">Back Alley</div>';
	echo'<div class="collegebox" style="margin:25px;width:66%;">';
		echo'<img src="./images/backalley.png" width="302" height="200" /><br />';
		echo'<br />';
		echo'&bull; <span style="color:red;">Welcome to the Back Alley!</span><br />';
		echo'<span style="color:red;">&bull;</span>You will battle against different opponents.<br />';
		echo'&bull;&nbsp;<span style="color:red;">But will you take the risk, when its 20% energy per attack?</span><br />';
		echo'<span style="color:red;">&bull;</span> If you fail, you will find yourself in the hospital<br />';
	echo'</div>';
	echo'<div style="clear:both;"></div>';
	echo'<button onclick="doBA();">Attack!</button>';
echo'</div>';
echo'<div id="rtn"></div>';
include"footer.php";
?>