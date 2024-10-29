<?php

require "header.php";

$currentQuestSeason = getCurrentQuestSeasonForUser($user_class);
$questSeasonUser = getQuestSeasonUser($user_class->id, $currentQuestSeason['id']);

if ($questSeasonUser) {

?>

<h1><?php echo $currentQuestSeason['name'] ?>: </h1><hr />

<p>
    Welcome to operations, complete activities to earn rewards with each category hosting 7 levels of challenges! Operations run monthly and reset for you
    to complete on the first day of each month.
</p>

<div class="row">
    <div class="col-md-12">
    </div>
</div>
<?php

} else {

}
