<?php
require "header.php";

    $db->query("SELECT * FROM gang_territory_zone WHERE owned_by_gang_id > 0");
    $db->execute();
    $gangTerritoryZones = $db->fetch_row();

    foreach ($gangTerritoryZones as $gangTerritoryZone) {
        $ownedByGang = new Gang($gangTerritoryZone['owned_by_gang_id']);

        foreach ($ownedByGang->memberids as $memberid) {
            // if ($gangTerritoryZone['daily_points_payout'] > 0) {
            //     $db->query("UPDATE grpgusers SET points = points + " . $gangTerritoryZone['daily_points_payout'] . " WHERE id = " . $memberid['id']);
            //     $db->execute();

            //     Send_Event($memberid['id'], 'You gained ' . number_format($gangTerritoryZone['daily_points_payout'], 0) . ' points for your gangs protection racket ' . $gangTerritoryZone['name']);
            // }

            
            if ($gangTerritoryZone['daily_money_payout'] > 0) {
                $us = new User($memberid['id']);
                $bank = $us->bank + $gangTerritoryZone['daily_money_payout'];
                $db->query("INSERT INTO bank_log (`userid`, `amount`, `action`, `newbalance`, `timestamp`) VALUES (?, ?, ?, ?, ?)");
                $db->execute(
                    array(
                        $memberid['id'],
                        $gangTerritoryZone['daily_money_payout'],
                        'mdep',
                        $bank,
                        time()
                    )
                );
               
                $db->query("UPDATE grpgusers SET bank = ". $bank . " WHERE id = ". $memberid['id']);
                $db->execute();

                
                Send_Event($memberid['id'], 'You gained $' . number_format($gangTerritoryZone['daily_money_payout'], 0) . ' for your gangs protection racket ' . $gangTerritoryZone['name']);
            }

            // if ($gangTerritoryZone['daily_raid_tokens_payout'] > 0) {
            //     $db->query("UPDATE grpgusers SET raidtokens = raidtokens + " . $gangTerritoryZone['daily_raid_tokens_payout'] . " WHERE id = " . $memberid['id']);
            //     $db->execute();

            //     Send_Event($memberid['id'], 'You gained ' . number_format($gangTerritoryZone['daily_raid_tokens_payout'], 0) . ' Raid Tokens for your gangs protection racket ' . $gangTerritoryZone['name']);
            // }

            // TODO: Daily EXP Payout

        //     if ($gangTerritoryZone['daily_item_payout'] > 0) {
        //         $itemname = Item_Name($gangTerritoryZone['daily_item_payout']);
        //         Give_Item($gangTerritoryZone['daily_item_payout'], $memberid['id'], 1);

        //         Send_Event($memberid['id'], 'You gained 1 x ' . $itemname . ' for your gangs protection racket ' . $gangTerritoryZone['name']);
        //     }
        // }
    }

}