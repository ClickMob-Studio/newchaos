<?php
include 'header.php';
$crew_class = new crew($user_class->crew);
$user_rank = new crewRank($user_class->crank);
if ($user_rank->invite != 1 && $user_class->admin != 1)
    diefun("You don't have permission to be here!");
if (isset($_POST['invite']))
    if (!empty($_POST['id'])) {
        security($_POST['id']);
        $to = $_POST['id'];
        $crew = $user_class->crew;
        $invite_class = new User($to);
		$db->query("SELECT id FROM grpgusers WHERE id = ?");
		$db->execute(array(
			$to
		));
		if(!$db->num_rows()){
			diefun('That player ID doesn\'t exist.');
		}
		$db->query("SELECT playerid FROM crewinvites WHERE playerid = ? AND crewid = ?");
		$db->execute(array(
			$to,
			$crew_class->id
		));
		if($db->num_rows()){
			diefun('That user has already been invited to your crew.');
		}
		if ($crew_class->members >= $crew_class->capacity)
            diefun('Your crew already has the maximum number of members.');
        elseif ($invite_class->crew != 0)
            diefun('That user is already in a crew.');
        else {
            $result = mysql_query("INSERT INTO crewinvites (playerid, crewid) VALUES ($to, $crew)");
            echo Message("You have invited $invite_class->formattedname to your crew!");
			Send_Event($to, "[-_USERID_-] has invited you to their crew! <a href='crewinvites.php'>[Click to view Invite]</a>", $user_class->id);
        }
    } else
        diefun("You didn't enter a player ID.");
print"
<tr><td class='contenthead'>Invite User To [$crew_class->tag]</td></tr>
<tr><td class='contentcontent'>
    <form method='post'>
        Here you can invite users to join your crew.<br /><br />
        [ID] <input type='text' name='id' size='15'> <input type='submit' name='invite' value='Invite'>
        <input type='hidden' name = 'crewid' value='$user_class->crew' />
    </form>
</td></tr>
";
include("crewheaders.php");
include 'footer.php';
?>