<?php
include 'header.php';

// Function to handle item purchase and update user's points in the database
function purchaseItem($apoints, $user_class, $db){
    if ($user_class->apoints >= $apoints) {
        // Deduct points from the user's activity points
        $user_class->apoints -= $apoints; 

        // Update the user's activity points in the database
        $db->query("UPDATE grpgusers SET apoints = apoints - ? WHERE id = ?");
        $db->execute(array($apoints, $user_class->id));

        return true; // Indicate successful purchase
    } else {
        return false; // Indicate insufficient points
    }
}

// Define the available items in the store
$items = array(
    array("DE", "1 Double EXP Pill", 500),
    array("MS", "15 Maze Searches", 500),
    array("RT", "10 Raid Tokens", 1500),
    array("MNY", "$5,000,000 Money", 250),
    array("RSU", "1 Raid Speed Up Token", 250),
    array("PT", "10,000 Points", 500), // New item: 10,000 Points for 500 Activity Points
);

// Check if the 'buy' GET parameter is set
if(isset($_GET['buy'])){
    foreach($items as $item) {
        if($_GET['buy'] == $item[0]) {
            if(purchaseItem($item[2], $user_class, $db)) {
                // Handle the purchase based on item code
                switch ($item[0]) {
                    case 'DE':
               Give_Item(10, $user_class->id, 1);
                        $message = "1 Hour Double EXP pack";
                        break;
                    case 'MS':
                        $db->query("UPDATE grpgusers SET cityturns = cityturns + 15 WHERE id = ?");
                        $db->execute(array($user_class->id));
                        $message = "15 Maze Searches";
                        break;
                    case 'RT':
                        $db->query("UPDATE grpgusers SET raidtokens = raidtokens + 10 WHERE id = ?");
                        $db->execute(array($user_class->id));
                        $message = "10 Raid Tokens";
                        break;
                    case 'MNY':
                        $db->query("UPDATE grpgusers SET money = money + 5000000 WHERE id = ?");
                        $db->execute(array($user_class->id));
                        $message = "$5,000,000 Money";
                        break;
                    case 'RSU':
               Give_Item(194, $user_class->id, 1);
                        $message = "1 Raid Speed Up Token";
                        break;
                    case 'PT':
                        $db->query("UPDATE grpgusers SET points = points + 10000 WHERE id = ?");
                        $db->execute(array($user_class->id));
                        $message = "10,000 Points";
                        break;
                }

                // Confirm the purchase to the user
                echo "You have successfully purchased {$message} for {$item[2]} Activity Points.";
            } else {
                echo "You do not have enough Activity Points for this purchase.";
            }
            break; // Exit the loop once the item is found and processed
        }
    }
}

// Display the store items
echo '<div class="contenthead floaty">';
echo '<h4><center>You currently have <span style="color:#3ab997;font-weight:bold;">' . prettynum($user_class->apoints) . '</span> Activity Points</center></h4>';
echo '</div><center>Welcome to the Activity Store, here you can spend your Activity Points on various things.<br /><br />';
echo '<table id="newtables" style="width:100%;">';
echo '<tr><th>Item</th><th>Cost (Activity Points)</th><th>Action</th></tr>';

foreach($items as $item) {
    echo "<tr><td>{$item[1]}</td><td>" . prettynum($item[2]) . " Activity Points</td><td><a class='ycbutton' style='padding:2px 10px;' href='?buy={$item[0]}'>Buy Now</a></td></tr>";
}

echo '</table></center>';
include 'footer.php';
?>
