<?php
include "ajax_header.php";
$user_class = new User($_SESSION['id']);
$db->query("SELECT days FROM bans WHERE id = ? AND type = 'gc'");
$db->execute(array(
    $user_class->id
));
if($mins = $db->fetch_single())
    die();
if (isset($user_class->permban) && $user_class->permban > 0)
    die();
$db->query("SELECT * FROM gcusers WHERE userid = ?");
$db->execute(array(
    $_SESSION['id']
));
$r = $db->fetch_row(true);
$typing = isset($r['typing']) ? $r['typing'] : 0;
if($_SESSION['id'] != 0){
    $db->query("REPLACE INTO gcusers (userid, typing, lastseen) VALUES (?, ?, unix_timestamp())");
    $db->execute(array(
        $_SESSION['id'],
        $typing
    ));
}
$db->query("DELETE FROM gcusers WHERE lastseen < unix_timestamp()");
$db->execute();
if (isset($_POST['msg'])) {
    $avatar = $user_class->avatar;
    $msg = nl2br($_POST['msg']);
    $msg = strip_tags($msg);
	$db->query("UPDATE grpgusers SET globalchat = 1 WHERE id <> ?");
	$db->execute(array(
		$user_class->id
	));
	$db->query("UPDATE grpgusers SET lastactive = unix_timestamp() WHERE id = ?");
	$db->execute(array(
		$user_class->id
	));
    $db->query("UPDATE gcusers SET typing = 0 WHERE userid = ?");
    $db->execute(array(
        $_SESSION['id']
    ));
    $db->query("INSERT INTO globalchat (playerid, timesent, body) VALUES (?, unix_timestamp(), ?)");
    $db->execute(array(
        $_SESSION['id'],
        $msg
    ));
    $newid = $db->insert_id();
    $quotetext=str_replace(array('\'','"'),array('\\\'','&quot;'),$msg);
    $banbutton = (($user_class->admin || $user_class->gm || $user_class->cm)) ? "<a href='?deltav=$newid'><button style='float:left;height:25px;'>Delete Post</button></a> " : "";
    print gcTalking() . "|-|-|" . $newid . "|-|-|";
	?>
	<table class="table">
   <tbody>
      <tr>
         <td class="text-center" style="width:150px;">
            <img src="<?php echo $avatar; ?>" class="img-fluid rounded-circle" style="width: 150px; height: 150px;">
            <br><?php echo $user_class->formattedname; ?>
         </td>
         <td style="padding:10px;">
            <?php echo $msg; ?><br><br>NOW <br><br>
         </td>
      </tr>
   </tbody>
</table>
<?php
} elseif (isset($_GET['lastID'])) {
    $db->query("UPDATE grpgusers SET globalchat = 0 WHERE id = ?");
    $db->execute(array(
        $_SESSION['id']
    ));
    $db->query("SELECT * from globalchat WHERE id > ? AND playerid <> ? ORDER BY timesent");
    $db->execute(array(
        $_GET['lastID'],
        $_SESSION['id']
    ));
    $rows = $db->fetch_row();
    $db->query("SELECT id FROM globalchat ORDER BY id DESC");
    $db->execute();
    $lastid = $db->fetch_row(true);
    if ($lastid['id'] == $_GET['lastID']) {
        print gcTalking();
        die();
    }
    print gcTalking() . "|-|-|" . $lastid['id'] . "|-|-|";
    foreach ($rows as $row) {
        $reply_class = new User($row['playerid']);
        $avatar = ($reply_class->avatar != "") ? $reply_class->avatar : "/images/no-avatar.png";
        $quotetext=str_replace(array('\'','"'),array('\\\'','&quot;'),$row['body']);
        echo '<div class="card my-3">';
        echo '<div class="card-body d-flex">';
        echo '<div class="flex-shrink-0 me-3 text-center">';
        echo '<img src="' . $avatar . '" class="img-fluid rounded-circle" style="max-width: 150px;" />';
        echo '<p class="mt-2 mb-0">' . $reply_class->formattedname . '</p>';
        echo '</div>';
        echo '<div>';
        echo '<p>' . BBCodeParse(stripslashes($row['body'])) . '</p>';
        echo '</div>';
        echo '</div>';
        echo '<div class="card-footer">';
        echo '<div class="d-flex justify-content-between">';
        echo '<span>Now!</span>';
        echo (($user_class->admin || $user_class->gm || $user_class->cm) && (!$reply_class->admin && !$reply_class->gm)) ? '<a href="?tavban=' . $row['playerid'] . '&conf=' . $_SESSION['security'] . '" class="btn btn-danger btn-sm">Ban User</a> ' : '';
        echo ($user_class->admin || $user_class->gm || $user_class->cm) ? '<a href="?delgc=' . $lastid['id'] . '" class="btn btn-danger btn-sm">Delete Post</a>' : '';
        echo '<button class="btn btn-secondary btn-sm" onclick="addsmiley(\'[quote=' . $row['playerid'] . ']' . str_replace(array("\n","\r"),array('','\n'),$quotetext) . '[/quote]\\n\\n\');">Quote</button>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}
?>
