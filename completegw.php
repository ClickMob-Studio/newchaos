<?php
include("header.php");

$db->query("SELECT * FROM `gangwars` WHERE (`gang1` = ? OR `gang2` = ?) AND `accepted` = '1' LIMIT 1");
$db->execute([$user_class->gang, $user_class->gang]);
$result = $db->fetch_row(true);
$atwar = count($result);

if ($user_class->gang != 0 && $atwar > 0 && time() >= $result['timeending']) {
    if ($result['gang1'] == $user_class->gang) {
        $yourgang = $result['gang1'];
        $yourscore = $result['gang1score'];
        $theirgang = $result['gang2'];
        $theirscore = $result['gang2score'];
    } else {
        $yourgang = $result['gang2'];
        $yourscore = $result['gang2score'];
        $theirgang = $result['gang1'];
        $theirscore = $result['gang1score'];
    }

    $max = max($yourscore, $theirscore);
    if ($max == $yourscore) {
        $winner = $yourgang;
    } else {
        $winner = $theirgang;
    }
    $winner_gang = new Gang($winner);
    $loser_gang = new Gang($theirgang);

    $newvault = $winner_gang->moneyvault + ($result['bet'] * 2);

    perform_query("UPDATE `gangs` SET `moneyvault` = ? WHERE `id` = ?", [$newvault, $winner]);

    Send_Event($winner_gang->leader, "Your gang war has ended and you won! You finished with the total score of " . $yourscore . " and [-_GANGID_-] finished with the total score of " . $theirscore . ". You have been granted the bet of $" . prettynum($result['bet'] * 2) . ".", $theirgang);
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