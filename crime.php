<?php

include 'header.php';

?>
<div class='box_top'>Crimes</div>
						<div class='box_middle'>
							<div class='pad'>
<style>
.star-rating {
    color: #ccc; /* Light grey for unearned stars */
    font-size: 16px;
}

.star-rating .gold {
    color: #ffcc00; /* Gold for earned stars */
}

/* Sidebar and segments */
.sidebar {
    position: fixed;
    right: 0;
    top: 0;
    width: 65px; /* Adjusted width to fit the sidebar and milestone header */
    height: 100vh;
    background-color: #333; /* Dark background color */
    padding: 20px 10px; /* Padding adjusted for your design */
    z-index: 1000; /* Large z-index to ensure it's on top of other elements */
    display: flex;
    flex-direction: column;
    justify-content: center; /* Center content vertically */
    align-items: flex-end; /* Align content to the right */
    box-shadow: 0 0 10px 5px #4D4D4D; /* Dark grey glow */
}

.milestone-header {
    color: #ffd700; /* Gold text */
    padding: 10px; /* Padding for the text */
    writing-mode: vertical-lr; /* Vertical text */
    transform: rotate(180deg); /* Correct orientation of the text */
    transform-origin: bottom; /* Pivot at the bottom */
    position: absolute;
    right: 25px; /* Position the text to the left of the progress bar */
    top: 50%; /* Center the text vertically */
    z-index: 1010; /* Ensure the text is above the progress bar */
    margin-top: -50px; /* Adjust based on the text's width to center it */
}

.milestone-container {
    width: 20px; /* Exact width of the progress bar */
    background-color: #ccc; /* Light grey background */
    position: absolute;
    right: 10px; /* Right align with the sidebar padding */
    top: 20px; /* Align top with the sidebar padding */
    bottom: 20px; /* Align bottom with the sidebar padding */
    z-index: 1005; /* Above the sidebar but below the text */
}

.milestone-progress-bar {
    background-color: #999; /* Darker grey for the progress */
    width: 100%; /* Full width of the container */
    height: 0%; /* Initial height will be set dynamically */
    transition: height 0.5s ease-in-out; /* Smooth transition for height changes */
}

.milestone-segment {
    background-color: #999; /* Dark grey for the filled segment */
    width: 100%; /* Full width */
    height: 10%; /* 10% height for each segment */
    border-bottom: 1px solid #333; /* Border between segments */
    position: relative; /* Needed for absolute positioning of the tooltip */
}

/* Tooltip styles */
.milestone-tooltip {
    visibility: hidden;
    width: 300px; /* Width auto to fit content */
    max-width: 120px; /* Max width to prevent overflow */
    background-color: #333; /* Tooltip background color similar to sidebar */
    color: #fff;
    text-align: center;
    padding: 5px 10px; /* Padding around the text */
    border-radius: 6px;
    position: absolute;
    z-index: 1011; /* Ensure it's above everything in the sidebar */
    top: 50%; /* Center vertically */
    left: -140%; /* Adjust left positioning to not overlap with sidebar */
    transform: translate(-100%, -50%); /* Center tooltip horizontally and vertically to the segment */
    box-shadow: 0 0 8px rgba(0, 0, 0, 0.6); /* Dark glow effect */
    opacity: 0;
    transition: opacity 0.2s ease-in-out, visibility 0s linear 0.2s; /* Adjust for quicker transition */
}

.milestone-tooltip::after {
    content: "";
    position: absolute;
    top: 50%;
    right: -5px; /* Arrow pointing towards the segment */
    margin-top: -5px; /* Center the arrow vertically */
    border-width: 5px;
    border-style: solid;
    border-color: transparent transparent transparent #333; /* Arrow color similar to tooltip */
}

/* Adjust hover state for immediate tooltip disappearance */
.milestone-segment:hover .milestone-tooltip {
    visibility: visible;
    opacity: 1;
    transition-delay: 0s; /* Remove delay to show tooltip immediately */
}

/* Ensure tooltips disappear immediately when not hovering */
.milestone-segment .milestone-tooltip {
    transition: opacity 0.2s ease-in-out, visibility 0s linear 0s; /* Adjust disappearance timing */
</style>



<script>
    $(document).ready(function() {
        // Your existing jQuery scripts
    });
</script>




<?php
// Perform the crime update query
$db->query("UPDATE grpgusers SET crimes = 'crime' WHERE id = ?");
$db->execute(array(
    $user_class->id
));

// Check if the user is restricted from committing crimes due to certain conditions
$restrictions = array(
    'jail' => "You can't do crimes if you're in prison!",
    'fbitime' => "You can't do crimes if you're in FBI Jail!",
    'hospital' => "You can't do crimes if you're in hospital!"
);

foreach ($restrictions as $condition => $message) {
    if ($user_class->$condition > 0) {
        diefun($message);
    }
}

// Process crime action
if (!empty($_GET['action']) && $_GET['action'] == 'crime') {
    // Validate nonce
    if (isset($_GET['nonce']) && !empty($_GET['nonce'])) {
        if ($_GET['nonce'] != $_SESSION['crimenonce']) {
            $nonce = md5(uniqid(rand(), true));
            $_SESSION['crimenonce'] = $nonce;
        }
    } else {
        $nonce = md5(uniqid(rand(), true));
        $_SESSION['crimenonce'] = $nonce;
    }

    // Fetch crime details from database
    $id = security($_GET['id']);
    $db->query("SELECT * FROM crimes WHERE id = ?");
    $db->execute(array($id));
    $crime = $db->fetch_row(true);

    if (empty($crime)) {
        diefun("Crime does not exist.");
    }

    // Extract crime details
    $nerve = $crime['nerve'];
    $time = floor(($nerve - ($nerve * 0.5)) * 6);
    $name = $crime['name'];
    $stext = 'You successfully managed to ' . $name;
    $ftext = 'You failed to ' . $name;
    $chance = rand(0, 100);

    // Calculate money and experience gains
    $money = ((50 * $nerve) + 15 * ($nerve - 1)) * 1;
    $exp = ((10 * $nerve) + 2 * ($nerve - 1));

    // Handle bonuses
    if ($user_class->exppill >= time()) {
        $chance = 100;
        $money *= 1.0;
        $exp *= 2.0;
    }
    
    // Assuming you've already fetched the crime details and are about to calculate experience...

// Step 1: Retrieve crime count for the user and specific crime
$crimeCount = 0;
$db->query("SELECT `count` FROM crimeranks WHERE userid = ? AND crimeid = ?");
$db->execute(array($user_class->id, $id));
$crimeRankResult = $db->fetch_row(true);

if ($crimeRankResult) {
    $crimeCount = (int)$crimeRankResult['count'];
}

$level = 0; // Default level

if ($crimeCount >= 100 && $crimeCount < 10000) {
    $level = 1;
} elseif ($crimeCount >= 10000 && $crimeCount < 10000000) {
    $level = 2;
} elseif ($crimeCount >= 10000000 && $crimeCount < 20000000) {
    $level = 3;
} elseif ($crimeCount >= 20000000 && $crimeCount < 20000000) {
    $level = 4;
} elseif ($crimeCount >= 20000000) {
    $level = 5;
}

// Your level determination logic here...

// Step 3: Apply bonus experience based on level
$bonusExperienceMultiplier = 1 + ($level * 0.1);
$exp = round($exp * $bonusExperienceMultiplier, 2);


    // Check for pack bonuses
    if ($user_class->pack1 == 1 && $user_class->pack1time >= time()) {
        $exp *= 1.2; // Apply the 20% bonus
    }

    if ($user_class->pack1 == 2 && $user_class->pack1time >= time()) {
        $money *= 1.2; // Apply the 20% bonus
    }

    // Handle prestige bonuses
    if ($user_class->prestige > 0) {
        $exp *= 1.20; // Increase $exp by 20% for each point of prestige
    }
    
    
    
    $db->query("SELECT * FROM gamebonus WHERE ID = 1 LIMIT 1");
    $db->execute();
    $bonus_row = $db->fetch_row(true);

    $debug['worked'] = $bonus_row;

    if ($bonus_row['Time'] > 0) {
        $exp *= 2;
        $money *= 1;
        $chance = 100;
    }


    // Calculate gang tax
    $gtax = 0;
    if ($user_class->gang != 0) {
        $gang_class = new Gang($user_class->gang);
        if ($gang_class->tax > 0) {
            $gtax = $money * ($gang_class->tax / 100);
        }
    }

    $money = $money - $gtax;

    // Process crime outcome
    if ($user_class->nerve >= $nerve) {
        if ($chance < 5) {
            echo "<div class='failure-message'>$ftext<br /><br /></div>";
            $user_class->nerve -= $nerve;
            $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, nerve = nerve - ? WHERE id = ?");
            $db->execute(array(
                $nerve,
                $user_class->id
            ));
        } elseif ($chance < 7) {
            echo "<div class='failure-message'>$ftext You were hauled off to jail for 5 minutes.<br /><br /></div>";
            $user_class->nerve -= $nerve;
            $db->query("UPDATE grpgusers SET crimefailed = crimefailed + 1, caught = caught + 1, jail = 300, nerve = nerve - ? WHERE id = ?");
            $db->execute(array(
                $nerve,
                $user_class->id
            ));
        } else {
            // Process successful crime
            mission('c');
            $which = ($nerve >= 50) ? "crimes50" : (($nerve >= 25) ? "crimes25" : (($nerve >= 10) ? "crimes10" : (($nerve >= 5) ? "crimes5" : "crimes1")));
            newmissions($which);
            gangContest(array('crimes' => 1, 'exp' => $exp));
            bloodbath('crimes', $user_class->id, $nerve / $user_class->level);
            $user_class->money += $money;
            $user_class->nerve -= $nerve;
            $totaltax = $gtax;

            // Update user and gang records
            $db->query("UPDATE gangs SET moneyvault = moneyvault + ? WHERE id = ?");
            $db->execute(array(
                $gtax,
                $user_class->gang
            ));

            // Output success message
            $message = $stext . "<br />You received {$exp} exp and \${$money}.";
            if ($gtax > 0) {
                $message .= " (Gang Tax: \${$gtax})";
            }
            $message .= "<br /><br />You have {$user_class->nerve} nerve left!";
            echo Message("<div class='success-message'>$message</div>");




 // Fetch the current count for the user and crime
$db->query("SELECT count FROM crimeranks WHERE userid = ? AND crimeid = ?");
$db->execute(array($user_class->id, $id));
$crimeRankResult = $db->fetch_row(true);

// Determine the new count
$newCount = $crimeRankResult ? $crimeRankResult['count'] + 1 : 1;

if ($crimeRankResult) {
    // A record exists, update it with the new count
    $db->query("UPDATE crimeranks SET count = ? WHERE userid = ? AND crimeid = ?");
    $db->execute(array($newCount, $user_class->id, $id));
} else {
    // No record exists, insert a new one with count = 1
    $db->query("INSERT INTO crimeranks (userid, crimeid, count) VALUES (?, ?, 1)");
    $db->execute(array($user_class->id, $id));
}


            // EXP 20% BUFF
            $exp = $exp + (($exp / 100) * 80);

            // Update user records in database
            $db->query("UPDATE grpgusers SET loth = loth + ?, exp = exp + ?, crimesucceeded = crimesucceeded + 1, crimemoney = crimemoney + ?, money = money + ?, nerve = nerve - ?, todaysexp = todaysexp + ?, expcount = expcount + ?, totaltax = totaltax + ? WHERE id = ?");
            $db->execute(array(
                $exp,
                $exp,
                $money,
                $money,
                $nerve,
                $exp,
                $exp,
                $totaltax,
                $user_class->id
            ));
        }
    } else {
        echo Message("<B>You don't have enough nerve for that crime.<a href='crime.php'></a>");
    }
}

// Output the header for crime container
echo '<div class="contenthead floaty">';
echo '    <span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;"></span>';
echo '<center>';
echo '<a href="?spend=refnerve&crime"><img src="images/wand.png" /><button>Refill Nerve</button></a>';
echo '</center>';

// Check if crimes are giving double EXP payouts
$result2 = mysql_query("SELECT * FROM gamebonus WHERE ID = 1");
$worked = mysql_fetch_array($result2);

// Output relevant message if double EXP payout is active
if ($worked['Time'] > 0) {
    $_tt = secondsToHumanReadable($bonus_row['Time'] * 60);
    echo Message('<br><br><span class="pulsate" style="color:green;font-weight:bold;display:block;text-align:center;font-size:1.3em;">Crimes are currently giving Double EXP Payouts You have  <font color=orange>' . $_tt . '</font>  left!!</span><br />');
}

// Function to determine the number of stars based on crime count
function determineStars($count) {
    if ($count >= 9999 && $count <= 100000) {
        return 1;
    } elseif ($count >= 100001 && $count <= 2000000000000000) {
        return 2;
    } elseif ($count >= 10000000000 && $count <= 100000000000) {
        return 3;
    } elseif ($count >= 20000000 && $count <= 2000000000000) {
        return 4;
    } elseif ($count >= 20000000) {
        return 5;
    } else {
        return 0; // No stars if count is 0
    }
}

// Output container for crimes
echo '<div class="crimes-container">';

$max = ($user_class->nerref == 2) ? $user_class->maxnerve : $user_class->nerve;
$db->query("SELECT * FROM crimes WHERE nerve <= ? ORDER BY nerve ASC");
$db->execute(array($max));
$rows = $db->fetch_row();

$chunks = array_chunk($rows, 4, true);

foreach ($chunks as $inner_rows) {
    echo '<div class="crime-row">';
    foreach ($inner_rows as $row) {
        $db->query("SELECT count FROM crimeranks WHERE userid = ? AND crimeid = ?");
        $db->execute(array($user_class->id, $row['id']));
        $crimeRank = $db->fetch_row(true);
        $count = $crimeRank ? $crimeRank['count'] : 0;
        $stars = determineStars($count);

        $nonce = md5(uniqid(rand(), true));
        $_SESSION['crimenonce'] = $nonce;
        echo '<div class="crime" style="display: inline-block; margin-right: 10px;">';
        echo $row['name'] . '<br />';
        echo $row['nerve'] . ' Nerve<br />';
        
        // Display 5 stars with earned stars in gold
        echo '<div class="star-rating">';
        for ($i = 1; $i <= 5; $i++) {
            echo $i <= $stars ? '<span class="gold">&#9733;</span>' : '&#9733;'; // Gold for earned stars, default color for unearned
        }
        echo '</div>';

        echo '<a href="?action=crime&id=' . $row['id'] . '&nonce=' . $nonce . '" id="crimebutton" onclick="disableButton()">';
        echo '<button id="crimeBtn">Do Crime</button>';
        echo '</a>';
        echo '</div>';
    }
    echo '</div>';
}


echo '</div>';

echo '</div>';
// Include the footer
include 'footer.php';
?>
