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
    if ($user_class->credits >= 100) {
        // Calculate the new timestamp for current time (set to 0 to reset the cooldown)
        $newTimeTill = 0;

        // Update pack1timetill with the new timestamp (set to 0 to reset the cooldown)
        $db->query("UPDATE grpgusers SET pack1timetill = ? WHERE id = ?");
        $db->execute(array($newTimeTill, $user_id));

        // Deduct 100 credits from the user's account
        $db->query("UPDATE grpgusers SET credits = credits - 100 WHERE id = ?");
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
        $message = "You have paid 100 Credits to skip the cooldown. You can now change packs immediately!";
    } else {
        $timeRemaining = formatCountdown($user_class->pack1timetill - time());
        $message = "You still have time remaining to change packs. You can't change packs again until {$timeRemaining}.";
    }
}

if (isset($_GET['buy'])) {
    $selected_pack = $_GET['buy'];

    // Check if the user has changed packs in the last 24 hours
    if ($user_class->pack1timetill > time()) {
        $timeRemaining = formatCountdown($user_class->pack1timetill - time());
        $message = "You can't change packs again until $timeRemaining. <br><a href='gamething.php?skip_cooldown=1'>Reset remaining time for 100 credits</a>";
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

            $message = "You Changed to Pack1.";
        } elseif ($selected_pack === "FirstSet2" && $user_class->id >= 0) {
            $newTimeTill = time() + 86400; // 24 hours from now
            $db->query("UPDATE grpgusers SET pack1 = 2, pack1timetill = ? WHERE id = ?");
            $db->execute(array(
                $newTimeTill,
                $user_class->id
            ));

            // Update the user's selected pack in the session
            $_SESSION['user_class']->pack1 = 2;

            $message = "You Changed to Pack2.";
        } elseif ($selected_pack === "FirstSet3" && $user_class->id >= 0) {
            $newTimeTill = time() + 86400; // 24 hours from now
            $db->query("UPDATE grpgusers SET pack1 = 3, pack1timetill = ? WHERE id = ?");
            $db->execute(array(
                $newTimeTill,
                $user_class->id
            ));

            // Update the user's selected pack in the session
            $_SESSION['user_class']->pack1 = 3;

            $message = "You Changed to Pack3.";
        } elseif ($selected_pack === "FirstSet4" && $user_class->id >= 0) {
            $newTimeTill = time() + 86400; // 24 hours from now
            $db->query("UPDATE grpgusers SET pack1 = 4, pack1timetill = ? WHERE id = ?");
            $db->execute(array(
                $newTimeTill,
                $user_class->id
            ));

            // Update the user's selected pack in the session
            $_SESSION['user_class']->pack1 = 4;

            $message = "You Changed to Pack4.";
        } elseif ($selected_pack === "FirstSet5" && $user_class->id >= 0) {
            $newTimeTill = time() + 86400; // 24 hours from now
            $db->query("UPDATE grpgusers SET pack1 = 5, pack1timetill = ? WHERE id = ?");
            $db->execute(array(
                $newTimeTill,
                $user_class->id
            ));

            // Update the user's selected pack in the session
            $_SESSION['user_class']->pack1 = 5;

            $message = "You Changed to Pack5.";
        }
    }
}

if ($_GET['buy'] == "14days") {
    if ($user_class->credits >= 50) {
        // Calculate the timestamp for current time + 14 days (in seconds)
        $targetTimestamp = time() + 1209600;

        // If the user's current pack1time is in the future, add 14 days to it; otherwise, set it to the targetTimestamp
        $updatedPack1Time = max($targetTimestamp, $user_class->pack1time + 1209600);

        $db->query("UPDATE grpgusers SET pack1time = ?, credits = credits - 50 WHERE id = ?");
        $db->execute(array(
            $updatedPack1Time,
            $user_class->id
        ));

        echo Message("You spent 50 credits for 14 Days Pack 1 Bonus.");
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

    /* Tooltip styles */
    .tooltip {
        pointer-events: none;
        position: fixed;
        display: none;
        width: 120px;
        background-color: #333;
        color: #fff;
        text-align: center;
        border: 1px solid #000; /* 1px black border */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.5); /* Black shadow */
        border-radius: 5px;
        padding: 5px;
        z-index: 999; /* Increase the z-index to ensure the tooltip appears above other elements */
    }

    /* Image containers to display images side by side */
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


<h3>Prayer</h3>
<hr>
<table style='width: 100%; border-collapse: collapse;'>
<?php
// Assuming $user_class->pack1 holds the user's selected pack number (1 to 5)
$user_pack = $user_class->pack1;

$custom_texts = array(
    "<font color=green>You currently don't have any pack selected. Choose a pack from the options below to activate its effects.</font></br> 

Each Upgrade will grant you a bonus around the game! You Can extend your pack time by 14 days for 100 Credits", // Custom text for Pack 0 (no pack selected)
    "Custom text for Pack 1 Blah Blah Custom text for Pack 1 Blah Blah Custom text for Pack 1 Blah Blah Custom text for Pack 1 Blah Blah Custom text for Pack 1 Blah Blah ",
    "Custom text for Pack 2 Custom text for Pack 1 Blah Blah Custom text for Pack 1 Blah Blah Custom text for Pack 1 Blah Blah ",
    "Custom text for Pack 3",
    "Custom text for Pack 4",
    "Custom text for Pack 5"
);
echo "<tr><td colspan='5' style='text-align: center; font-weight: bold;'>Blessing from Apollo</td></tr>";
echo "<tr><td colspan='5' style='text-align: center; padding: 10px;'>" . ($user_pack > 0 ? $custom_texts[$user_pack] : $custom_texts[0]) . "</td></tr>";echo "<tr>";

echo "<td style='text-align: center;'>";

// Create an array of image paths for each pack
$image_paths = array(
    "/images/ml2thing.jpg",
    "/images/ml2thing.jpg",
    "/images/ml2thing.jpg",
    "/images/ml2thing.jpg",
    "/images/ml2thing.jpg"
);

// Create an array of tooltip texts for each pack
$tooltip_texts = array(
    "<b><font color=red>20% Bonus On Crimes</font></b></br>With this upgrade, You can enjoy 20% bonus on all Exp Earned whilst this bonus is Equipped!",
    "Tooltip Text 2",
    "Tooltip Text 3",
    "Tooltip Text 4",
    "Tooltip Text 5"
);

for ($i = 0; $i < 5; $i++) {
    echo "<div class=\"image-container\">";
    // Determine if the image should remain in color based on the user's selected pack
    $isUserPack = ($user_pack === $i + 1);
    echo "<a href='gamething.php?buy=FirstSet" . ($i + 1) . "&image=" . $i . "'><img src=\"" . $image_paths[$i] . "\" width='100' height='100' alt='test' " . ($isUserPack ? "class='active'" : "") . " /></a>";
    echo "<div class=\"tooltip\" id=\"tooltip" . ($i + 1) . "\">" . $tooltip_texts[$i] . "</div>";
    echo "</div>";
}
echo "</td>";
echo "<tr>";
echo "<td>Time Remaining For Pack 1: <font color=red>" . formatCountdown($user_class->pack1time - time()) . "</font> &nbsp &nbsp &nbsp &nbsp &nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp  &nbsp &nbsp  &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp <a href='?buy=14days'><font size=3>Purchase 14 Days for 100 Credits</font></a></td>";
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
    fetch("gamething.php?buy=FirstSet" + (pack + 1) + "&image=" + pack)
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
                handleImageClick(index, img);
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