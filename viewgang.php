<?php
include 'header.php';


function generateStars($value) {
    $totalStars = 10; // Total number of stars
    $filledStars = str_repeat("<span style='color:gold; font-size: 20px;'>&#9733;</span>", $value); // Gold filled stars
    $emptyStars = str_repeat("<span style='color:gray; font-size: 20px;'>&#9733;</span>", $totalStars - $value); // Gray empty stars
    return $filledStars . $emptyStars;
}
$gang_class = new Gang($_GET['id']);
security($_GET['id']);


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
<style>
.upgrades-container {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 20px;
    background-color: #333; /* Dark background for the container */
    padding: 20px; /* Adds some space inside the container */
}

.upgrade-section {
    flex: 1;
    min-width: 300px; /* Minimum width to ensure content is readable */
    background-color: #444; /* Slightly lighter than the container for contrast */
    padding: 15px;
    border-radius: 5px; /* Soften the corners */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5); /* Subtle shadow for depth */
}

.upgrades-table {
    width: 100%;
    border-collapse: collapse;
    color: #FFFFFF; /* Light text for readability */
}

.upgrades-table th, .upgrades-table td {
    border: 1px solid #555; /* Borders slightly lighter than background */
    padding: 8px;
    text-align: left;
}

.upgrades-table th {
    background-color: #666; /* Header background slightly lighter for distinction */
    color: #FFF; /* Ensuring header text is light for contrast */
}


/* Responsive adjustments */
@media screen and (max-width: 768px) {
    .upgrades-container {
        flex-direction: column;
    }
    .upgrade-section {
        flex-basis: 100%; /* Make each box take full width on smaller screens */
        margin: 10px 0;
    }
}

</style>
<div class='box_top'></div>
    <div class='box_middle'>
        <div class='pad'>
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
        
        
        </table>
        <div class='upgrades-container'>
            <div class='upgrade-section'>
                <table class='upgrades-table'>
                    
                    <tr>
                        <td colspan='4'>Strength Upgrade:  " . generateStars($gang_class->upgrade1) . " ($gang_class->upgrade1/10)</td>
                    </tr>
                    <tr>
                        <td colspan='4'>Defense Upgrade:  " . generateStars($gang_class->upgrade2) . " ($gang_class->upgrade2/10)</td>
                    </tr>
                    <tr>
                        <td colspan='4'>Speed Upgrade:  " . generateStars($gang_class->upgrade3) . " ($gang_class->upgrade3/10)</td>
                    </tr>
                    <tr>
                        <td colspan='4'>Agility Upgrade:  " . generateStars($gang_class->upgrade_agility) . " ($gang_class->upgrade_agility/10)</td>
                    </tr>
                    <tr>
                        <td colspan='4'>Raid Item Drop Chance: " . generateStars($gang_class->upgrade4) . " ($gang_class->upgrade4/10)</td>
                    </tr>
                </table>
            </div>
        
            <div class='upgrade-section'>
                <table class='upgrades-table'>
                   
                    <tr>
                        <td colspan='4'>Training Upgrades:  " . generateStars($gang_class->upgrade6) . " ($gang_class->upgrade6/10)</td>
                    </tr>
                    <tr>
                        <td colspan='4'>Battle Upgrades:  " . generateStars($gang_class->upgrade7) . " ($gang_class->upgrade7/10)</td>
                    </tr>
                    <tr>
                        <td colspan='4'>Mugging Upgrades: " . generateStars($gang_class->upgrade8) . " ($gang_class->upgrade8/10)</td>
                    </tr>
                    <tr>
                        <td colspan='4'>Faster Regeneration Bars:  " . generateStars($gang_class->upgrade9) . " ($gang_class->upgrade9/10)</td>
                    </tr>
                </table>
            </div>
        </div>
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
    print "</table></td></tr></div></div>";
include 'footer.php';
?>
