<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <link rel="stylesheet" href="css/newlogin2.css" media="screen" />
        <title>YobCity Login</title>
    </head>
    <body>
        <header>
            <div id="logo">
                <img src="../images/login/bg-logo.png"/>
            </div>
        </header>
        <div id="login_panel">
			<span id="errors" style="color:red;font-size:16px;font-weight:bold;"><?php echo ($_SESSION['failmessage']) ? $_SESSION['failmessage'] : "" ; ?></span>
            <form name="login" action="login.php" method="post" accept-charset="utf-8">
                <label for="username">Username</label><br />
                <input class="textbox" type="text" name="username" id="username" required> <br /><br />
                <label for="userpass">Password</label><br />
                <input class="textbox" type="password" name="password" id="password" required> <br /><br />
                <input id="button-login" type="submit" value=""/><br /><br />
                <a href="register.php">Join Yob City</a>&nbsp;&nbsp;<a href="http://facebook.com/yobcity">Can't log in?</a>
            </form>
        </div>
        <footer>
            <p>&copy; 2016 YobCity | Privacy Policy | Terms of Service</p>
        <footer>
    </body>
</html>
<?php
$_SESSION['failmessage'] = "";
?>