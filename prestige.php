<?php
include "header.php";
?>
	
	<div class='box_top'>Prestige</div>
						<div class='box_middle'>
							<div class='pad'>
								<?php
// General message for users trying to prestige before reaching level 1000

// Allow access if the user is an admin or their level is >= 1000


// Rest of your prestige functionality goes here...


// Check for maximum prestige limit

echo '<style>
.content-area {
    
    padding: 20px;
    border-radius: 5px;
    margin-bottom: 20px;
}

table#newtables {
    width: 100%;
    margin: auto;
    border-spacing: 0;
    border-collapse: collapse;
}

table#newtables th, table#newtables td {
    padding: 10px;
    border: 1px solid #444; /* Subtle borders for the cells */
}


.progress-container {
    width: 100%; /* Full width of the container */
    height: 25px; /* Maintain the original height */
     border-radius: 5px;
    overflow: hidden;
    margin: 20px auto; /* Center the bar and add some vertical spacing */
}

.custom-progress-bar {
    width: 0; /* Start with 0 width and grow as needed */
    height: 100%; /* Full height of the container */
    background-image: linear-gradient(45deg, red 25%, white 25%, white 50%, red 50%, red 75%, white 75%, white); /* Red and white stripes */
    background-size: 50px 50px; /* Size of the stripes */
    animation: move-stripes 2s linear infinite; /* Slower animation */
    display: flex; /* Use flexbox to center content */
    justify-content: center; /* Center horizontally */
    align-items: center; /* Center vertically */
    color: black; /* Text color */
    font-size: 20px; /* Increased text size */
    border-radius: 5px;
    transition: width 0.4s ease-in-out;
}

@keyframes move-stripes {
    0% { background-position: 0 0; }
    100% { background-position: 50px 0; }
}



div#error, div#message {
    padding: 20px;
    text-align: center;
    color: red;
    font-size: 1.2em;
    margin-bottom: 20px;
    border: 1px solid #444; /* Subtle borders for the message */
    border-radius: 5px;
}
.custom-button-container {
    padding: 5px; /* Padding around the button */
    text-align: center; /* Keep the button centered */
}


.custom-button:disabled {
    cursor: not-allowed;
    opacity: 0.7; /* Slightly higher opacity for better visibility */
}

stats-contents {
    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19); /* Dark shadow for depth */
    padding: 20px; /* Padding around content */
    margin: 20px 0; /* Margin for spacing around the div */
    border-radius: 10px; /* Rounded corners */
}

.center-text { text-align: center; } /* Class for centering text */
 }
</style>';
echo '<style>
/* Existing styles */
</style>';
$prestigeLevel = $user_class->prestige;

// Calculate the bonus percentage for both Training and EXP
$bonusPercentage = $prestigeLevel * 20;

// Start the table for prestige badges
echo '<div class="contenthead floaty">';


// Existing content starts here

echo '<div class="stats-contents">';
echo '<table id="newtables" style="width: 100%; margin: auto; border-collapse: collapse;">';
echo '    <tr>';
echo '        <td colspan="3" class="center-text"><h4>You are currently getting a <span style="color:yellow;">' . $bonusPercentage . '%</span> bonus<br> towards Training</h4></td>';
echo '        <td colspan="3" class="center-text"><h4>You are also getting a <span style="color:yellow;">' . $bonusPercentage . '%</span> bonus<br> towards EXP</h4></td>';
echo '    </tr>';
echo '</table>';
echo '</div>';



echo '<style>
#newtables td {
    text-align: center; /* Center align table cell content */
    vertical-align: middle; /* Middle align table cell content */
}
</style>';

echo '<table id="newtables" style="margin:auto;">';
echo '    <tr>';
echo '        <td><img src="images/muscles1.png" style="width:100px; height:100px;"></td>';
echo '        <td><img src="images/exp.png" style="width:100px; height:100px;"></td>';
echo '    </tr>';
// Removed the key.png image and its row as per request.
echo '    </tr>';
echo '    <tr>';
echo '        <th><center> Get An Additional <font color=yellow><b>+20% bonus</b></font> on <font color=red><b>Trains</b></font> per Prestige Level!</th>';
echo '       <th><center> Get An Additional <font color=yellow><b>+20% bonus</b></font> on <font color=red><b>EXP</b></font> per Prestige! [<a href="#" title="You will gain 20% More EXP in all aspects of the game.">?</a>]</th>';
// Removed the description for the "Access to a prestige city" as per request.
echo '    </tr>';
echo '</table>';

if ($can) {
    echo '<br />';
    echo '<br />';
    echo '<form method="post">';
    echo '    <table id="newtables" style="width: 100%; margin:auto; table-layout: fixed;">';
    echo '        <colgroup>';
    echo '            <col span="1" style="width: 15%;">';
    echo '            <col span="1" style="width: 10%;">';
    echo '            <col span="1" style="width: 10%;">';
    echo '            <col span="1" style="width: 10%;">';
    echo '        </colgroup>';
    echo '        <tbody>';
    echo '            <tr>';
    echo '                <th colspan="4" style="min-height:30px"><input type="submit" value="Prestige!" /></th>';
    echo '            </tr>';
    echo '        </tbody>';
    echo '    </table>';
    echo '</form>';
}

// Calculate the remaining levels to reach 1000 and display it
$levelsToGo = 1000 - $user_class->level; // Remaining levels to reach 1000
echo '<div style="text-align:center; margin-bottom:20px;">';




echo '<table id="newtables" style="margin:auto; width:100%; table-layout:fixed;">';
echo '    <tr>';

// Generate cells for badges and descriptions
for($i = 1; $i <= 5; $i++) {
    echo '        <td style="text-align:center;">';
    echo '            <img src="images/prestige_' . $i . '.png" style="width:80px; height:80px;">';
    echo '            <br>Prestige ' . $i . '';
    echo '        </td>';
}

echo '    </tr>';
echo '</table>';

// Calculate level percentage for display
$lvlperc = min(100, floor(($user_class->level / 1000) * 100));

// Display prestige requirements and progress
echo '<table id="newtables" style="margin:auto;">';
echo '    <tr>';
echo '        <th colspan="3" class="center-text"><h4><center>You currently need <span style="color:yellow;">' . $levelsToGo . '</span> levels till your next Prestige.</h4></center></th>';
echo '    </tr>';
echo '    <tr>';
echo '        <td colspan="2"><h4>Current Prestige Level: <span style="color:red;"><b>' . $user_class->prestige . '</b></span></h4></td>';
echo '        <td><h4>' . prettynum($user_class->level) . ' / ' . prettynum(1000) . ' (' . number_format_short(1000) . ')</h4></td>';
echo '    </tr>';
echo '<table id="newtables" style="margin:auto;">';
echo '    <tr>';
echo '        <td colspan="3">'; // Span the progress bar across all columns
echo '            <div class="progress-container">';
echo '                <div class="custom-progress-bar" style="width: ' . $lvlperc . '%;"><h4>' . $lvlperc . '%</h4></div>';
echo '            </div>';
echo '        </td>';
echo '    </tr>';
echo '</table>';


// Show the prestige button if the user has reached level 1000

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the user has reached the maximum prestige level
    if ($user_class->prestige >= 5) {
        echo Message("You cannot Prestige again!!");
    } else if ($user_class->level >= 1000) {
        // User is eligible to prestige, and hasn't reached the maximum prestige level
        // Assuming $db is your database connection
        $db->query("UPDATE grpgusers SET prestige = prestige + 1, level = 1, exp = 0 WHERE id = ?");
        $db->execute([$user_class->id]);
        // Assuming the prestige level is updated in the object, you might need to refresh it or adjust the object property accordingly
        echo Message("Congratulations! You have prestiged to level " . ($user_class->prestige + 1) . ".");
    } else {
        // User is not eligible to prestige due to not being at least level 1000
        echo Message("You must be at least level 1000 to prestige.");
    }
    include 'footer.php';
    die();
}


// Ensure the prestige button is always displayed but disabled unless the user is level 1000 or higher
echo '<div class="custom-button-container">';
echo '<form method="post" style="text-align:center;">';
if ($user_class->level >= 1000) {
    echo '<input type="submit" class="custom-button" value="Prestige!" />';
} else {
    echo '<input type="submit" class="custom-button" value="Sorry, You Cannot Prestige Yet" disabled />';
}
echo '</form>';
echo '</div>';


?>

<script>
$(".stat_input").change(function(e) {
    console.log($(this));
    var sum = 0;
    $('.stat_input').each(function() {
        sum += Number($(this).val());
    });
    console.log(sum);
    sum = String(sum).replace(/(.)(?=(\d{3})+$)/g,'$1,')
    $("#stat_total").html(sum);
});
</script>

<?php
include "footer.php";
?>