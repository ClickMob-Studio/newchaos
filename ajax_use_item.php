<?php
include 'ajax_header.php';

$user_class = new User($_SESSION['id']);
$response = "";

if (isset($_GET['use'])) {
    $id = security($_GET['use']);
    $howmany = check_items($id);

    if ($howmany) {
        switch ($id) {
            case 4: // Awake Pill
                $db->query("UPDATE grpgusers SET awake = ? WHERE id = ?");
                $db->execute(array($user_class->maxawake, $user_class->id));
                $response = Message("You successfully used an awake pill to refill your awake to 100%.");
                break;

            case 8: // Mug Protection
                $timeAgo = time() - 900;
                if ($user_class->last_mug_time > $timeAgo) {
                    diefun('You have performed a mug in the last 15 minutes. You\'ll need to wait before you can take this protection.');
                }

                $itemDailyLimit = getItemDailyLimit($user_class->id);
                if ($itemDailyLimit['mug_protection'] >= 4) {
                    diefun('You can only use 4 mug protections per day.');
                }

                addItemDailyLimit($user_class, 'mug_protection');
                $db->query("UPDATE grpgusers SET mprotection = unix_timestamp() + 3600 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response = Message("You are now protected from mugs for 1 hour.");
                break;

            case 9: // Attack Protection
                $timeAgo = time() - 900;
                if ($user_class->last_attack_time > $timeAgo) {
                    diefun('You have performed an attack in the last 15 minutes. You\'ll need to wait before you can take this protection.');
                }

                $itemDailyLimit = getItemDailyLimit($user_class->id);
                if ($itemDailyLimit['attack_protection'] >= 4) {
                    diefun('You can only use 4 attack protections per day.');
                }

                addItemDailyLimit($user_class, 'attack_protection');
                $db->query("UPDATE grpgusers SET aprotection = unix_timestamp() + 3600, king = 0, queen = 0 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response = Message("You are now protected from attacks for 1 hour.");
                break;

            case 10: // Double EXP
                $db->query("UPDATE grpgusers SET exppill = unix_timestamp() + 3600 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response = Message("You will receive double exp on crimes for 1 hour.");
                break;

            case 196: // Night Vision
                $db->query("UPDATE grpgusers SET nightvision = nightvision + 15 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response = Message("You have added 15 minutes to your Night Vision!");
                break;

            case 168: // FBI Watch
                $db->query("UPDATE grpgusers SET fbi = fbi + 30 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response = Message("You are now being watched by the FBI for an extra 30 Minutes!.");
                break;

            case 169: // Escape FBI
                if ($user_class->fbitime == 0) {
                    diefun("You are currently not in Fed Jail!");
                }
                $db->query("UPDATE grpgusers SET fbitime = 0 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response = Message("You have used an Escape FBI Item and have now escaped!");
                break;

            case 13: // Minor Health Pill
            case 14: // Major Health Pill
                if ($user_class->purehp >= $user_class->puremaxhp && !$user_class->hospital) {
                    diefun("You already have full HP and are not in the hospital.");
                }

                if (in_array($user_class->hhow, ["bombed", "cbombed", "abombed"])) {
                    diefun("These won't help you when you are in bits.. you are going to have to wait it out.");
                }

                $db->query("SELECT * FROM items WHERE id = ?");
                $db->execute(array($id));
                $row = $db->fetch_row(true);
                $hosp = floor(($user_class->hospital / 100) * $row['reduce']);
                $newhosp = $user_class->hospital - $hosp;
                $newhosp = ($newhosp < 0) ? 0 : $newhosp;
                $hp = floor(($user_class->puremaxhp / 4) * $row['heal']);
                $hp = $user_class->purehp + $hp;
                $hp = ($hp > $user_class->puremaxhp) ? $user_class->puremaxhp : $hp;
                $db->query("UPDATE grpgusers SET hospital = ?, hp = ? WHERE id = ?");
                $db->execute(array($newhosp, $hp, $user_class->id));
                $response = Message("You successfully used a {$row['itemname']}.");
                break;

            case 27: // Meth
                druggie(0);
                $response = Message("You successfully used some Meth. Your speed has been increased for 15 minutes.");
                break;

            case 28: // Adrenaline
                druggie(1);
                $response = Message("You successfully used some Adrenaline. Your defense has been increased for 15 minutes.");
                break;

            case 29: // PCP
                druggie(2);
                $response = Message("You successfully used some PCP. Your strength has been increased for 15 minutes.");
                break;

            case 235: // Serenity Serum
                druggie(3);
                $response = Message("You successfully used some Serenity Serum. Your strength, defense and speed have been increased for 15 minutes.");
                break;

            case 196: // Night Vision
                $db->query("UPDATE grpgusers SET nightvision = nightvision + 15 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response = Message("You have added 15 minutes to your Night Vision!");
                break;

            case 168: // FBI Watch
                $db->query("UPDATE grpgusers SET fbi = fbi + 30 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response = Message("You are now being watched by the FBI for an extra 30 Minutes!.");
                break;

            case 169: // Escape FBI
                if ($user_class->fbitime == 0) {
                    diefun("You are currently not in Fed Jail!");
                }
                $db->query("UPDATE grpgusers SET fbitime = 0 WHERE id = ?");
                $db->execute(array($user_class->id));
                $response = Message("You have used an Escape FBI Item and have now escaped!");
                break;

            case 38: // Move to City
                if (empty($_GET['cityid'])) {
                    $db->query("SELECT id, name, levelreq FROM cities WHERE country = 1 ORDER BY levelreq DESC");
                    $db->execute();
                    $rows = $db->fetch_row();
                    $opts = "";
                    foreach ($rows as $row) {
                        $opts .= "<option value='{$row['id']}'>{$row['name']} (LVL: {$row['levelreq']})</option>";
                    }
                    echo '<form method="get">';
                    echo '<select name="cityid">';
                    echo $opts;
                    echo '</select>';
                    echo '<input type="hidden" name="use" value="38" />';
                    echo '<input type="submit" value="Move to City" />';
                    echo '</form>';
                    diefun();
                } else {
                    $cid = security($_GET['cityid']);
                    $db->query("SELECT * FROM cities WHERE id = ? AND pres = 0");
                    $db->execute(array($cid));
                    if ($db->fetch_row()) {
                        $db->query("UPDATE grpgusers SET city = ? WHERE id = ?");
                        $db->execute(array($cid, $user_class->id));
                        $response = Message("You have moved cities for free!");
                    } else {
                        diefun("City does not exist.");
                    }
                }
                break;

            default:
                $response = Message("Item not recognized or cannot be used.");
                break;
        }

        // After using the item, remove it from the user's inventory
        Take_Item($id, $user_class->id);
    } else {
        $response = "You don't have enough of that item.";
    }
} else {
    $response = "No item selected for use.";
}

// Output the response
echo $response;
?>
