<?php
include 'header.php';

// Function to calculate remaining time as array(days, hours, minutes, seconds)
function calculateRemainingTime($remainingTimeInSeconds) {
    $days = floor($remainingTimeInSeconds / 86400);
    $remainingTimeInSeconds %= 86400;
    $hours = floor($remainingTimeInSeconds / 3600);
    $remainingTimeInSeconds %= 3600;
    $minutes = floor($remainingTimeInSeconds / 60);
    $seconds = $remainingTimeInSeconds % 60;

    return array($days, $hours, $minutes, $seconds);
}

/// Function to format remaining time as a string (e.g., "7 days 14 hours 15 minutes 24 seconds")
function formatCountdown($remainingTimeInSeconds) {
    if ($remainingTimeInSeconds < 0) {
        return "00:00:00";
    }

    list($days, $hours, $minutes, $seconds) = calculateRemainingTime($remainingTimeInSeconds);
    $formattedTime = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);

    if ($days > 0) {
        $formattedTime = "{$days} days {$formattedTime}";
    }

    return $formattedTime;
}
// Function to handle skipping cooldown
function skipCooldown($user_id) {
    global $db, $user_class;

    // Check if the user has enough credits to skip the cooldown
    if ($user_class->credits >= 50) {
        // Calculate the new timestamp for current time (set to 0 to reset the cooldown)
        $newTimeTill = 0;

        // Update pack1timetill with the new timestamp (set to 0 to reset the cooldown)
        $db->query("UPDATE grpgusers SET pack1timetill = ? WHERE id = ?");
        $db->execute(array($newTimeTill, $user_id));

        // Deduct 50 credits from the user's account
        $db->query("UPDATE grpgusers SET credits = credits - 50 WHERE id = ?");
        $db->execute(array($user_id));

        // Update the user's selected pack in the session (set to 0 to reset the cooldown)
        $_SESSION['user_class']->pack1timetill = $newTimeTill;

        return true;
    } else {
        return false;
    }
}
// Handle skipping cooldown if the "skip_cooldown" parameter is present in the URL
if (isset($_GET['skip_cooldown'])) {
    $cooldownSkipped = skipCooldown($user_class->id);
    if ($cooldownSkipped) {
        $message = "You have paid 50 Credits to skip the cooldown. You can now change packs immediately!";
    } else {
        $timeRemaining = formatCountdown($user_class->pack1timetill - time());
        $message = "You still have time remaining to change packs. You can't change packs again until {$timeRemaining}.";
    }
}

if (isset($_GET['buy'])) {
    $selected_pack = $_GET['buy'];

    // Check if the user has changed packs in the last 24 hours
    if ($user_class->pack1time < time()) {
        $timeRemaining = formatCountdown($user_class->pack1timetill - time());
        $message = "You do not have an active membership.";
    } elseif ($user_class->pack1timetill > time()) {
        $timeRemaining = formatCountdown($user_class->pack1timetill - time());
        $message = "You can't change packs again until $timeRemaining. <br><a href='pacts.php?skip_cooldown=1'>Reset remaining time for 50 credits</a>";
    } else {
        // Perform the pack change and update pack1timetill
        if ($selected_pack === "FirstSet1" && $user_class->id >= 0) {
            $newTimeTill = time() + 86400; // 24 hours from now
            $db->query("UPDATE grpgusers SET pack1 = 1, pack1timetill = ? WHERE id = ?");
            $db->execute(array(
                $newTimeTill,
                $user_class->id
            ));

            // Update the user's selected pack in the session
            $_SESSION['user_class']->pack1 = 1;

            $message = "You Changed to the Gambino Pact!";
        } elseif ($selected_pack === "FirstSet2" && $user_class->id >= 0) {
            $newTimeTill = time() + 86400; // 24 hours from now
            $db->query("UPDATE grpgusers SET pack1 = 2, pack1timetill = ? WHERE id = ?");
            $db->execute(array(
                $newTimeTill,
                $user_class->id
            ));

            // Update the user's selected pack in the session
            $_SESSION['user_class']->pack1 = 2;

            $message = "You Changed to the Luciano Pact!";
        } elseif ($selected_pack === "FirstSet3" && $user_class->id >= 0) {
            $newTimeTill = time() + 86400; // 24 hours from now
            $db->query("UPDATE grpgusers SET pack1 = 3, pack1timetill = ? WHERE id = ?");
            $db->execute(array(
                $newTimeTill,
                $user_class->id
            ));

            // Update the user's selected pack in the session
            $_SESSION['user_class']->pack1 = 3;

            $message = "You Changed to the Bonanno Pact!";
        } elseif ($selected_pack === "FirstSet4" && $user_class->id >= 0) {
            $newTimeTill = time() + 86400; // 24 hours from now
            $db->query("UPDATE grpgusers SET pack1 = 4, pack1timetill = ? WHERE id = ?");
            $db->execute(array(
                $newTimeTill,
                $user_class->id
            ));

            // Update the user's selected pack in the session
            $_SESSION['user_class']->pack1 = 4;

            $message = "You Changed to the Genovese Pact!";
        } elseif ($selected_pack === "FirstSet5" && $user_class->id >= 0) {
            $newTimeTill = time() + 86400; // 24 hours from now
            $db->query("UPDATE grpgusers SET pack1 = 5, pack1timetill = ? WHERE id = ?");
            $db->execute(array(
                $newTimeTill,
                $user_class->id
            ));

            // Update the user's selected pack in the session
            $_SESSION['user_class']->pack1 = 5;

            $message = "You Changed to the Gambino Pact!";
        }
    }
}

if ($_GET['buy'] == "7days") {
    if ($user_class->credits >= 100) {
        // Calculate the timestamp for current time + 7 days (in seconds)
        $targetTimestamp = time() + 604800;

        // If the user's current pack1time is in the future, add 14 days to it; otherwise, set it to the targetTimestamp
        $updatedPack1Time = max($targetTimestamp, $user_class->pack1time + 604800);

        $db->query("UPDATE grpgusers SET pack1time = ?, credits = credits - 100 WHERE id = ?");
        $db->execute(array(
            $updatedPack1Time,
            $user_class->id
        ));

        echo Message("You spent 100 credits for 7 Days Pack 1 Bonus.");
    } else {
        echo Message("You don't have enough credits. You can buy some at the upgrade store.");
    }
}// Set the last clicked image index in the session
if (isset($_GET['image'])) {
    $_SESSION['last_clicked_image'] = $_GET['image'];
}
?>

<style>
    /* Set initial filter to grayscale (100%) for all images */
    img {
        filter: grayscale(100%);
        transition: filter 0.3s ease; /* Add a smooth transition effect */
    }

    /* Remove the grayscale filter when the image is clicked */
    img.active {
        filter: none;
    }

    .tooltip {
        pointer-events: none;
        position: fixed;
        display: none;
        width: 300px; /* Adjust the width to your desired value */
        color: #fff;
        text-align: left; /* Align the text to the left */
        border: 1px solid #000; /* 1px black border */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5); /* Black shadow */
        border-radius: 5px;
        padding: 15px; /* Adjust the padding to create more space */
        z-index: 999; /* Increase the z-index to ensure the tooltip appears above other elements */
        backdrop-filter: blur(5px); /* Apply a blur effect to the tooltip background */
        -webkit-backdrop-filter: blur(5px); /* Vendor-prefixed property for Safari */
        /* Remove the opacity */
    }

    .tooltip::before {
        content: '';
        position: absolute;
        top: -10px;
        left: -10px;
        right: -10px;
        bottom: -10px;
        background-color: rgba(51, 51, 51, 0.85); /* Use rgba to set the background color with an alpha value for transparency */
        border-radius: 5px;
        z-index: -1; /* Place the pseudo-element behind the tooltip content */
    }    /* Image containers to display images side by side */
    .image-container {
        display: inline-block;
        margin: 0 10px; /* Adjust this value to change the spacing between images */
        position: relative;
    }

    /* Reset margins for first and last images */
    .image-container:first-child {
        margin-left: 0;
    }

    .image-container:last-child {
        margin-right: 0;
    }

.grey-text {
    color: lightgrey;
}

.limegreen-text {
    color: limegreen;
}

    /* Center the images within the containers */
    .image-container img {
        display: block;
        margin: 0 auto;
    }

    #countdown {
        display: inline; /* Changed to inline to keep it visible */
    }
</style>
<style>
    /* ... (your existing styles) */

    /* Define styles for future-time (green) and expired-time (red) */
    .future-time {
        color: green;
    }

    .expired-time {
        color: red;
    }
</style>
<style>
    /* ... (your existing styles) */

    /* Tooltip styles */
    .tooltip {
    pointer-events: none;
    position: fixed;
    display: none;
    width: 300px; /* Adjust the width to your desired value */
    color: #fff;
    text-align: left; /* Align the text to the left */
    border: 1px solid #000; /* 1px black border */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5); /* Black shadow */
    border-radius: 5px;
    padding: 15px; /* Adjust the padding to create more space */
    z-index: 999; /* Increase the z-index to ensure the tooltip appears above other elements */
    backdrop-filter: blur(5px); /* Apply a blur effect to the tooltip background */
    -webkit-backdrop-filter: blur(5px); /* Vendor-prefixed property for Safari */
}

.tooltip::before {
    content: '';
    position: absolute;
    top: -10px;
    left: -10px;
    right: -10px;
    bottom: -10px;
    background-color: rgba(51, 51, 51, 0.85); /* Use rgba to set the background color with an alpha value for transparency */
    border-radius: 5px;
    z-index: -1; /* Place the pseudo-element behind the tooltip content */
}</style>

<table style='width: 100%; border-collapse: collapse;'>
<?php
// Assuming $user_class->pack1 holds the user's selected pack number (1 to 5)
$user_pack = $user_class->pack1;

$custom_texts = array(
    "<font color=green>You currently don't have any pack selected. Choose a pack from the options below to activate its effects.</font></br> 

 </br>
Make a Pact with one of the Local Crime Families and Earn a Powerful Bonus.</br>
<font color=red>Be Careful</font> - You can only change between pacts once every 24 Hours.
", // Custom text for Pack 0 (no pack selected)
    "<font color=green>You have activated the <font color=white><b>Gambino</b></font> Pact!</font></br> 

 </br>
You will be gaining an additional 20% Exp for all Crimes. </br>
<font color=red>Tips</font> - Be sure to use a Double Exp pill to help maximize the benefits!.
",
    "<font color=green>You have activated the <font color=white><b>Luciano</b></font> Pact!</font></br> 

 </br>
You will be gaining an additional 20% Cash for all Crimes. </br>
<font color=red>Tips</font> - Be sure to use a Double Exp pill to help maximize the benefits!.
",
    "<font color=green>You have activated the <font color=white><b>Bananno</b></font> Pact!</font></br> 

 </br>
You will be gaining an additional 20% extra stats when training inside of the Gym. </br>
<font color=red>Tips</font> - Make sure to get a good house and be in a good gang to take advantage of the gang house benefits!.",
    "<font color=green>You have activated the <font color=white><b>Colombo</b></font> Pact!</font></br> 

 </br>
You will be gaining an additional 30% towards your stats during all battles!</br>
<font color=red>Tips</font> - Make sure to equipped the strongest gear and prepare for war!.",
    "<font color=green>You have activated the <font color=white><b>Genovese</b></font> Pact!</font></br> 

 </br>
You will be gaining an additional 25% more from all your Succesful Mugs!</br>
<font color=red>Tips</font> - Target those with cash out!."
);
echo "<tr><td colspan='5' style='text-align: center; font-weight: bold;'>Crime Family Pacts</td></tr>";
echo "<tr><td colspan='5' style='text-align: center; padding: 10px;'>" . ($user_pack > 0 ? $custom_texts[$user_pack] : $custom_texts[0]) . "</td></tr>";echo "<tr>";

echo "<td style='text-align: center;'>";

// Create an array of image paths for each pack
$image_paths = array(
    "/images/money.jpg",
    "/images/money.jpg",
    "/images/money.jpg",
    "/images/money.jpg",
    "/images/money.jpg",
);

$tooltip_texts = array(
    "<b>The Gambino Pact</b></br></br><span class='grey-text'>Form a pact with us and experience unrivaled growth in the underworld!
 Embrace the might of the Gambino Family and conquer the criminal realm like never before! Your journey to dominance begins now!</span> </br></br>
<span class='limegreen-text'>Gain 20% more Exp on all Crimes!</span>",
    "<b>The Luciano Pact</b></br></br><span class='grey-text'>Become a part of the illustrious Luciano Family, where prosperity knows no bounds!
 Our potent pact guarantees unparalleled gains in your criminal ventures, propelling you to unimaginable financial heights. 
Embrace this alliance and reign as a master of wealth in the unforgiving underworld!</span> </br></br><span class='limegreen-text'>Gain 20% more Cash on all Crimes performed!</span>",
    "<b>The Bonanno Pact</b></br></br><span class='grey-text'>Join us and unlock unparalleled strength! Our pact enhances your abilities,
 making you an unstoppable force. Embrace the power of the Bonanno Family and dominate the underworld!</span> </br></br><span class='limegreen-text'>Gain 20% more stats when training in the Gym!</span>",
    "<b>The Colombo Pact</b></br></br><span class='grey-text'>Step into the elite ranks of the Colombo Family and witness your influence surge to new heights!
 Our powerful pact bestows upon you an unyielding army of loyal supporters,
 ready to stand by your side in every endeavor.</span> </br></br><span class='limegreen-text'>Gain 30% in all stats when in any battle!</span>",
    "<b>The Genovese Pact</b></br></br><span class='grey-text'>Step into the elite ranks of the Colombo Family and witness your influence surge to new heights!
 Our powerful pact bestows upon you an unyielding army of loyal supporters,
 ready to stand by your side in every endeavor.</span> </br></br><span class='limegreen-text'>Gain 25% more with all succesful mugs </span>"
);


for ($i = 0; $i < 5; $i++) {
    echo "<div class=\"image-container\">";
    // Determine if the image should remain in color based on the user's selected pack
    $isUserPack = ($user_pack === $i + 1);
    echo "<a href='pacts.php?buy=FirstSet" . ($i + 1) . "&image=" . $i . "'><img src=\"" . $image_paths[$i] . "\" width='100' height='100' alt='test' " . ($isUserPack ? "class='active'" : "") . " /></a>";
    echo "<div class=\"tooltip\" id=\"tooltip" . ($i + 1) . "\">" . $tooltip_texts[$i] . "</div>";
    echo "</div>";
}
echo "</td>";
echo "<tr>";
echo "<td>Time Remaining For Pack 1: <font color=red>" . formatCountdown($user_class->pack1time - time()) . "</font> &nbsp &nbsp &nbsp &nbsp &nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  &nbsp &nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp <a href='?buy=7days'><font size=3>Purchase 7 Days for 100 Credits</font></a></td>";
echo "</tr>";

// Display the notification message if set
if (isset($message)) {
    echo "<tr><td colspan='5' style='text-align: center;'><div id=\"notification\">" . $message . "</div></td></tr>";
}

echo "</table>";
?>

<script>
    // Function to show tooltip
    function showTooltip(event, tooltip) {
        const scrollX = window.pageXOffset || document.documentElement.scrollLeft;
        const scrollY = window.pageYOffset || document.documentElement.scrollTop;

        tooltip.style.left = event.clientX + "px";
        tooltip.style.top = (event.clientY + 20) + "px";
        tooltip.style.display = "block";
    }

    // Function to hide tooltip
    function hideTooltip(tooltip) {
        tooltip.style.display = "none";
    }

   // Function to handle AJAX call
function handleImageClick(pack, img) {
    fetch("pacts.php?buy=FirstSet" + (pack + 1) + "&image=" + pack)
        .then((response) => response.json())
        .then((data) => {
            if (data.message) {
                const notification = document.getElementById("notification");
                notification.textContent = data.message;
                notification.style.display = "block";

                // Hide notification after a few seconds (adjust as needed)
                setTimeout(() => {
                    notification.textContent = "";
                    notification.style.display = "none";
                }, 3000); // 3000 milliseconds = 3 seconds
            }



function confirmPackChange(packName) {
    return confirm("Are you sure you wish to change to " + packName + "?");
}

            if (data.pack) {
                const images = document.querySelectorAll(".image-container img");
                const userPack = data.pack; // Get user's selected pack from AJAX response

                images.forEach((imgElement, index) => {
                    // Check if the image should have the grayscale removed based on the user's selected pack
                    if (userPack === index + 1) {
                        imgElement.style.filter = "none";
                    } else {
                        imgElement.style.filter = "grayscale(100%)";
                    }
                });
                
                // Update the countdown text when the 14 Days Pack is purchased
                const countdownText = document.getElementById("countdown");
                countdownText.innerHTML = "Time Remaining For Pack 1: <font color=red>0 days 0 hours 0 minutes 0 seconds</font>";
            }
        })
        .catch((error) => console.log(error));
}
    // Add event listeners to images
    document.addEventListener("DOMContentLoaded", () => {
        const images = document.querySelectorAll(".image-container img");
        const userPack = <?php echo $user_class->pack1; ?>; // Get user's selected pack from PHP

        images.forEach((img, index) => {
            // Check if the image should have the grayscale removed based on the user's selected pack
            if (userPack === index + 1) {
                img.style.filter = "none";
            } else {
                img.style.filter = "grayscale(100%)";
            }

            img.addEventListener("click", () => {
    const packNames = ["FirstSet1", "FirstSet2", "FirstSet3", "FirstSet4", "FirstSet5"];
    if (confirmPackChange(packNames[index])) {
        handleImageClick(index, img);
    }
});

            img.addEventListener("mouseover", (event) => {
                showTooltip(event, document.getElementById("tooltip" + (index + 1)));
                img.style.filter = "none"; // Update the hover effect directly here
            });
            img.addEventListener("mousemove", (event) => {
                showTooltip(event, document.getElementById("tooltip" + (index + 1)));
            });
            img.addEventListener("mouseout", () => {
                hideTooltip(document.getElementById("tooltip" + (index + 1)));
                if (userPack !== index + 1) {
                    img.style.filter = "grayscale(100%)"; // Update the hover effect directly here
                }
            });
        });
    });
</script>