<?php
include 'header.php';
if ($user_class->spins != 0) {
    if ($user_class->hospital == 0) {
        if ($user_class->jail == 0) {
            if (isset($_POST['submit'])) {
                security($_POST['guess'], 'num');
                $guess = strip_tags($_POST['guess']);
                $guess = addslashes($guess);
                if ($guess < 1 or $guess > 10) {
                    echo Message("You must enter a number between 1 and 10.<br /><br /><a href='roulettespin.php'>Go Back</a>");
                    include "footer.php";
                    die();
                }
                if ($user_class->money < 1000)
                    echo Message("You don't have enough money to spin!");
                else {
                    $newmoney = $user_class->money - 1000;
                    perform_query("UPDATE `grpgusers` SET `spins` = spins-1, `money` = ? WHERE `id` = ?", [$newmoney, $user_class->id]);
                    $roulette = rand(1, 10);
                    $random = rand(2000, 6000);
                    if ($roulette == $guess) {
                        $newmoney = $user_class->money + $random;
                        perform_query("UPDATE `grpgusers` SET  `money` = ? WHERE `id` = ?", [$newmoney, $user_class->id]);
                        echo Message("Your Prediction: $guess&nbsp;&nbsp;Outcome: $roulette<br />Congratulations, you have won $" . prettynum($random) . "!");
                    } else
                        echo Message("Your Prediction: $guess&nbsp;&nbsp;Outcome: $roulette<br />Sorry, you didn't win anything.");
                }
            }
            ?>
            <h3>Roulette</h3>
            <hr>
            <tr>
                <td class="contentspacer"></td>
            </tr>
            <tr>
                <td class="contenthead">Roulette </td>
            </tr>
            <tr>
                <td class="contentcontent">
                    Feeling lucky at roulette? It only costs $1,000 a spin and you have <?php echo $user_class->spins; ?> spins left
                    today! All you have to do is predict the number that will be chosen at random!
                    <br><br>
                    <form method='post'>
                        Predict Number (1 - 10):&nbsp;<input type='text' name='guess' size='10' maxlength='2'><br />
                        <input type='submit' name='submit' value='Predict'>
                    </form>
                </td>
            </tr>
            <?php
        } else
            echo Message("You can't play Roulette if your in prison!");
    } else
        echo Message("You can't play Roulette if your in hospital!");
} else
    echo Message("You have already played Roulette 20 times today!");
include 'footer.php';
?>