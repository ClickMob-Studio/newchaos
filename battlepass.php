<?php

include 'header.php';

$now = new \DateTime();

$bpCategory = getBpCategory();
$bpCategoryPrizes = getBpCategoryPrizes($bpCategory);
$bpCategoryChallenges = getBpCategoryChallenges($bpCategory);
$bpCategoryUser = getBpCategoryUser($bpCategory, $user_class);

?>

<div class='box_top'><h1>Battle Pass</h1></div>
<div class='box_middle'>
    <div class='pad'>
        <h2>Challenges</h2>
        <ul>
            <?php foreach ($bpCategoryChallenges as $bpCategoryChallenge): ?>
                <li>
                    <?php echo $bpCategoryChallenge['type'] ?> - <?php echo $bpCategoryChallenge['amount'] ?> - <?php echo $bpCategoryChallenge['prize'] ?>

                    <?php if ($bpCategoryUser[$bpCategoryChallenge['type']] >= $bpCategoryChallenge['type']): ?>
                        <a href="#">Claim</a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <h2>Prizes</h2>
        <ul>
            <?php foreach ($bpCategoryPrizes as $bpCategoryPrize): ?>
                <li><?php echo $bpCategoryPrize['type'] ?> - <?php echo $bpCategoryPrize['amount'] ?> - <?php echo $bpCategoryPrize['cost'] ?></li>
            <?php endforeach; ?>
        </ul>

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