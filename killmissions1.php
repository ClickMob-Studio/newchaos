 	<?php
include "header.php";
if (isset($_GET['do'])) {
    $do = abs(intval($_GET['do']));

    $mission = mysql_query("SELECT * FROM mission WHERE id={$do}");
    $mm = mysql_fetch_array($mission);

    if (mysql_num_rows($mission) < 1) {
        header('location: missions.php');
        exit();
    }

    // if ($do != 1 && $do != 2 && $do != 3 && $do != 4 && $do != 5 && $do != 6  && $do != 7 && $do != 8 && $do != 9 && $do != 10)
    //     die("");

    $r = mysql_fetch_array(mysql_query("SELECT * FROM missions WHERE userid={$user_class->id} AND mid={$do} ORDER BY timestamp DESC LIMIT 1"));
    $q = mysql_fetch_array(mysql_query("SELECT * FROM missions WHERE userid={$user_class->id} AND completed='no' ORDER BY timestamp DESC LIMIT 1"));
    $now = time();
    if ($q)
        $msgg = "You are currently doing a mission!";
    else if ($r && $r['completed'] != "no") {
        $currenttime = time();
        if ($mm['between'] + $r['timestamp'] > $currenttime) {
            $msgg = "You have to wait " . secondsToTime(($mm['between'] + $r['timestamp']) - $currenttime) . " until you can start another mission!";
        } else {
            $msgg = "You have successfully started a mission!";

            //'userid', 'crimes', 'mugs', 'kills', 'busts', 'timestamp', 'mid', 'completed', 'partner'

            mysql_query("INSERT INTO missions (`userid`, `timestamp`, `mid`) VALUES({$user_class->id}, {$now}, {$do})");
            mysql_query("INSERT INTO missionlog (`id`, `text`, `timestamp`) VALUES('[x] started a {$mm['name']},$user_class->id', unix_timestamp())");

            // mysql_query("INSERT INTO missions VALUES(NULL,{$user_class->id},'','','','',unix_timestamp(),{$do},'no','')"));
            // mysql_query("INSERT INTO missionlog VALUES(NULL,'[x] started a {$mm['name']},$user_class->id',unix_timestamp())"));
        }
    } else if  ($r['completed'] == "no")
        $msgg = "You are currently doing a mission!";
    else {
        $msgg = "You have successfully started a mission!";
        mysql_query("INSERT INTO missions (`userid`,`timestamp`, `mid`) VALUES({$user_class->id}, {$now}, {$do})");
        mysql_query("INSERT INTO missionlog (`id`, `text`, `timestamp`) VALUES('[x] started a {$mm['name']},$user_class->id', unix_timestamp())");
        // mysql_query("INSERT INTO missions VALUES(NULL,{$user_class->id},'','','','',unix_timestamp(),{$do},'no','')");
        // mysql_query("INSERT INTO missionlog VALUES(NULL,'[x] started a {$mm['name']},$user_class->id',unix_timestamp())");
    }
}
$q2 = mysql_query("SELECT * FROM mission WHERE category = 1 ORDER BY id ASC ");
$msgg = (isset($msgg)) ? $msgg : "";
print $msgg;
print "
<div class='collegebox'>
    <h3><a href=/missions.php><font color=red>Mission Sets</font></a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href=/killmissions.php><font color=red>Kill Missions</font></a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href=/bustmissions.php><font color=red>Bust Missions</font></a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href=/crimemissions.php><font color=red>Crime Missions</font></a> </h3>
<hr>


<div class=\"hundred centered\">";
$check = mysql_query("SELECT * FROM missions WHERE userid=$user_class->id AND completed='no'");
if (mysql_fetch_array($check)) {
    $usermission = mysql_fetch_array(mysql_query("SELECT * FROM missions WHERE userid=$user_class->id AND completed='no'"));
    $miss = mysql_fetch_array(mysql_query("SELECT * FROM mission WHERE id={$usermission['mid']}"));
    $kills = ($miss['kills'] > $usermission['kills']) ? "<font color='red'>{$usermission['kills']}/{$miss['kills']}</font>" : "<font color='green'>{$miss['kills']}/{$miss['kills']}</font>";
    $crimes = ($miss['crimes'] > $usermission['crimes']) ? "<font color='red'>{$usermission['crimes']}/{$miss['crimes']}</font>" : "<font color='green'>{$miss['crimes']}/{$miss['crimes']}</font>";
    $mugs = ($miss['mugs'] > $usermission['mugs']) ? "<font color='red'>{$usermission['mugs']}/{$miss['mugs']}</font>" : "<font color='green'>{$miss['mugs']}/{$miss['mugs']}</font>";
    $busts = ($miss['busts'] > $usermission['busts']) ? "<font color='red'>{$usermission['busts']}/{$miss['busts']}</font>" : "<font color='green'>{$miss['busts']}/{$miss['busts']}</font>";
    $currenttime = time();
    $timeleft = ($miss['time'] + $usermission['timestamp']) - $currenttime;
    print "<div class=\"doingMission\">
<br><font color=yellow>You have " . secondsToTime($timeleft - 1) . " left to finish this mission!</font><br />
<br />

<table id='newtables' class='altcolors' style='margin:auto;'>
   <tr>
        <th><font color=red>Category</th>
        <th><font color=red>Requirement</th>
        <th><font color=red>Reward</th>
        <th><font color=red>Progress</font></th>
    </tr>
<tr>
        <td>Attacking</td>
        <td>Kill User(s) from the Mobster List</td>
        <td>{$miss['payKills']} points</td>
        <td>{$kills}</td>

    </tr>
<tr>
        <td>Crimes</td>
        <td>Complete any crime(s) from the crime(s) page</td>
        <td>{$miss['payCrimes']} points</td>
        <td>{$crimes}</th>

    </tr>
<tr>
        <td>Mugs</td>
        <td>Mug User(s) from the Mobster List</td>
        <td>{$miss['payMugs']} points</td>
        <td>{$mugs}</td>

    </tr>
<tr>
        <td>Bust</td>
        <td>Bust User(s) from the Jail Page</td>
        <td>{$miss['payBusts']} points</td>
        <td>{$busts}</td>

    </tr>


</div>";
} else {
    while ($v = mysql_fetch_array($q2)) {

        print "
        <table class='mission-main' style='text-align:center'>
            <tr>
                <td class='mission-columns' style='width: 20%'><b>Name</b></td>
                <td class='mission-columns'><b>Requirements</b></td>
                <td class='mission-columns'><b>Rewards</b></td>
                <td class='mission-columns' style='width: 15%'><b>Action</b></td>
            </tr>

            <tr>
                <td class='mission-columns'>{$v['name']}</td>
                <td class='mission-columns'>Kills: {$v['kills']}</td>
                <td class='mission-columns'>Kills Points: {$v['payKills']}</td>
                <td class='mission-columns'>
$r = mysql_fetch_array(mysql_query("SELECT * FROM missions WHERE userid={$user_class->id} AND mid={$v['id']} ORDER BY timestamp DESC LIMIT 1"));
if ($v['between'] + $r['timestamp'] > $currenttime) {
    $button = "Available in " . secondsToTime(($v['between'] + $r['timestamp']) - $currenttime);
} else {
    $button = "<input TYPE='button' value='Do Mission' onclick=window.location.href='?do={$v['id']}'>";
}
</td>
            </tr>

        </table>
        <br />";
    }
}
print "<div class='clear'></div></div></div><div class='container'>

<table>
<br>
    <h3>Missions Log</h3>
<hr>
  <tr>
<div class=\"doingMission\" style=\"margin-top:20px;\">
<table style='width:95%'>";
$ml = mysql_query("SELECT * FROM missionlog ORDER BY timestamp DESC LIMIT 25");
while ($mm = mysql_fetch_array($ml)) {
    $text = explode(',', $mm['text']);
    print "<tr><td>&bull;&nbsp;&nbsp;" . str_replace('[x]', formatName($text[1]), $text[0]) . "</td><td>" . date('m/d/y g:i a', $mm['timestamp']) . "</td></tr>\n<tr><td><br /></td></tr>\n";
}
print "</table></div></div></div></table></tr>";
include "footer.php";
?>