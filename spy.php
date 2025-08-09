<?php
include 'header.php';
?>
<div class='box_top'>Mug</div>
<div class='box_middle'>
    <div class='pad'>

        <td class="contentcontent">
            <table width="100%">
                <?php
                if (isset($_GET['id']) && $_GET['id'] != "") {
                    $spy_class = new User($_GET['id']);
                    $cost = $spy_class->level * 1000;
                    if (isset($_GET['confirm']) && $_GET['confirm'] != "yes") {
                        echo "Are you sure that you want to hire a spy to spy on " . $spy_class->formattedname . " for $" . prettynum($cost) . "?<br><br><a href='spy.php?id=" . $spy_class->id . "&confirm=yes'>Yes</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='profiles.php?id=" . $spy_class->id . "'>No</a>";
                    } else {
                        if ($cost > $user_class->money) {
                            echo "You don't have enough money to spy on this person.<br /><br /><a href='index.php'>Home</a>";
                        } else {
                            $chance = 50;
                            $user_boosts = get_skill_boosts($user_class->skills);
                            if (isset($user_boosts['spy_chance']) && $user_boosts['spy_chance'] > 0) {
                                $chance = $chance * $user_boosts['spy_chance'];
                            }

                            $points = (rand(1, 100) <= $chance) ? $spy_class->points : "Your spy could not find their points out.";
                            $bank = (rand(1, 100) <= $chance) ? $spy_class->bank : "Your spy could not find their bank out.";
                            $strength = (rand(1, 100) <= $chance) ? $spy_class->strength : "Your spy could not find their strength out.";
                            $defense = (rand(1, 100) <= $chance) ? $spy_class->defense : "Your spy could not find their defense out.";
                            $speed = (rand(1, 100) <= $chance) ? $spy_class->speed : "Your spy could not find their speed out.";
                            $agility = (rand(1, 100) <= $chance) ? $spy_class->agility : "Your spy could not find their agility out.";
                            echo "<b>Your spy found out the following about " . $spy_class->formattedname . ":</b><br /><br /><b>Strength:</b>&nbsp;" . prettynum($strength) . "<br /><b>Defense:</b>&nbsp;" . prettynum($defense) . "<br /><b>Speed:</b>&nbsp;" . prettynum($speed) . "<br /><b>Agility:</b>&nbsp;" . prettynum($agility) . "<br /><b>Bank:</b>&nbsp;" . prettynum($bank, 1) . "<br /><b>Points:</b>&nbsp;" . prettynum($points) . "<br /><br /><a href='spylog.php'>View Spylog</a>";
                            $total = $user_class->money - $cost;
                            bloodbath('spies', $user_class->id);

                            $db->query("UPDATE `grpgusers` SET `money` = ? WHERE `id` = ?");
                            $db->execute([$total, $user_class->id]);

                            if (!is_numeric($defense)) {
                                $defense = "-1";
                            }
                            if (!is_numeric($speed)) {
                                $speed = "-1";
                            }
                            if (!is_numeric($bank)) {
                                $bank = "-1";
                            }
                            if (!is_numeric($strength)) {
                                $strength = "-1";
                            }
                            if (!is_numeric($agility)) {
                                $agility = "-1";
                            }
                            if (!is_numeric($points)) {
                                $points = "-1";
                            }

                            $db->query("INSERT INTO `spylog` (`uid`, `spyid`, `strength`, `defense`, `speed`, `agility`, `bank`, `points`, `age`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $db->execute([$user_class->id, $spy_class->id, $strength, $defense, $speed, $agility, $bank, $points, time()]);
                        }
                    }
                }
                echo '</table>';
                ?>
                <?php
                include 'footer.php';
                ?>