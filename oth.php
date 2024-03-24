<?php
include "header.php";
?>
<table id="newtables" class="altcolors">
<tr>
    <th>Record</th>
    <th>Mobster</th>
    <th>Amount</th>
</tr>
<?php

$admins = [1,2, 174];
$admin_ids = implode(',', $admins);

$mostoths = mysql_fetch_array(mysql_query("SELECT count(*) as count,userid FROM oth WHERE userid NOT IN ($admin_ids) AND id > 3 GROUP BY userid ORDER BY count DESC"));
$mostloths = mysql_fetch_array(mysql_query("SELECT count(*) as count,userid FROM oth WHERE userid NOT IN ($admin_ids)  AND id > 3 AND type = 'leveler'  GROUP BY userid ORDER BY count DESC"));
$mostkoths = mysql_fetch_array(mysql_query("SELECT count(*) as count,userid FROM oth WHERE userid NOT IN ($admin_ids)  AND id > 3 AND type = 'killer'  GROUP BY userid ORDER BY count DESC"));
$highestloths = mysql_fetch_array(mysql_query("SELECT userid,amnt FROM oth WHERE userid NOT IN ($admin_ids)  AND id > 3 AND type = 'leveler' ORDER BY amnt DESC"));
$highestkoths = mysql_fetch_array(mysql_query("SELECT userid,amnt FROM oth WHERE userid NOT IN ($admin_ids)  AND id > 3 AND type = 'killer' ORDER BY amnt DESC"));
print"
<tr>
    <td>Most OTHs</td>
    <td>".formatName($mostoths['userid'])."</td>
    <td>".number_format($mostoths['count'])."</td>
</tr>
<tr>
    <td>Most LOTHs</td>
    <td>".formatName($mostloths['userid'])."</td>
    <td>".number_format($mostloths['count'])."</td>
</tr>
<tr>
    <td>Most KOTHs</td>
    <td>".formatName($mostkoths['userid'])."</td>
    <td>".number_format($mostkoths['count'])."</td>
</tr>
<tr>
    <td>Highest LOTH</td>
    <td>".formatName($highestloths['userid'])."</td>
    <td>".number_format($highestloths['amnt'])." EXP</td>
</tr>
<tr>
    <td>Highest KOTH</td>
    <td>".formatName($highestkoths['userid'])."</td>
    <td>".number_format($highestkoths['amnt'])." Kills</td>
</tr>
";
?>
</table>
<table  id="newtables" class="altcolors">
    <tr>
        <th>Mobster</th>
        <th>OTH</th>
        <th>Amount</th>
        <th>Time</th>
    </tr>
<?php
$oths = mysql_query("SELECT * FROM oth WHERE userid NOT IN ($admin_ids) AND id > 3 ORDER BY timestamp DESC LIMIT 100");
while ($oth = mysql_fetch_array($oths)) {
    $type = ($oth['type'] == "leveler") ? "EXP" : "Kills";
    print "
    <tr>
        <td>" . formatName($oth['userid']) . "</td>
        <td>" . ucfirst($oth['type']) . "</td>
        <td>" . number_format($oth['amnt']) . " $type</td>
        <td>" . date('g A', $oth['timestamp']) . "</td>
    </tr>
";
}
print "
</table>
";
include "footer.php";
?>