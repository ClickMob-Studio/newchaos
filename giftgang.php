<?php
include "header.php";
?>

<div class='box_top'>Gang Gifrs</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        // Define the gifts and their cost
        $gifts = array(
            //'Thank You' => 250,
            'Strawberry' => 500,
            'Pie' => 750,
            //'Band-Aid' => 1000,
            'Coffee' => 1250,
            'Beer' => 1900,
            //'Tissues' => 2500,
            'Feather' => 3750,
            'Kiss' => 5000,
            'Cigars' => 7500,
            'Dom Perignon' => 10000,
            'Crown Royal-XO' => 12500,
            'Red Rose' => 15000,
            'Black Rose' => 17500,
            'Engagement Ring' => 20000,
            'Watch' => 22500,
            'Diamond' => 25000
        );
        $plurals = array(
            'Strawberry' => 'Strawberries',
            'Tissues' => 'Tissues',
            'Kiss' => 'Kisses',
            'Cigars' => 'Cigars',
            'Watch' => 'Watches',
            'Jar of Peanut Butter' => 'Jars of Peanut Butter'
        );

        if (isset($_POST['itemname'])) {
            $qty = security($_POST['qty']);
            $index = $_POST['itemname'];

            // Get all members of that gang
            $db->query("SELECT id FROM grpgusers WHERE gang = ?");
            $db->execute([$user_class->gang]);
            $gangMembers = $db->fetch_row();
            $totalMembers = count($gangMembers);

            // Calculate the total cost
            $cost = $qty * $gifts[$index] * $totalMembers;

            if ($qty == 0) {
                diefun("Invalid quantity.");
            }
            if ($cost > $user_class->bank) {
                diefun("You do not have enough money in the bank to send these gifts.");
            } else {
                // Deduct the money
                $db->query("UPDATE grpgusers SET bank = bank - ? WHERE id = ?");
                $db->execute(array($cost, $user_class->id));
                $user_class->bank -= $cost;

                // Loop through each member and send the gift
                foreach ($gangMembers as $member) {
                    $memberId = $member['id'];

                    $db->query("INSERT INTO user_gifts VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE qty = qty + ?");
                    $db->execute(array($memberId, $index, $qty, $qty));

                    $note = (!empty($_POST['note'])) ? $_POST['note'] : "";
                    Send_event($memberId, "[-_USERID_-] sent you [x$qty] " . pluralize($index, $qty) . "! $note", $user_class->id);
                }

                echo Message('You have sent [x' . $qty . '] ' . pluralize($index, $qty) . ' to all members of your gang.');
            }
        }

        echo '<br />';
        echo '<span style="font-size:22px;">You are sending gifts to all members of your gang!</span>';
        echo '<br /><br />';
        echo '<table id="newtables" style="width:100%;">';
        foreach (array_chunk($gifts, 4, true) as $smgifts) {
            echo '<tr>';
            foreach ($smgifts as $name => $price) {
                echo '<td style="padding:8px;">';
                echo $name . ' (<span style="color:green;">' . prettynum($price, 1) . '</span> ea)<br />';
                echo '<img height="50" src="gifts/' . str_replace(' ', '', $name) . '.png" />';
                echo '<form method="post">';
                echo '<center>';
                echo 'Send: <input type="text" size="5" maxlength="5" name="qty" value="0" /><br /><br />';
                echo 'Note: <input type="text" size="15" name="note" placeholder="Send note with gift." /><br /><br />';
                echo '<input type="hidden" name="itemname" value="' . $name . '" />';
                echo '<input type="submit" value="Send!" />';
                echo '</center>';
                echo '</form>';
                echo '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
        include "footer.php";

        function pluralize($index, $num)
        {
            global $plurals;
            if ($num == 1)
                return $index;
            return (isset($plurals[$index])) ? $plurals[$index] : $index . 's';
        }
        ?>