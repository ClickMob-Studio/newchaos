<?php
include 'header.php';
?>

<div class='box_top'>Gang Crime Log</div>
<div class='box_middle'>
    <div class='pad'>
        <?php

        if ($user_class->gang != 0) {
            $gang_class = new Gang($user_class->gang);
            $db->query("SELECT * FROM gcrimelog WHERE gangid = ? ORDER BY timestamp DESC");
            $db->execute([$gang_class->id]);
            $results = $db->fetch_row();
            if (!empty($results)) {
                ?>
                <table id="newtables" style="width:100%;table-layout:fixed;">
                    <tr>
                        <th colspan="4">Gang Crime Log</th>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <th>Reward</th>
                        <th>Starter</th>
                        <th>Time</th>
                    </tr>
                    <?php

                    foreach ($results as $row) {
                        if ($row['gangid'] == $gang_class->id) {
                            $extra = new User($row['userid']);
                            echo "
    <tr>
        <td>" . $row['text'] . "</td>
        <td>" . $row['reward'] . "</td>
        <td>" . $extra->formattedname . "</td>
        <td>" . date("d M Y, g:ia", $row['timestamp']) . "</td>
    </tr>
            ";
                        }
                    }
                    ?>
                </table>
                </td>
                </tr>
                <?php
            } else {
                echo 'No logs found';
            }
        } else {
            echo Message("You aren't in a gang.");
        }
        include("gangheaders.php");
        include 'footer.php';
        ?>