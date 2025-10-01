<?php
include 'header.php';
?>

<div class="container mt-5">
    <?php

    $find = ['[con]', '[ui]', '[bug]', '[sys]', '[func]', '[other]'];
    $repl = [
        "<span style='color:#FFF;font-weight:700;'>[CONTENT]</span>",
        "<span style='color:#FF0;font-weight:700;'>[UI]</span>",
        "<span style='color:#215E21;font-weight:700;'>[BUGFIX]</span>",
        "<span style='color:#99182C;font-weight:700;'>[SYSTEM]</span>",
        "<span style='color:#436EEE;font-weight:700;'>[FUNCTIONALITY]</span>",
        "<span style='color:#898;font-weight:700;'>[OTHER]</span>"
    ];
    $types = [
        'con' => "<span style='color:#FFF;font-weight:700;'>[CONTENT]</span>",
        'ui' => "<span style='color:#FF0;font-weight:700;'>[UI]</span>",
        'bug' => "<span style='color:#215E21;font-weight:700;'>[BUGFIX]</span>",
        'sys' => "<span style='color:#99182C;font-weight:700;'>[SYSTEM]</span>",
        'func' => "<span style='color:#436EEE;font-weight:700;'>[FUNCTIONALITY]</span>",
        'other' => "<span style='color:#898;font-weight:700;'>[OTHER]</span>"
    ];

    if ($user_class->game_updates > 0)
        perform_query("UPDATE grpgusers SET new_updates = 0 WHERE id = ?", [$user_class->id]);

    if ($user_class->admin) {
        ?>
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Game Updates</h3>
            </div>
            <div class="card-body" style="background-color: #8e8e8e21;">
                <?php

                if (isset($_POST['submit']) && ($user_class->admin)) {
                    $text = "[{$_POST['type']}] {$_POST['update']}";
                    perform_query("INSERT INTO game_updates (update_text) VALUES (?)", [$text]);
                    perform_query("UPDATE grpgusers SET new_updates = new_updates + 1 WHERE id <> ?", [$user_class->id]);
                    Message("Update posted");
                    if ($user_class->id == 9) {
                        set_last_active($user_class->id);
                    }
                }

                if ($user_class->admin) {
                    ?>
                    <form method="post">
                        <div class="mb-3">
                            <label for="update" class="form-label">Update</label>
                            <input type="text" name="update" class="form-control" id="update" autofocus="autofocus">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label><br>
                            <?php
                            foreach ($types as $type => $show) {
                                printf("<div class='form-check'><input class='form-check-input' type='radio' name='type' value='%s'%s>%s</div>", $type, $type == 'bug' ? " checked='checked'" : '', $show);
                            }
                            ?>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Add Update</button>
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>
    <?php } ?>
    <div class="mt-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0">Latest Updates</h4>
            </div>
            <div class="card-body" style="background-color: #8e8e8e21;">
                <div id="udiv">
                    <?php
                    $db->query("SELECT DATE_FORMAT(update_posted, '%d/%m/%Y') AS posted, update_text FROM game_updates ORDER BY update_posted DESC;");
                    $db->execute();
                    $rows = $db->fetch_row();

                    $grouped_rows = [];
                    foreach ($rows as $row) {
                        $date = $row['posted'];
                        if (!isset($grouped_rows[$date])) {
                            $grouped_rows[$date] = [];
                        }

                        // Group updates by date
                        $grouped_rows[$date][] = $row['update_text'];
                    }

                    foreach ($grouped_rows as $date => $updates) {
                        ?>
                        <div class="mb-3">
                            <h5><strong><?php echo $date; ?></strong></h5>
                            <ul class="list-group">
                                <?php
                                foreach ($updates as $update) {
                                    ?>
                                    <li class="list-group-item" style="background-color: #8e8e8e21;">
                                        <?php
                                        echo $user_class->game_updates > 0 ? "<span class='badge bg-warning text-dark me-2'>New!</span>" : '';
                                        echo str_replace($find, $repl, BBCodeParse(stripslashes($update)));
                                        ?>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                        <?php
                    }
                    $user_class->game_updates = 0;
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>