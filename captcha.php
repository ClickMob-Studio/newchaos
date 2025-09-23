<?php

include 'header.php';

$newToken = macroTokenCheck($user_class->id);

if (!isset($_GET['page'])) {
    diefun('Something went wrong.');
}
$page = $_GET['page'];
$validPages = array(
    'backalley',
    'jail',
    'search',
    'profiles',
    'super_attack',
    'search',
    'citizens',
);

if (!in_array($page, $validPages)) {
    diefun('Something went wrong.');
}

if (isset($_POST) && isset($_POST['code'])) {
    if ((int) $_POST['code'] == (int) $user_class->captcha) {
        perform_query("UPDATE `grpgusers` SET `captcha_timestamp` = ? WHERE `id` = ?", [time(), $user_class->id]);

        if ($page === 'backalley') {
            header('Location: backalley_new.php');
        } else if ($page === 'jail') {
            header('Location: jail.php');
        } else if ($page === 'search') {
            header('Location: search.php');
        } else if ($page === 'citizens') {
            header('Location: citizens.php');
        } else if ($page === 'super_attack') {
            header('Location: super_attack.php');
        } else if ($page === 'profiles') {
            if (isset($_GET['pid'])) {
                header('Location: profiles.php?id=' . $_GET['pid']);
            } else if (isset($_POST['pid'])) {
                header('Location: profiles.php?id=' . $_POST['pid']);
            } else {
                header('Location: index.php');
            }
        }
    }
    echo $_POST['code'] . '==' . $user_class->captcha;

    diefun('You entered the wrong code.');
}

$code = rand(1000, 99999);
perform_query("UPDATE `grpgusers` SET `captcha` = ? WHERE `id` = ?", [$code, $user_class->id]);

?>

<div class="row">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <center>
            <p>Please enter the code below.</p>
            <img src="captcha_image.php?code=<?php echo $code ?>" width="100%" /><br />
            <form method="POST" action="captcha.php?token=<?php echo $newToken ?>&page=<?php echo $page ?>">
                <input type="text" name="code" class="form-control" />
                <?php if (isset($_GET['pid'])): ?>
                    <input type="hidden" name="pid" value="<?php echo (int) $_GET['pid'] ?>" />
                <?php endif; ?>
                <input type="submit" value="Submit" />
            </form>
        </center>
    </div>
    <div class="col-md-3"></div>
</div>

<?php
include 'footer.php';
?>