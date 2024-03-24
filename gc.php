<?php
include 'header.php';
if(isset($_GET['gcban']) && $_GET['conf'] == $_SESSION['security']){
    if($user_class->admin || $user_class->gm){
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
            'tav',
            60
        ));
        $mtg->success(formatName($id). " has been banned for 60 minutes. If the user needs a more severe punishment, use a mail ban.");
    }
}
if(isset($_GET['delgc'])){
    if($user_class->admin || $user_class->gm){
        $db->query("DELETE FROM globalchat WHERE id = ?");
        $db->execute(array(
            $_GET['delgc']
        ));
    }
}
$db->query("SELECT days FROM bans WHERE id = ? AND type = 'tav'");
$db->execute(array(
    $user_class->id
));
if($mins = $db->fetch_single())
    $mtg->error("You are banned from global chat for $mins minutes.");
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
function sendGmail() {
    if($('#reply').val() != ''){
        var ts = new Date().getTime();
        $.post("ajax_gc.php?ts="+ts, {'msg':$('#reply').val()}, function (d){ 
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
function syncGmail() {
    var ts = new Date().getTime();
    $.get("ajax_gc.php?ts="+ts+"&lastID="+lastGmailID, function(d){
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
        $.get("ajax_globaltyping.php?is=0");
    else
        $.get("ajax_globaltyping.php?is=1");
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
    <textarea name="msgtext" id="reply" cols="90" oninput="typing();" rows="5"></textarea><br />
    <input type="submit" name="submit" onclick="return sendGmail();" value="Post" />
</form>
</td>
</tr>
<tr><td class="contentcontent" id="chat_block" style="width: 660px;">';
$db->query("UPDATE grpgusers SET globalchat = 0 WHERE id = $user_class->id");
$db->execute();
$db->query("SELECT * from globalchat ORDER BY timesent DESC LIMIT 20");
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
    } else {
        $array = $m->get('tavcache.'.$row['playerid']);
    }
    $avatar = ($array['avatar'] != "") ? $array['avatar'] : "/images/no-avatar.png";
    $quotetext=str_replace(array('\'','"'),array('\\\'','&quot;'),$row['body']);
    $banbutton = (($user_class->admin || $user_class->gm) && (!$array['admin'] && !$array['gm'])) ? "<A href='?gcban={$row['playerid']}&conf={$_SESSION['security']}'><button style='float:left;height:25px;'>Ban User</button></a> " : "";
    $banbutton .= (($user_class->admin || $user_class->gm)) ? "<a href='?delgc={$row['id']}'><button style='float:left;height:25px;'>Delete Post</button></a> " : "";
    print"
<style>
#chatdiv img{
    height: auto;
    max-width: 650px;
}
</style>
<table width='100%' style='word-wrap:break-word;'>
    <tr>
        <td rowspan='2' style='background:rgba(0,0,0,.25);border:thin solid #000;width:20%;' align='center'>" . howlongago($row['timesent']) . " ago<br /><br /><img src='$avatar' height='150' width='150' style='border:1px solid #666666' /><br />{$array['name']}</td>
        <td style='background:rgba(0,0,0,.25);border:thin solid #000;height:25px;width:80%;' align='center'>$banbutton<button onClick=\"addsmiley('[quote={$row['playerid']}]".str_replace(array("\n","\r"),array('','\n'),$quotetext)."[/quote]\\n\\n');\" style='float:right;height:25px;'>Quote</button></td>
    </tr>
    <tr>
        <td style='background:rgba(0,0,0,.25);padding:5px;border:thin solid #000;width:80%;' valign='top' id='chatdiv'>" . BBCodeParse(stripslashes($row['body'])) . "</td>
    </tr>
</table>";
}
print"</td></tr>";
include("footer.php");
?>