<?php
include 'header.php';
if (isset($_GET['delete'])) {
    perform_query("UPDATE attacklog SET attackerhide = 1 WHERE attacker = ?", [$user_class->id]);
}

$db->query("SELECT * FROM attack_v2 WHERE attacking_user_id = " . $user_class->id . " OR defending_user_id = " . $user_class->id);
$db->execute();
$attacks = $db->fetch_row();
?>
<div class="box_top">Attacks</div>
<div class="box_middle">
    <div class="pad">
        <table id="newtables" style="width:100%;">
            <tr>
                <th>Time</td>
                <th>Attacker</th>
                <th>Defender</th>
                <th>Winner</th>
                <th>View Logs</th>
            </tr>
            <?php foreach ($attacks as $attack): ?>
                <tr>
                    <td>

                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<div class="box_top">Attack Logs</div>
<div class="box_middle">
    <div class="pad">
        <tr>
            <td class='contentcontent'>
                <center><a href="?delete"><button class="ycbutton">Delete Attack Log</button></a></center>
                <table id="newtables" style="width:100%;">
                    <tr>
                        <th>Time</b>
            </td>
            <th>Attacker</th>
            <th>Defender</th>
            <th>Winner</th>
            <th>Online?</th>
        </tr>
        <?php
        $start = isset($_GET['page']) ? ($_GET['page'] - 1) * 50 : 0;

        $db->query("SELECT * FROM attacklog WHERE attacker = ? AND attackerHide = 0 ORDER BY timestamp DESC LIMIT $start, 50");
        $db->execute([$user_class->id]);
        $result = $db->fetch_row();
        foreach ($result as $row) {
            $active = ($row['active'] == "1") ? "<span style='color:green;'>Online</span>" : "<span style='color:red;'>Offline</span>";
            $time = date("F d, Y g:ia", $row['timestamp']);
            echo "
    <tr>
        <td width='28%'>$time</td>
        <td width='20%'>" . formatName($row['attacker']) . "</td>
        <td width='20%'>" . formatName($row['defender']) . "</td>
        <td width='20%'>" . formatName($row['winner']) . "</td>
        <td>$active</td>
    </tr>
    ";
        }
        echo "</table>";

        $db->query("SELECT COUNT(*) FROM attacklog WHERE attacker = ? AND attackerHide = 0");
        $db->execute([$user_class->id]);
        $count = $db->fetch_single();
        $count = (($count / 50) > 30) ? 30 : ($count / 50);
        for ($i = 1; $i <= $count; $i++) {
            if ($i == 1)
                print "Pages: ";
            if ($i == $_GET['page'])
                print "<b>";
            print " <a href='?page=$i'>[$i]</a> ";
            if ($i == $_GET['page'])
                print "</b>";
        }
        print "</td></tr></div></div>";
        include 'footer.php';
        ?>