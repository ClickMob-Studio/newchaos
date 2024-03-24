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
print <<< OUT
<script>
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
echo"

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
		</div>" , emotes() , "<form name='message'>
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
foreach($rows as $row) {
    $avatar = ($row['avatar'] != "") ? $row['avatar'] : "/images/no-avatar.png";
    $quotetext = str_replace(array('\'','"'), array('\\\'','&quot;'), $row['body']);

    echo '<div class="floaty">';
        echo '<div class="flexcont" style="text-align:center;">';
            echo '<div class="flexele">';
                echo howlongago($row['timesent']) . ' ago';
            echo '</div>';

            // Pin Button
            if (($gang_class->leader == $user_class->id) || ($user_class->admin == 1)) {
                echo '<div class="flexele forumhover" onclick="pinMessage(' . $row['id'] . ', true)">Pin</div>';
            }

            // Quote Button
            echo '<div class="flexele forumhover" onclick="addsmiley(\'[quote=' . $row['playerid'] . ']' . str_replace(array("\n","\r"), array('','\n'), $quotetext) . '[/quote]\\n\\n\');">';
                echo 'Quote';
            echo '</div>';
        echo '</div>';

        echo '<hr style="border:0; border-top:thin solid #333;" />';
        echo '<div class="flexcont">';
            echo '<div class="flexele" style="border-right:thin solid #333; text-align:center;">';
                echo '<img src="' . $avatar . '" height="150" width="150" style="border:1px solid #666666" />';
                echo '<br />';
                echo formatName($row['playerid']);
            echo '</div>';
            echo '<div class="flexele" style="flex:3; padding:10px;">';
                echo BBCodeParse(stripslashes($row['body']));
            echo '</div>';
        echo '</div>';
    echo '</div>';
}
echo $pages->displayPages();
include("footer.php");
?>