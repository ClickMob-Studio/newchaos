<?php
include 'header.php';
?>

<tr valign="top"><td class="contentspacer"></td></tr><tr valign="top"><td class="contenthead">Exp Guide</td></tr>
<tr valign="top"><td class="contentcontent">
<table width="100%" border="0">
<tr valign="top">
<td>

<table width="33%">
<tr valign="top">
<td width="13%"><b>Level</b></td>
<td width="20%"><b>Exp</b></td>
</tr>
<?php
for($x; $x<201; $x++) {
$exp = GangExperience($x);
echo "<tr valign='top'><td width='13%'>".$x."</td><td width='20%'>".prettynum($exp)."</td></tr>";
}
?>
</table>

</td>
<td>
<table width="33%">
<tr valign="top">
<td width="13%"><b>Level</b></td>
<td width="20%"><b>Exp</b></td>
</tr>
<?php
for($x; $x<401; $x++) {
$exp = GangExperience($x);
echo "<tr valign='top'><td width='13%'>".$x."</td><td width='20%'>".prettynum($exp)."</td></tr>";
}
?>
</table>

</td>
<td>
<table width="33%">
<tr valign="top">
<td width="13%"><b>Level</b></td>
<td width="20%"><b>Exp</b></td>
</tr>
<?php
for($x; $x<501; $x++) {
$exp = GangExperience($x);
echo "<tr valign='top'><td width='13%'>".$x."</td><td width='20%'>".prettynum($exp)."</td></tr>";
}
?>
</table>

</td>


<?php
include("footer.php");
?>