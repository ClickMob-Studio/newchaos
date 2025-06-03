<?php
include 'header.php';
?>

<div class='box_top'>Pet Track</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        $db->query("SELECT * FROM petmarket WHERE userid = ?");
        $db->execute(array($user_class->id));
        $pm_exist = $db->num_rows();
        if ($pm_exist >= 1)
            diefun("You can't put your pet on the tracks if your marketing it.");
        $db->query("SELECT * FROM pets WHERE userid = ?");
        $db->execute(array($user_class->id));
        $pet_exist = $db->num_rows();
        if ($pet_exist == 0)
            diefun("You don't have a pet.<br /><br /><a href='petshop.php'>Buy a Pet</a>");
        if (isset($_POST['takebet'])) {
            security($_POST['bet_id']);
            $db->query("SELECT * FROM pettracks WHERE id = ?");
            $db->execute(array($_POST['bet_id']));
            $worked = $db->fetch_row(true);
            if ($worked['userid'] == $user_class->id)
                diefun("You can't take your own bet.<br /><br /><a href='pettrack.php'>Go Back</a>");
            $amount = $worked['cashbet'];
            $orig = $worked['cashbet'];
            $user_points = new User($worked['userid']);
            if ($amount > $user_class->money)
                echo Message("You don't have enough money to match their bet.");
            if ($amount <= $user_class->money) {
                $user_class->money -= $amount;
                $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
                $db->execute(array($user_class->money, $user_class->id));
                $db->query("SELECT * FROM pets WHERE userid = ?");
                $db->execute(array($user_class->id));
                $user_pt = $db->fetch_row(true);
                if ($worked['petspeed'] > $user_pt['spe']) {
                    echo Message("You have lost.");
                    $amount = ($amount * 0.95) + $orig;
                    $user_points->money += $amount;
                    $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
                    $db->execute(array($user_points->money, $user_points->id));
                    Send_Event($user_points->id, "Congratulations! You won the " . prettynum($worked['cashbet'], 1) . " bid you placed on you pet.");
                } else {
                    echo Message("You have won!");
                    $amount = ($amount * 0.95) + $orig;
                    $user_class->money += $amount;
                    $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
                    $db->execute(array($user_class->money, $user_class->id));
                    Send_Event($user_points->id, "You lost the " . prettynum($worked['cashbet'], 1) . " bid you placed on your pet.");
                }
                $db->query("DELETE FROM pettracks WHERE id = ?");
                $db->execute(array($worked['id']));
            }
        }
        if (isset($_POST['makebet'])) {
            $_POST['amount'] = abs((int) $_POST['amount']);
            security($_POST['amount']);
            if ($_POST['amount'] > $user_class->money)
                echo Message("You don't have that much money.");
            elseif ($_POST['amount'] < 1000)
                echo Message("The minimum bid you can place is $1,000.");
            elseif ($_POST['amount'] > 1000000)
                echo Message("The maximum bid you can place is $1,000,000.");
            else {
                echo Message("You have added a bet at " . prettynum($_POST['amount'], 1) . ".");
                $db->query("SELECT * FROM pets WHERE userid = ?");
                $db->execute(array($user_class->id));
                $user_pname = $db->fetch_row(true);
                $db->query("SELECT * FROM petshop WHERE id = ?");
                $db->execute(array($user_pname['petid']));
                $workedpett = $db->fetch_row(true);
                $petname = $workedpett['name'];
                $db->query("INSERT INTO pettracks (userid, petid, cashbet, petspeed) VALUES (?, ?, ?, ?)");
                $db->execute(array($user_class->id, $petname, $_POST['amount'], $user_pname['spe']));
                $user_class->money -= $_POST['amount'];
                $db->query("UPDATE grpgusers SET money = ? WHERE id = ?");
                $db->execute(array($user_class->money, $user_class->id));
            }
        }
        genHead('Pet Tracks');
        print "
        Welcome to the pet tracks. Here you can set up a bet with your pet on the tracks. The owner of the winning pet will gain 95% of the cash!<br />
        <br />
        <form method='post'>
                Amount of money to bid. $
                <input type='text' name='amount' size='10' maxlength='20' value='$user_class->money'> 
                (Min Bet: $1,000  Max Bet: $1,000,000)<br>
                <br>";
        $db->query("SELECT * FROM pets WHERE userid = ?");
        $db->execute(array($user_class->id));
        $user_pname = $db->fetch_row(true);
        print "
                    <input type='submit' name='makebet' value='Make Bet'>
            </form>
    </td>
</tr>
<tr><td class='contentspacer'></td></tr>";
        genHead('Current Bets');
        print "
        <table id='newtables' style='width:100%;'>
                <tr>
                        <th>Better</th>
                        <th>Amount</th>
                        <th>Pet Type</th>
                        <th>Pet Name</th>
                        <th>Bet</th>
                </tr>";
        $db->query("SELECT * FROM pettracks ORDER BY cashbet DESC");
        $db->execute();
        $lines = $db->fetch_row();
        foreach ($lines as $line) {
            $user_pname = new Pet($line['userid']);
            echo "
                    <form method='post'>
                            <tr>
                                    <td>" . formatName($line['userid']) . "</td>
                                    <td>" . prettynum($line['cashbet'], 1) . "</td>
                                    <td>" . $line['petid'] . "</td>
                                    <td>" . $user_pname->formatName() . "</td>
                                    <td>
                                            <input type='hidden' name='bet_id' value='" . $line['id'] . "'>
                                            <input type='submit' name='takebet' value='Take Bet'>
                                    </td>
                            </tr>
                    </form>";
        }
        print "</td></tr>";
        include 'footer.php';
        ?>