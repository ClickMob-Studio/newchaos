<?php

include "ajax_header.php";
function log50($better, $userid, $winner, $amount, $currency){
    global $db;
    $db->query("INSERT INTO 5050log (better, userid, winner, amount, currency) VALUES (?, ?, ?, ?,?)");
    $db->execute(array(
        $better,
        $userid,
        $winner,
        $amount,
        $currency
    ));
}
$user_class = new user($_SESSION['id']);
if($_GET['action'] == 'fecthLatest'){
    $db->query("SELECT * FROM 5050log ORDER BY `id` DESC LIMIT 10");
    $db->execute();
    $results = $db->fetch_row();
    $output = "<ul>"; // Start list to display bets
    foreach($results as $row){
        $amount = '';
        if($row['currency'] == 'cash'){
            $amount = prettynum($row['amount'], 1);
        }else if($row['currency'] == 'credits'){
            $amount = prettynum($row['amount']).' credits';
        }else if($row['currency'] == 'points'){
            $amount = prettynum($row['amount']).' points';
        }

        $winner = ($row['winner'] == $row['userid']) ? $row['winner'] : $row['better'];
        $loser = ($row['winner'] == $row['userid']) ? $row['better'] : $row['userid'];

        $output .= "<li>".formatName($winner)." won ".$amount." from ".formatName($loser)."</li>";
    }
    $output .= "</ul>"; // Close list
    echo $output;
}

if($_GET['action'] == 'update'){
    $db->query("SELECT * FROM fiftyfifty WHERE currency = 'cash'");
    $db->execute();
    $cash = $db->fetch_row();
    $formattedCash = array_map(function($cash) {
        $cash['formatted_userid'] = formatName($cash['userid']);
        if($_SESSION['id'] == $cash['userid']) {
            $cash['button'] = '<button class="removeCashButton" value="'.$cash['id'].'">Remove</button>';
        }else{
           $cash['button'] = '<button class="takeCashButton" value="'.$cash['id'].'">Take</button>';
        }
        return $cash;
    }, $cash);
    

    $db->query("SELECT * FROM fiftyfifty WHERE currency = 'points'");
    $db->execute();
    $points = $db->fetch_row();
    $formattedPoints = array_map(function($points) {
        $points['formatted_userid'] = formatName($points['userid']);
        if($_SESSION['id'] == $points['userid']) {
            $points['button'] = '<button class="removeCashButton" value="'.$points['id'].'">Remove</button>';
        }else{
           $points['button'] = '<button class="takePointsButton" value="'.$points['id'].'">Take</button>';
        }
        return $points;
    }, $points);
    

    $db->query("SELECT * FROM fiftyfifty WHERE currency = 'credits'");
    $db->execute();
    $credits = $db->fetch_row();
    $formattedCredits = array_map(function($credit) {
        $credit['formatted_userid'] = formatName($credit['userid']);
        if($_SESSION['id'] == $credit['userid']) {
            $credit['button'] = '<button class="removeCashButton" value="'.$credit['id'].'">Remove</button>';
        }else{
           $credit['button'] = '<button class="takeCreditButton" value="'.$credit['id'].'">Take</button>';
        }
        return $credit;
    }, $credits);
    
    
    $response = [
        'cash' => $formattedCash,
        'points' => $formattedPoints,
        'credits' => $formattedCredits
    ];

header('Content-Type: application/json');
echo json_encode($response);
}
if($_POST['action'] == 'pointbet'){
    $amount = intval($_POST['amount']);
    if($amount < 1){
        $text = "You can not place a bet of 0";
        echo json_encode(array(
            'text' => $text,));
        exit;
    }
    if($user_class->points < $amount){
        $text = "You don't have that many points.";
        echo json_encode(array(
            'text' => $text,));
        exit;
    }
    $user_class->points -= $amount;
    $db->query("UPDATE grpgusers SET points = $user_class->points WHERE id = ". $user_class->id);
    $db->execute();
    $text =  "You have placed a bet of ". $amount. " points.";
    echo json_encode(array(
        'text' => $text,
        'stats' => array(
            'points' => number_format($user_class->points),
            'money' => number_format($user_class->money),
        ),
    ));


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
        log50($fet['userid'], $user_class->id, $fet['userid'], $fet['amnt'], 'cash');
    }else{
        echo "You have won the bet for $".number_format($fet['amnt']);
        $db->query("UPDATE grpgusers SET money = money + ".$fet['amnt']." WHERE id = ".$user_class->id);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] to your bet of $".$fet['amnt']." and you lost", $user_class->id);
        log50($fet['userid'], $user_class->id, $user_class->id, $fet['amnt'], 'cash');
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
    $row = $db->fetch_row(true);
    if($row['currency'] == 'points'){
        echo "You have removed the bet for ".number_format($fet['amnt'])." points";
        $db->query("UPDATE grpgusers SET points = points + ".$fet['amnt']." WHERE id = ".$user_class->id);
        $db->execute();
    }else if($row['currency'] == 'cash'){
        echo "You have removed the bet for $".number_format($fet['amnt']);
        $db->query("UPDATE grpgusers SET money = money + ".$fet['amnt']." WHERE id = ".$user_class->id);
        $db->execute();
    }else if($row['currency'] == 'credits'){
        echo "You have removed the bet for ".number_format($fet['amnt'])." credits";
        $db->query("UPDATE grpgusers SET credits = credits + ".$fet['amnt']." WHERE id = ".$user_class->id);
        $db->execute();
    }
    $db->query("DELETE FROM fiftyfifty WHERE id = ".$id);
    $db->execute();
    
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
        $db->query("UPDATE grpgusers SET points = points + ".$amnt." WHERE id = ".$fet['userid']);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] to your bet of ".$fet['amnt']." points and you won", $user_class->id);
        log50($fet['userid'], $user_class->id, $fet['userid'], $fet['amnt'], 'points');
    }else{
        echo "You have won the bet for ".number_format($fet['amnt']." points");
        $db->query("UPDATE grpgusers SET points = points + ".$fet['amnt']." WHERE id = ".$user_class->id);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] to your bet of ".$fet['amnt']." points and you lost", $user_class->id);
        log50($fet['userid'], $user_class->id, $user_class->id, $fet['amnt'], 'points');
    }
    $db->query("DELETE FROM fiftyfifty WHERE id = ".$id);
    $db->execute();
    
}
if($_GET['action'] == 'takecreditbet'){
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
    if($user_class->credits < $fet['amnt']){
        echo "You do not have enough cedits to take this bet";
        exit;
    }
    if($user_class->id == $fet['userid']){
        echo "You cannot take your own bets";
        exit;
    }
    $rand = mt_rand(1,2);
    if($rand == 1){
        $amnt = $fet['amnt'] * 2;
        echo "You have lost the bet for ".number_format($fet['amnt']." credits");
        $db->query("UPDATE grpgusers SET credits = credits - ".$fet['amnt']." WHERE id = ".$user_class->id);
        $db->execute();
        $db->query("UPDATE grpgusers SET credits = credits + ".$amnt." WHERE id = ".$fet['userid']);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] to your bet of ".$fet['amnt']." credits and you won", $user_class->id);
        log50($fet['userid'], $user_class->id, $fet['userid'], $fet['amnt'], 'credits');
    }else{
        echo "You have won the bet for ".number_format($fet['amnt']." credits");
        $db->query("UPDATE grpgusers SET credits = credits + ".$fet['amnt']." WHERE id = ".$user_class->id);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] to your bet of ".$fet['amnt']." credits and you lost", $user_class->id);
        log50($fet['userid'], $user_class->id, $user_class->id, $fet['amnt'], 'credits');
    }
    $db->query("DELETE FROM fiftyfifty WHERE id = ".$id);
    $db->execute();
    
}


if($_POST['action'] == 'creditbet'){
    $amount = intval($_POST['amount']);
    if($amount < 0){
        echo "You can not place a bet of 0";
        exit;
    }
    if($user_class->credits < $amount){
        echo "You don't have that many credits.";
        exit;
    }
    $user_class->money -= $amount;
    $db->query("UPDATE grpgusers SET credits = $user_class->credits WHERE id = ". $user_class->id);
    $db->execute();
    echo "You have placed a bet of ". number_format($amount). " credits.";
    $db->query("INSERT INTO fiftyfifty(userid, amnt, currency) VALUES (".$user_class->id .", ".$amount.", 'credits')");
    $db->execute();
}