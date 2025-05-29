<?php

include "ajax_header.php";
function log50($better, $userid, $winner, $amount, $currency)
{
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

$db->query("SELECT COUNT(id) FROM fiftyfifty WHERE userid = " . $user_class->id);
$db->execute();
$ffCount = $db->fetch_single();

if (isset($_GET['action']) && $_GET['action'] == 'fetchLatest') {
    // <CachePotential>
    $db->query("SELECT * FROM 5050log ORDER BY `id` DESC LIMIT 10");
    $db->execute();
    $results = $db->fetch_row();
    $output = "<ul>"; // Start list to display bets
    foreach ($results as $row) {
        $amount = '';
        if ($row['currency'] == 'cash') {
            $amount = prettynum($row['amount'], 1);
        } else if ($row['currency'] == 'credits') {
            $amount = prettynum($row['amount']) . ' credits';
        } else if ($row['currency'] == 'points') {
            $amount = prettynum($row['amount']) . ' points';
        }

        $winner = ($row['winner'] == $row['userid']) ? $row['winner'] : $row['better'];
        $loser = ($row['winner'] == $row['userid']) ? $row['better'] : $row['userid'];

        $output .= "<li>" . formatName($winner) . " won " . $amount . " from " . formatName($loser) . "</li>";


    }
    $output .= "</ul>"; // Close list
    echo $output;
}

if (isset($_GET['action']) && $_GET['action'] == 'update') {
    $db->query("SELECT * FROM fiftyfifty WHERE currency = 'cash'");
    $db->execute();
    $cash = $db->fetch_row();
    $formattedCash = array_map(function ($cash) {
        $cash['formatted_userid'] = formatName($cash['userid']);
        if ($_SESSION['id'] == $cash['userid']) {
            $cash['button'] = '<button class="removeCashButton" value="' . $cash['id'] . '">Remove</button>';
        } else {
            $cash['button'] = '<button class="takeCashButton" value="' . $cash['id'] . '">Take</button>';
        }
        return $cash;
    }, $cash);


    $db->query("SELECT * FROM fiftyfifty WHERE currency = 'points'");
    $db->execute();
    $points = $db->fetch_row();
    $formattedPoints = array_map(function ($points) {
        $points['formatted_userid'] = formatName($points['userid']);
        if ($_SESSION['id'] == $points['userid']) {
            $points['button'] = '<button class="removeCashButton" value="' . $points['id'] . '">Remove</button>';
        } else {
            $points['button'] = '<button class="takePointsButton" value="' . $points['id'] . '">Take</button>';
        }
        return $points;
    }, $points);


    $db->query("SELECT * FROM fiftyfifty WHERE currency = 'credits'");
    $db->execute();
    $credits = $db->fetch_row();
    $formattedCredits = array_map(function ($credit) {
        $credit['formatted_userid'] = formatName($credit['userid']);
        if ($_SESSION['id'] == $credit['userid']) {
            $credit['button'] = '<button class="removeCashButton" value="' . $credit['id'] . '">Remove</button>';
        } else {
            $credit['button'] = '<button class="takeCreditButton" value="' . $credit['id'] . '">Take</button>';
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

if (isset($_POST['action']) && $_POST['action'] == 'pointbet') {
    if ($ffCount >= 5) {
        $text = "You can only place 5 x 5050 bets at a time.";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    $amount = intval($_POST['amount']);
    if ($amount < 1) {
        $text = "You can not place a bet of 0";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    if ($user_class->points < $amount) {
        $text = "You don't have that many points.";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    $user_class->points -= $amount;
    $db->query("UPDATE grpgusers SET points = $user_class->points WHERE id = " . $user_class->id);
    $db->execute();
    $text = "You have placed a bet of " . $amount . " points.";
    echo json_encode(array(
        'text' => $text,
        'stats' => array(
            'points' => number_format($user_class->points),
            'money' => number_format($user_class->money),
        ),
    ));


    $db->query("INSERT INTO fiftyfifty(userid, amnt, currency, timestamp) VALUES (?, ?, 'points', ?)");
    $db->execute([$user_class->id, $amount, time()]);
}

if (isset($_POST['action']) && $_POST['action'] == 'cashbet') {
    if ($ffCount >= 5) {
        $text = "You can only place 5 x 5050 bets at a time.";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    $amount = intval($_POST['amount']);
    if ($amount < 0) {
        $text = "You can not place a bet of 0";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    if ($user_class->money < $amount) {
        $text = "You don't have that much money.";
        echo json_encode(array(
            'text' => $text,
        ));

        exit;
    }
    $user_class->money -= $amount;
    $db->query("UPDATE grpgusers SET money = $user_class->money WHERE id = " . $user_class->id);
    $db->execute();
    $text = "You have placed a bet of $" . number_format($amount) . ".";
    echo json_encode(array(
        'text' => $text,
        'money' => '$' . number_format($user_class->money)
    ));
    $db->query("INSERT INTO fiftyfifty(userid, amnt, currency) VALUES (" . $user_class->id . ", " . $amount . ", 'cash')");
    $db->execute();
}

if (isset($_POST['action']) && $_POST['action'] == 'takecashbet') {
    if (!isset($_POST['id'])) {
        echo "That bet does not appear to be valid";
        exit();
    }
    $id = intval($_POST['id']);
    $db->query("SELECT * FROM fiftyfifty WHERE id = ?");
    $db->execute(array($id));
    if ($db->num_rows() < 1) {

        $text = "That bet does not appear to be valid";
        json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    $fet = $db->fetch_row(true);
    if ($user_class->money < $fet['amnt']) {
        $text = "You do not have enough money to take this bet";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    if ($user_class->id == $fet['userid']) {
        $text = "You cannot take your own bets";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    $rand = mt_rand(1, 2);
    if ($rand == 1) {
        $amnt = $fet['amnt'] * 2;

        $text = "You have lost the bet for $" . number_format($fet['amnt']);
        $user_class->money -= $fet['amnt'];
        echo json_encode(array(
            'text' => $text,
            'money' => '$' . number_format($user_class->money)
        ));
        $db->query("UPDATE grpgusers SET money = money - " . $fet['amnt'] . " WHERE id = " . $user_class->id);
        $db->execute();
        $db->query("UPDATE grpgusers SET money = money + " . $amnt . " WHERE id = " . $fet['userid']);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] took your bet of $" . $fet['amnt'] . " and you won", $user_class->id);
        log50($fet['userid'], $user_class->id, $fet['userid'], $fet['amnt'], 'cash');
    } else {
        $text = "You have won the bet for $" . number_format($fet['amnt']);
        $user_class->money += $fet['amnt'];
        echo json_encode(array(
            'text' => $text,
            'money' => '$' . number_format($user_class->money)
        ));
        $db->query("UPDATE grpgusers SET money = money + " . $fet['amnt'] . " WHERE id = " . $user_class->id);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] took your bet of $" . $fet['amnt'] . " and you lost", $user_class->id);
        log50($fet['userid'], $user_class->id, $user_class->id, $fet['amnt'], 'cash');
    }
    $db->query("DELETE FROM fiftyfifty WHERE id = " . $id);
    $db->execute();
}

if (isset($_POST['action']) && $_POST['action'] == 'removecashbet') {
    if (!isset($_POST['id'])) {
        $text = "That bet does not appear to be valid";
        echo json_encode(array(
            'text' => $text,
        ));
        exit();
    }
    $id = intval($_POST['id']);
    $db->query("SELECT * FROM fiftyfifty WHERE id = ?");
    $db->execute(array($id));
    if ($db->num_rows() < 1) {
        $text = "That bet does not appear to be valid";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    $fet = $db->fetch_row(true);
    if ($user_class->id != $fet['userid']) {
        $text = "You cannot delete someone elses bet";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    $row = $db->fetch_row(true);
    if ($row['currency'] == 'points') {
        $text = "You have removed the bet for " . number_format($fet['amnt']) . " points";
        $user_class->points += $fet['$amnt'];
        echo json_encode(array(
            'text' => $text,
            'points' => number_format($user_class->points),
            'money' => '$' . number_format($user_class->money),
            'credits' => number_format($user_class->credits) . ' credits'
        ));
        $db->query("UPDATE grpgusers SET points = points + " . $fet['amnt'] . " WHERE id = " . $user_class->id);
        $db->execute();
    } else if ($row['currency'] == 'cash') {
        $text = "You have removed the bet for $" . number_format($fet['amnt']);
        $user_class->money += $fet['amnt'];
        echo json_encode(array(
            'text' => $text,
            'money' => '$' . number_format($user_class->money),
            'points' => number_format($user_class->points),
            'credits' => number_format($user_class->credits) . ' credits'
        ));
        $db->query("UPDATE grpgusers SET money = money + " . $fet['amnt'] . " WHERE id = " . $user_class->id);
        $db->execute();
    } else if ($row['currency'] == 'credits') {
        $text = "You have removed the bet for " . number_format($fet['amnt']) . " credits";
        $user_class->credits += $fet['amnt'];
        echo json_encode(array(
            'text' => $text,
            'points' => number_format($user_class->points),
            'credits' => number_format($user_class->credits) . ' credits',
            'money' => '$' . number_format($user_class->money)
        ));
        $db->query("UPDATE grpgusers SET credits = credits + " . $fet['amnt'] . " WHERE id = " . $user_class->id);
        $db->execute();
    }
    $db->query("DELETE FROM fiftyfifty WHERE id = " . $id);
    $db->execute();

}

if (isset($_POST['action']) && $_POST['action'] == 'takepointbet') {
    if (!isset($_POST['id'])) {
        echo "That bet does not appear to be valid";
        exit();
    }
    $id = intval($_POST['id']);
    $db->query("SELECT * FROM fiftyfifty WHERE id = ?");
    $db->execute(array($id));
    if ($db->num_rows() < 1) {

        $text = "That bet does not appear to be valid";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    $fet = $db->fetch_row(true);
    if ($user_class->points < $fet['amnt']) {
        $text = "You do not have enough points to take this bet";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    if ($user_class->id == $fet['userid']) {
        $text = "You cannot take your own bets";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    $rand = mt_rand(1, 2);
    if ($rand == 1) {
        $amnt = $fet['amnt'] * 2;
        $user_class->points -= $fet['amnt'];
        $text = "You have lost the bet for " . number_format($fet['amnt'] . " points");
        echo json_encode(array(
            'text' => $text,
            'points' => number_format($user_class->points)
        ));
        $db->query("UPDATE grpgusers SET points = points - " . $fet['amnt'] . " WHERE id = " . $user_class->id);
        $db->execute();
        $db->query("UPDATE grpgusers SET points = points + " . $amnt . " WHERE id = " . $fet['userid']);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] took your bet of " . $fet['amnt'] . " points and you won", $user_class->id);
        log50($fet['userid'], $user_class->id, $fet['userid'], $fet['amnt'], 'points');
    } else {
        $text = "You have won the bet for " . number_format($fet['amnt'] . " points");
        $user_class->points += $fet['amnt'];
        echo json_encode(array(
            'text' => $text,
            'points' => number_format($user_class->points)
        ));
        $db->query("UPDATE grpgusers SET points = points + " . $fet['amnt'] . " WHERE id = " . $user_class->id);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] took your bet of " . $fet['amnt'] . " points and you lost", $user_class->id);
        log50($fet['userid'], $user_class->id, $user_class->id, $fet['amnt'], 'points');
    }
    $db->query("DELETE FROM fiftyfifty WHERE id = " . $id);
    $db->execute();

}
if (isset($_POST['action']) && $_POST['action'] == 'takecreditbet') {
    if (!isset($_POST['id'])) {
        $text = "That bet does not appear to be valid";
        echo json_encode(array(
            'text' => $text,
        ));
        exit();
    }
    $id = intval($_POST['id']);
    $db->query("SELECT * FROM fiftyfifty WHERE id = ?");
    $db->execute(array($id));
    if ($db->num_rows() < 1) {
        $text = "That bet does not appear to be valid";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    $fet = $db->fetch_row(true);
    if ($user_class->credits < $fet['amnt']) {
        $text = "You do not have enough cedits to take this bet";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    if ($user_class->id == $fet['userid']) {
        $text = "You cannot take your own bets";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    $rand = mt_rand(1, 2);
    if ($rand == 1) {
        $amnt = $fet['amnt'] * 2;
        $user_class->credits -= $fet['amnt'];
        $text = "You have lost the bet for " . number_format($fet['amnt'] . " credits");
        echo json_encode(array(
            'text' => $text,
            'credits' => number_format($user_class->credits)
        ));
        $db->query("UPDATE grpgusers SET credits = credits - " . $fet['amnt'] . " WHERE id = " . $user_class->id);
        $db->execute();
        $db->query("UPDATE grpgusers SET credits = credits + " . $amnt . " WHERE id = " . $fet['userid']);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] took your bet of " . $fet['amnt'] . " credits and you won", $user_class->id);
        log50($fet['userid'], $user_class->id, $fet['userid'], $fet['amnt'], 'credits');
    } else {
        $user_class->credits = $fet['amnt'];
        $text = "You have won the bet for " . number_format($fet['amnt'] . " credits");
        echo json_encode(array(
            'text' => $text,
            'credits' => number_format($user_class->credits)
        ));
        $db->query("UPDATE grpgusers SET credits = credits + " . $fet['amnt'] . " WHERE id = " . $user_class->id);
        $db->execute();
        Send_Event($fet['userid'], "[-_USERID_-] took your bet of " . $fet['amnt'] . " credits and you lost", $user_class->id);
        log50($fet['userid'], $user_class->id, $user_class->id, $fet['amnt'], 'credits');
    }
    $db->query("DELETE FROM fiftyfifty WHERE id = " . $id);
    $db->execute();

}

if (isset($_POST['action']) && $_POST['action'] == 'creditbet') {
    if ($ffCount >= 5) {
        $text = "You can only place 5 x 5050 bets at a time.";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    $amount = intval($_POST['amount']);
    if ($amount < 0) {
        $text = "You can not place a bet of 0";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    if ($user_class->credits < $amount) {
        $text = "You don't have that many credits.";
        echo json_encode(array(
            'text' => $text,
        ));
        exit;
    }
    $user_class->credits -= $amount;
    $db->query("UPDATE grpgusers SET credits = $user_class->credits WHERE id = " . $user_class->id);
    $db->execute();
    $text = "You have placed a bet of " . number_format($amount) . " credits.";
    echo json_encode(array(
        'text' => $text,
        'credits' => $user_class->credits . ' credits'
    ));
    $db->query("INSERT INTO fiftyfifty(userid, amnt, currency) VALUES (" . $user_class->id . ", " . $amount . ", 'credits')");
    $db->execute();
}