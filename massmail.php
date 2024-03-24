<?php
include 'header.php';
if ($user_class->admin == 0 && $user_class->id != 864) {
    echo "You are not authorized to be here...";
    include 'footer.php';
    die();
}
$result = mysql_query("SELECT * from `grpgusers` ORDER BY `id` DESC");
while ($row = mysql_fetch_array($result, mysql_ASSOC)) {
    if (isset($_POST['newmessage'])) {
        if (isset($_POST['admins'])) {
            $to = $row['id'];
            $from = $user_class->id;
            $timesent = time();
            $_POST['subject'] = str_replace('"', '', $_POST['subject']);
            $subject = strip_tags($_POST['subject']);
            $subject = addslashes($subject);
            $_POST['msgtext'] = str_replace('"', '', $_POST['msgtext']);
            $msgtext = $_POST['msgtext'];
            $msgtext = nl2br($msgtext);
            $msgtext = addslashes($msgtext);
            $checkuser = mysql_query("SELECT `username` FROM `grpgusers` WHERE `id`='" . $to . "' AND `admin`='1'");
            $username_exist = mysql_num_rows($checkuser);
            if ($username_exist > 0) {
                $result5 = mysql_query("INSERT INTO `pms` (`to`, `from`, `timesent`, `subject`, `msgtext`)" .
                        "VALUES ('$to', '$from', '$timesent', '$subject', '$msgtext')");
            } else {
                
            }
        }
        if (isset($_POST['gms'])) {
            $to = $row['id'];
            $from = $user_class->id;
            $timesent = time();
            $_POST['subject'] = str_replace('"', '', $_POST['subject']);
            $subject = strip_tags($_POST['subject']);
            $subject = addslashes($subject);
            $_POST['msgtext'] = str_replace('"', '', $_POST['msgtext']);
            $msgtext = $_POST['msgtext'];
            $msgtext = nl2br($msgtext);
            $msgtext = addslashes($msgtext);
            $checkuser = mysql_query("SELECT `username` FROM `grpgusers` WHERE `id`='" . $to . "' AND `gm`='1'");
            $username_exist = mysql_num_rows($checkuser);
            if ($username_exist > 0) {
                $result5 = mysql_query("INSERT INTO `pms` (`to`, `from`, `timesent`, `subject`, `msgtext`)" .
                        "VALUES ('$to', '$from', '$timesent', '$subject', '$msgtext')");
            } else {
                
            }
        }
        if (isset($_POST['fms'])) {
            $to = $row['id'];
            $from = $user_class->id;
            $timesent = time();
            $_POST['subject'] = str_replace('"', '', $_POST['subject']);
            $subject = strip_tags($_POST['subject']);
            $subject = addslashes($subject);
            $_POST['msgtext'] = str_replace('"', '', $_POST['msgtext']);
            $msgtext = $_POST['msgtext'];
            $msgtext = nl2br($msgtext);
            $msgtext = addslashes($msgtext);
            $checkuser = mysql_query("SELECT `username` FROM `grpgusers` WHERE `id`='" . $to . "' AND `fm`='1'");
            $username_exist = mysql_num_rows($checkuser);
            if ($username_exist > 0) {
                $result5 = mysql_query("INSERT INTO `pms` (`to`, `from`, `timesent`, `subject`, `msgtext`)" .
                        "VALUES ('$to', '$from', '$timesent', '$subject', '$msgtext')");
            } else {
                
            }
        }
        if (isset($_POST['mobsters'])) {
            $to = $row['id'];
            $from = $user_class->id;
            $timesent = time();
            $_POST['subject'] = str_replace('"', '', $_POST['subject']);
            $subject = strip_tags($_POST['subject']);
            $subject = addslashes($subject);
            $_POST['msgtext'] = str_replace('"', '', $_POST['msgtext']);
            $msgtext = $_POST['msgtext'];
            $msgtext = nl2br($msgtext);
            $msgtext = addslashes($msgtext);
            $checkuser = mysql_query("SELECT `username` FROM `grpgusers` WHERE `id`='" . $to . "' AND `admin`='0' AND `gm`='0' AND `fm`='0' AND `rmdays` = '0'");
            $username_exist = mysql_num_rows($checkuser);
            if ($username_exist > 0) {
                $result5 = mysql_query("INSERT INTO `pms` (`to`, `from`, `timesent`, `subject`, `msgtext`)" .
                        "VALUES ('$to', '$from', '$timesent', '$subject', '$msgtext')");
            } else {
                
            }
        }
        if (isset($_POST['rms'])) {
            $to = $row['id'];
            $from = $user_class->id;
            $timesent = time();
            $_POST['subject'] = str_replace('"', '', $_POST['subject']);
            $subject = strip_tags($_POST['subject']);
            $subject = addslashes($subject);
            $_POST['msgtext'] = str_replace('"', '', $_POST['msgtext']);
            $msgtext = $_POST['msgtext'];
            $msgtext = nl2br($msgtext);
            $msgtext = addslashes($msgtext);
            $checkuser = mysql_query("SELECT `username` FROM `grpgusers` WHERE `id`='" . $to . "' AND `admin`='0' AND `gm`='0' AND `fm`='0' AND `rmdays` != '0'");
            $username_exist = mysql_num_rows($checkuser);
            if ($username_exist > 0) {
                $result5 = mysql_query("INSERT INTO `pms` (`to`, `from`, `timesent`, `subject`, `msgtext`)" .
                        "VALUES ('$to', '$from', '$timesent', '$subject', '$msgtext')");
            } else {
                
            }
        }
    }
}
?>
<tr><td class="contentspacer"></td></tr><tr><td class="contenthead">Mass Mail</td></tr>
<tr><td class="contentcontent">Here you can send a mass mail to every player in the game.</td></tr>
<tr><td class="contentspacer"></td></tr><tr><td class="contenthead">New Message</td></tr>
<tr><td class="contentcontent">
        <table width='100%'>
            <form id='frm1' method='post'>
                <tr>
                    <td width='15%'><b>Subject:</b></td>
                    <td width='85%'><input type='text' name='subject' size='70' maxlength='75' value="MASS MAIL"></td>
                </tr>
                <tr>
                    <td width='15%'><b>Message:</b>[<a href="bbcode.php">BBCode</a>]</td>
                    <td width='85%' colspan='3'><textarea name='msgtext' cols='53' rows='7'></textarea></td>
                </tr>
                <table width="90%">
                    <tr>
                        <td width='16%'><b>To:</b></td>
                        <td><input type="checkbox" name="admins"/> Admins</td>
                        <td><input type="checkbox" name="gms"/> Game Mods</td>
                        <td><input type="checkbox" name="fms"/> Forum Mods</td>
                    </tr>
                    <tr>
                        <td width='16%'>&nbsp;</td>
                        <td><input type="checkbox" name="rms"/> Respected Mobsters</td>
                        <td><input type="checkbox" name="mobsters"/> Mobsters</td>
                        <td><input type="checkbox" name="allbox" onclick="checkAll();"/> <b>Check all</b></td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td width='100%' colspan='4' align='center'><input type='submit' name='newmessage' value='Send'></td>
                    </tr>
            </form>
        </table>
    </table>
</td></td>
<?php
include 'footer.php';
?>