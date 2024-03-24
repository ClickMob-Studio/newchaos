<?php
include 'header.php';
if(isset($_GET['gcban']) && $_GET['conf'] == $_SESSION['security']){
    if($user_class->admin || $user_class->gm || $user_class->cm){
        $db->query("SELECT id FROM grpgusers WHERE id = ? AND admin = 0 AND gm = 0");
        $db->execute(array(
            $_GET['gcban']
        ));
        $id = $db->fetch_single();
        if(empty($id) || $id <= 0)
            diefun("Invalid id number. Ban failed.");
        $db->query("INSERT INTO bans VALUES('', ?, ?, ?, ?)");
        $db->execute(array(
            $id,
            $user_class->id,
            'gc',
            60
        ));
        diefun(formatName($id). " has been banned for 60 minutes. If the user needs a more severe punishment, use a mail ban.");
    }
}
if(isset($_GET['delgc'])){
    if($user_class->admin || $user_class->gm || $user_class->cm){
        $db->query("DELETE FROM globalchat WHERE id = ?");
        $db->execute(array(
            $_GET['delgc']
        ));
    }
}
$db->query("SELECT days FROM bans WHERE id = ? AND type = 'gc'");
$db->execute(array(
    $user_class->id
));
if($mins = $db->fetch_single())
    diefun("You are banned from global chat for $mins minutes.");
$_SESSION['security'] = rand(1000000000,2000000000);
$db->query("SELECT id FROM globalchat ORDER BY id DESC");
$db->execute();
$lastid = $db->fetch_row(true);
$db->query("SELECT * FROM bans WHERE type = 'mail' AND id = $user_class->id");
$db->execute();
if ($db->num_rows()) {
    $r = $db->fetch_row(true);
    diefun("&nbsp;You have been mail banned for " . prettynum($r['days']) . " days.");
}
if ($user_class->level < 10)
    diefun("You must be level 10 to use this feature.");
$lastid = (!empty($lastid['id'])) ? $lastid['id'] : 0;
print <<<TEXT
<script>
var lastGmailID = $lastid;
syncGmail();
</script>
TEXT;
echo'
    <div id="gccontainer" style="margin:0;">
        ' . gcTalking() . '
   </div>';
echo'<div class="floaty" style="margin-bottom:-10px;">';
	echo'<div class="flexcont">';
		echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[b][/b]\', 4);return false;">';
			echo'[b]';
		echo'</div>';
		echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[u][/u]\', 4);return false;">';
			echo'[u]';
		echo'</div>';
		echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[i][/i]\', 4);return false;">';
			echo'[i]';
		echo'</div>';
		echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[s][/s]\', 4);return false;">';
			echo'[s]';
		echo'</div>';
		echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[url][/url]\', 6);return false;">';
			echo'[url]';
		echo'</div>';
		echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[img][/img]\', 6);return false;">';
			echo'[img]';
		echo'</div>';
		echo'<div class="flexele forumhover" onclick="insertAtCursor(\'[youtube][/youtube]\', 10);return false;">';
			echo'[youtube]';
		echo'</div>';
		echo'<div id="semojis" class="flexele forumhover" onclick="return showemojis();" style="display:' , ($user_class->hideemojis) ? 'block' : 'none' , ';flex:2;">';
			echo'Show Emojis';
		echo'</div>';
		echo'<div id="hemojis" class="flexele forumhover" onclick="return hideemojis();" style="display:' , ($user_class->hideemojis) ? 'none' : 'block' , ';flex:2;">';
			echo'Hide Emojis';
		echo'</div>';
	echo'</div>';
	echo'<hr style="border:0;border-top:thin solid #333;" />';
	echo'<form name="message">';
		echo'<textarea name="msgtext" id="reply" oninput="typing();" style="width:90%;height:125px;"></textarea><br />';
		echo'<input type="submit" name="submit" onclick="return sendGmail();" value="Post" />';
	echo'</form>';
	echo'<div id="emojis" style="display:' , ($user_class->hideemojis) ? 'none' : 'block' , ';">';
		emotes();
	echo'</div>';
echo'</div>';
echo'<style>';
echo'#chatdiv img{';
    echo'height: auto;';
    echo'max-width: 500px;';
echo'}';
echo'</style>';
echo'<div id="chat_block">';
$db->query("UPDATE grpgusers SET globalchat = 0 WHERE id = $user_class->id");
$db->execute();
$db->query("SELECT * from globalchat ORDER BY timesent DESC LIMIT 800");
$rows = $db->fetch_row();
foreach ($rows as $row) {
    if(!$m->get('tavcache.'.$row['playerid'])){
        $reply_class = new User($row['playerid']);
        $array = array(
            'name' => $reply_class->formattedname,
            'avatar' => $reply_class->avatar,
            'admin' => $reply_class->admin,
            'gm' => $reply_class->gm
        );
        $m->set('tavcache.'.$row['playerid'], $array, 60);
    } else
        $array = $m->get('tavcache.'.$row['playerid']);
    $avatar = ($array['avatar'] != "") ? $array['avatar'] : "/images/no-avatar.png";
    $quotetext=str_replace(array('\'','"'),array('\\\'','&quot;'),$row['body']);
    echo'<div class="floaty">';
		echo'<div class="flexcont" style="text-align:center;">';
			echo'<div class="flexele">';
				echo howlongago($row['timesent']) . ' ago';
			echo'</div>';
			echo'<div class="flexele">';
				echo (($user_class->admin || $user_class->gm || $user_class->cm) && (!$array['admin'] && !$array['gm'])) ? '<a href="?gcban=' . $row['playerid'] . '&conf=' . $_SESSION['security'] . '">Ban User</a>' : '';
			echo'</div>';
			echo'<div class="flexele">';
				echo ($user_class->admin || $user_class->gm || $user_class->cm) ? '<a href="?delgc=' . $row['id'] . '">Delete Post</a>' : '';
			echo'</div>';
			echo'<div class="flexele forumhover" onClick="addsmiley(\'[quote=' . $row['playerid'] . ']' . str_replace(array("\n","\r"),array('','\n'),$quotetext) . '[/quote]\\n\\n\');">';
				echo 'Quote';
			echo'</div>';
		echo'</div>';
		echo'<hr style="border:0;border-top:thin solid #333;" />';
		echo'<div class="flexcont">';
			echo'<div class="flexele" style="border-right:thin solid #333;text-align:center;">';
				echo'<img src="' . $avatar . '" height="150" width="150" style="border:1px solid #666666" />';
				echo'<br />';
				echo $array['name'];
			echo'</div>';
			echo'<div class="flexele" style="flex:3;padding:10px;">';
				echo BBCodeParse(stripslashes($row['body']));
			echo'</div>';
		echo'</div>';
	echo'</div>';
}
print"</div>";
include("footer.php");
?>