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

<style>
    .ql-toolbar .ql-stroke {
        fill: none;
        stroke: #fff;
    }

    .ql-toolbar .ql-fill {
        fill: #fff;
        stroke: none;
    }

    .ql-toolbar .ql-picker {
        color: #fff;
    }

    .ql-toolbar.ql-snow {
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-sizing: border-box;
        font-family: 'Helvetica Neue', 'Helvetica', 'Arial', sans-serif;
        padding: 6px;
        border-radius: 8px 8px 0px 0px;
    }

    .ql-container.ql-snow {
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 0px 0px 8px 8px;
    }

    .ql-editor {
        background-color:
            color-mix(in oklab, var(--color-black) 40%, transparent);
        border-radius: 0px 0px 8px 8px;
        min-height: 200px;
    }
</style>

<form method="POST">
    <input type="hidden" name="fid" value="<?= $fid ?>">

    <div class="max-w-7xl mx-auto flex flex-col gap-y-4 px-2 md:px-6 lg:px-8">
        <div class="w-full border border-white/10 bg-black/40 border-4 rounded-lg">
            <h2 class="text-white text-xl px-4 py-2">Post new thread in <?= $forum['name'] ?></h2>

            <div class="px-4 py-2">
                <input type="text" id="subject" name="subject" placeholder="Subject"
                    class="text-white bg-black/40 border border-white/10 rounded-lg px-4 py-1 w-full" required>
            </div>

            <div class="px-4 py-2 rounded-lg text-white">
                <div id="editor"></div>
            </div>

            <div class="px-4 py-2">
                <button type="submit"
                    class="bg-black-500 hover:bg-black-600 text-white font-bold py-2 px-4 rounded">Post thread</button>
            </div>
        </div>
    </div>
</form>

<script>
    const quill = new Quill('#editor', {
        theme: 'snow'
    });
</script>

<?php include "nc_footer.php"; ?>