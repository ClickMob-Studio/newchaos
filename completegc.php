<?php
include("header.php");

$gang = new Gang($user_class->gang);
if ($user_class->gang != 0 && $gang->crime != 0) {
    $gang_rank = new GangRank($user_class->grank);
    if ($gang_rank->crime == 1) {
        $db->query("SELECT * FROM `gangs` WHERE `id` = ?");
        $db->execute([$user_class->gang]);
        $worked = $db->fetch_row(true);

        $db->query("SELECT * FROM `gangcrime` WHERE `id` = ?");
        $db->execute([$gang->crime]);
        $worked2 = $db->fetch_row(true);
        if (time() >= $worked['ending']) {
            $random = rand(1, 4);
            if ($random == 4) {
                $db->query("SELECT * FROM `gangs` WHERE `id` = ?");
                $db->execute([$user_class->gang]);
                $test1 = $db->fetch_row(true);

                Crime_Event($user_class->gang, $worked2['name'], "Failed", $test1['crimestarter']);
                perform_query("DELETE FROM `gcrimelog` WHERE `reward` = 'In Progress...' AND `gangid` = ?", [$user_class->gang]);
                perform_query("UPDATE `gangs` SET `crime` = '0', `ending` = '0', `crimestarter` = '0' WHERE `id` = ?", [$user_class->gang]);
                echo Message("Your gang crime has failed.");
            } else {
                $newmoney = $worked['moneyvault'] + $worked2['reward'];
                $db->query("SELECT * FROM `gangs` WHERE `id` = ?");
                $db->execute([$user_class->gang]);
                $test1 = $db->fetch_row(true);

                perform_query("UPDATE `gangs` SET `crime` = '0', `moneyvault` = ?, `ending` = '0', `crimestarter` = '0' WHERE `id` = ?", [$newmoney, $user_class->gang]);
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