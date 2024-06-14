<?php

final class Attack extends AttackCommon
{
    /**
     * @throws SoftException
     * @throws FailedResult
     * @throws \Doctrine\DBAL\Exception
     * @throws SuccessResult
     */
    public function __construct(User $attackingUser, User $targetUser, &$attacks = null, &$hitdetails = null)
    {
        try {
            $isRiot = ((float) Variable::GetValue('riotStarted') > time());

            $user2 = SUserFactory::getInstance()->getUser($attackingUser->id);
            if ($user2->tutorial_v2 == 'attack_1') {

            } else {
                $this->Validation($attackingUser, $targetUser, []);
            }
        } catch (Exception $e) {
            throw $e;
        }

        $user2 = SUserFactory::getInstance()->getUser($attackingUser->id);
        if ($user2->tutorial_v2 == 'attack_1') {
            $user2->SetAttribute('tutorial_v2', 'medpack_1');
        }

        if ($user2->tutorial_v2 == 'attack_2') {
            $user2->SetAttribute('tutorial_v2', 'missions_1');
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

        $wreathQuery = DBi::$conn->query("SELECT * FROM temp_items_use WHERE christmas_wreath > 0 AND userid = " . $attackingUser->id);
        if (mysqli_num_rows($wreathQuery) > 0) {
            $defense = $defense * 2;

            DBi::$conn->query("UPDATE temp_items_use SET christmas_wreath = christmas_wreath - 1 WHERE userid = " . $attackingUser->id);
        }


        $attackerIds = '';

        $statsFlag = ['str' => false, 'def' => false, 'spd' => false];

        $inithp = $yourhp;

        $theirhp = $targetUser->hp;
        if ($attackingUser->gprot > time()) {
            $attackingUser->RemoveFromAttribute('gprot', 600);
        }

        if ($speed > $targetUser->GetModdedSpeed()) {
            $wait = 1;
        } else {
            $wait = 0;
        }
        if ($attackingUser->gang && $attackingUser->gang == $targetUser->gang){
            throw new SoftException("Friendly Fire is not tolerated ");
            exit;
        }
        //time add - 14days
        $inactive = time() - 1209600;
        if ($targetUser->id != 3 && $targetUser->level == 1 && $targetUser->lastactive > $inactive) {
            throw new SoftException("Hey bully! You can't attack level 1 players.");
            exit;
        }
        $statsFlag['spd'] = true;

        $attackLog = AttackLog::Create($attackingUser, $targetUser, $speed, $targetUser->GetModdedSpeed());

        $gangexpwon = 0;

        $winner = 0;

        $attackCount = 0;
        while ($yourhp > 0 && $theirhp > 0) {
            $attackCount++;
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

            if (UserBooks::UserHasStudied($targetUser->id, 35)) {
                $critical_attack = (5 >= rand(1, 100)); //calculate critical hit. 5% probablity
            } else {
                $critical_attack = (2 >= rand(1, 100)); //calculate critical hit. 2% probablity
            }

            if ($critical_attack) { //if critical attack then multiply damage by 2
                $damage = $damage * 2;
            }

            if ($wait == 0) {
                $turn = new AttackTurn();
                $yourhp = $yourhp - $damage;
                $turn->damage = $damage;
                if ($critical_attack) {
                    $turn->critical = $damage;
                }

                $attackLog->AddNewTurn($attackingUser, $targetUser, $targetUser, $turn, $yourhp, $theirhp);
            } else {
                $wait = 0;
            }

            if ($yourhp > 0) {
                $turn = new AttackTurn();
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

                $turn->damage = $damage;
                if (UserBooks::UserHasStudied($attackingUser->id, 35)) {
                    $critical_attack = (5 >= rand(1, 100));
                } else {
                    $critical_attack = (2 >= rand(1, 100));
                }
                if ($critical_attack) { //if critical attack then multiply damage by 2
                    $damage = $damage * 2;
                    $turn->critical = $damage;
                }
                $theirhp = $theirhp - $damage;

                $attackLog->AddNewTurn($attackingUser, $targetUser, $attackingUser, $turn, $yourhp, $theirhp);

                // Art of War: Attack By Fire - double attack
                if ($attackCount == 1) {
                    if ($speed > $targetUser->GetModdedSpeed() && UserBooks::UserHasStudied($attackingUser->id, 36)) {
                        $doubleAttackChance = mt_rand(1,100);
                        if ($doubleAttackChance <= 15) {
                            $turn = new AttackTurn();
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

                            $turn->damage = $damage;
                            if (UserBooks::UserHasStudied($attackingUser->id, 35)) {
                                $critical_attack = (5 >= rand(1, 100));
                            } else {
                                $critical_attack = (2 >= rand(1, 100));
                            }
                            if ($critical_attack) { //if critical attack then multiply damage by 2
                                $damage = $damage * 2;
                                $turn->critical = $damage;
                            }
                            $theirhp = $theirhp - $damage;

                            $attackLog->AddNewTurn($attackingUser, $targetUser, $attackingUser, $turn, $yourhp, $theirhp);
                        }
                    }
                }
            }
        }

        $time = time();


        if ($attackingUser->GetLovePotionTime() > time()) {
            $newenergy = $attackingUser->energy;
        } else {
            if (UserBooks::UserHasStudied($attackingUser->id, 38)) {
                if (5 >= mt_rand(1,100)) {
                    $newenergy = (int) ($attackingUser->energy - floor($attackingUser->GetMaxEnergy() * .10));
                } else {
                    $newenergy = (int) ($attackingUser->energy - floor($attackingUser->GetMaxEnergy() * .25));
                }
            } else {
                $newenergy = (int) ($attackingUser->energy - floor($attackingUser->GetMaxEnergy() * .25));
            }

            if ($newenergy < 0) {
                $newenergy = 0;
            }
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
            $extra = '';
            $theirhp = 0;

            $moneywon = (float) floor($targetUser->money / 10);
            if($level > 500){
                $level = 500;
            }
            if($targetUser->level > 500){
                $targetUser->level = 500;
            }
            if ($targetUser->level >= $level) {
                $expwon = 150 - (50 * ($level - $targetUser->level));
            } else {
                $expwon = 150 - (10 * ($level - $targetUser->level));
            }

            $expwon = ($expwon < 0) ? 0 : $expwon;

            $expwon = (int) floor($expwon * $attackingUser->GetExpMultiplier());

            $expwon = (int) floor($expwon * $attackingUser->XGetExpMultiplier());
            
            if (Utility::IsEventRunning('doublexp') === true) {
				$expwon = $expwon * 2;
			}

            $gangmoney = 0;

            $newmoney = (float) $moneywon;

            $targetUser->RemoveFromAttribute('money', $newmoney);

            if ($targetUser->GetFungalVialTime() > time()) {
                $attackingUser->performUserQuestAction('fungal_vial_attacks', 1);
            }

            if ($attackingUser->IsInAGang() && $attackingUser->GetGang()->gangtax != 0) {
                //Handle gang tax

                $gangmoney = (int) floor($moneywon * ($attackingUser->GetGang()->gangtax / 100));

                $newmoney = (int) floor($moneywon * ((100 - $attackingUser->GetGang()->gangtax) / 100));

                $attackingUser->GetGang()->AddToAttribute('vault', $gangmoney);
            }
            
                        if ($attackingUser->santa < 1 && $targetUser->id == 1 && false) {
                           $tot = mt_rand(1,5);
                           if($tot ==  1){
                               $time = mt_rand(10,15);
                               $ti = ($time * 60) + time();
                               $attackingUser->AddToAttribute('santa', 1);  
                               $attackingUser->AddToAttribute('hospital', $ti);
                               $attackingUser->SetAttribute('hp', 0);
                            throw new FailedResult("Trick or treat? The devil kicked your ass and you are sent to the hospital for ".$time." minuets");
                           }elseif ($tot == 2){
                            $time = mt_rand(10,15);
                            $ti = ($time * 60) + time();
                            $attackingUser->AddToAttribute('santa', 1);
                            $attackingUser->SetAttribute('hospital', $ti);
                            $attackingUser->SetAttribute('hp', 0);
                            throw new FailedResult("Trick or treat? The devil turned the MPs on you, they haul you away for ".$time." minuets");
                           }else{
                               $pointsorexp = mt_rand(1,2);

                               if($pointsorexp == 1){
                                   $rpoints = mt_rand(100,400);
                                   $attackingUser->AddToAttribute('santa', 1);
                                   $attackingUser->AddToAttribute('points', $rpoints);
                                   throw new SuccessResult("You got a treat! You gain ". $rpoints." points");
                                }else{
                                   $rexp = mt_rand(100,1000);
                                   $attackingUser->AddToAttribute('santa', 1);
                                   $attackingUser->AddToAttribute('exp', $rexp);
                                   throw new SuccessResult("You got a treat! You gain ". $rexp." exp");
                              
                               }
                           }

                            /* $rand = DBi::$conn->query('SELECT * FROM 12days WHERE day = ' . date('d'));
                            if (mysqli_num_rows($rand)) {
                                $w = mysqli_fetch_assoc($rand);
                                if ($w['what'] == 'points') {
                                    $attackingUser->AddToAttribute($w['what'], $w['qty']);
                                    $attackingUser->AddToAttribute('santa', 1);
                                    $attackingUser->Notify('You won  ' . $w['qty'] . ' x ' . $w['what']);
                                    throw new SuccessResult('You won  ' . $w['qty'] . ' x ' . $w['what']);
                                } elseif ($w['what'] == 'money') {
                                    $attackingUser->AddToAttribute($w['what'], $w['qty']);
                                    $attackingUser->AddToAttribute('santa', 1);
                                    $attackingUser->Notify('You won  $' . number_fornat($w['qty']) . ' x ' . $w['what']);
                                    throw new SuccessResult('You won  $' . number_fornat($w['qty']) . ' x ' . $w['what']);
                                } elseif ($w['what'] == 'awakepill') {
                                    $attackingUser->AddItems(36, $w['qty']);
                                    $attackingUser->AddToAttribute('santa', 1);
                                    $attackingUser->Notify('You won  ' . $w['qty'] . ' x Awake Pills');
                                    throw new SuccessResult('You won  ' . $w['qty'] . ' x Rs Days');
                                } elseif ($w['what'] == 'rmdays') {
                                    $attackingUser->AddItems(14, $w['qty']);
                                    $attackingUser->AddToAttribute('santa', 1);
                                    $attackingUser->Notify('You won  ' . $w['qty'] . ' x Awake Pills');
                                    throw new SuccessResult('You won  ' . $w['qty'] . ' x Awake Pills');
                                } elseif ($w['what'] == 'exp') {
                                    $attackingUser->AddToAttribute('exp', $w['qty']);
                                    $attackingUser->AddToAttribute('santa', 1);
                                    $attackingUser->Notify('You won  ' . $w['qty'] . ' x Exp');
                                    throw new SuccessResult('You won  ' . $w['qty'] . ' x Exp');
                                }
                            } */
                        }
//            if($attackingUser->id == 1){
//                var_dump(BattleLadder::LadderAttack($attackingUser->id, $targetUser->id, $winner));
//            }else{
//                BattleLadder::LadderAttack($attackingUser->id, $targetUser->id, $winner);
//            }

            if (Utility::IsEventRunning('virus') === true) {
                if ($attackingUser->is_infected === false && $targetUser->IsAdmin()) {
                    $attackingUser->SetAttribute('virus_infected_time', date('Y-m-d H:i:s'));
                    $targetUser->AddToAttribute('virus_infected_points', 1);
                    $attackingUser->Notify(sprintf(ATK_INFECTED_ATTACKER, $targetUser->formattedname));
                } elseif ($attackingUser->is_infected === true && $targetUser->is_infected === false) {
                    $targetUser->SetAttribute('virus_infected_time', date('Y-m-d H:i:s'));
                    $attackingUser->AddToAttribute('virus_infected_points', 1);
                    $targetUser->Notify(sprintf(ATK_INFECTED_ATTACKER, $attackingUser->formattedname));
                }
                if ($attackingUser->is_infected === true && $targetUser->is_infected === false) {
                    if ($targetUser->virus_infected_points > 1) {
                        $targetUser->RemoveFromAttribute('virus_infected_points', 1);
                    }
                }
            }
            $queryBuilder = BaseObject::createQueryBuilder();

$queryBuilder->select('*')
             ->from('battle_members')
             ->where('bmemberUser = :userid')
             ->setParameter('userid', $attackingUser->id);

$check_one = $queryBuilder->execute();

$queryBuilder = BaseObject::createQueryBuilder();

$queryBuilder->select('*')
             ->from('battle_members')
             ->where('bmemberUser = :userid')
             ->setParameter('userid', $targetUser->id);

$check_two = $queryBuilder->execute();

if ($check_one->rowCount() && $check_two->rowCount()) {
    $check1 = $check_one->fetchAssociative();
    $check2 = $check_two->fetchAssociative();

    if ($check1['bmemberLadder'] == $check2['bmemberLadder']) {
        $score = rand(12, 24);

        $queryBuilder = BaseObject::createQueryBuilder();

        $queryBuilder->update('battle_members')
                     ->set('bmemberScore', 'bmemberScore - :score')
                     ->set('bmemberLosses', 'bmemberLosses + 1')
                     ->where('bmemberUser = :userid')
                     ->setParameter('score', $score)
                     ->setParameter('userid', $targetUser->id)
                     ->execute();

        $queryBuilder = BaseObject::createQueryBuilder();

        $queryBuilder->update('battle_members')
                     ->set('bmemberWins', 'bmemberWins + 1')
                     ->where('bmemberUser = :userid')
                     ->setParameter('userid', $attackingUser->id)
                     ->execute();

      //  echo 'You have added ' . $score . ' points to the score on the battle ladder, well done.';
    }
}
            if ($attackingUser->signuptime > (time() - 7776000)) {
                $black = DBi::$conn->query('SELECT * FROM blackops WHERE userid = ' . $attackingUser->id);
                if (mysqli_num_rows($black)) {
                    DBi::$conn->query('UPDATE blackops SET attacks = attacks + 1 WHERE userid = ' . $attackingUser->id);
                } else {
                    DBi::$conn->query('INSERT INTO blackops (userid, attacks) VALUES(' . $attackingUser->id . ', 1)');
                }
            }
            Prestige::addToPres($attackingUser, 'attacks', 1);
           // $chk_one = DBi::$conn->query('SELECT * FROM `battle_members` WHERE `bmemberUser` = ' . $attackingUser->id);
            //$chk_two = DBi::$conn->query('SELECT * FROM `battle_members` WHERE `bmemberUser` = ' . $targetUser->id);
            //if (mysqli_num_rows($chk_one) and mysqli_num_rows($chk_two)) {
              //  $ch1 = mysqli_fetch_assoc($chk_one);
               // $ch2 = mysqli_fetch_assoc($chk_two);
                //if ($ch1['bmemberLadder'] == $ch2['bmemberLadder']) {
                  //  $score = mt_rand(12, 24);
                    //DBi::$conn->query('UPDATE `battle_members` SET `bmemberScore` = `bmemberScore` - ' . $score . ", `bmemberLosses` = `bmemberLosses` + '1' WHERE `bmemberUser` = " . $targetUser->id);
                    //DBi::$conn->query('UPDATE `battle_members` SET `bmemberScore` = `bmemberScore` + ' . $score . ", `bmemberWins` = `bmemberWins` + '1' WHERE `bmemberUser` = " . $attackingUser->id);
                    //$extra .= '<br>You have added ' . $score . ' points to the score on the battle ladder, well done.<br>';
                //}
            //}
            $targetUser->ForceRemoveFromAttribute('battlemoney', $moneywon);

            $targetUser->AddToAttribute('battlelost', 1);
           
            
                BattlePass::addExp($attackingUser->id, 10);
                $attackingUser->addActivityPoint();
                

            $targetUser->SetAttribute('hwhoID', $attackingUser->id);

            $targetUser->SetAttribute('hhow', 'wasattacked');

            $targetUser->SetAttribute('hwhen', date('g:i:sa', time()));

            $hospDuration = (time() - $targetUser->lastactive < 900) ? 5 : 10;

            $hospDuration += $attackingUser->GetHospitalDurationBonus();

            $skill = User::sGetSkill($attackingUser->id, SK_CRUSHING_ID);

            if ($skill->activated == 1) {
                $hosptime = 5 * $skill->level;
                $hospDuration += $hosptime;
            }

            $hitdetails['hospitalTime'] = $hospDuration; // Added by for task Hitman

            $targetUser->SetAttribute('hospital', time() + 60 * $hospDuration);

            if (isset($hitdetails['thiswasahit'])) {
                $msg = HITMAN_ATTACK;
            } else {
                $msg = '';
            }

            $targetUser->Notify(sprintf(ATK_YOU_HOSPITALIZED_BY_USER, '<a href="profiles.php?id=' . $attackingUser->id . '">' . $attackingUser->username . '</a>', $hospDuration, $moneywon, $msg), COM_ATTACK);
            if ($attackingUser->attackexp > time()) {
                $expwon = $expwon * 2;
            }
            $attackingUser->AddToAttribute('exp', $expwon);

            $attackingUser->AddToAttribute('battlewon', 1);

            $attackingUser->AddToAttribute('battlemoney', $moneywon);

            $attackingUser->AddToAttribute('money', $newmoney);

            $attackingUser->SetAttribute('energy', $newenergy);

            $attackingUser->SetAttribute('hp', $yourhp);

            $targetUser->SetAttribute('hp', $theirhp);

            // Dog Tags
            if ($targetUser->dog_tags > 0) {
	            if (Utility::IsEventRunning('tags') === true){
		            $tags = 4;
	            }else{
		            $tags = 2;
	            }
                $rand = mt_rand(1,25);
                if($rand == 1) {
                    $attackingUser->AddToAttribute('dog_tags', $tags);

                    if (Utility::IsEventRunning('tags') === false) {
                        $targetUser->RemoveFromAttribute('dog_tags', 1);
                    }



                $targetUser->Notify(sprintf(ATK_DOG_TAG_LOST, $attackingUser->formattedname));
                $extra .= '<br>' . sprintf(ATK_DOG_TAG_GAINED, $tags, $targetUser->formattedname);
                    }
            } else if ($targetUser->dog_tags == 0) {
                if (mt_rand(1, 4) == 2) {
                    $attackingUser->AddToAttribute('dog_tags', 1);
                    $extra .= '<br>' . sprintf(ATK_DOG_TAG_GAINED, $tags, $targetUser->formattedname);
                }
            }
            if (Utility::IsEventRunning('attackadmin') === true && $targetUser->IsAdmin()) {
                $chance = mt_rand(1, 4);
                if ($chance == 1) {
                    $attackingUser->AddToAttribute('dog_tag', 1);
                    $extra .= 'You have won a dog tag for attacking the officer.';
                } elseif ($chance == 2) {
                    $maxExp = User::GetNeededXPForLevel($attackingUser->level);
                    $expEarning = $maxExp / 100 * mt_rand(1,3);

                    $extra .= 'You have won ' . number_format($expEarning, 0) . ' EXP for attacking the officer.';
                } elseif ($chance == 3) {
                    $attackingUser->SetAttribute('hospital', time() + 60 * 5);
                    $extra .= 'You may of won the fight, but you shouldn\'t be hitting the officers!, they have sent
		        	you to the hospital for 5 minutes';
                } elseif ($chance == 4) {
                    $attackingUser->AddItems(Item::GetItemId('CHRISTMAS_CANDY_CANE_NAME'), 1);

                    $extra .= 'You have won 1 x Candy Cane for attacking the officer.';
                }
            }
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

                $attackLog->Save($attackingUser, $moneywon, $expwon, $gangmoney, $newgangexp);

                throw new SuccessResult(sprintf(ATK_HOSPITALIZED_1, $targetUser->formattedname, $expwon, $moneywon, $gangmoney, $targetUser->formattedname, $gangexpwon) . $extra);
            }

            $attackLog->Save($attackingUser, $moneywon, $expwon, 0, 0);

            if ($targetUser->IsInAGang()) {
                Logs::sAddGangAtkLog($time, $targetUser->gang, $attackingUser->id, $targetUser, $winner, 'NULL', $gangexpwon, $gangmoney, $expwon, $moneywon);
            } else {
                Logs::sAddGangAtkLog($time, 'NULL', $attackingUser->id, $targetUser, $winner, 'NULL', $gangexpwon, $gangmoney, $expwon, $moneywon);
            }

            throw new SuccessResult(sprintf(ATK_HOSPITALIZED_2, $targetUser->formattedname, $expwon, $moneywon, $targetUser->formattedname) . $extra);
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

            if (Utility::IsEventRunning('doublexp') === true) {
				$expwon = $expwon * 2;
			}

            $newmoney = (float) $moneywon;

            $gangmoney = 0;

            $attackingUser->RemoveFromAttribute('money', $newmoney);

            if ($targetUser->IsInAGang() && $targetUser->GetGang()->gangtax != 0) {
                //Handle gang tax

                $gangmoney = (int) floor($moneywon * ($targetUser->GetGang()->gangtax / 100));

                $newmoney = (int) floor($moneywon * ((100 - $targetUser->GetGang()->gangtax) / 100));

                $targetUser->GetGang()->AddToAttribute('vault', $gangmoney);
            }
            $extra = '';

            if (Utility::IsEventRunning('virus') === true) {
                if ($attackingUser->is_infected === false && $targetUser->IsAdmin()) {
                    $attackingUser->SetAttribute('virus_infected_time', date('Y-m-d H:i:s'));
                    $targetUser->AddToAttribute('virus_infected_points', 1);
                    $attackingUser->Notify(sprintf(ATK_INFECTED_ATTACKER, $targetUser->formattedname));
                } elseif ($attackingUser->is_infected === true && $targetUser->is_infected === false) {
                    $targetUser->SetAttribute('virus_infected_time', date('Y-m-d H:i:s'));
                    $attackingUser->AddToAttribute('virus_infected_points', 1);
                    $targetUser->Notify(sprintf(ATK_INFECTED_ATTACKER, $attackingUser->formattedname));
                }
                if ($attackingUser->is_infected === true && $targetUser->is_infected === false) {
                    if ($targetUser->virus_infected_points > 1) {
                        $targetUser->RemoveFromAttribute('virus_infected_points', 1);
                    }
                }
            }

            if ($attackingUser->dog_tags > 0) {
                $targetUser->AddToAttribute('dog_tags', 1);
                $attackingUser->RemoveFromAttribute('dog_tags', 1);
                $targetUser->Notify(sprintf(ATK_DOG_TAG_GAINED, 1, $attackingUser->formattedname));
                $extra .= '<br>' . sprintf(ATK_DOG_TAG_LOST, $targetUser->formattedname);
            }
            //put defense log into gang
            $queryBuilder = BaseObject::createQueryBuilder();

            $queryBuilder->select('*')
                         ->from('battle_members')
                         ->where('bmemberUser = :userid')
                         ->setParameter('userid', $attackingUser->id);
            
            $check_one = $queryBuilder->execute();
            
            $queryBuilder = BaseObject::createQueryBuilder();
            
            $queryBuilder->select('*')
                         ->from('battle_members')
                         ->where('bmemberUser = :userid')
                         ->setParameter('userid', $targetUser->id);
            
            $check_two = $queryBuilder->execute();
            
            if ($check_one->rowCount() && $check_two->rowCount()) {
                $check1 = $check_one->fetchAssociative();
                $check2 = $check_two->fetchAssociative();
            
                if ($check1['bmemberLadder'] == $check2['bmemberLadder']) {
                    $score = rand(12, 24);
            
                    $queryBuilder = BaseObject::createQueryBuilder();
            
                    $queryBuilder->update('battle_members')
                                 ->set('bmemberScore', 'bmemberScore - :score')
                                 ->set('bmemberLosses', 'bmemberLosses + 1')
                                 ->where('bmemberUser = :userid')
                                 ->setParameter('score', $score)
                                 ->setParameter('userid', $attackingUser->id)
                                 ->execute();
            
                    $queryBuilder = BaseObject::createQueryBuilder();
            
                    $queryBuilder->update('battle_members')
                                 ->set('bmemberWins', 'bmemberWins + 1')
                                 ->where('bmemberUser = :userid')
                                 ->setParameter('userid', $targetUser->id)
                                 ->execute();
            
                  //  echo 'You have added ' . $score . ' points to the score on the battle ladder, well done.';
                }
            }

            // $chk_one = DBi::$conn->query('SELECT * FROM `battle_members` WHERE `bmemberUser` = ' . $targetUser->id);
            // $chk_two = DBi::$conn->query('SELECT * FROM `battle_members` WHERE `bmemberUser` = ' . $attackingUser->id);
            // if (mysqli_num_rows($chk_one) and mysqli_num_rows($chk_two)) {
            //     $score = rand(12, 24);
            //     DBi::$conn->query('UPDATE `battle_members` SET `bmemberScore` = `bmemberScore` - ' . $score . ", `bmemberLosses` = `bmemberLosses` + '1' WHERE `bmemberUser` = " . $attackingUser->id);
            //     DBi::$conn->query('UPDATE `battle_members` SET `bmemberScore` = `bmemberScore` + ' . $score . ", `bmemberWins` = `bmemberWins` + '1' WHERE `bmemberUser` = " . $targetUser->id);
            //     $extra .= '<br>You have lost ' . $score . ' points from the score on the battle ladder.<br>';
            // }
//            if($attackingUser->id == 1){
//                var_dump(BattleLadder::LadderAttack($attackingUser->id, $targetUser->id, $winner));
//            }else{
//                BattleLadder::LadderAttack($attackingUser->id, $targetUser->id, $winner);
//            }
            $attackingUser->ForceRemoveFromAttribute('battlemoney', $moneywon);

            $attackingUser->AddToAttribute('battlelost', 1);
            
                BattlePass::addExp($targetUser->id, 20);
                $targetUser->addActivityPoint();
                

            $attackingUser->SetAttribute('hwhoID', $targetUser->id);

            $attackingUser->SetAttribute('hhow', 'attacked');

            $attackingUser->SetAttribute('hwhen', date('g:i:sa', time()));

            $hospDuration = 5;

            $attackingUser->SetAttribute('hospital', time() + 60 * $hospDuration);

            $attackingUser->SetAttribute('energy', $newenergy);

            $attackingUser->SetAttribute('hp', $yourhp);

            $attackingUser->Notify(sprintf(ATK_YOU_ATTACKED_AND_HOSPITALIZED, ' <a href="profiles.php?id=' . $targetUser->id . '">' . $targetUser->username . '</a>', $moneywon), COM_ATTACK);

            if ($targetUser->expfailatck == 1 && $targetUser->level >= 4) {
                $expwon = 0;
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

                $attackLog->Save($targetUser, $moneywon, $expwon, $gangmoney, $gangexpwon);

                $targetUser->notify(sprintf(ATK_YOU_WERE_ATTACKED, '<a href="profiles.php?id=' . $attackingUser->id . '">' . $attackingUser->username . '</a>', $expwon, $moneywon, $gangmoney, $gangexpwon), COM_ATTACK);
            } else {
                $attackLog->Save($targetUser, $moneywon, $expwon, 0, 0);

                $targetUser->notify(sprintf(ATK_YOU_WERE_ATTACKED_1, '<a href="profiles.php?id=' . $attackingUser->id . '">' . $attackingUser->username . '</a>', $expwon, $moneywon), COM_ATTACK);
            }

            if ($winner != 0) {
                if ($targetUser->IsInAGang() === true && $attackingUser->IsInAGang() === true) {
                    Logs::sAddGangAtkLog($time, $targetUser->gang, $attackingUser->id, $targetUser, $winner, $attackingUser->gang, $gangexpwon, $gangmoney, $expwon, $moneywon, $attackerIds);
                } elseif ($targetUser->IsInAGang() === true) {
                    Logs::sAddGangAtkLog($time, $targetUser->gang, $attackingUser->id, $targetUser, $winner, 'NULL', $gangexpwon, $gangmoney, $expwon, $moneywon, $attackerIds);
                } elseif ($attackingUser->IsInAGang() === true) {
                    Logs::sAddGangAtkLog($time, 'NULL', $attackingUser->id, $targetUser, $winner, $attackingUser->gang, $gangexpwon, $gangmoney, $expwon, $moneywon, $attackerIds);
                }
            }

            throw new FailedResult(sprintf(ATK_HOSPITALIZED_3, $targetUser->formattedname, $moneywon) . $extra);
        }
    }


}
