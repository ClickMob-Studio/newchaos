<?php
include "header.php";

require_once 'includes/functions.php';

start_session_guarded();

$db = database::getInstance();

?>
<div class="container mt-4">
    <div class="card" style="background: transparent;">
        <div class="card-header">
            <h1>Gang Mass Mail</h1>
        </div>
        <div class="card-body">
            <?php
            if (!$user_class->gang) {
                die("You're not in a gang.");
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $subject = isset($_POST['subject']) ? $_POST['subject'] : 'GANG MASS MAIL';
                $message = isset($_POST['msgtext']) ? $_POST['msgtext'] : '';
                $sendto = [];

                for ($i = 1; $i <= 30; $i++) {
                    $index = "uid" . $i;
                    if (isset($_POST[$index])) {
                        $sendto[] = (int) $_POST[$index];
                    }
                }

                if (empty($sendto)) {
                    die("No recipients selected.");
                }

                $sendto_list = implode(",", $sendto);
                $db->query("SELECT id FROM grpgusers WHERE gang = ? AND id IN ($sendto_list)");
                $db->execute(array($user_class->gang));
                $recipients = $db->fetch_row();

                foreach ($recipients as $y) {
                    $db->query("INSERT INTO pms (`to`, `from`, `timesent`, `subject`, `msgtext`, `reported`, `viewed`, `parent`, `bomb`, `bombed`, `check`, `starred`, `outboxhidden`) 
                                  VALUES (?, ?, unix_timestamp(), ?, ?, 0, 1, 0, 0, 0, 0, 0, 0)");
                    $db->execute(array($y['id'], $user_class->id, $subject, $message));
                }

                echo "<div class='alert alert-success'>Messages sent out!</div>";
            } else {
                $db->query("SELECT id FROM grpgusers WHERE gang = ?");
                $db->execute(array($user_class->gang));
                $res = $db->fetch_row();
                echo "<form method='post' name='message'>";
                echo "<div class='table-responsive'><table class='table table-bordered table-striped text-center'>";
                $count = 1;
                foreach ($res as $f) {
                    if ($count == 1) {
                        echo "<tr>";
                    } elseif ($count % 5 == 1) {
                        echo "<tr>";
                    }

                    $u = formatName($f['id']);
                    $che = $f['id'] == $user_class->id ? "" : "checked";
                    echo "<td><input type='checkbox' class='form-check-input' name='uid{$count}' value='{$f['id']}' $che /> $u</td>";

                    if ($count % 5 == 0) {
                        echo "</tr>";
                    }
                    $count++;
                }
                if (($count - 1) % 5 != 0) {
                    echo "</tr>";
                }

                echo "<tr><td colspan='5' class='text-start'><label for='subject' class='form-label'>Subject:</label> <input type='text' name='subject' style='width:100%' value='GANG MASS MAIL' /></td></tr>";
                echo "<tr><td colspan='5' class='text-start'><label for='msgtext' class='form-label'>Message:</label> <textarea  rows='5' name='msgtext' style='width:100%' id='textbox'></textarea></td></tr>";
                echo "<tr><td colspan='5' class='text-center'><button type='submit' class='btn btn-primary'>Send Mass Mail</button></td></tr>";
                echo "<tr><td colspan='5'>";
                emotes();
                echo "</td></tr>";
                echo "</table></div></form><div class='clear'><br /></div>";
            }
            ?>
        </div>
    </div>
</div>
<?php
include "footer.php";
?>