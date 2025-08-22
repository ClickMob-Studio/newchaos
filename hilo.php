<?php
include "header.php";
?>

<div class='box_top'>High Low</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $typecc = rand(1, 4);
        $valuecc = rand(2, 14);
        if (!$_SESSION['cardtypeb'])
            $_SESSION['cardtypeb'] = $typecc;
        if (!$_SESSION['cardvalueb'])
            $_SESSION['cardvalueb'] = 8;
        $cardtypeb = $_SESSION['cardtypeb'];
        $cardvalueb = $_SESSION['cardvalueb'];
        if ($_POST['higher']) {
            if ($cardvalueb == $valuecc)
                $valuecc = rand(2, 14);
            if ($cardvalueb == $valuecc)
                $valuecc = rand(2, 14);
            if ($user_class->money < 10000) {
                diefun('You don\'t have enough money to play high low.');

            }
            $showa = 1;
            if ($cardvalueb < $valuecc) {
                $user_class->money += 5000;
                perform_query("UPDATE grpgusers SET money = ? WHERE id = ?", [$user_class->money, $user_class->id]);
                echo Message('You got it right and won $5,000!');
            } else {
                $user_class->money -= 10000;
                perform_query("UPDATE grpgusers SET money = ? WHERE id = ?", [$user_class->money, $user_class->id]);
                echo Message('Sorry. You got it wrong and lost $10,000.');
            }
            $_SESSION['cardtypeb'] = $typecc;
            $_SESSION['cardvalueb'] = $valuecc;
            $cardtypeb = $typecc;
            $cardvalueb = $valuecc;
        }
        if ($_POST['lower'] != "" && $_POST['higher'] == "") {
            if ($user_class->money < 10000) {
                diefun('You don\'t have enough money to play high low.');

            }
            $showa = 1;
            if ($cardvalueb > $valuecc) {
                $user_class->money += 5000;
                perform_query("UPDATE grpgusers SET money = ? WHERE id = ?", [$user_class->money, $user_class->id]);
                echo Message('You got it right and won $5,000!');
            } else {
                $user_class->money -= 10000;
                perform_query("UPDATE grpgusers SET money = ? WHERE id = ?", [$user_class->money, $user_class->id]);
                echo Message('Sorry. You got it wrong and lost $10,000.');
            }
            $_SESSION['cardtypeb'] = $typecc;
            $_SESSION['cardvalueb'] = $valuecc;
            $cardtypeb = $typecc;
            $cardvalueb = $valuecc;
        }
        $mo = prettynum($user_class->money, 1);

        echo "
	<table id='newtables' style='background:none;width:50%;'>
		<tr style='background:none;'>
			<td style='background:none;'><small><b>Cash:</b> {$mo} dollars<br /><br />Simply guess if the next card is valued higher or lower.<br />If you lose, you lose $10,000.<br />If you win, you win $5,000.<br />Ace is high and 2's are low.<br />Ties go to the game.</small></td>
		</tr>
		<tr style='background:none;'>
			<td style='background:none;'><img src='images/Slots 2/{$cardvalueb}.gif' /></td>
		</tr>
		<tr style='background:none;'>	
			<th style='background:none;'>
				<form method='post'>
					<input type='submit' name='higher' value='Higher' class='button'>&nbsp;&nbsp;
					<input type='submit' name='lower' value='Lower' class='button'>
				</form>
			</th>
		</tr>
	</table>";

        include "footer.php";
        ?>