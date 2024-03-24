<?php
include 'gmheader.php';

?>
<tr><td class="contentspacer"></td></tr><tr><td class="contenthead">View PM's</td></tr>
<tr><td class="contentcontent">
<table width='100%'>
<?php
$result = mysql_query("SELECT * from `maillog` WHERE `id`='".$_GET['id']."'");
$row = mysql_fetch_array($result);
     $from_user_class = new User($row['from']);
if ($_GET['id'] != ""){
$textyy = BBCodeParse(strip_tags($row['msgtext']));
    echo "
						<tr>
							<td width='15%'><b>Subject:</b></td>
							<td width='45%'>".$row['subject']."</td>
							<td width='15%'><b>Sender:</b></td>

							<td width='25%'>".$from_user_class->formattedname."</td>
						</tr>
						<tr>
							<td><b>Recieved:</b></td>
							<td colspan='3'>".date(F." ".d.", ".Y." ".g.":".i.":".sa,$row['timesent'])."</td>
						</tr>
						<tr>
							<td colspan='3' class='textm'><br>". $textyy ."
					    	</td>
						</tr>

";
	}
?>

</table>
</td></tr>
<?php
include 'footer.php';
?>