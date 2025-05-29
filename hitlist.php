<?php
include("header.php");
?>
<div class='box_top'>Hitlist</div>
<div class='box_middle'>
    <div class='pad'>
        <?php

        if (isset($_POST['newhit'])) {

            $ref = $_SERVER['HTTP_REFERER'];
            $id = $_GET['target'];


            security($_POST['bounty'], 'num');
            $bounty = $_POST['bounty'];
            $target = $_POST['target'];
            $reason = strip_tags($_POST['reason']);
            $reason = addslashes($reason);
            $reasoncheck = str_replace(" ", "", $_POST['reason']);
            $target_person = new User($_POST['target']);
            $check1 = mysql_query("SELECT * FROM `grpgusers` WHERE `id` = '" . $target . "'");
            $check = mysql_num_rows($check1);
            $error = "";
            $error = ($check == 0) ? "That target doesn't exist." : $error;
            $error = ($bounty < 50000) ? "Your bounty has to be at least $50,000." : $error;
            $error = ($bounty < $target_person->level * 1000) ? "Your bounty has to be at least $1,000 times the players level." : $error;
            $error = (strlen($reasoncheck) < 1) ? "You have to have a reason to make the hit." : $error;
            $error = ($bounty > $user_class->bank) ? "You don't have enough money in the bank to have a bounty that high." : $error;
            $error = (strlen($reason) > 100) ? "Your reason can only be up to 100 characters long." : $error;
            $error = ($target == $user_class->id) ? "You can't put a hit on yourself." : $error;
            if ($error != "")
                echo Message($error);
            else {
                $newmoney = $user_class->bank - $bounty;
                perform_query("UPDATE grpgusers SET bank = ? WHERE id = ?", [$newmoney, $user_class->id]);
                perform_query("INSERT INTO bank_log VALUES('', ?, ?, 'mwith', ?, unix_timestamp())", [$user_class->id, $bounty, $user_class->bank]);
                perform_query("INSERT INTO hitlist (target, reason, bounty, `from`) VALUES (?, ?, ?, ?)", [$target, $reason, $bounty, $user_class->id]);
                echo Message("You have successfully made a hit.");
            }
        }
        if (isset($_GET['remove'])) {
            $result = mysql_query("SELECT * FROM hitlist WHERE id = {$_GET['remove']}");
            $worked = mysql_fetch_array($result);
            $error = (empty($worked['id'])) ? "The hit you were looking for couldn't be found, sorry." : $error;
            $error = ($worked['from'] != $user_class->id) ? "You don't own that hit, so you can't remove it." : $error;
            if (!empty($error))
                echo Message($error);
            else {
                $newmoney = $user_class->bank + $worked['bounty'];
                perform_query("UPDATE grpgusers SET bank = ? WHERE id = ?", [$newmoney, $user_class->id]);
                perform_query("INSERT INTO bank_log VALUES('', ?, ?, 'mdep', ?, unix_timestamp())", [$user_class->id, $worked['bounty'], $user_class->bank]);
                perform_query("DELETE FROM hitlist WHERE id = ?", [$_GET['remove']]);
                echo Message("You have successfully removed that hit.");
            }
        }
        if (isset($_GET['hit'])) {
            $result = mysql_query("SELECT * FROM hitlist WHERE id = '" . $_GET['hit'] . "'");
            $worked = mysql_fetch_array($result);
            $attack_person = new User($worked['target']);
            if ($attack_person->id >= 9999999999 and $attack_person->id <= 1) {
                $attack_person->level = $user_class->level + $attack_person->id - 339;
                $attack_person->hp = $attack_person->purehp = $attack_person->maxhp = $attack_person->puremaxhp = $attack_person->level * 50;
                $attack_person->hppercent = 100;
                $attack_person->formattedhp = $attack_person->hp . " / " . $attack_person->maxhp . " [100%]";
                $attack_person->city = $user_class->city;
                $attack_person->jail = 0;
                $attack_person->moddedstrength = rand(1000 * (($attack_person->id - 333) / 10), 2500 * (($attack_person->id - 333) / 10));
                $attack_person->moddeddefense = rand(1000 * (($attack_person->id - 333) / 10), 2500 * (($attack_person->id - 333) / 10));
                $attack_person->moddedspeed = rand(1000 * (($attack_person->id - 333) / 10), 2500 * (($attack_person->id - 333) / 10));
                $user_class->moddedstrength = rand(1000, 5000);
                $user_class->moddeddefense = rand(1000, 5000);
                $user_class->moddedspeed = rand(1000, 5000);
                $attack_person->lastactive = time();
            }
            $error = ($worked['target'] == "") ? "That hit doesn't exist." : $error;
            $error = ($worked['from'] == $user_class->id) ? "You can't take your own hit." : $error;
            if ($user_class->energypercent < 25)
                refill('e');
            $error = ($user_class->energypercent < 25) ? "You need to have at least 25% of your energy if you want to hit someone." : $error;
            $error = ($user_class->hppercent < 50) ? "You need to have over 50% HP to hit someone." : $error;
            $error = ($user_class->jail > 0) ? "You can't hit someone if you are in prison." : $error;
            $error = ($user_class->hospital > 0) ? "You can't hit someone if you are in hospital." : $error;
            $error = ($attack_person->city != $user_class->city && $user_class->id != 146) ? "You must be in the same city as the person you're attacking!" : $error;


            $error = ($_GET['hit'] == "") ? "You didn't choose someone to hit." : $error;
            $error = ($user_class->gang == $attack_person->gang && $user_class->gang > 0) ? "You can't hit someone in your gang." : $error;
            $error = ($worked['target'] == $user_class->id) ? "You can't hit yourself." : $error;
            $error = ($attack_person->protectionact > time()) ? "This player is under the MW protection act for another " . howlongleft($attack_person->protectionact) . "." : $error;
            //$error = ($attack_person->hppercent < 50) ? "This player has under 50% HP therefore you can't hit him/her yet." : $error;
            $error = ($attack_person->username == "") ? "That person doesn't exist." : $error;
            $error = ($attack_person->hospital > 0) ? "You can't hit someone that is in hospital." : $error;
            $error = ($attack_person->jail > 0) ? "You can't hit someone that is in prison." : $error;
            $error = ($attack_person->admin == 1) ? "Im sorry, You cannot attack the owner" : $error;
            $error = ($attack_person->aprotection > time()) ? "Im sorry, You cannot attack a person under protection" : $error;


            $error = (time() - $attack_person->lastactive >= 900) ? "The target must be online." : $error;
            if (isset($error)) {
                echo Message($error);
            } else {
                $yourhp = $user_class->hp;
                $theirhp = $attack_person->hp;
                genHead("Hitlist");
                echo "You are hitting $attack_person->formattedname.<br /><br />
                    You are using your $user_class->weaponname.<br />
                    $attack_person->formattedname is using their $attack_person->weaponname.<br /><br />
                ";
                $userspeed = $user_class->moddedspeed;
                $attackspeed = $attack_person->moddedspeed;
                $wait = ($userspeed > $attackspeed) ? 1 : 0;
                $number = 0;
                if ($attack_person->invincible > 0 && time() < $attack_person->invincible)
                    echo "<font color='red'><b>This player is invincible thanks to a rare present, he has automatically won the battle.</b></font>";
                if ($user_class->invincible > 0 && time() > $user_class->invincible)
                    echo "<font color='red'><b>You are invincible thanks to a rare present. You have automatically won the battle.</b></font>";
                if ($user_class->invincible == 0) {
                    if ($attack_person->invincible == 0) {
                        while ($yourhp > 0 && $theirhp > 0) {
                            if ($attack_person->eqweapon == 71) {
                                $chance = rand(1, 20);
                                if ($chance == 10)
                                    $double_damage = 2;
                                else
                                    $double_damage = 1;
                            } else
                                $double_damage = 1;
                            $damage = ($attack_person->moddedstrength * $double_damage) - $user_class->moddeddefense;
                            $damage = ($damage < 1) ? 1 : $damage;
                            if ($wait == 0) {
                                $yourhp = $yourhp - $damage;
                                $number++;
                                echo $number . ":&nbsp;" . $attack_person->formattedname . " hit you for " . prettynum($damage) . " damage using their " . $attack_person->weaponname . ". <br>";
                            } else
                                $wait = 0;
                            if ($yourhp > 0) {
                                if ($user_class->eqweapon == 71) {
                                    $chance2 = rand(1, 20);
                                    if ($chance2 == 10)
                                        $double_damage2 = 2;
                                    else
                                        $double_damage2 = 1;
                                } else
                                    $double_damage2 = 1;
                                $damage = ($user_class->moddedstrength * $double_damage2) - $attack_person->moddeddefense;
                                $damage = ($damage < 1) ? 1 : $damage;
                                $theirhp = $theirhp - $damage;
                                $number++;
                                echo $number . ":&nbsp;" . "You hit " . $attack_person->formattedname . " for " . prettynum($damage) . " damage using your " . $user_class->weaponname . ". <br>";
                            }
                        }
                    } else
                        $yourhp = 0;
                } else
                    $theirhp = 0;
                if ($theirhp <= 0) {
                    $winner = $user_class->id;
                    perform_query("UPDATE grpgusers SET hwho = ?, hhow = 'wasattacked', hwhen = ?, hospital = 300 WHERE id = ?", [$user_class->id, date(g . ":" . i . ":" . sa, time()), $attack_person->id]);
                    $theirhp = 0;
                    $newmoney = $user_class->bank + $worked['bounty'];
                    mission('k');
                    perform_query("UPDATE grpgusers SET bank = ? WHERE id = ?", [$newmoney, $user_class->id]);
                    perform_query("INSERT INTO bank_log VALUES('', ?, ?, 'mdep', ?, unix_timestamp())", [$user_class->id, $worked['bounty'], $user_class->bank]);
                    Send_Event($attack_person->id, "[-_USERID_-] hit you via the hitlist! They gained $" . prettynum($worked['bounty']) . " for hitting you.", $user_class->id);
                    echo "<br />Your hit on " . $attack_person->formattedname . " was successful. You receive $" . prettynum($worked['bounty']) . " for attacking this person.";
                    Send_Event($worked['from'], "[-_USERID_-] has completed the hit you requested. They received the $" . prettynum($worked['bounty']) . " bounty you set up.", $user_class->id);
                    perform_query("DELETE FROM hitlist WHERE id = ?", [$_GET['hit']]);
                }
                if ($yourhp <= 0) {
                    $winner = $attack_person->id;
                    perform_query("UPDATE grpgusers SET hwho = ?, hhow = 'attacked', hwhen = ?, hospital = 300 WHERE id = ?", [$attack_person->id, date(g . ":" . i . ":" . sa, time()), $user_class->id]);
                    $yourhp = 0;
                    Send_Event($attack_person->id, "[-_USERID_-] tried to hit you via the hitlist but lost! They were sent to hospital for 5 minutes.", $user_class->id);
                    echo "<br />You have lost the battle to " . $attack_person->formattedname . " and have been sent to hospital for 5 minutes.";
                }
                $newenergy = $user_class->energy - floor(($user_class->energy / 100) * 25);
                $theirhp = ($theirhp > $attack_person->purehp) ? $attack_person->purehp : $theirhp;
                $yourhp = ($yourhp > $user_class->purehp) ? $user_class->purehp : $yourhp;

                perform_query("UPDATE grpgusers SET hp = ? WHERE id = ?", [$theirhp, $attack_person->id]);
                perform_query("UPDATE grpgusers SET hp = ?, energy = ? WHERE id = ?", [$yourhp, $newenergy, $user_class->id]);
                echo "</td></tr>";
            }
        }
        genHead("New Hit");
        echo "

            -<i>The hit reason must not contain anything that violates the ToS.</i><br /><br />
            -<i>The hitlist will cost $1,000 times the level of the player. The minimum bounty however is $50,000 regardless of the targets level.</i><br /><br />
            <table id='newtables' style='margin:0 auto;width:40%;'>
                <form method='post' action='hitlist.php'>
                    <tr>
                        <th><b>Target:</b></td>
                        <td style='text-align:left;'><input type='text' id='target' name='target' size='5' /> [ID]</td>
                    </tr>
                    <tr>
                        <th><b>Reason:</b></td>
                        <td style='text-align:left;'><input type='text' name='reason' size='20' maxlength='100' /></td>
                    </tr>
                    <tr>
                        <th><b>Bounty: $</b></td>
                        <td style='text-align:left;'><input type='text' name='bounty' size='8' id='minimumBountyDisplay' /> </td>
                    </tr>
                    <tr>
                        <th colspan='2'><input type='submit' name='newhit' value='Submit Hit' /></td>
                    </tr>
                </form>
            </table>
        </td></tr>
            <i><center>You may only hit the target when he/she is online.</center></i><br /><br />
            <table id='newtables' style='width:100%;'>
                <tr>
                    <th colspan='5'>Hitlist</th>
                </tr>
                <tr>
                    <th>Target</th>
                    <th>Why</th>
                    <th>Bounty</th>
                    <th>Attack</th>
                    <th>Online/Offline</th>
                </tr>
        ";
        $result = mysql_query("SELECT * FROM `hitlist`");
        while ($line = mysql_fetch_array($result)) {
            $hitlist_class = new User($line['target']);
            $action = ($user_class->id == $line['from']) ? "<a href='hitlist.php?remove=" . $line['id'] . "'>Remove</a>" : "<a href='hitlist.php?hit=" . $line['id'] . "'><font color=green>Claim Bounty</font></a>";
            if (((time() - $hitlist_class->lastactive) < 900) && $user_class->id == $line['from']) {
                $online = "<font color=green><b>Online</b></font>";
                $action = "<a href='hitlist.php?remove=" . $line['id'] . "'>Remove</a>";
            } else if (((time() - $hitlist_class->lastactive) >= 900) && $user_class->id == $line['from']) {
                $online = "<font color=red><b>Offline</b></font>";
                $action = "<a href='hitlist.php?remove=" . $line['id'] . "'>Remove</a>";
            } else if (((time() - $hitlist_class->lastactive) < 900) && $user_class->id != $line['from']) {
                $online = "<font color=green><b>Online</b></font>";
                $action = "<a href='hitlist.php?hit=" . $line['id'] . "'><font color=green>Claim Bounty</font></a>";
            } else {
                $online = "<font color=red><b>Offline</b></font>";
                $action = "<font color='#FFCC00'><s>User Is Not Online</s></font>";
            }

            // || NOT($hitlist_class->id >= 334 AND $hitlist_class->id <= 353)  REMOVED
        
            echo "
                <tr>
                    <td width='25%'>$hitlist_class->formattedname</td>
                    <td width='48%'>{$line['reason']}</td>
                    <td width='13%'>" . prettynum($line['bounty'], 1) . "</td>
                    <td width='11%'>$action</td>
                    <td width='3%'>$online</td>
            </tr>
            ";
        }
        echo "</table></td></tr>";
        include "footer.php";
        ?>
        <script>
            // Function to update the minimum bounty display
            function updateMinimumBounty() {
                var targetID = $("#target").val(); // Get the entered target ID

                // Make an AJAX request to retrieve minimum bounty based on the entered ID
                $.ajax({
                    url: 'get_minimum_bounty.php', // Create a separate PHP file to handle this AJAX request
                    type: 'GET',
                    data: { targetID: targetID }, // Send the target ID to the server
                    success: function (response) {
                        $('#minimumBountyDisplay').val(response);
                        //$('#minimumBountyDisplay').text(response); // Update the div with the calculated minimum bounty
                    }
                });
            }

            // Listen for changes in the input field (target ID)
            $(document).ready(function () {
                $('#target').on('input', function () {
                    updateMinimumBounty(); // Update the minimum bounty display when the input changes
                });
            });
        </script>