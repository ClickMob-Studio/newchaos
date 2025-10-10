<?php
$smiarr = array(
    ':smile:' => array('smile.gif'),
    ':nerd:' => array('nerd.gif'),
    ':blowkiss:' => array('kiss.gif', 23, 26),
    ':bigkiss:' => array('bigkiss.jpg', 32, 32),
    ':sad:' => array('frown.gif', 20, 24),
    ':grin:' => array('teeth.gif'),
    ':shock:' => array('surprise.gif'),
    ':scared:' => array('scared.gif', 42, 34),
    ':tongue:' => array('tongue.gif'),
    ':yay:' => array('anime.gif'),
    ':wave:' => array('wave.gif', 26, 18),
    ':haha:' => array('haha.gif', 28, 20),
    ':lol:' => array('lol.gif', 30, 20),
    ':zzz:' => array('sleepy.gif', 20, 20),
    ':star:' => array('star.gif'),
    ':angry:' => array('angry.gif'),
    ':evil:' => array('evil.gif', 37, 31),
    ':angel:' => array('angel.gif', 27, 26),
    ':confused:' => array('confused.gif', 32, 20),
    ':eek:' => array('neutral.gif'),
    ':cool:' => array('cool.gif', 20, 20),
    ':shifty:' => array('shifty.gif'),
    ':huh:' => array('boggle.gif'),
    ':love:' => array('heart.gif', 28, 26),
    ':wink:' => array('wink.gif', 20, 24),
    ':P' => array('tongue.gif'),
    ':)' => array('smile.gif'),
    ':(' => array('frown.gif'),
    ':-)' => array('smile.gif'),
    ':-(' => array('frown.gif'),
    ';)' => array('wink.gif'),
    ':D' => array('teeth.gif'),
    ':bash:' => array('bash.gif', 31, 26),
    ':hmm:' => array('hmm.gif', 20, 29),
    ':facepalm:' => array('facepalm.gif', 28, 24),
    ':alcoholic:' => array('alcoholic.gif', 40, 20),
    ':cry:' => array('cry.gif', 31, 22),
    ':needdrugs:' => array('needdrugs.gif', 41, 46),
    ':popcorn:' => array('popcorn.gif', 37, 28),
    ':evilgirl:' => array('evilgirl.gif', 46, 28),
    ':coffeescreen:' => array('coffeescreen.gif', 43, 36),
    ':puke:' => array('puke.gif', 20, 20),
    ':hug:' => array('hug.gif', 40, 15),
    ':grouphug:' => array('grouphug.gif', 57, 15),
    ':cheers:' => array('cheers.gif', 51, 28),
    ':smoke:' => array('smoke.gif', 35, 30),
    ':evillaugh:' => array('evillaugh.gif', 75, 40),
    ':fyou:' => array('fyou.gif', 27, 24),
    ':yes:' => array('yes.gif', 27, 24),
    ':no:' => array('no.gif', 29, 28),
    ':yahoo:' => array('yahoo.gif', 42, 27),
    ':cheat:' => array('cheat.gif', 86, 42),
    ':faceslap:' => array('face-slap.gif', 78, 32),
    ':whip:' => array('whipping.gif', 96, 71),
    ':buttkisser:' => array('butt-kisser.gif', 64, 57),
);
function BBCodeParse($str)
{
    global $user_class, $smiarr;

    $str = str_replace("[url]http://www.", "[url]http://", $str);
    $str = str_replace("[url]http://www2.", "[url]http://", $str);
    $str = str_replace("[url]www.", "[url]http://", $str);
    $str = str_replace("[url]www2.", "[url]http://", $str);
    $str = str_replace("<", "&lt;", $str);
    $str = str_replace(">", "&gt;", $str);
    $str = str_replace('\"', '&quot;', $str);
    $str = str_replace("\'", "&#39;", $str);
    $str = str_replace("&lt;br /&gt;", "<br />", $str);
    $adverts = "([Dd][^A-Za-z]*[Oo0][^A-Za-z]*[Pp][^A-Za-z]*[Ee3][^A-Za-z]*[Rr][^A-Za-z]*[Uu][^A-Za-z]*[Nn][^A-Za-z]*[Nn][^A-Za-z]*[Ee3][^A-Za-z]*[Rr][^A-Za-z]*[Ss$][^A-Za-z]*)|([Uu][^A-Za-z]*[Nn][^A-Za-z]*[Tt][^A-Za-z]*[Oo0][^A-Za-z]*[Ll!][^A-Za-z]*[Dd][^A-Za-z]*[Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*)|([Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Nn][^A-Za-z]*[Dd][^A-Za-z]*[Ee3][^A-Za-z]*[Mm][^A-Za-z]*[Zz2][^A-Za-z]*)|([Aa@4][^A-Za-z]*[Rr][^A-Za-z]*[Mm][^A-Za-z]*[Yy][^A-Za-z]*[Ww][^A-Za-z]*[Aa@4][^A-Za-z]*[Rr][^A-Za-z]*[Ss$][^A-Za-z]*)|([Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Oo0][^A-Za-z]*[Uu][^A-Za-z]*[Tt][^A-Za-z]*[Ll!][^A-Za-z]*[Aa@4][^A-Za-z]*[Ww][^A-Za-z]*[Zz2][^A-Za-z]*)|([Tt][^A-Za-z]*[Hh][^A-Za-z]*[Uu][^A-Za-z]*[Gg][^A-Za-z]*[Pp][^A-Za-z]*[Aa@4][^A-Za-z]*[Rr][^A-Za-z]*[Aa@4][^A-Za-z]*)|([Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Tt][^A-Za-z]*[Hh][^A-Za-z]*[Uu][^A-Za-z]*[Gg][^A-Za-z]*)|([Gg][^A-Za-z]*[Aa@4][^A-Za-z]*[Nn][^A-Za-z]*[Gg][^A-Za-z]*[Ss$][^A-Za-z]*[Tt][^A-Za-z]*[Ee3][^A-Za-z]*[Rr][^A-Za-z]*[Ll!][^A-Za-z]*[Aa@4][^A-Za-z]*[Nn][^A-Za-z]*[Dd][^A-Za-z]*)|([^A-Za-z]*[Rr][^A-Za-z]*[Ii!][^A-Za-z]*[Vv][^A-Za-z]*[Aa@4][^A-Za-z]*[Ll!][^A-Za-z]*[Ss$][^A-Za-z]*|[Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Rr][^A-Za-z]*[Ii!][^A-Za-z]*[Vv][^A-Za-z]*[Aa@4][^A-Za-z]*[Ll!][^A-Za-z]*[Ss$][^A-Za-z]*|[Gg][^A-Za-z]*[Aa@4][^A-Za-z]*[Nn][^A-Za-z]*[Gg][^A-Za-z]*[Ss$][^A-Za-z]*[Tt][^A-Za-z]*[Aa@4][^A-Za-z]*[Cc][^A-Za-z]*[Ii!][^A-Za-z]*[Tt][^A-Za-z]*[Yy][^A-Za-z]*[Oo0][^A-Za-z]*[Nn][^A-Za-z]*[Ll!][^A-Za-z]*[Ii!][^A-Za-z]*[Nn][^A-Za-z]*[Ee3][^A-Za-z]*|[Dd][^A-Za-z]*[Oo0][^A-Za-z]*[Pp][^A-Za-z]*[Ee3][^A-Za-z]*[Rr][^A-Za-z]*[Uu][^A-Za-z]*[Nn][^A-Za-z]*[Nn][^A-Za-z]*[Ee3][^A-Za-z]*[Rr][^A-Za-z]*[Ss$][^A-Za-z]*|[Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Rr][^A-Za-z]*[Ee3][^A-Za-z]*[Vv][^A-Za-z]*[Oo0][^A-Za-z]*[Ll!][^A-Za-z]*[Tt][^A-Za-z]*|[Cc][^A-Za-z]*[Rr][^A-Za-z]*[Ii!][^A-Za-z]*[Mm][^A-Za-z]*[Ee3][^A-Za-z]*[Kk][^A-Za-z]*[Ii!][^A-Za-z]*[Nn][^A-Za-z]*[Gg][^A-Za-z]*[Ss$][^A-Za-z]*|[Pp][^A-Za-z]*[Rr][^A-Za-z]*[Ii!][^A-Za-z]*[Ss$][^A-Za-z]*[Oo0][^A-Za-z]*[Nn][^A-Za-z]*[Ss$][^A-Za-z]*[Tt][^A-Za-z]*[Rr][^A-Za-z]*[Uu][^A-Za-z]*[Gg][^A-Za-z]*[Gg][^A-Za-z]*[Ll!][^A-Za-z]*[Ee3][^A-Za-z]*|[Uu][^A-Za-z]*[Kk][^A-Za-z]*[Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*|[Mm][^A-Za-z]*[Ee3][^A-Za-z]*[Aa@4][^A-Za-z]*[Nn][^A-Za-z]*[Cc][^A-Za-z]*[Rr][^A-Za-z]*[Ii!][^A-Za-z]*[Mm][^A-Za-z]*[Ss$][^A-Za-z]*|[Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Rr][^A-Za-z]*[Ee3][^A-Za-z]*[Vv][^A-Za-z]*[Ee3][^A-Za-z]*[Nn][^A-Za-z]*[Gg][^A-Za-z]*[Ee3][^A-Za-z]*|[Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Dd][^A-Za-z]*[Ee3][^A-Za-z]*[Aa@4][^A-Za-z]*[Tt][^A-Za-z]*[Hh][^A-Za-z]*)|([Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Tt][^A-Za-z]*[Uu][^A-Za-z]*[Rr][^A-Za-z]*[Ff][^A-Za-z]*)|([Bb][^A-Za-z]*[Ll!][^A-Za-z]*[Aa@4][^A-Za-z]*[Cc][^A-Za-z]*[Kk][^A-Za-z]*[Hh][^A-Za-z]*[Aa@4][^A-Za-z]*[Nn][^A-Za-z]*[Dd][^A-Za-z]*[Ss$][^A-Za-z]*[Yy][^A-Za-z]*[Nn][^A-Za-z]*)|([Pp][^A-Za-z]*[Rr][^A-Za-z]*[Ii!][^A-Za-z]*[Ss$][^A-Za-z]*[Oo0][^A-Za-z]*[Nn][^A-Za-z]*[Hh][^A-Za-z]*[Aa@4][^A-Za-z]*[Vv][^A-Za-z]*[Oo0][^A-Za-z]*[Cc][^A-Za-z]*)|Blackhand-Syn|syn.com|([Dd][^A-Za-z]*[Rr][^A-Za-z]*[Cc][^A-Za-z]*[Ii!][^A-Za-z]*[Tt][^A-Za-z]*[Yy][^A-Za-z]*)|([Cc][^A-Za-z]*[Oo0][^A-Za-z]*[Mm][^A-Za-z]*[Ww][^A-Za-z]*[Aa@4][^A-Za-z]*[Rr][^A-Za-z]*[Ss$][^A-Za-z]*)|([Gg][^A-Za-z]*[Aa@4][^A-Za-z]*[Nn][^A-Za-z]*[Gg][^A-Za-z]*[Ss$][^A-Za-z]*[Tt][^A-Za-z]*[Ee3][^A-Za-z]*[Rr][^A-Za-z]*[Dd][^A-Za-z]*[Ee3][^A-Za-z]*[Aa@4][^A-Za-z]*[Tt][^A-Za-z]*[Hh][^A-Za-z]*)|([Mm][^A-Za-z]*[Aa@4][^A-Za-z]*[Ff][^A-Za-z]*[Ii!][^A-Za-z]*[Aa@4][^A-Za-z]*[Cc][^A-Za-z]*[Rr][^A-Za-z]*[Ii!][^A-Za-z]*[Mm][^A-Za-z]*[Ee3][^A-Za-z]*)|([Ff][^A-Za-z]*[Ee3][^A-Za-z]*[Ll!][^A-Za-z]*[Oo0][^A-Za-z]*[Nn][^A-Za-z]*[Yy][^A-Za-z]*[Cc][^A-Za-z]*[Ii!][^A-Za-z]*[Tt][^A-Za-z]*[Yy][^A-Za-z]*)";
    $format_search = array(
        '#\[b\](.*?)\[/b\]#is',
        '#\[center\](.*?)\[/center\]#is',
        '#\[left\](.*?)\[/left\]#is',
        '#\[i\](.*?)\[/i\]#is',
        '#\[u\](.*?)\[/u\]#is',
        '#\[s\](.*?)\[/s\]#is',
        '#\[colo[u]{0,1}r=(.*?)\](.*?)\[/colo[u]{0,1}r\]#is',
        '#\[url=((?:ftp|https?)://.*?)\](.*?)\[/url\]#i',
        '#\[url\]((?:ftp|https?)://.*?)\[/url\]#i',
        '#\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp|webp))\[/img\]#i',
        '#\[youtube\](.*?)\[/youtube\]#is',
        '#\[mp3\](.*?)\[/mp3\]#is',
        '#\[size=(.*?)\](.*?)\[/size\]#is',
        "#\{$adverts}#is",
        '#\[he\/she\]#is',
        //'#\[me\]#is' // Formatted username ([me])
    );
    $format_replace = array(
        '<b>$1</b>',
        '<center>$1</center>',
        '<div align="left">$1</div>',
        '<i>$1</i>',
        '<u>$1</u>',
        '<s>$1</s>',
        '<font color="$1">$2</font>',
        '<a href="$1">$2</a>',
        '<a href="$1">$1</a>',
        '<img src="$1" alt="" border="0" style="max-width:500px;" />',
        '<iframe src="https://www.youtube.com/embed/$1" allowfullscreen="" frameborder="0" height="344" width="425"></iframe>',
        '',
        '<font size="$1">$2</font>',
        'Wanker',
        ($user_class->gender == 'Female') ? 'she' : 'he',
        ' ',
        //$user_class->formattedname
    );
    $smiley_search = $smiley_replace = array();
    foreach ($smiarr as $index => $img) {
        $smiley_search[] = $index;
        if (empty($img[1]))
            $img[1] = $img[2] = 19;
        $smiley_replace[] = '<img src="smileys/' . $img[0] . '" width="' . $img[1] . '" height="' . $img[2] . '" border="0" style="vertical-align: bottom;" alt="" />';
    }
    $str = preg_replace($format_search, $format_replace, $str);

    $str = preg_replace_callback("/\[user\](.*)\[\/user\]/", function ($matches) {
        return displayInfo($matches[1]);
    }, $str);

    $str = preg_replace_callback("/\[user2\](.*)\[\/user2\]/", function ($matches) {
        return displayInfo2($matches[1]);
    }, $str);

    $str = preg_replace_callback("/\[quote=([0-9]*?)\]/", function ($matches) {
        return quote1($matches[1]);
    }, $str);

    $str = preg_replace_callback("/\[\/quote\]/", function ($matches) {
        return quote2();
    }, $str);

    $str = preg_replace_callback("/\[tag\]([0-9]+)\[\/tag\]/", function ($m) {
        return formatName($m[1], 1);
    }, $str);
    $str = str_replace($smiley_search, $smiley_replace, $str);
    $str = ($user_class->music) ? $str : str_replace('autoplay=true', '', $str);
    $str = ($user_class->music) ? $str : str_replace('autoplay=1', '', $str);
    $str = nl2br($str);
    return $str;
}
function MP3Parse($str2)
{
    global $db;

    $db->query("SELECT * FROM `grpgusers` WHERE `id` = ?");
    $db->execute([$_SESSION['id']]);
    $worked = $db->fetch_row(true);

    $search = array(
        '#\[mp3\]((?:ftp|https?)://.*?)\[/mp3\]#i'
    );
    $replace = array(
        '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="30" height="30" id="player"><param name="allowScriptAccess" value="sameDomain" /><param name="movie" value="http://mafia-warfare.com/player.swf" /><param name="quality" value="high" /><param name="bgcolor" value="#111111" /><param name="FlashVars" value="mp3=$1&vol=' . $worked['volume'] . '" /><embed src="http://mafia-warfare.com/player.swf" quality="high" bgcolor="#111111" width="30" height="30" name="player" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" FlashVars="mp3=$1&vol=' . $worked['volume'] . '" /></object>'
    );

    $str2 = preg_replace($search, $replace, $str2);

    return $str2;
}
function quote1($id)
{
    return "<table id='newtables' style='margin:0 auto;padding:0;width:90%;'><tr><th>" . formatName($id) . " wrote:</th></tr><tr><td>";
}
function quote2()
{
    return "</td></tr></table>";
}
?>