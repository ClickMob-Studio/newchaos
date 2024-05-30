<?php

include 'header.php';

$now = new \DateTime();


if ($user_class->admin > 0) {
    $db->query("SELECT * FROM bp_category WHERE month_year = '06-2024' LIMIT 1");
    $db->execute();
    $r = $db->fetch_row();
    $bpCategory = $r[0];

} else {
    $bpCategory = getBpCategory();
}
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

    if ($challenge['is_premium'] > 0 && $bpCategoryUser['is_premium'] < 1) {
        diefun('You need to purchase a premium Battle Pass to complete this challenge.');
    }

    if ($bpCategoryUser[$challenge['type']] >= $challenge['amount']) {
        $challengesClaimed[] = $challenge['id'];

        $newChallengesClaimed = serialize($challengesClaimed);

        $db->query("UPDATE bp_category_user SET points = points + " . $challenge['prize'] . ", challenge_ids_serialized = '" . $newChallengesClaimed . "' WHERE id = " . $bpCategoryUser['id']);
        $db->execute();

        $resMes = 'You have successfully completed the challenge';
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

    if ($prize['is_premium'] > 0 && $bpCategoryUser['is_premium'] < 1) {
        diefun('You need to purchase a premium Battle Pass to claim this prize.');
    }

    if ($bpCategoryUser['points'] >= $prize['cost']) {
        $prizesClaimed[] = $prize['id'];
        $newPrizesClaimed = serialize($prizesClaimed);

        $db->query("UPDATE bp_category_user SET prize_ids_serialized = '" . $newPrizesClaimed . "' WHERE user_id = " . $bpCategoryUser['user_id']);
        $db->execute();

        if ($prize['type'] === 'points') {
            $db->query("UPDATE grpgusers SET points = points + '" . $prize['amount'] . "' WHERE id = " . $bpCategoryUser['user_id']);
            $db->execute();

            $resMes =  'You have successfully claimed your prize of ' . number_format($prize['amount'], 0) . ' points.';
        }

        if ($prize['type'] === 'money') {
            $db->query("UPDATE grpgusers SET money = money + '" . $prize['amount'] . "' WHERE id = " . $bpCategoryUser['user_id']);
            $db->execute();

            $resMes = 'You have successfully claimed your prize of $' . number_format($prize['amount'], 0);
        }

        if ($prize['type'] === 'item') {
            Give_Item($prize['entity_id'], $user_class->id, $prize['amount']);

            $resMes =  'You have successfully claimed your prize of ' . number_format($prize['amount']) . ' x ' . Item_Name($prize['entity_id']) . '.';
        }

        if ($prize['type'] === 'raid_tokens') {
            $db->query("UPDATE grpgusers SET raidtokens = raidtokens + '" . $prize['amount'] . "' WHERE id = " . $bpCategoryUser['user_id']);
            $db->execute();

            $resMes = 'You have successfully claimed your prize of ' . number_format($prize['amount'], 0) . ' Raid Tokens.';
        }
    }
}

?>

<div class='box_top'><h1>Battle Pass</h1></div>
<div class='box_middle'>
    <div class='pad'>
        <p><center><strong>You currently have <?php echo number_format($bpCategoryUser['points'], 0) ?> Battle Pass points</strong></center></p>

        <p>Welcome to the Battle Pass! Here you can complete challenges to earn prizes. You'll see all your challenges & prizes below.</p>
        <p>Battle Pass points are earned by completing challenges and then can be used to claim prizes.</p>
        <p>
            The Battle Pass resets & changes monthly. You have the option of just running the free Battle Pass, or you can update
            via the store to unlock the months Premium Battle Pass challenges & prizes too.
        </p>

        <?php if ($bpCategory['id'] > 1): ?>
                <?php if ($bpCategoryUser['is_premium'] < 1): ?>
                    <p style="color: red;"><strong>You have not yet purchased this months Premium Battle Pass, purchase now from the store to unlock more challenges and prizes.</strong></p>
                <?php endif; ?>
        <?php else: ?>
            <p><strong>This months Battle Pass is free and includes no premium options.</strong></p>
        <?php endif; ?>

        <?php if (isset($resMes) && $resMes): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $resMes ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="new_table" id="newtables">
                <tr>
                    <th>Challenges</th>
                </tr>
                <?php if ($bpCategory['id'] > 0): ?>
                    <tr>
                        <td>
                            <br />
                            Filter: <a href="#" class="filter-link" data-filter-type="all">All</a> | <a href="#" class="filter-link" data-filter-type="crimes">Crimes</a> |
                            <a href="#" class="filter-link" data-filter-type="attacks">Attacks</a> | <a href="#" class="filter-link" data-filter-type="mugs">Mugs</a> |
                            <a href="#" class="filter-link" data-filter-type="busts">Busts</a> | <a href="#" class="filter-link" data-filter-type="backalley">Backalley</a> |
                            <a href="#" class="filter-link" data-filter-type="trains">Trains</a> | <a href="#" class="filter-link" data-filter-type="premium">Premium</a>
                            <br /><br />
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <div class="row">
                            <?php foreach ($bpCategoryChallenges as $bpCategoryChallenge): ?>
                                <?php
                                $isComplete = false;
                                $divClass = 'bg-danger';
                                if ($bpCategoryChallenge['is_premium'] > 0) {
                                    $divClass = 'bg-info';
                                }
                                if (in_array($bpCategoryChallenge['id'], $challengesClaimed)) {
                                    $isComplete = true;
                                    $divClass = 'bg-success';
                                }
                                ?>

                                <div class="col-md-4">
                                    <div class="card text-white <?php echo $divClass ?> mb-3 <?php echo $bpCategoryChallenge['type'] ?>-c-card">
                                        <div class="card-header">
                                            <?php echo number_format($bpCategoryChallenge['amount'], 0) ?> x <?php echo ucfirst($bpCategoryChallenge['type']) ?>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">
                                                <?php echo number_format($bpCategoryUser[$bpCategoryChallenge['type']], 0) ?>/<?php echo number_format($bpCategoryChallenge['amount'], 0) ?><br />
                                                Points:  <?php echo $bpCategoryChallenge['prize'] ?>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            <center>
                                                <?php if ($bpCategoryChallenge['is_premium'] > 0 && $bpCategoryUser['is_premium'] < 1): ?>
                                                    Premium Only
                                                <?php else: ?>
                                                    <?php if ($bpCategoryUser[$bpCategoryChallenge['type']] >= $bpCategoryChallenge['amount'] && !$isComplete): ?>
                                                        <a href="battlepass.php?claim_challenge=<?php echo $bpCategoryChallenge['id'] ?>" class="btn btn-primary">(Complete)</a>
                                                    <?php elseif ($isComplete): ?>
                                                        Completed
                                                    <?php else: ?>
                                                        Incomplete
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th>Prizes</th>
                </tr>
                <tr>
                    <td>
                        <div class="row">
                            <?php foreach ($bpCategoryPrizes as $bpCategoryPrize): ?>
                                <?php
                                $isComplete = false;
                                $divClass = 'bg-danger';
                                if ($bpCategoryPrize['is_premium'] > 0) {
                                    $divClass = 'bg-info';
                                }
                                if (in_array($bpCategoryPrize['id'], $prizesClaimed)) {
                                    $isComplete = true;
                                    $divClass = 'bg-success';
                                }
                                ?>

                                <div class="col-md-4">
                                    <div class="card text-white <?php echo $divClass ?> mb-3">
                                        <div class="card-header">
                                            <?php echo displayBpCategoryPrize($bpCategoryPrize) ?>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">
                                                Points Cost:  <?php echo $bpCategoryPrize['cost'] ?>
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            <center>
                                                <?php if ($bpCategoryPrize['is_premium'] > 0 && $bpCategoryUser['is_premium'] < 1): ?>
                                                    Premium Only
                                                <?php else: ?>
                                                    <?php if ($bpCategoryUser['points'] >= $bpCategoryPrize['cost'] && !$isComplete): ?>
                                                        <a href="battlepass.php?claim_prize=<?php echo $bpCategoryPrize['id'] ?>" class="btn btn-primary">Claim</a>
                                                    <?php elseif ($isComplete): ?>
                                                        Claimed
                                                    <?php else: ?>
                                                        Unclaimed
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                    </td>
                </tr>

            </table>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('document').ready(function() {
        $('.filter-link').click(function(e) {
            e.preventDefault();

            var filterType = $(this).data('filter-type');

            if (filterType === 'all') {
                $('.crimes-c-card').show();
                $('.attacks-c-card').show();
                $('.mugs-c-card').show();
                $('.busts-c-card').show();
                $('.backalley-c-card').show();
                $('.trains-c-card').show();

            } else {
                $('.crimes-c-card').hide();
                $('.attacks-c-card').hide();
                $('.mugs-c-card').hide();
                $('.busts-c-card').hide();
                $('.backalley-c-card').hide();
                $('.trains-c-card').hide();

                $('.' + filterType + '-c-card').show();
            }

        });

    });
</script>

<?php
include 'footer.php';
?>