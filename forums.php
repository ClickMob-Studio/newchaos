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

$canpostthreads = (int) $permissions['canpostthreads'] == 1;

$threads = [];
if ($canonlyviewownthreads) {
    $threads = getOwnThreads($fid, $user_class->id, $page);
} else {
    $threads = getThreads($fid, $page);
}
?>

<div class="max-w-7xl mx-auto flex gap-y-4 px-2 md:px-6 lg:px-8 items-center justify-between pt-2 pb-4">
    <div class="flex items-center gap-x-2">
        <h1 class="pl-4 text-white text-2xl"><?= $forum['name'] ?></h1>
        <span class="text-lg text-gray-300"> <em>-</em> </span>
        <h3 class="text-lg text-gray-400"><?= $forum['description'] ?></h3>
    </div>
    <?php
    if ($canpostthreads) {
        echo '<a href="/newthread.php?fid=' . $fid . '"class="bg-black/40 hover:bg-black/80 cursor-pointer border border-white/10 text-white py-1 px-4 rounded text-md">New thread</a>';
    }
    ?>
</div>

<div class="max-w-7xl mx-auto flex flex-col gap-y-4 px-2 md:px-6 lg:px-8">
    <div class="w-full border border-white/10 bg-black/40 border-4 rounded-lg">
        <?php
        if (empty($threads)) {
            echo "There are currently no threads here.";
        }
        ?>

        <?php if (!empty($threads)): ?>
            <table class="w-full text-left text-sm text-gray-400 border-collapse">
                <thead class="text-xs uppercase bg-black text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 w-3/10 rounded-tl-md">Author</th>

                        <th scope="col" class="px-6 py-3 w-5/10">Thread</th>

                        <th scope="col" class="px-6 py-3 w-2/10">Replies</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($threads as $thread): ?>
                        <tr>
                            <?php $author = new User($thread['uid']); ?>
                            <td class="px-6 py-4 flex items-center w-3/10">
                                <img class="size-6 md:size-8 rounded-full mr-6" src="<?php echo $author->avatar ?>" alt="" />
                                <?= $author->formattedname ?>
                            </td>

                            <td class="px-6 py-4 w-5/10">
                                <a href="/thread.php?tid=<?= $thread['id'] ?>"
                                    class="text-white hover:underline"><?= htmlspecialchars($thread['subject']) ?></a>
                            </td>

                            <td class="px-6 py-4 w-2/10"><?= $thread['replies'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php include "nc_footer.php"; ?>