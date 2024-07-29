 	<?php
include "header.php";
?>
<div class='box_top'>Kill Missions</div>
						<div class='box_middle'>
							<div class='pad'>
<?php
if (isset($_GET['reset_mission']) && (int)$_GET['reset_mission']) {
    $q = mysql_fetch_array(mysql_query("SELECT * FROM missions WHERE userid={$user_class->id} AND completed='no' ORDER BY timestamp DESC LIMIT 1"));
    if ($q) {
        diefun('You already have an active mission.');
    }

    $tempItemUse = getItemTempUse($user_class->id);
    if ($tempItemUse['mission_passes'] < 1) {
        diefun('You do not have any mission passes available to reset this mission.');
    }

    $resetMissionId = (int)$_GET['reset_mission'];
    $now = time();

    $r = mysql_fetch_array(mysql_query("SELECT * FROM missions WHERE userid={$user_class->id} AND mid={$resetMissionId} ORDER BY timestamp DESC LIMIT 1"));

    mysql_query("UPDATE missions SET completed = 'no', timestamp = " . $now . ", crimes = 0, mugs = 0, kills = 0, busts = 0, backalleys = 0, crimes_paid = 0 WHERE id = " . $r['id']);

    removeItemTempUse($user_class->id, 'mission_passes', 1);

    diefun('You have successfully reset your mission. <a href="killmissions.php">Go Back</a>');
}

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
if(!empty($msgg))
print "<div class='floaty1'>".$msgg."</div>";
print "
<div class='contenthead floaty'>
    <h4>Mission Categories</h4>
    <div><a href='/missions.php' class='link-red'>Mission Sets</a> &nbsp;&nbsp;&nbsp; <a href='/killmissions.php' class='link-red'>Kill Missions</a> &nbsp;&nbsp;&nbsp; <a href='/bustmissions.php' class='link-red'>Bust Missions</a> &nbsp;&nbsp;&nbsp; <a href='/crimemissions.php' class='link-red'>Crime Missions</a> &nbsp;&nbsp;&nbsp; <a href='/mug_missions.php' class='link-red'>Mug Missions</a> &nbsp;&nbsp;&nbsp; <a href='/ba_missions.php' class='link-red'>BA Missions</a></div>
</div>

<hr>";
?>
<style>
    .floaty1{
    display: block;
    width: 97%;
    margin: 0 auto;
    margin-right: 10px;
    color: #000;
    /* width: 72%; */
    text-align: center;
    background-color: #fff;
    border-radius: 10px !important;
    box-shadow: 0px 2px 10px rgba(93, 93, 93, 1);
    padding: 5px 5px 4px;
    margin-bottom: 10px;
    }
    </style>
<?php


echo "<div class=\"hundred centered\">";
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
    echo "<span class='floaty1'>You have " . secondsToTime($timeleft - 1) . " left to finish this mission!</span><br />";

    print "<div class=\"doingMission\">


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
   
      $currenttime = time();
    while ($v = mysql_fetch_array($q2)) {

        $r = mysql_fetch_array(mysql_query("SELECT * FROM missions WHERE userid={$user_class->id} AND mid={$v['id']} ORDER BY timestamp DESC LIMIT 1"));
        if ($v['between'] + $r['timestamp'] > $currenttime) {
            $button = "Available in " . secondsToTime(($v['between'] + $r['timestamp']) - $currenttime);
            $tempItemUse = getItemTempUse($user_class->id);
            if ($tempItemUse['mission_passes'] > 0) {
                $secondButton = '
                <br /><br />
                <a href="?reset_mission=' . $v['id'] . '" style="color:#ff6218;">Reset Mission (Resetting a mission will start it instantly)</a>
                ';
            }
        } else {
            $button = "<input TYPE='button' value='Do Mission' onclick=window.location.href='?do={$v['id']}'>";
        }

        print "
        <div class='myTable' style='margin-top: 20px;'>
    <table width='100%' style='background-color: #292929; border-collapse: collapse;'>
        <thead>
            <tr style='background-color: #1c1c1c; color: #ffffff;'>
                <th style='border: 1px solid #444444; padding: 8px;'>Name</th>
                <th style='border: 1px solid #444444; padding: 8px;'>Requirements</th>
                <th style='border: 1px solid #444444; padding: 8px;'>Rewards</th>
                <th style='border: 1px solid #444444; padding: 8px;'>Action</th>
            </tr>
        </thead>

            <tr>
                <td class='mission-columns'>{$v['name']}</td>
                <td class='mission-columns'>Kills: <span class='text-green'>{$v['kills']}<br /></span>Crimes: <span class='text-green'>{$v['crimes']}<br /></span>Mugs: <span class='text-green'>{$v['mugs']}<br /></span>Busts: <span class='text-green'>{$v['busts']}</span></td>
                <td class='mission-columns'>Kills: <span class='text-green'>{$v['payKills']}</span> Points<br/>Crimes: <span class='text-green'>{$v['payCrimes']}</span> Points<br/>Mugs: <span class='text-green'>{$v['payMugs']}</span> Points<br/>Busts: <span class='text-green'>{$v['payBusts']}</span> Points<br/>EXP: <span class='text-green'>{$v['exp_level']}%</span> of max EXP<br/></td>
                <td class='mission-columns'>
                    {$button}
                    {$secondButton}
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
print "</table></div></div> ";
include "footer.php";
?>