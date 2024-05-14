<?php
include "header.php";
if (!isset($_SESSION)) session_start();
$db = database::getInstance();

?>
<div class='box_top'>Gang Mass Mail</div>
<div class='box_middle'>
    <div class='pad'>
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
            $sendto[] = (int)$_POST[$index];
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
    
    echo "Messages sent out!";
} else {
    $db->query("SELECT id FROM grpgusers WHERE gang = ?");
    $db->execute(array($user_class->gang));
    $res = $db->fetch_row();
    var_dump($res);
    echo "<form method='post' name='message'><table style='width:100%;text-align:center;'>";
    $count = 1;
    foreach ($res as $f) {
        if ($count == 1) {
            echo "<tr>";
        } elseif ($count % 5 == 1) {
            echo "<tr>";
        }
        
        $u = formatName($f['id']);
        $che = $f['id'] == $user_class->id ? "" : "checked";
        echo "<td><input type='checkbox' name='uid{$count}' value='{$f['id']}' $che /> $u</td>";

        if ($count % 5 == 0) {
            echo "</tr>";
        }
        $count++;
    }
    if (($count - 1) % 5 != 0) {
        echo "</tr>";
    }

    echo "<tr><td colspan='5'>Subject: <input type='text' name='subject' value='GANG MASS MAIL' /></td></tr>";
    echo "<tr><td colspan='5'>Message: <textarea rows='5' cols='80' name='msgtext' id='textbox'></textarea></td></tr>";
    echo "<tr><td colspan='5'><input type='submit' value='Send Mass Mail' /></td></tr>";
    echo "<tr><td colspan='5'>"; emotes(); echo "</td></tr>";
    echo "</table></form><div class='clear'><br /></div>";
}
echo "</div>";
include "footer.php";
?>
