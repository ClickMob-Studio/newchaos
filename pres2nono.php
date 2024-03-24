<?php
include "header.php";
echo '<style>
div#error {
padding: 20px;
    text-align: center;
    color: red;
    font-size: 1.2em;
}
    </style>';
genHead("Prestige");
$total = $user_class->strength + $user_class->defense + $user_class->speed;
$perc = $total / 100000000;
$perc = ($perc > 100) ? 100 : floor($perc);
$ptperc = $user_class->points / 250;
$ptperc = ($ptperc > 100) ? 100 : floor($ptperc);
$lvlperc = $user_class->level / 4;
$lvlperc = ($lvlperc > 100) ? 100 : floor($lvlperc);
if(isset($_POST['str'])){
    $str = security($_POST['str']);
    $def = security($_POST['def']);
    $spe = security($_POST['spe']);
    if($str > $user_class->strength)
        $error = "You do not have that much strength.";
    elseif($def > $user_class->defense)
        $error = "You do not have that much defense.";
    elseif($spe > $user_class->speed)
        $error = "You do not have that much speed.";
    elseif(25000 > $user_class->points)
        $error = "You do not have enough points to pretige.";
    elseif(400 > $user_class->level)
        $error = "You are not a high enough level to prestige.";
    elseif($str + $def + $spe != 10000000000)
        $error = "You entered an amount of stats that doesn't equal " . prettynum(10000000000) . '.';
    else{
        $db->query("UPDATE grpgusers SET prestige = prestige + 1, points = points - 25000, level = 1, strength = strength - ?, defense = defense - ?, speed = speed - ?, exp = 0 WHERE id = ?");
        $db->execute(array(
            $str,
            $def,
            $spe,
            $user_class->id
        ));
        $success = "You have prestiged!";
    }
}
if(isset($error))
    echo'<div id="error">' . $error . '</div>';
if(isset($success))
    echo'<div id="success">' . $success . '</div>';


$db->query("SELECT `level` FROM prestige_levels WHERE `user_id` = ?");
$db->execute([
    $user_class->id
]);
$rows = $db->fetch_row();

$prestiged_levels = array_map(function($a) {
    return $a['level'];
}, $rows);

$select = "<select name='level' style='padding: 8px 50px;'>";

$levels = array(400, 500, 600, 700, 800, 900, 1000, 1100, 1200, 1300);

foreach ($levels as $level) {
    $style = '';
    if (in_array($level, $prestiged_levels)) {
        $style = 'disabled style="color: #44bd32;font-weight:bold"';
    } else {
        $d = ($user_class->level >= $level) ? '' : 'disabled';
    }
    $select .= "<option name='$level' $d $style>$level</option>";
}

$select .= "</select>";

echo'<table id="newtables" style="margin:auto;">';
    echo'<tr>';
        echo'<th colspan="3">What it will cost?</th>';
    echo'</tr>';
    echo'<tr>';
        echo'<th>What?</th>';
        echo'<th>How Much?</th>';
        echo'<th>Your Progress</th>';
    echo'</tr>';
    echo'<tr>';
        echo'<td colspan="2">Level: </td>';
        echo '<td>' . $select . '</td>';
        //echo '<td><select name="level" style="padding: 8px 50px;"><option>400</option><option>500</option></select></td>';
        //echo'<td>' . prettynum($user_class->level) . ' / ' . prettynum(400) . ' (' . number_format_short(400) . ')</td>';
        // echo'<td>';
        //     echo'<div class="progress-bar blue stripes" style="height:33px;line-height:33px;width:100%;">';
        //         echo'<span style="width: ' . $lvlperc .'%;height:33px;">' . $lvlperc .'%</span>';
        //     echo'</div>';
        // echo'</td>';
    echo'</tr>';
    echo'<tr>';
        echo'<td>Stats: </td>';
        echo'<td>' . prettynum($total) . ' / ' . prettynum(10000000000) . ' (' . number_format_short(10000000000) . ')</td>';
        echo'<td>';
            echo'<div class="progress-bar blue stripes" style="height:33px;line-height:33px;width:100%;">';
                echo'<span style="width: ' . $perc .'%;height:33px;">' . $perc .'%</span>';
            echo'</div>';
        echo'</td>';
    echo'</tr>';
    echo'<tr>';
        echo'<td>Points: </td>';
        echo'<td>' . prettynum($user_class->points) . ' / ' . prettynum(25000) . ' (' . number_format_short(25000) . ')</td>';
        echo'<td>';
            echo'<div class="progress-bar blue stripes" style="height:33px;line-height:33px;width:100%;">';
                echo'<span style="width: ' . $ptperc .'%;height:33px;">' . $ptperc .'%</span>';
            echo'</div>';
        echo'</td>';
    echo'</tr>';
    echo'<tr>';
    if($perc == 100 && $ptperc == 100 && $lvlperc == 100){
        $can = 1;
        echo'<td colspan="3"><span style="font-size:14px;color:green;font-weight:bold;">You can prestige!</span></td>';
    } else {
        $can = 0;
        echo'<td colspan="3"><span style="font-size:14px;color:red;font-weight:bold;">You do not meet the above requirements!</span></td>';
    }
    echo'<tr>';
echo'</table>';
echo'<table id="newtables" style="margin:auto;">';
    echo'<tr>';
        echo'<td><img src="images/muscles.png" style="width:100%;"></td>';
        echo'<td><img src="images/exp.png" style="width:100%;"></td>';
        echo'<td><img src="images/key.png" style="width:100%;"></td>';
    echo'</tr>';
    echo'<tr>';
        echo'<th>Get +10% bonus on trains!</th>';
        echo'<th>Get +10% bonus on EXP! [<a href="#" title="You will gain 10% More EXP in all aspects of the game.">?</a>]</th>';
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
                echo'<th></th>';
                echo'<th>Strength</th>';
                echo'<th>Defense</th>';
                echo'<th>Speed</th>';
            echo'</tr>';
            echo'<tr>';
                echo'<th>What you have:</th>';
                echo'<td>' . prettynum($user_class->strength) . '</td>';
                echo'<td>' . prettynum($user_class->defense) . '</td>';
                echo'<td>' . prettynum($user_class->speed) . '</td>';
            echo'</tr>';
            echo'<tr>';
                echo'<th>How much of each to get rid of:</th>';
                echo'<td><input class="stat_input" style="width: 80%;" type="text" value="0" name="str" /></td>';
                echo'<td><input class="stat_input" style="width: 80%;" type="text" value="0" name="def" /></td>';
                echo'<td><input class="stat_input" style="width: 80%;" type="text" value="0" name="spe" /></td>';
            echo'</tr>';
            echo '<tr>
                    <th>10,000,000,000 (10B) Required</th>
                    <td colspan="3"><span id="stat_total">0</span></td>
            </tr>';
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