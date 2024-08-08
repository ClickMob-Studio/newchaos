<?php

session_start();

function error($msg) {
    return array(
        'success' => 'false',
        'error' => $msg
    );
}

function success($msg) {
    return array(
        'success' => 'TRUE',
        'message' => $msg
    );
}

include "SlimUser.php";
include "classes.php";
include "database/pdo_class.php";

function ofthes_wrapper($id, $toadd) {
    ofthes($id, $toadd);
}

$active = time() - 604800;

try {
    $m = new Memcache();
    $m->addServer('127.0.0.1', 11211, 33);

    $user_class = new SlimUser($_SESSION['id']);
    session_write_close();

    $response = array();

    if (!isset($_GET['alv']) || $_GET['alv'] !== 'yes') {
        echo json_encode(error('Something went wrong.'));
        exit;
    }

    $attack_person = new SlimUser($_GET['mug']);
    $gang_class = new Gang($user_class->gang);

    $db->query("UPDATE grpgusers SET lastactive = unix_timestamp() WHERE id = ?");
    $db->execute(array($user_class->id));

    if ($attack_person->mprotection > time()) {
        echo json_encode(error("This player is currently under mug protection."));
        exit;
    }

    // Attempt to refill nerve if it's less than 10
    if ($user_class->nerve < 10) {
     //   refill('n');
    }

    $conditions = array(
        array($user_class->fbitime > 0, 'You can\'t perform a mug whilst in FBI Jail.'),
        array($attack_person->fbitime > 0, 'This user is currently in FBI Jail.'),
        array($attack_person->city != $user_class->city, 'You must be in the same city as the person you are mugging.'),
        array($user_class->nerve < 10, 'You need to have at least 10 nerve if you want to mug someone.'),
        array($attack_person->level < 0, 'This player is under newbie protection.'),
        array($user_class->jail > 0, 'You can\'t mug someone if you\'re in prison.'),
        array($user_class->hospital > 0, 'You can\'t mug someone if you\'re in the hospital.'),
        array(empty($_GET['mug']), 'You didn\'t choose someone to mug.'),
        array($_GET['mug'] == $user_class->id, 'You can\'t mug yourself.'),
        array(empty($attack_person->username), 'That person doesn\'t exist.'),
        // array($attack_person->hospital > 0, 'You can\'t mug someone that\'s in hospital.'),
        array($attack_person->jail > 0, 'You can\'t mug someone that\'s in prison.'),
        array($attack_person->gang == $user_class->gang && $user_class->gang > 0, 'You can\'t mug someone that\'s in your gang.'),
        array($attack_person->id == $user_class->relplayer, 'You can\'t mug your partner.'),
        array($attack_person->mprotection > time(), 'Your target is under mug protection and cannot be mugged.'),
        array($attack_person->admin > 0, 'You can\'t mug an admin.'),
        array($user_class->mprotection > time(), 'You have an active mug protection and cannot mug during this time.')
    );

    foreach ($conditions as $condition) {
        if ($condition[0]) {
            echo json_encode(error($condition[1]));
            exit;
        }
    }

    $newnerve = $user_class->nerve - 10;
    $db->query("UPDATE grpgusers SET nerve = ?, last_mug_time = ? WHERE id = ?");
    $db->execute(array($newnerve, time(), $user_class->id));

    $mug = mt_rand(0, 10);

    if ($mug > 9 && $user_class->bustpill > 0) {
        $mug = 9;
    }

    if ($mug <= 8) {
        $success = true;

        $robinfo = explode("|", $attack_person->robInfo);
        if ($robinfo[0] == 1) {
            $success = mt_rand(0, 2) != 0;
        }

        if ($attack_person->fbi > 0) {
            $db->query("UPDATE grpgusers SET fbitime = ? WHERE id = ?");
            $db->execute(array(15, $user_class->id));

            Send_Event($user_class->id, "You mugged a player being watched by the FBI! You landed yourself in Federal Jail for 15 minutes!");
            Send_Event($attack_person->id, "You just got mugged and this player has landed themselves in FBI Jail!");

            echo json_encode(error('The FBI are watching this player! You have been sent to FBI Jail!'));
            exit;
        }

        if ($success) {
            $divide = rand(7, 8);
            $mugamount = floor($attack_person->money / $divide);

            if ($gang_class->upgrade8 >= 1) {
                $mugamount = floor($mugamount * (1 + 0.10 * $gang_class->upgrade8));
            }

            if ($user_class->gang > 0 && $gang_class->upgrade8 > 0) {
                $mugamount = floor($mugamount * (1 + 0.10 * $gang_class->upgrade8));
            }

            addToGangCompLeaderboard($user_class->gang, 'mugs_complete', 1);
            $bpCategory = getBpCategory();
            if ($bpCategory) {
                addToBpCategoryUser($bpCategory, $user_class, 'mugs', 1);
            }
            addToUserCompLeaderboard($user_class->id, 'mugs_complete', 1);

            $db->query("SELECT * FROM activity_contest WHERE id = 1 LIMIT 1");
            $db->execute();
            $activityContest = $db->fetch_row();
            if ($activityContest['type'] == 'mugs') {
                addToUserCompLeaderboard($user_class->id, 'activity_complete', $activityContest['type_value']);
            }

            if ($mugamount < 1) {
                $db->query("UPDATE grpgusers SET mugsucceeded = mugsucceeded + 1, moth = moth + 1, motd = motd + 1 WHERE id = ?");
                $db->execute(array($user_class->id));

                mission('m');
                newmissions('mugs');
                updateGangActiveMission('mugs', 1);
                gangContest(array('mugs' => 1));
                bloodbath('mugs', $user_class->id);
                ofthes_wrapper($user_class->id, array('motd' => 1));

                echo json_encode(success("You reach into {$attack_person->formattedname}'s pockets and find nothing!"));
                exit;
            } else {
                if ($user_class->gang != 0 && $gang_class->tax > 0) {
                    $tax = round($mugamount / 100) * $gang_class->tax;
                    $mugamount -= $tax;
                    $newvault = $gang_class->moneyvault + $tax;

                    $db->query("UPDATE gangs SET moneyvault = ? WHERE id = ?");
                    $db->execute(array($newvault, $user_class->gang));
                }

                $newmuggedamount = $attack_person->money - $mugamount;
                $newmuggeramount = $user_class->money + $mugamount;

                $db->query("UPDATE grpgusers SET money = ?, muggedmoney = muggedmoney + ?, mugsucceeded = mugsucceeded + 1, moth = moth + 1, motd = motd + 1, tamt = tamt + ? WHERE id = ?");
                $db->execute(array($newmuggeramount, $mugamount, $mugamount, $user_class->id));

                $db->query("UPDATE grpgusers SET muggedmoney = muggedmoney - ?, money = ? WHERE id = ?");
                $db->execute(array($mugamount, $newmuggedamount, $attack_person->id));

                $online = (time() - $attack_person->lastactive < 900) ? 1 : 0;
                $db->query("INSERT INTO muglog (mugger, mugged, amount, active, timestamp) VALUES (?, ?, ?, ?, unix_timestamp())");
                $db->execute(array($user_class->id, $attack_person->id, $mugamount, $online));

                mission('m');
                newmissions('mugs');
                updateGangActiveMission('mugs', 1);
                gangContest(array('mugs' => 1));
                bloodbath('mugs', $user_class->id);
                if($attack_person->lastactive > $active){
                    Send_Event($attack_person->id, "You were mugged by [-_USERID_-]. They stole " . prettynum($mugamount, 1) . ".", $user_class->id);
                }
                echo json_encode(success("You successfully mugged {$attack_person->formattedname} for " . prettynum($mugamount, 1) . "."));
                exit;
            }
        } else {
            if($attack_person->lastactive > $active){
                Send_Event($attack_person->id, "[-_USERID_-] tried to mug you, but failed.", $user_class->id);
            }
            echo json_encode(success("You failed to mug {$attack_person->formattedname}."));
            exit;
        }
    } else if ($mug == 9) {
        if($attack_person->lastactive > $active){
            Send_Event($attack_person->id, "[-_USERID_-] tried to mug you, but failed.", $user_class->id);
        }
        $response = success("You failed to mug " . $attack_person->formattedname . ".");
        echo json_encode($response);
        exit;
    } else {
        if($attack_person->lastactive > $active){
            Send_Event($attack_person->id, "[-_USERID_-] tried to mug you, but failed.", $user_class->id);
        }
        $db->query("UPDATE grpgusers SET jail = ? WHERE id = ?");
        $db->execute(array(300, $user_class->id));

        echo json_encode(success("You failed and were sent to prison for 5 minutes!"));
        exit;
    }
} catch (Exception $e) {
    echo json_encode(error('An unexpected error occurred: ' . $e->getMessage()));
    exit;
}
?>
