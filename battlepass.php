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
        <p>You currently have <?php echo number_format($bpCategoryUser['points'], 0) ?> Battle Pass points</p>

        <div class="table-responsive">
            <table class="new_table" id="newtables">
                <tr>
                    <th>Challenges</th>
                </tr>
                <tr>
                    <td>

                        <?php foreach ($bpCategoryChallenges as $bpCategoryChallenge): ?>
                            <div class="card text-white bg-danger mb-3">
                                <div class="card-header"><?php echo number_format($bpCategoryChallenge['amount'], 0) ?> x <?php echo ucfirst($bpCategoryChallenge['type']) ?></div>
                                <div class="card-body">
                                    <p class="card-text">
                                        <?php echo number_format($bpCategoryUser[$bpCategoryChallenge['type']], 0) ?>/<?php echo number_format($bpCategoryChallenge['amount'], 0) ?><br />
                                        Points:  <?php echo $bpCategoryChallenge['prize'] ?>
                                    </p>
                                </div>
                                <div class="card-footer">
                                    <center>
                                        <?php if ($bpCategoryUser[$bpCategoryChallenge['type']] >= $bpCategoryChallenge['amount'] && !in_array($bpCategoryChallenge['id'], $challengesClaimed)): ?>
                                            <a href="battlepass.php?claim_challenge=<?php echo $bpCategoryChallenge['id'] ?>" class="btn btn-primary">(Complete)</a>
                                        <?php elseif (in_array($bpCategoryChallenge['id'], $challengesClaimed)): ?>
                                            <span style="color: green">Completed</span>
                                        <?php endif; ?>
                                    </center>
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
                            <div class="col-md-4">
                                <div class="card text-white bg-danger mb-3">
                                    <div class="card-header"><?php echo $bpCategoryPrize['amount'] ?> x <?php echo $bpCategoryPrize['type'] ?></div>
                                    <div class="card-body">
                                        <p class="card-text">
                                            Points Cost:  <?php echo $bpCategoryPrize['cost'] ?>
                                        </p>
                                    </div>
                                    <div class="card-footer">
                                        <center>
                                            <?php if ($bpCategoryUser['points'] >= $bpCategoryPrize['cost'] && !in_array($bpCategoryPrize['id'], $prizesClaimed)): ?>
                                                <a href="battlepass.php?claim_prize=<?php echo $bpCategoryPrize['id'] ?>" class="btn btn-primary">(Claimed)</a>
                                            <?php elseif (in_array($bpCategoryPrize['id'], $prizesClaimed)): ?>
                                                <span style="color: green">Claim</span>
                                            <?php endif; ?>
                                        </center>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </td>
                </tr>

            </table>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>