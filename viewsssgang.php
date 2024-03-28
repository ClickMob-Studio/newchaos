<?php
include 'header.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function generateStars($value) {
    $totalStars = 10; // Total number of stars
    $filledStars = str_repeat("<span style='color:gold; font-size: 20px;'>&#9733;</span>", $value); // Gold filled stars
    $emptyStars = str_repeat("<span style='color:gray; font-size: 20px;'>&#9733;</span>", $totalStars - $value); // Gray empty stars
    return $filledStars . $emptyStars;
}
$gang_class = new Gang($_GET['id']);
security($_GET['id']);

echo "Debugging: " . $gang_class->upgrade1;  // This is the added debugging line

$gang_class = new Gang($_GET['id']);
if (empty($gang_class->id)) {
    echo Message("That gang doesn't exist!");
    include 'footer.php';
    die();
}
if (!empty($gang_class->banner))
    print "
    <center>
        <a href='viewgang.php?id=$gang_class->id' />
            <img src='$gang_class->banner' width='300' height='75' alt='Gang Banner' title='$gang_class->name' />
        </a>
    </center>
    ";
print "
<table id='newtables' style='width:100%;table-layout:fixed;'>
    <tr>
        <th colspan='4'>Your Gang</td>
    </tr>
    <tr>
        <th>Gang:</th><td>[$gang_class->tag] $gang_class->name</td>
        <th>Gang Level:</th><td>$gang_class->level</td>
    </tr>
    <tr>
        <th>Gang Exp:</th><td>$gang_class->formattedexp</td>
        <th>Members:</th><td>$gang_class->members / $gang_class->capacity</td>
    </tr>
    <tr>
        <th>Gang House:</th><td>$gang_class->housename [+$gang_class->houseawake%]</td>
        <th>Respect:</th><td>" . number_format($gang_class->respect, 5) . "</td>
    </tr>

<!-- Gang Upgrades Display -->
<tr>
    <th colspan='4'>Gang Upgrades</th>
</tr>
<tr>
    <td colspan='4'>
        Strength Upgrade: " . generateStars($gang_class->upgrade1) . " ($gang_class->upgrade1/10)
    </td>
</tr>
<tr>
    <td colspan='4'>
        Defense Upgrade: " . generateStars($gang_class->upgrade2) . " ($gang_class->upgrade2/10)
    </td>
</tr>
<tr>
    <td colspan='4'>
        Speed Upgrade: " . generateStars($gang_class->upgrade3) . " ($gang_class->upgrade3/10)
    </td>
</tr>

<tr>
    <td colspan='4'>
        Raid Item Drop Chance: " . generateStars($gang_class->upgrade4) . " ($gang_class->upgrade4/10)
    </td>
</tr>
</table>
<table id='newtables' style='width:100%;'>
    <tr>
        <th>Gang Public Page</th>
    </tr>
    <tr>
        <td>" . BBCodeParse(strip_tags($gang_class->publicpage)) . "</td>
    </tr>
</table>
";
if ($user_class->level > 4)
    if ($user_class->gang == 0)
        print "<br /><a href='apply.php?gang={$_GET['id']}'>Apply for gang</a><br /><br />";
if ($user_class->level <= 4)
    print "<font size='3px'><font color=darkgreen>You cannot join a gang until you are level 5<br></b></font></font>";
if ($user_class->level > "4") {
    $war = CheckGangWar($user_class->gang);
    $user_gang = new Gang($user_class->gang);
    if ($user_class->gang != 0 && $user_gang->leader == $user_class->id && $war == 0 && $_GET['id'] != $user_class->gang)
        print "<br /><a href='gangwar.php?gang={$_GET['id']}'>Invite to gang war</a><br /><br />";
}
    print "
<table id='newtables' style='width:100%;'>
    <tr>
        <th>Rank</th>
        <th>Mobster</th>
        <th>Level</th>
        <th>Money</th>
        <th>Gang Rank</th>
        <th>Online</th>
    </tr>
";
    $result = mysql_query("SELECT * FROM grpgusers WHERE gang = {$_GET['id']} ORDER BY level DESC");
    $rank = 0;
    while ($line = mysql_fetch_array($result)) {
        $gang_member = new User($line['id']);
        if ($gang_member->id == $gang_class->leader)
            $gang_member->rank = "<b>" . $gang_member->rank . "</b>";
        print "
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
    print "</table></td></tr>";
include 'footer.php';
?>
