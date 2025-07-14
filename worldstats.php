<?php
include 'header.php';
?>

<h1 class="text-center mb-4">World Stats</h1>
<style>
    .card-body {
        background: #8e8e8e21 !important;
    }
</style>
<div class="container">
    <?php
    $db->query("
    SELECT
        COUNT(*) AS totalall,
        COUNT(CASE WHEN rmdays = '0' THEN 1 END) AS totalmobsters,
        COUNT(CASE WHEN rmdays != '0' THEN 1 END) AS totalrm,
        SUM(money) AS total_money,
        SUM(points) AS total_points,
        SUM(crimesucceeded) AS total_crimes,
        SUM(battlewon) AS total_kills,
        SUM(battlelost) AS total_deaths,
        SUM(bank) AS total_bank,
        COUNT(CASE WHEN gender = 'Male' THEN 1 END) AS total_male,
        COUNT(CASE WHEN gender = 'Female' THEN 1 END) AS total_female
    FROM grpgusers
    WHERE admin <> 1
");
    $stats = $db->fetch_row(true);

    $totalall = (int) $stats['totalall'];
    $totalmobsters = (int) $stats['totalmobsters'];
    $totalrm = (int) $stats['totalrm'];
    $money = (int) $stats['total_money'];
    $points = (int) $stats['total_points'];
    $crimes = (int) $stats['total_crimes'];
    $kills = (int) $stats['total_kills'];
    $deaths = (int) $stats['total_deaths'];
    $bank = (int) $stats['total_bank'];
    $male = (int) $stats['total_male'];
    $female = (int) $stats['total_female'];

    $malepercent = $totalall > 0 ? round(($male / $totalall) * 100) : 0;
    $femalepercent = $totalall > 0 ? round(($female / $totalall) * 100) : 0;

    // Gangs Stuff
    $db->query("SELECT COUNT(*) as total_gangs FROM gangs");
    $gangs = (int) $db->fetch_row(true)['total_gangs'];

    // Total money in gang vaults
    $db->query("SELECT SUM(moneyvault) as total_gang_money FROM gangs WHERE moneyvault != 0");
    $gangmoney = (int) $db->fetch_row(true)['total_gang_money'];
    ?>

    <div class="row row-cols-1 row-cols-md-2 g-4">
        <div class="col">
            <div class="card">
                <div class="card-header">Mobsters</div>
                <div class="card-body">
                    <h5 class="card-title">Mobsters: <?php echo prettynum($totalmobsters) ?></h5>
                    <p class="card-text">Respected Mobsters: <?php echo prettynum($totalrm) ?></p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">Demographics</div>
                <div class="card-body">
                    <h5 class="card-title">Gender Distribution</h5>
                    <p class="card-text">Male: <?php echo prettynum($male) . " [" . $malepercent . "%]"; ?></p>
                    <p class="card-text">Female: <?php echo prettynum($female) . " [" . $femalepercent . "%]"; ?></p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">Economics</div>
                <div class="card-body">
                    <h5 class="card-title">Financial Overview</h5>
                    <p class="card-text">Total Money: $<?php echo prettynum($money) ?></p>
                    <p class="card-text">Total Bank: $<?php echo prettynum($bank) ?></p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">Crime and Battle Stats</div>
                <div class="card-body">
                    <h5 class="card-title">Detailed Statistics</h5>
                    <p class="card-text">Points: <?php echo prettynum($points) ?></p>
                    <p class="card-text">Crimes: <?php echo prettynum($crimes) ?></p>
                    <p class="card-text">Total Kills: <?php echo prettynum($kills) ?></p>
                    <p class="card-text">Total Deaths: <?php echo prettynum($deaths) ?></p>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card">
                <div class="card-header">Gangs</div>
                <div class="card-body">
                    <h5 class="card-title">Gang Details</h5>
                    <p class="card-text">Gangs: <?php echo prettynum($gangs) ?></p>
                    <p class="card-text">Gang Money: $<?php echo prettynum($gangmoney) ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include 'footer.php';
?>