<?php
include 'header.php';

if ($user_class->admin != 1)
    header('location: index.php');

echo '<link rel="stylesheet" href="css/bars-1to10.css?'.uniqid().'">';

$expBonus = $user_class->prestige_exp * 10;
$gymBonus = $user_class->prestige_gym * 10;
$levelPercentage = ($user_class->level >= 500) ? 100 : ($user_class->level / 500) * 100;

// if ($user_class->level >= 500) {

//     echo '<div class="floaty" style="margin:2px;"><span style="color:white;font-size:14px">

//     Congratulations in reaching level 500!.  You now have an option to Prestige.<br>

//     If you continue to Prestige the following will happen:<br><br>
//     Your <strong>level</strong> will be reset to <strong>1</strong><br>
//     Your <strong>energy</strong> will be reset to <strong>10</strong><br>
//     Your <strong>exp</strong> will be reset to <strong>0</strong><br>
//     <br>
//     You will be awarded with +1 Prestige Token<br>
//     You will be granted a Prestige star <img src="images/pres_1.png"> next to your name so you can stand out from the others!<br>
//     <br>
//     There is no change to any other stats

//     </span></div><br>';
// }

if(isset($_GET['prestige'])) {
    if ($user_class->level >= 500) {
        echo '<div class="floaty" style="margin:2px;"><span style="color:white;font-size:14px">
        <br>
        If you continue to Prestige the following will happen:<br><br>
        Your <strong>level</strong> will be reset to <strong>1</strong><br>
        Your <strong>energy</strong> will be reset to <strong>10</strong><br>
        Your <strong>exp</strong> will be reset to <strong>0</strong><br>
        <br>
        </span>
        <a class="preslink" href="prestige.php?doit">I understand - lets do it!</a><br><br>
        <a class="preslink1" href="index.php">Hell No - Get me out of here!</a>
        </div><br>';
    } else {
        echo '<script>window.location.href = "prestige.php";</script>';
    }
} else if (isset($_GET['doit'])) {
    if ($user_class->level >= 500) {
		$db->query("UPDATE grpgusers SET prestige = prestige + 1, level = 1, prestige_tokens = prestige_tokens + 1, energy = 10, nerve = 5, exp = 0 WHERE id = ?");
		$db->execute(array(
			$user_class->id
        ));
        echo '<script>window.location.href = "prestige.php";</script>';
    } else {
        echo '<script>window.location.href = "prestige.php";</script>';
    }
} else {

if (isset($_GET['upgrade'])) {
    if ($_GET['upgrade'] == 'gym' || $_GET['upgrade'] == 'exp') {

        if ($user_class->prestige_tokens >= 1) {
            echo '<div class="floaty" style="margin:2px;"><span style="color:white;font-size:14px">
            <br>
            Are you sure you wish to spend <strong>1 Prestige Token</strong> to get <strong>+10% ' . ucwords($_GET['upgrade']) . ' Bonus</strong>?<br><br>

            <a href="prestige.php?conf=' . $_GET['upgrade'] . '">Yes</a><br>
            <a href="prestige.php">Cancel</a><br><br>

            </span></div><br>';
        } else {
            echo '<div class="floaty" style="margin:2px;"><span style="color:white;font-size:14px">Sorry do you not have enough prestige tokens</span></div>';
        }
    }
} else if (isset($_GET['conf'])) {
    if ($_GET['conf'] == 'gym' || $_GET['conf'] == 'exp') {
        if ($user_class->prestige_tokens >= 1) {

            $db->query("UPDATE grpgusers SET prestige_tokens = prestige_tokens - 1, prestige_".$_GET['conf']." = prestige_".$_GET['conf']." + 1 WHERE id = ?");
            $db->execute(
                array($user_class->id)
            );

        }
        echo '<script>window.location.href = "prestige.php";</script>';
    }
} else {

    echo'<table id="newtables" style="margin:auto;">';
    echo '<thead><th colspan="4" style="color:white;">Prestige</th></thead>';
    echo '<tr><td colspan="4">Current Prestige ' . $user_class->prestige . ' ' . (($user_class->prestige > 0) ? '<img src="images/pres_' . $user_class->prestige . '.png">' : "") . '</td></tr>';
    echo '<tr><td colspan="4">Gym & EXP Gains Increased By ' . $user_class->prestige * 10 . '%</td>';
	echo'<tr>';
		echo'<th>Required Level</th>';
		echo'<th>Current Level</th>';
        echo'<th width="33%">Progress</th>';
        echo'<th>Reward</th>';
	echo'</tr>';
	echo'<tr>';
		echo'<td>500</td>';
		echo'<td>' . prettynum($user_class->level) . '</td>';
		echo'<td>';
			echo'<div class="progress-bar" style="height:20px;line-height:20px;width:100%; background-color:#6C7A89;">';
				echo'<span style="width: ' . $levelPercentage .'%;height:20px;color:white;">' . $levelPercentage .'%</span>';
			echo'</div>';
        echo'</td>';
        echo'<td><img src="images/prestige_token.png" width="20%"><br>+1 Prestige Token</td>';
    echo'</tr>';
echo'</table>';
if($user_class->level >= 500){
	echo'<form method="post">';
		echo'<table id="newtables" style="margin:auto;">';
			echo'<tr>';
				echo'<th colspan="4"><br><a style="color: #3184ff; letter-spacing: 2px;" href="prestige.php?prestige">Prestige Now!</a><br><br>';
			echo'</tr>';
		echo'</table>';
	echo'</form><br>';
}

    echo'<table id="newtables" style="margin:auto;">';
    echo '<thead><th colspan="4" style="color:white;">Prestige Upgrades</th></thead>';
    echo'<thead><th>Upgrade</th><th>Cost</th><th>Upgraded</th><th>Action</th></thead>';
    echo'<tbody>';
    /*echo'<tr>';
    echo'<td>Increase Gym Gains +10%</td>';
    echo '<td>1 Token(s)</td>';
    echo '<td width="23%"><select id="gymRating">
        <option value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
        </select></td>';
    echo '<td>' . (($user_class->prestige_tokens > 0) ? '<a href="prestige.php?upgrade=gym">Upgrade</a>' : 'Upgrade') . '</td>';
    echo'</tr>';
    echo'<tr>';
    echo'<td>Increase Crimes EXP +10%</td>';
    echo '<td>1 Token(s)</td>';
    echo '<td width="23%"><select id="expRating">
        <option value="0">0</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
        </select></td>';
        echo '<td>' . (($user_class->prestige_tokens > 0) ? '<a href="prestige.php?upgrade=exp">Upgrade</a>' : 'Upgrade') . '</td>';
    echo'</tr>';*/
    echo '</tbody>';
    echo'</table><br>';

    echo'<table id="newtables" style="margin:auto;">';
    echo'<thead><th colspan="2">Your Prestige Tokens</th></thead>';
    echo'<tbody><tr><td><img src="images/prestige_token.png"></td><td>' . $user_class->prestige_tokens . '</td></tr></tbody>';
    echo'</table>';
}

}

include 'footer.php';
?>
<script src="js/jquery.barrating.js"></script>
<script>
    $(function() {
        $('#gymRating').barrating('show', {
            theme: 'bars-1to10',
            readonly: true,
            allowEmpty: true,
            initialRating: '<?php echo $user_class->prestige_gym;?>',
            showSelectedRating: false
        });
        $('#expRating').barrating('show', {
            theme: 'bars-1to10',
            readonly: true,
            allowEmpty: true,
            initialRating: '<?php echo $user_class->prestige_exp;?>',
            showSelectedRating: false
        });
        $("a[data-rating-value='0']").hide();
    });
</script>