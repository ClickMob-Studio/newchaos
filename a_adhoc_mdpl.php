<?php

include 'header.php';

if ($_GET['wekey'] === 'herewego') {
    // Define Dates
    $startDate = new \DateTime();
    $startDate->setDate(2024, 04, 01);
    $startDate->setTime(00, 00,00);

    $endDate = new \DateTime();
    $endDate->setDate(2024, 04, 01);
    $endDate->setTime(23, 59,59);


    // Retrieve the mission
    $db->query("SELECT * FROM `mission`");
    $db->execute();
    $rows = $db->fetch_row();

    // Build an array that holds the payouts for the mission, and profit, indexed on the mission ID for use later
    $missionPayoutsIndexedOnId = array();
    foreach ($rows as $row) {
        $pointsPayout = $row['payCrimes'] + $row['payKills'] + $row['payMugs'] + $row['payBusts'];
        $pointsCost = 0;
        if ($row['crimes'] > 0) {
            $pointsCost = $pointsCost + ($row['crimes'] / 10);
        }
        if ($row['mugs'] > 0) {
            $pointsCost = $pointsCost + $row['mugs'];
        }
        if ($row['kills'] > 0) {
            $pointsCost = $pointsCost + ($row['kills'] * 2);
        }

        $pointsProfit = $pointsPayout - $pointsCost;


        $missionPayoutsIndexedOnId[$row['id']] = array(
            'points_payout' => $pointsPayout,
            'points_cost' => $pointsCost,
            'points_profit' => $pointsProfit
        );
        var_dump($missionPayoutsIndexedOnId); exit;
    }


    $db->query("SELECT * FROM `missions` WHERE `timestamp` BETWEEN " . $startDate->getTimestamp() . " AND " . $endDate->getTimestamp() . " AND `completed` = 'successful'");
    $db->execute();
    $rows = $db->fetch_row();


    $valuesIndexedByUserId = array();
    foreach ($rows as $row) {

    }

}