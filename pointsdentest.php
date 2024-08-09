<?php
include 'header.php';
?>

<div class="container mt-5">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h3 class="mb-0">Points Den</h3>
        </div>
        <div class="card-body">
            <?php
            $cost = array();

            $energyBoost = $user_class->energyboost;
            if ($energyBoost < 20) {
                $cost['energy'] = 500;
            } else if ($energyBoost >= 20 && $energyBoost < 40) {
                $cost['energy'] = 1000;
            } else if ($energyBoost >= 40 && $energyBoost < 60) {
                $cost['energy'] = 2000;
            } else if ($energyBoost >= 60 && $energyBoost < 80) {
                $cost['energy'] = 4000;
            } else if ($energyBoost >= 80 && $energyBoost < 100) {
                $cost['energy'] = 8000;
            } else {
                $cost['energy'] = 10000;
            }

            $nerveBoost = $user_class->nerveboost;
            if ($nerveBoost < 20) {
                $cost['nerve'] = 500;
            } else if ($nerveBoost >= 20 && $nerveBoost < 40) {
                $cost['nerve'] = 1000;
            } else if ($nerveBoost >= 40 && $nerveBoost < 60) {
                $cost['nerve'] = 2000;
            } else if ($nerveBoost >= 60 && $nerveBoost < 80) {
                $cost['nerve'] = 4000;
            } else if ($nerveBoost >= 80 && $nerveBoost < 100) {
                $cost['nerve'] = 8000;
            } else {
                $cost['nerve'] = 10000;
            }

            $cost['bankboost'] = 20000;
            $cost['crimeexpboost'] = 20000;

            if (isset($_GET['spend'])) {
                if ($_GET['spend'] == "energy") {
                    if ($user_class->points >= 10) {
                        if ($user_class->energy == $user_class->maxenergy) {
                            echo Message("Your energy is already full up!");
                        } else {
                            $newpoints = $user_class->points - 10;
                            $result = mysql_query("UPDATE `grpgusers` SET `energy` = '" . $user_class->maxenergy . "', `points`='" . $newpoints . "' WHERE `id`='" . $_SESSION['id'] . "'");
                            echo Message("You spent 10 points and refilled your energy.");
                        }
                    } else {
                        echo Message("You don't have enough points to do that.");
                    }
                }
                if ($_GET['spend'] == "nerve") {
                    if ($user_class->points >= 10) {
                        if ($user_class->nerve == $user_class->maxnerve) {
                            echo Message("Your nerve is already full up!");
                        } else {
                            $newpoints = $user_class->points - 10;
                            $newnerve = ($user_class->maxnerve > 100) ? $user_class->nerve + 100 : $user_class->maxnerve;
                            $newnerve = ($newnerve > $user_class->maxnerve) ? $user_class->maxnerve : $newnerve;
                            $result = mysql_query("UPDATE `grpgusers` SET `nerve` = '" . $newnerve . "', `points`='" . $newpoints . "' WHERE `id`='" . $_SESSION['id'] . "'");
                            echo Message("You spent 10 points and refilled your nerve.");
                        }
                    } else {
                        echo Message("You don't have enough points to do that.");
                    }
                }

                if ($_GET['spend'] == "energy1") {
                    if ($user_class->points >= $cost['energy']) {
                        if ($user_class->energyboost >= 250) {
                            echo Message("You have already maxed this boost!");
                        } else {
                            $newpoints = $user_class->points - $cost['energy'];
                            $newenergy = $user_class->energyboost + 1;
                            $user_class->energyboost = $newenergy;

                            $result = mysql_query("UPDATE `grpgusers` SET `energyboost` = '" . $newenergy . "', `points`='" . $newpoints . "' WHERE `id`='" . $_SESSION['id'] . "'");
                            echo Message("You spent {$cost['energy']} points and received +1 to your energy.");
                        }
                    } else {
                        echo Message("You don't have enough points to do that.");
                    }
                }

                if ($_GET['spend'] == "nerve1") {
                    if ($user_class->points >= $cost['nerve']) {
                        if ($user_class->nerveboost >= 250) {
                            echo Message("You have already maxed this boost!");
                        } else {
                            $newpoints = $user_class->points - $cost['nerve'];
                            $newenergy = $user_class->nerveboost + 1;
                            $user_class->nerveboost = $newenergy;

                            $result = mysql_query("UPDATE `grpgusers` SET `nerveboost` = '" . $newenergy . "', `points`='" . $newpoints . "' WHERE `id`='" . $_SESSION['id'] . "'");
                            echo Message("You spent {$cost['nerve']} points and received +1 to your nerve.");
                        }
                    } else {
                        echo Message("You don't have enough points to do that.");
                    }
                }

                if ($_GET['spend'] == "bankinterest") {
                    if ($user_class->points >= $cost['bankboost']) {
                        if ($user_class->bankboost >= 10) {
                            echo Message("You already have the maximum Bank Interest boost available");
                        } else {
                            $newpoints = $user_class->points - $cost['bankboost'];
                            $newbankboost = $user_class->bankboost + 1;
                            $user_class->bankboost = $newbankboost;
                            $user_class->points = $newpoints;

                            $result = mysql_query("UPDATE `grpgusers` SET `bankboost` = '" . $newbankboost . "', `points`='" . $newpoints . "' WHERE `id`='" . $_SESSION['id'] . "'");
                            echo Message("You spent {$cost['bankboost']} points and received +10% Bank Interest Bonus.");
                        }
                    } else {
                        echo Message("You need at least " . $cost['bankboost'] . " points");
                    }
                }

                if ($_GET['spend'] == "crimeexp") {
                    if ($user_class->points >= $cost['crimeexpboost']) {
                        if ($user_class->crimeexpboost >= 10) {
                            echo Message("You already have the maximum Crime EXP boost available");
                        } else {
                            $newpoints = $user_class->points - $cost['crimeexpboost'];
                            $newcrimeexpboost = $user_class->crimeexpboost + 1;
                            $user_class->crimeexpboost = $newcrimeexpboost;
                            $user_class->points = $newpoints;

                            $result = mysql_query("UPDATE `grpgusers` SET `crimeexpboost` = '" . $newcrimeexpboost . "', `points`='" . $newpoints . "' WHERE `id`='" . $_SESSION['id'] . "'");
                            $bonus = ($user_class->crimeexpboost > 1) ? "3.33%" : "20%";
                            echo Message("You spent {$cost['crimeexpboost']} points and received +{$bonus} Crime EXP Bonus.");
                        }
                    } else {
                        echo Message("You need at least " . $cost['crimeexpboost'] . " points");
                    }
                }

                if ($_GET['spend'] == "awake") {
                    if ($user_class->awakepercent >= 100) {
                        echo Message("Your awake is already full up!");
                    } elseif ($user_class->points == 0) {
                        echo Message("You don't have enough points to do that.");
                    } else {
                        $points_to_use = $user_class->points;
                        $points_to_use = floor(($points_to_use > (100 - $user_class->awakepercent)) ? (100 - $user_class->awakepercent) : $points_to_use);
                        $awake_to_digits = floor($user_class->directmaxawake * ($points_to_use / 100));
                        $newawake = floor($user_class->directawake + $awake_to_digits);
                        $newawake = ($newawake > $user_class->directmaxawake) ? $user_class->directmaxawake : $newawake;
                        $newpoints = $user_class->points - $points_to_use;
                        $result = mysql_query("UPDATE `grpgusers` SET `awake` = '" . $newawake . "', `points`='" . $newpoints . "' WHERE `id`='" . $user_class->id . "'");
                        echo Message("You have refilled your awake by " . $points_to_use . "%.");
                    }
                }

                //Admin Section
                if ($_GET['admin'] == "hosp") {
                    if ($user_class->admin == 1) {
                        if ($user_class->hospital == 0) {
                            echo Message("You're not in the hospital.");
                        } else {
                            $result = mysql_query("UPDATE `grpgusers` SET `hospital` = '0' AND `hp` = '" . $user_class->puremaxhp . "' WHERE `id`='" . $_SESSION['id'] . "'");
                            echo Message("You used your corruption powers to get out of hospital.");
                        }
                    }
                }

                if ($_GET['admin'] == "prison") {
                    if ($user_class->admin == 1) {
                        if ($user_class->jail == 0) {
                            echo Message("You're not in prison.");
                        } else {
                            $result = mysql_query("UPDATE `grpgusers` SET `jail` = '0' WHERE `id`='" . $_SESSION['id'] . "'");
                            echo Message("You used your corruption powers to get out of prison.");
                        }
                    }
                }

                if ($_GET['admin'] == "energy") {
                    if ($user_class->admin == 1) {
                        if ($user_class->energy == $user_class->maxenergy) {
                            echo Message("Your energy is already full.");
                        } else {
                            $result = mysql_query("UPDATE `grpgusers` SET `energy` = '$user_class->maxenergy' WHERE `id`='" . $_SESSION['id'] . "'");
                            echo Message("You used your corruption powers to refill your energy.");
                        }
                    }
                }

                if ($_GET['admin'] == "nerve") {
                    if ($user_class->admin == 1) {
                        if ($user_class->nerve == $user_class->maxnerve) {
                            echo Message("Your nerve is already full.");
                        } else {
                            $result = mysql_query("UPDATE `grpgusers` SET `nerve` = '$user_class->maxnerve' WHERE `id`='" . $_SESSION['id'] . "'");
                            echo Message("You used your corruption powers to refill your energy.");
                        }
                    }
                }

                if ($_GET['admin'] == "awake") {
                    if ($user_class->admin == 1) {
                        if ($user_class->awake == $user_class->maxawake) {
                            echo Message("Your awake is already full.");
                        } else {
                            $result = mysql_query("UPDATE `grpgusers` SET `awake` = '$user_class->maxawake' WHERE `id`='" . $_SESSION['id'] . "'");
                            echo Message("You used your corruption powers to refill your awake.");
                        }
                    }
                }

                if ($_GET['admin'] == "money") {
                    if ($user_class->admin == 1) {
                        $newpoints = $user_class->points - 1;
                        $newmoney = $user_class->money + 1000;
                        $result = mysql_query("UPDATE `grpgusers` SET `points` = '$newpoints', `money` = '$newmoney' WHERE `id`='" . $_SESSION['id'] . "'");
                        echo Message("You used your corruption powers to get some quick cash.");
                    }
                }
            }
            ?>

            <table class="table table-striped table-hover mt-4">
                <thead class="thead-dark">
                    <tr>
                        <th>Upgrade</th>
                        <th>Costs</th>
                        <th>Level</th>
                        <th>Buy</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Energy + 1</td>
                        <td><?php echo $cost['energy'] . ' points'; ?></td>
                        <td><?php echo ($user_class->energyboost) . '/250'; ?></td>
                        <td>
                            <?php if ($user_class->energyboost < 250) {
                                echo '<a href="pointsden.php?spend=energy1" class="btn btn-sm btn-success">Buy</a>';
                            } else {
                                echo '<span class="badge bg-secondary">FULL</span>';
                            } ?>
                        </td>
                    </tr>

                    <tr>
                        <td>Nerve + 1</td>
                        <td><?php echo $cost['nerve'] . ' points'; ?></td>
                        <td><?php echo ($user_class->nerveboost) . '/250'; ?></td>
                        <td>
                            <?php if ($user_class->nerveboost < 250) {
                                echo '<a href="pointsden.php?spend=nerve1" class="btn btn-sm btn-success">Buy</a>';
                            } else {
                                echo '<span class="badge bg-secondary">FULL</span>';
                            } ?>
                        </td>
                    </tr>

                    <tr>
                        <td>Bank Daily Income +10%</td>
                        <td><?php echo number_format($cost['bankboost'], 0) . ' points'; ?></td>
                        <td><?php echo ($user_class->bankboost) . '/10'; ?></td>
                        <td>
                            <?php if ($user_class->bankboost < 10) {
                                echo '<a href="pointsden.php?spend=bankinterest" class="btn btn-sm btn-success">Buy</a>';
                            } else {
                                echo '<span class="badge bg-secondary">FULL</span>';
                            } ?>
                        </td>
                    </tr>
                </tbody>
            </table>
    
        </div>
    </div>

    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4>Points + Nerve Upgrades</h4>
                </div>
                <div class="card-body">
                    <p>Total of 100 Upgrades - Maximum Boost of +100 Nerve & Energy</p>
                    <ul class="list-group">
                        <li class="list-group-item">Level 1-19 Cost: 500 Points Per level or  10,000 Points in total</li>
                        <li class="list-group-item">Level 20-39 Cost: 1,000 Points Per level or  20,000 Points in total</li>
                        <li class="list-group-item">Level 40-59 Cost: 2,000 Points Per level or  40,000 Points in total</li>
                        <li class="list-group-item">Level 60-79 Cost: 4,000 Points Per level or  80,000 Points in total</li>
                        <li class="list-group-item">Level 80-100 Cost: 8,000 Points Per level or  160,000 Points in total</li>
                        <li class="list-group-item">Level 101-250 Cost: 10,000 Points Per level or  1,500,000 Points in total</li>
                    </ul>
                    <p class="mt-3">Cost to Max Level 250 - 1,810,000 Points</p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h4>Bank Income Upgrade</h4>
                </div>
                <div class="card-body">
                    <p>Total of 10 Upgrades - Maximum Boost of +100%</p>
                    <ul class="list-group">
                        <li class="list-group-item">Each upgrade costs 20K Points and grants +10% Bank Daily Income</li>
                        <li class="list-group-item">Level 10 will take your 600k Daily interest up to 1.2 Million per day!</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>
