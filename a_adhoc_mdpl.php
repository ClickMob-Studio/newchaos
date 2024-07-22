<?php

include 'header.php';

if ($_GET['wekey'] === 'herewego') {

    // Define Dates
    $day = 12;
    while ($day < 21){
        $startDate = new \DateTime();
        $startDate->setDate(2024, 07, $day);
        $startDate->setTime(00, 00,00);

        $endDate = new \DateTime();
        $endDate->setDate(2024, 07, $day);
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


            $missionPayoutsIndexedOnId[$row['id']] = $row;
            $missionPayoutsIndexedOnId[$row['id']]['points_payout'] = $pointsPayout;
            $missionPayoutsIndexedOnId[$row['id']]['points_cost'] = $pointsCost;
            $missionPayoutsIndexedOnId[$row['id']]['points_profit'] = $pointsProfit;
        }


        $db->query("SELECT * FROM `missions` WHERE `timestamp` BETWEEN " . $startDate->getTimestamp() . " AND " . $endDate->getTimestamp());
        $db->execute();
        $rows = $db->fetch_row();


        $valuesIndexedByUserId = array();
        foreach ($rows as $row) {
            if (!isset($valuesIndexedByUserId[$row['userid']])) {
                $valuesIndexedByUserId[$row['userid']] = array();
                $valuesIndexedByUserId[$row['userid']]['missions_complete'] = 0;
                $valuesIndexedByUserId[$row['userid']]['total_points_earned'] = 0;
                $valuesIndexedByUserId[$row['userid']]['total_profit_earned'] = 0;
            }

            $missionPayouts = $missionPayoutsIndexedOnId[$row['mid']];

            $valuesIndexedByUserId[$row['userid']]['missions_complete'] = $valuesIndexedByUserId[$row['userid']]['missions_complete'] + 1;
            if ($row['crimes'] >= $missionPayouts['crimes']) {
                $valuesIndexedByUserId[$row['userid']]['total_points_earned'] +=  $missionPayouts['payCrimes'];
                $valuesIndexedByUserId[$row['userid']]['total_profit_earned'] +=  ($missionPayouts['payCrimes'] - ($missionPayouts['crimes'] / 10));
            }
            if ($row['mugs'] >= $missionPayouts['mugs']) {
                $valuesIndexedByUserId[$row['userid']]['total_points_earned'] += $missionPayouts['payMugs'];
                $valuesIndexedByUserId[$row['userid']]['total_profit_earned'] += $missionPayouts['payMugs'];
            }
            if ($row['kills'] >= $missionPayouts['kills']) {
                $valuesIndexedByUserId[$row['userid']]['total_points_earned'] += $missionPayouts['payKills'];
                $valuesIndexedByUserId[$row['userid']]['total_profit_earned'] += ($missionPayouts['payKills'] - ($missionPayouts['kills'] * 2));

            }
            if ($row['busts'] >= $missionPayouts['busts']) {
                $valuesIndexedByUserId[$row['userid']]['total_points_earned'] += $missionPayouts['payBusts'];
                $valuesIndexedByUserId[$row['userid']]['total_profit_earned'] += $missionPayouts['payBusts'];
            }
        }

        foreach ($valuesIndexedByUserId as  $userId => $values) {
            $db->query("
          INSERT INTO 
            `mission_daily_payout_logs` (user_id, date, missions_complete, total_points_earned, total_profit_earned) 
          VALUES 
            (" . $userId . ", '" . $startDate->format('d-m-Y') . "',  " . $values['missions_complete'] . ",  " . $values['total_points_earned'] . ",  " . $values['total_profit_earned'] . ")
        ");
            $db->execute();
        }


        $day++;
    }

    echo 'done'; exit;

}