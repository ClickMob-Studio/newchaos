<?php
include 'header.php';
?>
    
	<tr><td class="contentspacer"></td></tr><tr><td class="contenthead"><?php echo $user_class->formattedname; ?></td></tr>
	<tr><td class="contentcontent">
    <?php
	
    $bar_class = new User($user_class->id);
	
if($bar_class->hppercent >= 75) {
$colour = "#00DD00";
} else {
$colour = "#BB0000";
}
if((time() - $bar_class->lastactive) < 600) {
$colour1 = "#005500";
$colour2 = "#006600";
} else {
$colour1 = "#550000";
$colour2 = "#660000";
}

echo '<center><table width="450px" class="userbar" cellspacing="0" cellpadding="3"><tr><td width="40%">' . $bar_class->formattedname . '</td><td style="border-left: 1px solid #444444;" width="15%">LVL:&nbsp;' . $bar_class->level . '</td><td style="border-left: 1px solid #444444;" width="15%">HP:&nbsp;<font color="' . $colour . '">' . $bar_class->hppercent . '%</font></td><td style="border-left: 1px solid #444444;" width="26%"><a href="bus.php">' . $bar_class->cityname . '</a></td><td align="center" style="border-left: 1px solid #444444; background-color:' . $colour1 . ';" width="4%"><div style="background-color:' . $colour2 . ';">&nbsp;&nbsp;</div></td></tr></table><table width="450px" class="userbar2" cellspacing="0" cellpadding="3"><tr><td width="20%" align="center">[<a href="attack.php?attack='.$bar_class->id.'">attack</a>]</td><td style="border-left: 1px solid #444444;" width="20%" align="center">[<a href="mug.php?mug='.$bar_class->id.'">mug</a>]</td><td style="border-left: 1px solid #444444;" width="20%" align="center">[<a href="spy.php?id='.$bar_class->id.'">spy</a>]</td><td style="border-left: 1px solid #444444;" width="20%" align="center">[<a href="profiles.php?id='.$bar_class->id.'&contact=friend">friend</a>]</td><td style="border-left: 1px solid #444444;" width="20%" align="center">[<a href="profiles.php?id='.$bar_class->id.'&contact=enemy">enemy</a>]</td></tr></table></center>';

	?>
    </td></tr>
    
<?php
include 'footer.php';
?>