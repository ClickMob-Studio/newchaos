<?php
session_start();
include 'dbcon.php';

// Get client's IP address
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
  $IP = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
  $IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
  $IP = $_SERVER['REMOTE_ADDR'];
}

if (!empty($_POST['username']) && !empty($_POST['password'])) {
    $username = $_POST['username'];
    $password = sha1($_POST['password']); // You might need to use a better hashing algorithm than sha1
    $password2 = fuzzehCrypt($password);

    // Prepare and execute query to fetch user details
    $query = "SELECT * FROM grpgusers WHERE loginame LIKE ?";
    $statement = $db->prepare($query);
    $statement->execute([$username]);
    $worked = $statement->fetch(PDO::FETCH_ASSOC);

    // Prepare and execute query to check if user is banned
    $banQuery = "SELECT * FROM bans WHERE id = ? AND (type = 'freeze' OR type = 'perm')";
    $banStatement = $db->prepare($banQuery);
    $banStatement->execute([$worked['id']]);
    $ban = $banStatement->rowCount();

    // Prepare and execute query to check if IP is banned
    $ipBanQuery = "SELECT * FROM ip_bans WHERE ip = ?";
    $ipBanStatement = $db->prepare($ipBanQuery);
    $ipBanStatement->execute([$IP]);
    $ipban = $ipBanStatement->rowCount();

    //Lowercase username stored and given, then perform check of equality (bypass capital letters)
    $stored_username = strtolower($worked['loginame']);
    $given_username = strtolower($username);

    if ($stored_username == $given_username && ($worked['password'] == $password || $worked['password'] == $password2)) {
        if ($worked['ban/freeze'] == 1 || $ban > 0 || $ipban > 0) {
            $_SESSION['failmessage'] = 'Your account has been banned';
            header('Location: /index.php');
            exit();
        }
        $_SESSION["id"] = $worked['id'];
        // Using PDO to perform queries
        $db->query("DELETE FROM sessions WHERE userid={$worked['id']}");
        $db->query("INSERT INTO sessions VALUES({$worked['id']},'{$_COOKIE['PHPSESSID']}','emptyfornow')");
        header('Location: /index.php');
        exit();
    } else {
        $_SESSION['failmessage'] = 'Invalid username or password';
        header('Location: /home.php');
        exit();
    }
} else {
    $_SESSION['failmessage'] = 'You have not entered a username or password.';
    header('Location: home.php');
    exit();
}

// Function to mimic the fuzzehCrypt function (you might need to adjust this according to your needs)
function fuzzehCrypt($pass) {
    return crypt($pass, '$6$rounds=5000$awrgwrnuBUIEF89243t89bNFAEb942$');
}
?>
