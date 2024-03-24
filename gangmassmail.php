<?php
include "header.php";
if (!$user_class->gang) {
    die("You're not in a gang.");
}
print "<div class='container'><h3>Gang Mass Mail</h3><div class='content'>
<form method='post' name='message'>";
$q     = mysql_query("SELECT id FROM grpgusers WHERE gang=$user_class->gang");
$count = 1;
while ($f = mysql_fetch_array($q)) {
    if ($count == 1) {
        echo "<table style='width:100%;text-align:center;'><tr>
";
    } elseif ($count % 5 == 1) {
        echo "<tr>
";
    }
    $u = formatName($f['id']);
    if ($f['id'] == $user_class->id) {
        $che = "";
    } else {
        $che = "checked";
    }
    echo "<td><input type=\"checkbox\" name=\"uid{$count}\" alt=\"Checkbox\" value=\"{$f['id']}\" $che /> $u</td>
";
    if ($count % 5 == 0) {
        echo "</tr>
";
    }
    $count++;
}
if (($count - 1) % 5 != 0) {
    print "</tr>";
}
?>
<tr><td colspan='5'>Subject: <input type='text' name='subject' value='GANG MASS MAIL' /></td></tr>
<tr><td colspan='5'>Message: <textarea autofocus rows=5 cols=80 name='msgtext' id='textbox'></textarea></td></tr>
<tr><td colspan='5'><input type='submit' value='Send Mass Mail' /></td></tr>
<tr><td colspan='5'><?php emotes(); ?></td></tr>
<?php
print "</table>
</form>
<div class=\"clear\"><br /></div>";
for ($i = 1; $i <= 30; $i++) {
    $index = "uid" . $i;
    if (isset($_POST[$index])) {
        if (isset($sendto)) {
            $sendto .= "," . $_POST[$index];
        } else {
            $sendto = $_POST[$index];
        }
    }
}
if (isset($_POST['msgtext'])) {
    if (!isset($sendto)) {
        die();
    }
    $message = $_POST['msgtext'];
    $subject = $_POST['subject'];
    $yyy     = mysql_query("SELECT id FROM grpgusers WHERE gang = $user_class->gang AND id IN ($sendto)");
    while ($y = mysql_fetch_array($yyy)) {
        mysql_query("INSERT INTO pms VALUES('',{$y['id']},$user_class->id,unix_timestamp(),'$subject','$message',0,1,0,0,0,0,0)");
    }
    print "Messages sent out!";
}
include "footer.php";
?>