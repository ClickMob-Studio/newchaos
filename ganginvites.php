<?php
include 'header.php';


if ($user_class->gangwait != 0)
    diefun("You've just left a gang! Please wait before applying to another gang!.");

if (isset($_GET['accept'])) {
    $gang_class = new Gang($_GET['accept']);

    $checkuser = mysql_query("SELECT playerid FROM ganginvites WHERE playerid = $user_class->id AND gangid = {$_GET['accept']}");
    $username_exist = mysql_num_rows($checkuser);
    if ($username_exist != 0) {
        perform_query("DELETE FROM ganginvites WHERE playerid = ?", $user_class->id);
        perform_query("DELETE FROM gangapps WHERE applicant = ?", $user_class->id);
        perform_query("UPDATE grpgusers SET gang = ? WHERE id = ?", [$gang_class->id, $user_class->id]);
        echo Message("You have joined $gang_class->formattedname.");
        Gang_Event($gang_class->id, "[-_USERID_-] has joined the gang.", $user_class->id);
        perform_query("DELETE FROM gangcontest WHERE userid = ?", $user_class->id);
        perform_query("INSERT INTO gangcontest (userid,gangid) VALUES (?,?)", [$user_class->id, $gang_class->id]);
    }
}
if (isset($_GET['decline'])) {
    perform_query("DELETE FROM ganginvites WHERE playerid = ? AND gangid = ?", [$user_class->id, $_GET['decline']]);
    $invite_gang = new Gang($_GET['decline']);
    echo Message("You have declined the invitation to $invite_gang->formattedname.");
}
print '<tr><td class="contentspacer"></td></tr><tr><td class="contenthead">Gang Invitations</td></tr>
<tr><td class="contentcontent">';
if ($user_class->level <= 9)
    echo '<center><font size="3px"><font color=lime>You must be at least level 10 to join a gang<br></b></font></font></center>';
if ($user_class->level > 9) {
    print '
        <table id="newtables" style="width:90%;margin:auto;">
            <tr>
                <th>Gang</th>
                <th>Accept</th>
                <th>Decline</th>
            </tr>';
    $result = mysql_query("SELECT * FROM ganginvites WHERE playerid = $user_class->id");
    while ($line = mysql_fetch_array($result)) {
        $invite_class = new Gang($line['gangid']);
        echo "
            <tr>
                <td width='50%'>$invite_class->formattedname</td>
                <td width='20%'><a href='ganginvites.php?accept=$invite_class->id'>Accept</a></td>
                <td width='20%'><a href='ganginvites.php?decline=$invite_class->id'>Decline</a></td>
            </tr>";
    }
}
echo "</table>
</td>
</tr>";
include 'footer.php';
?>