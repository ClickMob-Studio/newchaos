<?php
include 'header.php';
?>
<div class='box_top'>View Pm</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $db->query("SELECT * FROM bans WHERE type = 'mail' AND id = ?");
        $db->execute(array(
            $user_class->id
        ));

        $r = $db->fetch_row(true);
        if (!empty($r))
            diefun('&nbsp;You have been mail banned for ' . prettynum($r['days']) . ' days.');
        $db->query("SELECT * FROM pms WHERE id = ?");
        $db->execute(array(
            $_GET['id']
        ));


        $row = $db->fetch_row(true);
        if ($row['bomb'] == 1 && $row['bombed'] == 0) {
            $user_class->hospital += 1200;
            $db->query("UPDATE grpgusers SET hospital = ?, hhow = ?, hwho = ? WHERE id = ?");
            $db->execute(array(
                $user_class->hospital,
                'mbomb',
                $row['from'],
                $user_class->id
            ));
            $db->query("UPDATE pms SET bombed = 1 WHERE id = ?");
            $db->execute(array(
                $row['id']
            ));
            echo Message("<span style='color:red;'>You have been hit by a mail bomb and have been sent to hospital for 20 minutes.</span>");
        } else if ($row['bomb'] == 2 && $row['bombed'] == 0) {
            $user_class->hospital += 2400;
            $db->query("UPDATE grpgusers SET hospital = ?, hhow = ?, hwho = ? WHERE id = ?");
            $db->execute(array(
                $user_class->hospital,
                'mbomb',
                $row['from'],
                $user_class->id
            ));
            $db->query("UPDATE pms SET bombed = 1 WHERE id = ?");
            $db->execute(array(
                $row['id']
            ));
            echo Message("<span style='color:red;'>You have been hit by a mail bomb and have been sent to hospital for 40 minutes.</span>");
        }
        print mailHeader() . "
        <table id='newtables' style='width:100%;table-layout:fixed;'>";
        if (!empty($_GET['id'])) {
            if ($row['to'] == $user_class->id) {
                $string = strip_tags($row['msgtext']);
                $output = BBCodeParse($string);
                $name = ($row['from'] == 0000) ? "<b><i>Auto Mail</i></b>" : formatName($row['from']);
                echo "
            <tr>
                <th>" . $name . "</th>
                <th>{$row['subject']}</th>
                <th>" . date("F d, Y g:i:sa", $row['timesent']) . "</th>
            </tr>
            <tr>
                <td colspan='3'><br /><br />$output<br /><br /><br /></td>
            </tr>
        </table>
        <table id='newtables' class='linkstable' style='width:100%;'>
            <tr>
                <td><a href='pms.php?delete={$row['id']}'>Delete</a></td>
                <td><a href='pms.php?view=new&reply={$row['id']}#new'>Reply</a></td>
                <td><a href='pms.php?report={$row['id']}'>Report</a></td>
            </tr>
        </table>";
                $db->query("UPDATE pms SET viewed = 2 WHERE id = ? AND viewed <> 2");
                $db->execute([$row['id']]);
                $didUpdate = $db->affected_rows();
                if ($didUpdate > 0) {
                    decrease_pm_count($user_class->id);
                }

                print "<table id='newtables' style='width:100%;'>";
                $db->query("SELECT * FROM pms WHERE id != ? AND `to` IN (?, ?) AND `from` IN (?, ?) AND `to` <> `from` ORDER BY timesent DESC LIMIT 10");
                $db->execute(array(
                    $_GET['id'],
                    $user_class->id,
                    $row['from'],
                    $user_class->id,
                    $row['from']
                ));
                $rows = $db->fetch_row();
                if (count($rows) > 0) {
                    print "
                <tr>
                    <th colspan='6' style='background:none;border:none;'><br /></th>
                </tr>
                <tr>
                    <th colspan='6'>Mail Log</th>
                </tr>";
                    foreach ($rows as $row)
                        echo "
                <tr>
                    <td style='width:20%;'>", ($row['from'] == $user_class->id) ? $user_class->formattedname : $name, "</td>
                    <td style='width:70%;'>" . BBCodeParse(strip_tags($row['msgtext'])) . "</td>
                </tr>";
                    print "
                    </table>
                 </td>
            </tr>";
                }
            }
        }
        ?>
        </table>
        <?php
        include 'footer.php';
        ?>