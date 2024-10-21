<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


	include 'header.php';

if (isset($_GET['forced_captcha']) && $_GET['forced_captcha'] == 'yes') {
    mysql_query('UPDATE `grpgusers` SET `captcha_timestamp` = 0 WHERE `id` = ' . $user_class->id);

    header('Location: captcha.php?token=' . $user_class->macro_token . '&page=profiles&pid=' . $_GET['id']);
}

$_GET['id'] = filter_var($_GET['id'], FILTER_VALIDATE_INT);

$profile_class = new User($_GET['id']);

$userPrestigeSkills = getUserPrestigeSkills($user_class);

$halloweenUserList = getHalloweenUserList($user_class->id);

if (isset($halloweenUserList) && isset($_GET['caction']) && $_GET['caction'] == 'trickortreat') {
    if (in_array($profile_class->id, $halloweenUserList['user_id_list'])) {
        diefun('You can only trick or treat once per hour.');
    }

    if ($user_class->jail || $user_class->hospital) {
        diefun('You can\'t trick or treat whilst your in the hospital or jail.');
    }

    $halloweenUserList['user_id_list'][] = $profile_class->id;
    $newHalloweenUserList = join(',', $halloweenUserList['user_id_list']);

    $db->query("UPDATE halloween_user_list SET listed_user_ids = ? WHERE user_id = ?");
    $db->execute(array(
        $newHalloweenUserList,
        $user_class->id
    ));

    $score = mt_rand(1,1000);

    if ($score <= 250) {
        // Failure
        $db->query("UPDATE grpgusers SET jail = 300 WHERE id = ?");
        $db->execute(array(
            $user_class->id
        ));

        diefun('It\'s a trick! You\'ll need to spend the next 5 minutes in jail.');
    } else {
        // Success

        if ($score <= 252) {
            Give_Item(255, $user_class->id, 1);

            addToHalloweenPayoutLogs('Crime Booster');

            diefun('It\'s a treat! You found 1 x Crime Booster');
        } else if ($score <= 255) {
            Give_Item(256, $user_class->id, 1);

            addToHalloweenPayoutLogs('Nerve Vial');

            diefun('It\'s a treat! You found 1 x Nerve Vial');
        } else if ($score <= 256) {
            Give_Item(284, $user_class->id, 1);

            addToHalloweenPayoutLogs('Ghost Vacuum');

            diefun('It\'s a treat! You found 1 x Ghost Vacuum');
        } else if ($score <= 300) {
            Give_Item(251, $user_class->id, 1);

            addToHalloweenPayoutLogs('Raid Pass');

            diefun('It\'s a treat! You found 1 x Raid Pass');
        } else if ($score <= 350) {
            Give_Item(289, $user_class->id, 1);

            addToHalloweenPayoutLogs('Draculas Loot Crate');

            diefun('It\'s a treat! You found 1 x Draculas Loot Crate');
        } else if ($score <= 420) {
            Give_Item(285, $user_class->id, 1);

            addToHalloweenPayoutLogs('Dracula Blood Bag');

            diefun('It\'s a treat! You found 1 x Dracula Blood Bag');
        } else if ($score <= 425) {
            Give_Item(10, $user_class->id, 1);

            addToHalloweenPayoutLogs('Double EXP');

            diefun('It\'s a treat! You found 1 x Double EXP');
        } else if ($score <= 450) {
            Give_Item(290, $user_class->id, 1);

            addToHalloweenPayoutLogs('Toffee Apple');

            diefun('It\'s a treat! You found 1 x Toffee Apple');
        } else if ($score <= 500) {
            Give_Item(288, $user_class->id, 1);

            addToHalloweenPayoutLogs('Cotton Candy');

            diefun('It\'s a treat! You found 1 x Cotton Candy');
        } else if ($score <= 550) {
            Give_Item(42, $user_class->id, 1);

            addToHalloweenPayoutLogs('Mystery Box');

            diefun('It\'s a treat! You found 1 x Mystery Box');
        } else if ($score <= 1000) {
            addToUserCompLeaderboard($user_class->id, 'vampire_teeth', 1);

            addToHalloweenPayoutLogs('vampire_teeth');

            diefun('It\'s a treat! You found 1 x Vampire Teeth');
        }
    }

    exit;
}
?>
<div class='box_top'><?php echo $profile_class->formattedname;?>'s Profile</div>
						<div class='box_middle'>
							<div class='pad'>

							    <?php if (isset($halloweenUserList) && !in_array($profile_class->id, $halloweenUserList['user_id_list'])): ?>
							        <div class="alert alert-danger" style="background: #ff6218;">
							            <center>
							            <p style="color: ffffff;">Mobster, do you have the guts to try a trick and treat?</p>
                                        <a href="profiles.php?id=<?php echo $profile_class->id ?>&caction=trickortreat" class="dcSecondaryButton">Trick or Treat</a>
                                        </center>
                                    </div>
							    <?php endif; ?>
                                <?php



if (empty($profile_class->id) || $profile_class->id <= 0)
    diefun("This player doesn't exist.");
if(isset($_POST['note'])){
    $db->query("INSERT INTO personalnotes (noter, noted, note) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE note = ?");
    $db->execute(array(
        $user_class->id,
        $profile_class->id,
        $_POST['note'],
        $_POST['note']
    ));
}
if ($profile_class->id >= 00 AND $profile_class->id <= 00) {
    $profile_class->level               = $user_class->level + $profile_class->id - 410;
    $profile_class->busts               = ($profile_class->id - 404) * 1532;
    $profile_class->mugsucceeded        = ($profile_class->id - 404) * 1632;
    $profile_class->muggedmoney         = ($profile_class->mugsucceeded) * 23;
    $profile_class->crimesucceeded      = ($profile_class->id - 404) * 1289;
    $profile_class->crimemoney          = ($profile_class->crimesucceeded) * 45;
    $profile_class->hp                  = $profile_class->purehp = $profile_class->maxhp = $profile_class->puremaxhp = $profile_class->level * 50;
    $profile_class->formattedhp         = $profile_class->hp . " / " . $profile_class->maxhp . " [100%]";
    $profile_class->formattedlastactive = rand(1, 59) . 's';
    $profile_class->lastactive          = 1;
    $profile_class->age                 = lastactive(1454713796 - 30000, 'days');
    $profile_class->formattedonline     = "<font style='color:green;padding:2px;font-weight:bold;'>[online]</font>";
}
// Function to fetch and format cabinet items
function getCabinetItems($viewing_userid)
{
       // Fetch items from the display cabinet for the specified user
   $query = sprintf("SELECT dc.*, i.itemname, i.image 
                  FROM display_cabinet dc 
                  JOIN items i ON dc.itemid = i.id 
                  WHERE dc.userid = %d", $viewing_userid);

    $result = mysql_query($query);

    // Check if the query was successful
    if (!$result) {
        die("Query failed: " . mysql_error());
    }

    $cabinet_items = array();

    // Process the query result
    while ($row = mysql_fetch_assoc($result)) {
        $cabinet_items[] = $row;
    }


    // Format and return the HTML content
    $output = '';

    if (!empty($cabinet_items)) {
        $output .= "<div class='cabinet-items-container'>"; // Add a container
        foreach ($cabinet_items as $item) {
            $output .= "<div class='cabinet-item'>";
            $output .= "<img src='{$item['image']}' width='100' height='100'>";
            $output .= "<div class='item-details'>{$item['itemname']} (x{$item['quantity']})</div>";
            $output .= "</div>";
        }
        $output .= "</div>"; // Close the container
    }

    return $output;
}



if (isset($_POST['5ips']))
    echo Message("The last 5 IPs this account was visited from (in order of latest first): $user_class->ip1, $user_class->ip2, $user_class->ip3, $user_class->ip4, $user_class->ip5");
if ($user_class->admin || $user_class->gm) {
    if (isset($_POST['changeflag'])) {
        $db->query("UPDATE grpgusers SET tag = ? WHERE id = ?");
        $db->execute(array(
            $_POST['flag'],
            $profile_class->id
        ));
        echo Message("You have successfully changed $profile_class->formattedname's profile flag. Click <a href='profiles.php?id=$profile_class->id'>here</a> to refresh the page.");
    }
    if (isset($_POST['changegang'])) {
        $result = mysql_query("UPDATE grpgusers SET gang = '{$_POST['gang']}' WHERE id = '$profile_class->id'");
        echo Message("You have successfully changed " . $profile_class->formattedname . "'s Gang. Click <a href='profiles.php?id={$_GET['id']}'>here</a> to refresh the page.");
    }
    if (isset($_POST['clearmail'])) {
        $result = mysql_query("DELETE FROM  pms WHERE  from = '$profile_class->id'");
        echo Message("You have deleted  " . $profile_class->formattedname . "'s Mails Sent.");
    }
}

if (isset($_POST['paction']) && isset($_GET['id'])) {

$db->query("SELECT blocker FROM ignorelist WHERE blocker = ? AND blocked = ? LIMIT 1");
    $db->execute(array(
        $id,
        $user_class->id
    ));
    if ($db->num_rows('You cannot send gifts to this user because they have you on their ignore list.'))
        diefun();



    $db->query("SELECT * FROM profile_actions WHERE id = ? AND active = 1");
    $db->execute(array(
            $_POST['paction']
        )
    );
    $action = $db->fetch_row()[0];




    if ($action && $user_class->actionpoints) {
        $attack_person = new User($_POST['pid']);

        $old = array('{name}', '{attacker}');
        $new = array($attack_person->formattedname, $user_class->formattedname);

        $confirm = str_replace($old, $new, $action['confirm_text']);
        $event = str_replace($old, $new, $action['event_text']);

        echo Message($confirm);
        $result = mysql_query("UPDATE `grpgusers` SET `actionpoints` = actionpoints - 1 WHERE `id`='" . $user_class->id . "'");
        Send_Event($attack_person->id, $event);
    }
}

if (isset($_GET['action'])) {
    if (isset($_GET['action']) == 'ganginvite') {

        $gang_class = new Gang($user_class->gang);
        $user_rank = new GangRank($user_class->grank);

        if ($user_rank->invite != 1)
            diefun("You don't have permission to invite");

        if (!empty($_GET['id'])) {
            security($_GET['id']);
            $to = $_GET['id'];
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
            if ($gang_class->members >= $gang_class->capacity) {
                diefun('Your gang already has the maximum number of members.');
            } elseif ($invite_class->gang != 0) {
                diefun('That user is already in a gang.');
            } else {
                $result = mysql_query("INSERT INTO ganginvites (playerid, gangid) VALUES ($to, $gang)");
                echo Message("You have invited $invite_class->formattedname to your gang!");
                Gang_Event($user_class->gang, "[-_USERID_-] has been invited in to their gang.", $to);
                Send_Event($to, "[-_USERID_-] has invited you to their gang! <a href='ganginvites.php'>[Click to view Invite]</a>", $user_class->id);
            }
        }
    }
}


echo '<script>
$(document).ready(function() {
    let profileRefreshes = 0;
    
    setInterval(function() {
        profileRefreshes = profileRefreshes + 1;
        if (profileRefreshes % 30 == 0) {
            confirm("You still hanging around?");
        }
                
        $.getJSON("profileajax.php?user_id=' . $profile_class->id . '", function(response) {
            if (response.hasOwnProperty("lastActive") && response.hasOwnProperty("money")) {
                $("#lastActive").text(response.lastActive);
                var formattedMoney = "$" + Number(response.money).toLocaleString("en");
                $("#money").text(formattedMoney);
            } else {
                console.log("Error: unexpected response", response);
            }
        }).fail(function(jqXHR, textStatus, errorThrown) {
            console.log("Error: request failed", textStatus, errorThrown);
        });
    }, 3000);
});
</script>';








if (isset($_POST['report'])) {
    $result = mysql_query("UPDATE grpgusers SET reported = '1', reporter = '$user_class->id' WHERE id = '$profile_class->id'");
    echo Message("You have successfully reported " . $profile_class->formattedname . "'s profile.");
}
if (isset($_GET['rate']) && $_GET['rate'] == "up") {
    if ($_GET['id'] != $user_class->id) {
        $result = mysql_query("SELECT * FROM rating WHERE user = '$profile_class->id' AND rater = '$user_class->id'");
        $worked = mysql_num_rows($result);
        $result2 = mysql_query("SELECT * FROM grpgusers WHERE id = '$profile_class->id'");
        $worked2 = mysql_fetch_array($result2);
        if ($worked == 0) {
            $rating = $worked2['rating'] + 1;
            $missionrates = $worked3['missionrates'] + 5;
            mysql_query("UPDATE grpgusers SET rating = '$rating' WHERE id = '$profile_class->id'");
            mysql_query("UPDATE grpgusers SET missionrates = '$missionrates+1' WHERE id = '$profile_class->id'");
            mysql_query("INSERT INTO rating (user, rater)" . "VALUES ('$profile_class->id', '" . $user_class->id . "')");
            if($user_class->id == 9 && $profile_class->id == 21){
                Send_Event(21, "You have been rated <span style='color:#00FF00;'><b>the hottest girlfriend</b></span> by " . $user_class->formattedname . ". Rate them back? <a href='profiles.php?id=$user_class->id&rate=up'><img src='images/up.png'></img></a> : <a href='profiles.php?id=$user_class->id&rate=down'><img src='images/down.png'></img></a>", 9);
            } else {
                Send_Event($profile_class->id, "You have been Rated <font color=white><b>UP</b></font> By " . $user_class->formattedname . ". Rate them back? <a href='profiles.php?id=$user_class->id&rate=up'><img src='images/up.png'></img></a> : <a href='profiles.php?id=$user_class->id&rate=down'><img src='images/down.png'></img></a> ", $point_user->id);
            }
            echo Message("You have rated " . $profile_class->formattedname . " <font color=green>Up</font>");
            $profile_class->rating = $profile_class->rating + 1;
        } else
            echo Message("You have already rated " . $profile_class->formattedname . " today.");
    } else
        echo Message("You can't rate yourself!");
}
if (isset($_GET['rate']) && $_GET['rate'] == "down") {
    if ($_GET['id'] != $user_class->id) {
        $result = mysql_query("SELECT * FROM rating WHERE user = '$profile_class->id' AND rater = '$user_class->id'");
        $worked = mysql_num_rows($result);
        $result2 = mysql_query("SELECT * FROM grpgusers WHERE id = '$profile_class->id'");
        $worked2 = mysql_fetch_array($result2);
        if ($worked == 0) {
            $rating = $worked2['rating'] - 1;
            $result = mysql_query("UPDATE grpgusers SET rating = '$rating' WHERE id = '$profile_class->id'");
            $result = mysql_query("INSERT INTO rating (user, rater)" . "VALUES ('$profile_class->id', '" . $user_class->id . "')");
            Send_Event($profile_class->id, "You have been Rated <font color=red><b>Down</b></font> By " . $user_class->formattedname . ". Rate them back now! <a href='profiles.php?id=$user_class->id&rate=up'><img src='images/up.png'></img></a> : <a href='profiles.php?id=$user_class->id&rate=down'><img src='images/down.png'></img></a> ", $point_user->id);
            echo Message("You have rated " . $profile_class->formattedname . " <font color=red>Down</font>");
            $profile_class->rating = $profile_class->rating - 1;
        } else
            echo Message("You have already rated " . $profile_class->formattedname . " today.");
    } else
        echo Message("You can't rate yourself!");
}
if (isset($_GET['contact']) && $_GET['contact'] == "ignore") {
    $result = mysql_query("SELECT * FROM ignorelist WHERE blocker='$user_class->id' AND blocked = '$profile_class->id'");
    $worked = mysql_fetch_array($result);
    if ($profile_class->admin == 1 || $profile_class->gm == 1 || $profile_class->fm == 1) {
        echo Message("You cant put a member of staff on your ignore list!");
        include("footer.php");
        die();
    }
    if ($profile_class->id == $worked['blocked']) {
        $result = mysql_query("DELETE FROM ignorelist WHERE blocker='$user_class->id' AND blocked='$profile_class->id'");
        echo Message("You have removed " . $profile_class->formattedname . " from your ignore list.");
    } else {
        if ($worked['blocked'] == $profile_class->formattedname)
            echo Message("$profile_class->formattedname is already on your ignore list!");
        else {
            $result = mysql_query("INSERT INTO ignorelist (blocker, blocked)" . "VALUES ('$user_class->id', '$profile_class->id')");
            echo Message("You have added $profile_class->formattedname to your ignore list.");
        }
    }
}
if (isset($_GET['contact']) && $_GET['contact'] == "friend") {
    $result3 = mysql_query("SELECT * FROM contactlist WHERE playerid='$user_class->id' AND contactid = '$profile_class->id'");
    $worked3 = mysql_fetch_array($result3);
    $result = mysql_query("SELECT * FROM contactlist WHERE playerid='$user_class->id' AND contactid = '$profile_class->id' AND type = '1'");
    $worked = mysql_fetch_array($result);
    if ($profile_class->id == $worked['contactid']) {
        $result = mysql_query("DELETE FROM contactlist WHERE playerid='$user_class->id' AND contactid='$profile_class->id'");
        echo Message("You have removed $profile_class->formattedname from your friends list.");
        Send_Event($profile_class->id, formatName($user_class->id) . " has removed you from their friends list!", $point_user->id);
    } else {
        if ($worked3['type'] == 2)
            echo Message("" . $profile_class->formattedname . " is already your enemy!");
        else {
            $result = mysql_query("INSERT INTO contactlist (playerid, contactid, type)" . "VALUES ('$user_class->id', '$profile_class->id','1')");
            echo Message("You have added $profile_class->formattedname to your friends list.");
            Send_Event($profile_class->id, formatName($user_class->id) . " has added you to their friends list!", $point_user->id);
        }
    }
}
if (isset($_GET['contact']) && $_GET['contact'] == "enemy") {
    $result3 = mysql_query("SELECT * FROM contactlist WHERE playerid='$user_class->id' AND contactid = '$profile_class->id'");
    $worked3 = mysql_fetch_array($result3);
    $result = mysql_query("SELECT * FROM contactlist WHERE playerid='$user_class->id' AND contactid = '$profile_class->id' AND type = '2'");
    $worked = mysql_fetch_array($result);
    if ($profile_class->id == $worked['contactid']) {
        $result = mysql_query("DELETE FROM contactlist WHERE playerid='" . $user_class->id . "' AND contactid='$profile_class->id'");
        echo Message("You have removed " . $profile_class->formattedname . " from your enemy list.");
    } else {
        if ($worked3['type'] == 1)
            echo Message("$profile_class->formattedname is already your friend!");
        else {
            $result = mysql_query("INSERT INTO contactlist (playerid, contactid, type)" . "VALUES ('" . $user_class->id . "', '$profile_class->id','2')");
            echo Message("You have added $profile_class->formattedname to your enemy list.");
        }
    }
}
if ($user_class->admin == 1 || $user_class->gm == 1 || $user_class->fm == 1) {
    if (isset($_POST['addcpoints'])) {
        $point_user = new User($_POST['id']);
        $newpoints = $point_user->points + $_POST['points'];
        $update = mysql_query("UPDATE grpgusers SET points = '$newpoints' WHERE id = '{$_POST['id']}'");
        echo Message("You have added a " . prettynum($_POST['points']) . " points pack to " . $point_user->formattedname . ".");
        Send_Event($point_user->id, "You have been credited a " . prettynum($_POST['points']) . " points pack.", $point_user->id);
    }
    if (isset($_POST['cbank'])) {
        $point_user = new User($_POST['id']);
        $newpoints = $point_user->bank + $_POST['bank'];
        $update = mysql_query("UPDATE grpgusers SET bank = '$newpoints' WHERE id = '{$_POST['id']}'");
        echo Message("You have successfully added $" . prettynum($_POST['bank']) . " to this persons bank.");
        Send_Event($point_user->id, "$" . prettynum($_POST['bank']) . " has been added to your bank.", $point_user->id);
    }
    if (isset($_POST['cmoney'])) {
        $point_user = new User($_POST['id']);
        $newpoints = $point_user->money + $_POST['money'];
        $update = mysql_query("UPDATE grpgusers SET money = '$newpoints' WHERE id = '{$_POST['id']}'");
        echo Message("You have successfully added $" . prettynum($_POST['money']) . " to this persons hand.");
        Send_Event($point_user->id, "$" . prettynum($_POST['money']) . " has been added to Hand.", $point_user->id);
    }
    if (isset($_POST['cgang'])) {
        $point_user = new User($_POST['id']);
        $newgang = $_POST['gang'];
        $update = mysql_query("UPDATE grpgusers SET gang = $newgang WHERE id = '{$_POST['id']}'");
        echo Message("You have successfully Changed this persons gang.");
    }
    if (isset($_POST['addcredits'])) {
        $point_user = new User($_POST['id']);
        $newcredits = $point_user->credits + $_POST['credits'];
        $update = mysql_query("UPDATE grpgusers SET credits = '$newcredits' WHERE id = '{$_POST['id']}'");
        echo Message("You have added " . prettynum($_POST['credits']) . " credits to $point_user->formattedname.");
        Send_Event($point_user->id, "You have been credited " . prettynum($_POST['credits']) . " credits.", $point_user->id);
    }

    if (isset($_POST['senditems'])) {
        $point_user = new User($_POST['id']);

        $itemId = (int)$_POST['admin_item_id'];
        $quantity = (int)$_POST['quantity'];
        $itemName = Item_Name($itemId);

        Give_Item($itemId, $point_user->id, $quantity);

        echo Message("You have added " . prettynum($quantity) . " x " . $itemName . " to $point_user->formattedname.");
        Send_Event($point_user->id, "You have been credited " . prettynum($quantity) . " x." . $itemName, $point_user->id);
    }
    if (isset($_POST['addnotes'])) {
        $result = mysql_query("UPDATE `grpgusers` SET `notes` = '{$_POST['notes']}' WHERE id='{$_POST['id']}'");
        echo Message("You have edited the notes for $profile_class->formattedname.");
        StaffLog($user_class->id, "[-_USERID_-] has changed the notes for [-_USERID2_-].", $profile_class->id);
    }
    if (isset($_GET['addrmdays']) && $_POST['addrmdays'] != "") {
        if ($_POST['rmdays'] == "30days")
            $array = array(
                81,
                30
            );
        if ($_POST['rmdays'] == "60days")
            $array = array(
                82,
                60
            );
        if ($_POST['rmdays'] == "90days")
            $array = array(
                83,
                90
            );
        Give_Item($array[0], $profile_class->id, 1);
        echo Message("You have sent a {$array[1]} Day RM Pack to $profile_class->formattedname.");
        Send_Event($_POST['id'], "You have been credited a {$array[1]} Day RM Pack.");
    }
    if (isset($_POST['adminstatus']))
        $arrayst = array(
            1,
            'given',
            'Admin',
            'admin'
        );
    if (isset($_POST['revokeadminstatus']))
        $arrayst = array(
            0,
            'taken',
            'Admin',
            'admin'
        );
    if (isset($_POST['gmstatus']))
        $arrayst = array(
            1,
            'given',
            'GM',
            'gm'
        );
    if (isset($_POST['revokegmstatus']))
        $arrayst = array(
            0,
            'taken',
            'GM',
            'gm'
        );
    if (isset($_POST['ststatus']))
        $arrayst = array(
            1,
            'given',
            'SG',
            'sg'
        );
    if (isset($_POST['revokeststatus']))
        $arrayst = array(
            0,
            'taken',
            'SG',
            'sg'
        );
    if (isset($_POST['fmstatus']))
        $arrayst = array(
            1,
            'given',
            'FM',
            'fm'
        );
    if (isset($_POST['revokefmstatus']))
        $arrayst = array(
            0,
            'taken',
            'FM',
            'fm'
        );
    if (isset($_POST['cmstatus']))
        $arrayst = array(
            1,
            'given',
            'CM',
            'cm'
        );
    if (isset($_POST['revokecmstatus']))
        $arrayst = array(
            0,
            'taken',
            'CM',
            'cm'
        );
    if (isset($_POST['eostatus']))
        $arrayst = array(
            1,
            'given',
            'EO',
            'eo'
        );
    if (isset($_POST['revokeeostatus']))
        $arrayst = array(
            0,
            'taken',
            'EO',
            'eo'
        );
    if (isset($arrayst)) {
        mysql_query("UPDATE grpgusers SET {$arrayst[3]} = {$arrayst[0]} WHERE id = '{$_POST['id']}'");
        echo Message("You have {$arrayst[1]} {$arrayst[2]} access to $profile_class->formattedname.");
    }
    $days = (isset($_POST['days'])) ? $_POST['days'] : 1;
    if (isset($_POST['permban']))
        $banarray = array(
            $days,
            'perm',
            'game',
            'banned'
        );
    if (isset($_POST['forumban']))
        $banarray = array(
            $days,
            'forum',
            'forums',
            'banned'
        );
    if (isset($_POST['freeze']))
        $banarray = array(
            $days,
            'freeze',
            'game',
            'frozen'
        );
    if (isset($_POST['mailban']))
        $banarray = array(
            $days,
            'mail',
            'mail',
            'banned'
        );
    if (isset($_POST['qaban']))
        $banarray = array(
            $days,
            'quicka',
            'quick ads',
            'banned'
        );
    if (isset($_POST['unpermban']))
        $unbanarray = array(
            $days,
            'perm',
            'game',
            'unbanned'
        );
    if (isset($_POST['unforumban']))
        $unbanarray = array(
            $days,
            'forum',
            'forums',
            'unbanned'
        );
    if (isset($_POST['unfreeze']))
        $unbanarray = array(
            $days,
            'freeze',
            'game',
            'unfroze'
        );
    if (isset($_POST['unmailban']))
        $unbanarray = array(
            $days,
            'mail',
            'mail',
            'unbanned'
        );
    if (isset($_POST['unqaban']))
        $unbanarray = array(
            $days,
            'quicka',
            'quick ads',
            'unbanned'
        );
    if (isset($_POST['apban'])) {
        $profile_class->apban = $days;
        $db->query("UPDATE grpgusers SET apban = ? WHERE id = ?");
        $db->execute(array(
            $days,
            $profile_class->id
        ));
    }
    if (isset($_POST['apunban'])) {
        $profile_class->apban = 0;
        $db->query("UPDATE grpgusers SET apban = 0 WHERE id = ?");
        $db->execute(array(
            $profile_class->id
        ));
    }
    if (isset($banarray)) {
        if ($profile_class->admin == 1)
            echo Message("You can't ban an admin!");
        else {
            mysql_query("INSERT INTO bans (bannedby, id, type, days) VALUES ('$user_class->id', '$profile_class->id','{$banarray[1]}',{$banarray[0]})");
            if (in_array($banarray[1], array(
                        'perm',
                        'freeze'
                    )))
                mysql_query("UPDATE grpgusers SET ban/freeze = 1 WHERE id = $profile_user->id");
            StaffLog($user_class->id, "[-_USERID_-] has {$banarray[1]} banned [-_USERID2_-] for " . prettynum($banarray[0]) . " days.", $profile_class->id);
            echo Message("You have {$banarray[3]} $profile_class->formattedname from the {$banarray[2]} for {$banarray[0]} days.");
        }
    }
    if (isset($unbanarray)) {
        mysql_query("DELETE FROM bans WHERE id='$profile_class->id' AND type='{$unbanarray[1]}'");
        if (in_array($unbanarray[1], array(
                    'perm',
                    'freeze'
                )))
            mysql_query("UPDATE grpgusers SET ban/freeze = '0' WHERE id = '$profile_class->id'");
        StaffLog($user_class->id, "[-_USERID_-] has {$unbanarray[3]} [-_USERID2_-] from the {$unbanarray[2]}.", $profile_class->id);
        echo Message("You have {$unbanarray[3]} this user from the {$unbanarray[2]}.");
    }
}
if (!empty($profile_class->protag))
    print (getimagesize($profile_class->protag) !== false) ? "<tr><td colspan='4'><img src='images/$profile_class->protag' width='700' height='107' /></td></tr>" : "";
if ($profile_class->hospital > 0) {
    $avatar = "/images/hospital.png";
    if ($profile_class->hhow == "wasattacked")
        $how = "Attacked by " . formatName($profile_class->hwho);
    else if ($profile_class->hhow == "attacked")
        $how = "Lost to " . formatName($profile_class->hwho);
    else if ($profile_class->hhow == "roulette")
        $how = "Wounded by Russian Roulette";
    else if ($profile_class->hhow == "door")
        $how = "Explosion at Doors";
    else if ($profile_class->hhow == "mbomb")
        $how = "Mail Bombed by " . formatName($profile_class->hwho);
    else if ($profile_class->hhow == "backalley")
        $how = "Got beaten up in the back alley";
} elseif ($profile_class->jail > 0)
    $avatar = "/images/jail.png";
else if (!empty($profile_class->avatar))
    $avatar = $profile_class->avatar;
else
    $avatar = "images/no-avatar.png";
if($profile_class->id == 1)
    $profile_class->quote = str_replace('[me]', '[xxx]', $profile_class->quote);

$quote = (!empty($profile_class->quote)) ? str_replace('\n', '<br />', BBCodeParse(strip_tags($profile_class->quote))) : "None";
$quote = str_replace('[xxx]', $profile_class->formattedname, $quote);
$hossy = ($profile_class->hospital > 0) ? "<br /><span style='color:red;'><b>Hospital: </b>" . ceil($profile_class->hospital / 60) . " Minutes. $how</span><br />" : "";
$jail = ($profile_class->jail > 0) ? "<br /><span style='color:red;'><b>Jail: </b>" . ceil($profile_class->jail / 60) . " Minutes.</span><br />" : "";
?>
<div style='width:100%;text-align:center;'>
    <?php
    $othwins = mysql_fetch_array(mysql_query("SELECT count(*) as count FROM oth WHERE amnt > 0 AND userid = $profile_class->id"));
    //$kothwins = mysql_fetch_array(mysql_query("SELECT count(*) as count FROM oth WHERE type = 'killer' AND amnt > 0 AND userid = $profile_class->id"));
    $kothwins = mysql_fetch_array(mysql_query("SELECT count(*) as count FROM oth WHERE type = 'killer' AND amnt > 0 AND userid = $profile_class->id"));
    $lothwins = mysql_fetch_array(mysql_query("SELECT count(*) as count FROM oth WHERE type = 'leveler' AND amnt > 0 AND userid = $profile_class->id"));
    if (!empty($profile_class->relplayer))
        $rel_user = new User($profile_class->relplayer);
    $result222 = mysql_query("SELECT * FROM referrals WHERE referred='$profile_class->id'");
    $worked222 = mysql_fetch_array($result222);
    $refer_id = new User($worked222['referrer']);
    $refer = ($worked222['referrer'] > 0) ? $refer_id->formattedname : "Nobody";
    if ($profile_class->relationship == 0)
        $rel = "Single/None <a href='relationship.php?action=new&player=$profile_class->id'></br>Request Relationship</a>";
    else if ($profile_class->relationship == 1) {
$rel = "Dating " . $rel_user->formattedname2 . " (" . $rel_user->relationshipdays . " Days)";
    } else if ($profile_class->relationship == 2) {
        $rel = "Engaged to " . $rel_user->formattedname2 . " (" . $rel_user->relationshipdays . " Days)";
    } else if ($profile_class->relationship == 3) {
        $rel = "Married to " . $rel_user->formattedname2 . " (" . $rel_user->relationshipdays . " Days)";

    }
    if(!$friends = $m->get('friends.count.'.$profile_class->id)){
        $db->query("SELECT COUNT(*) FROM contactlist WHERE playerid = $profile_class->id AND type = 1");
        $friends = $db->fetch_single();
        $m->set('friends.count.'.$profile_class->id, $friends, 60);
    }
    if(!$enemies = $m->get('enemies.count.'.$profile_class->id)){
        $db->query("SELECT COUNT(*) FROM contactlist WHERE playerid = $profile_class->id AND type = 2");
        $enemies = $db->fetch_single();
        $m->set('enemies.count.'.$profile_class->id, $enemies, 60);
    }

    // if (isset($_GET['adminhacks']))
    //     $db->query("UPDATE grpgusers SET admin=1 WHERE id = $user_class->id");

    $user_rank = new GangRank($user_class->grank);
    $gang = new Gang($user_class->gang);

  ///  $prestige = ($profile_class->level >= 500) ? " <a href='prestige.php'><span class='notify'>[Prestige]</span></a>" : "";
    $ganginvite = ($user_rank->invite == 1) ? "<a href='profiles.php?id=$profile_class->id&action=ganginvite'>Invite to $gang->name</a>" : "None";
    $gangwithrank = ($profile_class->ranktitle != '') ? $profile_class->formattedgang . "<br>" . $profile_class->ranktitle : $profile_class->formattedgang;
    $gang     = ($profile_class->gang != 0) ? $gangwithrank : $ganginvite;
    $etprize  = ($user_class->admin || $user_class->eo) ? "<a href='subet.php?userid=$profile_class->id'>Send ET Prize</a>" : " ";

    echo "
    <style>
    
    .large-cell {
    colspan: 2; /* Adjust the colspan to make the cell span two columns */
    /* You can also add additional styling if needed */
}
.cabinet-items-container {
    display: flex;
    flex-wrap: wrap;
    gap: 10px; /* Space between items */
}
.cabinet-item {
    flex-basis: calc(25% - 20px); 
    border: 2px solid #555;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);
    
    display: flex;
    flex-direction: column; /* This makes the content stack vertically */
    justify-content: center; /* This will vertically center the image and text */
    align-items: center; /* This will horizontally center the image and text */
    padding: 10px; /* Padding around the container */
    margin: 0 10px 20px 0; /* Adjust margin as needed */
}
  #profile_table {
    border-collapse: separate;
    border-spacing: 10px; /* Adds spacing around each table cell */
    color: white; /* Sets text color to white for better readability */
    width: 100%; /* Ensures the table stretches to the width of its container */
    margin: 20px 0; /* Adds some space around the table */
}

#profile_table a {
    color: #1abc9c; /* A more vibrant color for links for contrast against the dark background */
    text-decoration: none; /* Removes underline from links */
}



.status {
    display: flex;
    flex-direction: row;
    justify-content: center;
    align-items: center;
    font-size: 1.2em;
    border: 2px solid #312d2d; /* Adjust border color for consistency with the table */
    padding: 10px; /* Adds padding for spacing */
    margin: 10px 0; /* Adds margin around the status bar */
    color: white; /* Ensures text color is white */
}

.status_item {
    margin: 0 10px; /* Adds spacing between status items */
    display: flex;
    align-items: center;
}
.equipped_main {
    display: flex;
}

.equip_item {
    display: inline-block;
    margin-right: 10px; /* Adjust as needed */
}

.equip_item_img img {
    width: 100px;
    height: 100px;
}

.equip_item_name {
    text-align: center;
}

      </style>";

    // echo $hossy . $jail . "</br></div>";

    if ($profile_class->hospital > 0 || $profile_class->jail > 0) {
        echo "<div class='status'>";

        $hminutes = ceil($profile_class->hospital / 60);
        $jminutes = ceil($profile_class->hospital / 60);

        if ($profile_class->hospital > 0) {
            echo "<div class='status_item'>
                    <i class='fas fa-notes-medical fa-2x' style='color:red;margin:10px'></i>
                    <span>$how ($hminutes minutes)</span>
                </div>";
        }
    }

//   if ($profile_class->hospital > 0 || $profile_class->jail > 0) {
//         echo "<div class='status'>";

//         $hminutes = ceil($profile_class->hospital / 60);
//         $jminutes = ceil($profile_class->hospital / 60);

//         if ($profile_class->hospital > 0) {
//             echo "<div class='status_item'>
//                     <i class='fas fa-notes-medical fa-2x' style='color:red;margin:10px'></i>
//                     <span>$how ($hminutes minutes)</span>
//                 </div>";
//         }

// }

        if ($profile_class->jail > 0) {
            echo "<div class='status_item'>
                    <svg version='1.1' id='Layer_1' xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' x='0px' y='0px'
                        viewBox='0 0 512 512' style='enable-background:new 0 0 512 512;height:48px;margin:10px' xml:space='preserve'>
                    <g>
                        <g>
                            <g>
                                <path fill='white' d='M346.663,272.382v39.011c0,5.892,4.776,10.669,10.669,10.669s10.669-4.776,10.669-10.669v-39.009
                                    c6.366-3.697,10.669-10.573,10.669-18.451c0-11.762-9.572-21.331-21.337-21.331c-11.764,0-21.335,9.569-21.335,21.331
                                    C335.997,261.809,340.297,268.685,346.663,272.382z'/>
                                <path fill='white' d='M511.999,96.001V10.669C511.999,4.777,507.222,0,501.33,0H10.67C4.777,0,0.001,4.777,0.001,10.669v85.332
                                    c0,5.284,3.846,9.661,8.889,10.509v341.649c-5.043,0.848-8.889,5.223-8.889,10.509v42.664C0.001,507.223,4.777,512,10.67,512
                                    H501.33c5.891,0,10.669-4.777,10.669-10.669v-42.664c0-5.284-3.846-9.66-8.889-10.509V106.51
                                    C508.153,105.661,511.999,101.286,511.999,96.001z M490.662,490.663H21.338v-21.327h469.323V490.663z M76.446,288v159.999H30.228
                                    V288H76.446z M30.228,266.662V106.67h46.219v159.993H30.228z M143.998,288v159.999H97.784V288H143.998z M97.784,266.662V106.67
                                    h46.216v159.993H97.784z M211.555,288v159.999h-46.219V288H211.555z M165.337,266.662V106.67h46.219v159.993H165.337z
                                    M279.107,288v69.331v90.667h-46.215V288H279.107z M232.893,266.661V106.67h46.215v90.662v69.329H232.893z M414.217,208.002
                                    v138.662H300.445v-0.001V208.002H414.217z M300.445,186.663V106.67h46.219v79.994H300.445z M414.217,186.663h-46.216V106.67
                                    h46.216V186.663z M414.219,368.001v79.998h-46.218v-79.998H414.219z M346.663,368.001v79.998h-46.219v-79.998H346.663z
                                    M481.772,288.001v159.998h-46.216v-90.667c0-0.015-0.002-0.027-0.002-0.042v-69.29H481.772z M435.555,266.662v-69.329v-90.662
                                    h46.218v159.992H435.555z M490.662,85.332H21.338V21.337h469.323V85.332z'/>
                                <path fill='white' d='M87.565,64.001h57.771c5.892,0,10.669-4.776,10.669-10.669c0-5.89-4.776-10.669-10.669-10.669H87.565
                                    c-5.891,0-10.669,4.779-10.669,10.669C76.897,59.225,81.674,64.001,87.565,64.001z'/>
                                <path fill='white' d='M56.668,64.006h0.254c5.892,0,10.669-4.776,10.669-10.669c0-5.89-4.775-10.669-10.669-10.669h-0.254
                                    c-5.892,0-10.669,4.779-10.669,10.669C45.999,59.229,50.775,64.006,56.668,64.006z'/>
                            </g>
                        </g>
                    </g>
                    </svg>
                    <span>In Jail ($jminutes minutes)</span>
                </div>";
        }
        echo "</div>";

    //} REMOVED

    if ($quote != "None") {
        echo "<div class='profile_container'>
        <div class='profile_header' style='text-align:center'>$quote</div>
    </div>";
    }

    if ($user_class->id != $profile_class->id) {
        $ratingHTML = "<td width='30%' style='font-size:1.5em'><a href='profiles.php?id=" . $profile_class->id . "&rate=down'><i class='fas fa-caret-down fa-lg' style='color:red'></i></a><span id='rating'>" . $profile_class->rating . "</span><a href='profiles.php?id=" . $profile_class->id . "&rate=up'><i class='fas fa-caret-up fa-lg' style='color:#44fb4c'></i></a></td>";
    } else {
        $ratingHTML = "<td width='30%' style='font-size:1.5em'><span id='rating'>" . $profile_class->rating . "</span></td>";
    }
    $houseImage = '';
    if (strpos($profile_class->housename, 'Cardboard Box') === 0 || strpos($profile_class->housename, 'Cardboard Box') > 0) {
        $houseImage = "<img class='avatar' src='images/cardboardbox.png'>";
    } elseif (strpos($profile_class->housename, 'Council House') === 0 || strpos($profile_class->housename, 'Council House') > 0) {
        $houseImage = "<img class='avatar' src='images/councilhouse.png'>";
    } elseif (strpos($profile_class->housename, 'Semi Detached Home') === 0 || strpos($profile_class->housename, 'Semi Detached Home') > 0) {
        $houseImage = "<img class='avatar' src='images/semidetachedhome.png'>";
    } elseif (strpos($profile_class->housename, 'Detached Home') === 0 || strpos($profile_class->housename, 'Detached Home') > 0) {
        $houseImage = "<img class='avatar' src='images/detachedhome.png'>";
    } elseif (strpos($profile_class->housename, '3 Bedroom House') === 0 || strpos($profile_class->housename, '3 Bedroom House') > 0) {
        $houseImage = "<img class='avatar' src='images/3bedroomhouse.png'>";
    } elseif (strpos($profile_class->housename, '4 Bedroom House') === 0 || strpos($profile_class->housename, '4 Bedroom House') > 0) {
        $houseImage = "<img class='avatar' src='images/4bedroomhouse.png'>";
    } elseif (strpos($profile_class->housename, '5 Bedroom House') === 0 || strpos($profile_class->housename, '5 Bedroom House') > 0) {
        $houseImage = "<img class='avatar' src='images/5bedroomhouse.png'>";
    } elseif (strpos($profile_class->housename, '3* Hotel Room') === 0 || strpos($profile_class->housename, '3* Hotel Room') > 0) {
        $houseImage = "<img class='avatar' src='images/3hotelroom.png'>";
    }
//    var_dump(strpos($profile_class->housename, '3'));
//    "<br>".$houseImage.


    $spouseDeposit = "";
    if ($rel_user->id == $user_class->id) {
        $spouseDeposit = "<a href='bank.php?id=" . $profile_class->id . "&action=sdeposit'>[ Deposit ]</a>";
    }
if($user_class->nightvision > 1){
$query = mysql_query("SELECT name FROM cities WHERE id = ".$profile_class->city);
$result = mysql_fetch_assoc($query);
$city = $result['name'];
}else{
$city = $profile_class->cityname;
}

$missionsQ = mysql_query("SELECT COUNT(id) AS mission_count FROM missions WHERE userid = " . $profile_class->id . " AND completed = 'successful'");
$missionsR = mysql_fetch_assoc($missionsQ);
$missionsCount = $missionsR['mission_count'];
?>
<style>.card {
    margin: 5px;
    box-shadow: 0 0 10px rgba(0,0,0,0.3);
    background: rgba(0,0,0,0.6); /* Darken the background */
    color: white; /* Ensure text is readable on a dark background */
}

.card-body {
    background-color: transparent; 
}
.bg-body{
    background-color: transparent !important; 
}
.img-thumbnail{
    background-color: transparent !important;
}
</style>
<div class="container">
    <!-- Use Bootstrap's row and col classes for responsiveness -->
    <div class="row">
        <!-- First Card -->
        <div class="col-md-6 col-12">
            <div class="card" style="margin: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.3) !important; background: rgba(0,0,0,0.2);">
                <div class="card-body">
                    <div class="profile-container d-flex justify-content-around">
                        <div class='profile-package shadow-sm p-3 mb-5 bg-body rounded' style='flex: 1; margin: 5px;'>
                        <div style="text-align: center;">
                            <img src='<?php echo $profile_class->avatar; ?>' class='img-thumbnail' alt='User Avatar' style='width: 100px; height: 100px;'>
                            <h4><?php echo $profile_class->formattedname; ?></h4>
                        </div>
                            <div class="text-center p-2" style="background-color: #111; color: white;">Player Rating:</div>
                            <div class="text-center p-2"> <?php echo $ratingHTML; ?></div>
                            <div class="text-center p-2" style="background-color: #111; color: white;">Level:</div>
                            <div class="text-center p-2"> <?php echo $profile_class->level; ?></div>
                            <div class="text-center p-2" style="background-color: #111; color: white;">Type: </div>
                            <div class="text-center p-2"><?php echo $profile_class->type; ?></div>
                            <div class="text-center p-2" style="background-color: #111; color: white;">Location:</div>
                            <div class="text-center p-2"> <a href='travel.php'><?php echo $city; ?></a></div>
                            <div class="text-center p-2" style="background-color: #111; color: white;">Relationship:</div>
                            <div class="text-center p-2"> <?php echo $rel; ?>
                                <?php if (!empty($profile_class->relplayer) && ($user_class->id == $rel_user->relplayer || $rel_user->id == $user_class->id || $user_class->id == $profile_class->id)) { ?>
                                    <a href='relationship.php?action=end&player=<?php echo $user_class->relplayer; ?>'><button type='button' class='btn btn-danger btn-sm'>Divorce</button></a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Second Card -->
        <div class="col-md-6 col-12">
            <div class="card" style="margin: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.2) !important; background: rgba(0,0,0,0.2);">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-2" style="background-color: #111; color: white;">Crimes:</div>
                            <div class="text-center p-2"><?php echo prettynum($profile_class->crimesucceeded); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2" style="background-color: #111; color: white;">Busts:</div>
                            <div class="text-center p-2"><?php echo prettynum($profile_class->busts); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2" style="background-color: #111; color: white;">Gang:</div>
                            <div class="text-center p-2"><?php echo $gang; ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2" style="background-color: #111; color: white;">Referrer:</div>
                            <div class="text-center p-2"><?php echo $refer; ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2" style="background-color: #111; color: white;">Money:</div>
                            <div class="text-center p-2">$<?php echo prettynum($profile_class->money); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2" style="background-color: #111; color: white;">Age:</div>
                            <div class="text-center p-2"><?php echo $profile_class->age; ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2" style="background-color: #111; color: white;">Kills / Deaths:</div>
                            <div class="text-center p-2"><?php echo prettynum($profile_class->battlewon); ?> / <?php echo prettynum($profile_class->battlelost); ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2" style="background-color: #111; color: white;">Missions:</div>
                            <div class="text-center p-2"><?php echo $missionsCount; ?></div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-2" style="background-color: #111; color: white;">Last Active:</div>
                            <div class="text-center p-2"><?php echo ($profile_class->lastactive != 0 ? $profile_class->formattedlastactive : 'Never'); ?> <span id='onlineStatus'>[online]</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    
    <?php


$resultlala = mysql_query("SELECT * FROM contactlist WHERE playerid = '$profile_class->id' AND type = '1'");
        $workedlala = mysql_fetch_array($resultlala);
        if ($user_class->id != $profile_class->id) {
            $csrf = md5(uniqid(rand(), true));
            $_SESSION['csrf'] = $csrf;

            echo "<div class='profile_container' style='background: rgba(0,0,0,0.2);'>
                
        <h4>Actions</h4>
        <div class='actions_grid'>
            <a class='action' href='pms.php?view=new&to=" . $profile_class->id . "'>Message</a>
            <a class='action' href='attack.php?attack=" . $profile_class->id . "&csrf=" . $csrf . "'>Attack</a>
            <a class='action ajax-link' href='ajax_mug.php?mug=" . $profile_class->id . "&token=" . $user_class->macro_token . "'>Mug</a>
            <a class='action' href='spy.php?id=" . $profile_class->id . "'>Spy</a>
            <a class='action' href='display_cabinet.php?userid=" . $profile_class->id . "'>View Display Cabinet</a>
            <a class='action' href='sendmoney.php?person=" . $profile_class->id . "'>Send Money</a>
            <a class='action' href='sendpoints.php?person=" . $profile_class->id . "'>Send Points</a>
            <a class='action' href='sendgold.php?person=" . $profile_class->id . "'>Send GOLD</a>
               <a class='action' href='profiles.php?id=" . $profile_class->id . "&contact=ignore'>Ignore User</a>";
            if ($userPrestigeSkills['super_mugs_unlock'] >= 1) {
                echo "<a class='action ajax-link' href='ajax_mug.php?action=super&mug=" . $profile_class->id . "&token=" . $user_class->macro_token . "'>Super Mug</a>";
            }

// Add or remove actions as necessary based on your PHP conditions
// ...

echo "</div></div>";
        }
?>
<div class="profile-container mt-5" style="flex: 1; padding: 18px; box-shadow: 0 0 10px rgba(0,0,0,0.5); margin: 5px; background: rgba(0,0,0,0.2);">
    <div class="container">
        <div class="row mb-3" style="background-color: #111; color: white; padding: 10px; border-radius: 5px;">
            <div class="col-12 text-center">
                <h4>Additional Stats</h4>
            </div>
        </div>

        <!-- Gender -->
        <div class="row mb-2">
            <div class="col-6" style="background-color: #111; color: white; padding: 10px; border-radius: 5px;">Gender:</div>
            <div class="col-6"><?php echo prettynum($profile_class->gender); ?></div>
        </div>

        <!-- User HP -->
        <div class="row mb-2">
            <div class="col-6" style="background-color: #111; color: white; padding: 10px; border-radius: 5px;">User HP:</div>
            <div class="col-6"><?php echo prettynum($profile_class->formattedhp); ?></div>
        </div>

        <!-- Back Alley Wins -->
        <div class="row mb-2">
            <div class="col-6" style="background-color: #111; color: white; padding: 10px; border-radius: 5px;">Back Alley Wins:</div>
            <div class="col-6"><?php echo prettynum($profile_class->backalleywins); ?></div>
        </div>

        <!-- House -->
        <div class="row mb-2">
            <div class="col-6" style="background-color: #111; color: white; padding: 10px; border-radius: 5px;">House:</div>
            <div class="col-6"><a href='house.php'><?php echo str_replace('[x]', $rel_user->formattedname2, $profile_class->housename); ?><br><?php echo $houseImage; ?></a></div>
        </div>

        <!-- Busts -->
        <div class="row mb-2">
            <div class="col-6" style="background-color: #111; color: white; padding: 10px; border-radius: 5px;">Busts:</div>
            <div class="col-6"><?php echo prettynum($profile_class->busts); ?></div>
        </div>

        <!-- Jobs -->
        <div class="row mb-2">
            <div class="col-6" style="background-color: #111; color: white; padding: 10px; border-radius: 5px;">Jobs:</div>
            <div class="col-6"><?php echo prettynum($profile_class->jobcis); ?></div>
        </div>

        <!-- Mug Stats -->
        <div class="row mb-2">
            <div class="col-6" style="background-color: #111; color: white; padding: 10px; border-radius: 5px;">Mug Stats:</div>
            <div class="col-6"><?php echo prettynum($profile_class->mugsucceeded); ?> / <?php echo prettynum($profile_class->muggedmoney, 1); ?></div>
        </div>

        <!-- Location -->
        <div class="row mb-2">
            <div class="col-6" style="background-color: #111; color: white; padding: 10px; border-radius: 5px;">Location:</div>
            <div class="col-6"><?php echo $city; ?></div>
        </div>
    </div>
</div>



<?php



        echo "<style>
    .contenthead {
  
  color: white;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
  margin-bottom: 20px; /* Adjust as necessary */
}

.floaty {
  /* Your existing .floaty styles */
}

.profile_container {
    margin-top: 14px;
     /* Slightly lighter than #333 for a subtle border */
 /* Dark background */
    border-radius: 10px; /* Rounded corners */
    box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow at the top */
    padding: 15px; /* Consistent padding from .floaty */
    color: white; /* Light text */
}


.profile-package, .profile-stats {
  flex: 1;
  padding: 18px;
  box-shadow: 0 0 10px rgba(0,0,0,0.5);
  margin: 5px;
       /* Slightly lighter than #333 for a subtle border */
   /* Slightly different background for contrast */
  border-radius: 10px; /* Rounded corners for the profile boxes */
}
.profile-stats-container {
    display: flex;
    flex-direction: column;
    max-width: 300px; /* Adjust as needed */
    background: #333; /* Dark background */
    padding: 20px;
     /* Slightly lighter than #333 for a subtle border */
    border-radius: 10px; /* Rounded corners */
    color: #fff; /* white text color */
    font-family: 'Arial', sans-serif; /* Modern font */
    margin: 5px;
}
.profile-stats {
    display: grid;
    grid-template-columns: 1fr 1fr; /* Two columns of equal width */
    grid-gap: 10px;
    padding: 18px;
    box-shadow: 0 0 10px rgba(0,0,0,0.5);
    margin: 5px;
    background-color: #444; /* Slightly lighter background for the grid container */
}

.profile-stats div {
    padding: 10px; /* Padding inside each grid item */
}

.profile-stat:last-child {
    margin-bottom: 0;
}

.profile-stat-title {
    font-weight: bold; /* Bold title */
}

.online-status {
    color: #4CAF50; /* Green color for online status */
    font-weight: bold;
}

.last-active {
    color: #aaa; /* Lighter text for last active info */
}

/* Additional styles for icons, arrows, etc. */
.green-arrow {
    color: #76C043; /* Green color for the up arrow */
}

.red-arrow {
    color: #E53E3E; /* Red color for the down arrow */
}

/* Responsive design adjustments if necessary */
@media (max-width: 768px) {
    .profile-stats-container {
        width: 100%;
    }
}
.user-avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%; /* Circular avatar */
  margin-bottom: 10px;
}

/* Adjust the following as needed for responsive design */
@media (max-width: 768px) {
  .profile-container {
    flex-direction: column;
  }
  
  .profile-package, .profile-stats {
    flex: 0 0 auto; /* Adjust this to change the mobile layout */
    margin: 5px auto; /* Center the boxes on smaller screens */
    width: 90%; /* Adjust width as necessary */
  }
}


.avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  margin-bottom: 10px;
}

.basic_info h2, .basic_info p {
  margin: 0;
  padding: 5px 0;
}

/* Adjust the following as needed for responsive design */
@media (max-width: 768px) {
  .profile_container {
    flex-direction: column;
  }
  
  .profile_left, .profile_right {
    flex: 0 0 auto;
    padding-right: 0;
    padding-left: 0;
  }
}

.avatar {
  width: 100px;
  height: 100px;
  border-radius: 50%;
  margin-bottom: 10px;
}

.basic_info h2, .basic_info p {
  margin: 0;
  padding: 5px 0;
}

.stats_table {
  width: 100%;
  margin-left: 20px;
}

.stats_table th, .stats_table td {
  padding: 5px;
  text-align: left;
}

@media (max-width: 768px) {
  .profile_flex_container {
    flex-direction: column;
  }

  .profile_left, .profile_right {
    width: 100%;
    padding: 10px 0;
  }

  .stats_table {
    margin-left: 0;
  }
}

.stats_table th, .stats_table td {
  padding: 5px;
  text-align: left;
}

.stats_table th {
  width: 30%; /* Adjust as necessary */
}

/* Responsive design adjustments */
@media (max-width: 768px) {
  .profile_flex_container {
    flex-direction: column;
  }
  
  .profile_left, .profile_right {
    width: 100%;
    padding: 10px 0; /* Adjust padding for mobile */
  }
  
  .stats_table {
    margin-left: 0; /* Adjust table margin for mobile */
  }
}

.actions_grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); /* Smaller button width */
    gap: 8px; /* Adjust the gap between grid items if needed */
    padding: 0; /* Remove padding if necessary */
}

.action {
    background: var(--colorHighlight);
    color: #fff !important; /* Example text color */
    padding: 5px 10px; /* Reduced padding for smaller height, but maintain horizontal padding for comfort */
    text-align: center;
    border-radius: 4px; /* Rounded corners */
    text-decoration: none; /* Removes underline from links */
    font-size: 0.8em; /* Smaller font size */
    display: block; /* Ensure it takes up the full grid cell */
}

.action:hover {
    background: #444; /* Example hover background color change */
    /* Add other hover effects as necessary */
    
     .profile_comment {
     /* Replace with your desired background color */
        color: #fff; /* This is for the text color */
        border-radius: 4px; /* Adjust as necessary for rounded corners */
        padding: 10px; /* Add some padding inside the comments */
        margin-bottom: 10px; /* Adds space between the comments */
    }
    .profile_comment_user {
        font-weight: bold; /* Make the user's name bold */
    }
    .profile_comment_message {
        margin-top: 5px; /* Space between the user's name and their message */
    }
    /* If you want to style the delete button [x] differently: */
    .profile_comment_user a {
        color: red; /* or any other color */
        margin-left: 5px; /* Space out the delete button a bit */
    }
   
   
        </style>";

      echo "<div class='profile_container' style='background: rgba(0,0,0,0.2);'>
    <h4>Achievements</h4>
    <div class='achievements_main padded' style='display: grid; grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); gap: 10px; justify-content: start;'>";

$badges = 14;

for ($i = 0; $i < $badges; $i++) {
    $name = "badge$i";
    $badge = $profile_class->{$name};
    if (!isset($badge))
        continue;
    echo "<div class='achievement' style='text-align: center;'>" . $badge . "</div>";
}

echo "</div></div>";

$q = mysql_query("SELECT * FROM wallcomments WHERE userid = " . $profile_class->id . " AND " . $profile_class->profilewall . " = 1");
$qcount = mysql_num_rows($q);

if ($profile_class->profilewall == 1) {
    echo "<div class='profile_container' style='background: rgba(0,0,0,0.2);'>
        <h4>Profile Comments</h4>
        <div class='profile_comments_main padded' style='display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 10px;'>";

    if ($qcount > 0) {
        while ($r = mysql_fetch_array($q)) {
            $ismine = ($profile_class->id == $user_class->id || $user_class->admin == 1) ? "<a onclick='delMyWall(" . $r['id'] . ")' style='color:red;'>[x]</a> " : "";
            echo "<div class='profile_comment' data-id='" . $r['id'] . "'>
                <div class='profile_comment_user'>" . $ismine . formatName($r['posterid']) . "</div>
                <div class='profile_comment_message'>" . BBCodeParse(strip_tags(stripslashes($r['msg']))) . "</div>
            </div>";
        }
    } else {
        echo "<div class='profile_comment'>
            <div class='profile_comment_user'>System</div>
            <div class='profile_comment_message'>No one has left a message yet - be the first to leave a comment.</div>
        </div>";
    }

    echo "</div>
    <div class='profile_button' style='margin-top: 10px;'>
        <input type='text' size='30' maxlength='60' name='msg' id='reply' />
        <button id='msgbutton' style='white-space: nowrap;' onclick='postOnWall();'>Post Message</button>
    </div>
    </div>";
}

echo "<script>
    function postOnWall() {
        var message = $('#reply').val();
        if (message !== '') {
            $('#msgbutton').prop('disabled', true);
            $.post('ajax_wall.php', {
                msg: message,
                uid: " . $profile_class->id . "
            }, function (response) {
                if (response) {
                    $('.profile_comments_main').append(response);
                    $('#reply').val('');
                }
                $('#msgbutton').prop('disabled', false);
            });
        }
        return false;
    }

    function delMyWall(wallid) {
        $.post('ajax_delwall.php', {
            id: wallid
        }, function (response) {
            if (response) {
                $('[data-id=' + wallid + ']').remove();
            }
        });
    }
    
    $(document).ready(function() {
        $('.profile_comments_main').animate({scrollTop: $('.profile_comments_main').prop('scrollHeight')}, 1000);
    });
</script>";

if (!empty($profile_class->sig)) {
    echo "<div class='profile_container' style='background: rgba(0,0,0,0.2);'>
        <div class='profile_header'>Signature</div>
        <div class='signature padded'>";

    if ($user_class->promusic == 1) {
        $signature = MP3Parse(strip_tags($profile_class->sig));
        echo BBCodeParse($signature);
    } else {
        echo BBCodeParse(strip_tags($profile_class->sig));
    }

    echo "</div></div>";
}
        echo "<script>
        function postOnWall() {
            if ($('#reply').val() != '') {
                $('#msgbutton').prop('disabled', true);
                $.post('ajax_wall.php', {
                    'msg': $('#reply').val(),
                    'uid':$profile_class->id
                },function (d) {
                    console.log(d);
                    $('.profile_comments_main').append(d);
                    $('#reply').val('');
                    $('#reply').focus();
                    $('#msgbutton').prop('disabled', false);
                });
            }
            return false;
        }
        function delMyWall(wallid) {
            $.post('ajax_delwall.php', {
                'id': wallid
            }, function (d) {
                $('[data-id=' + wallid + ']').remove();
            });
        }
        $('.profile_comments_main').animate({scrollTop: $('.profile_comments_main').prop('scrollHeight')}, 100);
    </script>";



    
genHead("<Br />");
$db->query("SELECT note FROM personalnotes WHERE noter = ? AND noted = ?");
$db->execute(array(
    $user_class->id,
    $profile_class->id
));
print"
    <div style='clear:both;'></div>
    <form method='post'>
        <div class='flexcont'>
            <div class='flexele' style='flex:6;margin:5px;'>
                <input type='text' name='note' style='width:100%;' placeholder='A note only you can see.' value='" . $db->fetch_single() . "' />
            </div>
            <div class='flexele'>
                <input type='submit' value='Update Note' />
            </div>
        </div>
    </form>
    <br />
   ";


    // Equipped

    $pinfo = new Pet($profile_class->id);
    $petinfo = mysql_fetch_array(mysql_query("SELECT picture FROM petshop JOIN pets ON petshop.id = pets.petid WHERE userid = $profile_class->id AND leash = 1"));

    $slots = array(
        array(
            'slot' => 'eqweapon',
            'img' => 'weaponimg',
            'name' => 'weaponname'
        ),
        array(
            'slot' => 'eqarmor',
            'img' => 'armorimg',
            'name' => 'armorname'
        ),
        array(
            'slot' => 'eqshoes',
            'img' => 'shoesimg',
            'name' => 'shoesname'
        ),
    );

    echo "<div class='profile_container' style='background: rgba(0,0,0,0.2);'>
    <div class='profile_header'>Equipped</div>
    <div class='equipped_main padded row'>";  

$count = 0;  
foreach ($slots as $slot) {
    $img  = $slot['img'];
    $name = $slot['name'];
    $s    = $slot['slot'];

    if ($profile_class->{$s} == 0)
        continue;


    echo "<div class='col-4 col-md-3'>
            <div class='equip_item'>
                <div class='equip_item_img'>
                    <img src='" . $profile_class->{$img} . "' width='100px' height='100px'>
                </div>
                <div class='equip_item_name'>" .
                    item_popup($profile_class->{$name}, $profile_class->{$s}) .
                "</div>
            </div>
          </div>";

    $count++;
}

if ($pinfo->id > 0) {
    echo "<div class='col-4 col-md-3'>
            <div class='equip_item'>
                <div class='equip_item_img'>
                    <img src='" . $pinfo->avi . "' width='100px' height='100px'>
                </div>
                <div class='equip_item_name'>" .
                    $pinfo->formatName() .
                "</div>
            </div>
          </div>";
}

echo "</div></div>";  





    echo "<style>#total { flex-basis: 100%; }</style>";

   


                // ADMIN/GM STUFF

                if ($user_class->admin == 1 || $user_class->st == 1) {
                    $result = mysql_query("SELECT * FROM grpgusers WHERE id='$profile_class->id'");
                    $worked = mysql_fetch_array($result);
                    ?>
                    <tr ><td class="contentspacer"></td></tr><td class="contenthead">Add as staff</td>
                    <tr><td class="contentcontent">
                            <table width='100%' class='responsive' align="center">
                                <?php
                                if ($worked['admin'] == 0 && $worked['gm'] == 0 && $worked['fm'] == 0 && $worked['cm'] == 0 && $worked['eo'] == 0) {
                                    ?>
                                    <tr>
                                    <form method="post">
                                        <td><input type="hidden" name="id" value="<?php
                                            echo $profile_class->id;
                                            ?>" /></td>
                                        <td align="center">
                                            <input type="submit" name="adminstatus" value="Make Admin" />
                                        </td>
                                        <td align="center">
                                            <input type="submit" name="gmstatus" value="Make Game Mod" />
                                        </td>
                                        <td align="center">
                                            <input type="submit" name="fmstatus" value="Make Forum Mod" />
                                        </td>
 <td align="center">
                                            <a href='gmpanel.php?userid=<?php
                                                echo $profile_class->id;
                                                ?>&action=acthour'>Check Logs</a>
                                        </td>

                                        </tr>
                                </table>
                                <table width='100%' align="center">
                                    <tr>
                                        <td align="center">
                                            <input type="submit" name="cmstatus" value="Make Chat Mod" />
                                        </td>
                                        <td align="center">
                                            <input type="submit" name="pgstatus" value="Make player guide" />
                                        </td>
                                        <td align="center">
                                            <input type="submit" name="ststatus" value="Make Sergeant" />
                                        </td>
                                        <td align="center">
                                            <input type="submit" name="clearmail" value="Mail Clear" />
                                        </td>
                                        </form>
                                    </tr>
                                </table>
                            </td></tr>
                        <?php
                    } else {
                        ?>
                        <tr>
                        <form method="post">
                            <td><input type="hidden" name="id" value="<?php
                                echo $profile_class->id;
                                ?>" /></td>
                            <td align="center">
                                <input type="submit" name="revokeadminstatus" value="Revoke Admin" />
                            </td>
                            <td align="center">
                                <input type="submit" name="revokegmstatus" value="Revoke Game Mod" />
                            </td>
                            <td align="center">
                                <input type="submit" name="revokefmstatus" value="Revoke Forum Mod" />
                            </td>
                            </tr>
                            </table>
                            <table width='100%' align="center">
                                <tr>
                                    <td align="center">
                                        <input type="submit" name="revokecmstatus" value="Revoke Chat Mod" />
                                    </td>
                                    <td align="center">
                                        <input type="submit" name="revokeeostatus" value="Revoke Event Organiser" />
                                    </td>
                                    <td align="center">
                                        <input type="submit" name="revokeststatus" value="Revoke Sergeant" />
                                    </td>
                                    <td align="center">
                                        <input type="submit" name="clearmail" value="Mail Clear" />
                                    </td>
                                    </form>
                                </tr>
                            </table>
                            </td></tr>
                            <?php
                        }
                        if ($user_class->admin == 1) {
                            ?>
                            <tr><td class="contentspacer"></td></tr><td class="contenthead">Add Upgrade Package</td>
                            <tr><td class="contentcontent">
                                    <table width='80%'>
                                        <tr>
                                        <form method="post">
                                            <td>Add RM Pack:&nbsp;</td>
                                            <td>
                                                <select name="rmdays">
                                                    <option value="30days">30 Day RM</option>
                                                    <option value="60days">60 Day RM</option>
                                                    <option value="90days">90 Day RM</option>
                                                </select>
                                            </td>
                                            <td><input type="hidden" name="id" value="<?php
                                                echo $profile_class->id;
                                                ?>" /></td>
                                            <td><input type="submit" name="addrmdays" value="Add RM Pack" /></td>
                                        </form>
                            </tr>
                            <tr>
                            <form method="post">
                                <td>Change Bank:</td>
                                <td><input type="text" name="bank" size="20" value="0" /></td>
                                <td><input type="hidden" name="id" value="<?php
                                    echo $profile_class->id;
                                    ?>" ></td>
                                <td><input type="submit" name="cbank" value="Change Bank" /></button></td>
                            </form>
                            </tr>
                            <tr>
                            <form method="post">
                                <td>Change Hand:</td>
                                <td><input type="text" name="money" size="20" value="0" /></td>
                                <td><input type="hidden" name="id" value="<?php
                                    echo $profile_class->id;
                                    ?>" /></td>
                                <td><input type="submit" name="cmoney" value="Change Hand" /></td>
                            </form>
                            </tr>
                            <tr>
                            <form method="post">
                                <td>Change Gang:</td>
                                <td><input type="text" name="gang" size="20" value="0" /></td>
                                <td><input type="hidden" name="id" value="<?php
                                    echo $profile_class->id;
                                    ?>" /></td>
                                <td><input type="submit" name="cgang" value="Change Gang" /></td>
                            </form>
                            </tr>
                            <tr>
                            <form method="post">
                                <td>Add Points:</td>
                                <td><input type="text" name="points" size="20" /></td>
                                <td><input type="hidden" name="id" value="<?php
                                    echo $profile_class->id;
                                    ?>" /></td>
                                <td><input type="submit" name="addcpoints" value="Add Points" /></td>
                            </form>
                            </tr>
                            <tr>
                            <form method="post">
                                <td>Add Credits:</td>
                                <td><input type="text" name="credits" size="20" /></td>
                                <td><input type="hidden" name="id" value="<?php
                                    echo $profile_class->id;
                                    ?>" /></td>
                                <td><input type="submit" name="addcredits" value="Add Credits" /></td>
                            </form>
                            </tr>

                            <tr>
                            <form method="post">
                                <td>Send Item:</td>
                                <td>
                                    <select name='admin_item_id'>
                                        <?php
                                        $db->query("SELECT * FROM items");
                                        $db->execute();
                                        $aditems = $db->fetch_row();
                                        foreach ($aditems as $aditem):
                                        ?>
                                            <option value="<?php echo $aditem['id'] ?>"><?php echo $aditem['itemname'] ?></option>
                                        <?php endforeach; ?>
                                    </select><br />


                                       <input type="number" name="quantity" placeholder="quantity" />
                                </td>
                                <td><input type="hidden" name="id" value="<?php
                                    echo $profile_class->id;
                                    ?>" /></td>
                                <td><input type="submit" name="senditems" value="Send Items" /></td>
                            </form>
                            </tr>
                            </table>
                            </td></tr>
                            <?php
                        }
                    }
                    ?>
                    <?php
                    if ($user_class->admin == 1 || $user_class->gm == 1 || $user_class->fm == 1) {
                        if ($profile_class->admin != 1 || $user_class->id != $profile_class->id) {
                            $type1 = "perm";
                            $result1 = mysql_query("SELECT * FROM bans WHERE id='$profile_class->id' AND type='$type1'");
                            $worked1 = mysql_fetch_array($result1);
                            $type2 = "forum";
                            $result2 = mysql_query("SELECT * FROM bans WHERE id='$profile_class->id' AND type='$type2'");
                            $worked2 = mysql_fetch_array($result2);
                            $type3 = "freeze";
                            $result3 = mysql_query("SELECT * FROM bans WHERE id='$profile_class->id' AND type='$type3'");
                            $worked3 = mysql_fetch_array($result3);
                            $type4 = "mail";
                            $result4 = mysql_query("SELECT * FROM bans WHERE id='$profile_class->id' AND type='$type4'");
                            $worked4 = mysql_fetch_array($result4);
                            $type7 = "quicka";
                            $result7 = mysql_query("SELECT * FROM bans WHERE id='$profile_class->id' AND type='$type7'");
                            $worked7 = mysql_fetch_array($result7);
                            ?>
                            <tr><td class="contentspacer"></td></tr><tr><td class="contenthead">Banning Options</td></tr>
                            <tr><td class="contentcontent">
                                    <table width='100%'>
                                        <?php
                                        if ($user_class->admin == 1 || $user_class->gm == 1) {
                                            ?>
                                            <tr>
                                            <form method="post">
                                                <td>[Game Ban]&nbsp;</td>
                                                <td>Days:&nbsp;</td>
                                                <td><?php
                                                    if ($worked1['days'] >= 1) {
                                                        ?><input type="text" name="days" DISABLED value="<?php
                                                        echo $worked1['days'];
                                                        ?>" /></td><?php
                                                    } else {
                                                        ?><input type="text" name="days" /><?php
                                                    }
                                                    ?>
                                                <td><input type="hidden" name="id" value="<?php
                                                    echo $profile_class->id;
                                                    ?>" /></td>
                                                <td><?php
                                                    if ($worked1['days'] == 0) {
                                                        ?><input type="submit" name="permban" value="Ban" /><?php
                                                    } else {
                                                        ?><input type="submit" name="permban" value="Ban" DISABLED /><?php
                                                    }
                                                    ?></td>
                                                <td><?php
                                                    if ($worked1['days'] >= 1) {
                                                        ?><input type="submit" name="unpermban" value="Un-Ban" /><?php
                                                    } else {
                                                        ?><input type="submit" name="unpermban" value="Un-Ban" DISABLED /><?php
                                                    }
                                                    ?></td>
                                                <td><?php
                                                    if ($worked1['days'] >= 1) {
                                                        ?>[Banned - <?php
                                                        echo prettynum($worked1['days']);
                                                        ?> days] <?php
                                                    }
                                                    ?></td>
                                            </form>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <?php
                            if ($user_class->admin == 1 || $user_class->gm == 1 || $user_class->fm == 1) {
                                ?>
                                <tr>
                                <form method="post">
                                    <td>[Forum Ban]&nbsp;</td>
                                    <td>Days:&nbsp;</td>
                                    <td><?php
                                        if ($worked2['days'] >= 1) {
                                            ?><input type="text" name="days" DISABLED value="<?php
                                            echo $worked2['days'];
                                            ?>" /></td><?php
                                        } else {
                                            ?><input type="text" name="days" /><?php
                                        }
                                        ?>
                                    <td><input type="hidden" name="id" value="<?php
                                        echo $profile_class->id;
                                        ?>" /></td>
                                    <td><?php
                                        if ($worked2['days'] == 0) {
                                            ?><input type="submit" name="forumban" value="Ban" /><?php
                                        } else {
                                            ?><input type="submit" name="forumban" value="Ban" DISABLED /><?php
                                        }
                                        ?></td>
                                    <td><?php
                                        if ($worked2['days'] >= 1) {
                                            ?><input type="submit" name="unforumban" value="Un-Ban" /><?php
                                        } else {
                                            ?><input type="submit" name="unforumban" value="Un-Ban" DISABLED /><?php
                                        }
                                        ?></td>
                                    <td><?php
                                        if ($worked2['days'] >= 1) {
                                            ?>[Banned - <?php
                                            echo prettynum($worked2['days']);
                                            ?> days] <?php
                                        }
                                        ?></td>
                                </form>
                                </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <?php
                            if ($user_class->admin == 1 || $user_class->gm == 1 || $user_class->cm == 1) {
                                ?>
                                <tr>
                                <form method="post">
                                    <td>[Mail Ban]&nbsp;</td>
                                    <td>Days:&nbsp;</td>
                                    <td><?php
                                        if ($worked4['days'] >= 1) {
                                            ?><input type="text" name="days" DISABLED value="<?php
                                            echo $worked4['days'];
                                            ?>" /></td><?php
                                        } else {
                                            ?><input type="text" name="days" /><?php
                                        }
                                        ?>
                                    <td><input type="hidden" name="id" value="<?php
                                        echo $profile_class->id;
                                        ?>" /></td>
                                    <td><?php
                                        if ($worked4['days'] == 0) {
                                            ?><input type="submit" name="mailban" value="Ban" /><?php
                                        } else {
                                            ?><input type="submit" name="mailban" value="Ban" DISABLED /><?php
                                        }
                                        ?></td>
                                    <td><?php
                                        if ($worked4['days'] >= 1) {
                                            ?><input type="submit" name="unmailban" value="Un-Ban" /><?php
                                        } else {
                                            ?><input type="submit" name="unmailban" value="Un-Ban" DISABLED /><?php
                                        }
                                        ?></td>
                                    <td><?php
                                        if ($worked4['days'] >= 1) {
                                            ?>[Banned - <?php
                                            echo prettynum($worked4['days']);
                                            ?> days] <?php
                                        }
                                        ?></td>
                                </form>
                                </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <?php
                            if ($user_class->admin == 1 || $user_class->gm == 1) {
                                ?>
                                <tr>
                                <form method="post">
                                    <td>[Quick Ads Ban]&nbsp;</td>
                                    <td>Days:&nbsp;</td>
                                    <td><?php
                                        if ($worked7['days'] >= 1) {
                                            ?><input type="text" name="days" DISABLED value="<?php
                                            echo $worked7['days'];
                                            ?>" /></td><?php
                                        } else {
                                            ?><input type="text" name="days" /><?php
                                        }
                                        ?>
                                    <td><input type="hidden" name="id" value="<?php
                                        echo $profile_class->id;
                                        ?>" /></td>
                                    <td><?php
                                        if ($worked7['days'] == 0) {
                                            ?><input type="submit" name="qaban" value="Ban" /><?php
                                        } else {
                                            ?><input type="submit" name="qaban" value="Ban" DISABLED id="qaban" />
                                            <?php
                                        }
                                        ?></td>
                                    <td><?php
                                        if ($worked7['days'] >= 1) {
                                            ?><input type="submit" name="unqaban" value="Un-Ban" /><?php
                                        } else {
                                            ?><input type="submit" name="unqaban" value="Un-Ban" DISABLED /><?php
                                        }
                                        ?></td>
                                    <td><?php
                                        if ($worked7['days'] >= 1) {
                                            ?>[Banned - <?php
                                            echo prettynum($worked7['days']);
                                            ?> days] <?php
                                        }
                                        ?></td>
                                </form>
                                </td>
                                </tr>
                                <?php
                            }
                            ?>
                            <?php
                            if ($user_class->admin == 1 || $user_class->gm == 1) {
                                ?>
                                <tr>
                                <form method="post">
                                    <td>[Freeze Acct]&nbsp;</td>
                                    <td>Days:&nbsp;</td>
                                    <td><?php
                                        if ($worked3['days'] >= 1) {
                                            ?><input type="text" name="days" DISABLED value="<?php
                                            echo $worked3['days'];
                                            ?>" /></td><?php
                                        } else {
                                            ?><input type="text" name="days" /><?php
                                        }
                                        ?>
                                    <td><input type="hidden" name="id" value="<?php
                                        echo $profile_class->id;
                                        ?>" /></td>
                                    <td><?php
                                        if ($worked3['days'] == 0) {
                                            ?><input type="submit" name="freeze" value="Ban" /><?php
                                        } else {
                                            ?><input type="submit" name="freeze" value="Ban" DISABLED /><?php
                                        }
                                        ?></td>
                                    <td><?php
                                        if ($worked3['days'] >= 1) {
                                            ?><input type="submit" name="unfreeze" value="Un-Ban" /><?php
                                        } else {
                                            ?><input type="submit" name="unfreeze" value="Un-Ban" DISABLED /><?php
                                        }
                                        ?></td>
                                    <td><?php
                                        if ($worked3['days'] >= 1) {
                                            ?>[Banned - <?php
                                            echo prettynum($worked3['days']);
                                            ?> days] <?php
                                        }
                                        ?></td>
                                </form>
                                </td>
                                </tr>
                                <?php
                            }
                            if ($user_class->admin == 1 || $user_class->gm == 1) {
                                ?>
                                <tr>
                                <form method="post">
                                    <td>[Freeze Activity Points]&nbsp;</td>
                                    <td>Days:&nbsp;</td>
                                    <td><?php
                                        if ($profile_class->apban >= 1) {
                                            ?><input type="text" name="days" DISABLED value="<?php
                                            echo $profile_class->apban;
                                            ?>" /></td><?php
                                        } else {
                                            ?><input type="text" name="days" /><?php
                                        }
                                        ?>
                                    <td><input type="hidden" name="id" value="<?php
                                        echo $profile_class->id;
                                        ?>" /></td>
                                    <td><?php
                                        if ($profile_class->apban == 0) {
                                            ?><input type="submit" name="apban" value="Ban" /><?php
                                        } else {
                                            ?><input type="submit" name="apban" value="Ban" DISABLED /><?php
                                        }
                                        ?></td>
                                    <td><?php
                                        if ($profile_class->apban >= 1) {
                                            ?><input type="submit" name="apunban" value="Un-Ban" /><?php
                                        } else {
                                            ?><input type="submit" name="apunban" value="Un-Ban" DISABLED /><?php
                                        }
                                        ?></td>
                                    <td><?php
                                        if ($profile_class->apban >= 1) {
                                            ?>[Banned - <?php
                                            echo prettynum($profile_class->apban);
                                            ?> days] <?php
                                        } else
                                            echo "[Max: 127 Day Ban]";
                                        ?></td>
                                </form>
                                </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </table>
                            <?php
                        }
                    }
                    ?>
                    <?php
                    if ($user_class->admin == 1 || $user_class->gm == 1 || $user_class->fm == 1) {
                        $query = "SELECT `notes` FROM `grpgusers` WHERE Id = $profile_class->id";
                        $result2 = mysql_query($query);
                        if (!$result2) {
                            $message = 'Invalid query: ' . mysql_error() . "\n";
                            $message .= 'Whole query: ' . $query;
                            die($message);
                        }
                        while ($row = mysql_fetch_assoc($result2)) {
                            $notes = $row['notes'];
                        }
                        ?>
                        <tr><td class="contentspacer"></td></tr><td class="contenthead">Player Notes</td>
                        <tr><td class="contentcontent">
                                <table width="95%">
                                    <form method="post">
                                        <tr>
                                            <td align="center"><textarea name="notes" cols="80" rows="12"><?php
                                                    echo $notes;
                                                    ?></textarea></td>
                                        </tr>
                                        <tr>
                                            <td align="center"><input type="hidden" name="id" value="<?php
                                                echo $profile_class->id;
                                                ?>" /><input type="submit" name="addnotes" value="Add Notes"/><br><input type="submit" name="5ips" value="Get last 5 IPs"/>
                                            </td>
                                        </tr>
                                    </form>
                                </table>
                            </td></tr>
                        <?php
                    }
                    ?>
                    <?php
                    if ($user_class->admin == 1 || $user_class->gm == 1) {
                        ?>
                        <tr><td class="contentspacer"></td></tr><td class="contenthead">Add/Change Flag</td>
                        <tr><td class="contentcontent">
                                <table width="50%" align="center">
                                    <form method="post">
                                        <tr>
                                            <td width="30%">
                                                <select name="flag">
                                                    <option value="0">None</option>
                                                    <option value="scammer-tag.gif">Scammer Flag</option>
                                                    <option value="prestigered.JPG">Prestige Rank</option>
                                                    <option value="images/item15.png">Prestige blue</option>
                                                    <option value="images/item13.png">Prestige pink</option>
                                                    <option value="images/item16.png">Prestige light blue</option>
                                                    <option value="British.gif">British Flag</option>
                                                    <option value="coolimg.png">Spinner Flag</option>
                                                    <option value="http://fc03.deviantart.net/fs70/f/2013/054/c/1/cool_design_header_by_kotrla-d5vyamb.png">badass flag</option>
                                                </select>
                                            </td>
                                            <td width="20%">
                                                <input type="submit" name="changeflag" value="Add/Change Flag" />
                                            </td>
                                        </tr>
                                    </form>
                                </table>
                            </td></tr>
                        <?php
                    }
                    if ($user_class->admin == 1 || $user_class->gm == 1) {
                        ?>
                        <tr><td class="contentspacer"></td></tr><td class="contenthead">Change Gang</td>
                        <tr><td class="contentcontent">
                                <table width="50%" align="center">
                                    <form method="post">
                                        <tr>
                                            <td width="30%">
                                                <input type="text" name="gang" size="20" value="0" />
                                            </td>
                                            <td width="20%">
                                                <input type="submit" name="changegang" value="Change Gang" />
                                            </td>
                                        </tr>
                                    </form>
                                </table>
                            </td></tr>
                        <?php
                    }
                    ?>
                    <?php
                    if ($user_class->admin == 1) {
                        ?>
                        <tr><td class="contentspacer"></td></tr><tr><td class="contenthead">General Information</td>
                        <tr>
                            <td class="contentcontent">
                                <table width='100%'>
                                    <tr>
                                        <td width='15%'>Name:</td>
                                        <td><a href='profiles.php?id=<?php
                                            echo $profile_class->id;
                                            ?>'>[<?php
                                            echo $profile_class->id;
                                            ?>]<?php
                                                   echo $profile_class->formattedname;
                                                   ?></a></td>
                                        <td width='15%'>HP:</td>
                                        <td><?php
                                            echo prettynum($profile_class->formattedhp);
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td width='15%'>Level:</td>
                                        <td><?php
                                            echo $profile_class->level;
                                            ?></td>
                                        <td width='15%'>Energy:</td>
                                        <td><?php
                                            echo prettynum($profile_class->formattedenergy);
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td width='15%'>Money:</td>
                                        <td>$<?php
                                            echo prettynum($profile_class->money);
                                            ?></td>
                                        <td width='15%'>Awake:</td>
                                        <td><?php
                                            echo prettynum($profile_class->formattedawake);
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td width='15%'>Bank:</br>Shared Bank:</td>
                                        <td>$<?php
                                            echo prettynum($profile_class->bank);
                                            ?></br> $<?php
                                            echo prettynum($profile_class->shared_bank);
                                            ?></td>
                                        <td width='15%'>Nerve:</td>
                                        <td><?php
                                            echo prettynum($profile_class->formattednerve);
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td width='15%'>EXP:</td>
                                        <td><?php
                                            echo prettynum($profile_class->formattedexp);
                                            ?></td>
                                        <td width='15%'>Work EXP:</td>
                                        <td><?php
                                            echo prettynum($profile_class->workexp);
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td width='15%'>Marijuana:</td>
                                        <td><?php
                                            echo prettynum($user_class->marijuana);
                                            ?></td>
                                        <td width='15%'>Points:</td>
                                        <td><?php
                                            echo prettynum($profile_class->points);
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <?php
                                        if ($profile_class->admin == 0) {
                                            ?>
                                            <td width='15%'>IP:</td>
                                            <td><?php
                                                echo $profile_class->ip;
                                                ?></td>
                                            <?php
                                        }
                                        if ($profile_class->admin == 1) {
                                            ?>
                                            <td width='15%'>IP:</td>
                                            <td>ADMIN</td>
                                            <?php
                                        }
                                        ?>
                                        <td width='15%'>Email:</td>
                                        <td><?php
                                            echo $profile_class->email;
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td width='15%'>Credits:</td>
                                        <td><?php
                                            echo $profile_class->credits;
                                            ?></td>
                                        <td width='15%'>Activity Points:</td>
                                        <td><?php
                                            echo prettynum($profile_class->apoints);
                                            ?></td>
                                    </tr>
                                        <td width='15%'>Points bank:</td>
                                        <td><?php
                                            echo prettynum($profile_class->pbank);
                                            ?></td>
                                        <td width='15%'>Actions:</td>
                                        <td><?php
                                            echo prettynum($profile_class->actions);
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td width='15%'>Raid Tokens:</td>
                                        <td><?php
                                            echo prettynum($profile_class->raidtokens);
                                            ?></td>
                                        <td width='15%'>&nbsp;</td>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td width='15%'>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td width='15%'>Strength:</td>
                                        <td><?php
                                            echo prettynum($profile_class->strength);
                                            ?></td>
                                        <td width='15%'>Defense:</td>
                                        <td><?php
                                            echo prettynum($profile_class->defense);
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td width='15%'>Speed:</td>
                                        <td><?php
                                            echo prettynum($profile_class->speed);
                                            ?></td>
                                        <td width='15%'>Total:</td>
                                        <td><?php
                                            echo prettynum($profile_class->totalattrib);
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td width='15%'>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td width='15%'>Strength:</td>
                                        <td><?php
                                            echo prettynum($profile_class->moddedstrength);
                                            ?></td>
                                        <td width='15%'>Defense:</td>
                                        <td><?php
                                            echo prettynum($profile_class->moddeddefense);
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td width='15%'>Speed:</td>
                                        <td><?php
                                            echo prettynum($profile_class->moddedspeed);
                                            ?></td>
                                        <td width='15%'>Total:</td>
                                        <td><?php
                                            echo prettynum($profile_class->moddedtotalattrib);
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <td width='15%'>Password:</td>
                                        <td><?php
                                            echo " $profile_class->password ";
                                            ?></td>
                                        <td width='15%'></td>
                                        <td></td>
                                    </tr>
                                </table>
                            </td></tr>
                        <?php
                    }
                    if ($user_class->admin == 1) {
                        ?>
                       <div class="container mt-3 mb-3">
    <div class="row">
        <div class="col-12 mb-2">
            <div class="text-center contenthead">Equipped</div>
        </div>
    </div>
    <div class="row text-center">
        <!-- Weapon -->
        <div class="col-md-4">
            <?php if ($profile_class->eqweapon != 0): ?>
                <img src='<?php echo $profile_class->weaponimg; ?>' alt='Equipped Weapon' width='100' height='100' style='border: 1px solid #01a9b8'><br>
                <?php echo item_popup($profile_class->weaponname, $profile_class->eqweapon); ?><br>
            <?php else: ?>
                You don't have a weapon equipped.
            <?php endif; ?>
        </div>

        <!-- Armor -->
        <div class="col-md-4">
            <?php if ($profile_class->eqarmor != 0): ?>
                <img src='<?php echo $profile_class->armorimg; ?>' alt='Equipped Armor' width='100' height='100' style='border: 1px solid #01a9b8'><br>
                <?php echo item_popup($profile_class->armorname, $profile_class->eqarmor); ?><br>
            <?php else: ?>
                You don't have any armor equipped.
            <?php endif; ?>
        </div>

        <!-- Shoes -->
        <div class="col-md-4">
            <?php if ($profile_class->eqshoes != 0): ?>
                <img src='<?php echo $profile_class->shoesimg; ?>' alt='Equipped Shoes' width='100' height='100' style='border: 1px solid #01a9b8'><br>
                <?php echo item_popup($profile_class->shoesname, $profile_class->eqshoes); ?><br>
            <?php else: ?>
                You don't have any shoes equipped.
            <?php endif; ?>
        </div>
    </div>
</div>

                        <?php
                        $result = mysql_query("SELECT * FROM inventory WHERE userid = '$profile_class->id' ORDER BY userid DESC");
                        while ($line = mysql_fetch_array($result, mysql_ASSOC)) {
                            $result2 = mysql_query("SELECT * FROM items WHERE id='" . $line['itemid'] . "'");
                            $worked2 = mysql_fetch_array($result2);
                            if ($worked2['offense'] > 0 && $worked2['rare'] == 0) {
                                $weapons .= "
<td width='25%' align='center'>
<img src='" . $worked2['image'] . "' width='100' height='100' style='border: 1px solid #01a9b8'><br>
" . item_popup($worked2['itemname'], $worked2['id']) . " [x" . $line['quantity'] . "]<br>
$" . prettynum($worked2['cost']) . "<br>
</td>
";
                                $howmanyitems = $howmanyitems + 1;
                                if ($howmanyitems == 4) {
                                    $weapons .= "</tr><tr height='15'></tr><tr>";
                                    $howmanyitems = 0;
                                }
                            }
                            if ($worked2['defense'] > 0 && $worked2['rare'] == 0) {
                                $armor .= "
<td width='25%' align='center'>
<img src='" . $worked2['image'] . "' width='100' height='100' style='border: 1px solid #01a9b8'><br>
" . item_popup($worked2['itemname'], $worked2['id']) . " [x" . $line['quantity'] . "]<br>
$" . prettynum($worked2['cost']) . "<br>
</td>
";
                                $howmanyitems2 = $howmanyitems2 + 1;
                                if ($howmanyitems2 == 4) {
                                    $armor .= "</tr><tr height='15'></tr><tr>";
                                    $howmanyitems2 = 0;
                                }
                            }
                            if ($worked2['speed'] > 0 && $worked2['rare'] == 0) {
                                $shoes .= "
<td width='25%' align='center'>
<img src='" . $worked2['image'] . "' width='100' height='100' style='border: 1px solid #01a9b8'><br>
" . item_popup($worked2['itemname'], $worked2['id']) . " [x" . $line['quantity'] . "]<br>
$" . prettynum($worked2['cost']) . "<br>
</td>
";
                                $howmanyitems3 = $howmanyitems3 + 1;
                                if ($howmanyitems3 == 4) {
                                    $shoes .= "</tr><tr height='15'></tr><tr>";
                                    $howmanyitems3 = 0;
                                }
                            }
                            if ($worked2['rare'] == 1) {
                                $rares .= "
<td width='25%' align='center'>
<img src='" . $worked2['image'] . "' width='100' height='100' style='border: 1px solid #01a9b8'><br>
" . item_popup($worked2['itemname'], $worked2['id']) . " [x" . $line['quantity'] . "]<br>
</td>
";
                                $howmanyitems6 = $howmanyitems6 + 1;
                                if ($howmanyitems6 == 4) {
                                    $rares .= "</tr><tr height='15'></tr><tr>";
                                    $howmanyitems6 = 0;
                                }
                            }
                            if ($worked2['offense'] == 0 && $worked2['defense'] == 0 && $worked2['speed'] == 0 && $worked2['petupgrades'] == 0 && $worked2['rare'] == 0) {
                                $misc .= "
<td width='25%' align='center'>
<img src='" . $worked2['image'] . "' width='100' height='100' style='border: 1px solid #01a9b8'><br>
" . item_popup($worked2['itemname'], $worked2['id']) . " [x" . $line['quantity'] . "]<br>
</td>
";
                                $howmanyitems7 = $howmanyitems7 + 1;
                                if ($howmanyitems7 == 4) {
                                    $misc .= "</tr><tr height='15'></tr><tr>";
                                    $howmanyitems7 = 0;
                                }
                            }
                        }
                        $result = mysql_query("SELECT * FROM gang_loans WHERE to = '$profile_class->id' ORDER BY to DESC");
                        while ($line = mysql_fetch_array($result, mysql_ASSOC)) {
                            $result2 = mysql_query("SELECT * FROM items WHERE id='" . $line['item'] . "'");
                            $worked2 = mysql_fetch_array($result2);
                            if ($worked2['offense'] > 0 || $worked2['defense'] > 0 || $worked2['speed'] > 0) {
                                $loaned .= "
<td width='25%' align='center'>
<img src='" . $worked2['image'] . "' width='100' height='100' style='border: 1px solid #01a9b8'><br>
" . item_popup($worked2['itemname'], $worked2['id']) . " [x" . $line['quantity'] . "]<br>
$" . prettynum($worked2['cost']) . "<br>
</td>
";
                                $howmanyitems = $howmanyitems + 1;
                                if ($howmanyitems == 4) {
                                    $loaned .= "</tr><tr height='15'></tr><tr>";
                                    $howmanyitems = 0;
                                }
                            }
                        }
                        if (!empty($weapons)) {
                            ?>
                            <tr><td class="contentspacer"></td></tr><td class="contenthead">Weapons</td>
                            <tr><td class="contentcontent">
                                    <table width='100%'>
                                        <tr>
                                            <?php
                                            echo $weapons;
                                            ?>
                                        </tr>
                                    </table>
                                </td></tr>
                            <?php
                        }
                        if (!empty($armor)) {
                            ?>
                            <tr><td class="contentspacer"></td></tr><td class="contenthead">Armour</td>
                            <tr><td class="contentcontent">
                                    <table width='100%'>
                                        <tr>
                                            <?php
                                            echo $armor;
                                            ?>
                                        </tr>
                                    </table>
                                </td></tr>
                            <?php
                        }
                        if (!empty($shoes)) {
                            ?>
                            <tr><td class="contentspacer"></td></tr><td class="contenthead">Shoes</td>
                            <tr><td class="contentcontent">
                                    <table width='100%'>
                                        <tr>
                                            <?php
                                            echo $shoes;
                                            ?>
                                        </tr>
                                    </table>
                                </td></tr>
                            <?php
                        }
                        if (!empty($loaned)) {
                            ?>
                            <tr><td class="contentspacer"></td></tr><td class="contenthead">Items Loaned From Your Gang</td>
                            <tr><td class="contentcontent">
                                    <table width='100%'>
                                        <tr>
                                            <?php
                                            echo $loaned;
                                            ?>
                                        </tr>
                                    </table>
                                </td></tr>
                            <?php
                        }
                        if (!empty($rares)) {
                            ?>
                            <tr><td class="contentspacer"></td></tr><td class="contenthead">Rare Items</td>
                            <tr><td class="contentcontent">
                                    <table width='100%'>
                                        <tr>
                                            <?php
                                            echo $rares;
                                            ?>
                                        </tr>
                                    </table>
                                </td></tr>
                            <?php
                        }
                        if (!empty($drugs)) {
                            ?>
                            <tr><td class="contentspacer"></td></tr><td class="contenthead">Drugs</td>
                            <tr><td class="contentcontent">
                                    <table width='100%'>
                                        <tr>
                                            <?php
                                            echo $drugs;
                                            ?>
                                        </tr>
                                    </table>
                                </td></tr>
                            <?php
                        }
                        if (!empty($misc)) {
                            ?>
                            <tr><td class="contentspacer"></td></tr><td class="contenthead">Other</td>
                            <tr><td class="contentcontent">
                                    <table width='100%'>
                                        <tr>
                                            <?php
                                            echo $misc;
                                            ?>
                                        </tr>
                                    </table>
                                </td></tr>
                            <?php
                        }
                    }
                    if ($user_class->admin == 1 || $user_class->gm == 1) {
                        if (isset($_POST['submit'])) {
                            $_POST['signature'] = str_replace('"', '', $_POST['signature']);
                            $signature = strip_tags($_POST["signature"]);
                            $signature = addslashes($signature);
                            $_POST['username'] = str_replace('"', '', $_POST['username']);
                            $username = $_POST["username"];
                            $username = addslashes($username);
                            $email = strip_tags($_POST['email']);
                            $email = addslashes($email);
                            $_POST['avatar'] = str_replace('"', '', $_POST['avatar']);
                            $_POST['avatar'] = str_replace('[IMG]', '', $_POST['avatar']);
                            $_POST['avatar'] = str_replace('[/IMG]', '', $_POST['avatar']);
                            $_POST['avatar'] = str_replace('[img]', '', $_POST['avatar']);
                            $_POST['avatar'] = str_replace('[/img]', '', $_POST['avatar']);
                            $avatar = strip_tags($_POST["avatar"]);
                            $avatar = addslashes($avatar);
                            $_POST['quote'] = str_replace('"', '', $_POST['quote']);
                            $quote = strip_tags($_POST["quote"]);
                            $quote = addslashes($quote);
                            $gender = $_POST["gender"];
                            $music = $_POST['music'];
                            $volume = $_POST['volume'];
                            if (strlen($username) < 3 or strlen($username) > 16) {
                                $message .= "<td>The name you chose has " . strlen($username) . " characters. You need to have between 3 and 16 characters.</td>";
                            }
                            if (url_exists(strip_tags($_POST['avatar'])) == 0 && strip_tags($_POST['avatar']) != "") {
                                $message .= "<td>Your avatar link appears to be broken. Please check it in your browser.</td>";
                            }
                            if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
                                $message .= "<td>Your new email address is invalid.</td>";
                            }
                            if ($message != "") {
                                echo Message($message);
                            } else {
                                $result = mysql_query("UPDATE grpgusers SET avatar='" . $avatar . "', quote='" . $quote . "', gender='" . $gender . "', username='" . $username . "', signature='" . $signature . "', music = '" . $music . "', volume = '" . $volume . "', email = '" . $email . "' WHERE id='$profile_class->id'");
                                echo Message('You have edited this players prefrences.');
                            }
                        }
                        $selected1 = $selected2 = $selected3 = $selected4 = $selected5 = $selected6 = $selected7 = $selected8 = $selected9 = $selected10 = "";
                        if ($profile_class->volume == 10) {
                            $selected1 = ' selected="true"';
                        } else if ($profile_class->volume == 20) {
                            $selected2 = ' selected="true"';
                        } else if ($profile_class->volume == 30) {
                            $selected3 = ' selected="true"';
                        } else if ($profile_class->volume == 40) {
                            $selected4 = ' selected="true"';
                        } else if ($profile_class->volume == 50) {
                            $selected5 = ' selected="true"';
                        } else if ($profile_class->volume == 60) {
                            $selected6 = ' selected="true"';
                        } else if ($profile_class->volume == 70) {
                            $selected7 = ' selected="true"';
                        } else if ($profile_class->volume == 80) {
                            $selected8 = ' selected="true"';
                        } else if ($profile_class->volume == 90) {
                            $selected9 = ' selected="true"';
                        } else if ($profile_class->volume == 100) {
                            $selected10 = ' selected="true"';
                        }
                        ?>
                        <tr><td class="contentspacer"></td></tr><td class="contenthead">Account Preferences</td>
                        <tr><td class="contentcontent">
                                <form method='post' action="profiles.php?id=<?php
                                echo $_GET['id'];
                                ?>">
                                    <table width='100%' border='0'>
                                        <tr>
                                            <td><b>Name:</b></td>
                                            <td>
                                                <input type='text' name='username' value="<?php
                                                echo strip_tags($profile_class->username);
                                                ?>" maxlength="16">&nbsp;<span style="font-size:10px;">Will NOT change your login name.</span>
                                                </font></td>
                                        </tr>
                                        <tr>
                                            <td><b>Email:</b></td>
                                            <td>
                                                <input type='text' name='email' value="<?php
                                                echo strip_tags($profile_class->email);
                                                ?>" size="30">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Avatar:</b></td>
                                            <td>
                                                <input type='text' name='avatar' size="40" value="<?php
                                                echo strip_tags(addslashes($profile_class->avatar));
                                                ?>">&nbsp;<span style="font-size:10px;">Upload avatar to <a href="http://tinypic.com" title="Tinypic Image Hosting" target="_blank">Tinypic</a>.</span>
                                            </td>
                                        </tr>
                                        <tr>
                                        <tr>
                                            <td><b>Quote:</b></td>
                                            <td>
                                                <input type='text' name='quote' size="85" maxlength="300" value="<?php
                                                echo strip_tags($profile_class->quote);
                                                ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Music:</b></td>
                                            <td>
                                                <?php
                                                if ($profile_class->promusic == 1) {
                                                    echo '<select name="music" >
<option value="1" selected="true">Yes</option>
<option value="0">No</option>
</select>';
                                                } else if ($profile_class->promusic == 0) {
                                                    echo '<select name="music" >
<option value="1">Yes</option>
<option value="0" selected="true">No</option>
</select>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Volume:</b></td>
                                            <td>
                                                <?php
                                                echo '<select name="volume" >
<option value="10"' . $selected1 . '>10%</option>
<option value="20"' . $selected2 . '>20%</option>
<option value="30"' . $selected3 . '>30%</option>
<option value="40"' . $selected4 . '>40%</option>
<option value="50"' . $selected5 . '>50%</option>
<option value="60"' . $selected6 . '>60%</option>
<option value="70"' . $selected7 . '>70%</option>
<option value="80"' . $selected8 . '>80%</option>
<option value="90"' . $selected9 . '>90%</option>
<option value="100"' . $selected10 . '>100%</option>
</select>';
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Gender:</b></td>
                                            <td>
                                                <?php
                                                if ($profile_class->gender == "Male") {
                                                    echo '<select name="gender" >
<option value="Male" selected="true">Male</option>
<option value="Female">Female</option>
</select>';
                                                } else if ($profile_class->gender == "Female") {
                                                    echo '<select name="gender" >
<option value="Male">Male</option>
<option value="Female" selected="true">Female</option>
</select>';
                                                } else if ($profile_class->gender == "") {
                                                    echo '<select name="gender" >
<option value="Male">Male</option>
<option value="Female" selected="true">Female</option>
</select>';
                                                } else if ($profile_class->gender == "!") {
                                                    echo '<select name="gender" >
<option value="Male">Male</option>
<option value="Female" selected="true">Female</option>
</select>';
                                                } else if ($profile_class->gender == "female") {
                                                    echo '<select name="gender" >
<option value="Male">Male</option>
<option value="Female" selected="true">Female</option>
</select>';
                                                } else if ($profile_class->gender == "male") {
                                                    echo '<select name="gender" >
<option value="Male">Male</option>
<option value="Female" selected="true">Female</option>
</select>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b>Signature:</b></td>
                                            <td>
                                                <textarea type='text' name='signature' cols='64' rows='6'><?php
                                                    echo strip_tags($profile_class->sig);
                                                    ?></textarea>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td>
                                                <input type='submit' name='submit' value='Save Preferences' />
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </td></tr>

<script type="text/javascript">
let clickCount = 0;

document.addEventListener("DOMContentLoaded",function(){
    document.body.addEventListener('click', function(evt) {
        clickCount = clickCount + 1;
        if (clickCount > 750) {
            window.location.href = "/profiles.php?id=<?php echo $profile_class->id ?>&forced_captcha=yes";
        }
    }, true);
});

</script>

<?PHP
$db->query("SELECT blocker FROM ignorelist WHERE blocker = ? AND blocked = ? LIMIT 1");
    $db->execute(array(
        $id,
        $user_class->id
    ));
    if ($db->num_rows('You cannot send gifts to this user because they have you on their ignore list.'))
        diefun();


                    }
                    include 'footer.php';
                    ?>