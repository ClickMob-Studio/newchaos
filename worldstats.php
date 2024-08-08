<?php
include 'header.php';
?>

<h1 class="text-center mb-4">World Stats</h1>
<style>
	.card-body{
		background: #8e8e8e21 !important;
	}
	</style>
<div class="container">
    <?php
    $result = mysql_query("SELECT * FROM `grpgusers` WHERE `rmdays`='0' AND admin <> 1");
    $totalmobsters = mysql_num_rows($result);
    $result2 = mysql_query("SELECT * FROM `grpgusers` WHERE `rmdays`!='0' AND admin <> 1");
    $totalrm = mysql_num_rows($result2);
    $totalall = $totalmobsters + $totalrm;
    $result = mysql_query("SELECT * FROM `grpgusers` WHERE `money` != '0' AND admin <> 1");
    $money = 0;
    while($line = mysql_fetch_array($result)) {
        $money = $money + $line['money'];
    }
    //Total Points Stuff
    $result = mysql_query("SELECT * FROM `grpgusers` WHERE `points` != '0' AND admin <> 1");
    $points = 0;
    while($line = mysql_fetch_array($result)) {
        $points = $points + $line['points'];
    }
    //Total Crimes Stuff
    $result = mysql_query("SELECT * FROM `grpgusers` WHERE `crimesucceeded` != '0' AND admin <> 1");
    $crimes = 0;
    while($line = mysql_fetch_array($result)) {
        $crimes = $crimes + $line['crimesucceeded'];
    }
    //Total Kills Stuff
    $result = mysql_query("SELECT * FROM `grpgusers` WHERE `battlewon` != '0' AND admin <> 1");
    $kills = 0;
    while($line = mysql_fetch_array($result)) {
        $kills = $kills + $line['battlewon'];
    }
    //Total Deaths Stuff
    $result = mysql_query("SELECT * FROM `grpgusers` WHERE `battlelost` != '0' AND admin <> 1");
    $deaths = 0;
    while($line = mysql_fetch_array($result)) {
        $deaths = $deaths + $line['battlelost'];
    }
    //Total Bank Stuff
    $result = mysql_query("SELECT * FROM `grpgusers` WHERE `bank` != '0' AND admin <> 1");
    $bank = 0;
    while($line = mysql_fetch_array($result)) {
        $bank = $bank + $line['bank'];
    }
    //Male Stuff
    $result = mysql_query("SELECT * FROM `grpgusers` WHERE `gender` = 'Male' AND admin <> 1");
    $male = mysql_num_rows($result);
    $malepercent = round(($male / $totalall) * 100);
    //Female Stuff
    $result = mysql_query("SELECT * FROM `grpgusers` WHERE `gender` = 'Female' AND admin <> 1");
    $female = mysql_num_rows($result);
    $femalepercent = round(($female / $totalall) * 100);
    //Gangs Stuff
    $result = mysql_query("SELECT * FROM `gangs`");
    $gangs = mysql_num_rows($result);
    //Total Gang Money Stuff
    $result = mysql_query("SELECT * FROM `gangs` WHERE `moneyvault` != '0'");
    $gangmoney = 0;
    while($line = mysql_fetch_array($result)) {
        $gangmoney = $gangmoney + $line['moneyvault'];
    }
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
                    <p class="card-text">Male: <?php echo prettynum($male)." [".$malepercent."%]"; ?></p>
                    <p class="card-text">Female: <?php echo prettynum($female)." [".$femalepercent."%]"; ?></p>
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
