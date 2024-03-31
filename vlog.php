<?php
include 'header.php';
?>
<div class='box_top'>Vault Log</div>
						<div class='box_middle'>
							<div class='pad'>
<?php
if ($user_class->gang != 0) {
    $gang_class = new Gang($user_class->gang);
    $result = mysql_query("SELECT COUNT(*) FROM vlog WHERE gangid = " . $user_class->gang);
    $r = mysql_fetch_row($result);
    if(mysql_num_rows($result)){
    ?>
    <table id="newtables" style="width:100%;">
        <tr>
            <th>Description</th>
            <th>Time</th>
        </tr>
        <?php
    
        $numrows = $r[0];
        $rowsperpage = 30;
        $totalpages = ceil($numrows / $rowsperpage);
        $totalpages = ($totalpages <= 0) ? 1 : ceil($numrows / $rowsperpage);
        $currentpage = (isset($_GET['page']) && is_numeric($_GET['page'])) ? (int) $_GET['page'] : 1;
        if ($currentpage > $totalpages)
            $currentpage = $totalpages;
        if ($currentpage < 1)
            $currentpage = 1;
        $offset = ($currentpage - 1) * $rowsperpage;
        $result = mysql_query("SELECT * from vlog WHERE gangid = $user_class->gang ORDER BY timestamp DESC LIMIT $offset, $rowsperpage");
        while ($row = mysql_fetch_array($result)) {
            $extra_user = new User($row['userid']);
            $text = str_replace('[-_USERID_-]', $extra_user->formattedname, $row['text']);
            echo "
    <tr>
        <td width='68%'>" . $text . "</td>
        <td width='32%'>" . date("d M Y, g:ia", $row['timestamp']) . "</td>
    </tr>
        ";
        }
        ?>
    </table>
    <br /><br />
    <?php
    $range = 2;
    if ($currentpage > 1)
        echo " <a href='?id={$_GET['id']}&page=1'><<</a> ";
    for ($x = ($currentpage - $range); $x < (($currentpage + $range) + 1); $x++)
        if (($x > 0) && ($x <= $totalpages))
            echo ($x == $currentpage) ? " [<b>$x</b>] " : " <a href='?id={$_GET['id']}&page=$x'>$x</a> ";
    if ($currentpage < $totalpages)
        echo " <a href='?id={$_GET['id']}&page=$totalpages'>>></a> ";
    print "</td></tr>";
    }else{
        echo 'No logs found';
    }
} else {
    echo Message("You aren't in a gang.");
}
include("gangheaders.php");
include 'footer.php';
?>