<?php
include 'header.php';

$types = [
    'con' => "CONTENT",
    'ui' => "UI",
    'bug' => "BUGFIX",
    'sys' => "SYSTEM",
    'func' => "FUNCTIONALITY",
    'other' => "OTHER"
];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit']) && $user_class->admin) {
    $text = "[{$_POST['type']}] {$_POST['update']}";
    $db->query("INSERT INTO game_updates (update_text) VALUES (:text)");
    $db->bind(':text', $text);
    $db->execute();

    $db->query("UPDATE grpgusers SET new_updates = new_updates + 1 WHERE id <> :userid");
    $db->bind(':userid', $user_class->id);
    $db->execute();

    Message("Update posted");
    if ($user_class->id == 9) {
        set_last_active($user_class->id);
    }
}

if ($user_class->game_updates) {
    $db->query("UPDATE grpgusers SET new_updates = 0 WHERE id = :userid");
    $db->bind(':userid', $user_class->id);
    $db->execute();
}
?>
<style>
    .type-con {
        color: #FFF;
    }

    /* White */
    .type-ui {
        color: #FFD700;
    }

    /* Gold */
    .type-bug {
        color: #228B22;
    }

    /* Forest Green */
    .type-sys {
        color: #FF4500;
    }

    /* OrangeRed */
    .type-func {
        color: #1E90FF;
    }

    /* DodgerBlue */
    .type-other {
        color: #6A5ACD;
    }

    /* SlateBlue */
    .dark-card {
        background-color: #000000;
        color: #fff;
    }
</style>
<div class="container mt-4">
    <h1 class="mb-3">Game Updates</h1>

    <?php if ($user_class->admin): ?>
        <div class="card dark-card mb-4">
            <div class="card-body">
                <form method="post">
                    <div class="mb-3">
                        <label for="update" class="form-label">Update</label>
                        <input type="text" class="form-control" id="update" name="update" autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type</label>
                        <?php foreach ($types as $type => $label): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="type<?= $type ?>"
                                    value="<?= $type ?>" <?= $type == 'bug' ? 'checked' : '' ?>>
                                <label class="form-check-label type-<?= $type ?>" for="type<?= $type ?>">
                                    <?= $label ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary">Add Update</button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <?php
    $db->query("SELECT DATE_FORMAT(update_posted, '%d/%m/%Y') AS posted FROM game_updates GROUP BY posted ORDER BY id DESC");
    $dates = $db->fetch_row();
    if ($dates): ?>
        <div class="updates-list">
            <?php foreach ($dates as $row): ?>
                <div class="card dark-card mb-3">
                    <div class="card-header">
                        <strong><?= $row['posted']; ?></strong>
                    </div>
                    <ul class="list-group list-group-flush dark-card">
                        <?php
                        $db->query("SELECT update_text FROM game_updates WHERE DATE_FORMAT(update_posted, '%d/%m/%Y') = :posted ORDER BY id DESC");
                        $db->bind(':posted', $row['posted']);
                        $updates = $db->fetch_row();
                        foreach ($updates as $update):
                            preg_match('/^\[([a-z]+)\]/', $update['update_text'], $matches);
                            $type_class = 'type-' . ($matches[1] ?? 'other'); // Default to 'other' if type not found
                            ?>
                            <li class="list-group-item <?= $type_class ?>">
                                <?= $user_class->game_updates > 0 ? "<span class='badge bg-warning text-dark'>New!</span> " : ''; ?>
                                <?= str_replace($find, $repl, BBCodeParse(stripslashes($update['update_text']))); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No updates at the moment.</div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>