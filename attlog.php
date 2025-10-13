<?php
include 'header.php';
?>

<div class='box_top'>Attack Log</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if ($user_class->gang != 0) {
            $gang_class = new Gang($user_class->gang);
            if (isset($_GET['delete'])) {
                perform_query("DELETE FROM attlog WHERE gangid = ?", [$gang_class->id]);
                echo Message('You have delete the attack log');
            }
            $start = isset($_GET['page']) ? ($_GET['page'] - 1) * 30 : 0;

            $db->query("SELECT * FROM attlog WHERE gangid = ? ORDER BY timestamp DESC LIMIT $start, 30");
            $db->execute([$gang_class->id]);
            $result = $db->fetch_row();

            if (!empty($result)) {
                ?>
                <center><a href="?delete"><button class="ycbutton">Delete Attack Log</button></a></center>
                <table id="newtables" style="width:100%;">
                    <tr>
                        <th>Time</th>
                        <th>Attacker</th>
                        <th>Defender</th>
                        <th>Winner</th>
                        <th>Gang EXP</th>
                        <th>Online?</th>
                        <th>Respect</th>
                    </tr>
                    <?php

                    foreach ($result as $row) {
                        $attacker = new User($row['attacker']);
                        $defender = new User($row['defender']);
                        $winner = new User($row['winner']);
                        $active = ($row['active'] == "1") ? "<font color=green>[Online]</font>" : "<font color=red>[Offline]</font>";
                        echo "
    <tr>
        <td width='28%'>" . date("M d, Y g:ia", $row['timestamp']) . "</td>
        <td width='20%'>" . $attacker->formattedname . "</td>
        <td width='20%'>" . $defender->formattedname . "</td>
        <td width='20%'>" . $winner->formattedname . "</td>
        <td width='12%'>" . prettynum($row['gangexp']) . "</td>
        <td>" . $active . "</td>
        <td>" . (($row['respect'] == 0) ? 0 : (($row['attacker'] == $row['winner']) ? '+' : '-') . $row['respect']) . "</td>
    </tr>
        ";
                    }
                    echo "</table>";

                    $db->query("SELECT COUNT(*) FROM attlog WHERE gangid = ?");
                    $db->execute([$gang_class->id]);
                    $count = $db->fetch_single();
                    $count = (($count / 30) > 30) ? 30 : ($count / 30);

                    for ($i = 1; $i <= $count; $i++) {
                        if ($i == 1)
                            print "Pages: ";
                        if (isset($_GET['page']) && $i == $_GET['page'])
                            print "<b>";
                        print " <a href='?page=$i'>[$i]</a> ";
                        if (isset($_GET['page']) && $i == $_GET['page'])
                            print "</b>";
                    }
                    print "</td></tr>";
            } else {
                echo 'No logs found';
            }
        } else {
            echo Message("You aren't in a gang.");
        }
        include("gangheaders.php");
        include 'footer.php';
        ?>