<?php

include "ajax_header.php";

if($_GET['action'] == 'pointbet'){
    $user_class = new user($_SESSION['id']);
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
    $user_class = new user($_SESSION['id']);
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

    $rand = mt_rand(1,2);
    if($rand == 1){
        echo "You have lost the bet for $".number_format($fet['amnt']);
        $db->query("UPDATE grpgusers SET money = money - ".$fet['amnt']." WHERE id = ".$user_class->id);
        $db->query("UPDATE grpgusers SET money = money - ".$fet['amnt']." WHERE id = ".$fet['userid']);
        Send_Event($fet['userid'], $user_class->formattedname . " to your bet of $".$fet['amnt']." and you won");

    }
}