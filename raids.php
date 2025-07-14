<?php
require_once 'includes/functions.php';

start_session_guarded();

include 'header.php';

error_reporting(0); // Turn off all error reporting
ini_set('display_errors', 0); // Don't display errors

?>

<script>
    function openModal(raidId) {
        fetchParticipants(raidId);
        document.getElementById('userModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('userModal').style.display = 'none';
    }

    function fetchParticipants(raidid) {
        const raidId = raidid;
        console.log(raidid);
        fetch(`getParticipants.php?raidId=${raidId}`)
            .then(response => response.json())
            .then(data => {
                console.log(data);
                updateModalContent(data);
                openModal(); // Open the modal after updating content
            })
            .catch(error => console.error('Error fetching participants:', error));
    }
    function updateModalContent(participantsData) {
        const modalContent = document.getElementById('modal-content');
        modalContent.innerHTML = '';

        if (Array.isArray(participantsData)) {
            participantsData.forEach(participant => {
                const participantElement = document.createElement('div');
                participantElement.textContent = participant;
                modalContent.appendChild(participantElement);
            });
        } else {
            console.error('Error: Participants data is not an array');
        }
    }


    document.addEventListener("DOMContentLoaded", function () {
        const timers = document.querySelectorAll('.timer');

        timers.forEach(timer => {
            const secondsRemaining = timer.getAttribute('data-seconds');
            let countDownDate = new Date().getTime() + secondsRemaining * 1000;

            let x = setInterval(function () {
                let now = new Date().getTime();
                let distance = countDownDate - now;

                let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timer.textContent = hours + "h " + minutes + "m " + seconds + "s ";

                if (distance < 0) {
                    clearInterval(x);
                    timer.textContent = "EXPIRED";
                }
            }, 1000);
        });
    });
</script>



<script>
    function confirmSummon(cost) {
        return confirm('Summoning this boss will cost ' + cost + ' raid token(s). Are you sure you want to proceed?');
    }
    function toggleDropdown(element) {
        var dropdown = element.nextElementSibling;
        if (dropdown.style.display === "block") {
            dropdown.style.display = "none";
        } else {
            dropdown.style.display = "block";
        }
    }
</script>



<?php
$db->query("SELECT * FROM bosses WHERE is_active > 0");
$db->execute();
$bosses = $db->fetch_row();

$db->query("SELECT * FROM pets WHERE raid_leash = 1 AND userid = ? LIMIT 1");
$db->execute([$user_class->id]);
$pet = $db->fetch_row(true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_raid_id'])) {
    $raid_id = intval($_POST['join_raid_id']);
    $user_id = $user_class->id;

    // Check if the user is already a participant in an ongoing raid
    $db->query("SELECT rp.raid_id FROM raid_participants rp JOIN active_raids ar ON rp.raid_id = ar.id WHERE rp.user_id = ? AND TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(ar.summoned_at, INTERVAL 15 MINUTE)) > 0");
    $db->execute([$user_id]);
    if ($db->num_rows() > 0) {
        echo Message("You are already in a raid!");
        return;
    }

    $db->query("SELECT * FROM raid_participants WHERE user_id = ? AND raid_id = ?");
    $db->execute([$user_id, $raid_id]);
    if ($db->num_rows() > 0) {
        echo Message("You are already in a raid!");
        return;
    }

    // Fetch raid details including raid type and summoner's gang
    $db->query("SELECT ar.raid_type, ar.summoned_by, g.gang FROM active_raids ar LEFT JOIN grpgusers g ON ar.summoned_by = g.id WHERE ar.id = ?");
    $db->execute([$raid_id]);
    $raid_details = $db->fetch_row(true);

    // Check if the user can join based on the raid type
    if ($raid_details['raid_type'] === 'Private' && $user_id != $raid_details['summoned_by']) {
        echo Message("This is a private raid. You cannot join.");
        return;
    }

    if ($raid_details['raid_type'] === 'Gang') {
        $user_gang = $user_class->gang;
        if ($user_gang != $raid_details['gang']) {
            echo Message("This raid is only for gang members.");
            return;
        }
    }

    // Fetch the boss ID associated with the raid
    $db->query("SELECT boss_id, tokencost, maxraiders FROM active_raids WHERE id = ?");
    $db->execute([$raid_id]);
    $boss_values = $db->fetch_row(true);

    $boss_id = $boss_values['boss_id'];
    $tokencost = $boss_values['tokencost'];
    $maxraiders = $boss_values['maxraiders'];

    // Count the current number of participants in the raid
    $db->query("SELECT COUNT(*) AS participant_count FROM raid_participants WHERE raid_id = ?");
    $db->execute([$raid_id]);
    $participant_count = $db->fetch_single();

    // Check if the raid is already full
    if ($participant_count >= $maxraiders) {
        echo "This raid is already full with $participant_count/$maxraiders raiders.";
        return; // Stop further execution if the raid is full
    }

    // Check if the user has at least the required raid tokens
    if ($user_class->raidtokens < 1) {
        echo Message("You do not have enough raid tokens to join the raid. You need 1 token.");
    } else {
        // Deduct the required number of raid tokens from the user
        $db->query("UPDATE grpgusers SET raidtokens = raidtokens - 1 WHERE id = ?");
        $db->execute([$user_id]);

        // Proceed with joining the raid as before
        perform_query("INSERT INTO raid_participants (raid_id, user_id) VALUES (?, ?)", [$raid_id, $user_id]);

        // Update the raidsjoined count as before
        perform_query("UPDATE grpgusers SET raidsjoined = raidsjoined + 1, raidcomp = raidcomp + 1 WHERE id = ?", [$user_id]);

        // Get the owner of the raid as before
        $db->query("SELECT summoned_by FROM active_raids WHERE id = ?");
        $db->execute([$raid_id]);
        $owner_id = $db->fetch_single();

        // Convert the joining user's ID to their actual name as before
        $joining_user_name = formatName($user_id);

        // Create a notification for the raid owner as before
        $event_message = "$joining_user_name has joined your raid!";
        send_event($owner_id, $event_message);

        echo Message("Successfully joined the raid and 1 raid token(s) have been deducted.");
    }

}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['use_speedup'], $_POST['raid_id'])) {
    $raid_id = intval($_POST['raid_id']);
    $user_id = $user_class->id;
    $check = Check_Item(194, $user_id); // Check if the user has speed raid tokens
    if (!$check) {
        echo Message("You do not have any speed raid tokens!");
        require "footer.php";
        exit();
    }

    Take_Item(194, $user_id, 1);

    // Set the summoned_at column in the active_raids table to the current timestamp
    perform_query("UPDATE active_raids SET summoned_at = DATE_SUB(NOW(), INTERVAL 15 MINUTE) WHERE id = ?", [$raid_id]);

    header("Location: raids.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['boss_id'], $_POST['difficulty'])) {
    $user_id = $user_class->id;  // Assuming this is how you get the logged-in user's ID
    $boss_id = intval($_POST['boss_id']);

    // Take boss by ID from $bosses array:
    $boss = null;
    foreach ($bosses as $b) {
        if ($b['id'] == $boss_id) {
            $boss = $b;
            break;
        }
    }

    if (empty($boss) || $boss == null) {
        echo Message("Could not find a boss with ID($boss_id). Please try again.");
        require "footer.php";
        exit();
    }

    $tokencost = $boss['tokencost'];
    $level = $boss['level'];

    $raid_type = $_POST['raid_type'];
    // Ensure $raid_type is one of the expected values
    if (!in_array($raid_type, ['Public', 'Private', 'Gang'])) {
        $raid_type = 'Public'; // Default to Public if an unexpected value is provided
    }

    if ($pet && isset($pet['id']) && $pet['level'] >= $boss_level) {
        $tokencost *= 2; // Double the cost if the user has a pet
    }

    // Check user's raid tokens
    $user_tokens = $user_class->raidtokens;

    if ($boss_id == 21) {
        $totalDraculaBloodBagCount = check_items(285, $user_class->id);

        if (!$totalDraculaBloodBagCount) {
            echo "<script>alert('You need Dracula Blood Bags to raid Dracula.');</script>";
            return;  // Exit early so the rest of the code doesn't run
        }

    } else if ($boss_id == 24) {
        $totalbaskets = check_items(344, $user_class->id);

        if (!$totalbaskets) {
            echo "<script>alert('You need a Rare Egg Basket to raid Don Egghopper.');</script>";
            return;  // Exit early so the rest of the code doesn't run
        }
    } else if ($boss_id == 25) {
        $totalKeys1 = check_items(349, $user_class->id);
        $totalKeys2 = check_items(350, $user_class->id);
        if (!$totalKeys1 || !$totalKeys2) {
            echo "<script>alert('You need the two keys to raid the Egg Lab.');</script>";
            return;  // Exit early so the rest of the code doesn't run
        }
    } else if ($boss_id == 26) {
        $buildingPass = check_items(356, $user_class->id);
        if (!$buildingPass) {
            echo "<script>alert('You need a building pass to challenge The Janitor');</script>";
            return;  // Exit early so the rest of the code doesn't run
        }
    } else {
        if ($user_tokens < $tokencost) {
            echo "<script>alert('You do not have enough raid tokens to summon this boss. Required: $tokencost raid token(s).');</script>";
            return;  // Exit early so the rest of the code doesn't run
        }
    }

    // Check if the user is already a participant in any active raid
    $check_query = "
        SELECT rp.* FROM raid_participants rp
        JOIN active_raids ar ON rp.raid_id = ar.id
        WHERE rp.user_id = $user_id AND DATE_ADD(ar.summoned_at, INTERVAL 15 MINUTE) > NOW()
    ";
    $db->query("SELECT rp.* FROM raid_participants rp
        JOIN active_raids ar ON rp.raid_id = ar.id
        WHERE rp.user_id = ? AND DATE_ADD(ar.summoned_at, INTERVAL 15 MINUTE) > NOW()");
    $db->execute([$user_id]);

    if ($db->num_rows() > 0) {
        // The user is already in an active raid, display an error message
        echo "<script>alert('You are already in an active raid. You cannot summon another one at the moment.');</script>";
    } else {
        if ($user_class->level < $boss_level) {
            echo "<script>alert('You must be at least level $boss_level to summon this boss.');</script>";
            return;
        }

        $raid_type = isset($_POST['raid_type']) ? filter_input(INPUT_POST, 'raid_type', FILTER_SANITIZE_STRING) : 'Public';

        $tempItemUse = getItemTempUse($user_class->id);

        $used_booster = (int) $tempItemUse['raid_booster'] > 0 ? 1 : 0;
        removeItemTempUse($user_class->id, 'raid_booster', 1);

        $used_pass = (int) $tempItemUse['raid_pass'] > 0 ? 1 : 0;
        removeItemTempUse($user_class->id, 'raid_pass', 1);

        $difficulty = filter_input(INPUT_POST, 'difficulty', FILTER_SANITIZE_STRING);
        perform_query("INSERT INTO active_raids (boss_id, summoned_by, difficulty, raid_type, used_booster, used_pass) VALUES (?, ?, ?, ?, ?, ?)", [
            $boss_id,
            $user_id,
            $difficulty,
            $raid_type,
            $used_booster,
            $used_pass
        ]);

        // Get the ID of the raid that was just inserted
        $raid_id = $db->insert_id();

        if (isset($pet['id']) && $pet['level'] >= $boss_level) {
            $leashed_pet_id = $pet['id'];
        } else {
            $leashed_pet_id = 0;
        }

        // Insert the user into the raid_participants table
        perform_query("INSERT INTO raid_participants (raid_id, user_id, leashed_pet_id) VALUES (?, ?, ?)", [
            $raid_id,
            $user_id,
            $leashed_pet_id
        ]);

        // Deduct the correct number of raid tokens from the user's account
        perform_query("UPDATE grpgusers SET raidtokens = raidtokens - ?, raidshosted = raidshosted + 1, raidcomp = raidcomp + ?, raidsjoined = raidsjoined + 1 WHERE id = ?", [
            $tokencost,
            $tokencost,
            $user_id
        ]);

        if ($boss_id == 21) {
            Take_Item(285, $user_class->id, 1);
        } else if ($boss_id == 24) {
            Take_Item(344, $user_class->id, 1);
        } else if ($boss_id == 25) {
            Take_Item(349, $user_class->id, 1);
            Take_Item(350, $user_class->id, 1);
        } else if ($boss_id == 26) {
            Take_Item(356, $user_class->id, 1);
        }

        echo "<script>alert('$tokencost raid token(s) have been spent to summon the boss.');</script>";

        if (!isset($_SESSION['raid_difficulty'])) {
            $_SESSION['raid_difficulty'] = [];
        }
        $_SESSION['raid_difficulty'][$boss_id] = $difficulty;

        if (!isset($_SESSION['raid_type'])) {
            $_SESSION['raid_type'] = [];
        }
        $_SESSION['raid_type'][$boss_id] = $raid_type;

        // Redirect to raids.php to refresh the page
        header('Location: raids.php');
        exit;
    }
}



// Fetch all active raids
$active_raids_query = "SELECT ar.*, b.name AS boss_name, b.image_link, TIMESTAMPDIFF(SECOND, NOW(), DATE_ADD(ar.summoned_at,  INTERVAL 15 MINUTE)) AS seconds_remaining FROM active_raids ar JOIN bosses b ON ar.boss_id = b.id WHERE DATE_ADD(ar.summoned_at, INTERVAL 15 MINUTE) > NOW()";
if (isset($_GET['ftype']) && $_GET['ftype'] === 'gang_only') {
    $active_raids_query .= " AND ar.raid_type = 'Gang'";
} else if (isset($_GET['ftype']) && $_GET['ftype'] === 'public') {
    $active_raids_query .= " AND ar.raid_type = 'Public'";
} else if (isset($_GET['ftype']) && $_GET['ftype'] === 'private') {
    $active_raids_query .= " AND ar.raid_type = 'Private'";
}

$db->query($active_raids_query);
$db->execute();
$active_raids = $db->fetch_row();

if ($pet && isset($pet['id'])): ?>
    <div class="alert alert-info">
        <center>
            <strong>
                You currently have a pet on the leash. They will join in with any raids that they have the relevant level
                for.
                Raid costs are double for a pet to join. <span style="color: red;">Pet's Can Only Join The Raids You
                    Start.</span>
            </strong>
        </center>
    </div>
<?php endif; ?>
<?php

// Display active raids
echo "<div class='box_top'>Active Raids</div>";
echo "<div class='box_middle'>";
echo "<div class='pad'>";
echo "<p><strong>Filter By:</strong> ";
echo "<a href='raids.php'>Show All</a> | ";
echo "<a href='raids.php?ftype=gang_only'>Gang Only</a> | ";
echo "<a href='raids.php?ftype=public'>Public</a> | ";
echo "<a href='raids.php?ftype=private'>Private</a>";
echo "<div class='active-raids-grid'>";

foreach ($active_raids as $raid) {
    $db->query("SELECT gang FROM grpgusers WHERE id = ?");
    $db->execute([$raid['summoned_by']]);
    $gang = $db->fetch_row(true);
    $summoner_name = formatName($raid['summoned_by']);
    $user_gang_id = $user_class->gang; // Assuming this is how you get the user's gang ID
    $canJoin = true; // A flag to determine if the user can join the raid

    // Check if the raid type is either Private or Gang and if the user is in the same gang
    $raidTypeColor = 'green'; // Default color
    $user_id = $user_class->id; // Replace with the actual way to get the logged-in user's ID

    if ($raid['raid_type'] === 'Private' && $user_id != $raid['summoned_by']) {
        $raidTypeColor = 'red';
        $canJoin = false;
    } elseif ($raid['raid_type'] === 'Gang' && $user_gang_id != $gang['gang']) {
        $raidTypeColor = 'red';
        $canJoin = false;
    }

    // Fetch the maxraiders value for the boss of this raid
    $db->query("SELECT maxraiders FROM bosses WHERE id = ?");
    $db->execute([$raid['boss_id']]);
    $maxraiders_row = $db->fetch_row(true);
    $maxraiders = $maxraiders_row['maxraiders'];

    // Count the current number of participants in the raid
    $db->query("SELECT COUNT(*) AS participant_count FROM raid_participants WHERE raid_id = ?");
    $db->execute([$raid['id']]);
    $participant_count_row = $db->fetch_row(true);
    $participant_count = $participant_count_row['participant_count'];

    // Output the raid card
    $db->query("SELECT * FROM raid_participants WHERE raid_id = ? AND user_id = ?");
    $db->execute([$raid['id'], $user_class->id]);
    $participant_result = $db->fetch_row();

    if ($participant_count >= $maxraiders && count($participant_result) > 0) {
        echo "<div class='raid-card' style='display: none;'>";
    } else {
        echo "<div class='raid-card'>";
    }
    echo "<img src='" . $raid['image_link'] . "' alt='Boss Image' class='boss-image'>";
    echo "<h3>" . $raid['boss_name'] . " (Summoned by " . $summoner_name . ")</h3>";
    echo "<p>Difficulty: " . $raid['difficulty'] . "</p>";
    echo "<p style='color: " . $raidTypeColor . ";'>Raid Type: " . $raid['raid_type'] . "</p>";
    echo "<p>Time Remaining: <span class='timer' data-seconds='" . $raid['seconds_remaining'] . "'>Calculating...</span></p>";

    // Display the number of participants and the max raiders
    if ($participant_count >= $maxraiders) {
        echo "<p style='color: red;'>Participants: " . $participant_count . "/" . $maxraiders . " [FULL]</p>";
    } else {
        echo "<p>Participants: " . $participant_count . "/" . $maxraiders . "</p>";
    }

    // Check if the user is already a participant of this raid

    // Check if the user has item 194 (Raid Speedups)
    $raid_speedup = Check_Item(194, $user_class->id);

    if ($participant_count > 0) {
        echo "<button class='btn btn-success' disabled>Joined</button>";

        if ($raid_speedup > 0) {
            echo "<form action='raids.php' method='post'>";
            echo "<input type='hidden' name='use_speedup' value='1'>";
            echo "<input type='hidden' name='raid_id' value='" . $raid['id'] . "'>";
            echo "<button type='submit'>Use Raid Speedups (" . $raid_speedup . " left)</button>";
            echo "</form>";
        }
    } else {
        if ($canJoin) {
            echo "<form action='raids.php' method='post' style='display:inline;'>";
            echo "<input type='hidden' name='join_raid_id' value='" . $raid['id'] . "'>";
            echo "<button type='submit' class='btn btn-primary'>Join Raid</button>";
            echo "</form>";
        } else {
            echo "<button class='btn btn-danger' disabled>Join Raid</button>";
        }
    }

    if ($user_class->id == 9) {
        echo $raid['id'];
        echo "<button onclick=\"fetchParticipants(" . $raid['id'] . ")\">View Participants</button>";
    }

    echo "</div>"; // Close raid-card
}

echo "</div>"; // Close box_middle
echo "</div>"; // Close pad
echo "</div>"; // Close active-raids-grid
?>




<script>
    document.addEventListener("DOMContentLoaded", function () {
        const timers = document.querySelectorAll('.timer');

        timers.forEach(timer => {
            const secondsRemaining = timer.getAttribute('data-seconds');
            let countDownDate = new Date().getTime() + secondsRemaining * 1000;

            let x = setInterval(function () {
                let now = new Date().getTime();
                let distance = countDownDate - now;

                let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                let seconds = Math.floor((distance % (1000 * 60)) / 1000);

                timer.textContent = hours + "h " + minutes + "m " + seconds + "s ";

                if (distance < 0) {
                    clearInterval(x);
                    timer.textContent = "EXPIRED";
                }
            }, 1000);
        });
    });
</script>

<script>
    function showTooltip(event, element) {
        var tooltip = element.nextElementSibling;

        // Get the mouse coordinates and set the tooltip's position
        var left = event.clientX;
        var top = event.clientY;

        tooltip.style.left = left + 'px';
        tooltip.style.top = top + 'px';

        // Show the tooltip
        tooltip.style.display = 'block';

        // Hide the tooltip after a short delay (2 seconds in this case)
        setTimeout(function () {
            tooltip.style.display = 'none';
        }, 2000);
    }
</script>

<br /><br />
<div class="box_top">Current Statistics</div>
<div class="box_middle">
    <div class="pad">
        <?php
        // Fetch the user's raid stats
        $db->query("SELECT raidtokens, raidwins, raidlosses, raidsjoined, raidshosted FROM grpgusers WHERE id = ?");
        $db->execute([$user_class->id]);
        $user_stats = $db->fetch_row(true);

        echo "<h3>Raid Wins:<font color=red>" . $user_stats['raidwins'] . "</h3></font>";
        echo "<h3>Raid Losses:<font color=red> " . $user_stats['raidlosses'] . "</h3></font>";
        echo "<h3>Raids Joined:<font color=red> " . $user_stats['raidsjoined'] . "</h3></font>";
        echo "<h3>Raids Hosted:<font color=red> " . $user_stats['raidshosted'] . "</h3></font>";
        ?>
    </div>
</div>
<br /><br />

<div class="box_top">Top 5 Raiders</div>
<div class="box_middle">
    <div class="pad">
        <?php
        // Fetching top 5 raiders excluding admins
        $db->query("SELECT * FROM grpgusers WHERE admin != 1 ORDER BY raidpoints DESC LIMIT 5");
        $db->execute();
        $top_raiders = $db->fetch_row();

        // Displaying the top 5 raiders
        echo "<ul>";
        $player_position = 0;
        $rank = 1;
        foreach ($top_raiders as $raider) {
            echo "<h3>" . formatName($raider['id']) . " with " . $raider['raidpoints'] . " Raid Points.</h3>";
            if ($raider['id'] == $user_class->id) {
                $player_position = $rank;
            }
            $rank++;
        }
        echo "</ul>";
        ?>
    </div>
</div>
<br /><br />

<div class="box_top">Your Statistics</div>
<div class="box_middle">
    <div class="pad">
        <?php
        // Fetch the player's raid points
        $db->query("SELECT raidpoints FROM grpgusers WHERE id = ?");
        $db->execute([$user_class->id]);
        $player_raidpoints_data = $db->fetch_row(true);

        if ($player_position == 0) {
            // This means the player is not in the top 5. Let's find their exact position.
            $db->query("SELECT COUNT(*) AS position FROM grpgusers WHERE raidpoints > ?");
            $db->execute([$player_raidpoints_data['raidpoints']]);
            $player_exact_position_data = $db->fetch_single();

            $player_position = $player_exact_position_data + 1; // Add 1 because the COUNT(*) gives the number of users above the player
        }

        echo "<h3>Your are currently positioned number<font color=red> " . $player_position . "</font> on the Raids Leaderboards</h3>";
        echo "<h3>You have a total of<font color=yellow> " . number_format($player_raidpoints_data['raidpoints'], 0) . " </font>raid points</h3>";

        echo "<br /><a class='btn btn-primary' href='/raidpointstore.php'>Go to Raid Point Store</a>";
        ?>
    </div>
</div>
<br /><br />

<div class="box_top">Welcome to Raids</div>
<div class="box_middle">
    <div class="pad">
        <?php
        // Fetch the user's raid stats
        $db->query("SELECT raidtokens, raidwins, raidlosses, raidsjoined, raidshosted FROM grpgusers WHERE id = ?");
        $db->execute([$user_class->id]);
        $user_stats = $db->fetch_row(true);

        echo "<h3><center>YOU CURRENTLY HAVE <font color=yellow>" . $user_stats['raidtokens'] . "</font> RAID TOKENS </center></h3>";

        ?>

        <h3>
            <font color="brown">Embark on epic raids, where you challenge fearsome bosses in intense battles. As your
                level grows, so does the might of the adversaries you confront. Every boss presents distinct challenges
                and promises enticing rewards. Strategize your difficulty selection judiciously and join forces with
                fellow adventurers to conquer these formidable foes!</font>
        </h3>

        <h4>
            <font color="white"><u>Your Available Bosses</u></font>
        </h4>

        <div class="bosses-grid">
            <?php
            foreach ($bosses as $boss) {
                // If the boss has a timestamp set and the current time has surpassed it, skip displaying this boss
                if (isset($boss['available_unixtimestamp']) && $boss['available_unixtimestamp'] <= time()) {
                    continue;
                }

                // Fetch the token cost for the boss
                $tokencost = $boss['tokencost'];

                if ($pet && isset($pet['id']) && $pet['level'] >= $boss['level']) {
                    $tokencost = $tokencost * 2;
                }

                echo "<div class='boss-card'>";
                echo "<img src='" . $boss['image_link'] . "' alt='Boss Image' class='boss-image'>";
                echo "<h3>" . $boss['name'] . "</h3>";

                if (isset($boss['available_unixtimestamp']) && $boss['available_unixtimestamp'] > time()) {
                    $timeLeft = $boss['available_unixtimestamp'] - time();
                    $days = floor($timeLeft / 86400); // Calculate days
                    $hours = floor(($timeLeft % 86400) / 3600); // Hours after removing days
                    $minutes = floor(($timeLeft % 3600) / 60); // Minutes after removing hours
                    echo "<p>Available for: <strong>$days days $hours hours $minutes minutes</strong></p>";
                }
                echo "<p><strong>Level:</strong> " . $boss['level'] . "</p>";

                if ($boss['id'] == 21) {
                    echo "<p><strong>1 x Dracula Blood Bag to summon:</strong></p>";
                } else if ($boss['id'] == 24) {
                    echo "<p><strong>1 x Rare Egg Basket to summon:</strong></p>";
                } else if ($boss['id'] == 25) {
                    echo "<p><strong>1 x Egg Key (Part 1), 1 x Egg Key (Part 2), and 100 tokens to summon:</strong></p>";
                } else if ($boss['id'] == 26) {
                    echo "<p><strong>1 Building Pass to summon</strong></p>";
                } else {
                    echo "<p><strong>Token cost to summon:</strong> $tokencost</p>";
                }

                if ($pet && isset($pet['id']) && $pet['level'] >= $boss['level']) {
                    echo "<p style='font-size: 10px; color: red;'><strong>Your Pet Can Join This Raid</strong></p>";
                }

                $db->query("SELECT l.*, i.itemname, l.bonus FROM loot l JOIN items i ON l.item_id = i.id WHERE l.boss_id = ?");
                $db->execute([$boss['id']]);
                $boss_rewards = $db->fetch_row();

                $rewards = [];
                foreach ($boss_rewards as $reward) {
                    $bonus_style = $reward['bonus'] == 1 ? 'style="color: red;"' : '';
                    $tooltip = $reward['bonus'] == 1 ? 'title="Summoner will receive a 50% buff to this item\'s drop rate"' : '';
                    $rewards[] = "<span $bonus_style $tooltip>" . $reward['itemname'] . "</span>";
                }

                echo "<div class='rewards-btn' onclick='toggleDropdown(this)'>View Rewards</div>";
                echo "<div class='rewards-dropdown'>";
                echo "<center><font color=green><u>Possible Rewards</u></font></center><br>";
                echo "<font color=white>Potential Items:</font><font color=yellow> " . implode(", ", $rewards) . "<br></font>";
                echo "</div>";


                echo "<form action='raids.php' method='post' class='difficulty-form' onsubmit='return confirmSummon(" . $tokencost . ");'>";

                // Grab previous difficulty selection for this boss from session
                $previous_difficulty = isset($_SESSION['raid_difficulty'][$boss['id']]) ? $_SESSION['raid_difficulty'][$boss['id']] : 'Easy';

                echo "<div class='form-group'><label>Select Difficulty: ";
                echo "<select name='difficulty'>";
                echo "<option value='Easy'" . ($previous_difficulty == 'Easy' ? ' selected' : '') . ">Easy</option>";
                echo "<option value='Medium'" . ($previous_difficulty == 'Medium' ? ' selected' : '') . ">Medium</option>";
                echo "<option value='Hard'" . ($previous_difficulty == 'Hard' ? ' selected' : '') . ">Hard</option>";
                echo "</select></label></div>";

                // Grab the previous raid type selection from session
                $previous_raid_type = isset($_SESSION['raid_type'][$boss['id']]) ? $_SESSION['raid_type'][$boss['id']] : 'Public';

                // New dropdown for Raid Type
                echo "<div class='form-group'><label>Raid Type: ";
                echo "<select name='raid_type'>";
                echo "<option value='Public'" . ($previous_raid_type == 'Public' ? ' selected' : '') . ">Public Raid</option>"; // Default option
                echo "<option value='Private'" . ($previous_raid_type == 'Private' ? ' selected' : '') . ">Private (Solo Raid)</option>";
                echo "<option value='Gang'" . ($previous_raid_type == 'Gang' ? ' selected' : '') . ">Gang Only</option>";
                echo "</select></label></div>";

                echo "<input type='hidden' name='boss_id' value='" . $boss['id'] . "'>";
                echo "<input type='submit' value='Summon Boss' class='summon-button'>";
                echo "</form>";

                echo "</div>"; // Close boss-card
            }
            ?>
        </div>
    </div>
</div>


</div>
<div style="clear:both;"></div>

<style>
    .btn-danger {
        /* Styles for the red disabled button */
        background-color: red;
        color: white;
        /* Other necessary styles */
    }

    .btn-primary {
        /* Styles for the regular join button */
        background-color: blue;
        color: white;
        /* Other necessary styles */
    }

    .difficulty-form {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        /* Align items to the start of the flex container */
    }

    .form-group {
        margin-bottom: 10px;
        /* Add some space between each form group */
    }

    .bonus-item {
        color: red;
        /* Highlight bonus items in red */
        cursor: pointer;
    }

    .tooltip {
        position: absolute;
        border: 1px solid #ccc;
        background-color: #fff;
        padding: 5px;
        z-index: 1000;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    .raids-container {
        background-color: #292727;
        padding: 20px;
        border-radius: 10px;
    }

    .bosses-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
    }

    .boss-card {
        color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 -4px 8px rgba(0, 0, 0, 0.2);
        padding: 15px 5px 10px;
        text-align: center;
    }

    .boss-rewards {
        position: relative;
        ;
        top: 0;
        left: 100%;
        background-color: #f5f5f5;
        border-left: 1px solid #ccc;
        width: 200px;
        height: 100%;
        padding: 10px;
        display: none;
        overflow-y: auto;
    }

    .boss-card:hover .boss-rewards {
        display: block;
    }

    .rewards-btn {
        cursor: pointer;
        color: #007bff;
        text-decoration: underline;
    }

    .rewards-dropdown {
        display: none;
        padding: 10px;
        border: 1px solid #ccc;
        background-color: #2b2a2a;
        border-radius: 4px;
        max-width: 250px;
    }

    .rewards-tooltip {
        display: none;
        position: fixed;
        /* Use fixed positioning instead of absolute */
        white-space: nowrap;
        padding: 10px;
        color: black;
        border: 1px solid #222;
        border-radius: 5px;
        z-index: 10;
        transform: translate(-50%, -100%);
        /* Adjust the tooltip to appear slightly above the cursor */
    }

    .boss-card:hover .rewards-tooltip {
        display: block;
        /* Show tooltip when boss-card is hovered */
    }


    .boss-image {
        width: 112px;
        height: 112px;
        object-fit: cover;
        border-radius: 8%;
        margin: 0 auto;
        display: block;
        margin-bottom: 15px;
    }

    .boss-card h3 {
        color: #eee;
        margin-bottom: 10px;
        text-align: center;
    }

    .boss-card p {
        color: #ccc;
        margin-bottom: 10px;
        text-align: center;
    }

    .difficulty-form {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 10px;
    }

    .summon-button {
        background-color: #454545;
        color: #fff;
        padding: 5px 15px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .summon-button:hover {
        background-color: #3b3838;
    }

    .active-raids-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        /* Adjusted from space-between to flex-start */
        margin-bottom: 30px;
    }

    .active-raids-grid .raid-card {
        flex-basis: calc(33.33% - 20px);
        /* Adjusted for 3 cards per row */
        padding: 15px;
        margin-right: 20px;
        /* Added to create spacing between the cards */
        margin-bottom: 20px;
        border-radius: 10px;
        box-shadow: 0px 2px 10px rgba(93, 93, 93, 1);
    }

    .active-raids-grid .boss-image {
        width: 112;
        height: 112;
        object-fit: cover;
        border-radius: 8%;
        margin: 0 auto;
        display: block;
        margin-bottom: 15px;
    }

    .active-raids-grid h3,
    .active-raids-grid p {
        text-align: center;
    }

    .active-raids-grid button {
        display: block;
        margin: 10px auto;
        padding: 5px 15px;
        background-color: #454545;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .active-raids-grid button:hover {
        background-color: #3b3838;
    }

    .modal {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fefefe;
        margin: 15% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
<div id="userModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="modal-content"></div>
    </div>
</div>
</div>
<?php
include 'footer.php';
?>