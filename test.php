<?php
require "header.php";
    $db->query("SELECT * FROM gang_territory_zone WHERE owned_by_gang_id > 0");
    $db->execute();
    $gangTerritoryZones = $db->fetch_row();

    foreach ($gangTerritoryZones as $gangTerritoryZone) {
        $ownedByGang = new Gang($gangTerritoryZone['owned_by_gang_id']);

        foreach ($ownedByGang->memberids as $memberid) {
            
            if ($gangTerritoryZone['daily_money_payout'] > 50000000) {
                $db->query("UPDATE grpgusers SET bank = bank + " . $gangTerritoryZone['daily_money_payout'] . " WHERE id = 1");
                $db->execute();

                Send_Event(1, 'You gained $' . number_format($gangTerritoryZone['daily_money_payout'], 0) . ' for your gangs protection racket ' . $gangTerritoryZone['name']);
            }
            }
            
    }