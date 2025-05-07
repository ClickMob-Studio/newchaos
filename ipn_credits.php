<?php
include "dbcon.php";
include "classes.php";
include "database/pdo_class.php";
file_put_contents('ipn_log/' . time() . '.txt', file_get_contents('php://input'));
// Function to get the current microtime
function microtime_float()
{
    $time = microtime();
    return (double) substr($time, 11) + (double) substr($time, 0, 8);
}

// Get the current time
$time = microtime_float();

// Construct the validation request
$req = 'cmd=_notify-validate';
foreach ($_POST as $key => $value) {
    $value = urlencode(stripslashes($value));
    $req .= "&$key=$value";
}

$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen('www.paypal.com', 80, $errno, $errstr, 30);
$item_name = $_POST['item_name'];
$item_number = $_POST['item_number'];
$payment_status = $_POST['payment_status'];
$creditsbought = $_POST['mc_gross'];
$payment_amount = $_POST['mc_gross'];
$payment_currency = $_POST['mc_currency'];
$txn_id = $_POST['txn_id'];
$receiver_email = $_POST['receiver_email'];
$payer_email = $_POST['payer_email'];
$first = $_POST['first_name'];
$last = $_POST['last_name'];
$quantity = $_POST['quantity'];
$userId = $_POST['custom'];
//if($receiver_email != "ExcelledGaming@outlook.com")
//  die();

//$custom = explode(',', $_POST['custom']);
//$boost = ($custom[0] == 1) ? true : false;
//$userId = $custom[1];
$boost = true;
$result1000 = mysql_query("INSERT INTO `ipn` (`itemname`, `date`, `itemnumber`, `creditsbought`, `paymentstatus`, `paymentamount`, `currency`, `txnid`, `receiveremail`, `payeremail`, `first`, `last`, `quantity`, `user_id`)" . "VALUES ('" . $item_name . "', '$time',  '" . $item_number . "', '" . $creditsbought . "', '" . $payment_status . "', '" . $payment_amount . "', '" . $payment_currency . "', '" . $txn_id . "', '" . $receiver_email . "', '" . $payer_email . "', '" . $first . "', '" . $last . "', '" . $quantity . "', '" . $userId . "')");
$result2 = mysql_query("SELECT * FROM `grpgusers` WHERE `id`='$userId'");
$worked = mysql_fetch_array($result2);

if ($payment_status == "Completed") {

    $userrmdays = $worked['credits'] + $creditsbought;
    $buyer = new User($userId);

    if ($boost && $buyer->donate_token > 0) {
        $creditsbought = (floor($creditsbought * 10)) * 2;
        mysql_query("UPDATE grpgusers SET donate_token = donate_token - 1 WHERE id = $buyer->id");
    } else {
        $creditsbought = floor($creditsbought * 10);
    }

    $creditsbought = $creditsbought * 2;

    $result = mysql_query("UPDATE `grpgusers` SET credits = credits + $creditsbought, christmasraffle = christmasraffle + $payment_amount, donationmonth = donationmonth + $creditsbought WHERE `id` = {$userId}");
    Send_Event($userId, "Your $creditsbought Credit(s) have just been credited. PayPal Transaction ID: " . $txn_id . ".", $userId);
    Send_Event(1059, "$payment_amount Dolla Donation for $creditsbought credits. by $userId. PayPal Transaction ID: " . $txn_id . ".", 1);
    Send_Event(1034, "$payment_amount Dolla Donation for $creditsbought credits. by $userId. PayPal Transaction ID: " . $txn_id . ".", 1);

    mysql_query("UPDATE bbusers SET donator = donator + $payment_amount WHERE userid = $userId");

    $referr = mysql_fetch_array(mysql_query("SELECT referrer FROM referrals WHERE referred = $userId AND credited = 1"));
    if ($referr) {
        if ($referr['referrer'] > 0) {
            $referrer = $referr['referrer'];
            $bonus = $creditsbought * 0.10;
            mysql_query("UPDATE `grpgusers` SET credits = credits + $bonus WHERE `id` = $referrer");
            Send_Event($referrer, "You have been credited with $bonus credit(s) as your referral " . formatName($userId) . " has donated", $referrer);
        }
    }

}
?>