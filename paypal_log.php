<?php
include 'header.php';
if ($user_class->admin != 1 && $user_class->id != 9) {
echo Message("You are not authorized to be here!");
include("footer.php");
die();
}
$result = mysql_query("SELECT COUNT(*) FROM `ipn`");
$r = mysql_fetch_row($result);
$numrows = $r[0];
$rowsperpage = 30;
$totalpages = ceil($numrows / $rowsperpage);
if ($totalpages <= 0)
	$totalpages = 1;
else
	$totalpages = ceil($numrows / $rowsperpage);
if (isset($_GET['pnum']) && is_numeric($_GET['pnum']))
   $currentpage = (int) $_GET['pnum'];
else
   $currentpage = 1;
if ($currentpage > $totalpages)
   $currentpage = $totalpages;
if ($currentpage < 1)
   $currentpage = 1;
$offset = ($currentpage - 1) * $rowsperpage;
$result = mysql_query("SELECT SUM(paymentamount) total FROM ipn WHERE paymentamount != '0'");
$totalmoney = mysql_fetch_row($result)[0];
$totalmoney2 = round($totalmoney * 0.77);
?>
<tr><td class="contentspacer"></td></tr><tr><td class="contenthead">Paypal Logs</td></tr>
<tr><td class="contentcontent">
<table width='100%' style="table-layout:fixed; word-wrap:break-word; width: 100%;">
<tr><td align='center'><b>Total Money Made: $<?php echo $totalmoney ?> / £<?php echo $totalmoney2 ?></b></td></tr>
</table>
<table style="border-left: 1px solid #444444; border-top: 1px solid #444444;" cellspacing="0" cellpadding="2" width="100%" style="width:100%; overflow:hidden; word-wrap:break-word;">
<tr>
<td style="border-right: 1px solid #444444; border-bottom: 1px solid #444444; background-color: #222222;"><b>Buyer</b></td>
<td style="border-right: 1px solid #444444; border-bottom: 1px solid #444444; background-color: #222222;"><b>Package</b></td>
<td style="border-right: 1px solid #444444; border-bottom: 1px solid #444444; background-color: #222222;"><b>Amount Payed</b></td>
<td style="border-right: 1px solid #444444; border-bottom: 1px solid #444444; background-color: #222222;"><b>Buyer Email</b></td>
<td style="border-right: 1px solid #444444; border-bottom: 1px solid #444444; background-color: #222222;"><b>Date</b></td>
</tr>
<?php
$result = mysql_query("SELECT * FROM `ipn` ORDER BY `date` DESC LIMIT $offset, $rowsperpage");
if(mysql_num_rows($result) > 0) {
	while($line = mysql_fetch_array($result)) {
		$buyer = new User($line['user_id']);
		echo "<tr><td style='border-right: 1px solid #444444; border-bottom: 1px solid #444444;'>".$buyer->formattedname."</td><td style='border-right: 1px solid #444444; border-bottom: 1px solid #444444;'>".$line['creditsbought']." Credits</td><td style='border-right: 1px solid #444444; border-bottom: 1px solid #444444;'>$".$line['paymentamount'].".00 USD</td><td style='border-right: 1px solid #444444; border-bottom: 1px solid #444444;'>".$line['payeremail']."</td><td style='border-right: 1px solid #444444; border-bottom: 1px solid #444444;'>".date("d F Y, g:ia",$line['date'])."</td></tr>";
	}
	echo "</table>";
} else {
	echo "</table>";
	echo "<br />There are no payment logs.";
}
echo "<br />";
$range = 10;
if ($currentpage > 1)
   echo " <a href='{$_SERVER['PHP_SELF']}?pnum=1'><<</a> ";
for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++)
   	if (($x > 0) && ($x <= $totalpages))
  		if ($x == $currentpage)
 			echo " [<b>$x</b>] ";
  		else
 			echo " <a href='{$_SERVER['PHP_SELF']}?pnum=$x'>$x</a> ";
if ($currentpage < $totalpages) 
   echo " <a href='{$_SERVER['PHP_SELF']}?pnum=$totalpages'>>></a> ";
?>
</td></tr>
<?php
include 'footer.php';
?>

