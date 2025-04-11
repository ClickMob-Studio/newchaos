<?php
include 'header.php';


if ($user_class->crewwait != 0)
    diefun("You've just left a crew! Please wait before applying to another crew!.");

if (isset($_GET['accept'])) {
    $crew_class = new crew($_GET['accept']);

    $checkuser = mysql_query("SELECT playerid FROM crewinvites WHERE playerid = $user_class->id AND crewid = {$_GET['accept']}");
    $username_exist = mysql_num_rows($checkuser);
    if ($username_exist != 0) {
        $result = mysql_query("DELETE FROM crewinvites WHERE playerid = $user_class->id");
        $result = mysql_query("DELETE FROM crewapps WHERE applicant = $user_class->id");
        $newsql = mysql_query("UPDATE grpgusers SET crew = $crew_class->id WHERE id = $user_class->id");
        echo Message("You have joined $crew_class->formattedname.");
        crew_Event($crew_class->id, "[-_USERID_-] has joined the crew.", $user_class->id);
        mysql_query("DELETE FROM crewcontest WHERE userid = $user_class->id");
        mysql_query("INSERT INTO crewcontest (userid,crewid) VALUES ($user_class->id,$crew_class->id)");
    }
}
if (isset($_GET['decline'])) {
    $result = mysql_query("DELETE FROM crewinvites WHERE playerid = $user_class->id AND crewid = {$_GET['decline']}");
    $invite_crew = new crew($_GET['decline']);
    echo Message("You have declined the invitation to $invite_crew->formattedname.");
}
print '<tr><td class="contentspacer"></td></tr><tr><td class="contenthead">crew Invitations</td></tr>
<tr><td class="contentcontent">';
if ($user_class->level <= 9)
    echo '<center><font size="3px"><font color=darkgreen>You must be at least level 10 to join a crew<br></b></font></font></center>';
if ($user_class->level > 9) {
    print '
        <table id="newtables" style="width:90%;margin:auto;">
            <tr>
                <th>crew</th>
                <th>Accept</th>
                <th>Decline</th>
            </tr>';
    $result = mysql_query("SELECT * FROM crewinvites WHERE playerid = $user_class->id");
    while ($line = mysql_fetch_array($result)) {
        $invite_class = new crew($line['crewid']);
        echo "
            <tr>
                <td width='50%'>$invite_class->formattedname</td>
                <td width='20%'><a href='crewinvites.php?accept=$invite_class->id'>Accept</a></td>
                <td width='20%'><a href='crewinvites.php?decline=$invite_class->id'>Decline</a></td>
            </tr>";
    }
}
echo "</table>
</td>
</tr>";
include 'footer.php';
?>