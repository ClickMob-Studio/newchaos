<?php

include 'header.php';

if ($user_class->gang < 1) {
    diefun('You are not in a gang.');
}

$gangCompLeaderboard = getGangCompLeaderboard($user_class->gang);

$prizesClaimed = unserialize($gangCompLeaderboard['serialised_prizes_claimed']);
if (!$prizesClaimed || $prizesClaimed == '') {
    $prizesClaimed = array();
}

$gang_class = new Gang($user_class->gang);
$isLeader = false;
if ($gang_class->leader == $user_class->id) {
    $isLeader = true;
}


$claimPrizeOptions = array('crimes','kills','busts','mugs');
if (isset($_GET['claim_prize']) && in_array($_GET['claim_prize'], $claimPrizeOptions)) {
    if (!$isLeader) {
        diefun('Only a gang leader can claim a prize.');
    }

    $claimPrize = $_GET['claim_prize'];

    if ($claimPrize === 'crimes') {
        if ($gangCompLeaderboard['weekly_crimes_complete'] >= 10000000) {
            if (in_array($claimPrize, $prizesClaimed)) {
                diefun('You have already claimed this prize.');
            } else {
                $prizesClaimed[] = 'crimes';

                $db->query("UPDATE gang_comp_leaderboard SET serialised_prizes_claimed = '" . serialize($prizesClaimed) . "' WHERE gang_id = " . $user_class->gang);
                $db->execute();

                Give_Item(257, $user_class->id);

                $resMes = 'You have successfully claimed your Gang Double EXP Pill for completing the crimes mission. It has been added to your inventory.';
            }
        } else {
            diefun('You have not earned the prize yet.');
        }
    }

    if ($claimPrize === 'kills') {
        if ($gangCompLeaderboard['weekly_attacks_complete'] >= 150000) {
            if (in_array($claimPrize, $prizesClaimed)) {
                diefun('You have already claimed this prize.');
            } else {
                $prizesClaimed[] = 'kills';

                $db->query("UPDATE gang_comp_leaderboard SET serialised_prizes_claimed = '" . serialize($prizesClaimed) . "' WHERE gang_id = " . $user_class->gang);
                $db->execute();

                Give_Item(257, $user_class->id);

                $resMes = 'You have successfully claimed your Gang Double EXP Pill for completing the kills mission. It has been added to your inventory.';
            }
        } else {
            diefun('You have not earned the prize yet.');
        }
    }

    if ($claimPrize === 'busts') {
        if ($gangCompLeaderboard['weekly_busts_complete'] >= 200000) {
            if (in_array($claimPrize, $prizesClaimed)) {
                diefun('You have already claimed this prize.');
            } else {
                $prizesClaimed[] = 'busts';

                $db->query("UPDATE gang_comp_leaderboard SET serialised_prizes_claimed = '" . serialize($prizesClaimed) . "' WHERE gang_id = " . $user_class->gang);
                $db->execute();

                Give_Item(257, $user_class->id);

                $resMes = 'You have successfully claimed your Gang Double EXP Pill for completing the busts mission. It has been added to your inventory.';
            }
        } else {
            diefun('You have not earned the prize yet.');
        }
    }

    if ($claimPrize === 'mugs') {
        if ($gangCompLeaderboard['weekly_mugs_complete'] >= 150000) {
            if (in_array($claimPrize, $prizesClaimed)) {
                diefun('You have already claimed this prize.');
            } else {
                $prizesClaimed[] = 'mugs';

                $db->query("UPDATE gang_comp_leaderboard SET serialised_prizes_claimed = '" . serialize($prizesClaimed) . "' WHERE gang_id = " . $user_class->gang);
                $db->execute();

                Give_Item(257, $user_class->id);

                $resMes = 'You have successfully claimed your Gang Double EXP Pill for completing the mugs mission. It has been added to your inventory.';
            }
        } else {
            diefun('You have not earned the prize yet.');
        }
    }
}

?>

<div class='box_top'><h1>Gang Challenge</h1></div>
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
                Mobsters, it's time to gang up and show what you & the homies are made of! Complete the missions below with 4 x Gang Double EXP Pills up for grabs!
            </p>
            <p>
                Each mission complete will earn your 1 x Gang Double EXP Pill. Once the mission is complete, your gang leader will be able to claim the prize on this page
                and the pill will be added to their inventory, ready for them to activate double EXP for your gang at anytime!
            </p>

            <p style="color: red;"><strong>Challenge Ends 26th May at 9am Server Time.</strong></p>

            <p>Enjoy!</p>
            <br /><br /><hr />
        </center>


        <table id="newtables" style="width:100%; text-align: left;">
            <tr>
                <th><b>Mission</b></th>
                <th width="30%"><b>Progress</b></th>
                <th>Rewards</th>
                <th><b>Claim</b></th>
            </tr>

            <!-- CRIMES -->
            <tr>
                <td>
                    <center>
                        <strong>Crimes</strong><br />
                        Complete 12,000,000 Crimes
                    </center>
                </td>
                <td>
                    <?php
                    $barWidthPer = $gangCompLeaderboard['weekly_crimes_complete'] / 12000000 * 100;
                    ?>
                    <div class="progress pb-star-holder" style="height:2rem;" role="progressbar" aria-valuenow="<?php echo $barWidthPer ?>%" aria-valuemin="0" aria-valuemax="100" title="<?php echo number_format($gangCompLeaderboard['weekly_crimes_complete'], 0) ?>/10,000,000">
                        <div class="progress-bar bg-success pb-star-bar" style="background-color: #ff6218 !important; width: <?php echo $barWidthPer ?>%">
                            <?php echo number_format($gangCompLeaderboard['weekly_crimes_complete'], 0) ?>/10,000,000
                        </div>
                    </div>
                </td>
                <td>
                    <ul>
                        <li>1 x Gang Double EXP Pill</li>
                        <li>500,000 points</li>
                    </ul>
                </td>
                <td>
                    <center>
                        <?php if ($isLeader && !in_array('crimes', $prizesClaimed) && $gangCompLeaderboard['weekly_crimes_complete'] >= 10000000): ?>
                            <a class="btn btn-success" href="gang_doubleexp.php?claim_prize=crimes">Claim Prize</a>
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
                        Complete 150,000 Kills
                    </center>
                </td>
                <td>
                    <?php
                    $barWidthPer = $gangCompLeaderboard['weekly_attacks_complete'] / 150000 * 100;
                    ?>
                    <div class="progress pb-star-holder" style="height:2rem;" role="progressbar" aria-valuenow="<?php echo $barWidthPer ?>%" aria-valuemin="0" aria-valuemax="100" title="<?php echo number_format($gangCompLeaderboard['weekly_attacks_complete'], 0) ?>/150,000">
                        <div class="progress-bar bg-success pb-star-bar" style="background-color: #ff6218 !important; width: <?php echo $barWidthPer ?>%">
                            <?php echo number_format($gangCompLeaderboard['weekly_attacks_complete'], 0) ?>/150,000
                        </div>
                    </div>
                </td>
                <td>
                    <ul>
                        <li>1 x Lifewood Crystal (Rare)</li>
                        <li>200,000 points</li>
                    </ul>
                </td>
                <td>
                    <center>
                        <?php if ($isLeader && !in_array('kills', $prizesClaimed) && $gangCompLeaderboard['weekly_attacks_complete'] >= 150000): ?>
                            <a class="btn btn-success" href="gang_doubleexp.php?claim_prize=kills">Claim Prize</a>
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
                        Complete 200,000 Busts
                    </center>
                </td>
                <td>
                    <?php
                    $barWidthPer = $gangCompLeaderboard['weekly_busts_complete'] / 200000 * 100;
                    ?>
                    <div class="progress pb-star-holder" style="height:2rem;" role="progressbar" aria-valuenow="<?php echo $barWidthPer ?>%" aria-valuemin="0" aria-valuemax="100" title="<?php echo number_format($gangCompLeaderboard['weekly_busts_complete'], 0) ?>/200,000">
                        <div class="progress-bar bg-success pb-star-bar" style="background-color: #ff6218 !important; width: <?php echo $barWidthPer ?>%">
                            <?php echo number_format($gangCompLeaderboard['weekly_busts_complete'], 0) ?>/200,000
                        </div>
                    </div>
                </td>
                <td>
                    <ul>
                        <li>1 x Gang Double EXP Pill</li>
                        <li>200,000 points</li>
                    </ul>
                </td>
                <td>
                    <center>
                        <?php if ($isLeader && !in_array('busts', $prizesClaimed) && $gangCompLeaderboard['weekly_busts_complete'] >= 200000): ?>
                            <a class="btn btn-success" href="gang_doubleexp.php?claim_prize=busts">Claim Prize</a>
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
                        Complete 150,000 Mugs
                    </center>
                </td>
                <td>
                    <?php
                    $barWidthPer = $gangCompLeaderboard['weekly_mugs_complete'] / 150000 * 100;
                    ?>
                    <div class="progress pb-star-holder" style="height:2rem;" role="progressbar" aria-valuenow="<?php echo $barWidthPer ?>%" aria-valuemin="0" aria-valuemax="100" title="<?php echo number_format($gangCompLeaderboard['weekly_mugs_complete'], 0) ?>/150,000">
                        <div class="progress-bar bg-success pb-star-bar" style="background-color: #ff6218 !important; width: <?php echo $barWidthPer ?>%">
                            <?php echo number_format($gangCompLeaderboard['weekly_mugs_complete'], 0) ?>/150,000
                        </div>
                    </div>
                </td>
                <td>
                    <ul>
                        <li>5 x Research Tokens</li>
                        <li>200,000 points</li>
                    </ul>
                </td>
                <td>
                    <center>
                        <?php if ($isLeader && !in_array('mugs', $prizesClaimed) && $gangCompLeaderboard['weekly_mugs_complete'] >= 150000): ?>
                            <a class="btn btn-success" href="gang_doubleexp.php?claim_prize=mugs">Claim Prize</a>
                        <?php endif; ?>

                        <?php if (in_array('mugs', $prizesClaimed)): ?>
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
