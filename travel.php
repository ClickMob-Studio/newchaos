<?php
include 'header.php';
$userPrestigeSkills = getUserPrestigeSkills($user_class);
$goldenTicketCount = Check_Item(38, $user_class->id);
if ($goldenTicketCount > 0) {
    if (isset($_GET['ticket'])) {
        echo '<div class="dcPanel p-3 mt-2" style="text-align:center">You are using a ' . item_popup('Golden Ticket', 38) . ' which enables you to travel for free. <b><a href="travel.php">Travel Normally</a></b></div>';
    } else {
        echo '<div class="dcPanel p-3 mt-2" style="text-align:center">You have a ' . item_popup('Golden Ticket', 38) . ' which enables you to travel for free. <b><a href="travel.php?ticket=1">Use Ticket</a></b></div>';
    }
}
?>


<div class='box_top'>Travel</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        if ($user_class->jail > 0)
            diefun("You can't travel while in Prison!");
        if ($user_class->hospital > 0)
            diefun("You can't travel while in Hospital!");

        $nightvision_level = $user_class->nightvision;

        // Initialize discount
        $db->query("SELECT discount FROM carlot c JOIN usercars ON carid = c.id WHERE userid = ? ORDER BY carid DESC LIMIT 1");
        $db->execute([$user_class->id]);
        $mydiscount = $db->fetch_single();
        $discount = 100 - (isset($mydiscount) ? $mydiscount : 0);

        // Apply nightvision discount if applicable
        if ($nightvision_level > 0 && ($goldenTicketCount <= 0 && !isset($_GET['ticket']))) {
            $discount -= 50; // additional 50% discount, subtract from 100%
            echo "Notification: Your nightvision ability has granted you a 50% discount on travel costs!<br>";
        }


        if (isset($_GET['go'])) {
            security($_GET['go']);
            $error = ($_GET['go'] == $user_class->city) ? "You are already there." : null;

            $db->query("SELECT * FROM cities WHERE id = ?");
            $db->execute([$_GET['go']]);
            $worked = $db->fetch_row(true);
            if (empty($worked)) {
                diefun("That city doesn't exist.");
            }

            if ($worked['pres'] && !$user_class->prestige)
                diefun("You do not have access to this city.");
            $cost = $worked['price'] * ($discount / 100);
            if ($userPrestigeSkills['travel_cost_unlock'] > 0) {
                $cost = $cost - ($cost / 100 * 20);
            }

            if ($goldenTicketCount > 0 && isset($_GET['ticket'])) {
                $cost = 0; // Free travel with Golden Ticket 
            }

            $error = ($worked['name'] == "") ? "That city doesn't exist." : $error;
            $error = ($user_class->level < $worked['levelreq']) ? "You are not a high enough level to go there!." : $error;
            $error = ($user_class->prestige < $worked['pres']) ? "You are not a high enough Prestige to go there!." : $error;

            $error = ($user_class->country != $worked['country']) ? "That city isn't in this country." : $error;
            $error = ($user_class->money < $cost) ? "You dont have enough money to go there." : $error;
            $error = ($worked['rmonly'] == 1 && $user_class->rmdays < 1) ? "You need to be a Respected Soldier to go there." : $error;

            if (!isset($error)) {
                $newmoney = $user_class->money - $cost;

                if ($goldenTicketCount > 0 && isset($_GET['ticket'])) {
                    Take_Item(38, $user_class->id, 1); // Remove one Golden Ticket
                }

                perform_query("UPDATE grpgusers SET city = ?, king = 0, queen = 0, money = ? WHERE id = ?", [$_GET['go'], $newmoney, $user_class->id]);
                echo Message("You successfully paid " . prettynum($cost, 1) . (($goldenTicketCount && isset($_GET['ticket'])) ? ", and a Golden Ticket" : "") . " for a Plane ticket and got to your destination.");
            } else
                echo Message($error);
        }
        echo "<style type='text/css'>
.rm {
   vertical-align:3px;
   font-weight:600;
}
</style>
<style>
    .small-column {
        width: 10%;
    }
    .large-column {
        width: 50%;
    }
</style>
<p>If you are holding a Boss Or Under Boss slot and travel out of the city you hold it in you will loose that spot!</p>
<table id='newtables' style='width:100%;table-layout:fixed;'>
    <tr>
    <th class='small-column'>Name</th>
    <th class='small-column'>Cost</th>
    <th class='small-column'>Level Required</th>
    <th class='small-column'>Population</th>
    <th class='small-column'>Boss</th>
    <th class='small-column'>Under Boss</th>
    </tr>";

        $db->query("SELECT * FROM cities WHERE country = ? AND id != 24 ORDER BY pres ASC, levelreq ASC");
        $db->execute([$user_class->country]);
        $cities = $db->fetch_row();

        foreach ($cities as $line) {
            $db->query("SELECT COUNT(*) FROM grpgusers WHERE city = ?");
            $db->execute([$line['id']]);
            $population = $db->num_rows();

            // Get King and Queen of the city
            $db->query("SELECT id, username FROM grpgusers WHERE  king = ?");
            $db->execute([$line['id']]);
            $king = $db->fetch_row(true);

            $db->query('SELECT id, username FROM grpgusers WHERE queen = ?');
            $db->execute([$line['id']]);
            $queen = $db->fetch_row(true);

            $king_status = isset($king) ? " " . formatName($king['id']) : 'Vacant';
            $queen_status = isset($queen) ? " " . formatName($queen['id']) : 'Vacant';

            $cost = $line['price'] * ($discount / 100);
            if ($userPrestigeSkills['travel_cost_unlock'] > 0) {
                $cost = $cost - ($cost / 100 * 20);
            }

            if ($goldenTicketCount > 0 && isset($_GET['ticket'])) {
                $cost = 0; // Free travel with Golden Ticket 
            }

            if ($line['rmonly'] == 1) {
                echo "<tr><td><a href='travel.php?go={$line['id']}" . (isset($_GET['ticket']) && $goldenTicketCount > 0 ? "&ticket=1" : "") . "'>{$line['name']}</a> <a href='rmstore.php' class='rm' style='color:yellow;'>RY ONLY</a></td><td>" . prettynum($cost, 1) . "</td><td>{$line['levelreq']}</td><td>{$line['pres']}</td><td>$population</td><td>$king_status</td><td>$queen_status</td></tr>";
            } else {
                echo "<tr><td><a href='travel.php?go={$line['id']}" . (isset($_GET['ticket']) && $goldenTicketCount > 0 ? "&ticket=1" : "") . "'>{$line['name']}</a></td><td>" . prettynum($cost, 1) . "</td><td>{$line['levelreq']}</td><td>$population</td><td>$king_status</td><td>$queen_status</td></tr>";
            }
        }
        echo "</table>";
        include 'footer.php';
        ?>