<?php
include "header.php";

// General message for users trying to prestige before reaching level 1000
$prestigeMessage = "You need to hit level 1000 before you can prestige again!";

// Check if the user is eligible for prestige based on their level
if ($user_class->level < 1000) {
    echo Message($prestigeMessage);
    include 'footer.php';
    die();
}

// Check for maximum prestige limit
if ($user_class->prestige == 5) {
    echo Message("You cannot Prestige again!!");
    include 'footer.php';
    die();
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Assuming $db is your database connection
    $db->query("UPDATE grpgusers SET prestige = prestige + 1, level = 1 WHERE id = ?");
    $db->execute([$user_class->id]);
    echo Message("You have prestiged!");
    include 'footer.php';
    die();
}
echo '<style>
div#error {
    padding: 20px;
    text-align: center;
    color: red;
    font-size: 1.2em;
}
</style>';
echo '<table id="newtables" style="margin:auto;">';
echo '    <tr>';
echo '        <td><img src="images/muscles1.png" style="width:300px; height:300px;"></td>';
echo '        <td><img src="images/exp.png" style="width:300px; height:300px;"></td>';
// Removed the key.png image and its row as per request.
echo '    </tr>';
echo '    <tr>';
echo '        <th>Get +20% bonus on trains!</th>';
echo '        <th>Get +20% bonus on EXP! [<a href="#" title="You will gain 20% More EXP in all aspects of the game.">?</a>]</th>';
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
echo '<div class="contenthead floaty">';
echo '    <span style="margin: 0; line-height: 27px; text-transform: uppercase; font-size: 20px; text-align: left; text-indent: 25px;"><h4>Prestige</h4></span>';

// Calculate level percentage for display
$lvlperc = min(100, floor(($user_class->level / 1000) * 100));

// Display prestige requirements and progress
echo '<table id="newtables" style="margin:auto;">';
echo '    <tr>';
echo '        <th colspan="3">What it will cost?</th>';
echo '    </tr>';
echo '    <tr>';
echo '        <td>Level: </td>';
echo '        <td>' . prettynum($user_class->level) . ' / ' . prettynum(1000) . ' (' . number_format_short(1000) . ')</td>';
echo '        <td>';
echo '            <div class="progress-bar blue stripes" style="height:33px;line-height:33px;width:100%;">';
echo '                <span style="width: ' . $lvlperc . '%;height:33px;">' . $lvlperc . '%</span>';
echo '            </div>';
echo '        </td>';
echo '    </tr>';
echo '</table>';

// Show the prestige button if the user has reached level 1000

echo'<table id="newtables" style="margin:auto;">';
    echo'<tr>';
        echo'<td><img src="images/muscles.png" style="width:100%;"></td>';
        echo'<td><img src="images/exp.png" style="width:100%;"></td>';
        echo'<td><img src="images/key.png" style="width:100%;"></td>';
    echo'</tr>';
    echo'<tr>';
        echo'<th>Get +20% bonus on trains!</th>';
        echo'<th>Get +20% bonus on EXP! [<a href="#" title="You will gain 20% More EXP in all aspects of the game.">?</a>]</th>';
        echo'<th>Access to a prestige city! [<a href="#" title="Prestige features will be added to this city overtime.">?</a>]</th>';
    echo'</tr>';
echo'</table>';
if($can){
    echo'<br />';
    echo'<br />';
    echo'<form method="post">';
        echo'<table id="newtables" style="width: 100%;margin:auto;table-layout: fixed;">';
        echo '<colgroup>
                <col span="1" style="width: 15%;">
                <col span="1" style="width: 10%;">
                <col span="1" style="width: 10%;">
                <col span="1" style="width: 10%;">
             </colgroup>';
             echo '<tbody>';
            
            echo'<tr>';
                echo'<th colspan="4" style="min-height:30px"><input type="submit" value="Prestige!" /></th>';
            echo'</tr>';
            echo '</tbody>';
        echo'</table>';
    echo'</form>';
}
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