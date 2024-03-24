<?php
session_start();
include 'dbcon.php';
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
  $IP = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
  $IP = $_SERVER['REMOTE_ADDR'];
}

if ($_POST['username'] != NULL & $_POST['password'] != NULL) {
    $username = mysql_real_escape_string($_POST['username']);
    $password = sha1(mysql_real_escape_string($_POST['password']));
    $password2 = fuzzehCrypt($password);
    $result = mysql_query("SELECT * FROM grpgusers WHERE loginame LIKE '$username'") or die(Message("Sorry, your username and password combination are invalid."));
    $worked = mysql_fetch_array($result);
    $ban1 = mysql_query("SELECT * FROM bans WHERE id = '{$worked['id']}' AND (type = 'freeze' OR type = 'perm')");
    $ban = mysql_num_rows($ban1);
    $q = mysql_query("SELECT * FROM ip_bans WHERE = {$IP}");
    $ipban = mysql_num_rows($q);

    //Lowercase username stored and given, then perform check of equality (bypass capitol letters)
    $stored_username = strtolower($worked['loginame']);
    $given_username = strtolower($username);

    if ($stored_username == $given_username && ($worked['password'] == $password || $worked['password'] == $password2)) {
        if ($worked['ban/freeze'] == 1 || $ban > 0 || $ipban > 0) {
            $_SESSION['failmessage'] = 'Your account has been banned';
            header('Location: index.php');
        }
        $_SESSION["id"] = $worked['id'];
        mysql_query("DELETE FROM sessions WHERE userid={$worked['id']}");
        mysql_query("INSERT INTO sessions VALUES({$worked['id']},'{$_COOKIE['PHPSESSID']}','emptyfornow')");
        header('Location: index.php');
    } else {
        $_SESSION['failmessage'] = 'Invalid username or password';
        header('Location: index.php');
    }
} else {
	$_SESSION['failmessage'] = 'You have not entered a username or password.';
    header('Location: index.php');
}
function fuzzehCrypt($pass) {
    return crypt($pass, '$6$rounds=5000$awrgwrnuBUIEF89243t89bNFAEb942$');
}
?>

