<?php
exit;

include "ajax_header.php";

$user_class = new User($_SESSION['id']);

if (isset($_POST['bust'])) {
    $bust = security($_POST['bust']);
    $db->query("SELECT jail, id FROM grpgusers WHERE id = ?");
    $db->execute(array(
        $bust
    ));
    $rtn = $db->fetch_row(true);
    if (!$rtn)
        $error = "That person doesn't exist.";
    if (!$rtn['jail'])
        $error = "That Mobster is not in Jail.";
    if ($bust == $user_class->id)
        $error = "You can't bust yourself.";
    if ($user_class->jail > 0)
        $error = "You can't bust someone while you're in the jail. <a onclick='bail();'>Bail Out</a>";
    if ($user_class->hospital > 0)
        $error = "You can't bust someone while you're in hospital.";
    if ($user_class->fbijail > 0)
        $error = "You can't bust someone whilst you're in FBI Jail.";

    if ($user_class->bustpill > 0)
        $chance = 77;
    else
        $chance = rand(1, 100);

    $nerve = 10;
    $exp = 2500;
    if (empty($error)) {
        if ($user_class->nerve < $nerve)
            refill('n');
        if ($user_class->nerve >= $nerve) {
            $user_class->nerve -= $nerve;
            if ($chance <= 99) {
                $success = "You successfully broke " . formatName($bust) . " out of jail. You receive $exp exp + 3 Points.";
                $db->query("INSERT INTO busts_log (buster_id, jailed_id) VALUES (?, ?)");
                $db->execute(array(
                    $user_class->id,
                    $bust
                ));

                // Remove memcache logic here

                if ($user_class->gang != 0) {
                    $db->query("UPDATE gangs SET dailyBusts = dailyBusts + 1 WHERE id = ?");
                    $db->execute(array(
                        $user_class->gang
                    ));
                }

                $db->query("UPDATE grpgusers SET `both` = `both` + 1, `epoints` = `epoints` + `eventbusts`, `bustcomp` = `bustcomp` + 1, exp = exp + ?, busts = busts + 1, points = points + 3, nerve = nerve - ? WHERE id = ?");
                $db->execute(array(
                    $exp,
                    $nerve,
                    $user_class->id
                ));
                $db->query("UPDATE grpgusers SET jail = 0 WHERE id = ?");
                $db->execute(array(
                    $bust
                ));
                Send_Event($bust, "You have been busted out of Jail by [-_USERID_-].", $user_class->id);
                mission('b');
                newmissions('busts');
                gangContest(array(
                    'busts' => 1,
                    'exp' => $exp
                ));
                $toadd = array('botd' => 1);
                ofthes($user_class->id, $toadd);
                bloodbath('busts', $user_class->id);
                addToUserOperations($user_class, 'busts', 1);
            } elseif ($chance >= 96) {
                $error = "You attempted to break " . formatName($bust) . " out of jail but you were caught. You were hauled into jail with them for 10 minutes. <a onclick='bail();'>Bail Out</a>";
                $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, caught = caught + 1, jail = 600, nerve = nerve - ? WHERE id = ?");
                $db->execute(array(
                    $nerve,
                    $user_class->id
                ));
            } else {
                $error = "You tried to break " . formatName($bust) . " out of jail but you failed, Better luck next time.";
                $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ? WHERE id = ?");
                $db->execute(array(
                    $nerve,
                    $user_class->id
                ));
            }
        } else
            $error = "You don't have enough nerve to break someone out of jail.";
    }
    if (isset($error)) {
        die(json_encode(array('code' => 'error', 'message' => $error)));
    } elseif (isset($success)) {
        die(json_encode(array('code' => 'success', 'message' => $success)));
    }
} elseif (isset($_POST['bail'])) {
    $cost = ceil($user_class->jail / 60);
    if ($user_class->jail > 0 AND $user_class->points >= $cost) {
        $user_class->points -= $cost;
        $user_class->jail = 0;
        $db->query("UPDATE grpgusers SET jail = 0, points = points - ? WHERE id = ?");
        $db->execute(array(
            $cost,
            $user_class->id
        ));
        $success = 'You have bailed yourself out for ' . $cost . ' points.';

        // Remove memcache logic here

    } else {
        $error = 'You need ' . $cost . ' points to bail yourself out.';
    }
    if (isset($error)) {
        die(json_encode(array('code' => 'error', 'message' => $error)));
    } elseif (isset($success)) {
        die(json_encode(array('code' => 'success', 'message' => $success)));
    }
} else {
    // var_dump($m);
    // $m->set('v2cells', 'test');
    // $cells = $m->get('v2cells');
    // echo json_encode($cells);

    // Fetch list of jailed users

    $ignore = array($user_class->id);
    $ignore = implode(',', $ignore);

    $db->query("SELECT id, jail FROM grpgusers WHERE jail > 0 AND id NOT IN ($ignore) ORDER BY jail ASC");
    $db->execute();
    $rows = $db->fetch_row();

    // Generate array of user ids [2, 5, 9]

    $rowJailed = array_map(function($a) {
        return $a['id'];
    }, $rows);

    // Generate cells available 1-12
    $available_cells = range(0, 11);

    // Fetch cached data of cells
    $cells = array();
    // Remove memcache logic here

    foreach ($rows as $row) {
        $cell = array_rand($available_cells);
        $cells[] = array(
            'id' => $row['id'],
            'username' => str_replace('</a>', '', preg_replace('/<a[^>]*>/', '', formatName($row['id']))),
            'cell' => $cell
        );
        unset($available_cells[$cell]);
    }
    echo json_encode($cells);
}

function assign_cell($userid, $available_cells) {
    global $m, $user_class;
    if (!$rtn = $m->get('v2cell.' . $user_class->id . '.' . $userid)) {
        if ($rtn !== false) {
            //$rtn = array_pop($available_cells);
            $rtn = array_rand($available_cells);
            $m->set('v2cell.' . $user_class->id . '.' . $userid, $rtn);
        }
    }
    return $rtn;
}
?>
