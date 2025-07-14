<?php
include 'header.php';
include 'includes/pagination.class.php';



if ($user_class->fbitime > 0) {
    diefun("You can't communicate if you're in FBI Jail!");
}

if ($user_class->gang == 0)
    diefun("You aren't in a gang.");

$db->query("SELECT * FROM bans WHERE type = 'mail' AND id = ?");
$db->execute(array(
    $user_class->id
));
if ($row = $db->fetch_row(true))
    diefun('&nbsp;You have been mail banned for ' . prettynum($row['days']) . ' days.');
$db->query("INSERT INTO gmusers (userid, gang, lastseen) VALUES (?, ?, unix_timestamp()) ON DUPLICATE KEY UPDATE lastseen = unix_timestamp()");
$db->execute(array(
    $user_class->id,
    $user_class->gang
));
$db->query("SELECT MAX(gmailid) FROM gangmail WHERE gangid = ?");
$db->execute(array(
    $user_class->gang
));
$lastid = $db->fetch_single();

if (empty($lastid))
    $lastid = 0;
$chatters = gcTalking(1, $user_class->gang);
$pages = new pagination();
$pages->items_per_page = 30;
$pages->max_pages = 10;
$db->query("SELECT count(*) FROM gangmail WHERE gangid = ?");
$db->execute(array(
    $user_class->gang
));
$pages->items_total = $db->fetch_single();
print <<<OUT
<script>
function addBB(text) {
    var textarea = document.getElementById('reply');
    textarea.value += text;
}
var lastGmailID = $lastid;
syncGmail();
function sendGmail() {
    if($('#reply').val() != ''){
        var ts = new Date().getTime();
        $.post("ajax_gmail.php?ts="+ts, {'msg':$('#reply').val()}, function (d){
            var myArr = d.split('|-|-|');
			$('#chat_block').prepend('<div id="t'+ts+'" style="display:none">'+myArr[2]+'</div>');
			$('#chat_block div#t' + ts).slideDown(500);
			$('#reply').val('');
			$('#reply').focus();
			lastGmailID = myArr[1];
			if(myArr[0]){
				$('#gccontainer').html(myArr[0]);
			}
        });
    }
    return false;
}



function pinMessage(messageId, pin) {
        $.ajax({
            url: 'gangmail.php', // URL to your PHP script that handles pinning
            type: 'POST',
            data: {
                action: 'pin',
                message_id: messageId,
                pin: pin
            },
            success: function(response) {
                // Handle the response, such as refreshing the chat
            }
        });
    }



function syncGmail() {
    var ts = new Date().getTime();
    $.get("ajax_gmail.php?ts="+ts+"&lastID="+lastGmailID, function(d){
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
}
function typing(){
	if($('#reply').val() == "")
		$.get("ajax_gmtyping.php?is=0");
	else
		$.get("ajax_gmtyping.php?is=1");
}
</script>


OUT;
?>
<table style="margin-bottom:-10px;">
    <tr>
        <td class="flexcont">
            <span class="flexele forumhover" onclick="addBB('[b][/b]', 4);return false;">[b]</span>
        </td>
        <td class="flexcont">
            <span class="flexele forumhover" onclick="addBB('[u][/u]', 4);return false;">[u]</span>
        </td>
        <td class="flexcont">
            <span class="flexele forumhover" onclick="addBB('[i][/i]', 4);return false;">[i]</span>
        </td>
        <td class="flexcont">
            <span class="flexele forumhover" onclick="addBB('[s][/s]', 4);return false;">[s]</span>
        </td>
        <td class="flexcont">
            <span class="flexele forumhover" onclick="addBB('[url][/url]', 6);return false;">[url]</span>
        </td>
        <td class="flexcont">
            <span class="flexele forumhover" onclick="v('[img][/img]', 6);return false;">[img]</span>
        </td>
        <td class="flexcont">
            <span class="flexele forumhover" onclick="addBB('[tag][/tag]', 6);return false;">[tag]</span>
        </td>
        <td class="flexcont">
            <span class="flexele forumhover" onclick="addBB('[youtube][/youtube]', 10);return false;">[youtube]</span>
        </td>
        <td class="flexcont">
            <span id="semojis" class="forumhover" onclick="return showemojis();"
                style="display:<?php echo ($user_class->hideemojis) ? 'block' : 'none'; ?>;flex:2;">Show Emojis</span>
        </td>
        <td class="flexcont">
            <span id="hemojis" class="forumhover" onclick="return hideemojis();"
                style="display:<?php echo ($user_class->hideemojis) ? 'none' : 'block'; ?>;flex:2;">Hide Emojis</span>
        </td>
    </tr>
</table>
<?php
echo "

<style>
    .pinned-message {
        background-color: #fffa90; /* Light yellow background for pinned messages */
        border: 1px solid #ffcc00;
        margin-bottom: 10px;
        padding: 5px;
    }
    /* Rest of your styles */
</style>
<tr>
    <td class='contentcontent' style='text-align:center;'>
		<div id='gccontainer'>
			$chatters
		</div>", emotes(), "<form name='message'>
			<textarea name='msgtext' autofocus id='reply' style='width:90%;height:125px;'></textarea><br />
			<input type='submit' name='submit' onclick='sendGmail();return false;' value='Post New Gang Mail' />
		</form>
		<div id='chat_block'>";
$db->query("UPDATE grpgusers SET gangmail = 0 WHERE id = ?");
$db->execute(array(
    $user_class->id
));
$db->query("SELECT g.*, avatar FROM gangmail g JOIN grpgusers u ON g.playerid = u.id WHERE gangid = ? ORDER BY timesent DESC" . $pages->limit());
$db->execute(array(
    $user_class->gang
));
$rows = $db->fetch_row();
foreach ($rows as $row) {
    $avatar = ($row['avatar'] != "") ? $row['avatar'] : "/images/no-avatar.png";
    $quotetext = str_replace(array('\'', '"'), array('\\\'', '&quot;'), $row['body']);

    echo '<div class="floaty">';


    echo '</div>';
    echo '<hr style="border:0;border-top:thin solid #333;" />';
    echo '<table class="flexcont" style="width:100%;">';
    echo '<tr>';

    // Left cell for avatar and username
    echo '<td class="flexele" style="border-right:thin solid #333;text-align:center;width:150px;">';
    echo '<img src="' . $avatar . '" height="150" width="150" style="border:1px solid #666666;margin-bottom: 6px;" />';
    echo '<br />';
    if ($row['playerid'] > 0) {
        $u = new User($row['playerid']);
        echo $u->formattedname;
    } else {
        echo '<span style="color:red">System</span>';
    }
    echo '</td>';

    // Right cell for the body content
    echo '<td class="flexele" style="padding:10px;">';
    echo BBCodeParse(stripslashes($row['body']));
    echo '<br><br>';
    echo howlongago($row['timesent']) . ' ago <br><br>';
    echo '<br><div class="flexele forumhover" onClick="addsmiley(\'[quote=' . $row['playerid'] . ']' . str_replace(array("\n", "\r"), array('', '\n'), $quotetext) . '[/quote]\\n\\n\');">';
    echo 'Quote';
    echo '</div>';
    echo '</td>';

    echo '</tr>';
    echo '</table>';

}
echo $pages->displayPages();
include("footer.php");
?>