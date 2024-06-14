<?php

final class AttackJoin extends AttackCommon
{
    public function AttackJoin($attackingUser, $targetUser, &$attacks = null, &$hitdetails = null, $jointAttackers = [])
    {
        try {
            $isRiot = ((float) Variable::GetValue('riotStarted') > time());

            $this->Validation($attackingUser, $targetUser, $jointAttackers);
        } catch (Exception $e) {
            throw $e;
        }

        if ($attacks === null) {
            $attacks = [];
        }

        if ($hitdetails === null) {
            $hitdetails = [];
        }

        $jointAttack = false;

        $yourhp = $attackingUser->hp;

        $level = $attackingUser->level;

        $strength = $attackingUser->GetModdedStrength();

        $speed = $attackingUser->GetModdedSpeed();

        $defense = $attackingUser->GetModdedDefense();

        $attackerIds = '';

        if (!empty($jointAttackers) && is_array($jointAttackers) && count($jointAttackers) >= 1) {
            $jointAttack = true;

            $attackerIds = $attackingUser->id;

            $attackerNames = $attackingUser->formattedname;

            if (10 <= Gang::CountJointAttacks($attackingUser->gang)) {
                throw new SoftException(sprintf(JOINT_ATTACK_GANG_LIMIT_ERR, 10));
            }
            if (5 <= User::CountJointAttacks($targetUser->id)) {
                throw new SoftException(sprintf(JOINT_ATTACK_USER_LIMIT_ERR, 5));
            }
            //PS bribe : Cost 10k per target level + 210k per target security level

            $bribe = $targetUser->securityLevel * 2100000 + $targetUser->level * 10000;

            if ($attackingUser->bank < $bribe) {
                throw new SoftException(JOINT_ATTACK_UNABLE_BRIBE);
            }
            if ($bribe <= $attackingUser->money) {
                if ($attackingUser->RemoveFromAttribute('money', $bribe)) {
                    $bribe = 0;
                }
            } else {
                $money = $attackingUser->money;

                if ($attackingUser->RemoveFromAttribute('money', $money)) {
                    $bribe = $bribe - $money;
                }
            }

            if ($bribe > 0 && !$attackingUser->RemoveFromAttribute('bank', $bribe)) {
                throw new SoftException(JOINT_ATTACK_UNABLE_BRIBE);
            }
            //Combine all member stats

            foreach ($jointAttackers as $member) {
                if ($member == $attackingUser->id) {
                    continue;
                }

                $userObj = UserFactory::getInstance()->getUser($member);

                if (Utility::GetPercent($userObj->energy, $userObj->GetMaxEnergy()) < 100) {
                    $attacks[] = sprintf(JOINT_ATTACK_NOT_ENERGY, $userObj->formattedname) . '<br>';

                    continue;
                }

                if ($userObj->IsInJail()) {
                    $attacks[] = sprintf(JOINT_ATTACK_IN_JAIL, $userObj->formattedname) . '<br>';

                    continue;
                }

                if ($userObj->IsInHospital()) {
                    $attacks[] = sprintf(JOINT_ATTACK_IN_HOSPITAL, $userObj->formattedname) . '<br>';

                    continue;
                }

                if (!$userObj->AvailableForJointAttack()) {
                    $attacks[] = sprintf(JOINT_ATTACK_NOT_AVAIL, $userObj->formattedname) . '<br>';

                    continue;
                }

                if (((time() - $userObj->lastactive) / 60) > 15) {
                    $attacks[] = sprintf(JOINT_ATTACK_NOT_ONLINE, $userObj->formattedname) . '<br>';

                    continue;
                }

                $yourhp += $userObj->hp;

                $level += $userObj->level;

                $strength += $userObj->GetModdedStrength();

                $speed += $userObj->GetModdedSpeed();

                $defense += $userObj->GetModdedDefense();

                $attackerIds .= ',' . $userObj->id;

                $attackerNames .= ',' . $userObj->formattedname;

                $userObj->__destruct();     //destroy the user object
            }
        }

        $statsFlag = [
            'str' => false,

            'def' => false,

            'spd' => false,
        ];

        $inithp = $yourhp;

        $theirhp = $targetUser->hp;

        if ($attackingUser->id == 49419) {
            $wait = 1;
        } elseif ($targetUser->id == 49419) {
            $wait = 0;
        } else {
            if ($speed > $targetUser->GetModdedSpeed()) {
                if ($jointAttack) {
                    $attacks[] = sprintf(ATK_OPPONENT_SPEED_JOINT_1, Utility::GetStatsAdj(Utility::GetPercDiff($speed, $targetUser->GetModdedSpeed()))) . '<br>';
                } else {
                    $attacks[] = sprintf(ATK_OPPONENT_SPEED_1, Utility::GetStatsAdj(Utility::GetPercDiff($speed, $targetUser->GetModdedSpeed()))) . '<br>';
                }

                $wait = 1;
            } else {
                if ($jointAttack) {
                    $attacks[] = sprintf(ATK_OPPONENT_SPEED_JOINT_2, Utility::GetStatsAdj(Utility::GetPercDiff($speed, $targetUser->GetModdedSpeed()))) . '<br>';
                } else {
                    $attacks[] = sprintf(ATK_OPPONENT_SPEED_2, Utility::GetStatsAdj(Utility::GetPercDiff($speed, $targetUser->GetModdedSpeed()))) . '<br>';
                }

                $wait = 0;
            }

            $statsFlag['spd'] = true;
        }

        $gangexpwon = 0;

        $winner = 0;

        while ($yourhp > 0 && $theirhp > 0) {
            $damage = $targetUser->GetModdedStrength() - $defense;

            $damage = ($damage < 1) ? 1 : $damage;

            $damage = rand(1, $damage);

            if ($damage == 1) {
                if (($targetUser->GetModdedSpeed()) < 100) {
                    $damage = rand(1, 3);
                } elseif (($targetUser->GetModdedSpeed()) < 500) {
                    $damage = rand(2, 6);
                } elseif (($targetUser->GetModdedSpeed()) < 2500) {
                    $damage = rand(5, 25);
                } elseif (($targetUser->GetModdedSpeed()) < 25000) {
                    $damage = rand(25, 100);
                } else {
                    $damage = rand(100, 200);
                }
            }

            $critical_attack = (2 >= rand(1, 100)); //calculate critical hit. 2% probablity

            if ($critical_attack) { //if critical attack then multiply damage by 2
                $damage = $damage * 2;
            }

            if ($wait == 0) {
                $yourhp = $yourhp - $damage;

                $attackHitStr = $jointAttack ? ATK_OPPONENT_HIT_JOINT : ATK_OPPONENT_HIT;

                if ($critical_attack) {
                    $attackStr = ATK_CRITICAL_HIT . '&nbsp;<font color="red">' . sprintf($attackHitStr, $targetUser->formattedname, number_format($damage)) . '</font> ';
                } else {
                    $attackStr = sprintf($attackHitStr, $targetUser->formattedname, number_format($damage)) . ' ';
                }

                if ($statsFlag['str'] === false) {
                    $attackStr .= sprintf(ATK_WITH_STRENGTH, Utility::GetStatsAdj(Utility::GetPercDiff($strength, $targetUser->GetModdedStrength()))) . ' ';

                    $statsFlag['str'] = true;
                }

                if ($targetUser->HasEquippedWeapon() === true) {
                    $attackStr .= sprintf(ATK_USING_WEAPON, $targetUser->GetWeapon()->itemname);
                } else {
                    $attackStr .= sprintf(ATK_USING_WEAPON, 'fists');
                }

                $attackStr .= '<br>';

                $attacks[] = $attackStr;
            } else {
                $wait = 0;
            }

            if ($yourhp > 0) {
                $damage = $strength - $targetUser->GetModdedDefense();

                $damage = ($damage < 1) ? 1 : $damage;

                $damage = rand(1, $damage);

                if ($damage == 1) {
                    if (($speed) < 100) {
                        $damage = rand(1, 3);
                    } elseif (($speed) < 500) {
                        $damage = rand(2, 6);
                    } elseif (($speed) < 2500) {
                        $damage = rand(5, 25);
                    } elseif (($speed) < 25000) {
                        $damage = rand(25, 100);
                    } else {
                        $damage = rand(100, 200);
                    }
                }

                if ($critical_attack) { //if critical attack then multiply damage by 2
                    $damage = $damage * 2;
                }

                $theirhp = $theirhp - $damage;

                if ($attackingUser->HasEquippedWeapon() === true) {
                    $weapon = $attackingUser->GetWeapon()->itemname;
                } else {
                    $weapon = 'fists';
                }

                if ($critical_attack && $jointAttack) {
                    $attackStr = ATK_CRITICAL_HIT . '&nbsp;<font color="red">' . sprintf(ATK_YOUR_HIT_JOINT, $targetUser->formattedname, number_format($damage)) . '</font> ';
                } elseif ($jointAttack) {
                    $attackStr = sprintf(ATK_YOUR_HIT_JOINT, $targetUser->formattedname, number_format($damage)) . ' ';
                } elseif ($critical_attack && $jointAttack) {
                    $attackStr = ATK_CRITICAL_HIT . '&nbsp;<font color="red">' . sprintf(ATK_YOUR_HIT, $targetUser->formattedname, number_format($damage), $weapon) . '</font> ';
                } else {
                    $attackStr = sprintf(ATK_YOUR_HIT, $targetUser->formattedname, number_format($damage), $weapon) . ' ';
                }

                if ($statsFlag['def'] === false && !$jointAttack) {
                    if ($strength >= $targetUser->GetModdedDefense()) {
                        $attackStr .= sprintf(ATK_YOUR_HIT_MODED_STRENGTH, Utility::GetStatsAdj(Utility::GetPercDiff($strength, $targetUser->GetModdedDefense())));
                    } else {
                        $attackStr .= sprintf(ATK_YOUR_HIT_NORMAL_STRENGTH, Utility::GetStatsAdj(Utility::GetPercDiff($strength, $targetUser->GetModdedDefense())));
                    }

                    $statsFlag['def'] = true;
                }

                $attackStr .= '<br>';

                $attacks[] = $attackStr;
            }
        }

        $time = time();

        $newenergy = (int) ($attackingUser->energy - floor($attackingUser->GetMaxEnergy() * .25));

        if ($newenergy < 0) {
            $newenergy = 0;
        }

        if ($theirhp <= 0) {
            $winner = $attackingUser->id;

            if (time() - $targetUser->lastactive < 900) {
                HatePoints::AddPoint($attackingUser, HatePoints::TASK_ONLINE_ATTACK, true, $targetUser);
            } else {
                HatePoints::AddPoint($attackingUser, HatePoints::TASK_OFFLINE_ATTACK, true, $targetUser);
            }
        } elseif ($yourhp <= 0) {
            $winner = $targetUser->id;

            if (time() - $targetUser->lastactive < 900) {
                HatePoints::AddPoint($attackingUser, HatePoints::TASK_ONLINE_ATTACK, false);
            } else {
                HatePoints::AddPoint($attackingUser, HatePoints::TASK_OFFLINE_ATTACK, false);
            }
        }

        // We handle gang wars

        if ($attackingUser->IsAtWarWith($targetUser) && !$jointAttack) {
            try {
                if ($winner == $attackingUser->id) {
                    GangWarAttack::Add($attackingUser, $targetUser, $attackingUser, $targetUser);
                } else {
                    GangWarAttack::Add($attackingUser, $targetUser, $targetUser, $attackingUser);
                }
            } catch (SuccessResult $s) {
                echo HTML::ShowSuccessMessage($s->getMessage());
            } catch (FailedResult $f) {
                echo HTML::ShowFailedMessage($f->getMessage());
            }
        }

        if ($theirhp <= 0) {
            $theirhp = 0;

            $moneywon = (float) floor($targetUser->money / 10);

            if ($targetUser->level >= $level) {
                $expwon = 150 - (50 * ($level - $targetUser->level));
            } else {
                $expwon = 150 - (10 * ($level - $targetUser->level));
            }

            $expwon = ($expwon < 0) ? 0 : $expwon;

            $expwon = (int) floor($expwon * $attackingUser->GetExpMultiplier());

            $expwon = (int) floor($expwon * $attackingUser->XGetExpMultiplier());

            $gangmoney = 0;

            $newmoney = (float) $moneywon;

            $targetUser->RemoveFromAttribute('money', $newmoney);

            if ($jointAttack) {
                $gangmoney = $moneywon;

                $newmoney = $moneywon;

                $attackingUser->GetGang()->AddToAttribute('vault', $gangmoney);
            } elseif ($attackingUser->IsInAGang() && $attackingUser->GetGang()->gangtax != 0) {
                //Handle gang tax

                $gangmoney = (int) floor($moneywon * ($attackingUser->GetGang()->gangtax / 100));

                $newmoney = (int) floor($moneywon * ((100 - $attackingUser->GetGang()->gangtax) / 100));

                $attackingUser->GetGang()->AddToAttribute('vault', $gangmoney);
            }

            $targetUser->ForceRemoveFromAttribute('battlemoney', $moneywon);

            $targetUser->AddToAttribute('battlelost', 1);

            $targetUser->SetAttribute('hwhoID', $attackingUser->id);

            $targetUser->SetAttribute('hhow', 'wasattacked');

            $targetUser->SetAttribute('hwhen', date('g:i:sa', time()));

            if ($jointAttack) {
                $hospDuration = 30;
            } else {
                $hospDuration = (time() - $targetUser->lastactive < 900) ? 10 : 20;
            }

            $hospDuration += $attackingUser->GetHospitalDurationBonus();

            $hitdetails['hospitalTime'] = $hospDuration; // Added by for task Hitman

            $targetUser->SetAttribute('hospital', time() + 60 * $hospDuration);

            $attackerIdsArr = explode(',', $attackerIds);

            foreach ($attackerIdsArr as $attackerId) {
                User::SNotify($attackerId, sprintf('%s made a joint attack on %s that ended up in Hospital for %s minutes', addslashes($attackerNames), addslashes($targetUser->formattedname), $hospDuration), COM_ATTACK);
            }

            if (isset($hitdetails['thiswasahit'])) {
                $msg = HITMAN_ATTACK;
            } else {
                $msg = '';
            }

            if ($jointAttack) {
                $targetUser->Notify(sprintf(ATK_YOU_HOSPITALIZED_BY_USER, addslashes($attackerNames), $hospDuration, $moneywon, $msg), COM_ATTACK);
            } else {
                $targetUser->Notify(sprintf(ATK_YOU_HOSPITALIZED_BY_USER, '<a href="profiles.php?id=' . $attackingUser->id . '">' . $attackingUser->username . '</a>', $hospDuration, $moneywon, $msg), COM_ATTACK);
            }

            if ($jointAttack) {
                DBi::$conn->query('UPDATE grpgusers SET energy = 0, battlewon = battlewon+1 WHERE id IN (' . $attackerIds . ')');

                if ($inithp > $yourhp) {
                    $result = DBi::$conn->query('SELECT id, hp FROM grpgusers WHERE id IN (' . $attackerIds . ') ORDER BY hp');

                    $totalhp = $inithp - $yourhp;

                    while (($attacker = mysqli_fetch_object($result)) && $totalhp > 0) {
                        if ($totalhp >= $attacker->hp) {
                            DBi::$conn->query('UPDATE grpgusers SET `hp` = 0, hwhoID=\'' . $targetUser->id . '\', hhow=\'attacked\', hwhen=\'' . date('g:i:sa', time()) . '\', `hospital`=\'' . (time() + 60 * 20) . '\' WHERE id = \'' . $attacker->id . '\'');

                            User::SNotify($attacker->id, sprintf(ATK_YOU_JOINT_ATTACKED_AND_HOSPITALIZED, ' <a href="profiles.php?id=' . $targetUser->id . '">' . $targetUser->username . '</a>', $moneywon, ' <a href="profiles.php?id=' . $attackingUser->id . '">' . $attackingUser->username . '</a>', $msg), COM_ATTACK);
                        } else {
                            DBi::$conn->query('UPDATE grpgusers SET `hp`= `hp` - ' . $totalhp . ' WHERE id = \'' . $attacker->id . '\'');
                        }

                        $totalhp -= $attacker->hp;
                    }
                }
            } else {
                $attackingUser->AddToAttribute('exp', $expwon);

                $attackingUser->AddToAttribute('battlewon', 1);

                $attackingUser->AddToAttribute('battlemoney', $moneywon);

                $attackingUser->AddToAttribute('money', $newmoney);

                $attackingUser->SetAttribute('energy', $newenergy);

                $attackingUser->SetAttribute('hp', $yourhp);
            }

            $targetUser->SetAttribute('hp', $theirhp);

            //give gang exp

            if ($attackingUser->IsInAGang()) {
                if ($targetUser->IsInAGang() || $jointAttack) {
                    $gangexpwon = $expwon;

                    Logs::sAddGangAtkLog($time, $targetUser->gang, $attackingUser->id, $targetUser, $winner, $attackingUser->gang, $gangexpwon, $gangmoney, $expwon, $moneywon, $attackerIds);
                } else {
                    $gangexpwon = (int) floor($expwon / 2);

                    Logs::sAddGangAtkLog($time, 'NULL', $attackingUser->id, $targetUser, $winner, $attackingUser->gang, $gangexpwon, $gangmoney, $expwon, $moneywon);
                }

                $newgangexp = $gangexpwon;

                $attackingUser->GetGang()->AddToAttribute('exp', $newgangexp);

                if ($jointAttack) {
                    throw new SuccessResult(sprintf(ATK_HOSPITALIZED_JOINT_1, $targetUser->formattedname, $gangexpwon, $gangmoney, $targetUser->formattedname));
                }
                throw new SuccessResult(sprintf(ATK_HOSPITALIZED_1, $targetUser->formattedname, $expwon, $moneywon, $gangmoney, $targetUser->formattedname, $gangexpwon));
            } elseif ($targetUser->IsInAGang()) {
                Logs::sAddGangAtkLog($time, $targetUser->gang, $attackingUser->id, $targetUser, $winner, 'NULL', $gangexpwon, $gangmoney, $expwon, $moneywon);
            } else {
                Logs::sAddGangAtkLog($time, 'NULL', $attackingUser->id, $targetUser, $winner, 'NULL', $gangexpwon, $gangmoney, $expwon, $moneywon);
            }

            throw new SuccessResult(sprintf(ATK_HOSPITALIZED_2, $targetUser->formattedname, $expwon, $moneywon, $targetUser->formattedname));
        } elseif ($yourhp <= 0) {
            // Defender won

            $yourhp = 0;

            $moneywon = (float) floor($attackingUser->money / 10);

            if ($level >= $targetUser->level) {
                $expwon = 150 - (50 * ($targetUser->level - $level));
            } else {
                $expwon = 150 - (10 * ($targetUser->level - $level));
            }

            $expwon = ($expwon < 0) ? 0 : $expwon;

            $expwon = (int) floor($expwon * $targetUser->GetExpMultiplier());

            $expwon = (int) floor($expwon * $targetUser->XGetExpMultiplier());

            $newmoney = (float) $moneywon;

            $gangmoney = 0;

            $attackingUser->RemoveFromAttribute('money', $newmoney);

            if ($targetUser->IsInAGang() && $targetUser->GetGang()->gangtax != 0) {
                //Handle gang tax

                $gangmoney = (int) floor($moneywon * ($targetUser->GetGang()->gangtax / 100));

                $newmoney = (int) floor($moneywon * ((100 - $targetUser->GetGang()->gangtax) / 100));

                $targetUser->GetGang()->AddToAttribute('vault', $gangmoney);
            }

            $attackingUser->ForceRemoveFromAttribute('battlemoney', $moneywon);

            $attackingUser->AddToAttribute('battlelost', 1);

            $attackingUser->SetAttribute('hwhoID', $targetUser->id);

            $attackingUser->SetAttribute('hhow', 'attacked');

            $attackingUser->SetAttribute('hwhen', date('g:i:sa', time()));

            $hospDuration = 20;

            if ($jointAttack) {
                DBi::$conn->query('UPDATE grpgusers SET energy = 0, hp=\'' . $yourhp . '\', battlelost=battlelost+1 , hwhoID=\'' . $targetUser->id . '\', hhow=\'attacked\', hwhen=\'' . date('g:i:sa', time()) . '\', hospital=\'' . (time() + 60 * $hospDuration) . '\' WHERE id IN (' . $attackerIds . ')');

                $attackerIdsArr = explode(',', $attackerIds);

                foreach ($attackerIdsArr as $attackerId) {
                    User::SNotify($attackerId, sprintf(ATK_YOU_JOINT_ATTACKED_AND_HOSPITALIZED, ' <a href="profiles.php?id=' . $targetUser->id . '">' . $targetUser->username . '</a>', $moneywon, ' <a href="profiles.php?id=' . $attackingUser->id . '">' . $attackingUser->username . '</a>'), COM_ATTACK);
                }
            } else {
                $attackingUser->SetAttribute('hospital', time() + 60 * $hospDuration);

                $attackingUser->SetAttribute('energy', $newenergy);

                $attackingUser->SetAttribute('hp', $yourhp);

                $attackingUser->Notify(sprintf(ATK_YOU_ATTACKED_AND_HOSPITALIZED, ' <a href="profiles.php?id=' . $targetUser->id . '">' . $targetUser->username . '</a>', $moneywon), COM_ATTACK);
            }

            $targetUser->AddToAttribute('exp', $expwon);

            $targetUser->AddToAttribute('battlewon', 1);

            $targetUser->AddToAttribute('battlemoney', $moneywon);

            $targetUser->AddToAttribute('money', $newmoney);

            $targetUser->SetAttribute('hp', $theirhp);

            //give gang exp

            if ($targetUser->IsInAGang()) {
                if ($attackingUser->IsInAGang() != 0) {
                    $gangexpwon = $expwon;
                } else {
                    $gangexpwon = floor($expwon / 2);
                }

                $newgangexp = (int) $gangexpwon;

                $targetUser->GetGang()->AddToAttribute('exp', $newgangexp);

                if ($jointAttack) {
                    $targetUser->notify(sprintf(ATK_YOU_WERE_ATTACKED, addslashes($attackerNames), $expwon, $moneywon, $gangmoney, $gangexpwon), COM_ATTACK);
                } else {
                    $targetUser->notify(sprintf(ATK_YOU_WERE_ATTACKED, '<a href="profiles.php?id=' . $attackingUser->id . '">' . $attackingUser->username . '</a>', $expwon, $moneywon, $gangmoney, $gangexpwon), COM_ATTACK);
                }
            } else {
                $targetUser->notify(sprintf(ATK_YOU_WERE_ATTACKED_1, '<a href="profiles.php?id=' . $attackingUser->id . '">' . $attackingUser->username . '</a>', $expwon, $moneywon), COM_ATTACK);
            }

            //put defense log into gang

            if ($winner != 0) {
                if ($targetUser->IsInAGang() === true && $attackingUser->IsInAGang() === true) {
                    Logs::sAddGangAtkLog($time, $targetUser->gang, $attackingUser->id, $targetUser, $winner, $attackingUser->gang, $gangexpwon, $gangmoney, $expwon, $moneywon, $attackerIds);
                } elseif ($targetUser->IsInAGang() === true) {
                    Logs::sAddGangAtkLog($time, $targetUser->gang, $attackingUser->id, $targetUser, $winner, 'NULL', $gangexpwon, $gangmoney, $expwon, $moneywon, $attackerIds);
                } elseif ($attackingUser->IsInAGang() === true) {
                    Logs::sAddGangAtkLog($time, 'NULL', $attackingUser->id, $targetUser, $winner, $attackingUser->gang, $gangexpwon, $gangmoney, $expwon, $moneywon, $attackerIds);
                }
            }

            if ($jointAttack) {
                throw new FailedResult(sprintf(ATK_HOSPITALIZED_JOINT_3, $targetUser->formattedname, $attackerNames, $moneywon));
            }
            throw new FailedResult(sprintf(ATK_HOSPITALIZED_3, $targetUser->formattedname, $moneywon));
        }
    }
}

?>

