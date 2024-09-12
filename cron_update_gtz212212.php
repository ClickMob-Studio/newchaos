<?php

include_once 'dbcon.php';
include_once 'classes.php';
include 'database/pdo_class.php';

/* Run every day */
if ($_GET['key'] === 'srunit') {
    $db->query("SELECT * FROM gang_territory_zone WHERE owned_by_gang_id > 0");
    $db->execute();
    $gangTerritoryZones = $db->fetch_row();

    foreach ($gangTerritoryZones as $gangTerritoryZone) {
        $ownedByGang = new Gang($gangTerritoryZone['owned_by_gang_id']);

        foreach ($ownedByGang->memberids as $memberid) {
            //  if ($gangTerritoryZone['daily_points_payout'] > 0) {
            //      $db->query("UPDATE grpgusers SET points = points + " . $gangTerritoryZone['daily_points_payout'] . " WHERE id = " . $memberid['id'].' ORDER BY `id` ASC');
            //      $db->execute();

            //      Send_Event($memberid['id'], 'You gained ' . number_format($gangTerritoryZone['daily_points_payout'], 0) . ' points for your gangs protection racket ' . $gangTerritoryZone['name']);
            //  }

            
            if ($gangTerritoryZone['daily_money_payout'] > 0) {
                $us = new User(1);
                $memberid['id'] = 1;
                $bank = $us->bank + $gangTerritoryZone['daily_money_payout'];
                try {
                    // Start a transaction
                    $db->startTrans();
                
                    // Update the bank balance in grpgusers
                    $db->query("UPDATE grpgusers SET bank = ? WHERE id = ?");
                    if (!$db->execute(array($bank, $memberid['id']))) {
                        throw new Exception("Failed to update bank for user ID: " . $memberid['id'] . " - Error: " . implode(", ", $db->errorInfo()));
                    }
                
                    // Insert into the bank_log table
                    $db->query("INSERT INTO bank_log (`userid`, `amount`, `action`, `newbalance`, `timestamp`) VALUES (?, ?, ?, ?, ?)");
                    if (!$db->execute(array($memberid['id'], $gangTerritoryZone['daily_money_payout'], 'mdep', $bank, time()))) {
                        throw new Exception("Failed to insert bank log for user ID: " . $memberid['id'] . " - Error: " . implode(", ", $db->errorInfo()));
                    }
                
                    // Commit the transaction
                    $db->endTrans();
                } catch (Exception $e) {
                    // Roll back the transaction if something went wrong
                    $db->cancelTransaction();
                    error_log($e->getMessage());
                }
               
               

                
                //Send_Event($memberid['id'], 'You gained $' . number_format($gangTerritoryZone['daily_money_payout'], 0) . ' for your gangs protection racket ' . $gangTerritoryZone['name']);
            }

            // if ($gangTerritoryZone['daily_raid_tokens_payout'] > 0) {
            //     $db->query("UPDATE grpgusers SET raidtokens = raidtokens + " . $gangTerritoryZone['daily_raid_tokens_payout'] . " WHERE id = " . $memberid['id']);
            //     $db->execute();

            //     Send_Event($memberid['id'], 'You gained ' . number_format($gangTerritoryZone['daily_raid_tokens_payout'], 0) . ' Raid Tokens for your gangs protection racket ' . $gangTerritoryZone['name']);
            // }
            // if ($gangTerritoryZone['daily_item_payout'] > 0) {
            //     $itemname = Item_Name($gangTerritoryZone['daily_item_payout']);
            //     Give_Item($gangTerritoryZone['daily_item_payout'], $memberid['id'], 1);

            //     Send_Event($memberid['id'], 'You gained 1 x ' . $itemname . ' for your gangs protection racket ' . $gangTerritoryZone['name']);
            // }
        }
    }

}