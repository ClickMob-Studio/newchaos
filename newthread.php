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

$permissions = getPermissions($fid, $user_class->usergroup);
if (empty($permissions)) {
    header('Location: /index.php');
    exit;
}

$canpostthreads = (int) $permissions['canpostthreads'] == 1;
if (!$canpostthreads) {
    header('Location: /forums.php?fid=' . $fid);
    exit;
}

?>



<?php include "nc_footer.php"; ?>