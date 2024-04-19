<?php
include 'header.php';
?>
<div class='box_top'>Gang Members</div>
						<div class='box_middle'>
							<div class='pad'>
                                <?php
$gang_class = new Gang($user_class->gang);
if ($user_class->gang == 0) {
    echo Message("You aren't in a gang.");
    include("footer.php");
    die();
}
?>
<table id="newtables" style="width:100%;">
    <tr>
        <th>Rank</th>
        <th>Mobster</th>
        <th>Level</th>
        <th>Money</th>
        <th>Gang Rank</th>
        <th>Online</th>
    </tr>
    <?php
    $result = mysql_query("SELECT `id` FROM grpgusers WHERE gang = $user_class->gang ORDER BY level DESC");
    $rank = 0;
    while ($line = mysql_fetch_array($result)) {
        $gang_member = new User($line['id']);
        if ($gang_member->id == $gang_class->leader)
            $gang_member->rank = "<b>" . $gang_member->rank . "</b>";
        print"
    <tr>
        <td width='10%'>" . ( ++$rank) . "</td>
        <td width='30%'>$gang_member->formattedname</td>
        <td width='10%'>$gang_member->level</td>
        <td width='18%'>" . prettynum($gang_member->money, 1) . "</td>
        <td width='22%'>$gang_member->rank</td>
        <td width='10%'>$gang_member->formattedonline</td>
    </tr>
";
    }
    print"</table>";
    include("gangheaders.php");
    include 'footer.php';
    ?>