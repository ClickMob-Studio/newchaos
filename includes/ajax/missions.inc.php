<?php
if ($user_class->jail > time()) {
throw new SoftException(CRIME_IN_SHOWER);
} elseif ($user_class->hospital > time()) {
throw new SoftException(CRIME_IN_HOSPITAL);
}

switch ($_POST['action']) {
    case 'setExpToOwner':
        $user2->setAttribute('perexp', (int)$_POST['value']);

        $user2->perexp = (int)$_POST['value'];

        throw new SuccessResult('You have successfully set your Mission Contract experience share to ' . (int)$_POST['value'] . '%.');
        break;
    case 'refillNerve':
        if ($user_class->merits > 0 && $user_class->RefillNerve('merits', 1)) {
            throw new SuccessResult('You have spent 1 merit and refilled your nerve.');
        }
        if ($user_class->points < 10) {
            throw new SoftException(CRIME_NOT_ENOUGH_POINTS);
        }
        if (!$user_class->RefillNerve('points', 10)) {
            throw new SoftException(CRIME_NOT_ENOUGH_POINTS);
        }
        throw new SuccessResult(CRIME_REFILLED_NERVE);
        break;
    case 'refillUseAllNerve':
        if (!UserBooks::UserHasStudied($user_class->id, 21)) {
            throw new FailedResult(BOOK_NOT_STUDIED);
        }
        if ($user_class->merits > 0) {
            if ($user_class->nerve < $user_class->GetMaxNerve()) {
                if (!$user_class->RefillNerve('merits', 1)) {
                    throw new SoftException('An issue occurred whilst refilling your nerve.');
                }
            }
        } else {
            if ($user_class->points < 10) {
                throw new SoftException(CRIME_NOT_ENOUGH_POINTS);
            }
            if ($user_class->nerve < $user_class->GetMaxNerve()) {
                if (!$user_class->RefillNerve('points', 10)) {
                    throw new SoftException(CRIME_NOT_ENOUGH_POINTS);
                }
            }
        }

        $_POST['action'] = 'useAllNerve';
// no break
    case 'useAllNerve':
        if (!UserBooks::UserHasStudied($user_class->id, 3)) {
            throw new FailedResult(BOOK_NOT_STUDIED);
        }
        if (empty($user_class->nerve)) {
            throw new FailedResult(CRIME_NOT_ENOUGH_NERVE);
        }
        $crime = Crime::GetByNerve($user_class->nerve);

        if (empty($crime)) {
            throw new FailedResult(CRIME_NOT_ENOUGH_NERVE);
        }
        $_POST['crimeId'] = $crime->id;

// no break
    case 'startCrime':
        $showQuestQuestion = false;
        if (empty($_POST['crimeId'])) {
            throw new SoftException(CRIME_FAILED);
        }

        if ($_POST['crimeId'] == 6) {
            $showQuestQuestion = true;
        }

        if (isset($tutorial)) {
            $tutorial->setDone('Crime');
        }
        $crime = new Crime($_POST['crimeId']);
        $nerve = $crime->nerve;

        if ($user_class->nerve < $nerve) {
            throw new FailedResult(CRIME_NOT_ENOUGH_NERVE);
        }
        ActionLogs::Log($user_class->id, $crime->name, $nerve); // Log the action

        if (!isset($userCrimeExpIndexed[$crime->id])) {
            UserCrimeExp::create($user_class->id, $crime->id);
            $userCrimeExpIndexed = UserCrimeExp::getForUserIndexed($user_class->id);
        }
        UserCrimeExp::addExp($userCrimeExpIndexed[$crime->id]);

        $name = $crime->name;
        $stext = '[[' . CRIME_NOT_SUCCESS_MESSAGE . ']]';
        $ctext = '[[' . CRIME_NOT_CAUGHT_MESSAGE . ']]';
        $ftext = '[[' . CRIME_NOT_FAILURE_MESSAGE . ']]';
        $stexta = explode('^', $crime->stext);
        $stext = ($stexta[0] != '') ? $stexta[array_rand($stexta)] : $stext;
        $ctexta = explode('^', $crime->ctext);
        $ctext = ($ctexta[0] != '') ? $ctexta[array_rand($ctexta)] : $ctext;
        $ftexta = explode('^', $crime->ftext);
        $ftext = ($ftexta[0] != '') ? $ftexta[array_rand($ftexta)] : $ftext;

        $chance2 = rand(1, 8);

        $userStat = $user_class->GetModdedSpeed();
        if ($crime->impact_stat === 'strength') {
            $userStat = $user_class->GetModdedStrength();
        } else if ($crime->impact_stat === 'defense') {
            $userStat = $user_class->GetModdedDefense();
        }

        if ($userStat < 50000) {
            $chance3 = rand(1, 35);
        } elseif ($userStat < 250000) {
            $chance3 = rand(1, 50);
        } elseif ($userStat < 1000000) {
            $chance3 = rand(1, 80);
        } else {
            $chance3 = rand(1, 100);
        }

        $calc = $nerve;
        if (($nerve > 30) and ($nerve < 40)) {
            $calc = $nerve * 2;
        } elseif (($nerve > 40) and ($nerve < 60)) {
            $calc = $nerve * 4;
        }

        $chance = rand(1, 100 * $calc - floor($userStat / 20));
        $money = getMoney($nerve);
        $exp = getExp($nerve);

        $crimesucceeded = 1;
        $groupCrimeMsg = '';
        if ($_POST['action'] == 'useAllNerve') {
            $multiplyer = (int)($user_class->nerve / $nerve);
            $money *= $multiplyer;
            $exp *= $multiplyer;
            $numCrimes = $multiplyer;

            $groupCrimeMsg = sprintf(BOOK_DONE_GROUP_CRIME_NERVE, $multiplyer . ' x <b>' . $name . '</b>', $nerve);

            $remainNerve = $user_class->nerve % $nerve;
            while ($remainNerve > 0) {
                $newCrime = Crime::GetByNerve($remainNerve);

                if ($newCrime->id == 6) {
                    $showQuestQuestion = true;
                }

                $newNerve = $newCrime->nerve;

                ActionLogs::Log($user_class->id, $newCrime->name, $newNerve); // Log the action

                $newMoney = getMoney($newNerve);
                $newExp = getExp($newNerve);

                $money += $newMoney;
                $exp += $newExp;

                $groupCrimeMsg .= '<br />' . sprintf(BOOK_DONE_GROUP_CRIME_NERVE, '1 x <b>' . $newCrime->name . '</b>', $newNerve);
                $remainNerve = $remainNerve - $newNerve;

                $nerve = $nerve * $multiplyer + $newNerve;
                ++$numCrimes;
                ++$crimesucceeded;
            }
            $nerve = $user_class->nerve;

            $groupCrimeMsg = sprintf(BOOK_DONE_GROUP_CRIME, $numCrimes) . '<br /><br />' . $groupCrimeMsg;
        }


        $removeNerve = true;
        if (2 >= rand(1, 100) && UserBooks::UserHasStudied($user_class->id, 20)) {
            $removeNerve = false;
        } elseif (1 >= rand(1, 100) && UserBooks::UserHasStudied($user_class->id, 16) && !UserBooks::UserHasStudied($user_class->id, 20)) {
            $removeNerve = false;
        }

        if ($chance3 == 27 || $chance >= 150) {
            $user_class->AddToAttribute('crimefailed', 1);
            if ($removeNerve) {
                $user_class->RemoveFromAttribute('nerve', $nerve, 'crimes');
            }
            $user_class->SetAttribute('jail', 60 * $crime->jail);

            HatePoints::AddPoint($user_class, HatePoints::TASK_CRIME, false);

//DailyTasks::checkDailies($user_class->id, 'crime');

            if ($_POST['action'] == 'useAllNerve') {
                throw new FailedResult($groupCrimeMsg . '<br><br><font color="darkred">' . CRIME_GROUP_FAILED . '</font> ' . sprintf(CRIME_CAUGHT_SHOWER, $crime->jail));
            }
            throw new FailedResult($ctext . '<br><br><font color="darkred">' . CRIME_CAUGHT . '</font> ' . sprintf(CRIME_CAUGHT_SHOWER, $crime->jail));
        } elseif ($chance <= 75 || $chance2 == 3) {
            $user_class->AddToAttribute('crimesucceeded', $crimesucceeded);
//$activeGameEvent = GameEvent::getActiveGameEvent();
//                if ($activeGameEvent && isset($activeGameEvent[0]) && isset($activeGameEvent[1])) {
//                    if ($activeGameEvent[0] === 'EXP') {
//                        $exp = $exp * $activeGameEvent[1];
//                    }
//                }


            if (isset($userCrimeExpIndexed[$crime->id]) && $userCrimeExpIndexed[$crime->id]['level'] > 1) {
                $exp = ceil($exp + (($exp / 10) * ($userCrimeExpIndexed[$crime->id]['level'] * 3)));
                $money = ceil($money + (($money / 10) * ($userCrimeExpIndexed[$crime->id]['level'] * 4)));
            }
            $exp = $exp * $user_class->GetStatMultiplier(GangBonus::$BONUS_EXP);
            if (count($crimesContract) == 1) {
                $ct = $crimesContract[0];

                $target = UserFactory::getInstance()->getUser($ct->buyer);
                $bexp = (int)(($user2->perexp / 100) * $exp);
                $uexp = $exp - $bexp;

                $target->AddToAttribute('exp', $bexp);

                try {
                    CrimesContract::Crimes($user_class, $bexp);
                } catch (SuccessResult $s) {
                    echo HTML::ShowSuccessMessage($s->getMessage());
                } catch (FailedResult $f) {
                    echo HTML::ShowFailedMessage($f->getMessage());
                } catch (SoftException $e) {
                    echo HTML::ShowErrorMessage($e->getMessage());
                }

                $user_class->AddToAttribute('exp', $uexp);

                $user_class->AddToAttribute('crimemoney', $money);

                $user_class->AddToAttribute('money', $money);

                $scrime =
                    '<br><br>' .
                    $target->formattedname .
                    ' earned ' .
                    $bexp .
                    ' of exp . You received' .
                    ' ' .
                    $uexp .
                    ' of experience';

                $id = CrimesContract::UnderContract($user_class->id);

                $crimesContract =
                    $id == false ? [] : [new CrimesContract($id)];
            } else {
                $scrime = '';
                $user_class->AddToAttribute('exp', $exp);

                $user_class->AddToAttribute('crimemoney', $money);

                $user_class->AddToAttribute('money', $money);
            }

            BattlePass::addExp($user_class->id, $nerve);

            if ($removeNerve) {
                $user_class->RemoveFromAttribute('nerve', $nerve, 'crimes');
            }

            HatePoints::AddPoint($user_class, HatePoints::TASK_CRIME);

            $stext1 = '';
            if (!$removeNerve) {
                $stext1 .= '<br /><br /><span class="darkred">' . BOOK_YOGA_EFFECT_CRIME . '</span>';
            }

// Potential of receiving wardens office key from crimes
//            if ($nerve >= 30 && mt_rand(0, 10000) === 0) {
//                echo HTML::ShowSuccessMessage('After you complete your nefarious activities you see a ' . HTML::ShowItemPopup('Warden Office Key', Item::GetItemId('WARDEN_OFFICE_KEY_NAME')) . '. You pick it up casually.');
//                $user_class->AddItems(Item::GetItemId('WARDEN_OFFICE_KEY_NAME'));
//                $user_class->Notify('While committing crimes you found a ' . HTML::ShowItemPopup('Warden Office Key', Item::GetItemId('WARDEN_OFFICE_KEY_NAME')) . '.');
//                Logs::addTokenLog($user_class->id, 'WARDEN_OFFICE_KEY_NAME', 'Crimes');
//            }
            DailyTasks::recordUserTaskAction(DailyTasks::USE_MAX_NERVE, $user_class, $nerve);

            DailyTasks::recordUserTaskAction(DailyTasks::COMMIT_CRIMES, $user_class, 1);
            WeeklyMissions::AddMission($user_class->id, 1);
            $black = DBi::$conn->query('SELECT * FROM blackops WHERE userid = ' . $user_class->id);
            if (mysqli_num_rows($black)) {
                DBi::$conn->query('UPDATE blackops SET missions = missions + 1 WHERE userid = ' . $user_class->id);
            } else {
                DBi::$conn->query('INSERT INTO blackops (userid, missions) VALUES(' . $user_class->id . ', 1)');
            }
            if($user_class->level > 249){
                $black = DBi::$conn->query('SELECT * FROM prestige1 WHERE userid = ' . $user_class->id);
                if (mysqli_num_rows($black)) {
                    DBi::$conn->query('UPDATE prestige1 SET missions = missions + 1 WHERE userid = ' . $user_class->id . ' AND missions < 1000');
                } else {
                    DBi::$conn->query('INSERT INTO prestige1 (userid, missions) VALUES(' . $user_class->id . ', 1)');
                }
            }

            if (!$user_class->IsAdmin()) {
                UserBarracksRecord::recordAction(UserBarracksRecord::CRIME, $user_class->id, 1);
            }

//DailyTasks::checkDailies($user_class->id, 'crime');

            if ($_POST['action'] == 'useAllNerve') {
                throw new SuccessResult($groupCrimeMsg . '<br><br><font color="darkgreen">' . sprintf(CRIME_GROUP_SUCCESS, $exp, number_format($money)) . '</font>' . $stext1);
            }
//throw new SuccessResult($stext . '<br><br><font color="darkgreen">' . sprintf(CRIME_SUCCESS, $exp, number_format($money)) . '</font>' . $stext1);
            throw new SuccessResult($stext . '<br><br><strong>' . sprintf(CRIME_SUCCESS, $exp, $money) . $scrime . '</strong>' . $stext1);
        }


        $black = DBi::$conn->query('SELECT * FROM blackops WHERE userid = ' . $user_class->id);
        if (mysqli_num_rows($black)) {
            DBi::$conn->query('UPDATE blackops SET missions = missions + 1 WHERE userid = ' . $user_class->id);
        } else {
            DBi::$conn->query('INSERT INTO blackops (userid, missions) VALUES(' . $user_class->id . ', 1)');
        }
        DailyTasks::recordUserTaskAction(DailyTasks::USE_MAX_NERVE, $user_class, $nerve);

        DailyTasks::recordUserTaskAction(DailyTasks::COMMIT_CRIMES, $user_class, 1);


        $user_class->AddToAttribute('crimefailed', 1);
        HatePoints::AddPoint($user_class, HatePoints::TASK_CRIME, false);
        if ($removeNerve) {
            $user_class->RemoveFromAttribute('nerve', $nerve, 'crimes');
        }
//DailyTasks::checkDailies($user_class->id, 'crime');

        if ($_POST['action'] == 'useAllNerve') {
            throw new FailedResult($groupCrimeMsg . '<br><br><font color="darkred">' . CRIME_GROUP_FAILED . '</font> ');
        }
        throw new FailedResult($ftext . '<br><br><font color="darkred">' . CRIME_FAILED . '</font>');
        break;

    default:
        break;
}