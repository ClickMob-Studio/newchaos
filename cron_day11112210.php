<?php

if($_GET['key'] != 'cron94'){
  die();
}
include_once 'dbcon.php';
include_once 'classes.php';
include 'database/pdo_class.php';
mysql_query("UPDATE `grpgusers` SET `tamt` = `0`, `todayskills` = '0', `todaysexp` = '0', `boxes_opened` = '1', `crimeauto` = '0', `csmuggling` = '6', `prayer` = '1', `searchdowntown` = '100', `dailytrains` = '0', `dailymugs` = '0', `spins` = '20', `todayskills` = '0', `dailyClockins` = '0', `todaysexp` = '0',  `gameevents` = '0', `voted1`='0', `doors`='3', `slots_left1`='100', `roulette`='1', `luckydip`='1', `csmuggling` = '6', `chase` = '1'") or die(mysql_error());
mysql_query("DELETE FROM votes WHERE 1");
// Lottery Stuff
//$checklotto = mysql_query("SELECT * FROM `lottery`") or die(mysql_error());
//$numlotto = mysql_num_rows($checklotto);
//$amountlotto = ($numlotto * 100000);
//$offset_result = mysql_query(" SELECT FLOOR(RAND() * COUNT(*)) AS `offset` //FROM `lottery` ") or die(mysql_error());
//$offset_row = mysql_fetch_assoc($offset_result);
//$offset = $offset_row['offset'];
//$result = mysql_query(" SELECT * FROM `lottery` LIMIT $offset, 1 ") or die//(mysql_error());
//$worked = mysql_fetch_assoc($result);
//$winner = $worked['userid'];
//$lottery_user = new User($worked['userid']);
//CheckForAward($lottery_user->id, WIN_LOTTERY_CASH, $player->cashlottery + 1);
//Send_Event($lottery_user->id, "Congratulations, You won the lottery of $" . //$amountlotto);
//$result2 = mysql_query("UPDATE `grpgusers` SET bank=bank + $amountlotto, //`cash_lot_wins` = `cash_lot_wins` + 1 WHERE `id` = '" . $lottery_user->id //. "'") or die(mysql_error());
//$result2 = mysql_query("DELETE FROM `lottery`") or die(mysql_error());
//Log Lottery
//$query = mysql_query("SELECT count(id) FROM lottery_winners WHERE `type`//='Money'") or die(mysql_error());
//$get_count = mysql_result($query);
//if ($get_count >= 30) {
  //  $query = mysql_query("SELECT min(id) FROM lottery_winners WHERE `type`//='Money'") or die(mysql_error());
 //   $get = mysql_result($query);
   // mysql_query("DELETE FROM lottery_winners WHERE id='$get'") or die//(mysql_error());
//}
$clock = date("d M H:i:s");
//mysql_query("INSERT INTO lottery_winners (userid, amount, `date`, `type`) //VALUES ('$lottery_user->id', '$amountlotto', '$clock', 'Money') ") or die//(mysql_error());
//$checkplotto = mysql_query("SELECT * FROM `plottery`") or die(mysql_error());
//$numplotto = mysql_num_rows($checkplotto);
//$amountplotto = ($numplotto * 10) + 250;
//$poffset_result = mysql_query(" SELECT FLOOR(RAND() * COUNT(*)) AS `offset` //FROM `plottery` ") or die(mysql_error());
//$poffset_row = mysql_fetch_assoc($poffset_result);
//$poffset = $poffset_row['offset'];
//$presult = mysql_query(" SELECT * FROM `plottery` LIMIT $poffset, 1 ") or die//(mysql_error());
//if (mysql_num_rows($presult)) {
 //   $worked = mysql_fetch_assoc($presult);
   // $pwinner = $worked['userid'];
//    $plottery_user = new User($worked['userid']);
  //  $newpoints = $plottery_user->points + $amountplotto;
//    $pointsss = Points;
  //  Send_Event($plottery_user->id, "Congratulations, You won the points lottery of " . $amountplotto . $pointsss);
   // CheckForAward($lottery_user->id, WIN_LOTTERY_POINTS, $player->pointlottery + 1);
    //$result2 = mysql_query("UPDATE `grpgusers` SET `points` = '" . $newpoints . "', `point_lot_wins` = `point_lot_wins` + 1 WHERE `id` = '" . $plottery_user->id . "'") or die(mysql_error());
//    $result2 = mysql_query("TRUNCATE TABLE `plottery`") or die(mysql_error());
//}
//Log Lottery
//$query = mysql_query("SELECT count(id) FROM lottery_winners WHERE `type`='Points'") or die(mysql_error());
//$get_count = mysql_result($query);
//if ($get_count >= 30) {
  //  $query = mysql_query("SELECT min(id) FROM lottery_winners WHERE `type`//='Points'") or die(mysql_error());
//    $get = mysql_result($query);
  //  mysql_query("DELETE FROM lottery_winners WHERE id='$get'") or die//(mysql_error());
//}
$clock = date("d M H:i:s");
//mysql_query("INSERT INTO lottery_winners (userid, amount, `date`, `type`) //VALUES ('$plottery_user->id', '$amountplotto', '$clock', 'Points') ") or //die(mysql_error());
//End
mysql_query("DELETE FROM votes WHERE 1");
mysql_query("UPDATE grpgusers SET ffban = 0");

$db->query("SELECT * FROM `gang_comp_leaderboard` ORDER BY `daily_missions_complete` DESC LIMIT 2");
$db->execute();
$dailyRows = $db->fetch_row();

$i = 1;
foreach ($dailyRows as $row) {
    $db->query("SELECT * FROM `grpgusers` WHERE `gang` = " . $row['gang_id']);
    $db->execute();
    $userRows = $db->fetch_row();

    foreach ($userRows as $uRow) {
        if ($i == 1) {
            $db->query("UPDATE `grpgusers` SET `points` = `points` + 25000 WHERE `id` = " . $uRow['id']);
            $db->execute();

            Give_Item(163, $uRow['id'], 1);
            Give_Item(42, $uRow['id'], 1);

            Send_Event($uRow['id'], "Your gang won 1st place in the daily contest. You have been awarded 25,000 points, 1 Police Badge & 1 Mystery Box.");
        } else {
            Give_Item(42, $uRow['id'], 1);

            Send_Event($uRow['id'], "Your gang won 2nd place in the daily contest. You have been awarded 1 Mystery Box.");
        }
    }

    $i++;
}

$db->query("UPDATE `gang_comp_leaderboard` SET `daily_missions_complete` = 0");
$db->execute();

$db->query("UPDATE user_research_type SET duration_in_days = duration_in_days - 1 WHERE duration_in_days > 0");
$db->execute();
?>