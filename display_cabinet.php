<?php
include 'header.php';
?>
<div class='box_top'>Display Cabinet</div>
<div class='box_middle'>
    <div class='pad'>
        <?php

        $notification = "";  // this will store our notification message
        
        // Ensure a connection to the database is established
        
        // Determine which user's display cabinet to show
        $viewing_userid = isset($_GET['userid']) ? intval($_GET['userid']) : $user_class->id;

        // Handle the form submissions (Add/Remove items)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Handle removal
            if (isset($_POST['remove_item_id'])) {
                $remove_item_id = intval($_POST['remove_item_id']);
                $remove_quantity = isset($_POST['remove_quantity']) ? intval($_POST['remove_quantity']) : 1;

                // Check current quantity in display_cabinet
                $check_query = sprintf("SELECT quantity FROM display_cabinet WHERE itemid = %d AND userid = %d", $remove_item_id, $viewing_userid);
                $db->query("SELECT quantity FROM display_cabinet WHERE itemid = ? AND userid = ?");
                $db->execute([$remove_item_id, $viewing_userid]);

                $result = $db->fetch_row(true);
                $current_quantity = $result['quantity'];
                if ($remove_quantity <= $current_quantity) {
                    // Decrease the quantity in the display_cabinet
                    $db->query("UPDATE display_cabinet SET quantity = quantity - ? WHERE itemid = ? AND userid = ?");
                    $db->execute([$remove_quantity, $remove_item_id, $viewing_userid]);

                    $db->query("INSERT INTO transactions (userid, action, itemid, quantity) VALUES (?, 'Removed from Display Cabinet', ?, ?)");
                    $db->execute([$viewing_userid, $remove_item_id, $remove_quantity]);


                    $db->query("DELETE FROM display_cabinet WHERE quantity <= 0 AND itemid = ? AND userid = ?");
                    $db->execute([$remove_item_id, $viewing_userid]);

                    Give_Item($remove_item_id, $viewing_userid, $remove_quantity);

                    $notification = "Successfully removed item from cabinet!";
                } else {
                    $notification = "You don't have enough of that item in your cabinet to remove.";
                }
            }

            // Handle addition
            if (isset($_POST['add_item_id'])) {
                $add_item_id = intval($_POST['add_item_id']);
                $add_quantity = intval($_POST['add_quantity']);

                // Check if the user has enough of the item in inventory before adding
                $quantity = Check_Item($add_item_id, $viewing_userid);
                if ($quantity >= $add_quantity) {
                    // Decrease the quantity in the inventory
                    Take_Item($add_item_id, $viewing_userid, $add_quantity);

                    $db->query("INSERT INTO transactions (userid, action, itemid, quantity) VALUES (?, 'Added to Display Cabinet', ?, ?)");
                    $db->execute([$viewing_userid, $add_item_id, $add_quantity]);

                    $db->query("INSERT INTO display_cabinet (itemid, userid, quantity) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE quantity = quantity + ?");
                    $db->execute([$add_item_id, $viewing_userid, $add_quantity, $add_quantity]);

                    $notification = "Successfully added item to cabinet!";
                } else {
                    $notification = "You don't have enough of that item in your inventory!";
                }
            }
        }

        // Fetch items from the display cabinet for the specified user
        $query = sprintf("SELECT dc.*, i.itemname, i.image FROM display_cabinet dc JOIN items i ON dc.itemid = i.id WHERE dc.userid = ?", $viewing_userid);
        $db->query("SELECT dc.*, i.itemname, i.image FROM display_cabinet dc JOIN items i ON dc.itemid = i.id WHERE dc.userid = ?");
        $db->execute([$viewing_userid]);
        $cabinet_items = $db->fetch_row();

        // If it's the user's own cabinet, fetch their inventory items for addition
        $inventory_items = array();
        if ($viewing_userid == $user_class->id) {
            $db->query("SELECT inv.*, i.itemname FROM inventory invJOIN items i ON inv.itemid = i.id WHERE inv.userid = ?");
            $db->execute([$viewing_userid]);
            $inventory_items = $db->fetch_row();

            if (empty($inventory_items)) {
                echo "<p class='debug-message'>Debug: No items in user's inventory.</p>";
            } else {
                echo "<p class='debug-message'>Debug: " . count($inventory_items) . " items fetched from user's inventory.</p>";
            }
        }


        ?>
        <style>
            /* Main content style */
            .main-content {

                border: 2px solid #666;
                /* Border around the main content */
                border-radius: 10px;
                /* Rounded corners */
                padding: 20px;
                /* Some spacing inside */
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.5);
                /* Add a shadow for depth */
            }

            /* Cabinet items section style */
            .cabinet-items-section {
                display: flex;
                flex-wrap: wrap;
                gap: 20px;
                /* Space between items */
            }

            /* Individual cabinet item style */
            .cabinet-item {
                flex-basis: calc(25% - 20px);
                background-color: #444;
                border: 2px solid #555;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.3);

                display: flex;
                flex-direction: column;
                /* This makes the content stack vertically */
                justify-content: center;
                /* This will vertically center the image and text */
                align-items: center;
                /* This will horizontally center the image and text */
                padding: 10px;
                /* Padding around the container */
            }

            /* Image style */
            .cabinet-item img {
                display: block;
                max-width: 90%;
                /* reduce it a bit for some space */
                height: auto;
                margin-bottom: 10px;
                /* Adds space below the image */
            }

            /* Item details style */
            .item-details {
                padding: 10px;
                text-align: center;
            }

            /* Add and Remove sections */
            .add-to-cabinet,
            .remove-from-cabinet {
                margin-top: 20px;
                background-color: #444;
                padding: 15px;
                border-radius: 10px;
                border: 2px solid #555;
            }

            /* Transaction table container */
            .transaction-table-container {
                margin-top: 30px;
                /* Some space from the previous content */
                padding: 20px;
                background-color: #2a2a2a;
                /* Darker background for the table container */
                border-radius: 10px;
                box-shadow: 0 0 10px 3px rgba(255, 255, 255, 0.1);
                /* Slight glow around the table */
                overflow: hidden;
                /* In case the table extends beyond the border-radius */
            }

            /* Transaction table styles */
            .transaction-table {
                width: 100%;
                border-collapse: collapse;
                /* Ensures borders don't double up */
            }

            .transaction-table th,
            .transaction-table td {
                padding: 10px 15px;
                /* Space inside cells */
                border-bottom: 1px solid #444;
                /* Border between rows */
                text-align: left;
            }

            .transaction-table th {
                color: #eee;
                /* Lighter text for headers */
                font-weight: bold;
            }

            .transaction-table tr:last-child td {
                border-bottom: none;
                /* Remove border for the last row to make it clean */
            }

            .transaction-table tr:hover {
                background-color: #3a3a3a;
                /* Slight change in background on hover for rows */
            }

            /* If you have action buttons or links inside the table, style them here */
            .transaction-table a {
                color: #56aaff;
                /* Example link color */
                text-decoration: none;
                /* Removes underline */
            }

            .transaction-table a:hover {
                text-decoration: underline;
                /* Underline on hover */
            }
        </style>

        <div class="main-content">

            <h2>Display Cabinet</h2>
            <div class="intro-section">
                <p>Welcome to the Display Cabinet! Here, players showcase their prized possessions. Dive in and
                    discover!</p>
            </div>

            <!-- Notification Display -->
            <?php if (!empty($notification)): ?>
                <div class="notification">
                    <?php echo $notification; ?>
                </div>
            <?php endif; ?>
            <div class="cabinet-items-section">
                <?php
                if (empty($cabinet_items)) {
                    echo "<p class='empty-message'>No items displayed in this cabinet.</p>";
                } else {
                    foreach ($cabinet_items as $item) {
                        ?>
                        <div class="cabinet-item">
                            <img src="<?php echo $item['image']; ?>" width="100" height="100">
                            <div class="item-details">
                                <?php echo $item['itemname']; ?> (x<?php echo $item['quantity']; ?>)
                            </div>
                        </div>
                        <?php
                    }

                    // If it's the user's own cabinet, show the removal dropdown
                    if ($viewing_userid == $user_class->id) {
                        echo "<div class='remove-from-cabinet'>";
                        echo "<form action='display_cabinet.php' method='post'>";
                        echo "<select name='remove_item_id'>";
                        foreach ($cabinet_items as $item) {
                            echo "<option value='" . $item['itemid'] . "'>" . $item['itemname'] . " (x" . $item['quantity'] . ")</option>";
                        }
                        echo "</select>";

                        // Quantity dropdown
                        echo "<select name='remove_quantity'>";
                        for ($i = 1; $i <= 10; $i++) {  // You can adjust the maximum quantity
                            echo "<option value='$i'>$i</option>";
                        }
                        echo "</select>";

                        echo "<input type='submit' value='Remove'>";
                        echo "</form>";
                        echo "</div>";
                    }
                }

                // If user's own cabinet, show dropdown to add items
                if ($viewing_userid == $user_class->id && !empty($inventory_items)) {
                    echo "<div class='add-to-cabinet'>";
                    echo "<form action='display_cabinet.php' method='post'>";
                    echo "<select name='add_item_id'>";
                    foreach ($inventory_items as $item) {
                        echo "<option value='" . $item['itemid'] . "'>" . $item['itemname'] . " (x" . $item['quantity'] . ")</option>";
                    }
                    echo "</select>";
                    echo "<select name='add_quantity'>";
                    for ($i = 1; $i <= 10; $i++) {
                        echo "<option value='$i'>$i</option>";
                    }
                    echo "</select>";
                    echo "<input type='submit' value='Add to Cabinet'>";
                    echo "</form>";
                    echo "</div>";
                }

                // Fetch transaction history for the user
                $db->query("SELECT t.*, i.itemname FROM transactions t JOIN items i ON t.itemid = i.id WHERE t.userid = ? ORDER BY t.timestamp DESC");
                $db->execute([$viewing_userid]);
                $transactions = $db->fetch_row();

                // Display the transactions in a table
                echo "<div class='transaction-table'>";
                echo "<h3>Your Transaction History</h3>";
                echo "<table>";
                echo "<thead>";
                echo "<tr><th>Action</th><th>Item</th><th>Quantity</th><th>Date</th></tr>";
                echo "</thead>";
                echo "<tbody>";
                foreach ($transactions as $transaction) {
                    echo "<tr>";
                    echo "<td>" . $transaction['action'] . "</td>";
                    echo "<td>" . $transaction['itemname'] . "</td>";
                    echo "<td>" . $transaction['quantity'] . "</td>";
                    echo "<td>" . $transaction['timestamp'] . "</td>";
                    echo "</tr>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
                ?>
            </div>

            <?php
            include 'footer.php';
            ?>