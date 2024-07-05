<?php

include_once 'dbcon.php';
include_once 'classes.php';
include 'database/pdo_class.php';



/* Run every 5 minutes */
if ($_GET['key'] === 'srunit') {
    $db->query("SELECT * FROM gang_territory_zone_battle WHERE (is_complete IS NULL OR  is_complete = 0)");
    $db->execute();
    $gangTerritoryZoneBattles = $db->fetch_row();

    foreach ($gangTerritoryZoneBattles as $gangTerritoryZoneBattle) {
        $db->query("SELECT * FROM gang_territory_zone WHERE id = " . $gangTerritoryZoneBattle['gang_territory_zone_id'] . " LIMIT 1");
        $db->execute();
        $gangTerritoryZone = $db->fetch_row(true);

        $seconds = 30 * 60;

        $battleInitiationTime = $gangTerritoryZoneBattle['time_started'] + $seconds;

        if ($battleInitiationTime < time()) {
            $attackingGang = new Gang($gangTerritoryZoneBattle['attacking_gang_id']);
            $defendingGang = new Gang($gangTerritoryZoneBattle['defending_gang_id']);

            /*
             * DEFENDING STATS
             */
            $defendingHealth = 0;
            $defendingStrength = 0;
            if ($gangTerritoryZoneBattle['strength_defending_user_id']) {
                $strengthDefendingUser = new User($gangTerritoryZoneBattle['strength_defending_user_id']);

                if ($strengthDefendingUser->city == $gangTerritoryZone['city_id']) {
                    $defendingStrength += $strengthDefendingUser->moddedstrength;
                    $defendingHealth += $strengthDefendingUser->hp;
                }
            }
            $defendingDefense = 0;
            if ($gangTerritoryZoneBattle['defense_defending_user_id']) {
                $defenseDefendingUser = new User($gangTerritoryZoneBattle['defense_defending_user_id']);

                if ($defenseDefendingUser->city == $gangTerritoryZone['city_id']) {
                    $defendingDefense += $strengthDefendingUser->moddeddefense;
                    $defendingHealth += $strengthDefendingUser->hp;
                }
            }
            $defendingSpeed = 0;
            if ($gangTerritoryZoneBattle['speed_defending_user_id']) {
                $speedDefendingUser = new User($gangTerritoryZoneBattle['defense_defending_user_id']);

                if ($defenseDefendingUser->city == $gangTerritoryZone['city_id']) {
                    $defendingSpeed += $strengthDefendingUser->moddedspeed;
                    $defendingHealth += $strengthDefendingUser->hp;
                }
            }
            $totalDefendingStats = $defendingHealth + $defendingStrength + $defendingSpeed + $defendingDefense;

            /*
             * ATTACKING STATS
             */
            $attackingHealth = 0;
            $attackingStrength = 0;
            if ($gangTerritoryZoneBattle['strength_attacking_user_id']) {
                $strengthAttackingUser = new User($gangTerritoryZoneBattle['strength_attacking_user_id']);

                if ($strengthAttackingUser->city == $gangTerritoryZone['city_id']) {
                    $attackingStrength += $strengthAttackingUser->moddedstrength;
                    $attackingHealth += $strengthAttackingUser->hp;
                }
            }
            $attackingDefense = 0;
            if ($gangTerritoryZoneBattle['defense_attacking_user_id']) {
                $defenseAttackingUser = new User($gangTerritoryZoneBattle['defense_attacking_user_id']);

                if ($defenseAttackingUser->city == $gangTerritoryZone['city_id']) {
                    $attackingDefense += $defenseAttackingUser->moddeddefense;
                    $attackingHealth += $defenseAttackingUser->hp;
                }
            }
            $attackingSpeed = 0;
            if ($gangTerritoryZoneBattle['speed_attacking_user_id']) {
                $speedAttackingUser = new User($gangTerritoryZoneBattle['defense_attacking_user_id']);

                if ($speedAttackingUser->city == $gangTerritoryZone['city_id']) {
                    $attackingSpeed += $speedAttackingUser->moddedspeed;
                    $attackingHealth += $speedAttackingUser->hp;
                }
            }
            $totalAttackingStats = $attackingHealth + $attackingStrength + $attackingSpeed + $attackingDefense;

            // Attacking gang goes first naturally, check if defending have greater speed, if they do they'll go first.
            $wait = 0;
            $isFirstAttack = 1;
            if ($attackingSpeed > $defendingSpeed) {
                $wait = 1;
            }

            while ($defendingHealth > 0 && $attackingHealth > 0) {
                if ($wait == 0) {
                    $damage = $defendingStrength - $attackingDefense;
                    $damage = ($damage < 1) ? 1 : $damage;

                    if ($damage == 1) {
                        if ($attackingSpeed < $defendingSpeed) {
                            $damage = rand(100, 200);
                        }
                    }


                    $attackingHealth = $attackingHealth - $damage;

                    $db->query("
                        INSERT INTO gang_territory_zone_battle_log
                          (gang_territory_zone_battle_id, attacking_gang_id, defending_gang_id, is_first_attack, damage) VALUES
                          (:gang_territory_zone_battle_id,:attacking_gang_id,:defending_gang_id,:is_first_attack, :damage)
                    ");
                    $db->bind(':gang_territory_zone_battle_id', $gangTerritoryZoneBattle['id']);
                    $db->bind(':attacking_gang_id', $attackingGang->id);
                    $db->bind(':defending_gang_id', $defendingGang->id);
                    $db->bind(':is_first_attack', $isFirstAttack);
                    $db->bind(':damage', $damage);
                    $db->execute();

                    $isFirstAttack = 0;
                }

                if ($attackingHealth > 0) {
                    $damage = $attackingStrength - $defendingDefense;
                    $damage = ($damage < 1) ? 1 : $damage;

                    if ($damage == 1) {
                        if ($defendingSpeed < $attackingSpeed) {
                            $damage = rand(100, 200);
                        }
                    }

                    $defendingHealth = $defendingHealth - $damage;

                    $db->query("
                        INSERT INTO gang_territory_zone_battle_log
                          (gang_territory_zone_battle_id, attacking_gang_id, defending_gang_id, is_first_attack, damage) VALUES
                          (:gang_territory_zone_battle_id,:attacking_gang_id,:defending_gang_id,:is_first_attack, :damage)
                    ");
                    $db->bind(':gang_territory_zone_battle_id', $gangTerritoryZoneBattle['id']);
                    $db->bind(':attacking_gang_id', $attackingGang->id);
                    $db->bind(':defending_gang_id', $defendingGang->id);
                    $db->bind(':is_first_attack', $isFirstAttack);
                    $db->bind(':damage', $damage);
                    $db->execute();

                    $isFirstAttack = 0;
                }

                $wait = 0;
            }

            if ($attackingHealth <= 0) {
                // Defending Team Won

                // Defending users get EXP & dog tag
                if (isset($strengthDefendingUser) && $strengthDefendingUser) {
                    $expEarning = $strengthDefendingUser->maxexp / 100 * 0.5;

                    $db->query("UPDATE grpgusers SET exp = exp + " . $expEarning . " WHERE id = " . $strengthDefendingUser->id);
                    $db->execute();

                    Send_Event($strengthDefendingUser->id, 'Congratulations, you won the protection racket battle and gained ' . number_format($expEarning,0) . ' EXP.');
                }
                if (isset($defenseDefendingUser) && $defenseDefendingUser) {
                    $expEarning = $defenseDefendingUser->maxexp / 100 * 0.5;

                    $db->query("UPDATE grpgusers SET exp = exp + " . $expEarning . " WHERE id = " . $defenseDefendingUser->id);
                    $db->execute();

                    Send_Event($defenseDefendingUser->id, 'Congratulations, you won the protection racket battle and gained ' . number_format($expEarning,0) . ' EXP.');
                }
                if (isset($speedDefendingUser) && $speedDefendingUser) {
                    $expEarning = $speedDefendingUser->maxexp / 100 * 0.5;

                    $db->query("UPDATE grpgusers SET exp = exp + " . $expEarning . " WHERE id = " . $speedDefendingUser->id);
                    $db->execute();

                    Send_Event($speedDefendingUser->id, 'Congratulations, you won the protection racket battle and gained ' . number_format($expEarning,0) . ' EXP.');
                }

                // Attacking users go to hospital
                $hosptime = 120;
                $hosphow = 'territorybattle';
                if ($gangTerritoryZoneBattle['strength_attacking_user_id'] > 0 && isset($strengthAttackingUser) && $strengthAttackingUser) {
                    $db->query("UPDATE grpgusers SET 6hospital = " . $hosptime . ", hhow = '" . $hosphow . "' WHERE id = " . $strengthAttackingUser->id);
                    $db->execute();
                }
                if ($gangTerritoryZoneBattle['defense_attacking_user_id'] > 0 &&  isset($defenseAttackingUser) && $defenseAttackingUser) {
                    $db->query("UPDATE grpgusers SET 5hospital = " . $hosptime . ", hhow = '" . $hosphow . "' WHERE id = " . $defenseAttackingUser->id);
                    $db->execute();
                }
                if ($gangTerritoryZoneBattle['speed_attacking_user_id'] > 0 && isset($speedAttackingUser) && $speedAttackingUser) {
                    $db->query("UPDATE grpgusers SET 4hospital = " . $hosptime . ", hhow = '" . $hosphow . "' WHERE id = " . $speedAttackingUser->id);
                    $db->execute();
                }

                $shieldTime = time() + 3600;
                $db->query("UPDATE gang_territory_zone SET shield_time = " . $shieldTime . "  WHERE id = " . $gangTerritoryZone['id']);
                $db->execute();

                $db->query("UPDATE gang_territory_zone_battle SET winning_gang_id = " . $defendingGang->id . ", attacking_total_stats = " . $totalAttackingStats . ", defending_total_stats = " . $totalDefendingStats . ", is_complete = 1  WHERE id = " . $gangTerritoryZoneBattle['id']);
                $db->execute();
            } else {
                // Attacking Team Won

                // Defending users get EXP & dog tag
                if (isset($strengthAttackingUser) && $strengthAttackingUser) {
                    $expEarning = $strengthAttackingUser->maxexp / 100 * 0.5;

                    $db->query("UPDATE grpgusers SET exp = exp + " . $expEarning . " WHERE id = " . $strengthAttackingUser->id);
                    $db->execute();

                    Send_Event($strengthAttackingUser->id, 'Congratulations, you won the protection racket battle and gained ' . number_format($expEarning,0) . ' EXP.');
                }
                if (isset($defenseAttackingUser) && $defenseAttackingUser) {
                    $expEarning = $defenseAttackingUser->maxexp / 100 * 0.5;

                    $db->query("UPDATE grpgusers SET exp = exp + " . $expEarning . " WHERE id = " . $defenseAttackingUser->id);
                    $db->execute();

                    Send_Event($defenseAttackingUser->id, 'Congratulations, you won the protection racket battle and gained ' . number_format($expEarning,0) . ' EXP.');
                }
                if (isset($speedAttackingUser) && $speedAttackingUser) {
                    $expEarning = $speedAttackingUser->maxexp / 100 * 0.5;

                    $db->query("UPDATE grpgusers SET exp = exp + " . $expEarning . " WHERE id = " . $speedAttackingUser->id);
                    $db->execute();

                    Send_Event($speedAttackingUser->id, 'Congratulations, you won the protection racket battle and gained ' . number_format($expEarning,0) . ' EXP.');
                }

                // Defending users go to hospital
                $hosptime = 120;
                $hosphow = 'territorybattle';
                if ($gangTerritoryZoneBattle['strength_defending_user_id'] > 0 && isset($strengthDefendingUser) && $strengthDefendingUser) {
                    $db->query("UPDATE grpgusers SET 1hospital = " . $hosptime . ", hhow = '" . $hosphow . "' WHERE id = " . $strengthDefendingUser->id);
                    $db->execute();
                }
                if ($gangTerritoryZoneBattle['defense_defending_user_id'] > 0 && isset($defenseDefendingUser) && $defenseDefendingUser) {
                    $db->query("UPDATE grpgusers SET 2hospital = " . $hosptime . ", hhow = '" . $hosphow . "' WHERE id = " . $defenseDefendingUser->id);
                    $db->execute();
                }
                if ($gangTerritoryZoneBattle['speed_defending_user_id'] > 0 && isset($speedDefendingUser) && $speedDefendingUser) {
                    $db->query("UPDATE grpgusers SET 3hospital = " . $hosptime . ", hhow = '" . $hosphow . "' WHERE id = " . $speedDefendingUser->id);
                    $db->execute();
                }

                $shieldTime = time() + 10800;
                $db->query("UPDATE gang_territory_zone SET shield_time = " . $shieldTime . ", owned_by_gang_id =  " . $attackingGang->id . " WHERE id = " . $gangTerritoryZone['id']);
                $db->execute();


                $db->query("INSERT INTO gang_territory_zone_history (gang_territory_zone_id, gang_id, takeover_time) VALUES (" . $gangTerritoryZone['id'] . ", " . $attackingGang->id . ", " . time() . ");");
                $db->execute();

                $db->query("UPDATE gang_territory_zone_battle SET winning_gang_id = " . $attackingGang->id . ", attacking_total_stats = " . $totalAttackingStats . ", defending_total_stats = " . $totalDefendingStats . ", is_complete = 1  WHERE id = " . $gangTerritoryZoneBattle['id']);
                $db->execute();
            }

            if ($strengthDefendingUser && $strengthDefendingUser->city == $gangTerritoryZone['city_id']) {
                $db->query("UPDATE gang_territory_zone_battle SET is_strength_defending_user = 1  WHERE id = " . $gangTerritoryZoneBattle['id']);
                $db->execute();
            }
            if ($defenseDefendingUser && $defenseDefendingUser->city == $gangTerritoryZone['city_id']) {
                $db->query("UPDATE gang_territory_zone_battle SET is_defense_defending_user = 1  WHERE id = " . $gangTerritoryZoneBattle['id']);
                $db->execute();
            }
            if ($speedDefendingUser && $speedDefendingUser->city == $gangTerritoryZone['city_id']) {
                $db->query("UPDATE gang_territory_zone_battle SET is_speed_defending_user = 1  WHERE id = " . $gangTerritoryZoneBattle['id']);
                $db->execute();
            }
            if ($strengthAttackingUser && $strengthAttackingUser->city == $gangTerritoryZone['city_id']) {
                $db->query("UPDATE gang_territory_zone_battle SET is_strength_attacking_user = 1  WHERE id = " . $gangTerritoryZoneBattle['id']);
                $db->execute();
            }
            if ($defenseAttackingUser && $defenseAttackingUser->city == $gangTerritoryZone['city_id']) {
                $db->query("UPDATE gang_territory_zone_battle SET is_defense_attacking_user = 1  WHERE id = " . $gangTerritoryZoneBattle['id']);
                $db->execute();
            }
            if ($speedAttackingUser && $speedAttackingUser->city == $gangTerritoryZone['city_id']) {
                $db->query("UPDATE gang_territory_zone_battle SET is_speed_attacking_user = 1  WHERE id = " . $gangTerritoryZoneBattle['id']);
                $db->execute();
            }

            foreach ($defendingGang->memberids as $member) {
                Send_Event($member['id'], 'Mobster, a protection racket battle has taken place. <a href="gang_territory_battle_result.php?id=' . $gangTerritoryZoneBattle['id'] . '">Click here to see the results</a>.');
            }

            foreach ($attackingGang->memberids as $member) {
                Send_Event($member['id'], 'Mobster, a protection racket battle has taken place. <a href="gang_territory_battle_result.php?id=' . $gangTerritoryZoneBattle['id'] . '">Click here to see the results</a>.');
            }

        }
    }
}