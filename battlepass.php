<?php

include 'header.php';

$now = new \DateTime();

$bpCategory = getBpCategory();
$bpCategoryPrizes = getBpCategoryPrizes($bpCategory);
$bpCategoryChallenges = getBpCategoryChallenges($bpCategory);
$bpCategoryUser = getBpCategoryUser($bpCategory, $user_class);

$prizesClaimed = unserialize($bpCategoryUser['prize_ids_serialized']);
$challengesClaimed = unserialize($bpCategoryUser['challenge_ids_serialized']);

if (isset($_GET['claim_challenge']) && (int)$_GET['claim_challenge']) {
    $claimChallengeId = (int)$_GET['claim_challenge'];

    if (!isset($bpCategoryChallenges[$claimChallengeId])) {
        diefun('Something went wrong, if this issue persists please message an Admin.');
    }

    if (in_array($claimChallengeId, $challengesClaimed)) {
        diefun('You have already claimed this challenge.');
    }

    $challenge = $bpCategoryChallenges[$claimChallengeId];

    if ($bpCategoryUser[$challenge['type']] >= $challenge['amount']) {
        $challengesClaimed[] = $challenge['id'];

        $newChallengesClaimed = serialize($challengesClaimed);

        $db->query("UPDATE bp_category_user SET points = points + " . $challenge['prize'] . ", challenge_ids_serialized = '" . $newChallengesClaimed . "' WHERE id = " . $bpCategoryUser['id']);
        $db->execute();

        echo 'You have successfully completed the challenge';
        exit;
    } else {
        diefun('You have not completed this challenge yet.');
    }
}

if (isset($_GET['claim_prize']) && (int)$_GET['claim_prize']) {
    $claimPrizeId = (int)$_GET['claim_prize'];

    if (!isset($bpCategoryPrizes[$claimPrizeId])) {
        diefun('Something went wrong, if this issue persists please message an Admin.');
    }

    if (in_array($claimPrizeId, $prizesClaimed)) {
        diefun('You have already claimed this prize.');
    }

    $prize = $bpCategoryPrizes[$claimPrizeId];

    if ($bpCategoryUser['points'] >= $prize['cost']) {
        $prizesClaimed[] = $prize['id'];
        $newPrizesClaimed = serialize($prizesClaimed);

        $db->query("UPDATE bp_category_user SET prize_ids_serialized = '" . $newPrizesClaimed . "' WHERE id = " . $bpCategoryUser['id']);
        $db->execute();

        if ($prize['type'] === 'points') {
            $db->query("UPDATE grpgusers SET points = points + '" . $prize['amount'] . "' WHERE id = " . $bpCategoryUser['id']);
            $db->execute();

            echo 'You have successfully claimed your prize of ' . number_format($prize['amount'], 0) . ' points.';
            exit;
        }

        if ($prize['type'] === 'money') {
            $db->query("UPDATE grpgusers SET money = money + '" . $prize['amount'] . "' WHERE id = " . $bpCategoryUser['id']);
            $db->execute();

            echo 'You have successfully claimed your prize of $' . number_format($prize['amount'], 0);
            exit;
        }

        if ($prize['type'] === 'item') {
            Give_Item($prize['entity_id'], $user_class->id, $prize['amount']);

            echo 'You have successfully claimed your prize of ' . number_format($prize['amount']) . ' x ' . Item_Name($prize['entity_id']) . '.';
            exit;
        }

        if ($prize['type'] === 'raid_tokens') {
            $db->query("UPDATE grpgusers SET raidtokens = raidtokens + '" . $prize['amount'] . "' WHERE id = " . $bpCategoryUser['id']);
            $db->execute();

            echo 'You have successfully claimed your prize of ' . number_format($prize['amount'], 0) . ' Raid Tokens.';
            exit;
        }

        echo 'You have successfully claimed your prize';
        exit;
    }
}

?>

<div class='box_top'><h1>Battle Pass</h1></div>
<div class='box_middle'>
    <div class='pad'>
        <div class="table-responsive">
            <table class="table">
                <tr>
                    <th>Challenges</th>
                </tr>
                <tr>
                    <td>

                        <?php foreach ($bpCategoryChallenges as $bpCategoryChallenge): ?>
                            <div class="card text-center mb-3" style="width: 18rem;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $bpCategoryChallenge['amount'] ?> x <?php echo $bpCategoryChallenge['type'] ?></h5>
                                    <p class="card-text">Points:  <?php echo $bpCategoryChallenge['prize'] ?></p>

                                    <?php if ($bpCategoryUser[$bpCategoryChallenge['type']] >= $bpCategoryChallenge['amount'] && !in_array($bpCategoryChallenge['id'], $challengesClaimed)): ?>
                                        <a href="battlepass.php?claim_challenge=<?php echo $bpCategoryChallenge['id'] ?>" class="btn btn-primary">(Complete)</a>
                                    <?php elseif (in_array($bpCategoryChallenge['id'], $challengesClaimed)): ?>
                                        <span style="color: green">Completed</span>
                                    <?php endif; ?>
                                    <a href="#" >Go somewhere</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </td>
                </tr>

                <tr>
                    <th>Prizes</th>
                </tr>
                <tr>
                    <td>
                        <?php foreach ($bpCategoryPrizes as $bpCategoryPrize): ?>
                            <li>
                                <?php echo $bpCategoryPrize['type'] ?> - <?php echo $bpCategoryPrize['amount'] ?> - <?php echo $bpCategoryPrize['cost'] ?>

                                <?php if ($bpCategoryUser['points'] >= $bpCategoryPrize['cost'] && !in_array($bpCategoryPrize['id'], $prizesClaimed)): ?>
                                    <a href="battlepass.php?claim_prize=<?php echo $bpCategoryPrize['id'] ?>">(Claimed)</a>
                                <?php elseif (in_array($bpCategoryPrize['id'], $prizesClaimed)): ?>
                                    <span style="color: green">Claim</span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </td>
                </tr>

            </table>
        </div>

        <h3>Your Stats</h3>
        <ul>
            <li>Points: <?php echo $bpCategoryUser['points'] ?></li>
            <li>Crimes: <?php echo $bpCategoryUser['crimes'] ?></li>
            <li>Attacks: <?php echo $bpCategoryUser['attacks'] ?></li>
            <li>Mugs: <?php echo $bpCategoryUser['mugs'] ?></li>
            <li>Busts: <?php echo $bpCategoryUser['busts'] ?></li>
            <li>BA: <?php echo $bpCategoryUser['backalley'] ?></li>
            <li>Trains: <?php echo $bpCategoryUser['trains'] ?></li>
        </ul>

    </div>
</div>

<?php
include 'footer.php';
?>