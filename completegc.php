<?php
include("header.php");

$gang = new Gang($user_class->gang);
if ($user_class->gang != 0 && $gang->crime != 0) {
    $gang_rank = new GangRank($user_class->grank);
    if ($gang_rank->crime == 1) {
        $result = mysql_query("SELECT * FROM `gangs` WHERE `id` = '" . $user_class->gang . "'");
        $worked = mysql_fetch_array($result);
        $result2 = mysql_query("SELECT * FROM `gangcrime` WHERE `id` = '" . $gang->crime . "'");
        $worked2 = mysql_fetch_array($result2);
        if (time() >= $worked['ending']) {
            $user_rank = new GangRank($user_class->grank);
            if ($user_rank->crime == 1) {
                Send_Event($line['id'], "Your gang crime has finished.", $line['id']);
            }
            $random = rand(1, 4);
            if ($random == 4) {
                $test12 = mysql_query("SELECT * FROM `gangs` WHERE `id` = '" . $user_class->gang . "'");
                $test1 = mysql_fetch_array($test12);
                Crime_Event($user_class->gang, $worked2['name'], "Failed", $test1['crimestarter']);
                perform_query("DELETE FROM `gcrimelog` WHERE `reward` = 'In Progress...' AND `gangid` = ?", [$user_class->gang]);
                perform_query("UPDATE `gangs` SET `crime` = '0', `ending` = '0', `crimestarter` = '0' WHERE `id` = ?", [$user_class->gang]);
                echo Message("Your gang crime has failed.");
            } else {
                $newmoney = $worked['moneyvault'] + $worked2['reward'];
                $test12 = mysql_query("SELECT * FROM `gangs` WHERE `id` = '" . $user_class->gang . "'");
                $test1 = mysql_fetch_array($test12);
                $result = mysql_query("UPDATE `gangs` SET `crime` = '0', `moneyvault` = '" . $newmoney . "', `ending` = '0', `crimestarter` = '0' WHERE `id` = '" . $user_class->gang . "'");
                Crime_Event($user_class->gang, $worked2['name'], "$" . prettynum($worked2['reward']), $test1['crimestarter']);
                perform_query("DELETE FROM `gcrimelog` WHERE `reward` = 'In Progress...' AND `gangid` = ?", [$user_class->gang]);
                echo Message("Your gang crime has succeeded. $" . prettynum($worked2['reward']) . " has been added to your gang vault.");
            }
        } else {
            echo Message("Your gang crime hasn't finished yet.");
        }
    } else {
        echo Message("You don't have permission to be here.");
    }
} else {
    echo Message("You don't have a gang crime active.");
}

include("footer.php");
?>