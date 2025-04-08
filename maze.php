<style>
    .user-logs {
        margin-top: 20px;
        border-collapse: collapse;
        width: 100%;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .user-logs table {
        width: 100%;
        background-color: #222;
        /* Dark background */
    }

    .user-logs th,
    .user-logs td {

        padding: 8px 12px;
        text-align: left;
        color: #f5f5f5;
        /* Light text color */
    }

    .user-logs th {
        background-color: #333;
        /* Slightly lighter than the row color */
    }

    .user-logs tr:nth-child(even) {
        background-color: #444;
        /* Slightly darker for alternate rows */
    }

    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 50px;
        height: 50px;
        animation: spin 2s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .contenthead.floaty {
        text-align: center;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 8px;
    }
</style>
<?php
include 'header.php';
// if($user_class->admin < 1){

// exit;
// }
?>
<div class='box_top'>Maze</div>
<div class='box_middle'>
    <div class='pad'>

        <?php
        if ($user_class->cityturns <= 0) {
            echo "You have no turns left to search the streets!";
            include 'footer.php';
            exit;
        }

        $chosenDirection = "";
        // When user decides to search the street
        if (isset($_POST['direction'])) {
            $chosenDirection = $_POST['direction']; // Store the direction
            // Fetch all events from the citygame table
            $query = "SELECT * FROM citygame";
            $result = mysql_query($query);
            if (!$result) {
                die('Invalid query: ' . mysql_error());
            }
            // Create a weighted array
            $weightedEvents = [];
            while ($event = mysql_fetch_assoc($result)) {
                for ($i = 0; $i < $event['probability']; $i++) {
                    $weightedEvents[] = $event;
                }
            }
            // Randomly select an event from the weighted array
            $event = $weightedEvents[array_rand($weightedEvents)];

            $description = "";
            // Handle the event
            switch ($event['event_type']) {
                case 'text':
                    $description = $event['description_template'];
                    break;

                case 'money':
                    $money = rand($event['min_value'], $event['max_value']);
                    $description = str_replace('[money_amount]', "<span style='color: green;'>$" . $money . "</span>", $event['description_template']);
                    // Add the money to the user's account
                    $money_query = "UPDATE grpgusers SET money = money + $money WHERE id = " . $user_class->id;
                    mysql_query($money_query);
                    break;
                case 'credits':
                    $credits = rand($event['min_value'], $event['max_value']);
                    $description = str_replace('[credits_amount]', "<span style='color: yellow; font-weight: bold;'>" . $credits . " credits</span>", $event['description_template']);


                    // Log the event in user_logs table with the custom description
                    $logDescription = "Has found " . $credits . " Credits whilst searching downtown.";
                    $log_query = "INSERT INTO user_logs (user_id, event_type, description, timestamp) VALUES ('{$user_class->id}', 'credits', '{$logDescription}', UNIX_TIMESTAMP())";
                    mysql_query($log_query);


                    // Add the credits to the user's account
                    $credits_query = "UPDATE grpgusers SET credits = credits + $credits WHERE id = " . $user_class->id;
                    mysql_query($credits_query);
                    break;


                case 'shadyDealer':
                    $packageCost = 1000; // This is an example cost. Adjust as needed.
        
                    if ($user_class->bank >= $packageCost) {
                        // Deduct money from bank
                        $deductMoneyQuery = "UPDATE grpgusers SET bank = bank - $packageCost WHERE id = " . $user_class->id;
                        mysql_query($deductMoneyQuery);

                        // Randomly decide the outcome
                        $outcome = rand(1, 2); // 1 for Good Outcome, 2 for Trap Outcome
        
                        if ($outcome == 1) {
                            // Add rare item to inventory or give money/points
                            // This is just an example. Adjust based on your game's structure.
                            $description = "You received a rare item from the shady dealer!";
                        } else {
                            // Update player status to "in jail" or "in hospital"
                            $description = "It was a trap! The shady dealer set you up, and you've been sent to jail!";
                        }
                    } else {
                        $description = "You don't have enough money in your bank to engage with the shady dealer!";
                    }
                    break;

                case 'injuredStranger':
                    // Deduct turns for helping
                    $deductTurnsQuery = "UPDATE grpgusers SET cityturns = cityturns - 2 WHERE id = " . $user_class->id; // Deducting 2 turns as an example.
                    mysql_query($deductTurnsQuery);

                    // Randomly decide the outcome
                    $outcome = rand(1, 2); // 1 for Grateful Stranger, 2 for Deceitful Stranger
        
                    if ($outcome == 1) {
                        // Add reward to player's account or inventory
                        // This is just an example. Adjust based on your game's structure.
                        $description = "The injured stranger was genuinely in need and, as a token of gratitude, gave you a reward!";
                    } else {
                        // Deduct a random amount from player's money
                        $robAmount = rand(100, 500); // Random amount between 100 and 500 as an example.
                        $robMoneyQuery = "UPDATE grpgusers SET money = money - $robAmount WHERE id = " . $user_class->id;
                        mysql_query($robMoneyQuery);
                        $description = "It was a trap! The injured stranger was a con artist, and you were robbed!";
                    }
                    break;


                case 'raidtokens':
                    $raidtokens = rand($event['min_value'], $event['max_value']);
                    $description = str_replace('[raidtokens_amount]', "<span style='color: red; font-weight: bold;'>" . $raidtokens . " raid tokens</span>", $event['description_template']);

                    $logDescription = "Has found " . $raidtokens . " Raid Tokens whilst searching downtown.";
                    $log_query = "INSERT INTO user_logs (user_id, event_type, description, timestamp) VALUES ('{$user_class->id}', 'raidtokens', '{$logDescription}', UNIX_TIMESTAMP())";
                    mysql_query($log_query);

                    // Add the raid tokens to the user's account
                    $raidtokens_query = "UPDATE grpgusers SET raidtokens = raidtokens + $raidtokens WHERE id = " . $user_class->id;
                    mysql_query($raidtokens_query);
                    break;

                case 'points':
                    $points = rand($event['min_value'], $event['max_value']);
                    $description = str_replace('[points_amount]', $points, $event['description_template']);
                    // Add the points to the user's account
                    $points_query = "UPDATE grpgusers SET points = points + $points WHERE id = " . $user_class->id;
                    mysql_query($points_query);
                    break;

                case 'jail':
                    $logDescription = "Has landed in some trouble. They are on the way to Jail!.";
                    $log_query = "INSERT INTO user_logs (user_id, event_type, description, timestamp) VALUES ('{$user_class->id}', 'jail', '{$logDescription}', UNIX_TIMESTAMP())";
                    mysql_query($log_query);

                    $jailTime = rand($event['min_value'], $event['max_value']);
                    $description = "<strong style='color:red;'>" . $event['description_template'] . "</strong>";
                    $jail_query = "UPDATE grpgusers SET jail = jail + $jailTime WHERE id = " . $user_class->id;
                    mysql_query($jail_query);

                    echo json_encode(['redirect' => 'jail_page.php']);
                    exit;

                case 'hospital':
                    $hospitalTime = rand($event['min_value'], $event['max_value']);
                    $description = "<strong style='color:red;'>" . $event['description_template'] . "</strong>";
                    $hospital_query = "UPDATE grpgusers SET hospital = hospital + " . $hospitalTime . ", hhow = 'maze' WHERE id = " . $user_class->id;
                    mysql_query($hospital_query);

                    echo json_encode(['redirect' => 'hospital_page.php']);
                    exit;


                case 'item':
                    $item_query = "SELECT itemname FROM items WHERE id = " . $event['item_id'];
                    $item_result = mysql_query($item_query);
                    $item = mysql_fetch_assoc($item_result);

                    $description = str_replace('[item_name]', $item['itemname'], $event['description_template']);

                    // Log the event in user_logs table with the item name
                    $logDescription = "Has found a(n) " . $item['itemname'] . " whilst searching downtown.";
                    $log_query = "INSERT INTO user_logs (user_id, event_type, description, timestamp) VALUES ('{$user_class->id}', 'item', '{$logDescription}', UNIX_TIMESTAMP())";
                    mysql_query($log_query);
                    break;

                    // Add the item to the user's inventory. This step will depend on how you handle inventory in your game
                    break;

                // Add more cases as needed
            }

            // Deduct a turn from the user's cityturns
            $turns_query = "UPDATE grpgusers SET cityturns = cityturns - 1 WHERE id = " . $user_class->id;
            mysql_query($turns_query);

            // Display the description to the user
            echo "<p><strong>Search Result:</strong><br>";
            echo "You walked " . $chosenDirection . ".<br>"; // Display the chosen direction
            echo $description . "</p>";




            // Display remaining turns
            echo "<p>You have <strong>" . $user_class->cityturns . "</strong> turns left to search the streets.</p>";
        }


        $medPackHtml = '
<br /><br />
<center>
    <div id="med-pack-holder">
        <img src="css/images/NewGameImages/medi_cert.png" style="max-width: 75px;" class="img-responsive" /><br />
        <a class="ajax-link" href="ajax_med_cert.php?alv=yes">Use 100% Med Cert</a>
    </div>
</center> 
<br />
';

        // Display the compass buttons
        echo '

    <div class="contenthead floaty" style="width: 100%;">

        <div class="contenthead floaty" style="width: 88%;"> 
    <p>Here you can find Money,Points, Items and Gold whilst randomly searching.
    Below is the potential items you can find when searching, Displaying all different rarity types</p>
    <br>
    <span style="text-align:center"><a href="jail.php">Jail</a> | <a href="hospital.php">Hospital</a></span>
    ' . $medPackHtml . '
    </div>
    
     <div class="contenthead floaty" style="text-align: center; padding: 20px; margin-bottom: 20px; border-radius: 8px; width: 88%;">
    <br>
    <button class="direction-button" onclick="sendDirection(\'North\')">North</button><br><br>
    <button class="direction-button" onclick="sendDirection(\'West\')">West</button>
    <button class="direction-button" onclick="sendDirection(\'Search\')">Search</button>
    <button class="direction-button" onclick="sendDirection(\'East\')">East</button><br><br>
    <button class="direction-button" onclick="sendDirection(\'South\')">South</button>
</div>

<div class="contenthead floaty" style="text-align: center; padding: 20px; margin-bottom: 20px; border-radius: 8px;">
    <div id="searchFeedback">
        <div id="searchResult" class="alert alert-info" style="display: none;"></div>
        <div id="remainingTurns"></div>
    </div>

    <div class="spinner" style="display: none;">
        <div class="loader"></div>
    </div>
</div>

<div style="justify-content: center; gap: 20px;"> <!-- Flex container for both tables -->

    <!-- First table for Common items -->
    <div class="contenthead floaty common" style="padding: 20px; margin-bottom: 20px; border-radius: 8px;">
        <h1><font color=blue>Common Items</font></h1>
        <div style="display: flex; justify-content: center; gap: 20px;"> <!-- Flex container for item spacing -->
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 255, 255, 0.6); border-radius: 8px;">
                <img src="/diamondstone.png" width="50" height="50" alt="Diamond Stone">
            </div>
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 255, 255, 0.6); border-radius: 8px;">
                <img src="/ruby.jpg" width="50" height="50" alt="Ruby">
            </div>
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 255, 255, 0.6); border-radius: 8px;">
                <img src="/emeraldstone.png" width="50" height="50" alt="Emerald Stone">
            </div>
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 255, 255, 0.6); border-radius: 8px;">
                <img src="/sapphire.png" width="50" height="50" alt="Sapphire">
            </div>
        </div>
    </div>

    <!-- Second table for Uncommon items -->
    <div class="contenthead floaty uncommon" style="padding: 20px; margin-bottom: 20px; border-radius: 8px;">
        <h1><font color=green>Uncommon Items</font></h1>
        <div style="display: flex; justify-content: center; gap: 20px;"> <!-- Similar setup for Uncommon items -->
            <!-- Uncommon item images go here, similar to the common items section -->
            <!-- Example: -->
             <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(0, 255, 0, 0.6); border-radius: 8px;">
                <img src="/diamondstone22.png" width="50" height="50" alt="Diamond Stone">
            </div>
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(0, 255, 0, 0.6); border-radius: 8px;">
                <img src="/ruby2.png" width="50" height="50" alt="Ruby">
            </div>
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(0, 255, 0, 0.6); border-radius: 8px;">
                <img src="/emeraldstone2.png" width="50" height="50" alt="Emerald Stone">
            </div>
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(0, 255, 0, 0.6); border-radius: 8px;">
                <img src="/sapphirestone2.png" width="50" height="50" alt="Sapphire">
            </div>
        </div>
    </div>
</div>
<div style="justify-content: center; gap: 20px;"> <!-- Flex container for both tables -->

    <!-- First table for Common items -->
    <div class="contenthead floaty rare" style="padding: 20px; margin-bottom: 20px; border-radius: 8px;">
        <h1><font color=gold>Rare Item Finds</font></h1>
        <div style="display: flex; justify-content: center; gap: 20px;"> <!-- Flex container for item spacing -->
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 215, 0, 0.6); border-radius: 8px;">
                <img src="css/images/NewGameImages/mugprotection.png" width="50" height="50" alt="Diamond Stone">
            </div>
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 215, 0, 0.6); border-radius: 8px;">
                <img src="css/images/NewGameImages/attackprotection.png" width="50" height="50" alt="Ruby">
            </div>
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 215, 0, 0.6); border-radius: 8px;">
                <img src="css/images/NewGameImages/doubleexp.png" width="50" height="50" alt="Emerald Stone">
            </div>
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 215, 0, 0.6); border-radius: 8px;">
                <img src="css/images/NewGameImages/exoticbooster.png" width="50" height="50" alt="Sapphire">
            </div>
        </div>
        <br />
         <div style="display: flex; justify-content: center; gap: 20px;"> <!-- Flex container for item spacing -->
         <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 215, 0, 0.6); border-radius: 8px;">
                <img src="css/images/NewGameImages/metal.png" width="50" height="50" alt="Metal">
            </div>
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 215, 0, 0.6); border-radius: 8px;">
                <img src="css/images/NewGameImages/wood.png" width="50" height="50" alt="Wood">
            </div>
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 215, 0, 0.6); border-radius: 8px;">
                <img src="css/images/NewGameImages/leather.png" width="50" height="50" alt="Leather">
            </div>
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 215, 0, 0.6); border-radius: 8px;">
                <img src="css/images/NewGameImages/cpu.png" width="50" height="50" alt="CPU">
            </div>
         </div>
         <br />
         <div style="display: flex; justify-content: center; gap: 20px;"> <!-- Flex container for item spacing -->
         <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 215, 0, 0.6); border-radius: 8px;">
                <img src="css/images/NewGameImages/plastic.png" width="50" height="50" alt="Plastic">
            </div>
         </div>
    </div>

    <!-- Second table for Uncommon items -->
    <div class="contenthead floaty super-rare" style="padding: 20px; margin-bottom: 20px; border-radius: 8px;">
        <h1><font color=red>Super Rare Finds</font></h1>
        <div style="display: flex; justify-content: center; gap: 20px;"> <!-- Similar setup for Uncommon items -->
            <!-- Uncommon item images go here, similar to the common items section -->
            <!-- Example: -->
             <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 0, 0, 0.6); border-radius: 8px;">
                <img src="css/images/NewGameImages/heroicbooster.png" width="50" height="50" alt="Diamond Stone">
            </div>
           
            <div class="item-container" style="padding: 10px; box-shadow: 0 0 15px rgba(255, 0, 0, 0.6); border-radius: 8px;">
                <img src="images/raidspeedup.png" width="50" height="50" alt="Emerald Stone">
            </div>
            
        </div>
    </div>
</div>


 
';




        echo "<style>
/* Style for new tables */
#newtables, #usertables {
    width: 90%;
    max-width: 1000px;
    border-collapse: collapse;
    margin: 20px auto;
    padding: 0;
  
    font-family: 'Arial', sans-serif;
}

#newtables th, #usertables th {

    padding: 10px;
    text-align: left;
    font-weight: normal;
    border-bottom: 1px solid #555;
}

#newtables td, #usertables td {
    padding: 10px;
    border-bottom: 1px solid #444;
}

/* Responsive table adjustments */
@media (max-width: 768px) {
    #newtables, #usertables {
        width: 100%;
    }

    #newtables th, #newtables td, #usertables th, #usertables td {
        display: block;
        width: 100%;
    }

    #newtables th, #usertables th {
        text-align: right;
    }

    #newtables td, #usertables td {
        text-align: left;
        border-bottom: 0;
    }
}

/* Style for tabs */
.tab {
  overflow: hidden;

  text-align: center; /* Center the tabs */
}


.tabcontent {
  display: none;
  padding: 6px 12px;
  border-top: none;
 
  color: #000; /* Text color for the content */
}
</style>";

        echo "<div class='tab'>
  <button class='tablinks' onclick=\"openTab(event, 'Timestamp')\">Everyones Logs</button>
  <button class='tablinks' onclick=\"openTab(event, 'UserID')\">Your Personal Finds</button>
</div>";

        // Tab content for logs ordered by timestamp
        echo "<div id='Timestamp' class='tabcontent'>
      <div class='contenthead floaty'><h1>Everyones Logs</h1>";
        $query = "SELECT * FROM user_logs ORDER BY timestamp DESC LIMIT 10";
        $result = mysql_query($query);
        echo "<table id='newtables'>";
        echo "<tr><th>Timestamp</th><th>User</th><th>Description</th></tr>";
        while ($log = mysql_fetch_assoc($result)) {
            $username = formatName($log['user_id']);
            $timeAgo = howlongago($log['timestamp']);
            echo "<tr><td>{$timeAgo}</td><td>{$username}</td><td>{$log['description']}</td></tr>";
        }
        echo "</table></div></div>";

        // Tab content for logs ordered by user_id
        echo "<div id='UserID' class='tabcontent'>
      <div class='contenthead floaty'><h1>Your Maze Finds</h1>";
        $query2 = "SELECT * FROM user_logs WHERE user_id = '{$user_class->id}' ORDER BY timestamp DESC LIMIT 10";
        $result2 = mysql_query($query2);
        echo "<table id='usertables'>";
        echo "<tr><th>timestamp</th><th>User</th><th>Description</th></tr>";
        while ($log = mysql_fetch_assoc($result2)) {
            $username = formatName($log['user_id']);
            $timeAgo = howlongago($log['timestamp']);
            echo "<tr><td>{$timeAgo}</td><td>{$username}</td><td>{$log['description']}</td></tr>";
        }
        echo "</table></div></div>";

        echo "<script>
function openTab(evt, tabName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName('tabcontent');
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = 'none';
  }
  tablinks = document.getElementsByClassName('tablinks');
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(' active', '');
  }
  document.getElementById(tabName).style.display = 'block';
  evt.currentTarget.className += ' active';
}
document.addEventListener('DOMContentLoaded', function() {
  document.querySelector('.tablinks').click();
});
</script>";
        ?>

    </div>
</div>

<?php
include 'footer.php';
?>

<script>
    $(document).ready(function () {
        console.log("Document ready");
        //updateLogs(); // Initialize log updates
    });

    <?php if ($user_class->hospital > 0): ?>
        $('#med-pack-holder').show();
    <?php else: ?>
        $('#med-pack-holder').hide();
    <?php endif; ?>

    function updateLogs() {
        $.ajax({
            url: 'fetch_logs.php',
            cache: false,
            success: function (data) {
                console.log("Logs data:", data); // Debugging console log
                $('.user-logs').html(data); // Update the log display area
            },
            complete: function () {
                setTimeout(updateLogs, 1000); // Schedule the next update after 1 second
            },
            error: function (jqXHR, textStatus, errorThrown) {
                console.error("Error fetching logs:", textStatus, errorThrown); // Log errors to the console
            }
        });
    }

    let canClick = true; // Flag to control button click frequency

    function sendDirection(chosenDirection) {
        if (!canClick) return; // Prevent function execution if a click has recently been processed
        canClick = false; // Disable further clicks
        toggleDirectionButtons(true); // Disable direction buttons immediately

        document.querySelector("#searchFeedback").style.display = "none"; // Hide feedback section
        document.querySelector(".spinner").style.display = "block"; // Show loading spinner

        setTimeout(function () { // Correctly start the setTimeout function here
            var formData = new FormData();
            formData.append('direction', chosenDirection);

            fetch('process_maze_event.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    document.querySelector(".spinner").style.display = "none";

                    if (data.error) {
                        $('#searchResult').show();
                        document.querySelector("#searchResult").textContent = data.error;
                        document.querySelector("#remainingTurns").textContent = "";
                    } else {
                        // Display the new results
                        $('#searchResult').show();
                        document.querySelector("#searchResult").innerHTML = "You walked " + (data.direction || "unknown") + ".<br>" + data.description;
                        document.querySelector("#remainingTurns").textContent = (data.turnsLeft || "unknown");
                    }

                    if (data.hospitalTime > 0) {
                        $('#med-pack-holder').show();
                    } else {
                        $('#med-pack-holder').hide();
                    }

                    document.querySelector("#searchFeedback").style.display = "block";
                })
                .finally(() => {
                    setTimeout(() => {
                        canClick = true; // Re-enable clicks after a delay
                        toggleDirectionButtons(false); // Re-enable direction buttons
                    }, 500); // Wait for 3 seconds before re-enabling clicks and buttons
                });
        }, 200); // Wait 1 second before executing the request
    }

    function toggleDirectionButtons(disable) {
        document.querySelectorAll('.direction-button').forEach(button => {
            button.disabled = disable; // Disable or enable buttons based on the 'disable' parameter
        });
    }

    // Add your additional script here
    // You can paste your additional script below this line.
</script>