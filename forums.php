<?php

session_start();

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

$page = 1;
if (isset($_GET['page'])) {
    $page = intval($_GET['page']);
    if ($page < 1) {
        $page = 1;
    }
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

$threads = [];
if ($canonlyviewownthreads) {
    $threads = getOwnThreads($fid, $user_class->id, $page);
} else {
    $threads = getThreads($fid, $page);
}

var_dump($threads);
?>

<div class="max-w-7xl mx-auto flex gap-y-4 px-2 md:px-6 lg:px-8 items-center gap-x-2 pt-2 pb-4">
    <h1 class="pl-4 text-white text-2xl"><?= $forum['name'] ?></h1>
    <span class="text-lg text-gray-300"> <em>-</em> </span>
    <h3 class="text-lg text-gray-400"><?= $forum['description'] ?></h3>
</div>

<div class="max-w-7xl mx-auto flex flex-col gap-y-4 px-2 md:px-6 lg:px-8">
    <div class="w-full border border-white/10 bg-black/40 border-4 rounded-lg">
        <?php
        if (empty($threads)) {
            echo "There are currently no threads here.";
        }
        ?>
    </div>
</div>