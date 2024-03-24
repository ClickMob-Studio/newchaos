<?php
include 'header.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);


function check_number($number)
{
    if (preg_match("/^[0-9]+$/", $number)) {
        return $number;
    } else {
        return -1;
    }
}



// Database Operations


if (isset($_POST['accept'])) {
        $acceptid = check_number($_POST['accept']);
        $resultid = $mysql->query("SELECT * FROM `pss_games` WHERE `id`='".mysql_real_escape_string($acceptid)."'");
        $workedid = mysql_fetch_array($resultid);
        if($workedid['bet'] > $user_class->points){
            echo Message("You don't have enough points to match that bet.");
            include 'footer.php';
            die();
        }
        if($workedid['p2'] != 0){
            echo Message("That bet was already accepted.");
            include 'footer.php';
            die();
        }
        if ($user_class->points >= $workedid['bet']) {
            $resultbet = $mysql->query("UPDATE grpgusers SET points=points-".$workedid['bet']." WHERE `id`='".$user_class->id."' AND points>=".$workedid['bet']);
            if (mysql_affected_rows() > 0) {
                $user_class->points -= $workedid['bet'];
                echo Message("You have accepted ".makeuserfromid($workedid['p1'])."'s bet.");
                $resultaccept = $mysql->query("UPDATE pss_games SET p2='".$user_class->id."', bet=bet+".$workedid['bet']." WHERE `id`='".$acceptid."' LIMIT 1");
                Send_Event($workedid['p1'], makeuserfromid($user_class->id)." has accepted your game of Paper, Scissors, Stone. Click <a href='pss.php'>here</a> to go to PSS.");
            }
        }
}


if (isset($_POST['makebet'])) {
        $_POST['amount'] = check_number($_POST['amount']);
        if(!is_numeric($_POST['amount'])){
            echo Message("Invalid bet.");
            include 'footer.php';
            die();
        }
        if($_POST['amount'] > $user_class->points){
            echo Message("You don't have that many points.");
            include 'footer.php';
            die();
        }
        if (($_POST['amount'] < 10) || ($_POST['amount'] > 1000)){
            echo Message("Please enter a valid number of points between 10 and 1,000.");
            include 'footer.php';
            die();
        }
        if($_POST['amount'] >= 10 && $_POST['amount'] <= 1000 && $_POST['amount'] <= $user_class->points){
            $result2 = $mysql->query("UPDATE grpgusers SET points=points-".$_POST['amount']." WHERE `id`='".$user_class->id."' AND points>=".$_POST['amount']);
            if (mysql_affected_rows() > 0) {
                echo Message("You have added ".$_POST['amount']." Points bet.");
                $result = $mysql->query("INSERT INTO `pss_games` VALUES ('', '".$user_class->id."', '0', '0', '0', '0', '0', '1', '".time()."', '".mysql_real_escape_string($_POST[amount])."')");
                $user_class->points -= $_POST['amount'];
//              $resultlog = $mysql->query("INSERT INTO `pss_log` VALUES ('', '".$user_class->id."', '', '', '', '', '0', '".time()."', '".$user_class->id."', '".$user_class->id."', '".$amount."', 'points', '".$user_class->id."')");
            }
        }

}


if (isset($_POST['remove'])) {
    $removeid = check_number($_POST['remove']);
    $resultip = $mysql->query("SELECT * FROM `pss_games` WHERE `p1` = '".$user_class->id."' AND `p2` = '0' AND `id` = '".$removeid."'");
    if (mysql_num_rows($resultip) == 1) {
        $workedip = mysql_fetch_array($resultip);
        $resulttb = $mysql->query("UPDATE grpgusers SET points=points+".$workedip['bet']." WHERE id='".$user_class->id."' LIMIT 1");
        $resultrem = $mysql->query("DELETE FROM `pss_games` WHERE `p1` = '".$user_class->id."' AND `p2` = '0' AND `id` = '".$removeid."'");
        echo Message("Game removed.");
        $user_class->points += $workedip['bet'];
    } else { echo Message("There was a problem. You tried to remove the same game twice or something else went wrong.<br><br><a href='pss.php'>Back</a>"); include 'footer.php'; die(); }
}




?>

<h3>Paper, Scissors, Stone</h3>
<hr>
<div class="contentcontent">

<p>
Place a challenge or accept another Gangsta's challenge for a game of Paper, Scissors, Stone.<br>
The rules are simple:<br>
<b>1.</b> Paper covers or captures stone; paper wins.<br>
<b>2.</b> Scissors cut paper; scissors win.<br>
<b>3.</b> Stone breaks scissors; stone wins.<br>
Winner takes 90% of the total pot.<br>
Minimum bet is 10 points. Maximum bet is 1,000 points.<br>
If the game is not completed in 3 days then both players get their points back.<br><br>
</p>

<form method='post'>
    <input type='text' name='amount' size='10' maxlength='4' value=''>
    <input type='submit' name='makebet' value='Make Bet'>
</form>
<br><br>
<a href='city.php'>Back</a><br>

<!-- Your Games Section -->
<h3>Your Games</h3>
<!-- ... Your games code ... -->
<div class='contenthead'>Your Games</div><!--contenthead-->
<div class='contentcontent'>
<table id='cleanTable' width='100%'>
      <tr>
          <td id='headerRow'>Player 1</td>
          <td id='headerRow'>Player 2</td>
          <td id='headerRow'>Bet</td>
          <td id='headerRow'>Score</td>
          <td id='headerRow'>Action</td>
      </tr>
<?php

$result01 = $mysql->query("SELECT * FROM `pss_games` WHERE (`p1`='".$user_class->id."' AND `p2`!='0') OR (`p2`='".$user_class->id."' AND `p1`!='0') ORDER BY `timestarted` DESC");
while($line01 = mysql_fetch_array($result01, MYSQL_ASSOC)) {
        echo "<form method='post'>
              <tr>
                <td id='dottedRow'>".makeuserfromid($line01['p1'])."</td>";
        if ($line01['p2'] != '0') { echo "<td id='dottedRow'>".makeuserfromid($line01['p2'])."</td>"; }
            else { echo "<td id='dottedRow'>None Yet</td>"; }
        echo "<td id='dottedRow'>".$line01['bet']." Points</td>
              <td id='dottedRow'>".$line01['p1_score']."/".$line01['p2_score']."</td>";
        if (($line01['p1'] == $user_class->id) && ($line01['p1_current'] == "0")) { echo "<td id='dottedRow'><a href='pss_play.php?id=".$line01['id']."'>Play</a></td>"; }
           elseif (($line01['p2'] == $user_class->id) && ($line01['p1_current'] != "0")) { echo "<td id='dottedRow'><a href='pss_play.php?id=".$line01['id']."'>Play</a></td>"; }
            else { echo "<td id='dottedRow'></td>"; }
        echo "</tr></form>";

}
?>
</table>




<!-- Current Challenges Section -->
<h3>Current Challenges</h3>
<!-- ... Current Challenges code ... -->

<div class='contenthead'>Current Challenges</div><!--contenthead-->
<div class='contentcontent'>
<table id='cleanTable' width='100%'>
      <tr>
          <td id='headerRow'>Player 1</td>
          <td id='headerRow'>Player 2</td>
          <td id='headerRow'>Bet</td>
          <td id='headerRow'>Action</td>
      </tr>
<?php

$result02 = $mysql->query("SELECT * FROM `pss_games` WHERE `p2`='0' ORDER BY `timestarted` DESC");
while($line02 = mysql_fetch_array($result02, MYSQL_ASSOC)) {
        echo "<form method='post'>
              <tr>
                <td id='dottedRow'>".makeuserfromid($line02['p1'])."</td>";
        if ($line02['p2'] != '0') { echo "<td id='dottedRow'>".makeuserfromid($line02['p2'])."</td>"; }
            else { echo "<td id='dottedRow'>None Yet</td>"; }
        echo "<td id='dottedRow'>".$line02['bet']." Points</td>";
        if ($line02['p1'] == $user_class->id) { echo "<td id='dottedRow'><input type='hidden' name='remove' value='".$line02['id']."'><input type='submit' name='challenge' value='Remove'></td>"; }
           else { echo "<td id='dottedRow'><input type='hidden' name='accept' value='".$line02['id']."'><input type='submit' name='challenge' value='Accept'></td>"; }
        echo "</tr></form>";

}
?>
</table>



<!-- History Section -->
<h3>Paper, Scissors, Stone History</h3>
<!-- ... History code ... -->

<div class='contenthead'>Paper, Scissors, Stone History</div><!--contenthead-->
<div class='contentcontent'>
<table id='cleanTable' width='100%'>
      <tr>
          <td id='headerRow'>Player 1</td>
          <td id='headerRow'>Player 2</td>
          <td id='headerRow'>Score</td>
          <td id='headerRow'>Bet</td>
          <td id='headerRow'>Winner</td>
      </tr>
<form method='post' action='pss.php'>
<?php

//$result03 = $mysql->query("SELECT * FROM `log_pss` ORDER BY `timestarted` DESC LIMIT 10");
$result03 = $mysql->query("select log_pss.p1_score, log_pss.p2_score, log_pss.bet, log_pss.winner,
        usera.id as uid1, usera.gamename as gn1, usera.exp as ex1, usera.rmdays as rm1, usera.lastactive as la1, usera.namestyle as ns1, usera.gang as ga1, usera.gndays as gnd1, usera.gncode as gnc1, usera.cindays as cnd1, usera.cinapproved as cna1, usera.street_level as sl1, usera.accesslevel as al1,
        ganga.id as gid1, ganga.leader as oi1, ganga.name as na1, ganga.tagcolor as tgc1, ganga.tag as tg1,
        userb.id as uid2, userb.gamename as gn2, userb.exp as ex2, userb.rmdays as rm2, userb.lastactive as la2, userb.namestyle as ns2, userb.gang as ga2, userb.gndays as gnd2, userb.gncode as gnc2, userb.cindays as cnd2, userb.cinapproved as cna2, userb.street_level as sl2, userb.accesslevel as al2,
        gangb.id as gid2, gangb.leader as oi2, gangb.name as na2, gangb.tagcolor as tgc2, gangb.tag as tg2
    from log_pss left join grpgusers as usera on usera.id=log_pss.p1 left join gangs as ganga on ganga.id=usera.gang left join grpgusers as userb on userb.id=log_pss.p2 left join gangs as gangb on gangb.id=userb.gang ORDER BY log_pss.timestarted DESC LIMIT 10");
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

    $p1 = $t_from;
    $p2 = $t_to;

    if ($p1['id'] == $row['winner']) {
        $winner = $user_class->make_user_name($p1);
    } elseif ($p2['id'] == $row['winner']) {
        $winner = $user_class->make_user_name($p2);
    } else {
        $winner = "Tied";
    }

    echo "<tr>
                <td id='dottedRow'>".$user_class->make_user_name($p1)."</td>
                <td id='dottedRow'>".$user_class->make_user_name($p2)."</td>
                <td id='dottedRow'>".$row['p1_score']."/".$row['p2_score']."</td>
                <td id='dottedRow'>".$row['bet']." Points</td>
                <td id='dottedRow'>".$winner."</td>
          </tr>";

}
?>
</form>
</table>
</div><!--contentcontent--><div class="contentfoot"></div><!--contentfoot-->

</div>

<?php
include("footer.php");
?>
