<?php
header('Location: backalley_new.php');
include 'header.php';
?>
<div class='box_top'>BackAlley</div>
<div class='box_middle'>
    <div class='pad'>
        <?php
        // When the CheckOut POST request is made
        if (isset($_POST['CheckOut'])) {
            if ($user_class->hospital > 0) {
                echo Message("You cannot go in the back alley if you are in the hospital.");
                echo "<a href='backalley.php'>Click to return to the back alley</a>";
                include 'footer.php';
                die();
            } else {
                // Perform the intended action here...
        
                // Mark the action as done using a session variable
                $_SESSION['action_done'] = true;

                // Redirect to avoid form resubmission, using 'use=yes' as per your requirement
                header('Location: backalley.php?use=yes');
                exit();
            }
        }

        // Check on page load if the action was previously done
        if (isset($_GET['use']) && $_GET['use'] == 'yes') {
            if (isset($_SESSION['action_done'])) {
                // Inform the user that refreshing to perform the action again is not allowed
                echo Message('You cannot submit this form on refresh.');

                // Clear the session flag to allow future actions
                unset($_SESSION['action_done']);
            }
        }

        if ($_GET['buy'] == "backalleyhospitalyes") {
            $cost = $user_class->level * 300;
            if ($user_class->bank > $cost) {
                if ($user_class->hospital) {
                    $newcredit = $user_class->bank - $cost;
                    $puremaxhp = $user_class->puremaxhp;

                    $newhosp = 0;
                    $time = time();
                    perform_query("UPDATE `grpgusers` SET `bank`=?, `hp`=?, `hospital`=? WHERE `id`=?", [$newcredit, $puremaxhp, $newhosp, $_SESSION['id']]);
                    echo Message("You spent $$cost and brought yourself out of the hospital.");
                    // Add a link to return to the back alley
                    echo "<a href='backalley.php'>Click to return to the back alley</a>";
                } else {
                    echo Message("You are not in the hospital!");
                }
            } else {
                echo Message("You don't have enough money in the bank. You need $$cost");
            }
        }

        if ($_GET['use'] == 'yes') {
            $energyneeded = floor($user_class->maxenergy / 5);
            if ($user_class->energy < $energyneeded)
                refill('e');
            if ($user_class->energy < $energyneeded) {
                echo Message("You need at least 20% of your energy to explore the back alley!");
                include 'footer.php';
                die();
            }
            if ($user_class->jail > 0) {
                echo Message("You cannot go in the back alley if you are in Jail.");
                include 'footer.php';
                die();
            }
            if ($user_class->hospital > 0) {
                echo Message("You cannot go in the back alley if you are in Hospital.");
                include 'footer.php';
                die();
            }

            $randname = mt_rand(1, 6);
            switch ($randname) {
                case 1:
                    $attuser = "Private Niev";
                    break;
                case 2:
                    $attuser = "Private First Class Xali";
                    break;
                case 3:
                    $attuser = "Sergeant Beck";
                    break;
                case 4:
                    $attuser = "Sergeant First Class Walter";
                    break;
                case 5:
                    $attuser = "Captain Jericho";
                    break;
                default:
                    $attuser = "Colonel Pete";
                    break;
            }
            $randscenario = mt_rand(1, 4);
            switch ($randscenario) {
                case 1:
                    $itext = "You slowly walk down the alley and reach a dead end. You turn around to walk back and"
                        . $attuser . " is blocking your way, ready to fight!";
                    $stext = "You beat them up whilst they pleaded for mercy!";
                    $ftext = "They really kicked your butt, spiting in your face as they walk off in triumph.";
                    break;
                case 2:
                    $itext = "You walk confidently down the alley and " . $attuser . " hits you from behind. What a coward!
				You get up ready to kick their butt!";
                    $stext = "You punch them into the wall and leave them bleeding on the street.";
                    $ftext = "They knock you back down on the alleyway, and instead of getting back up, you lay there as they
				laugh and walk away.";
                    break;
                case 3:
                    $itext = "You go with a buddy down the alley and " . $attuser . " walks in front of you ready to fight!
				Your buddy runs away, leaving you there to fight them!";
                    $stext = "They run away, chasing your friend down as they have a grudge against them. Well that was rather
				anti-climatic.";
                    $ftext = "They knock you out with one blow. Your buddy was smart to run!";
                    break;
                default:
                    $itext = "You meet up with " . $attuser . " in the alley to buy some contraband, but it turns out that they're
				wearing a wire!";
                    $stext = "You beat them up, tearing the wire apart! You then run away in order to not get caught!";
                    $ftext = "They knock you down, leaving you there for the cops. Guess you were not as strong as you thought!";
                    break;
            }
            $randout = mt_rand(1, 4);
            ?>
            <script type="text/javascript">
                document.addEventListener("DOMContentLoaded", function () {
                    // Check if the current URL contains 'backalley.php?use=yes'
                    if (window.location.search.indexOf('use=yes') > -1) {
                        document.addEventListener("keydown", function (e) {
                            // Prevent F5 or Ctrl+R
                            if (e.keyCode === 116 || (e.ctrlKey && e.keyCode === 82)) {
                                e.preventDefault();
                                alert("Refreshing this page is not allowed.");
                            }
                        });
                    }
                });
            </script>
            <div class="floaty">
                <?php
                echo $itext . "<br />";
                if ($randout == 1) {
                    echo 'You hit <font color=red>' . $attuser . '</font> for 20 damage. <br />';
                    echo '<font color=red>' . $attuser . '</font> hit you for ' . $user_class->hp . ' damage. <br /><br />';
                    echo '<br /><h3><font color=red><b>FAILED!</b></font></h3><br />';
                    echo $ftext;
                    $hosp = 120;
                    perform_query("UPDATE `grpgusers` SET `hwho` = ?, `hhow` = 'backalley', `hwhen` = ?, `hospital` = ? WHERE `id` = ?", [$attuser, date("g:i:s", time()), $hosp, $user_class->id]);
                    echo "</center></td></tr>";
                    include 'footer.php';
                    die();
                } else {
                    if ($randscenario != 3) {
                        $randnum = mt_rand(10, 30);
                        echo '<font color=red>' . $attuser . '</font> hit you for 20 damage. <br />';
                        echo 'You hit <font color=red>' . $attuser . '</font> for ' . $user_class->moddedstrength . ' damage. <br /><br />';
                    }
                    echo '<br /><h3><font color=darkgreen><b>SUCCESS!</b></font></h3><br />';
                    echo $stext . '<br />';
                    $expgain = round(((mt_rand(1, 5) / 100) * $user_class->maxexp)); // experience gained
                    if ($expgain > 5000) {
                        $expgain = 5000;
                    }
                    $expwon *= (.15 * $user_class->prestige) + 1;
                    $expwon = floor($expwon);
                    $randfind = mt_rand(1, 100); // found points, money, or both
                    if ($randfind < 15) {
                        // found points & money
                        $points = mt_rand(5, 15);
                        $randnum13 = mt_rand(5, 25);
                        $randnum14 = $randnum13 * ($user_class->level + 2);
                        if ($randfind2 == 1) {
                            $rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp, $" . prettynum($randnum14) . ", " . $points . " points,";
                        } else {
                            $rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp and $" . prettynum($randnum14);
                        }
                        perform_query("UPDATE `grpgusers` SET `exp` = `exp` + ?, `money` = `money` + ?, `points` = `points` + ? WHERE `id` = ?", [$expgain, $randnum14, $points, $user_class->id]);

                    } else if ($randfind < 80) {
                        // found money only
                        $randnum13 = mt_rand(5, 25);
                        $randnum14 = $randnum13 * ($user_class->level + 2);
                        if ($randnum14 > 10000) {
                            $randnum14 = 10000;
                        }

                        if ($randfind2 == 1) {
                            $rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp,  $" . prettynum($randnum14) . ",";
                        } else {
                            $rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp and $" . prettynum($randnum14);
                        }
                        perform_query("UPDATE `grpgusers` SET `exp` = `exp` + ?, `money` = `money` + ? WHERE `id` = ?", [$expgain, $randnum14, $user_class->id]);
                    } else {
                        // found points only
                        $points = mt_rand(5, 15);
                        if ($randfind2 == 1) {
                            $rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp, " . prettynum($points) . " points,";
                        } else {
                            $rtext = " <font color='darkgreen'>You made off with " . prettynum($expgain) . " exp and " . prettynum($points) . " points";
                        }
                        perform_query("UPDATE `grpgusers` SET `exp` = `exp` + ?, `points` = `points` + ? WHERE `id` = ?", [$expgain, $points, $user_class->id]);
                    }
                    $rtext .= "!</font>";
                    perform_query("UPDATE `grpgusers` SET `energy` = `energy` - ?, `backalleywins` = `backalleywins` + 1 WHERE `id` = ?", [$energyneeded, $user_class->id]);
                    $toadd = array('baotd' => 1);
                    ofthes($user_class->id, $toadd);
                    echo $rtext;
                }
                echo "<br /><br /><form method='POST' action='backalley.php?use=yes'>
    <input type='submit' name='CheckOut' value='Check the alley again' style='background: #000000;color: #FFFFFF;'/>
</form></div>";
        } else {
            ?>

                <?php
                // Check if the user is currently in the hospital
                if ($user_class->hospital > 0) {
                    $cost = $user_class->level * 300;
                    echo "- <a href='backalley.php?buy=backalleyhospitalyes'><font color=red><b>Buy Out for $$cost</b></font></a><br><br>";

                    $meds = '11, 12, 13, 14';
                    $db->query("SELECT * FROM inventory LEFT JOIN items ON inventory.itemid = items.id WHERE itemid IN ($meds) AND userid = ?");
                    $db->execute([$user_class->id]);
                    $meds = $db->fetch_row();
                    echo "<div style='text-align:center; display: flex; flex-direction: row; justify-content: space-evenly; align-items: center; margin-bottom:10px;'>";
                    foreach ($meds as $med) {
                        echo "<div>";
                        echo image_popup($med['image'], $med['id']);
                        echo '<br />';
                        echo item_popup($med['itemname'], $med['id']) . ' [x' . $med['quantity'] . ']<br>';
                        echo '<a class="button-sm" href="inventory.php?use=' . $med['id'] . '">Use</a>';
                        echo "</div>";
                    }
                    echo "</div>";
                }
                ?>

                <div class="floaty">

                    &bull; <font color="red">Welcome to the BackAlley!<br />
                        &bull; <font color="#fff">You will battle against different opponents.<br />
                            &bull; <font color="red">But will you take the risk when its 20% energy per attack<br />
                                &bull; <font color="#fff">If you fail you will find yourself in the hospital</font>
                                <br /><br />
                                <form method="POST" action="backalley.php?use=yes">
                                    <input type="submit" name="CheckOut" value="Check it out"
                                        style="background: #000000;color: #FFFFFF;" />
                                </form>
                </div>
                </center>
            </div>
        </div>



        <?php
        }
        include 'footer.php';
        ?>