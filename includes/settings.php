<?php
error_reporting(1);

// Fetch configuration settings from the database
// $configuration_query = $pdo->prepare("SELECT Xkey AS cfgKey, Xvalue AS cfgValue FROM " . DB_SETTINGS);
// $configuration_query->execute();

// while ($configuration = $configuration_query->fetch(PDO::FETCH_ASSOC)) {
//     // Define constants from the configuration settings
//     define($configuration['cfgKey'], stripslashes($configuration['cfgValue']));
// }

// Adjust stakes according to admin settings
$smallbetfunc = 0;

if (STAKESIZE == 'tiny') {
    $smallbetfunc = 1;
} elseif (STAKESIZE == 'low') {
    $smallbetfunc = 2;
} elseif (STAKESIZE == 'med') {
    $smallbetfunc = 3;
}
?>
