<?php
require_once __DIR__ . '/header.php';
?>
	
	<div class='box_top'>Daily HOF</div>
						<div class='box_middle'>
							<div class='pad'>
								<?php
$db->query("SELECT * FROM otdwinners ORDER BY timestamp DESC LIMIT 100");
$db->execute();
$rows = $db->fetch_row();
?><br />
<hr>
<table id="newtables" style="table-layout:fixed;width:100%;">
    <tr>
        <th>Mobster</th>
        <th>OTD Won</th>
        <th>OTD Score</th>
        <th>Time</th>
    </tr><?php
    $i = 1;
    foreach ($rows as $row) {
        $time = date('M. j, Y', $row['timestamp']);
        if ($row['type'] == "Gang OTD") {
            $gi = new formatGang($row['userid']);
            $winner = $gi->formatTag() . " " . $gi->formatName();
        } else
            $winner = formatName($row['userid']);
        print "
    <tr>
        <td>" . $winner . "</td>
        <td>{$row['type']}</td>
        <td>" . prettynum($row['howmany']) . "</td>
        <td>$time</td>
    </tr>
";
        $i++;
    }
    ?></table>
</td></tr><?php
require_once __DIR__ . '/footer.php';
