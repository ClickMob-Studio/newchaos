<?php
exit();
include "ajax_header.php";
mysql_select_db('aa', mysql_connect('localhost', 'aa_user', 'GmUq38&SVccVSpt'));
$user_class = new User($_SESSION['id']);

if (isset($_POST['jailbreak'])) {
    $jailbreak = security($_POST['jailbreak']);
    $db->query("SELECT jail, id FROM grpgusers WHERE id = ?");
    $db->execute(array(
        $jailbreak
    ));
    if (!$rtn = $db->fetch_row(true))
        $error = ("That person doesn't exist.");
    if (!$rtn['jail'])
        $error = ("That Mobster is not in Jail.");
    if ($jailbreak == $user_class->id)
        $error = ("You can't bust yourself.");
    if ($user_class->jail > 0)
        $error = ("You can't bust someone while you're in the jail.");
    if ($user_class->hospital > 0)
        $error = ("You can't bust someone while you're in hospital.");
    $chance = rand(1, 100);
    $nerve = 10;
    $exp = 500;
    if (empty($error)) {
        if ($user_class->nerve < $nerve)
            refill('n');
        if ($user_class->nerve >= $nerve) {
            $user_class->nerve -= $nerve;
            if ($chance <= 95) {
                $state = 1;
                $message = "You successfully broke " . formatName($jailbreak) . " out of jail. You receive $exp exp and 3 Points";
                $db->query("UPDATE grpgusers SET `both` = `both` + 1, exp = exp + ?, busts = busts + 1, points = points +3, nerve = nerve - ? WHERE id = ?");
                $db->execute(array(
                    $exp,
                    $nerve,
                    $user_class->id
                ));
                $db->query("UPDATE grpgusers SET jail = 0 WHERE id = ?");
                $db->execute(array(
                    $jailbreak
                ));
                Send_Event($jailbreak, "You have been busted out of Jail by [-_USERID_-].", $user_class->id);
                mission('b');
                newmissions('busts');
                gangContest(array(
                    'busts' => 1,
                    'exp' => $exp
                ));
                $toadd = array('botd' => 1);
                ofthes($user_class->id, $toadd);
                bloodbath('busts', $user_class->id);
            } elseif ($chance >= 96) {
                $state = 2;
                $message = "You attempted to break " . formatName($jailbreak) . " out of jail but you were caught.<br />You were hauled into jail with them for 10 minutes.";
                $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, caught = caught + 1, jail = 600, nerve = nerve - ? WHERE id = ?");
                $db->execute(array(
                    $nerve,
                    $user_class->id
                ));
            } else {
                $state = 3;
                $message = "You tried to break " . formatName($jailbreak) . " out of jail but you failed, Better luck next time.";
                $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ? WHERE id = ?");
                $db->execute(array(
                    $nerve,
                    $user_class->id
                ));
            }
        } else
            $error = "You don't have enough nerve to break someone out of jail.<br /><br /><a href='jail.php'>Go Back</a>";
    }
}

$html = (empty($error)) ? $message : $error;
$response = array(
    'state' => $state,
    'html' => $html
);
print (json_encode(Message($response)));