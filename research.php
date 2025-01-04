<?php
include 'header.php';


$levels = 5;
$levelRows = array();
$combatTreeLevelRows = array();
$petTreeLevelRows = array();

$i = 1;
while ($i <= 6) {
    $db->query("SELECT * FROM `research_type` WHERE `level` = " . $i . " AND `type` = 'economy'");
    $db->execute();
    $levelRows[$i] = $db->fetch_row();

    $i++;
}

$i = 1;
while ($i <= 5) {
    $db->query("SELECT * FROM `research_type` WHERE `level` = " . $i . " AND `type` = 'combat'");
    $db->execute();
    $combatTreeLevelRows[$i] = $db->fetch_row();

    $i++;
}

$i = 1;
while ($i <= 5) {
    $db->query("SELECT * FROM `research_type` WHERE `level` = " . $i . " AND `type` = 'pet'");
    $db->execute();
    $petTreeLevelRows[$i] = $db->fetch_row();

    $i++;
}

$completeUserResearchTypes = $user_class->completeUserResearchTypes;
$completeUserResearchTypesIndexedOnId = $user_class->completeUserResearchTypesIndexedOnId;


$db->query("SELECT * FROM `user_research_type` WHERE `user_id` = " . $user_class->id . " AND `duration_in_days` > 0 LIMIT 1");
$db->execute();
$activeUserResearchType = $db->fetch_row(true);
if ($activeUserResearchType) {
    $db->query("SELECT * FROM `research_type` WHERE `id` = " . $activeUserResearchType['research_type_id']);
    $db->execute();
    $activeResearchType = $db->fetch_row(true);
}



if (isset($_GET['action']) && $_GET['action'] == 'start_research' && isset($_GET['rid']) && (int)$_GET['rid'])  {
    if ($activeUserResearchType) {
        diefun('You can only do one research at a time. <a href="research.php" style="color: red;">Go Back</a>');
    }

    $rid = (int)$_GET['rid'];

    $db->query("SELECT * FROM `research_type` WHERE `id` = " . $rid . " LIMIT 1");
    $db->execute();
    $researchType = $db->fetch_row(true);
    if (!$researchType) {
        diefun('You can only do one research at a time. <a href="research.php" style="color: red;">Go Back</a>');
    }

    if (isset($completeUserResearchTypesIndexedOnId[$researchType['id']])) {
        diefun('You have already completed this research. <a href="research.php" style="color: red;">Go Back</a>');
    }


    $db->query("SELECT * FROM `research_type` WHERE `level` < " . $researchType['level'] . " AND `type` = '" . $researchType['type'] . "'");
    $db->execute();
    $levelResearchTypes = $db->fetch_row();

    $isAllComplete = true;
    foreach ($levelResearchTypes as $levelResearchType) {
        if (!isset($completeUserResearchTypesIndexedOnId[$levelResearchType['id']])) {
            $isAllComplete = false;
        }
    }

    if (!$isAllComplete) {
        diefun('You need to complete all researches from the previous level to complete this research. <a href="research.php" style="color: red;">Go Back</a>');
    }
    $userPrestigeSkills = getUserPrestigeSkills($user_class);
    if ($userPrestigeSkills['research_cash_unlock'] > 0) {
        $researchType['cost'] = $researchType['cost'] - ($researchType['cost'] / 100 * 10);
    }
    if ($userPrestigeSkills['research_cash_boost_level'] > 0) {
        $researchType['cost'] = $researchType['cost'] - ($researchType['cost'] / 100 * (2 * $userPrestigeSkills['research_cash_boost_level']));
    }
    if ($researchType['cost'] > $user_class->money) {
        diefun('You need more cash on hand to complete this research. <a href="research.php" style="color: red;">Go Back</a>');
    }

    $db->query("
      INSERT INTO 
        user_research_type (user_id, research_type_id, duration_in_days)
      VALUES
        (" . $user_class->id . ", " . $researchType['id'] . ", " . $researchType['duration_in_days'] . ");
    ");
    $db->execute();

    $db->query("UPDATE grpgusers SET money = money - " . $researchType['cost'] . " WHERE id = " . $user_class->id);
    $db->execute();

    diefun('You have successfully started researching ' .  $researchType['name'] . '<a href="research.php" style="color: red;">Go Back</a>');
}
?>

<div class='box_top'>Research</div>
<div class='box_middle'>
    <!-- Combat Research -->
    <div class="row">
        <div class="col-md-12">
            <h2>Research</h2>

            <?php if ($activeUserResearchType): ?>
                <div class='alert alert-success'>
                    <p>Your currently researching <?php echo $activeResearchType['name'] ?> and have <?php echo $activeUserResearchType['duration_in_days'] ?> days remaining until it's complete.</p>
                </div>
            <?php endif; ?>

            <h3>Economic Tree</h3>
            <div class="table-container">
                <table class="new_table" id="newtables" style="width:100%;">
                        <tr>
                            <?php
                            $i = 1;
                            while ($i <= 6):
                            ?>
                                <td>
                                    <?php foreach ($levelRows[$i] as $levelRow):?>

                                        <?php
                                         $userPrestigeSkills = getUserPrestigeSkills($user_class);
                                         if ($userPrestigeSkills['research_cash_unlock'] > 0) {
                                             $levelRow['cost'] = $levelRow['cost'] - ($levelRow['cost'] / 100 * 10);
                                         }
                                         if ($userPrestigeSkills['research_cash_boost_level'] > 0) {
                                             $levelRow['cost'] = $levelRow['cost'] - ($levelRow['cost'] / 100 * (2 * $userPrestigeSkills['research_cash_boost_level']));
                                         }
                                        $bgClass = 'bg-danger';
                                        if (isset($completeUserResearchTypesIndexedOnId[$levelRow['id']])) {
                                            $bgClass = 'bg-success';
                                        }
                                        ?>
                                        <div class="card text-white <?php echo $bgClass ?> mb-3" style="width: 200px;">
                                            <div class="card-header">
                                                <?php echo $levelRow['name'] ?>
                                            </div>
                                            <div class="card-body">
                                                <p class="card-text">
                                                    <?php echo $levelRow['description'] ?><br /><br />
                                                    <strong>Cost:</strong> $<?php echo number_format($levelRow['cost'], 0) ?><br />
                                                    <strong>Length:</strong> <?php echo number_format($levelRow['duration_in_days'], 0) ?> Days
                                                </p>
                                            </div>
                                            <div class="card-footer">
                                                <?php if (!$activeUserResearchType && !isset($completeUserResearchTypesIndexedOnId[$levelRow['id']])): ?>
                                                    <a href="research.php?action=start_research&rid=<?php echo $levelRow['id'] ?>" class="btn btn-primary">Research</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <?php $i++; ?>
                            <?php endwhile; ?>
                        </tr>
                </table>
            </div>

            <br /><hr /><br />

            <h3>Combat Tree</h3>
            <div class="table-container">
                <table class="new_table" id="newtables" style="width:100%;">
                    <tr>
                        <?php
                        $i = 1;
                        while ($i <= 5):
                            ?>
                            <td>
                                <?php foreach ($combatTreeLevelRows[$i] as $combatTreeLevelRow): ?>
                                    <?php
                                    $bgClass = 'bg-danger';
                                    if (isset($completeUserResearchTypesIndexedOnId[$combatTreeLevelRow['id']])) {
                                        $bgClass = 'bg-success';
                                    }
                                    ?>
                                    <div class="card text-white <?php echo $bgClass ?> mb-3" style="width: 200px;">
                                        <div class="card-header">
                                            <?php echo $combatTreeLevelRow['name'] ?>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">
                                                <?php echo $combatTreeLevelRow['description'] ?><br /><br />
                                                <strong>Cost:</strong> $<?php echo number_format($combatTreeLevelRow['cost'], 0) ?><br />
                                                <strong>Length:</strong> <?php echo number_format($combatTreeLevelRow['duration_in_days'], 0) ?> Days
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            <?php if (!$activeUserResearchType && !isset($completeUserResearchTypesIndexedOnId[$combatTreeLevelRow['id']])): ?>
                                                <a href="research.php?action=start_research&rid=<?php echo $combatTreeLevelRow['id'] ?>" class="btn btn-primary">Research</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                            <?php $i++; ?>
                        <?php endwhile; ?>
                    </tr>
                </table>
            </div>

            <h3>Pet Tree</h3>
            <div class="table-container">
                <table class="new_table" id="newtables" style="width:100%;">
                    <tr>
                        <?php
                        $i = 1;
                        while ($i <= 5):
                            ?>
                            <td>
                                <?php foreach ($petTreeLevelRows[$i] as $petTreeLevelRow): ?>
                                    <?php
                                    $bgClass = 'bg-danger';
                                    if (isset($completeUserResearchTypesIndexedOnId[$petTreeLevelRow['id']])) {
                                        $bgClass = 'bg-success';
                                    }
                                    ?>
                                    <div class="card text-white <?php echo $bgClass ?> mb-3" style="width: 200px;">
                                        <div class="card-header">
                                            <?php echo $petTreeLevelRow['name'] ?>
                                        </div>
                                        <div class="card-body">
                                            <p class="card-text">
                                                <?php echo $petTreeLevelRow['description'] ?><br /><br />
                                                <strong>Cost:</strong> $<?php echo number_format($petTreeLevelRow['cost'], 0) ?><br />
                                                <strong>Length:</strong> <?php echo number_format($petTreeLevelRow['duration_in_days'], 0) ?> Days
                                            </p>
                                        </div>
                                        <div class="card-footer">
                                            <?php if (!$activeUserResearchType && !isset($completeUserResearchTypesIndexedOnId[$petTreeLevelRow['id']])): ?>
                                                <a href="research.php?action=start_research&rid=<?php echo $petTreeLevelRow['id'] ?>" class="btn btn-primary">Research</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </td>
                            <?php $i++; ?>
                        <?php endwhile; ?>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php'
?>
