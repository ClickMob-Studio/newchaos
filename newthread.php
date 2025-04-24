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

<form method="POST">
    <div class="max-w-7xl mx-auto flex flex-col gap-y-4 px-2 md:px-6 lg:px-8">
        <div class="w-full border border-white/10 bg-black/40 border-4 rounded-lg">
            <h2 class="text-white text-xl">Post new thread in <?= $forum['name'] ?></h2>

            <input type="text" id="subject" name="subject" placeholder="Subject" required>

            <div id="editor"></div>
        </div>
    </div>
</form>

<script>
    const quill = new Quill('#editor', {
        theme: 'snow'
    });
</script>

<?php include "nc_footer.php"; ?>