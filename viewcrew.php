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
$crew_class = new crew($_GET['id']);
security($_GET['id']);

echo "Debugging: " . $crew_class->upgrade1;  // This is the added debugging line

$crew_class = new crew($_GET['id']);
if (empty($crew_class->id)) {
    echo Message("That crew doesn't exist!");
    include 'footer.php';
    die();
}
if (!empty($crew_class->banner))
    print "
    <center>
        <a href='viewcrew.php?id=$crew_class->id' />
            <img src='$crew_class->banner' width='300' height='75' alt='crew Banner' title='$crew_class->name' />
        </a>
    </center>
    ";
print "
<table id='newtables' style='width:100%;table-layout:fixed;'>
    <tr>
        <th colspan='4'>Your crew</td>
    </tr>
    <tr>
        <th>crew:</th><td>[$crew_class->tag] $crew_class->name</td>
        <th>crew Level:</th><td>$crew_class->level</td>
    </tr>
    <tr>
        <th>crew Exp:</th><td>$crew_class->formattedexp</td>
        <th>Members:</th><td>$crew_class->members / $crew_class->capacity</td>
    </tr>
    <tr>
        <th>crew House:</th><td>$crew_class->housename [+$crew_class->houseawake%]</td>
        <th>Respect:</th><td>" . number_format($crew_class->respect, 5) . "</td>
    </tr>

<!-- crew Upgrades Display -->
<tr>
    <th colspan='4'>crew Upgrades</th>
</tr>
<tr>
    <td colspan='4'>
        Strength Upgrade: " . generateStars($crew_class->upgrade1) . " ($crew_class->upgrade1/10)
    </td>
</tr>
<tr>
    <td colspan='4'>
        Defense Upgrade: " . generateStars($crew_class->upgrade2) . " ($crew_class->upgrade2/10)
    </td>
</tr>
<tr>
    <td colspan='4'>
        Speed Upgrade: " . generateStars($crew_class->upgrade3) . " ($crew_class->upgrade3/10)
    </td>
</tr>

<tr>
    <td colspan='4'>
        Raid Item Drop Chance: " . generateStars($crew_class->upgrade4) . " ($crew_class->upgrade4/10)
    </td>
</tr>
</table>
<table id='newtables' style='width:100%;'>
    <tr>
        <th>crew Public Page</th>
    </tr>
    <tr>
        <td>" . BBCodeParse(strip_tags($crew_class->publicpage)) . "</td>
    </tr>
</table>
";
if ($user_class->level > 4)
    if ($user_class->crew == 0)
        print "<br /><a href='apply.php?crew={$_GET['id']}'>Apply for crew</a><br /><br />";
if ($user_class->level <= 4)
    print "<font size='3px'><font color=lime>You cannot join a crew until you are level 5<br></b></font></font>";
if ($user_class->level > "4") {
    $war = CheckgangWar($user_class->crew);
    $user_crew = new crew($user_class->crew);
    if ($user_class->crew != 0 && $user_crew->leader == $user_class->id && $war == 0 && $_GET['id'] != $user_class->crew)
        print "<br /><a href='crewwar.php?crew={$_GET['id']}'>Invite to crew war</a><br /><br />";
}
    print "
<table id='newtables' style='width:100%;'>
    <tr>
        <th>Rank</th>
        <th>Mobster</th>
        <th>Level</th>
        <th>Money</th>
        <th>crew Rank</th>
        <th>Online</th>
    </tr>
";
    $result = mysql_query("SELECT * FROM grpgusers WHERE crew = {$_GET['id']} ORDER BY level DESC");
    $rank = 0;
    while ($line = mysql_fetch_array($result)) {
        $crew_member = new User($line['id']);
        if ($crew_member->id == $crew_class->leader)
            $crew_member->rank = "<b>" . $crew_member->rank . "</b>";
        print "
    <tr>
        <td width='10%'>" . ( ++$rank) . "</td>
        <td width='30%'>$crew_member->formattedname</td>
        <td width='10%'>$crew_member->level</td>
        <td width='18%'>" . prettynum($crew_member->money, 1) . "</td>
        <td width='22%'>$crew_member->rank</td>
        <td width='10%'>$crew_member->formattedonline</td>
    </tr>
";
    }
    print "</table></td></tr>";
include 'footer.php';
?>
