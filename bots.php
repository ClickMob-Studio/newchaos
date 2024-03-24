<?php
include 'header.php';
?>
<table id="newtables" style="width:90%;">
    <tr>
        <th>Bot Name</th>
        <th>Level</th>
        <th>Hospital</th>
        <th>Attack</th>
        <th>Money</th>
        <th>Mug</th>
    </tr>
    <?php
    $bots = mysql_query("SELECT id,hospital,money FROM grpgusers WHERE id > 404 AND id < 426 ORDER BY hospital ASC, id ASC");
    while ($bot = mysql_fetch_array($bots)) {
        $level = $bot['id'] - 410 + $user_class->level;
        $attack = ($bot['hospital'] != 0) ? "Dead" : "<a href='attack.php?attack={$bot['id']}' style='color:orange;'>[Attack]</a>";
        $mug = ($bot['hospital'] != 0) ? "Dead" : "<a href='mug.php?mug={$bot['id']}' style='color:orange;'>[Mug]</a>";
        print"
    <tr>
        <td>" . formatName($bot['id']) . "</td>
        <td>$level</td>
        <td>" . number_format($bot['hospital'] / 60) . " Minutes.</td>
        <td><span style='color:orange;font-weight:bold;'>$attack</span></td>
        <td>" . prettynum($bot['money'], 1) . "</td>
        <td><span style='color:orange;font-weight:bold;'>$mug</span></td>
    </tr>
";
    }
    print"
</table>
";
    include 'footer.php';
    ?>