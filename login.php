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

if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = sha1($_POST['password']);
    $password2 = fuzzehCrypt($password);
    $query = "SELECT * FROM grpgusers WHERE loginame = ?";
     $statement = $db->prepare($query);
    $statement->execute([$username]);
     $worked = $statement->fetch(PDO::FETCH_ASSOC);
     $banQuery = "SELECT * FROM bans WHERE id = ? AND (type = 'freeze' OR type = 'perm')";
     $banStatement = $db->prepare($banQuery);
     $banStatement->execute([$worked['id']]);
     $ban = $banStatement->rowCount();
     //Lowercase username stored and given, then perform check of equality (bypass capitol letters)
     $stored_username = strtolower($worked['loginame']);
     $given_username = strtolower($username);

    if ($stored_username == $given_username && ($worked['password'] == $password || $worked['password'] == $password2)) {
         if ($worked['ban/freeze'] == 1 || $ban > 0 || $ipban > 0) {
             $_SESSION['failmessage'] = 'Your account has been banned';
             header('Location: index.php');
         }
        $_SESSION["id"] = $worked['id'];

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

