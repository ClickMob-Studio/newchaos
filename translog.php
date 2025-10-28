<?php
include "header.php";
include 'includes/pagination.class.php';
$pages = new pagination();
$pages->items_per_page = 30;
$pages->max_pages = 10;
$db->query("SELECT * FROM transferlog WHERE `to` = ? OR `from` = ?");
$db->execute(array(
    $user_class->id,
    $user_class->id
));
$pages->items_total = $db->fetch_single();


?>

<div class="box_top">Transaction Logs</div>
<div class="box_middle">
    <div class="pad">
        <?php
        print "
        <table id='newtables' class='altcolors' style='width:100%;'>
            <tr>
                <th>Time</th>
                <th>From</th>
                <th>To</th>
                <th>What?</th>
            </tr>";
        $db->query("SELECT * FROM transferlog WHERE `to` = ? OR `from` = ? ORDER BY timestamp DESC" . $pages->limit());
        $db->execute(array(
            $user_class->id,
            $user_class->id
        ));
        if (!$db->num_rows())
            echo "<tr><td colspan='4' class='center'>There are no logs</td></tr>";
        else {
            $rows = $db->fetch_row();
            foreach ($rows as $row) {
                if ($row['item']) {
                    $db->query("SELECT itemname FROM items WHERE id = ?");
                    $db->execute(array(
                        $row['item']
                    ));
                    $what = $db->fetch_single();
                } elseif ($row['money']) {
                    $what = prettynum($row['money'], 1);
                } elseif ($row['points']) {
                    $what = prettynum($row['points']) . " Points";
                } elseif ($row['credits']) {
                    $what = prettynum($row['credits']) . " Credits";
                }
                print "<tr>
                        <td>" . date('F d, Y\<\b\r\>g:i:sa', $row['timestamp']) . "</td>
                        <td>" . formatName($row['from']) . "</td>
                        <td>" . formatName($row['to']) . "</td>
                        <td>$what</td>
                    </tr>";
            }
        }
        print "</table>";
        if ($rtn = $pages->displayPages())
            echo '<br /><span class="floaty">' . $rtn . '</span><br />';
        print "</td></tr>";

        ?>
    </div>
</div>
<?php
include "footer.php";
