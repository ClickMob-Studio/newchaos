<?php
include "header.php";
?>

<div class='box_top'>Hourly HOF</div>
<div class='box_middle'>
    <div class='pad'>

        <table id="newtables" class="altcolors">
            <tr>
                <th>Record</th>
                <th>Mobster</th>
                <th>Amount</th>
            </tr>
            <?php

            $admins = [1034, 1059];
            $admin_ids = implode(',', $admins);

            $db->query("SELECT count(*) as count,userid FROM oth WHERE userid NOT IN ($admin_ids) GROUP BY userid ORDER BY count DESC");
            $db->execute();
            $mostoths = $db->fetch_row(true);

            $db->query("SELECT count(*) as count,userid FROM oth WHERE userid NOT IN ($admin_ids) AND type = 'leveler'  GROUP BY userid ORDER BY count DESC");
            $db->execute();
            $mostloths = $db->fetch_row(true);

            $db->query("SELECT count(*) as count,userid FROM oth WHERE userid NOT IN ($admin_ids) AND type = 'killer'  GROUP BY userid ORDER BY count DESC");
            $db->execute();
            $mostkoths = $db->fetch_row(true);

            $db->query("SELECT userid,amnt FROM oth WHERE userid NOT IN ($admin_ids) AND type = 'leveler' ORDER BY amnt DESC");
            $db->execute();
            $highestloths = $db->fetch_row(true);

            $db->query("SELECT userid,amnt FROM oth WHERE userid NOT IN ($admin_ids) AND type = 'killer' ORDER BY amnt DESC");
            $db->execute();
            $highestkoths = $db->fetch_row(true);
            print "
<tr>
    <td>Most OTHs</td>
    <td>" . (isset($mostoths) ? formatName($mostoths['userid']) : 'N/A') . "</td>
    <td>" . (isset($mostoths) ? number_format($mostoths['count']) : '0') . "</td>
</tr>
<tr>
    <td>Most LOTHs</td>
    <td>" . (isset($mostloths) ? formatName($mostloths['userid']) : 'N/A') . "</td>
    <td>" . (isset($mostloths) ? number_format($mostloths['count']) : '0') . "</td>
</tr>
<tr>
    <td>Most KOTHs</td>
    <td>" . (isset($mostkoths) ? formatName($mostkoths['userid']) : 'N/A') . "</td>
    <td>" . (isset($mostkoths) ? number_format($mostkoths['count']) : '0') . "</td>
</tr>
<tr>
    <td>Highest LOTH</td>
    <td>" . (isset($highestloths) ? formatName($highestloths['userid']) : 'N/A') . "</td>
    <td>" . (isset($highestloths) ? number_format($highestloths['amnt']) : '0') . " EXP</td>
</tr>
<tr>
    <td>Highest KOTH</td>
    <td>" . (isset($highestkoths) ? formatName($highestkoths['userid']) : 'N/A') . "</td>
    <td>" . (isset($highestkoths) ? number_format($highestkoths['amnt']) : '0') . " Kills</td>
</tr>
";
            ?>
        </table>
        <table id="newtables" class="altcolors">
            <tr>
                <th>Mobster</th>
                <th>OTH</th>
                <th>Amount</th>
                <th>Time</th>
            </tr>
            <?php
            $db->query("SELECT * FROM oth WHERE userid NOT IN ($admin_ids) ORDER BY timestamp DESC LIMIT 100");
            $db->execute();
            $oths = $db->fetch_row();
            foreach ($oths as $oth) {
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