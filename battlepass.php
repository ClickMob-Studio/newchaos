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
    </div>
</div>

<?php
include 'footer.php';
?>