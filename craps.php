<?php
include 'header.php';

$result1 = $mysql->query("SELECT * FROM `family_list` WHERE `userid` = '".$user_class->id."' OR `Soldier` = '".$user_class->id."' OR `Boss` = '".$user_class->id."' OR `Capo` = '".$user_class->id."' OR `Consigliere` = '".$user_class->id."' LIMIT 1");
if (mysql_num_rows($result1) > 0) {
    $worked1 = mysql_fetch_array($result1);
    if ($worked1['userid'] != $user_class->id) {
        echo Message("Only your <b>Main Gangsta</b> can use the Casino.<br /><br /><a href='city.php'>Back</a>");
        include 'footer.php';
        die();
    }
}

if ($_POST['takebet'] != ""){
        $_POST['bet_id'] = check_number($_POST['bet_id']);
        $resulttake = $mysql->query("SELECT * FROM `craps_points` WHERE `id`='".mysql_real_escape_string($_POST['bet_id'])."'");
        $worked = mysql_fetch_array($resulttake);
        if ($worked['user1'] == $user_class->id) {
                echo Message("You can't take your own bet.");
                include 'footer.php';
                die();
        }

        $amount = $worked['bet'];

        if ($amount > $user_class->points){
                echo Message("You don't have enough points to match their bet.");
        }

        $resulttemp = $mysql->query("SELECT ip FROM `grpgusers` WHERE `id` = '".$worked['user1']."'");
        $workedtemp = mysql_fetch_assoc($resulttemp);
        $user_points_ip = $workedtemp['ip'];

        if (($user_class->ip != $user_points_ip) || ($user_class->accesslevel == 1)) {
            if ($amount <= $user_class->points) {
                $result = $mysql->query("UPDATE `grpgusers` SET `points`=`points`-'".mysql_real_escape_string($amount)."' WHERE `id`='".$user_class->id."' AND `points` >= '".mysql_real_escape_string($amount)."'");
                if (mysql_affected_rows() > 0) {
                        $user_class->points -= $amount;
                        $dice1 = rand(1,6);
                        $dice2 = rand(1,6);
                        $dice12 = $dice1 + $dice2;
                        $wonamount = ceil($amount * 2 * .9);
                        if (($worked['user1_dice1'] + $worked['user1_dice2']) > $dice12) {
                            $winner = $worked['user1'];
                            $resultout = $mysql->query("UPDATE `craps_points` SET `user2` = '".$user_class->id."', `user2_dice1` = '".$dice1."', `user2_dice2` = '".$dice2."', `winner` = '".$winner."', `when` = '".time()."' WHERE `id`='".$worked['id']."' and `user2` = '0'");
                            if (mysql_affected_rows() > 0) {
                                Send_Event($worked['user1'], "You <font color='green'>WON</font> a game of <b>Craps</b> for ".number_format($amount)." Points. You won <b>".number_format($wonamount)." Points</b>. (Score was <b>".($worked['user1_dice1'] + $worked['user1_dice2'])."-".$dice12."</b> in your favor)");
                                $result_1 = $mysql->query("UPDATE `grpgusers` SET `points`=`points`+'".mysql_real_escape_string($wonamount)."' WHERE `id`='".$winner."' LIMIT 1");
                                if ($winner == $user_class->id) { $user_class->points += $wonamount; }
                                echo Message("
                                <table id='cleanTable' width='60%' align='center'>
                                    <tr>
                                        <td align='center'>".makeuserfromid($worked['user1'])."</td>
                                        <td align='center'>".$user_class->formattedname."</td>
                                    </tr>
                                    <tr>
                                        <td align='center'><img src='images_craps/".$worked['user1_dice1'].".png' title='".$worked['user1_dice1']."'>&nbsp;<img src='images_craps/".$worked['user1_dice2'].".png' title='".$worked['user1_dice2']."'></td>
                                        <td align='center'><img src='images_craps/".$dice1.".png' title='".$dice1."'>&nbsp;<img src='images_craps/".$dice2.".png' title='".$dice2."'></td>
                                    </tr>
                                    <tr>
                                        <td colspan='2' align='center'><br /><b>You have lost</b> the ".number_format($amount)." Points bid.</td>
                                    </tr>
                                </table>
                                <br /><br /><a href='craps.php'>Back</a>");
                            }
                        } elseif (($worked['user1_dice1'] + $worked['user1_dice2']) < $dice12) {
                            $winner = $user_class->id;
                            $resultout = $mysql->query("UPDATE `craps_points` SET `user2` = '".$user_class->id."', `user2_dice1` = '".$dice1."', `user2_dice2` = '".$dice2."', `winner` = '".$winner."', `when` = '".time()."' WHERE `id`='".$worked['id']."' and `user2` = '0'");
                            if (mysql_affected_rows() > 0) {
                                Send_Event($worked['user1'], "You <font color='red'>lost</font> a game of <b>Craps</b> for ".number_format($amount)." Points. (Score was <b>".($worked['user1_dice1'] + $worked['user1_dice2'])."-".$dice12."</b> on your opponent's favor)");
                                $result_1 = $mysql->query("UPDATE `grpgusers` SET `points`=`points`+'".mysql_real_escape_string($wonamount)."' WHERE `id`='".$winner."' LIMIT 1");
                                if ($winner == $user_class->id) { $user_class->points += $wonamount; }
                                echo Message("
                                <table id='cleanTable' width='60%' align='center'>
                                    <tr>
                                        <td align='center'>".makeuserfromid($worked['user1'])."</td>
                                        <td align='center'>".$user_class->formattedname."</td>
                                    </tr>
                                    <tr>
                                        <td align='center'><img src='images_craps/".$worked['user1_dice1'].".png' title='".$worked['user1_dice1']."'>&nbsp;<img src='images_craps/".$worked['user1_dice2'].".png' title='".$worked['user1_dice2']."'></td>
                                        <td align='center'><img src='images_craps/".$dice1.".png' title='".$dice1."'>&nbsp;<img src='images_craps/".$dice2.".png' title='".$dice2."'></td>
                                    </tr>
                                    <tr>
                                        <td colspan='2' align='center'><br /><b>You have won</b> the ".number_format($amount)." Points bid. You won <b>".number_format($wonamount)." Points</b>.</td>
                                    </tr>
                                </table>
                                <br /><br /><a href='craps.php'>Back</a>");
                            }
                        } elseif (($worked['user1_dice1'] + $worked['user1_dice2']) == $dice12) {
                            $winner = '-1';
                            $resultout = $mysql->query("UPDATE `craps_points` SET `user2` = '".$user_class->id."', `user2_dice1` = '".$dice1."', `user2_dice2` = '".$dice2."', `winner` = '".$winner."', `when` = '".time()."' WHERE `id`='".$worked['id']."' and `user2` = '0'");
                            if (mysql_affected_rows() > 0) {
                                Send_Event($worked['user1'], "A game of <b>Craps</b> for ".number_format($amount)." Points ended up as a <b>Draw</b>. You got back the ".number_format($amount)." Points. (Score was <b>".($worked['user1_dice1'] + $worked['user1_dice2'])."-".$dice12."</b>)");
                                $result_1 = $mysql->query("UPDATE `grpgusers` SET `points`=`points`+'".mysql_real_escape_string($amount)."' WHERE `id`='".$worked['user1']."' or `id`='".$user_class->id."' LIMIT 2");
                                $user_class->points += $amount;
                                echo Message("
                                <table id='cleanTable' width='60%' align='center'>
                                    <tr>
                                        <td align='center'>".makeuserfromid($worked['user1'])."</td>
                                        <td align='center'>".$user_class->formattedname."</td>
                                    </tr>
                                    <tr>
                                        <td align='center'><img src='images_craps/".$worked['user1_dice1'].".png' title='".$worked['user1_dice1']."'>&nbsp;<img src='images_craps/".$worked['user1_dice2'].".png' title='".$worked['user1_dice2']."'></td>
                                        <td align='center'><img src='images_craps/".$dice1.".png' title='".$dice1."'>&nbsp;<img src='images_craps/".$dice2.".png' title='".$dice2."'></td>
                                    </tr>
                                    <tr>
                                        <td colspan='2' align='center'><br /><b>Draw.</b> Refunded the Points to both players.</td>
                                    </tr>
                                </table>
                                <br /><br /><a href='craps.php'>Back</a>");
                            }
                        }
                        include 'footer.php';
                        die();
                    }
            }
        } else { echo Message("Accounts on same location (IP) CANNOT play Craps games between those accounts."); }

        $mysql->query('UNLOCK TABLES');
}

if ($_POST['makebet']) {
        $amount = check_number($_POST['amount']);
        if (!is_numeric($amount)) {
            echo Message("Invalid input.");
        }
         if ($amount > $user_class->points) {
            echo Message("You don't have that many points.");
        }
        if ($amount < 10) {
            echo Message("Please enter a valid number of points. You can't bet less than 10 Points.");
        }
        if ($amount > 5000) {
            echo Message("Please enter a valid number of points. You can't bet more than 5,000 Points.");
        }
        if ($amount >= 10 && $amount <= 5000 && $amount <= $user_class->points) {
            $result2 = $mysql->query("UPDATE `grpgusers` SET `points`=`points`-'".mysql_real_escape_string($amount)."' WHERE `id`='".$user_class->id."' AND `points`>='".mysql_real_escape_string($amount)."'");
            if (mysql_affected_rows() > 0) {
                $user_class->points -= $amount;
                $dice1 = rand(1,6);
                $dice2 = rand(1,6);
//                echo Message("<img src='images_craps/".$dice1.".png' title='".$dice1."'>&nbsp;<img src='images_craps/".$dice2.".png' title='".$dice2."'><br /><br />You have rolled <b>".($dice1+$dice2)."</b> eyes. Now you have to wait for another player to roll against you.<br /><br /><a href='craps.php'>Back</a>");
                echo Message("You have rolled the dice. Now you have to wait for another player to roll against you and you will see the outcome.<br /><br /><a href='craps.php'>Back</a>");
                $result= $mysql->query("INSERT INTO `craps_points` VALUES ('', '".$user_class->id."', '0', '".$dice1."', '".$dice2."', '0', '0', '".mysql_real_escape_string($amount)."', '0', '".time()."')");
                include 'footer.php';
                die();
            } else {
                echo Message("You don't have that many points.");
            }
        }
}

?>
<div class="contenthead">Craps</div><!--contenthead-->
<div class="contentcontent">
Place a bet and roll the dice. Your throw will be matched by another player and the one with the highest roll will take 90% of the total amount.
</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->
<div class="contentcontent">
<form method='post'>
Bet: <input type='text' name='amount' size='4' maxlength='4' value='0'> (Min: 10 Points; Max: 5,000 Points)
<input type='submit' name='makebet' value='Roll the Dice'></form>
<br />
</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->
<div class="contentcontent">
<table id='cleanTable' width='70%' align='center'>
       <tr>
           <td id='headerRow'>Gangsta</td>
           <td id='headerRow' align='center'>Points Bet</td>
           <td id='headerRow' align='center'>Play</td>
      </tr>
<?php
$result = $mysql->query("SELECT craps_points.user1, craps_points.bet, craps_points.id as crapsid, grpgusers.id, grpgusers.gamename, grpgusers.jail, grpgusers.hospital, grpgusers.infection, grpgusers.hwho, grpgusers.hhow, grpgusers.exp, grpgusers.gang, grpgusers.lastactive, grpgusers.rmdays, grpgusers.namestyle, grpgusers.gndays, grpgusers.gncode, grpgusers.cindays, grpgusers.cinapproved, grpgusers.street_level, grpgusers.accesslevel, gangs.id as gangid, gangs.leader, gangs.name, gangs.tagcolor, gangs.tag from craps_points left join grpgusers on grpgusers.id=craps_points.user1 left join gangs on gangs.id=grpgusers.gang WHERE craps_points.winner = '0' ORDER BY craps_points.bet DESC");
while($line = mysql_fetch_array($result, MYSQL_ASSOC)) {
    echo "<form method='post'>
             <tr>
                <td id='dottedRow'>".$user_class->make_user_name($line)."</td>
                <td id='dottedRow' align='center'>".number_format($line['bet'])."</td>";
        if ($user_class->id == $line['user1']) { echo "<td align='center' id='dottedRow'>-</td>"; }
           else { echo "<td align='center' id='dottedRow'><input type='hidden' name='bet_id' value='".$line['crapsid']."'> <input type='submit' name='takebet' value='Roll'></td>"; }
        echo "</tr></form>";
}
?>
</table>
<br />
</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->

<div class='contenthead'>Craps History</div><!--contenthead-->
<div class='contentcontent'>
<table id='cleanTable' width='100%' align='center'>
      <tr>
          <td id='headerRow' align='left' width='30%'>Player1</td>
          <td id='headerRow' align='right'>Roll</td>
          <td id='headerRow' align='center' width='20%'>Bet</td>
          <td id='headerRow' align='left'>Roll</td>
          <td id='headerRow' align='right' width='30%'>Player2</td>
      </tr>
<form method='post' action='pss.php'>
<?php

$result03 = $mysql->query("select craps_points.bet, craps_points.user1_dice1, craps_points.user1_dice2, craps_points.user2_dice1, craps_points.user2_dice2,
        usera.id as uid1, usera.gamename as gn1, usera.exp as ex1, usera.rmdays as rm1, usera.lastactive as la1, usera.namestyle as ns1, usera.gang as ga1, usera.gndays as gnd1, usera.gncode as gnc1, usera.cindays as cnd1, usera.cinapproved as cna1, usera.street_level as sl1, usera.accesslevel as al1,
        ganga.id as gid1, ganga.leader as oi1, ganga.name as na1, ganga.tagcolor as tgc1, ganga.tag as tg1,
        userb.id as uid2, userb.gamename as gn2, userb.exp as ex2, userb.rmdays as rm2, userb.lastactive as la2, userb.namestyle as ns2, userb.gang as ga2, userb.gndays as gnd2, userb.gncode as gnc2, userb.cindays as cnd2, userb.cinapproved as cna2, userb.street_level as sl2, userb.accesslevel as al2,
        gangb.id as gid2, gangb.leader as oi2, gangb.name as na2, gangb.tagcolor as tgc2, gangb.tag as tg2
    from craps_points left join grpgusers as usera on usera.id=craps_points.user1 left join gangs as ganga on ganga.id=usera.gang left join grpgusers as userb on userb.id=craps_points.user2 left join gangs as gangb on gangb.id=userb.gang where craps_points.winner != '0' ORDER BY craps_points.when DESC LIMIT 10");
while($row = mysql_fetch_array($result03, MYSQL_ASSOC)) {
    $t_from['id'] = $row['uid1'];
    $t_from['gamename'] = $row['gn1'];
    $t_from['exp'] = $row['ex1'];
    $t_from['rmdays'] = $row['rm1'];
    $t_from['lastactive'] = $row['la1'];
    $t_from['gang'] = $row['ga1'];
    $t_from['namestyle'] = $row['ns1'];
    $t_from['gndays'] = $row['gnd1'];
    $t_from['gncode'] = $row['gnc1'];
    $t_from['cindays'] = $row['cnd1'];
    $t_from['cinapproved'] = $row['cna1'];
    $t_from['street_level'] = $row['sl1'];
    $t_from['accesslevel'] = $row['al1'];
    $t_from['gangid'] = $row['gid1'];
    $t_from['tag'] = $row['tg1'];
    $t_from['leader'] = $row['oi1'];
    $t_from['name'] = $row['na1'];
    $t_from['tagcolor'] = $row['tgc1'];
    $t_from['tag'] = $row['tg1'];

    $t_to['id'] = $row['uid2'];
    $t_to['gamename'] = $row['gn2'];
    $t_to['exp'] = $row['ex2'];
    $t_to['rmdays'] = $row['rm2'];
    $t_to['lastactive'] = $row['la2'];
    $t_to['gang'] = $row['ga2'];
    $t_to['namestyle'] = $row['ns2'];
    $t_to['gndays'] = $row['gnd2'];
    $t_to['gncode'] = $row['gnc2'];
    $t_to['cindays'] = $row['cnd2'];
    $t_to['cinapproved'] = $row['cna2'];
    $t_to['street_level'] = $row['sl2'];
    $t_to['accesslevel'] = $row['al2'];
    $t_to['gangid'] = $row['gid2'];
    $t_to['tag'] = $row['tg2'];
    $t_to['leader'] = $row['oi2'];
    $t_to['name'] = $row['na2'];
    $t_to['tagcolor'] = $row['tgc2'];
    $t_to['tag'] = $row['tg2'];

    $user1 = $t_from;
    $user2 = $t_to;

    $wonamount = ceil($row['bet'] * 6 * .9);
        if (($row['user1_dice1'] + $row['user1_dice2']) > ($row['user2_dice1'] + $row['user2_dice2'])) {
            $user1won = " style='padding:2px; border:2px outset #BDA507; height:21px; align:center;'";
            $user2won = "";
        } elseif (($row['user1_dice1'] + $row['user1_dice2']) < ($row['user2_dice1'] + $row['user2_dice2'])) {
            $user1won = "";
            $user2won = " style='padding:2px; border:2px outset #BDA507; height:21px; align:center;'";
        } elseif (($row['user1_dice1'] + $row['user1_dice2']) == ($row['user2_dice1'] + $row['user2_dice2'])) {
            $user1won = "";
            $user2won = "";
        }
        echo "<tr>
                <td id='dottedRow' align='left'>".$user_class->make_user_name($user1)."</td>
                <td id='dottedRow' align='right'><div".$user1won."><img src='images_craps/".$row['user1_dice1'].".png' title='".$row['user1_dice1']."' width='20' height='20'>&nbsp;<img src='images_craps/".$row['user1_dice2'].".png' title='".$row['user1_dice2']."' width='20' height='20'>&nbsp;</div></td>
                <td id='dottedRow' align='center'>".number_format($row['bet'])." Points</td>
                <td id='dottedRow' align='left'><div".$user2won.">&nbsp;<img src='images_craps/".$row['user2_dice1'].".png' title='".$row['user2_dice1']."' width='20' height='20'>&nbsp;<img src='images_craps/".$row['user2_dice2'].".png' title='".$row['user2_dice2']."' width='20' height='20'></div></td>
                <td id='dottedRow' align='right'>".$user_class->make_user_name($user2)."</td>
              </tr>";

}
?>
</form>
</table>
</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->

<?php

include 'footer.php';
?>