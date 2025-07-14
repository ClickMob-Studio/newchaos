<?php
include 'header.php';
?>
<style>
    #attacklog {
        widt: 75%;
        text-align: center;
    }

    #attacklog th {
        text-align: center;
        height: 25px;
        border: #333 solid thin;
        background: rgba(0, 0, 0, .25);
        font-weight: bold;
        font-size: 1.1em;
    }

    #attacklog td {
        text-align: center;
        height: 25px;
        border: #333 solid thin;
        background: rgba(0, 0, 0, .125);
    }

    #attacklog a {
        color: red;
    }
</style>
<div class="box_top">Mug Logs</div>
<div class="box_middle">
    <div class="pad">
        <tr>
            <td class='contentcontent'>
                <table width="100%" id="attacklog">
                    <tr>
                        <th>Time</b>
            </td>
            <th>Mugger</th>
            <th>Mugged</th>
            <th>Amount</th>
            <th>Online?</th>
        </tr>
        <?php
        $start = isset($_GET['page']) ? ($_GET['page'] - 1) * 50 : 0;
        $db->query("SELECT * FROM muglog WHERE mugger = ? OR mugged = ? ORDER BY timestamp DESC LIMIT 0, 50");
        $db->execute(array(
            $user_class->id,
            $user_class->id
        ));
        $rows = $db->fetch_row();
        foreach ($rows as $row) {
            $active = ($row['active'] == "1") ? "<span style='color:green;'>Online</span>" : "<span style='color:red;'>Offline</span>";
            $time = date("F d, Y g:ia", $row['timestamp']);
            echo "
					<tr>
						<td width='28%'>$time</td>
						<td width='20%'>" . formatName($row['mugger']) . "</td>
						<td width='20%'>" . formatName($row['mugged']) . "</td>
						<td width='20%'>" . prettynum($row['amount']) . "</td>
						<td>$active</td>
					</tr>";
        }
        echo "</table>";

        $db->query("SELECT COUNT(*) FROM muglog WHERE mugger = ? OR mugged = ?");
        $db->execute([$user_class->id, $user_class->id]);
        $count = $db->fetch_single();
        $count = (($count / 10) > 30) ? 30 : ($count / 10);
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