<?php
include 'header.php';
echo '
<tr><td class="contentcontent" id="chat_block" style="width: 660px;">';
$db->query("SELECT * from globalchat WHERE playerid IN (864, 146) AND timesent > unix_timestamp() - (86400*33) ORDER BY timesent ASC");
$rows = $db->fetch_row();
foreach ($rows as $row) {
    $reply_class = new User($row['playerid']);
    $array = array(
        'name' => $reply_class->formattedname,
        'avatar' => $reply_class->avatar,
        'admin' => $reply_class->admin,
        'gm' => $reply_class->gm
    );

    $avatar = ($array['avatar'] != "") ? $array['avatar'] : "/images/no-avatar.png";
    $quotetext = str_replace(array('\'', '"'), array('\\\'', '&quot;'), $row['body']);
    $banbutton = (($user_class->admin || $user_class->gm || $user_class->cm) && (!$array['admin'] && !$array['gm'])) ? "<A href='?gcban={$row['playerid']}&conf={$_SESSION['security']}'><button style='float:left;height:25px;'>Ban User</button></a> " : "";
    $banbutton .= (($user_class->admin || $user_class->gm || $user_class->cm)) ? "<a href='?delgc={$row['id']}'><button style='float:left;height:25px;'>Delete Post</button></a> " : "";
    print "
<style>
#chatdiv img{
    height: auto;
    max-width: 650px;
}
</style>
<table width='100%' style='word-wrap:break-word;'>
    <tr>
        <td rowspan='2' style='background:rgba(0,0,0,.25);border:thin solid #000;width:20%;' align='center'>" . howlongago($row['timesent']) . " ago<br /><br />{$array['name']}</td>
        <td style='background:rgba(0,0,0,.25);border:thin solid #000;height:25px;width:80%;' align='center'>$banbutton<button onClick=\"addsmiley('[quote={$row['playerid']}]" . str_replace(array("\n", "\r"), array('', '\n'), $quotetext) . "[/quote]\\n\\n');\" style='float:right;height:25px;'>Quote</button></td>
    </tr>
    <tr>
        <td style='background:rgba(0,0,0,.25);padding:5px;border:thin solid #000;width:80%;' valign='top' id='chatdiv'>" . BBCodeParse(stripslashes($row['body'])) . "</td>
    </tr>
</table>";
}
print "</td></tr>";
include("footer.php");
?>