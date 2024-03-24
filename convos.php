<?php
include 'header.php';
if(empty($_GET['id'])){
	
} else {
	$id = security($_GET['id']);
	$db->query("SELECT userid FROM convo_users WHERE userid = ? AND cid = ? AND end = 0");
	$db->execute(array(
		$user_class->id,
		$id
	));
	if(empty($db->fetch_row()))
		diefun("You are not part of this group.");
	$db->query("SELECT * FROM convo_groups WHERE id = ?");
	$db->execute(array(
		$id
	));
	if(empty($db->fetch_row()))
		diefun("This conversation does not exist.");
	$db->query("SELECT MAX(id) FROM convos WHERE id = ?");
	$db->execute(array(
		$id
	));
	$lastid = $db->fetch_single();
	print <<<TEXT
<script>
var lastGmailID = $lastid;
syncGmail();
function sendGmail() {
    if($('#reply').val() != ''){
        var ts = new Date().getTime();
        $.post("ajax_convos.php?ts="+ts, {'msg':$('#reply').val()}, function (d){
			if(d){
				var myArr = d.split('|-|-|');
				$('#chat_block').prepend('<div id="t'+ts+'" style="display:none">'+myArr[2]+'</div>');
				$('#chat_block div#t' + ts).slideDown(500);
				$('#reply').val('');
				$('#reply').focus();
				lastGmailID = myArr[1];
				if(myArr[0]){
					$('#gccontainer').html(myArr[0]);
				}
			}
        });
    }
    return false;
}
function syncGmail() {
    var ts = new Date().getTime();
    $.get("ajax_convos.php?ts="+ts+"&lastID="+lastGmailID, function(d){
        var myArr = d.split('|-|-|');
        if(myArr[2]){
            $('#chat_block').prepend('<div id="t'+ts+'" style="display:none">'+myArr[2]+'</div>');
            $('#chat_block div#t' + ts).slideDown(500);
            lastGmailID = myArr[1];
        }
        if(myArr[0]){
            $('#gccontainer').html(myArr[0]);
        }
        setTimeout("syncGmail()",3000);
    });
    if($('#reply').val() == "")
        $.get("ajax_convos.php?is=0");
}
function typing(){
    if($('#reply').val() == "")
        $.get("ajax_convos.php?is=0");
    else
        $.get("ajax_convos.php?is=1");
}
</script>
TEXT;
echo'
<tr><td class="contentcontent" style="text-align:center;">
    <div id="gccontainer">
        ' . gcTalking() . '
   </div>
   <br />';
emotes();
echo'
    <br />
<form name="message">
    <textarea name="msgtext" id="reply" oninput="typing();" style="width:90%;height:125px;"></textarea><br />
    <input type="submit" name="submit" onclick="return sendGmail();" value="Post" />
</form>
</td>
</tr>
<tr><td class="contentcontent" id="chat_block" style="width: 660px;">';
$db->query("UPDATE grpgusers SET globalchat = 0 WHERE id = $user_class->id");
$db->execute();
$db->query("SELECT * FROM convos WHERE id = ? ORDER BY timesent DESC LIMIT 40");
$db->execute(array(
	$id
));
$rows = $db->fetch_row();
	foreach($rows as $row){
		if(!$m->get('tavcache.'.$row['playerid'])){
			$reply_class = new User($row['playerid']);
			$array = array(
				'name' => $reply_class->formattedname,
				'avatar' => $reply_class->avatar,
				'admin' => $reply_class->admin,
				'gm' => $reply_class->gm
			);
			$m->set('tavcache.'.$row['playerid'], $array, 60);
		} else {
			$array = $m->get('tavcache.'.$row['playerid']);
		}
		$avatar = ($array['avatar'] != "") ? $array['avatar'] : "/images/no-avatar.png";
		$quotetext=str_replace(array('\'','"'),array('\\\'','&quot;'),$row['body']);
		print"
			<table width='100%' style='word-wrap:break-word;'>
				<tr>
					<td rowspan='2' style='background:rgba(0,0,0,.25);border:thin solid #000;width:20%;' align='center'>
						" . howlongago($row['timesent']) . " ago<br />
						<br />
						<img src='$avatar' height='150' width='150' style='border:1px solid #666666' /><br />
						{$array['name']}
					</td>
					<td style='background:rgba(0,0,0,.25);border:thin solid #000;height:25px;width:80%;' align='center'>
						<button onClick=\"addsmiley('[quote={$row['playerid']}]".str_replace(array("\n","\r"),array('','\n'),$quotetext)."[/quote]\\n\\n');\" style='float:right;height:25px;'>Quote</button>
					</td>
				</tr>
				<tr>
					<td style='background:rgba(0,0,0,.25);padding:5px;border:thin solid #000;width:80%;' valign='top' id='chatdiv'>
						" . BBCodeParse(stripslashes($row['body'])) . "
					</td>
				</tr>
			</table>";
	}
}
print"</td></tr>";
include("footer.php");
?>