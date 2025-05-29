<?php
include 'header.php';
?>
<div class='box_top'>Bank</div>
<div class='box_middle'>
    <div class='pad'>
        <style>
            .upgrade-package {
                flex: 0 1 calc(50% - 20px);
                /* Keep as is, ensures two items per row */
                padding: 10px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
                margin-bottom: 20px;
                /* Ensure there's space at the bottom */
                border-radius: 10px;
                box-sizing: border-box;
                display: flex;
                flex-direction: column;
                /* Keeps children stacked vertically */
            }

            .div-form-wrapper {
                display: flex;
                justify-content: space-between;
                /* Adjusted for better spacing */
                gap: 15px;
                /* This creates space between the flex items */
            }

            /* Default styles for the bank containers */
            .bank-container {
                width: 47%;
                /* Default width for desktop */
                float: left;
                margin: 0;
            }

            /* Media query for mobile devices */
            @media only screen and (max-width: 767px) {
                .bank-container {
                    width: 100%;
                    /* Full width on mobile */
                    float: none;
                    /* Clear the float */
                    margin: 20px 0;
                    /* Add margin to separate sections on mobile */
                }
            }

            /* General reset for table elements */
            table {
                width: 100%;
                text-align: left;
                margin: auto;
                border-collapse: collapse;
            }

            table td,
            table th {
                border: 1px solid #444;
                padding: 10px;
            }

            /* Style the horizontal rule */
            hr {
                border: 0;
                border-bottom: thin solid #333;
            }

            /* Style the forms */
            .bank-form {
                display: flex;
                justify-content: center;
                /* Center the form content */
                align-items: center;
                margin-top: 20px;
            }

            input[type="text"] {
                padding: 10px;
                margin: 5px;
                border: 1px solid #444;
                color: #FFF;
                border-radius: 5px;
                text-align: center;
                /* Center text inside inputs */
            }

            input[type="submit"] {
                cursor: pointer;
                background-color: #333;
                /* Dark grey background */
                color: #ccc;
                /* Light grey text */
            }


            /* Responsive design */
            @media (max-width: 600px) {
                .bank-form {
                    flex-direction: column;
                }
            }
        </style>
        <?php
        $rel_user = new User($user_class->relplayer);

        if (isset($_POST['sdeposit'])) {
            if ($_POST['sid'] == $rel_user->id) {

                $amount = $_POST['damount'];

                if ($amount > $rel_user->money) {
                    echo "They do not have that much money on hand";
                } else {
                    $amount2 = round($amount - (($amount / 100) * 2));
                    $amount3 = round($amount - (($amount / 100) * 98));
                    $rel_user->bank += $amount2;
                    $rel_user->money -= $amount;
                    $notice = ("Money deposited with a 2% fee of $$amount3 taken.");
                    perform_query("UPDATE grpgusers SET bank = ?, money = ? WHERE id = ?", [$rel_user->bank, $rel_user->money, $rel_user->id]);
                    if ($amount > 0)
                        perform_query("INSERT INTO bank_log VALUES('', ?, ?, 'mdep', ?, unix_timestamp())", [$rel_user->id, $amount, $rel_user->bank]);
                    if ($rel_user->bank > $rel_user->banklog)
                        perform_query("UPDATE grpgusers SET banklog = ? WHERE id = ?", [$rel_user->bank, $rel_user->id]);

                    Send_Event($rel_user->id, $user_class->formattedname . " has deposited $" . number_format($amount) . " into your bank account.");
                    Send_Event($user_class->id, "You have deposited $" . number_format($amount) . " into $rel_user->formattedname's bank account");

                    echo $notice;
                }
            } else {
                echo Message("You do not have access to this persons money!");
            }
            include 'footer.php';
            die();
        }

        if (isset($_GET['id']) && isset($_GET['action'])) {
            if ($_GET['action'] == 'sdeposit') {
                if ($_GET['id'] == $rel_user->id) {

                    echo "
            <div class='floaty' style='width:50%;margin:0 auto;'>
            <h2>" . $rel_user->formattedname . "</h2><br>
            There will be a 2% Deposit Fee
            <div style='clear:both'></div>
            <hr style='border:0;border-bottom: thin solid #333;' />
            <table width='50%'>
                <tr>
                    <td width='20%'><b>Money On Hand:</b></td>
                    <td width='20%'>" . prettynum($rel_user->money, 1) . "</td>
                </tr>
            </table>
            <br />
            <form method='post' action='?'>
                <input type='text' name='damount' value='$rel_user->money' size='10' maxlength='20' />
                <input type='hidden' name='sid' value='" . $rel_user->id . "'>
                <input type='submit' style='width:75px;' name='sdeposit' value='Deposit' />
                <input type='hidden' name='type' value='money' />
            </form>
        </div>";
                } else {
                    echo "You do not have access to this persons money!";
                }
            }
            include 'footer.php';
            die();
        }

        if (isset($_GET['h_deposit']) && $_GET['h_deposit'] === 'cash') {
            $amount = $user_class->money;
            $amount2 = round($amount - (($amount / 100) * 2));
            $amount3 = round($amount - (($amount / 100) * 98));
            $user_class->bank += $amount2;
            $user_class->money -= $amount;
            $notice = ("Money deposited with a 2% fee of $$amount3 taken.");
            perform_query("UPDATE grpgusers SET bank = ?, money = ? WHERE id = ?", [$user_class->bank, $user_class->money, $user_class->id]);
            if ($amount > 0)
                perform_query("INSERT INTO bank_log VALUES('', ?, ?, 'mdep', ?, unix_timestamp(), ?)", [$user_class->id, $amount, $user_class->bank, $user_class->money]);
            if ($user_class->bank > $user_class->banklog)
                perform_query("UPDATE grpgusers SET banklog = ? WHERE id = ?", [$user_class->bank, $user_class->id]);

        }

        if ((isset($_GET['dep']) || isset($_POST['deposit']))) {
            if (isset($_GET['dep']))
                $_POST['type'] = 'money';
            else
                $_POST['damount'] = security($_POST['damount'], 'num');
            $amount = (isset($_GET['dep'])) ? $user_class->money : $_POST['damount'];
            $type = ($_POST['type'] == 'money') ? 'money' : 'points';
            if ($amount > $user_class->$type)
                $notice = ("You do not have that much $type.");
            else {
                if ($type == 'money') {
                    $amount2 = round($amount - (($amount / 100) * 2));
                    $amount3 = round($amount - (($amount / 100) * 98));
                    $user_class->bank += $amount2;
                    $user_class->money -= $amount;
                    $notice = ("Money deposited with a 2% fee of $$amount3 taken.");
                    perform_query("UPDATE grpgusers SET bank = ?, money = ? WHERE id = ?", [$user_class->bank, $user_class->money, $user_class->id]);
                    if ($amount > 0)
                        perform_query("INSERT INTO bank_log VALUES('', ?, ?, 'mdep', ?, unix_timestamp(), ?)", [$user_class->id, $amount, $user_class->bank, $user_class->money]);
                    if ($user_class->bank > $user_class->banklog)
                        perform_query("UPDATE grpgusers SET banklog = ? WHERE id = ?", [$user_class->bank, $user_class->id]);
                } else {
                    $user_class->pbank += $amount;
                    $user_class->points -= $amount;
                    $notice = ("Points deposited.");
                    perform_query("UPDATE grpgusers SET pbank = ?, points = ? WHERE id = ?", [$user_class->pbank, $user_class->points, $user_class->id]);
                    if ($amount > 0)
                        perform_query("INSERT INTO bank_log VALUES('', ?, ?, 'pdep', ?, unix_timestamp(), ?)", [$user_class->id, $amount, $user_class->pbank, $user_class->points]);
                }
            }
        }

        if ((isset($_GET['dep']) || isset($_POST['deposit_shared']))) {
            if (isset($_GET['dep'])) {
                $_POST['type'] = 'money';
            } else {
                $_POST['damount'] = security($_POST['damount'], 'num');
                $amount = (isset($_GET['dep'])) ? $user_class->money : $_POST['damount'];
            }
            $amount = (isset($_GET['dep'])) ? $user_class->money : $_POST['damount'];
            $type = ($_POST['type'] == 'money') ? 'money' : 'points';
            if ($amount > $user_class->$type) {
                $notice = ("You do not have that much $type.");
            } else {
                if ($_POST['type'] == 'money') {
                    $amount2 = round($amount - (($amount / 100) * 2));
                    $amount3 = round($amount - (($amount / 100) * 98));
                    $user_class->shared_bank += $amount2;
                    $user_class->money -= $amount;
                    $notice = ("Money deposited with a 2% fee of $$amount3 taken.");
                    perform_query("UPDATE grpgusers SET shared_bank = ?, money = ? WHERE id = ?", [$user_class->shared_bank, $user_class->money, $user_class->id]);
                    perform_query("UPDATE grpgusers SET shared_bank = ? WHERE id = ?", [$user_class->shared_bank, $user_class->relplayer]);
                    perform_query("UPDATE grpgusers SET sharedcontribution = sharedcontribution + ? WHERE id = ?", [$amount, $user_class->id]);

                    if ($amount > 0) {
                        Send_Event($user_class->relplayer, "" . $user_class->formattedname . " Has Deposited $amount Leaving you with a total of $" . $user_class->shared_bank . " in your shared account!");
                    }
                }
            }
        }

        if (isset($_POST['withdraw'])) {
            $amount = security($_POST['wamount'], 'num');
            $type = ($_POST['type'] == 'money') ? array(
                'money',
                'bank'
            ) : array(
                'points',
                'pbank'
            );
            if ($amount > $user_class->$type[1])
                $notice = ("You do not have that much {$type[0]} in the bank.");
            else {
                $notice = (ucfirst($type[0]) . " withdrawn.");
                $user_class->$type[1] -= $amount;
                $user_class->$type[0] += $amount;
                perform_query("UPDATE grpgusers SET {$type[1]} = ?, {$type[0]} = ? WHERE id = ?", [$user_class->{$type[1]}, $user_class->{$type[0]}, $user_class->id]);
                if ($amount > 0) {
                    $which = ($_POST['type'] == 'money') ? "mwith" : "pwith";
                    $whichhand = ($which == "mwith") ? $user_class->money : $user_class->points;
                    perform_query("INSERT INTO bank_log VALUES('', ?, ?, '$which', ?, unix_timestamp(), ?)", [$user_class->id, $amount, $user_class->{$type[1]}, $whichhand]);
                }
            }
        }


        if (isset($_POST['withdraw_shared'])) {
            $amount = security($_POST['wamount'], 'num');
            $type = ($_POST['type'] == 'money') ? array(
                'money',
                'shared_bank'
            ) : array(
                'points',
                'pbank'
            );
            if ($amount > $user_class->$type[1])
                $notice = ("You do not have that much {$type[0]} in the bank.");
            else {
                $notice = (ucfirst($type[0]) . " withdrawn.");
                $user_class->$type[1] -= $amount;
                $user_class->$type[0] += $amount;
                perform_query("UPDATE grpgusers SET {$type[1]} = ?, {$type[0]} = ? WHERE id = ?", [$user_class->{$type[1]}, $user_class->{$type[0]}, $user_class->id]);
                perform_query("UPDATE grpgusers SET sharedcontribution = sharedcontribution - ?, money = ? WHERE id = ?", [$amount, $user_class->money, $user_class->id]);

                Send_Event($user_class->relplayer, "" . $user_class->formattedname . " Has withdrawn $amount Leaving you with a total of $" . $user_class->shared_bank . "!");

                perform_query("UPDATE grpgusers SET {$type[1]} = ? WHERE id = ?", [$user_class->{$type[1]}, $user_class->relplayer]);

                if ($amount > 0) {
                    $which = ($_POST['type'] == 'money') ? "mwith" : "pwith";
                }
            }
        }
        // Calculate the base interest rate based on remaining membership days
        if ($user_class->rmdays >= 1) {
            $interest = 0.04;  // 4% interest rate if membership days are 1 or more
        } else {
            $interest = 0.02;  // 2% interest rate otherwise
        }

        // Adjust interest rate based on donations
        $addmul = $ptsadd = 0;
        if ($user_class->donations >= 50) {
            $addmul = 0.02;
            $ptsadd = 75;
        }
        if ($user_class->donations >= 100) {
            $addmul = 0.03;
            $ptsadd = 120;
        }
        if ($user_class->donations >= 200) {
            $addmul = 0.05;
            $ptsadd = 150;
        }

        // Increase the interest rate by the adjustments from donations
        $interest += $addmul;

        // Apply bank boost if it's set and greater than zero
        
        // Calculate the effective interest amount based on the user's bank balance
        if ($user_class->bank >= 15000000) {
            $interest = ceil(15000000 * $interest);  // Interest capped at a bank amount of 30 million
            if ($user_class->bankboost > 0) {
                $interest += ($interest * ($user_class->bankboost / 10));  // Adjusting the interest rate by bankboost
            }

        } else {
            $interest = ceil($user_class->bank * $interest);  // Interest based on the actual bank balance
            if ($user_class->bankboost > 0) {
                $interest += ($interest * ($user_class->bankboost / 10));  // Adjusting the interest rate by bankboost
            }

        }
        $bi = mysql_fetch_array(mysql_query("SELECT * FROM banksettings WHERE userid = $user_class->id"));
        if (empty($bi)) {
            $bi['limit'] = 25;
            $bi['format'] = 'us';
            $bi['show'] = 'all';
        }
        echo Message($notice) . " <br /><br />


<div class='contenthead floaty'>
    <br><br>
    ";
        echo Message("You will be charged a 2% Deposit Fee for Cash");
        echo "
</div>
<div class='container'>
    <hr />
    <table>
        <tr>
            <th><h4>Bank:</h4></th>
            <td><h4><font color=green>$$user_class->bank</font></h4></td>
            <th><h4>Points Bank:</h4></th>
            <td><h4><font color=white>$user_class->pbank</font></h4></td>
        </tr>
        <tr>
            <th><h4>Daily Interest:</h4></th>
            <td><h4><font color=green>+$$interest</font></h4></td>
            <th></th>
            <td></td>
        </tr>
        <tr>
            <th><h4>Interest Rate:</h4></th>
            <td><h4><font color=green>$rate</font></h4></td>
            <th></th>
            <td></td>
        </tr>
    </table>
    <br>
   <div class='div-form-wrapper' style='display: flex; justify-content: space-around; align-items: flex-start;'>
    <!-- Cash Transactions Section -->
    <div class='upgrade-package' style='flex: 1;'>
        <h4>Cash Transactions</h4>
        <form method='post' action='?'>
            <input type='text' name='damount' value='$user_class->money' size='5' maxlength='20' />
            <input type='submit' name='deposit' value='Deposit Cash' />
            <input type='hidden' name='type' value='money' /> </form>
        <form method='post' action='?'><input type='text' name='wamount' value='0' size='10' maxlength='20' />
        <input type='submit' style='width:75px;' name='withdraw' value='Withdraw' />
        <input type='hidden' name='type' value='money' />
        </form>
    </div>

    <!-- Points Transactions Section -->
    <div class='upgrade-package' style='flex: 1;'>
        <h4>Points Transactions</h4>
        <form method='post' action='?'>
            <input type='text' name='damount' value='$user_class->points' size='5' maxlength='20' />
            <input type='submit' name='deposit' value='Deposit Points' />
            <input type='hidden' name='type' value='points' /></form>
        <form method='post' action='?'>
            <input type='text' name='wamount' value='$user_class->pbank' size='5' maxlength='20' />
            <input type='submit' name='withdraw' value='Withdraw Points' />
            <input type='hidden' name='type' value='points' />
        </form>
    </div>
</div>

</div>";

        echo "<div style='clear:both;'></div>
<br />
<br />
Show <input type='text' value='" . (isset($bi['limit']) && $bi['limit'] !== '' ? $bi['limit'] : '5') . "' id='limit' size='3' maxlength='3' onkeyup='updateBankLog();' /> Transactions |
Date Format
    <select id='format' onchange='updateBankLog();'>
        <option value='us'" . ($bi['format'] == 'us' ? " selected" : "") . ">US Format</option>
        <option value='uk'" . ($bi['format'] == 'uk' ? " selected" : "") . ">Non-US Format</option>
    </select> |
Show
    <select id='show' onchange='updateBankLog();'>
        <option value='all'" . ($bi['show'] == 'all' ? " selected" : "") . ">All Transactions</option>
        <option value='money'" . ($bi['show'] == 'money' ? " selected" : "") . ">Show only Money Transactions</option>
        <option value='points'" . ($bi['show'] == 'points' ? " selected" : "") . ">Show only Points Transactions</option>
        <option value='withs'" . ($bi['show'] == 'withs' ? " selected" : "") . ">Show only Withdraws</option>
        <option value='deps'" . ($bi['show'] == 'deps' ? " selected" : "") . ">Show only Deposits</option>
    </select>

<br />
<br />
<div id='banklog'>
    " . banklog($bi['limit'], $bi['show'], $bi['format']) . "
</div>";

        print <<<TEXT
<script>
    function updateBankLog(){
        // Retrieve input values
        let limit = $("#limit").val();
        let format = $("#format").val();
        let show = $("#show").val();

        // Check if limit is empty or not a number, default to '0'
        limit = limit === '' || isNaN(limit) ? '0' : limit;

        // Perform the post request with the potentially modified limit value
        $.post("ajax_banklog.php", {'limit': limit, 'format': format, 'show': show}, function (callback){
            $("#banklog").html(callback);
        });
    }
</script>
<br>
TEXT;
        include 'footer.php';
