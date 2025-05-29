<?php
include("header.php");

$result = mysql_query("SELECT * FROM `gangwars` WHERE (`gang1` = '" . $user_class->gang . "' OR `gang2` = '" . $user_class->gang . "') AND `accepted` = '1' LIMIT 1");
$atwar = mysql_num_rows($result);
$query = mysql_fetch_array($result);
if ($user_class->gang != 0 && $atwar > 0 && time() >= $query['timeending']) {
    if ($query['gang1'] == $user_class->gang) {
        $yourgang = $query['gang1'];
        $yourscore = $query['gang1score'];
        $theirgang = $query['gang2'];
        $theirscore = $query['gang2score'];
    } else {
        $yourgang = $query['gang2'];
        $yourscore = $query['gang2score'];
        $theirgang = $query['gang1'];
        $theirscore = $query['gang1score'];
    }

    $max = max($yourscore, $theirscore);
    if ($max == $yourscore) {
        $winner = $yourgang;
    } else {
        $winner = $theirgang;
    }
    $winner_gang = new Gang($winner);
    $loser_gang = new Gang($theirgang);

    $newvault = $winner_gang->moneyvault + ($query['bet'] * 2);

    perform_query("UPDATE `gangs` SET `moneyvault` = ? WHERE `id` = ?", [$newvault, $winner]);

    Send_Event($winner_gang->leader, "Your gang war has ended and you won! You finished with the total score of " . $yourscore . " and [-_GANGID_-] finished with the total score of " . $theirscore . ". You have been granted the bet of $" . prettynum($query['bet'] * 2) . ".", $theirgang);
    Send_Event($loser_gang->leader, "Your gang war has ended and unfortunately you lost. You finished with the total score of " . $theirscore . " and [-_GANGID_-] finished with the total score of " . $yourscore . ".", $yourgang);

    perform_query("DELETE FROM `gangwars` WHERE (`gang1` = ? OR `gang2` = ?) AND `accepted` = '1' LIMIT 1", [$user_class->gang, $user_class->gang]);

    if ($user_class->gang == $winner_gang->id) {
        echo Message("You have won the gang war! Check your events for more details.");
    } else {
        echo Message("You have lost the gang war. Check your events for more details.");
    }
}

include("footer.php");
?>