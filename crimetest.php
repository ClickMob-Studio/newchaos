<?php

include 'header.php';

?>

<script>
    $(document).ready(function() {

        $(window).keydown(function(event) {
            if (event.keyCode == 116) {
                event.preventDefault();
                return false;
            }
        });

        // Function to disable crime button temporarily
        function disableButton() {
            var crimeBtn = document.getElementById("crimeBtn");
            crimeBtn.disabled = true;
            setTimeout(function() {
                crimeBtn.disabled = false;
            }, 600); // 0.6 seconds
        }

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
            echo "<div class='success-message'>$message</div>";

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
        echo "<B>You don't have enough nerve for that crime.<br /><br /><br /><br /><a href='crime.php'></a>";
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
    echo '<br><br><span class="pulsate" style="color:green;font-weight:bold;display:block;text-align:center;font-size:1.3em;">Crimes are currently giving Double EXP Payouts You have  <font color=white>' . $_tt . '</font>  left!!</span><br />';
}

// Output container for crimes
echo '<div class="crimes-container">';

// Fetch crimes from database based on user's nerve
$max = ($user_class->nerref == 2) ? $user_class->maxnerve : $user_class->nerve;
$db->query("SELECT * FROM crimes WHERE nerve <= ? ORDER BY nerve ASC");
$db->execute(array($max));
$rows = $db->fetch_row();

// Chunk the fetched rows into groups of 4 for display
$chunks = array_chunk($rows, 4, true);

// Iterate over each chunk to create rows of crimes
foreach ($chunks as $inner_rows) {
    echo '<div class="crime-row">'; // Start of crime-row
    foreach ($inner_rows as $row) {
        $nonce = md5(uniqid(rand(), true));
        $_SESSION['crimenonce'] = $nonce;
        echo '<div class="crime" style="display: inline-block; margin-right: 10px;">'; // Set display style to inline-block
        echo $row['name'] . '<br />';
        echo $row['nerve'] . ' Nerve<br />';
        echo '<a href="?action=crime&id=' . $row['id'] . '&nonce=' . $nonce . '" id="crimebutton" onclick="disableButton()">';
        echo '<button id="crimeBtn">Do Crime</button>';
        echo '</a>';
        echo '</div>';
    }
    echo '</div>'; // End of crime-row
}

// End of crimes container
echo '</div>';

// Include the footer
include 'footer.php';
?>
