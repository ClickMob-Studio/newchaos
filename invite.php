<?php
include 'header.php';
?>
<div class='box_top'>Gang Invite</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $gang_class = new Gang($user_class->gang);
        $user_rank = new GangRank($user_class->grank);
        if ($user_rank->invite != 1 && $user_class->admin != 1)
            diefun("You don't have permission to be here!");
        if (isset($_POST['invite']))
            if (!empty($_POST['id'])) {
                security($_POST['id']);
                $to = $_POST['id'];
                $gang = $user_class->gang;
                $invite_class = new User($to);
                $db->query("SELECT id FROM grpgusers WHERE id = ?");
                $db->execute(array(
                    $to
                ));
                if (!$db->num_rows()) {
                    diefun('That player ID doesn\'t exist.');
                }
                $db->query("SELECT playerid FROM ganginvites WHERE playerid = ? AND gangid = ?");
                $db->execute(array(
                    $to,
                    $gang_class->id
                ));
                if ($db->num_rows()) {
                    diefun('That user has already been invited to your gang.');
                }
                if ($gang_class->members >= $gang_class->capacity)
                    diefun('Your gang already has the maximum number of members.');
                elseif ($invite_class->gang != 0)
                    diefun('That user is already in a gang.');
                else {
                    perform_query("INSERT INTO ganginvites (playerid, gangid) VALUES (?, ?)", [$to, $gang]);
                    echo Message("You have invited $invite_class->formattedname to your gang!");
                    Gang_Event($user_class->gang, "[-_USERID_-] has been invited in to their gang.", $to);
                    Send_Event($to, "[-_USERID_-] has invited you to their gang! <a href='ganginvites.php'>[Click to view Invite]</a>", $user_class->id);
                }
            } else
                diefun("You didn't enter a player ID.");
        print "
<tr><td class='contenthead'>Invite User To [$gang_class->tag]</td></tr>
<tr><td class='contentcontent'>
    <form method='post'>
        Here you can invite users to join your gang.<br /><br />
        [ID] <input type='text' name='id' size='15'> <input type='submit' name='invite' value='Invite'>
        <input type='hidden' name = 'gangid' value='$user_class->gang' />
    </form>
</td></tr>
";
        include("gangheaders.php");
        include 'footer.php';
        ?>