<?php
include 'header.php';

if (isset($_POST['submit'])) {
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
        'secret' => '6LfOi6YeAAAAANlwf-BPv1qw7xYUKSI-D8xqsOBH',
        'response' => $_POST["g-recaptcha-response"],
        'remoteip' => $_SERVER['REMOTE_ADDR']
    );
    $options = array(
        'http' => array (
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $verify = file_get_contents($url, false, $context);
    $captcha_success = json_decode($verify);
    if ($captcha_success->success == false) {
        $db->query("UPDATE captcha SET result = 2 WHERE id = ?");
        $db->execute(
            array(
                $_POST['id']
            )
        );
    } else if ($captcha_success->success == true) {
        $_SESSION['anticheat'] = 0;

        $db->query("UPDATE grpgusers SET actions = 0 WHERE id = ?");
        $db->execute(
            array(
                $user_class->id
            )
        );

        $db->query("UPDATE captcha SET result = 1, completed_at = ? WHERE id = ?");
        $db->execute(
            array(
                time(),
                $_POST['id']
            )
        );
        if ($user_class->id == 174) {
            print_r($_SESSION['return_page']);
            print_r($_SESSION['last_page']);
            // print_r($_SERVER);
            //header('Location: ' . $_SESSION['return_page']);
        } else {
            header('Location: index.php');
        }
		exit;
    }
}

if ($user_class->id == 174) {
    unset($_SESSION['return_page']);
    if(!isset($_SESSION['return_page'])) {
        $_SESSION['return_page'] = $_SESSION['last_page'];
    }
    print_r($_SESSION['return_page']);
    print_r($_SESSION['last_page']);
}

if ($_SESSION['anticheat'] != 1 && $user_class->id != 150) {
    header('location: index.php');
} else {

    $db->query("INSERT INTO captcha (user_id, actions, prompted_at) VALUES (?, ?, ?)");
    $db->execute(
        array(
            $user_class->id,
            $user_class->actions,
            time()
        )
    );
    $db->query("SELECT LAST_INSERT_ID() as last_id");
    $db->execute();
    $id = $db->fetch_row()[0]['last_id'];

    $db->query("SELECT count(*) as total FROM captcha WHERE user_id = ? AND result = 0");
    $db->execute(
        array(
            $user_class->id
        )
    );
    // $total = $db->fetch_row()[0]['total'];
    // if ($total > 8) {
    //     session_destroy();
    //     header('location: index.php');
    // }

    echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
    echo '<style>input[type="submit"] { padding: 10px 30px; }</style>';
    echo '<h2 class="text-center">Human Check</h2>
        <p class="text-14 text-center">We need to make sure you are human.  Please solve the challenge below.</p>
        <form action="?" class="text-center" method="POST">
        <input type="hidden" name="id" value="' . $id . '">
        <div class="g-recaptcha" data-sitekey="6LfOi6YeAAAAAGe7VFiVuruLIG-vx4Yzok3Dd5Fy"></div>
        <br/>
        <input type="submit" name="submit" value="Submit">
      </form>
    ';
}

include 'footer.php';
?>