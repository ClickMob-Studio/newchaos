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

if (array_key_exists('submit', $_POST) && ($user_class->admin)) {
    $text = "[{$_POST['type']}] {$_POST['update']}";
    mysql_query("INSERT INTO game_updates (update_text) VALUES ('$text')");
    mysql_query("UPDATE grpgusers SET new_updates = new_updates + 1 WHERE id <> $user_class->id");
    Message("Update posted");
    if ($user_class->id == 9) {
        $db->query("UPDATE grpgusers SET lastactive = unix_timestamp() WHERE id = 1");
        $db->execute();
    }
}
if ($user_class->game_updates) {
    mysql_query("UPDATE grpgusers SET new_updates = 0 WHERE id = $user_class->id");
}
?>

<div class="container mt-4">
    <h1 class="mb-3">Game Updates</h1>

    <?php if ($user_class->admin): ?>
        <div class="card mb-4">
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
                                <input class="form-check-input" type="radio" name="type" id="type<?= $type ?>" value="<?= $type ?>" <?= $type == 'bug' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="type<?= $type ?>">
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
    $result = mysql_query("SELECT DATE_FORMAT(update_posted, '%d/%m/%Y') AS posted FROM game_updates GROUP BY posted ORDER BY id DESC");
    if (mysql_num_rows($result) > 0): ?>
        <div class="updates-list">
            <?php while ($row = mysql_fetch_array($result)): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <strong><?= $row['posted']; ?></strong>
                    </div>
                    <ul class="list-group list-group-flush">
                        <?php
                        $result2 = mysql_query("SELECT update_text FROM game_updates WHERE DATE_FORMAT(update_posted, '%d/%m/%Y') = '{$row['posted']}' ORDER BY id DESC");
                        while ($row2 = mysql_fetch_array($result2)): ?>
                            <li class="list-group-item">
                                <?= $user_class->game_updates > 0 ? "<span class='badge bg-warning text-dark'>New!</span> " : ''; ?>
                                <?= str_replace($find, $repl, BBCodeParse(stripslashes($row2['update_text']))); ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info">No updates at the moment.</div>
    <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
