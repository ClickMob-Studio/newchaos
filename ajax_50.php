<?php

include "ajax_header.php";
$user_class = new user($_SESSION['id']);
if($_GET['action'] == 'pointbet'){
    $amount = intval($_GET['amount']);
    if($amount < 0){
        echo "You can not place a bet of 0";
        exit;
    }
    if($user_class->points < $amount){
        echo "You don't have that many points.";
        exit;
    }
    $user_class->points -= $amount;
    $db->query("UPDATE grpgusers SET points = $user_class->points WHERE id = ". $user_class->id);
    $db->execute();
    echo "You have placed a bet of ". $amount. " points.";
    $db->query("INSERT INTO fiftyfifty(userid, amnt, currency) VALUES (".$user_class->id .", ".$amount.", 'points')");
    $db->execute();
}
if($_GET['action'] == 'cashbet'){
    $amount = intval($_GET['amount']);
    if($amount < 0){
        echo "You can not place a bet of 0";
        exit;
    }
    if($user_class->money < $amount){
        echo "You don't have that much money.";
        exit;
    }
    $user_class->money -= $amount;
    $db->query("UPDATE grpgusers SET money = $user_class->money WHERE id = ". $user_class->id);
    $db->execute();
    echo "You have placed a bet of $". number_format($amount). ".";
    $db->query("INSERT INTO fiftyfifty(userid, amnt, currency) VALUES (".$user_class->id .", ".$amount.", 'cash')");
    $db->execute();
}

if($_GET['action'] == 'takecashbet'){
    if(!isset($_GET['id'])){
        echo "That bet does not appear to be valid";
        exit();
    }
    $id = intval($_GET['id']);
    $db->query("SELECT * FROM fiftyfifty WHERE id = ?");
    $db->execute(array($id));
    if($db->num_rows() < 1){
        echo "That bet does not appear to be valid";
        exit;
    }
    $fet = $db->fetch_row(true);
    if($user_class->money < $fet['amnt']){
        echo "You do not have enough money to take this bet";
        exit;
    }
    if($user_class->id == $fet['userid']){
        echo "You cannot take your own bets";
        exit;
    }
    $rand = mt_rand(1,2);
    if($rand == 1){
        $amnt = $fet['amnt'] * 2;
        echo "You have lost the bet for $".number_format($fet['amnt']);
        $db->query("UPDATE grpgusers SET money = money - ".$fet['amnt']." WHERE id = ".$user_class->id);
        $db->execute();
        $db->query("UPDATE grpgusers SET money = money + ".$amnt." WHERE id = ".$fet['userid']);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] to your bet of $".$fet['amnt']." and you won", $user_class->id);
    }else{
        echo "You have won the bet for $".number_format($fet['amnt']);
        $db->query("UPDATE grpgusers SET money = money + ".$fet['amnt']." WHERE id = ".$user_class->id);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] to your bet of $".$fet['amnt']." and you lost", $user_class->id);
    }
    $db->query("DELETE FROM fiftyfifty WHERE id = ".$id);
    $db->execute();
}

if($_GET['action'] == 'removecashbet'){
    if(!isset($_GET['id'])){
        echo "That bet does not appear to be valid";
        exit();
    }
    $id = intval($_GET['id']);
    $db->query("SELECT * FROM fiftyfifty WHERE id = ?");
    $db->execute(array($id));
    if($db->num_rows() < 1){
        echo "That bet does not appear to be valid";
        exit;
    }
    $fet = $db->fetch_row(true);
    if($user_class->id != $fet['userid']){
        echo "You cannot delete someone elses bet";
        exit;
    }
    $db->query("DELETE FROM fiftyfifty WHERE id = ".$id);
    $db->execute();
    echo "You have removed the bet for $".number_format($fet['amnt']);
    Send_Event($user_class->id, "You removed your bet of $".number_format($fet['amnt']), $fet['userid']);

}

if($_GET['action'] == 'takepointbet'){
    if(!isset($_GET['id'])){
        echo "That bet does not appear to be valid";
        exit();
    }
    $id = intval($_GET['id']);
    $db->query("SELECT * FROM fiftyfifty WHERE id = ?");
    $db->execute(array($id));
    if($db->num_rows() < 1){
        echo "That bet does not appear to be valid";
        exit;
    }
    $fet = $db->fetch_row(true);
    if($user_class->points < $fet['amnt']){
        echo "You do not have enough points to take this bet";
        exit;
    }
    if($user_class->id == $fet['userid']){
        echo "You cannot take your own bets";
        exit;
    }
    $rand = mt_rand(1,2);
    if($rand == 1){
        $amnt = $fet['amnt'] * 2;
        echo "You have lost the bet for ".number_format($fet['amnt']." points");
        $db->query("UPDATE grpgusers SET points = points - ".$fet['amnt']." WHERE id = ".$user_class->id);
        $db->execute();
        $db->query("UPDATE grpgusers SET points = points + ".$amnt)." WHERE id = ".$fet['userid']);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] to your bet of ".$fet['amnt']." points and you won", $user_class->id);
    }else{
        echo "You have won the bet for $".number_format($fet['amnt']);
        $db->query("UPDATE grpgusers SET points = points + ".$fet['amnt']." WHERE id = ".$user_class->id);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] to your bet of ".$fet['amnt']." points and you lost", $user_class->id);
    }
    $db->query("DELETE FROM fiftyfifty WHERE id = ".$id);
    $db->execute();
}