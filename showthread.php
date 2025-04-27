<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $csrf_token = bin2hex(openssl_random_pseudo_bytes(32));
    $_SESSION['csrf'] = $csrf_token;
}

include 'nc_header.php';

if (!isset($_GET['fid'])) {
    header('Location: /index.php');
    exit;
}
$fid = intval($_GET['fid']);
$forums = getForums();

// Find Forum with this FID
$forum = null;
foreach ($forums as $f) {
    if ($f['id'] == $fid) {
        $forum = $f;
        break;
    }
}

if (!$forum) {
    header('Location: /index.php');
    exit;
}

$permissions = getPermissions($fid, $user_class->usergroup);
if (empty($permissions)) {
    header('Location: /index.php');
    exit;
}

$canview = (int) $permissions['canview'] == 1;
$canviewthreads = (int) $permissions['canviewthreads'] == 1;
$canonlyviewownthreads = (int) $permissions['canonlyviewownthreads'] == 1;
if (!$canview || (!$canviewthreads && !$canonlyviewownthreads)) {
    header('Location: /index.php');
    exit;
}

$canpostthreads = (int) $permissions['canpostthreads'] == 1;

$threads = [];
if ($canonlyviewownthreads) {
    $threads = getOwnThreads($fid, $user_class->id, $page);
} else {
    $threads = getThreads($fid, $page);
}
?>