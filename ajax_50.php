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
    echo "You have placed a bet of ". $amount. " points.";
    $db->query("INSERT INTO fiftyfifty(userid, amnt, currency) VALUES (".$user_class->id .", ".$amount.", 'points')");

}