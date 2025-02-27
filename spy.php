<?php
include 'header.php';
?>
<div class='box_top'>Mug</div>
						<div class='box_middle'>
							<div class='pad'>

    <td class="contentcontent">	<table width="100%">
            <?php
            if ($_GET['id'] != "") {
                $spy_class = new User($_GET['id']);
                $cost = $spy_class->level * 1000;
                if ($_GET['confirm'] != "yes") {
                    echo "Are you sure that you want to hire a spy to spy on " . $spy_class->formattedname . " for $" . prettynum($cost) . "?<br><br><a href='spy.php?id=" . $spy_class->id . "&confirm=yes'>Yes</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href='profiles.php?id=" . $spy_class->id . "'>No</a>";
                } else {
                    if ($cost > $user_class->money) {
                        echo "You don't have enough money to spy on this person.<br /><br /><a href='index.php'>Home</a>";
                    } else {
                        $points = (rand(0, 1) == 1) ? $spy_class->points : "Your spy could not find their points out.";
                        $bank = (rand(0, 1) == 1) ? $spy_class->bank : "Your spy could not find their bank out.";
                        $strength = (rand(0, 1) == 1) ? $spy_class->strength : "Your spy could not find their strength out.";
                        $defense = (rand(0, 1) == 1) ? $spy_class->defense : "Your spy could not find their defense out.";
                        $speed = (rand(0, 1) == 1) ? $spy_class->speed : "Your spy could not find their speed out.";
                        $agility = (rand(0, 1) == 1) ? $spy_class->agility : "Your spy could not find their agility out.";
                        echo "<b>Your spy found out the following about " . $spy_class->formattedname . ":</b><br /><br /><b>Strength:</b>&nbsp;" . prettynum($strength) . "<br /><b>Defense:</b>&nbsp;" . prettynum($defense) . "<br /><b>Speed:</b>&nbsp;" . prettynum($speed) . "<br /><b>Agility:</b>&nbsp;" . prettynum($agility) . "<br /><b>Bank:</b>&nbsp;" . prettynum($bank, 1) . "<br /><b>Points:</b>&nbsp;" . prettynum($points) . "<br /><br /><a href='spylog.php'>View Spylog</a>";
                        $total = $user_class->money - $cost;
                        $todaysspys = $user_class->todaysspys + 1;
                        bloodbath('spies', $user_class->id);
                        $result = mysql_query("UPDATE `grpgusers` SET `money` = '" . $total . "' WHERE `id` = '" . $user_class->id . "'");
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
                        if (!is_numeric($points)) {
                            $points = "-1";
                        }
                        $result = mysql_query("INSERT INTO `spylog` (`id`, `spyid`, `strength`, `defense`, `speed`, `agiltiy`, `bank`, `points`, `age`) VALUES ('" . $user_class->id . "', '" . $spy_class->id . "', '" . $strength . "', '" . $defense . "', '" . $speed . "', '" . $agility . "', '" . $bank . "', '" . $points . "', '" . time() . "')");
                    }
                }
            }
            echo '</table>';
            ?>
            <?php
            include 'footer.php';
            ?>
