<?php
$conn = mysql_connect("localhost", "silentma_silent", "sonum14");
$db = mysql_select_db("silentma_silentma");
function Get_Username2($id) {
    $result = mysql_query("SELECT * FROM `grpgusers` WHERE `id` = '" . $id . "'");
    $worked = mysql_fetch_array($result);
    $bbuser = New User($worked['id']);
    $secondsago = time() - $bbuser->lastactive;
    if ($secondsago <= 900) {
        return "<table width='55%' background='http://i48.tinypic.com/15zp0tc.gif'><tr><td width='40%'>" . $bbuser->formattedname . "</td><td width='15%'>" . $bbuser->level . "</td><td width='10%'>" . $bbuser->cityname . "</td></tr><tr><td><center>&nbsp; <a href=spy.php?id=" . $worked['id'] . ">Spy</a></center></td><td> <a href=mug.php?id=" . $worked['id'] . ">Mug</a></td><td width='33%'>&nbsp;&nbsp;&nbsp; <a href=attack.php?id=" . $worked['id'] . ">Attack</a></td></tr></table>";
    }
    if ($secondsago >= 900) {
        return "<table width='55%' background='http://i48.tinypic.com/2i12rmx.gif'><tr><td width='40%'>" . $bbuser->formattedname . "</td><td width='15%'>" . $bbuser->level . "</td><td width='10%'>" . $bbuser->cityname . "</td></tr><tr><td><center>&nbsp; " . $worked['id'] . "<a href=spy.php?id=" . $worked['id'] . ">Spy</a></center></td><td> <a href=mug.php?id=" . $worked['id'] . ">Mug</a></td><td width='33%'>&nbsp;&nbsp;&nbsp; <a href=attack.php?id=" . $worked['id'] . ">Attack</a></td></tr></table>";
    }
}
function callback1($buffer1) {
    $out1 = $buffer1;
    $ea_tr = array(":brainwash:", ":xd:", ":bighug:", ":mini:", ":scar:", ":scared:", ":moon:", ":tongue:", ":mad:", ":happy:", ":sad:", ":erm:", ":wink:", ":angry:", ":banham:", ":haha:", ":duh:", ":heh:", ":mwah:", ":rasberry:", ":grin:", ":gen:", "mafia-warrior", "mafiawarrior", "mafia-warfare", "Silent-mafia", "mafiamofo", "mafiaturf", "thugbattle", "thug battle", "Mafia Turf", "mafia outlaw", "Mafia outlaw", "crimelord", "extremewars", "mafia warriors", "mafiahitter", "Mafiaturf.net", "mafia hitter", "mafia block", "Mafiahitter", "mafiablock", "thug-battle", "mafia turf", "mafia-warriors", "silent-mafia", "mafia mofo", "mafia-outlawz", "mafiawarfare", "mafiagrounds", "Mafia-warfare", "crime-lord", "Crimelord", "Mafia-hitter", "Mafiahitter", "MafiaMofo", "MafiaHitter");
    $ea_sr = array
        (
        '[img]smilies/brainwash.gif[/img]',
        '[img]http://www.animaatjes.nl/smileys/smileys-en-emoticons/xd/animaatjes-xd-54921.png[/img]',
        '[img]smilies/bighug.gif[/img]',
        '[img]smilies/mini.gif[/img]',
        '[img]smilies/scar.gif[/img]',
        '[img]smilies/scared.gif[/img]',
        '[img]smilies/moon.gif[/img]',
        '[img]smilies/tongue.gif[/img]',
        '[img]smilies/mad.gif[/img]',
        '[img]smilies/heh.gif[/img]',
        '[img]smilies/sad.gif[/img]',
        '[img]smilies/erm.gif[/img]',
        '[img]smilies/wink.gif[/img]',
        '[img]smilies/angry.gif[/img]',
        '[img]smilies/banham.gif[/img]',
        '[img]smilies/haha.gif[/img]',
        '[img]smilies/duh.gif[/img]',
        '[img]smilies/heh.gif[/img]',
        '[img]smilies/mwah.gif[/img]',
        '[img]smilies/rasberry.gif[/img]',
        '[img]smilies/smile.gif[/img]',
        '[img]smilies/wave.gif[/img]',
        '[img]http://i717.photobucket.com/albums/ww173/prestonjjrtr/Smileys/01230009.gif[/img]',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        'Game-Advertising-is:banham: You stupid Fucker',
        '0',
        '0',
        '0',
        '0',
    );
    $adverts = "([Dd][^A-Za-z]*[Oo0][^A-Za-z]*[Pp][^A-Za-z]*[Ee3][^A-Za-z]*[Rr][^A-Za-z]*[Uu][^A-Za-z]*[Nn][^A-Za-z]*[Nn][^A-Za-z]*[Ee3][^A-Za-z]*[Rr][^A-Za-z]*[Ss$][^A-Za-z]*)|([Uu][^A-Za-z]*[Nn][^A-Za-z]*[Tt][^A-Za-z]*[Oo0][^A-Za-z]*[Ll!][^A-Za-z]*[Dd][^A-Za-z]*[Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*)|([Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Nn][^A-Za-z]*[Dd][^A-Za-z]*[Ee3][^A-Za-z]*[Mm][^A-Za-z]*[Zz2][^A-Za-z]*)|([Aa@4][^A-Za-z]*[Rr][^A-Za-z]*[Mm][^A-Za-z]*[Yy][^A-Za-z]*[Ww][^A-Za-z]*[Aa@4][^A-Za-z]*[Rr][^A-Za-z]*[Ss$][^A-Za-z]*)|([Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Oo0][^A-Za-z]*[Uu][^A-Za-z]*[Tt][^A-Za-z]*[Ll!][^A-Za-z]*[Aa@4][^A-Za-z]*[Ww][^A-Za-z]*[Zz2][^A-Za-z]*)|([Tt][^A-Za-z]*[Hh][^A-Za-z]*[Uu][^A-Za-z]*[Gg][^A-Za-z]*[Pp][^A-Za-z]*[Aa@4][^A-Za-z]*[Rr][^A-Za-z]*[Aa@4][^A-Za-z]*)|([Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Tt][^A-Za-z]*[Hh][^A-Za-z]*[Uu][^A-Za-z]*[Gg][^A-Za-z]*)|([Gg][^A-Za-z]*[Aa@4][^A-Za-z]*[Nn][^A-Za-z]*[Gg][^A-Za-z]*[Ss$][^A-Za-z]*[Tt][^A-Za-z]*[Ee3][^A-Za-z]*[Rr][^A-Za-z]*[Ll!][^A-Za-z]*[Aa@4][^A-Za-z]*[Nn][^A-Za-z]*[Dd][^A-Za-z]*)|([^A-Za-z]*[Rr][^A-Za-z]*[Ii!][^A-Za-z]*[Vv][^A-Za-z]*[Aa@4][^A-Za-z]*[Ll!][^A-Za-z]*[Ss$][^A-Za-z]*|[Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Rr][^A-Za-z]*[Ii!][^A-Za-z]*[Vv][^A-Za-z]*[Aa@4][^A-Za-z]*[Ll!][^A-Za-z]*[Ss$][^A-Za-z]*|[Gg][^A-Za-z]*[Aa@4][^A-Za-z]*[Nn][^A-Za-z]*[Gg][^A-Za-z]*[Ss$][^A-Za-z]*[Tt][^A-Za-z]*[Aa@4][^A-Za-z]*[Cc][^A-Za-z]*[Ii!][^A-Za-z]*[Tt][^A-Za-z]*[Yy][^A-Za-z]*[Oo0][^A-Za-z]*[Nn][^A-Za-z]*[Ll!][^A-Za-z]*[Ii!][^A-Za-z]*[Nn][^A-Za-z]*[Ee3][^A-Za-z]*|[Dd][^A-Za-z]*[Oo0][^A-Za-z]*[Pp][^A-Za-z]*[Ee3][^A-Za-z]*[Rr][^A-Za-z]*[Uu][^A-Za-z]*[Nn][^A-Za-z]*[Nn][^A-Za-z]*[Ee3][^A-Za-z]*[Rr][^A-Za-z]*[Ss$][^A-Za-z]*|[Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Rr][^A-Za-z]*[Ee3][^A-Za-z]*[Vv][^A-Za-z]*[Oo0][^A-Za-z]*[Ll!][^A-Za-z]*[Tt][^A-Za-z]*|[Cc][^A-Za-z]*[Rr][^A-Za-z]*[Ii!][^A-Za-z]*[Mm][^A-Za-z]*[Ee3][^A-Za-z]*[Kk][^A-Za-z]*[Ii!][^A-Za-z]*[Nn][^A-Za-z]*[Gg][^A-Za-z]*[Ss$][^A-Za-z]*|[Pp][^A-Za-z]*[Rr][^A-Za-z]*[Ii!][^A-Za-z]*[Ss$][^A-Za-z]*[Oo0][^A-Za-z]*[Nn][^A-Za-z]*[Ss$][^A-Za-z]*[Tt][^A-Za-z]*[Rr][^A-Za-z]*[Uu][^A-Za-z]*[Gg][^A-Za-z]*[Gg][^A-Za-z]*[Ll!][^A-Za-z]*[Ee3][^A-Za-z]*|[Uu][^A-Za-z]*[Kk][^A-Za-z]*[Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*|[Mm][^A-Za-z]*[Ee3][^A-Za-z]*[Aa@4][^A-Za-z]*[Nn][^A-Za-z]*[Cc][^A-Za-z]*[Rr][^A-Za-z]*[Ii!][^A-Za-z]*[Mm][^A-Za-z]*[Ss$][^A-Za-z]*|[Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Rr][^A-Za-z]*[Ee3][^A-Za-z]*[Vv][^A-Za-z]*[Ee3][^A-Za-z]*[Nn][^A-Za-z]*[Gg][^A-Za-z]*[Ee3][^A-Za-z]*|[Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Dd][^A-Za-z]*[Ee3][^A-Za-z]*[Aa@4][^A-Za-z]*[Tt][^A-Za-z]*[Hh][^A-Za-z]*)|([Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Tt][^A-Za-z]*[Uu][^A-Za-z]*[Rr][^A-Za-z]*[Ff][^A-Za-z]*)|([Bb][^A-Za-z]*[Ll!][^A-Za-z]*[Aa@4][^A-Za-z]*[Cc][^A-Za-z]*[Kk][^A-Za-z]*[Hh][^A-Za-z]*[Aa@4][^A-Za-z]*[Nn][^A-Za-z]*[Dd][^A-Za-z]*[Ss$][^A-Za-z]*[Yy][^A-Za-z]*[Nn][^A-Za-z]*)|([Pp][^A-Za-z]*[Rr][^A-Za-z]*[Ii!][^A-Za-z]*[Ss$][^A-Za-z]*[Oo0][^A-Za-z]*[Nn][^A-Za-z]*[Hh][^A-Za-z]*[Aa@4][^A-Za-z]*[Vv][^A-Za-z]*[Oo0][^A-Za-z]*[Cc][^A-Za-z]*)|Blackhand-Syn|syn.com|([Dd][^A-Za-z]*[Rr][^A-Za-z]*[Cc][^A-Za-z]*[Ii!][^A-Za-z]*[Tt][^A-Za-z]*[Yy][^A-Za-z]*)|([Cc][^A-Za-z]*[Oo0][^A-Za-z]*[Mm][^A-Za-z]*[Ww][^A-Za-z]*[Aa@4][^A-Za-z]*[Rr][^A-Za-z]*[Ss$][^A-Za-z]*)|([Gg][^A-Za-z]*[Aa@4][^A-Za-z]*[Nn][^A-Za-z]*[Gg][^A-Za-z]*[Ss$][^A-Za-z]*[Tt][^A-Za-z]*[Ee3][^A-Za-z]*[Rr][^A-Za-z]*[Dd][^A-Za-z]*[Ee3][^A-Za-z]*[Aa@4][^A-Za-z]*[Tt][^A-Za-z]*[Hh][^A-Za-z]*)|([Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Cc][^A-Za-z]*[Rr][^A-Za-z]*[Ii!][^A-Za-z]*[Mm][^A-Za-z]*[Ee3][^A-Za-z]*)|([Ff][^A-Za-z]*[Ee3][^A-Za-z]*[Ll!][^A-Za-z]*[Oo0][^A-Za-z]*[Nn][^A-Za-z]*[Yy][^A-Za-z]*[Cc][^A-Za-z]*[Ii!][^A-Za-z]*[Tt][^A-Za-z]*[Yy][^A-Za-z]*)";
    $out1 = str_replace($ea_tr, $ea_sr, $out1);
    $out1 = str_replace("[center]", "<center>", $out1);
    $out1 = str_replace("[/center]", "</center>", $out1);
    $out1 = str_replace("[u]", "<u>", $out1);
    $out1 = str_replace("[/u]", "</u>", $out1);
    $out1 = str_replace("[s]", "<s>", $out1);
    $out1 = str_replace("[test]", '<?php echo Get_Username2(', $out1);
    $out1 = str_replace("[/test]", ') ?>', $out1);
    $out1 = str_replace("[/s]", "</s>", $out1);
    $out1 = str_replace("[big]", "<font size=5>", $out1);
    $out1 = str_replace("[/big]", "</font>", $out1);
    $out1 = str_replace("[img]", "<img src=", $out1);
    $out1 = str_replace("[admin]", "" . Get_Username(1) . "", $out1);
    $out1 = str_replace("[/img]", " border=0></img>", $out1);
    $out1 = str_replace("[IMG]", "<img src=", $out1);
    $out1 = str_replace("[/IMG]", " border=0></img>", $out1);
    $out1 = str_replace("[b]", "<b>", $out1);
    $out1 = str_replace("[/b]", "</b>", $out1);
    $out1 = str_replace("[i]", "<i>", $out1);
    $out1 = str_replace("[/i]", "</i>", $out1);
    $out1 = preg_replace("/\[url\=(.*)\](.*)\[\/url\]/", "<a href=\"$1\">$2</a>", $out1);
    $out1 = preg_replace("/$adverts/", "Wanker", $out1);
    $out1 = str_replace("[enter]", "<br>", $out1);
    $out1 = str_replace("/r/n", "<br>", $out1);
    $out1 = str_replace("[/color]", "</font>", $out1);
    $out1 = preg_replace("/\[color=(\#[0-9A-F]{6}|[a-z]+)\]/", '<font color="$1">', $out1);
    $out1 = preg_replace("~\[user\](.*)\[\/user\]~", "" . Get_Username2('\\1') . "", $out1);
    $out1 = preg_replace("/\[user\](.*)\[\/user\]/Usi", "[user]$1[/user]", $out1);
	$out1 = preg_replace_callback("/\[tag\]([0-9]+)\[\/tag\]/", function($m) {
            return formatName($m[1]);
        }, $out1);
    $idbb = "1";
    $bbcodes_class = new User($idbb);
    $out1 = preg_replace("/\[user\](.*)\[\/user\]/Usi", " $idbb->formattedname ", $out1);
    $out1 = preg_replace("/\[quote](.*)\[\/quote\]/Uis", "<div></div><div style=\"border:solid 1px;\">\\1</div>", $out1);
    $out1 = preg_replace("/\[size=(.*)\](.*)\[\/size\]/Usi", "<span style=\"font-size:\\1ex\">\\2</span>", $out1);
    $out1 = preg_replace("/\[youtube]([^\?&\"'>]{11})\[\/youtube\]/Uis", '<object width="560" height="340"><param name="movie" value="http://www.youtube.com/v/$1&hl=en&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/$1&hl=en&fs=1&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="320" height="320"></embed></object>', $out1);
    return $out1;
}
ob_start("callback1");
?>