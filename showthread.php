<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $csrf_token = bin2hex(openssl_random_pseudo_bytes(32));
    $_SESSION['csrf'] = $csrf_token;
}

include 'nc_header.php';

if (!isset($_GET['´tid'])) {
    echo "REACHED UNO";
    // header('Location: /index.php');
    // exit;
}
$tid = intval($_GET['tid']);
$thread = getThread($tid);
if (!$thread) {
    echo "REACHED DDS";
    // header('Location: /index.php');
    // exit;
}

$permissions = getPermissions($fid, $user_class->usergroup);
if (empty($permissions)) {
    echo "REACHED TRES";
    // header('Location: /index.php');
    // exit;
}

$canviewthreads = (int) $permissions['canviewthreads'] == 1;
$canonlyviewownthreads = (int) $permissions['canonlyviewownthreads'] == 1;
if ((!$canviewthreads && !$canonlyviewownthreads) || ($canonlyviewownthreads && $thread['uid'] != $user_class->id)) {
    echo "REACHED CUATRO";
    // header('Location: /index.php');
    // exit;
}

?>

<div class="max-w-7xl mx-auto flex gap-y-4 px-2 md:px-6 lg:px-8 items-center justify-between pt-2 pb-4">
    <div class="flex items-center gap-x-2">
        <h1 class="pl-4 text-white text-2xl"><?= $thread['subject'] ?></h1>
    </div>
</div>