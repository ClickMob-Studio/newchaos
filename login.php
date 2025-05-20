<?php
require_once 'includes/functions.php';

start_session_guarded();

require_once 'dbcon.php';

// IP address determination using standard if-else
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $IP = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $IP = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $IP = $_SERVER['REMOTE_ADDR'];
}

if (!empty($_POST['username']) && !empty($_POST['password'])) {
    $username = $_POST['username'];
    $password = sha1($_POST['password']); // Moving to password_hash() recommended

    try {
        $query = "SELECT * FROM grpgusers WHERE loginame = ?";
        $statement = $db->prepare($query);
        $statement->execute([$username]);
        $user = $statement->fetch();

        if ($user) {
            $banQuery = "SELECT * FROM bans WHERE id = ? AND (type = 'freeze' OR type = 'perm')";
            $banStatement = $db->prepare($banQuery);
            $banStatement->execute([$user['id']]);
            $isBanned = $banStatement->rowCount() > 0;

            $stored_username = strtolower($user['loginame']);
            $given_username = strtolower($username);

            if ($stored_username === $given_username && ($user['password'] === $password)) {
                if ($user['ban/freeze'] == 1 || $isBanned) {
                    $_SESSION['failmessage'] = 'Your account has been banned';
                } else {
                    session_regenerate_id();

                    $bytes = openssl_random_pseudo_bytes(16);
                    $randomKey = bin2hex($bytes);
                    $queryInsertOrUpdate = "INSERT INTO sessions (userid, sessionid) VALUES (?, ?)
                            ON DUPLICATE KEY UPDATE sessionid = VALUES(sessionid)";

                    $statementInsertOrUpdate = $db->prepare($queryInsertOrUpdate);

                    $statementInsertOrUpdate->execute([$user['id'], $randomKey]);
                    $_SESSION['token'] = $randomKey;
                    $_SESSION["id"] = $user['id'];
                    header('Location: index.php');
                    exit;
                }
            } else {
                $_SESSION['failmessage'] = 'Invalid username or password';
            }
        } else {
            $_SESSION['failmessage'] = 'Invalid username or password';
        }
        header('Location: home.php');
    } catch (PDOException $e) {
        $_SESSION['failmessage'] = "An error occurred. Please try again later.";
        header('Location: home.php');
    }
} else {
    $_SESSION['failmessage'] = 'You have not entered a username or password.';
    header('Location: home.php');
}

