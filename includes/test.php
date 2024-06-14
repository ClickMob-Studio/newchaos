<?php
if (!defined($_CONFIG['define_code']))
{
    echo 'This file cannot be accessed directly.';
    exit;
}
if($ir['sbban'] < 1)
{
    if($_POST['message'] != "")
    {
        $_POST['message']=$db->real_escape_string(trim(strip_tags($_POST['message'])));
        $message=($_POST['message']);
//smileys
        $key = array(":)", ":(", ";)", ":D", ":innocent", ":hug", ":/", ":x", ":blush", ":P", ":*", ":broken", ":o", ":mad", ":sly", "B-)", ":-S", "=))", ":-$", ":thumbs", ":devil", ":angel", ":nerd", ":whatever", ":sleep", ":roll", ":loser", ":clown", ":hmmm", ":drool");
        $images  = array("<img src=smileys/1.gif>", "<img src=smileys/2.gif>", "<img src=smileys/3.gif>", "<img src=smileys/4.gif>", "<img src=smileys/5.gif>", "<img src=smileys/6.gif>", "<img src=smileys/7.gif>", "<img src=smileys/8.gif>", "<img src=smileys/9.gif>", "<img src=smileys/10.gif>", "<img src=smileys/11.gif>", "<img src=smileys/12.gif>", "<img src=smileys/13.gif>", "<img src=smileys/14.gif>", "<img src=smileys/15.gif>", "<img src=smileys/16.gif>", "<img src=smileys/17.gif>", "<img src=smileys/18.gif>", "<img src=smileys/19.gif>", "<img src=smileys/20.gif>", "<img src=smileys/21.gif>", "<img src=smileys/22.gif>", "<img src=smileys/23.gif>", "<img src=smileys/24.gif>", "<img src=smileys/25.gif>", "<img src=smileys/26.gif>", "<img src=smileys/27.gif>", "<img src=smileys/28.gif>", "<img src=smileys/29.gif>", "<img src=smileys/30.gif>");
        $message = str_replace($key, $images, $message);
        $db->query("INSERT INTO shoutbox VALUES('',{$ir['userid']},'$message',unix_timestamp())",$c) or die ($db->error());
        print "<form action='". $_SERVER['PHP_SELF'] ."' method='post' name='form' form='form1' onSubmit=\"return disableForm(this);\"><b>Message: </b><input type='text' name='message' maxlength='350' size='31'><br><input type='Submit' value='Post Message'><input type='reset' value='Reset Message'></form><br/><br/>";
    }
    else
    {
//Submit button JS Start
        print <<<EOF
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
function disableForm(theform) {
if (document.all || document.getElementById) {
for (i = 0; i < theform.length; i++) {
var tempobj = theform.elements[i];
if (tempobj.type.toLowerCase() == "submit" || tempobj.type.toLowerCase() == "reset")
tempobj.disabled = true;
}
return true;
}
else {
return false;
   }
}
//  End -->
</script>
EOF;
//Submit Button JS End
        print "<form action='". $_SERVER['PHP_SELF'] ."' method='post' name='form' form='form1' onSubmit=\"return disableForm(this);\"><b>Message: </b><input type='text' name='message' maxlength='255' size='31'><br><input type='Submit' value='Post Message'><input type='reset' value='Reset Message'></form><br/>";
    }
    echo <<<OUT
  <script language="JavaScript" type="text/javascript">
  function addImage(textToAdd)
  {
   document.form.message.value += textToAdd;document.form.message.focus();
  }
  </script>
OUT;

    OUT;

    $shoutbox=$db->query("SELECT s.*,u.* FROM shoutbox s LEFT JOIN users u ON u.userid=s.suserid ORDER BY s.stime DESC LIMIT 4",$c) or die ($db->error());
    print "<table width='95%' cellpadding=2 bgcolor=gray boarder='5'><tr><th class='table' colspan='0'><i>Chat Box</i></td>";
    if($ir['user_level'] > 1)
    {
        print "<td align='center' class='table'><i>Staff Actions</i></td>";
    }
    print "</tr>";
    while($sb=$db->fetch_row($shoutbox))
    {
        print "<tr bgcolor=silver><td valign='top'><font color=red><a href='viewuser.php?u={$sb['userid']}' title='View profile of {$sb['username']}'>". htmlspecialchars(stripslashes($sb['username'])) ."</font></a> [{$sb['userid']}]<br>".date('M j, Y g:i:s a',$sb['stime'])."</td><td valign='top'><font color=blue>". stripslashes($sb['smessage']) ."</font></td>";
        if($ir['user_level'] > 1)
        {
            print "<td align='center'><form action='index.php' method='post'>
<input type='hidden' name='del' value='{$sb['sid']}' />
<input type='submit' value='Delete Post' /></form> 
<form action='". $_SERVER['PHP_SELF'] ."' method='post'>
<input type='hidden' name='ban' value='{$sb['userid']}' />
<input type='submit' value='Ban User' /></form></td>";
            print "</tr>";
        }
    }
    if($ir['user_level'] > 1)
    {
        print "<tr><td colspan=3 align='center'><form action='". $_SERVER['PHP_SELF'] ."' method='post'>
<input type='hidden' name='flush' value='true' />
<input type='submit' value='Flush Chat Box' /></form>";
    }
    print "</td></tr></table>";
    if($_POST['flush']==true && $ir['user_level'] > 1)
    {

        $db->query("TRUNCATE TABLE shoutbox");
        print "<hr/><font color='red'>Shout Box Flushed</font><hr/>";
    }
    if($_POST['del'] && $ir['user_level'] > 1)
    {

        $db->query("DELETE FROM shoutbox WHERE sid={$_POST['del']}",$c);
        print "<hr/><font color='red'>Post Deleted</font><hr/>";
    }
    if($_POST['ban'] && $ir['user_level'] > 1)
    {
        $db->query("UPDATE users SET sbban=+3 WHERE userid={$_POST['ban']}");
        print "<hr/><font color='red'>User Banned for 3 Days</font><hr/>";
    }
}
else
{
    print "<font color='red'>You have been banned from the shoutbox for {$ir['sbban']} days.</font>";
}
?>