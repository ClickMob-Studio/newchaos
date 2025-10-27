<?php

$server_variables = Variable::GetAllValues();
function Validate($transaction_id)
{
    $req = 'action=status_trn&mb_trn_id=' . $transaction_id . '&email=telmo.cardoso@gmail.com&password=2da2231a32c8309dda1a4441bba77de8';

    $fp = fopen('https://www.moneybookers.com/app/query.pl?' . $req, 'r');

    if ($fp) {
        $str = fread($fp, 10000);
        if (!strstr($str, 'status=2')) {
            return false;
        }

        return true;
    }

    return false;
}
$TotalAmount = $_POST['amount'];
$CurrencyCode = $_POST['currency'];
$txn_id = $_POST['mb_transaction_id'];

// Server wide variables

if (!Validate($_POST['mb_transaction_id'])) {
    die();
}
if (mysqli_num_rows(DBi::$conn->query("SELECT * FROM `pagamentos` WHERE txn = 'MB:{$txn_id}'")) > 0) {
    die('');
}

if ($_POST['status'] != '2') {
    die();
}
$timesent = time();

// assign posted variables to local variables
$item_name = $_POST['field1'];
$payment_amount = (int) $_POST['amount'];
$payment_currency = $_POST['currency'];
if (!isset($_POST)) {
    header('HTTP/1.1 403 Forbidden');
    die;
}

// check that payment_amount/payment_currency are correct
if ($payment_currency != 'USD') {
    die;
}

// parse for pack
$packr = explode('|', $item_name);

if (str_replace('www.', '', $packr[0]) != str_replace('www.', '', $_SERVER['HTTP_HOST'])) {
    die;
}
$pack = $packr[2];

if ($packr[1] != 'DP') {
    die;
}
$pack = $packr[2];

if ($pack != '1' and $pack != '666' and $pack != '667' and $pack != '26' and $pack != '2' and $pack != '3' and $pack != '4' and $pack != '5' and $pack != '6' and $pack != '7' and $pack != '8' and $pack != '9' and $pack != '10' and $pack != '11' and $pack != '12' and $pack != '13' and $pack != '14' and $pack != '15' and $pack != '16' and $pack != '17' and $pack != '23' and $pack != '24' and $pack != '25' and $pack != '250' and $pack != '260' and $pack != '270' and $pack != '280' and $pack != '300') {
    die;
}

if ($pack == '1' && $payment_amount != '3') {
    die;
}
if ($pack == '2' && $payment_amount != '6') {
    die;
}
if ($pack == '3' && $payment_amount != '9') {
    die;
}
if ($pack == '4' && $payment_amount != '3') {
    die;
}
if ($pack == '5' && $payment_amount != '15') {
    die;
}
if ($pack == '6' && $payment_amount != '9') {
    die;
}
if ($pack == '7' && $payment_amount != '10') {
    die;
}
if ($pack == '8' && $payment_amount != '17') {
    die;
}
if ($pack == '9' && $payment_amount != '3') {
    die;
}
if ($pack == '10' && $payment_amount != '9') {
    die;
}
if ($pack == '11' && $payment_amount != '29') {
    die;
}
if ($pack == '12' && $payment_amount != '45') {
    die;
}
if ($pack == '13' && $payment_amount != '199') {
    die;
}
if ($pack == '14' && $payment_amount != '3') {
    die;
}
if ($pack == '15' && $payment_amount != '12') {
    die;
}
if ($pack == '16' && $payment_amount != '20') {
    die;
}
if ($pack == '17' && $payment_amount != '45') {
    die;
}
if ($pack == '23' && $payment_amount != '3') {
    die;
}
if ($pack == '24' && $payment_amount != '10') {
    die;
}
if ($pack == '25' && $payment_amount != '17') {
    die;
}
if ($pack == '26' && $payment_amount != '55') {
    die;
}

if ($pack == '250' && $payment_amount != '35') {
    die;
}
if ($pack == '260' && $payment_amount != '50') {
    die;
}
if ($pack == '270' && $payment_amount != '90') {
    die;
}
if ($pack == '280' && $payment_amount != '75') {
    die;
}
if ($pack == '300' && $payment_amount != '1000') {
    die;
}
if ($pack == '666' && $payment_amount != '75') {
    die;
}
if ($pack == '667' && $payment_amount != '55') {
    die;
}

// grab IDs
$buyer = $packr[3];
$for = $buyer;
$targetUser = UserFactory::getInstance()->getUser($for);
if (isset($packr[4]) && !empty($packr[4])) {
    $realbuyer = trim($packr[4]);
    $buyerUser = UserFactory::getInstance()->getUser($realbuyer); // real buyer who made the transaction
} else {
    $buyerUser = $targetUser;
}

/*
 * @author : Harish
 * @date: 04 Jan 2008
 * If purchased for personal shop then add file handling the personal shop.
 */
if (isset($packr[5]) && !empty($packr[5]) && $packr[5] == 'rps') {
    include 'ipn_shop_donator1.php';
    die();
}
/** end **/
$result = DBi::$conn->query('SELECT sum( quantity ) AS qtd FROM `inventory` WHERE userid = ' . $targetUser->id);
$worked2 = mysqli_fetch_array($result);
$result = DBi::$conn->query('SELECT amount FROM `land` WHERE userid = ' . $targetUser->id . ' and `city`=1');
$land = mysqli_fetch_array($result);

$worked2['qtd'] = $worked2['qtd'] + $land['amount'];
$res = DBi::$conn->query("INSERT INTO `pagamentos`
        (`buyer`, `for`, `RmoneyB`, `pointsB`, `moneyB`, `itemsB`, `runnersB`, `txn`) VALUES
         ('{$buyerUser->id}','{$targetUser->id}','" . $targetUser->realmoney . "','" . $targetUser->points . "','" . $targetUser->bank . "','" . $worked2['qtd'] . "','" . $targetUser->hookers . "', 'MB:" . $txn_id . "')");
if (!$res || DBi::$conn->affected_rows == 0) {
    die;
}
$id_pagamento = DBi::$conn->insert_id;
// all seems to be in order, credit it.

try {
    if ($pack == '1') {
        $t = '30day';
        $targetUser->AddToAttribute('rmdays', 30);
        $targetUser->AddToAttribute('bank', 10000);
        $targetUser->AddToAttribute('points', 50);
    } elseif ($pack == '2') {
        $t = '60day';
        $targetUser->AddToAttribute('rmdays', 60);
        $targetUser->AddToAttribute('bank', 25000);
        $targetUser->AddToAttribute('points', 125);
    } elseif ($pack == '3') {
        $t = '90day';
        $targetUser->AddToAttribute('rmdays', 90);
        $targetUser->AddToAttribute('bank', 75000);
        $targetUser->AddToAttribute('points', 200);
    } elseif ($pack == '4') {
        $targetUser->AddItems(14, 5);
        $t = '5pills';
    } elseif ($pack == '23') {
        $targetUser->AddItems(75, 5);
        $t = '5protection';
    } elseif ($pack == '7') {
        $targetUser->AddItems(14, 30);
        $t = '30pills';
    } elseif ($pack == '8') {
        $targetUser->AddItems(14, 60);
        $t = '60pills';
    } elseif ($pack == '24') {
        $targetUser->AddItems(75, 30);
        $t = '30protection';
    } elseif ($pack == '25') {
        $targetUser->AddItems(75, 60);
        $t = '60protection';
    } elseif ($pack == '26') {
        $t = 'UPack';
        $targetUser->AddItems(52, 1);
        $targetUser->AddItems(53, 1);
        $targetUser->AddToAttribute('points', 250);
    } elseif ($pack == '9') {
        $t = '250points';
        $targetUser->AddToAttribute('points', 250);
    } elseif ($pack == '10') {
        $t = '1000points';
        $targetUser->AddToAttribute('points', 1000);
    } elseif ($pack == '11') {
        $t = '5000points';
        $targetUser->AddToAttribute('points', 5000);
    } elseif ($pack == '12') {
        $t = '10000points';
        $targetUser->AddToAttribute('points', 10000);
    } elseif ($pack == '5') {
        $t = '9mmpistol';
        $targetUser->AddItems(38, 1);
        $targetUser->AddToAttribute('points', 250);
    } elseif ($pack == '6') {
        $t = 'BegPack';
        $targetUser->AddItems(36, 1);
        $targetUser->AddItems(41, 1);
        $targetUser->AddItems(14, 5);
        $targetUser->AddToAttribute('bank', 25000);
        $targetUser->AddToAttribute('points', 25);
    } elseif ($pack == '13') {
        $t = 'InPack';
        $targetUser->AddToAttribute('rmdays', 30);
        $targetUser->AddToAttribute('bank', 100000);
        $targetUser->AddItems(14, 100);
        $targetUser->AddToAttribute('points', 40000);
    } elseif ($pack == '14') {
        $targetUser->AddLand(1, 2);
        $t = '2acres';
    } elseif ($pack == '15') {
        $targetUser->AddLand(1, 10);
        $t = '10acres';
    } elseif ($pack == '16') {
        $targetUser->AddLand(1, 20);
        $t = '20acres';
    } elseif ($pack == '17') {
        $t = 'Killer';
        $targetUser->AddItems(45, 1);
        $targetUser->AddItems(50, 1);
        $targetUser->AddItems(14, 20);
        $targetUser->AddToAttribute('points', 2000);
    } elseif ($pack == '250') {
        $t = 'HTML Name';
    } elseif ($pack == '260') {
        $t = 'Simple Javascript Name';
    } elseif ($pack == '270') {
        $t = 'Advanced Javascript Name';
    } elseif ($pack == '280') {
        $t = 'Image Name';
    }
} catch (FailedResult $e) {
    $error = $e->getView();
    if (strpos($error, 'POINTS_ERR') !== false) {
        $temp = explode('|', $error);
        $pointsCredited = (int) $temp[1];
    }

    User::SNotify($targetUser->id, sprintf(MAXPOINTS_USER_NOTIFY, $t, MAX_POINTS, $pointsCredited), COM_ERROR);
    User::SNotify(ADMIN_USER_ID, sprintf(MAXPOINTS_ADMIN_NOTIFY, $t, $targetUser->id, MAX_POINTS, $pointsCredited), COM_ERROR);
}
$targetUser->AddPoints(20);
$result = DBi::$conn->query('SELECT sum( quantity ) AS qtd FROM `inventory` WHERE userid = ' . $targetUser->id);
$worked2 = mysqli_fetch_array($result);
$result = DBi::$conn->query('SELECT amount FROM `land` WHERE userid = ' . $targetUser->id . ' and `city`=1');
$land = mysqli_fetch_array($result);
$worked2['qtd'] = $worked2['qtd'] + $land['amount'];
$pagamento20 = floatval($payment_amount * 0.2);
$pagamento80 = floatval($payment_amount * 0.8);
$idref1 = '';
$idref2 = '';
$idref3 = '';
$monthSeconds = REFERRAL_VALIDITY_TIME;
$now = time();
$result = DBi::$conn->query('SELECT referrer FROM `referrals` WHERE `when` > ' . ($now - $monthSeconds) . ' and id = ' . $targetUser->id);
$worked3 = mysqli_fetch_array($result);

$willcredit = 2;
if (($worked3['referrer'] == '6316') || ($worked3['referrer'] == '95087') || ($worked3['referrer'] == '98155')) {
    $willcredit = rand(0, 2);
}

if ($pack == '250' or $pack == '260' or $pack == '270' or $pack == '280' or $pack == '300') {
    $willcredit == 0;
}

if ($willcredit == 2) {
    if ($worked3) {
        $idref1 = $worked3['referrer'];
        $newmoney = floatval($payment_amount * 0.1);
        User::SAddRealMoney($idref1, $newmoney);
        $result = DBi::$conn->query('SELECT referrer FROM `referrals` WHERE `when` > ' . ($now - $monthSeconds) . ' and id = ' . $idref1);
        $worked3 = mysqli_fetch_array($result);
        if ($worked3) {
            $idref2 = $worked3['referrer'];
            $newmoney = floatval($payment_amount * 0.07);
            User::SAddRealMoney($idref2, $newmoney);
            $result = DBi::$conn->query('SELECT referrer FROM `referrals` WHERE `when` > ' . ($now - $monthSeconds) . ' and id = ' . $idref2);
            $worked3 = mysqli_fetch_array($result);
            if ($worked3) {
                $idref3 = $worked3['referrer'];
                $newmoney = floatval($payment_amount * 0.03);
                User::SAddRealMoney($idref3, $newmoney);
            }
        }
    }
}

DBi::$conn->query("UPDATE `pagamentos` SET `type`='{$t}', `time`='{$timesent}', `RmoneyA`='" . $targetUser->realmoney . "', `pointsA`='" . $targetUser->points . "', 	`moneyA`='" . $targetUser->bank . "', `itemsA`='" . $worked2['qtd'] . "', `runnersA`='" . $targetUser->hookers . "', `amountM`='" . $payment_amount . "', `refAmount`='" . $pagamento20 . "', `PSamount`='" . $pagamento80 . "' WHERE id_pagamento=" . $id_pagamento);
DBi::$conn->query("UPDATE `pagamentos` SET `refId1`='{$idref1}', `refId2`='{$idref2}',`refId3`='{$idref3}' WHERE id_pagamento=" . $id_pagamento);

// process payment
if ($pack == '250' or $pack == '260' or $pack == '270' or $pack == '280') {
    $targetUser->Notify(PAYMENT_SOMEONECONTACT, 'Donation');

    $buyerUser->Notify(sprintf(PAYMENT_USERBOUGHT, $t), 'Donation');
    if ($buyerUser->id != $targetUser->id) {
        $targetUserName = User::SGetFormattedName($targetUser->id);
        $buyerUser->Notify(sprintf(PAYMENT_CREDITED, $targetUserName, $t), 'Donation');
    }

    User::SNotify(2000, sprintf(PAYMENT_NEWNAME, $targetUser->id, $t));
} elseif ($pack == '300') {
    $targetUser->Notify(PAYMENT_RECEIVESTUFF, 'Donation');

    $buyerUser->Notify(sprintf(PAYMENT_USERBOUGHT, $t), 'Donation');
    if ($buyerUser->id != $targetUser->id) {
        $targetUserName = User::SGetFormattedName($targetUser->id);
        $buyerUser->Notify(sprintf(PAYMENT_CREDITED, $targetUserName, $t), 'Donation');
    }
    User::SNotify(2000, PAYMENT_SPECIALDELIVERED);
} elseif ($pack == '667') {
} else {
    $targetUser->Notify(sprintf(PAYMENT_YOUCREDITED, $t, $payment_amount), 'Donation');

    $buyerUser->Notify(sprintf(PAYMENT_USERBOUGHT, $t), 'Donation');
    if ($buyerUser->id != $targetUser->id) {
        $targetUserName = User::SGetFormattedName($targetUser->id);
        $buyerUser->Notify(sprintf(PAYMENT_CREDITED, $targetUserName, $t), 'Donation');
    }
}
if (isset($targetUser)) {
    $targetUserName = User::SGetFormattedName($targetUser->id);
}
