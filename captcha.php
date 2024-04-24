<?php

include 'header.php';

$newToken = macroTokenCheck($user_class);

if (!isset($_GET['page'])) {
    diefun('Something went wrong.');
}
$page = $_GET['page'];
$validPages = array(
    'backalley',
    'jail'
);

if (!in_array($page, $validPages)) {
    diefun('Something went wrong.');
}

if (isset($_POST) && isset($_POST['code'])) {
    if ((int)$_POST['code'] == (int)$user_class->captcha) {
        mysql_query("UPDATE `grpgusers` SET `captcha_timestamp` = " . time() . " WHERE `id` = " . $user_class->id);

        if ($page === 'backalley') {
            header('Location: backalley_new.php');
        } else if ($page === 'jail') {
            header('Location: jail.php');
        }
    }
    echo $_POST['code'] . '==' . $user_class->captcha;

    diefun('You entered the wrong code.');
}

$code = rand(1000, 99999);
mysql_query('UPDATE `grpgusers` SET `captcha` = "' . $code . '" WHERE `id` = ' . $user_class->id);


?>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <center>
            <p>Please enter the code below.</p>
            <img src="captcha_image.php?code=<?php echo $code ?>" width="100%" /><br />
            <form method="POST" action="captcha.php?token=<?php echo $newToken ?>&page=<?php echo $page ?>">
                <input type="text" name="code" class="form-control" />
                <input type="submit" value="Submit" />
            </form>
        </center>
    </div>
    <div class="col-md-3"></div>
</div>

<?php
include 'footer.php';
?>


