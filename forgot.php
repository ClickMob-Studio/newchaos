<?php
ob_start(); // Start output buffering

include 'dbcon.php';
include 'database/pdo_class.php';

require_once(__DIR__ . '/vendor/autoload.php');

session_start();

$desired_ip = '142.116.133.64';
$client_ip = $_SERVER['REMOTE_ADDR'];

if ($client_ip == $desired_ip) {
    header('Location: https://meatspin.com');
    exit();
}

function generateRandomToken($length = 50)
{
    if (function_exists('openssl_random_pseudo_bytes')) {
        return bin2hex(openssl_random_pseudo_bytes($length / 2));
    } else {
        return bin2hex(substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / 2))), 0, $length / 2));
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'reset') {
    $token = $_GET['token'];
    $userid = $_GET['userid'];
    if (empty($userid)) {
        $_SESSION['failmessage'] = "Invalid token.";
        header("Location: forgot.php");
        exit();
    }
    if (empty($token)) {
        $_SESSION['failmessage'] = "Invalid token.";
        header("Location: forgot.php");
        exit();
    }

    $db->query("SELECT id FROM grpgusers WHERE forgot_password = ? AND id = $userid LIMIT 1");
    $db->execute([$token]);

    if (!$db->num_rows()) {
        $_SESSION['failmessage'] = "Invalid token.";
        header("Location: forgot.php");
        exit();
    }

    $row = $db->fetch_row(true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $password = $_POST['password'];
        $password2 = $_POST['confirm_password'];

        if (empty($password) || empty($password2)) {
            $_SESSION['failmessage'] = "Please enter a password.";
            header("Location: forgot.php");
            exit();
        }

        if ($password != $password2) {
            $_SESSION['failmessage'] = "Passwords do not match.";
            header("Location: forgot.php");
            exit();
        }

        $pass = sha1($password);
        $db->query("UPDATE grpgusers SET `password` = ?, forgot_password = '' WHERE forgot_password = ? LIMIT 1");
        $db->execute([$pass, $token]);

        $_SESSION['successmessage'] = "Your password has been reset. Please login.";
        header("Location: forgot.php");
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];

    if (empty($username) || empty($email)) {
        $_SESSION['failmessage'] = "Username and email are required.";
        header("Location: forgot.php");
        exit();
    }

    $db->query("SELECT * FROM grpgusers WHERE username = ? AND email = ? LIMIT 1");
    $db->execute([$username, $email]);

    if (!$db->num_rows()) {
        $_SESSION['failmessage'] = "Username and email do not match.";
        header("Location: forgot.php");
        exit();
    }

    $row = $db->fetch_row(true);
    $token = generateRandomToken();

    $mailer = new \PHPMailer\PHPMailer\PHPMailer();
    $mailer->getSentMIMEMEssage();
    $mailer->Host = 'smtp-relay.brevo.com';
    $mailer->SMTPAuth = true;
    $mailer->Username = '89f561001@smtp-brevo.com';
    $mailer->Password = 'PVKxjvyR1CHG8TzM';
    $mailer->SMTPSecure = 'tls';
    $mailer->Port = 2525;

    $email = $row['email'];
    $userid = $row['id'];

    $mailer->setFrom('noreply@chaoscity.co.uk', 'Noreply');
    $mailer->addAddress($email);
    $mailer->Subject = 'Chaos City - Password Reset';
    $mailer->Body = "<h3>Dear $username, You have requested a new password reset at <a href='http://chaoscity.co.uk'>Chaos City</a>.<br><a href='https://chaoscity.co.uk/forgot.php?action=reset&token=$token&userid=$userid'>Click Here</a> to reset your password</h3>";
    
    $mailer->SMTPDebug = 3;
    $mailer->Debugoutput = "html";

    $db->query("UPDATE grpgusers SET forgot_password = ? WHERE email = ? AND username = ? LIMIT 1");
    $db->execute([$token, $row['email'], $username]);

    if (!$mailer->send()) {
        $_SESSION['failmessage'] = "Failed to send email. Please try again.";
        header("Location: forgot.php");
    } else {
        $_SESSION['failmessage'] = "Password reset instructions have been sent to " . $email;
        header("Location: forgot.php");
    }
    exit();

    // $apikey = '7dc2ad83e7f15563b1dee7d48109dbb7';
    // $apisecret = '15326068ed7ef53039e03ca05662bde2';
    // $mj = new \Mailjet\Client($apikey, $apisecret);
    // $email = $row['email'];
    // $userid = $row['id'];
    // $body = [
    //     'FromEmail' => "admin@chaoscity.co.uk",
    //     'FromName' => "Chaos City",
    //     'Subject' => "Forgot Password",
    //     'Text-part' => "You have requested a password reset at ChaosCity!",
    //     'Html-part' => "<h3>Dear $username, You have requested a new password reset at <a href='http://chaoscity.co.uk'>Chaos City</a>.<br><a href='https://chaoscity.co.uk/forgot.php?action=reset&token=$token&userid=$userid'>Click Here</a> to reset your password</h3>",
    //     'Recipients' => [
    //         [
    //             'Email' => $email
    //         ]
    //     ]
    // ];
    // $response = $mj->post(Resources::$Email, ['body' => $body]);

    $db->query("UPDATE grpgusers SET forgot_password = ? WHERE email = ? AND username = ? LIMIT 1");
    $db->execute([$token, $row['email'], $username]);

    $response = $mj->post(Resources::$Email, ['body' => $body]);
    if ($response->success()) {
        $_SESSION['failmessage'] = "Password reset instructions have been sent to your email.";
        header("Location: forgot.php");
    } else {
        $_SESSION['failmessage'] = "Failed to send email. Please try again.";
        header("Location: forgot.php");
    }
    exit();
}

ob_end_flush();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chaos City - Free text based Mafia Crime MMORPG</title>
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
    <meta name="description"
        content="Chaos City is a mafia text based role-playing game with endless opportunities. Besides committing crimes, you can run your own Front and earn lots of money with your business. Being a successful businessman assumes participating in courses, so you could acquire new skills. Do you have what it takes?">
    <meta name="keywords"
        content="mafia, rpg, online, crime, game, hustle, Chaos CIty, mmorpg, pocket mafia, text based, wars, text based rpg">
    <meta property="og:title" content="Chaos CIty - Free text based RPG | Pocket Mafia | Gangster Game">
    <meta property="og:site_name" content="Chaos CIty - Free text based Mafia RPG">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="asset/css/lstyle.css?v=1">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>
    <img class="dcMascot d-none d-lg-block" src="/asset/img/man1.png">
    <div class="row h-100 m-0">
        <div class="col-12 col-lg-4 offset-lg-2 loginPanel text-center">
            <img class="m-5" src="/asset/img/logo1.png" style="max-width:200px">
            <div>
                <div class="d-inline-block">
                    <p class="highlightWelcome text-start m-0">Forgot Password</p>
                    <h1 class="loginTitle"></h1>
                    <?php if (isset($_SESSION['failmessage'])): ?>
                        <div class="alert alert-danger"><?= $_SESSION['failmessage'] ?></div>
                        <?php unset($_SESSION['failmessage']); ?>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['successmessage'])): ?>
                        <div class="alert alert-success"><?= $_SESSION['successmessage'] ?></div>
                        <?php unset($_SESSION['successmessage']); ?>
                    <?php endif; ?>
                    <div id="error_area">
                        <?php if (isset($_SESSION['failmessage'])): ?>
                            <div class="warning-msg">
                                <i class="fa fa-warning"></i>
                                <?= $_SESSION['failmessage'] ?>
                            </div>
                            <?php unset($_SESSION['failmessage']); ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (isset($_GET['action']) && $_GET['action'] == 'reset' && isset($row)): ?>
                    <div class="row justify-content-center mt-5">
                        <div class="col-md-6">
                            <div class="card" style="background:#6c757d; min-width:300px;">
                                <div class="card-header text-center">Reset Password</div>
                                <div class="card-body">
                                    <form method="post">
                                        <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']) ?>">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" class="form-control" id="password" name="password"
                                                required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="confirm_password" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" id="confirm_password"
                                                name="confirm_password" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row justify-content-center mt-5">
                        <div class="col-md-6">
                            <div class="card" style="background:#6c757d; min-width:300px;">
                                <div class="card-header text-center">Forgot Password</div>
                                <div class="card-body">
                                    <form method="post" action="forgot.php">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" class="form-control" id="username" name="username" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email address</label>
                                            <input type="email" class="form-control" id="email" name="email" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="footerBuffer"></div>
            </div>
        </div>
    </div>
    <footer id="footer">
        <div class="container-inner" style="text-align:center">
            <div class="legal">&copy; 2024 Chaos City</div>
            <div class="links">
                <a href="grules.php" title="Game Guide">Game Rules</a> |
                <a href="policy.php" title="Privacy Policy">Privacy Policy</a>
            </div>
        </div>
    </footer>
</body>

</html>