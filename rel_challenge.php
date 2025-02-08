<?php

include 'header.php';
if ($user_class->admin < 1) {
    //exit;
}
$relCompLeaderboard = geRelCompLeaderboard($user_class->id);

$prizesClaimed = unserialize($relCompLeaderboard['serialised_prizes_claimed']);
if (!$prizesClaimed || $prizesClaimed == '') {
    $prizesClaimed = array();
}


$claimPrizeOptions = array('crimes','kills','busts','mugs', 'ba');
if (isset($_GET['claim_prize']) && in_array($_GET['claim_prize'], $claimPrizeOptions)) {
    $claimPrize = $_GET['claim_prize'];

    if ($claimPrize === 'crimes') {
        if ($relCompLeaderboard['overall_crimes_complete'] >= 2500000) {
            if (in_array($claimPrize, $prizesClaimed)) {
                diefun('You have already claimed this prize.');
            } else {
                $prizesClaimed[] = 'crimes';

                $db->query("UPDATE rel_comp_leaderboard SET serialised_prizes_claimed = '" . serialize($prizesClaimed) . "' WHERE id = " . $relCompLeaderboard['id']);
                $db->execute();

                Give_Item(324, $relCompLeaderboard['user_id'], 5);
                $db->query("UPDATE grpgusers SET points = points + 300000 WHERE id = " . $relCompLeaderboard['user_id']);
                $db->execute();

                Give_Item(324, $relCompLeaderboard['two_user_id'], 5);
                $db->query("UPDATE grpgusers SET points = points + 300000 WHERE id = " . $relCompLeaderboard['two_user_id']);
                $db->execute();

                $resMes = 'You have successfully claimed your rewards for completing the crimes mission.';
            }
        } else {
            diefun('You have not earned the prize yet.');
        }
    }

    if ($claimPrize === 'kills') {
        if ($relCompLeaderboard['overall_attacks_complete'] >= 50000) {
            if (in_array($claimPrize, $prizesClaimed)) {
                diefun('You have already claimed this prize.');
            } else {
                $prizesClaimed[] = 'kills';

                $db->query("UPDATE rel_comp_leaderboard SET serialised_prizes_claimed = '" . serialize($prizesClaimed) . "' WHERE id = " . $relCompLeaderboard['id']);
                $db->execute();

                Give_Item(322, $relCompLeaderboard['user_id'], 10);
                $db->query("UPDATE grpgusers SET points = points + 100000 WHERE id = " . $relCompLeaderboard['user_id']);
                $db->execute();

                Give_Item(322, $relCompLeaderboard['two_user_id'], 10);
                $db->query("UPDATE grpgusers SET points = points + 100000 WHERE id = " . $relCompLeaderboard['two_user_id']);
                $db->execute();

                $resMes = 'You have successfully claimed your rewards for completing the kills mission.';
            }
        } else {
            diefun('You have not earned the prize yet.');
        }
    }

    if ($claimPrize === 'busts') {
        if ($relCompLeaderboard['overall_busts_complete'] >= 20000) {
            if (in_array($claimPrize, $prizesClaimed)) {
                diefun('You have already claimed this prize.');
            } else {
                $prizesClaimed[] = 'busts';

                $db->query("UPDATE rel_comp_leaderboard SET serialised_prizes_claimed = '" . serialize($prizesClaimed) . "' WHERE id = " . $relCompLeaderboard['id']);
                $db->execute();

                Give_Item(277, $relCompLeaderboard['user_id'], 3);
                $db->query("UPDATE grpgusers SET points = points + 100000 WHERE id = " . $relCompLeaderboard['user_id']);
                $db->execute();

                Give_Item(277, $relCompLeaderboard['two_user_id'], 3);
                $db->query("UPDATE grpgusers SET points = points + 100000 WHERE id = " . $relCompLeaderboard['two_user_id']);
                $db->execute();

                $resMes = 'You have successfully claimed your rewards for completing the busts mission.';
            }
        } else {
            diefun('You have not earned the prize yet.');
        }
    }

    if ($claimPrize === 'mugs') {
        if ($relCompLeaderboard['overall_mugs_complete'] >= 25000) {
            if (in_array($claimPrize, $prizesClaimed)) {
                diefun('You have already claimed this prize.');
            } else {
                $prizesClaimed[] = 'mugs';

                $db->query("UPDATE rel_comp_leaderboard SET serialised_prizes_claimed = '" . serialize($prizesClaimed) . "' WHERE id = " . $relCompLeaderboard['id']);
                $db->execute();

                Give_Item(276, $relCompLeaderboard['user_id'], 3);
                $db->query("UPDATE grpgusers SET points = points + 100000 WHERE id = " . $relCompLeaderboard['user_id']);
                $db->execute();

                Give_Item(276, $relCompLeaderboard['two_user_id'], 3);
                $db->query("UPDATE grpgusers SET points = points + 100000 WHERE id = " . $relCompLeaderboard['two_user_id']);
                $db->execute();

                $resMes = 'You have successfully claimed your rewards for completing the mugs mission.';
            }
        } else {
            diefun('You have not earned the prize yet.');
        }
    }
    if ($claimPrize === 'ba') {
        if ($gangCompLeaderboard['weekly_ba_complete'] >= 125000) {
            if (in_array($claimPrize, $prizesClaimed)) {
                diefun('You have already claimed this prize.');
            } else {
                $prizesClaimed[] = 'ba';

                $db->query("UPDATE rel_comp_leaderboard SET serialised_prizes_claimed = '" . serialize($prizesClaimed) . "' WHERE id = " . $relCompLeaderboard['id']);
                $db->execute();

                Give_Item(325, $relCompLeaderboard['user_id'], 3);
                $db->query("UPDATE grpgusers SET points = points + 100000 WHERE id = " . $relCompLeaderboard['user_id']);
                $db->execute();

                Give_Item(325, $relCompLeaderboard['two_user_id'], 3);
                $db->query("UPDATE grpgusers SET points = points + 100000 WHERE id = " . $relCompLeaderboard['two_user_id']);
                $db->execute();

                $resMes = 'You have successfully claimed your rewards for completing the BA mission.';
            }
        } else {
            diefun('You have not earned the prize yet.');
        }
    }
}

?>

<div class='box_top'><h1>Valentines Challenge</h1></div>
<div class='box_middle'>
    <div class='pad'>
        <br />
        <center>
            <?php if (isset($resMes) && $resMes): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $resMes ?>
                </div>
            <?php endif; ?>

            <p>
                Mobsters, it's time to and show what you and your parnter made of! Complete the missions below and earn the rewards listed!
            </p>

            <p style="color: red">Challenge Ends February 15, 2025 09:00:00 Server Time</p>

            <p>Enjoy!</p>
            <br /><br /><hr />
        </center>


        <table id="newtables" style="width:100%; text-align: left;">
            <tr>
                <th><b>Mission</b></th>
                <th width="25%"><b>Progress</b></th>
                <th>Rewards</th>
                <th><b>Claim</b></th>
            </tr>

            <!-- CRIMES -->
            <tr>
                <td>
                    <center>
                        <strong>Crimes</strong><br />
                        Complete 2,500,000 Crimes
                    </center>
                </td>
                <td>
                    <?php
                    $barWidthPer = $relCompLeaderboard['overall_crimes_complete'] / 2500000 * 100;
                    ?>
                    <div class="progress pb-star-holder" style="height:2rem;" role="progressbar" aria-valuenow="<?php echo $barWidthPer ?>%" aria-valuemin="0" aria-valuemax="100" title="<?php echo number_format($relCompLeaderboard['overall_crimes_complete'], 0) ?>/2,500,000">
                        <div class="progress-bar bg-success pb-star-bar" style="background-color: #ff6218 !important; width: <?php echo $barWidthPer ?>%">
                            <?php echo number_format($relCompLeaderboard['overall_crimes_complete'], 0) ?>/2,500,000
                        </div>
                    </div>
                </td>
                <td>
                    <ul>
                        <li>5 x Perfume</li>
                        <li>300,000 points</li>
                    </ul>
                </td>
                <td>
                    <center>
                        <?php if (!in_array('crimes', $prizesClaimed) && $relCompLeaderboard['overall_crimes_complete'] >= 2500000): ?>
                            <a class="btn btn-success" href="rel_challenge.php?claim_prize=crimes">Claim Prize</a>
                        <?php endif; ?>

                        <?php if (in_array('crimes', $prizesClaimed)): ?>
                            <span style="color: green">Claimed</span>
                        <?php endif; ?>
                    </center>
                </td>
            </tr>

            <!-- ATTACKS -->
            <tr>
                <td>
                    <center>
                        <strong>Kills</strong><br />
                        Complete 50,000 Kills
                    </center>
                </td>
                <td>
                    <?php
                    $barWidthPer = $relCompLeaderboard['overall_attacks_complete'] / 50000 * 100;
                    ?>
                    <div class="progress pb-star-holder" style="height:2rem;" role="progressbar" aria-valuenow="<?php echo $barWidthPer ?>%" aria-valuemin="0" aria-valuemax="100" title="<?php echo number_format($relCompLeaderboard['overall_attacks_complete'], 0) ?>/50,000">
                        <div class="progress-bar bg-success pb-star-bar" style="background-color: #ff6218 !important; width: <?php echo $barWidthPer ?>%">
                            <?php echo number_format($relCompLeaderboard['overall_attacks_complete'], 0) ?>/50,000
                        </div>
                    </div>
                </td>
                <td>
                    <ul>
                        <li>10 x Love Potions</li>
                        <li>100,000 points</li>
                    </ul>
                </td>
                <td>
                    <center>
                        <?php if (!in_array('kills', $prizesClaimed) && $relCompLeaderboard['overall_attacks_complete'] >= 50000): ?>
                            <a class="btn btn-success" href="rel_challenge.php?claim_prize=kills">Claim Prize</a>
                        <?php endif; ?>

                        <?php if (in_array('kills', $prizesClaimed)): ?>
                            <span style="color: green">Claimed</span>
                        <?php endif; ?>
                    </center>
                </td>
            </tr>

            <!-- BUSTS -->
            <tr>
                <td>
                    <center>
                        <strong>Busts</strong><br />
                        Complete 20,000 Busts
                    </center>
                </td>
                <td>
                    <?php
                    $barWidthPer = $relCompLeaderboard['overall_busts_complete'] / 20000 * 100;
                    ?>
                    <div class="progress pb-star-holder" style="height:2rem;" role="progressbar" aria-valuenow="<?php echo $barWidthPer ?>%" aria-valuemin="0" aria-valuemax="100" title="<?php echo number_format($relCompLeaderboard['overall_busts_complete'], 0) ?>/20,000">
                        <div class="progress-bar bg-success pb-star-bar" style="background-color: #ff6218 !important; width: <?php echo $barWidthPer ?>%">
                            <?php echo number_format($relCompLeaderboard['overall_busts_complete'], 0) ?>/20,000
                        </div>
                    </div>
                </td>
                <td>
                    <ul>
                        <li>3 x Mission Passes</li>
                        <li>100,000 points</li>
                    </ul>
                </td>
                <td>
                    <center>
                        <?php if (!in_array('busts', $prizesClaimed) && $relCompLeaderboard['overall_busts_complete'] >= 20000): ?>
                            <a class="btn btn-success" href="rel_challenge.php?claim_prize=busts">Claim Prize</a>
                        <?php endif; ?>

                        <?php if (in_array('busts', $prizesClaimed)): ?>
                            <span style="color: green">Claimed</span>
                        <?php endif; ?>
                    </center>
                </td>
            </tr>

            <!-- MUGS -->
            <tr>
                <td>
                    <center>
                        <strong>Mugs</strong><br />
                        Complete 25,000 Mugs
                    </center>
                </td>
                <td>
                    <?php
                    $barWidthPer = $relCompLeaderboard['overall_mugs_complete'] / 25000 * 100;
                    ?>
                    <div class="progress pb-star-holder" style="height:2rem;" role="progressbar" aria-valuenow="<?php echo $barWidthPer ?>%" aria-valuemin="0" aria-valuemax="100" title="<?php echo number_format($relCompLeaderboard['overall_mugs_complete'], 0) ?>/25,000">
                        <div class="progress-bar bg-success pb-star-bar" style="background-color: #ff6218 !important; width: <?php echo $barWidthPer ?>%">
                            <?php echo number_format($relCompLeaderboard['overall_mugs_complete'], 0) ?>/25,000
                        </div>
                    </div>
                </td>
                <td>
                    <ul>
                        <li>3 x Research Tokens</li>
                        <li>100,000 points</li>
                    </ul>
                </td>
                <td>
                    <center>
                        <?php if (!in_array('mugs', $prizesClaimed) && $relCompLeaderboard['overall_mugs_complete'] >= 25000): ?>
                            <a class="btn btn-success" href="rel_challenge.php?claim_prize=mugs">Claim Prize</a>
                        <?php endif; ?>

                        <?php if (in_array('mugs', $prizesClaimed)): ?>
                            <span style="color: green">Claimed</span>
                        <?php endif; ?>
                    </center>
                </td>
            </tr>

            <!-- BA -->
            <tr>
                <td>
                    <center>
                        <strong>Backalley</strong><br />
                        Complete 40,000 BA Searches
                    </center>
                </td>
                <td>
                    <?php
                    $barWidthPer = $relCompLeaderboard['overall_ba_complete'] / 40000 * 100;
                    ?>
                    <div class="progress pb-star-holder" style="height:2rem;" role="progressbar" aria-valuenow="<?php echo $barWidthPer ?>%" aria-valuemin="0" aria-valuemax="100" title="<?php echo number_format($relCompLeaderboard['overall_ba_complete'], 0) ?>/40,000">
                        <div class="progress-bar bg-success pb-star-bar" style="background-color: #ff6218 !important; width: <?php echo $barWidthPer ?>%">
                            <?php echo number_format($relCompLeaderboard['overall_ba_complete'], 0) ?>/40,000
                        </div>
                    </div>
                </td>
                <td>
                    <ul>
                        <li>1 x Heart Bed</li>
                        <li>100,000 points</li>
                    </ul>
                </td>
                <td>
                    <center>
                        <?php if (!in_array('mugs', $prizesClaimed) && $relCompLeaderboard['overall_ba_complete'] >= 40000): ?>
                            <a class="btn btn-success" href="rel_challenge.php?claim_prize=ba">Claim Prize</a>
                        <?php endif; ?>

                        <?php if (in_array('ba', $prizesClaimed)): ?>
                            <span style="color: green">Claimed</span>
                        <?php endif; ?>
                    </center>
                </td>
            </tr>
        </table>
    </div>
</div>

<?php
include 'footer.php';
?>
