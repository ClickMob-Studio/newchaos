<?php

final class GangWar extends BaseObject
{
    public static $idField = 'id';

    public static $dataTable = 'gang_wars';

    public function IsBilateral()
    {
        return $this->type == 'Bilateral';
    }

    public function IsUnilateral()
    {
        return $this->type == 'Unilateral';
    }

    public function InvolvesGang($gangId)
    {
        return $this->startingGang == $gangId || $this->targetGang == $gangId;
    }

    public function HasEnded()
    {
        return $this->endingDate < time();
    }

    public static function EndAllFinished()
    {
        $finishedWars = GangWar::GetAllFinished();

        foreach ($finishedWars as $finishedWar) {
            $war = new GangWar($finishedWar->id);

            $war->End();
        }

        return true;
    }

    public function GetAllWarMembers()
    {
        $res = parent::GetAll(['User', 'originalGang', 'targetGang', 'GangWar'], 'gang_wars_members', '`GangWar` = ' . $this->id);

        $users = [];

        foreach ($res as $entry) {
            $entry->formattedName = User::SGetFormattedName($entry->User, 'Low');

            $users[] = $entry;
        }

        return $users;
    }

    public function GetAlliedWarMembers(Gang $gang)
    {
        $res = parent::GetAll(['User', 'originalGang', 'targetGang', 'GangWar'], 'gang_wars_members', '`GangWar` = ' . $this->id . ' AND `originalGang`=' . $gang->id);

        $users = [];

        $ids = [];

        foreach ($res as $entry) {
            $ids[] = $entry->User;
        }

        $users = User::GetAllFromIdentifiers($ids);

        foreach ($res as $entry) {
            $users[$entry->User]->User = $entry->User;

            $users[$entry->User]->formattedName = User::SGetFormattedName($entry->User, 'Low');

            $users[$entry->User]->onlineStatus = User::GetOnlineStatus($users[$entry->User]->lastactive);

            $users[$entry->User]->warObj = $entry;
        }

        return $users;
    }

    public function GetEnemyWarMembers(Gang $gang)
    {
        $res = parent::GetAll(['User', 'originalGang', 'targetGang', 'GangWar'], 'gang_wars_members', '`GangWar` = ' . $this->id . ' AND `targetGang`=' . $gang->id);

        $users = [];

        $ids = [];

        foreach ($res as $entry) {
            $ids[] = $entry->User;
        }

        $users = User::GetAllFromIdentifiers($ids);

        foreach ($res as $entry) {
            $users[$entry->User]->formattedName = User::SGetFormattedName($entry->User, 'Low');

            $users[$entry->User]->onlineStatus = User::GetOnlineStatus($users[$entry->User]->lastactive);

            $users[$entry->User]->warObj = $entry;
        }

        return $users;
    }

    public function GetXAlliedWarMembers(Gang $gang)
    {
        $query = 'SELECT gang_wars_members.User, gang_wars_members.originalGang, gang_wars_members.targetGang, gang_wars_members.GangWar, grpgusers.* FROM gang_wars_members, grpgusers WHERE gang_wars_members.User = grpgusers.id AND `GangWar` = ' . $this->id . ' AND `originalGang`=' . $gang->id . ' ORDER BY grpgusers.level desc , grpgusers.exp DESC';

        $users = self::GetPaginationResults($query, 'apage');

        foreach ($users as $id => $entry) {
            $users[$id]->formattedName = User::SGetFormattedName($entry->User, 'Low');

            $users[$id]->onlineStatus = User::GetOnlineStatus($users[$id]->lastactive);

            //$users[$id]->warObj = $entry;

            //To calculate total points
            $strSQL = "SELECT *  

									 FROM `gang_wars_logs` 

									 WHERE `War`='" . $this->id . "' AND (`attackingUser`='" . $entry->User . "')";

            $rsResult = DBi::$conn->query($strSQL);

            $totalEarned = 0;

            while ($arrResult = mysqli_fetch_array($rsResult)) {
                $totalEarned = $totalEarned + $arrResult['earnedWPoints'];

                if ($arrResult['attackingUser'] == $arrResult['winningUser']) {
                   // $totalEarned = $totalEarned + 1;
                }

                if ($arrResult['attackingUser'] != $arrResult['winningUser']) {
                    //$totalEarned = $totalEarned - 1;
                }
            }

            $users[$id]->totalPoints = $totalEarned;

            $mytime = strtotime('today midnight');

            $strSQL .= ' AND `date` >	 ' . $mytime ;

            $rsResult = DBi::$conn->query($strSQL);

            $totalEarned = 0;

            while ($arrResult = mysqli_fetch_array($rsResult)) {
                $totalEarned = $totalEarned + $arrResult['earnedWPoints'];

                if ($arrResult['attackingUser'] == $arrResult['winningUser']) {
                   // $totalEarned = $totalEarned + 1;
                }

                if ($arrResult['attackingUser'] != $arrResult['winningUser']) {
                    $totalEarned = $totalEarned - 1;
                }
            }

            $users[$id]->todayPoints = $totalEarned;

            $intGrandTodayTotal = $intGrandTodayTotal + $users[$id]->todayPoints;

            $intGrandTotal = $intGrandTotal + $users[$id]->totalPoints;
        }

        $users['GrandTotal'] = $intGrandTotal;

        $users['GrandTodayTotal'] = $intGrandTodayTotal;

        return $users;
    }

    public function GetXEnemyWarMembers(Gang $gang)
    {
        $query = 'SELECT gang_wars_members.User, gang_wars_members.originalGang, gang_wars_members.targetGang, gang_wars_members.GangWar, grpgusers.id memberid, grpgusers.* FROM gang_wars_members, grpgusers WHERE gang_wars_members.User = grpgusers.id AND `GangWar` = ' . $this->id . ' AND `targetGang`=' . $gang->id . ' ORDER BY grpgusers.level DESC, grpgusers.exp DESC';

        $users = self::GetPaginationResults($query, 'epage');

        foreach ($users as $id => $entry) {
            $users[$id]->formattedName = User::SGetFormattedName($entry->User, 'Low');

            $users[$id]->onlineStatus = User::GetOnlineStatus($users[$id]->lastactive);

            //$users[$id]->warObj = $entry;
        }

        return $users;
    }

    public static function AddToPool($money = 0, $points = 0)
    {
        if ($money < 0 || $points < 0) {
            return false;
        }

        $query = 'UPDATE `gang_wars_pool` SET `money` = `money`+\'' . $money . '\', `points` = `points` + \'' . $points . '\' LIMIT 1';

        DBi::$conn->query($query);

        if (DBi::$conn->affected_rows == 0) {
            return false;
        }

        return true;
    }

    public static function GetPool()
    {
        $pool = ['money' => 0, 'points' => 0];

        $res = DBi::$conn->query('SELECT `money`, `points` FROM `gang_wars_pool` LIMIT 1');

        if (DBi::$conn->num_rows($res) == 0) {
            return $pool;
        }

        return mysqli_fetch_array($res, MYSQLI_ASSOC);
    }

    public static function ResetPool()
    {
        DBi::$conn->query('UPDATE `gang_wars_pool` SET `money`=0, `points`=0 ');

        if (DBi::$conn->affected_rows == 0) {
            return false;
        }

        return true;
    }

    public function GetWinnerName($type)
    {
        if ($type == 'Start') {
            $query = 'SELECT winninguser as mvm,SUM(earnedWPoints) as points 

								FROM gang_wars_logs 

								WHERE war=' . $this->id . ' AND attackingGang=' . $this->startingGang . ' AND earnedwpoints>0

								GROUP BY winninguser 

								ORDER BY sum(earnedWPoints) DESC';
        }

        if ($type == 'Target') {
            $query = 'SELECT winninguser as mvm,SUM(earnedWPoints) as points 

								FROM gang_wars_logs 

								WHERE war=' . $this->id . ' AND attackingGang=' . $this->targetGang . ' AND earnedwpoints>0

								GROUP BY winninguser 

								ORDER BY sum(earnedWPoints) DESC';
        }

        $rsUsers = DBi::$conn->query($query);

        $arrUser = mysqli_fetch_array($rsUsers);

        return $arrUser;
    }

    public function End()
    {
        if (isset($this->ended) && $this->ended == true) {
            throw new SoftException(GANG_WAR_ALREADY_ENDED);
        }
        $this->SetAttribute('endingDate', time() - 1);

        $startingGang = new Gang($this->startingGang);

        $targetGang = new Gang($this->targetGang);

        $startingLeader = UserFactory::getInstance()->getUser(User::GetFromUsername($startingGang->leader));

        $targetLeader = UserFactory::getInstance()->getUser(User::GetFromUsername($targetGang->leader));

        $winnerGang = null;

        $loserGang = null;

        if ($this->type == 'Bilateral') {
            if ($this->startingGangWPoints == $this->targetGangWPoints) {
                $startingGang->AddToAttribute('vault', $this->moneyTribute);
                $startingGang->AddToAttribute('points', $this->pointsTribute);

                $targetGang->AddToAttribute('vault', $this->moneyTribute);

                $targetGang->AddToAttribute('points', $this->pointsTribute);

                $startingLeader->Notify(sprintf(GANG_WAR_DRAW, $targetGang->GetFormattedName()), GANG_WARS);

                $targetLeader->Notify(sprintf(GANG_WAR_DRAW, $startingGang->GetFormattedName()), GANG_WARS);
            } elseif ($this->startingGangWPoints > $this->targetGangWPoints) {
                $wonMoney = (int) floor($this->moneyTribute * 2);

                $wonPoints = (int) floor($this->pointsTribute * 2);

                $prizeMoney = (int) floor($this->moneyTribute * 0.1);

                $prizePoints = (int) floor($this->pointsTribute * 0.1);

              //  GangWar::AddToPool($prizeMoney, $prizePoints);

                $startingGang->AddToAttribute('vault', $wonMoney);

                $startingGang->AddToAttribute('points', $wonPoints);

                $winnerGang = $startingGang;

                $loserGang = $targetGang;
                $loserGang->AddToAttribute('points', $this->startingGangWPoints);
                $winnerGang->AddToAttribute('points', $this->startingGangWPoints * 3);
                $arrWinner = $this->GetWinnerName('Start');

                $startingLeader->Notify(sprintf(GANG_WAR_ENDED, $targetGang->GetFormattedName(), number_format($wonMoney), number_format($wonPoints), User::SGetFormattedName($arrWinner['mvm']), $arrWinner['points']), GANG_WARS);

                $targetLeader->Notify(sprintf(GANG_WAR_ENDED_1, $startingGang->GetFormattedName()), GANG_WARS);

                $arrWinner = $this->GetWinnerName('Start');

                $startingLeader->Notify(sprintf(GANG_WAR_ENDED_2, $targetGang->GetFormattedName(), User::SGetFormattedName($arrWinner['mvm']), $arrWinner['points']), GANG_WARS);

                $gangUsers = DBi::$conn->query("SELECT id FROM grpgusers WHERE gang = " . $startingGang->id);
                while($row = mysqli_fetch_assoc($gangUsers)) {
                    $gangUser = new User($row['id']);
                    if ($gangUser->getWarPointsEarnedForWar($this->id)) {
                        $gangUser->AddItems(Item::GetItemId('DISK_NAME'), 5);
                        $gangUser->AddItems(Item::GetItemId('CAR_BOMB_NAME'), 1);

                        $msg = 'Congratulations, your regiment won it\'s war and you have been rewarded with 5 x Medals & 1 Car Bomb!';
                        $gangUser->Notify($msg, 'War Prize');
                    }
                }
            } elseif ($this->startingGangWPoints < $this->targetGangWPoints) {
                $wonMoney = (int) floor($this->moneyTribute * 2);

                $wonPoints = (int) floor($this->pointsTribute * 2);

                $prizeMoney = (int) floor($this->moneyTribute * 0.1);

                $prizePoints = (int) floor($this->pointsTribute * 0.1);

                //GangWar::AddToPool($prizeMoney, $prizePoints);

                $targetGang->AddToAttribute('vault', $wonMoney);

                $targetGang->AddToAttribute('points', $wonPoints);

                $winnerGang = $targetGang;

                $loserGang = $startingGang;

                $loserGang->AddToAttribute('points', $this->startingGangWPoints);
                $winnerGang->AddToAttribute('points', $this->startingGangWPoints * 3);
                $arrWinner = $this->GetWinnerName('Target');

                $targetLeader->Notify(sprintf(GANG_WAR_ENDED, $startingGang->GetFormattedName(), number_format($wonMoney), number_format($wonPoints), User::SGetFormattedName($arrWinner['mvm']), $arrWinner['points']), GANG_WARS);

                $startingLeader->Notify(sprintf(GANG_WAR_ENDED_1, $targetGang->GetFormattedName()), GANG_WARS);

                $arrWinner = $this->GetWinnerName('Target');

                $targetLeader->Notify(sprintf(GANG_WAR_ENDED_2, $startingGang->GetFormattedName(), User::SGetFormattedName($arrWinner['mvm']), $arrWinner['points']), GANG_WARS);

                $gangUsers = DBi::$conn->query("SELECT id FROM grpgusers WHERE gang = " . $targetGang->id);
                while($row = mysqli_fetch_assoc($gangUsers)) {
                    $gangUser = new User($row['id']);
                    if ($gangUser->getWarPointsEarnedForWar($this->id)) {
                        $gangUser->AddItems(Item::GetItemId('DISK_NAME'), 5);
                        $gangUser->AddItems(Item::GetItemId('CAR_BOMB_NAME'), 1);

                        $msg = 'Congratulations, your regiment won it\'s war and you have been rewarded with 5 x Medals & 1 Car Bomb!';
                        $gangUser->Notify($msg, 'War Prize');
                    }
                }
            }
        } elseif ($this->type == 'Unilateral') {
            if ($this->startingGangWPoints == $this->targetGangWPoints) {
                $startingLeader->Notify(sprintf(GANG_WAR_DRAW_UNI, $targetGang->GetFormattedName()), GANG_WARS);

                $targetLeader->Notify(sprintf(GANG_WAR_DRAW_UNI, $startingGang->GetFormattedName()), GANG_WARS);
            } elseif ($this->startingGangWPoints > $this->targetGangWPoints) {
                $winnerGang = $startingGang;

                $loserGang = $targetGang;

                $arrWinner = $this->GetWinnerName('Start');

                $startingLeader->Notify(sprintf(GANG_WAR_ENDED_UNI_1, $targetGang->GetFormattedName(), User::SGetFormattedName($arrWinner['mvm']), $arrWinner['points']), GANG_WARS);

                $targetLeader->Notify(sprintf(GANG_WAR_ENDED_UNI_2, $startingGang->GetFormattedName()), GANG_WARS);

                $arrWinner = $this->GetWinnerName('Start');

                $startingLeader->Notify(sprintf(GANG_WAR_ENDED_UNI_3, $targetGang->GetFormattedName(), User::SGetFormattedName($arrWinner['mvm']), $arrWinner['points']));
            } elseif ($this->startingGangWPoints < $this->targetGangWPoints) {
                $winnerGang = $targetGang;

                $loserGang = $startingGang;

                $arrWinner = $this->GetWinnerName('Target');

                $targetLeader->Notify(sprintf(GANG_WAR_ENDED_UNI_1, $startingGang->GetFormattedName(), User::SGetFormattedName($arrWinner['mvm']), $arrWinner['points']), GANG_WARS);

                $startingLeader->Notify(sprintf(GANG_WAR_ENDED_UNI_2, $targetGang->GetFormattedName()), GANG_WARS);

                $arrWinner = $this->GetWinnerName('Target');

                $targetLeader->Notify(sprintf(GANG_WAR_ENDED_UNI_3, $startingGang->GetFormattedName(), User::SGetFormattedName($arrWinner['mvm']), $arrWinner['points']));
            }
        }

        // Computing WOF points if there are any

       // $this->ComputeWOFPoints($startingGang, $targetGang, $winnerGang, $loserGang);

        // We insert the war in war logs

        GangWarFinished::Add($this);

        //Send Pmail to gang war memebter

        //User Story US143: Add war notifications

        $time = time();

        $subject = WAR_NOTIFICATION_END_SUB;

        $startingGang = addslashes(Gang::SGetPublicFormattedName($this->startingGang));

        $targetGang = addslashes(Gang::SGetPublicFormattedName($this->targetGang));

        if ($this->startingGangWPoints == $this->targetGangWPoints) {
            $message = sprintf(WAR_NOTIFICATION_DRAW_MSG, $startingGang, $targetGang, $this->startingGangWPoints);
        } elseif ($this->startingGangWPoints > $this->targetGangWPoints) {
            $message = sprintf(WAR_NOTIFICATION_END_MSG, $startingGang, $targetGang, $startingGang, $this->startingGangWPoints, $this->targetGangWPoints);
        } else {
            $message = sprintf(WAR_NOTIFICATION_END_MSG, $startingGang, $targetGang, $targetGang, $this->targetGangWPoints, $this->startingGangWPoints);
        }

        $members = $this->GetAllWarMembers();

        foreach ($members as $member) {
            Pms::Add($member->User, ANONYMOUS_GUARD_ID, $time, $subject, $message);
        }

        if ($this->startingGangWPoints > $this->targetGangWPoints) {
            $members = $this->GetAlliedWarMembers(new Gang($this->startingGang));
        } else {
            $members = $this->GetAlliedWarMembers(new Gang($this->targetGang));
        }

        $this->Delete();

        $this->ended = true;

        return true;
    }

    public static function ExistsBetween($startingGangId, $targetGangId)
    {
        $wars = parent::GetAll(self::GetDataTableFields(),
            self::GetDataTable(),
            '(`startingGang` = ' . $startingGangId . ' AND `targetGang` = ' . $targetGangId . ')

			OR (`startingGang` = ' . $targetGangId . ' AND `targetGang` = ' . $startingGangId . ')');

        return count($wars) > 0;
    }

    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable());
    }

    public static function GetBetweenUsers(User $firstUser, User $secondUser)
    {
        return self::GetBetweenGangs($firstUser->GetGang()->id, $secondUser->GetGang()->id);
    }

    public static function GetBetweenGangs($startingGangId, $targetGangId)
    {
        $wars = parent::GetAll(self::GetDataTableFields(),
            self::GetDataTable(),
            '(`startingGang` = ' . $startingGangId . ' AND `targetGang` = ' . $targetGangId . ')

			OR (`startingGang` = ' . $targetGangId . ' AND `targetGang` = ' . $startingGangId . ')');

        if (count($wars) == 1) {
            return $wars[0];
        }

        return $wars;
    }

    public static function GetAllFinished()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`endingDate` <= ' . time() . ' OR `startingGangWPoints` >= \'' . GWAR_FINISH_WPOINTS . '\' OR `targetGangWPoints` >= \'' . GWAR_FINISH_WPOINTS . '\'');
    }

    public static function GetAllFromGang(Gang $gang)
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`startingGang` = ' . $gang->id, false, false, 'startingDate', 'DESC');
    }

    public static function GetAllOnGang(Gang $gang)
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`targetGang` = ' . $gang->id, false, false, 'startingDate', 'DESC');
    }

    public static function GetAllWithGang(Gang $gang)
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '(`startingGang` = ' . $gang->id . ' OR `targetGang` = ' . $gang->id . ')', false, false, 'startingDate', 'DESC');
    }

    public static function GetAllWithGangInTimeline(Gang $gang)
    {
        $openWars = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '(`startingGang` = ' . $gang->id . ' OR `targetGang` = ' . $gang->id . ') AND `startingDate` > \'' . (time() - 864000) . '\'', false, false, 'startingDate', 'DESC');

        $finishedWars = GangWarFinished::GetAllWithGangInTimeline($gang);

        return array_merge($openWars, $finishedWars);

        //return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '(`startingGang` = '.$gang->id.' OR `targetGang` = '.$gang->id.') AND `startingDate` > \''.(time() - 864000).'\'', false, false, 'startingDate', 'DESC');
    }

    public static function CanGetWOFPoints(Gang $startingGang, Gang $targetGang)
    {
        $self_reason = [];

        // Wars in last 10 days check

        $wars = GangWarFinished::GetAllWithGangsInLast10Days($startingGang, $targetGang);

        if (count($wars) > 0) {
            $self_reason[] = 'There was already a war between these regiments in the last 10 days';
        }


        return $self_reason;
    }

    public static function CheckRequirements($startingGangId, $targetGangId)
    {
        // Checks if the gang does not match minimum requirements

        $startingGang = new Gang($startingGangId);

        $targetGang = new Gang($targetGangId);

        // First we check pure gang requirements
        $sMembers = User::GetGangMembersByLevel($startingGangId);

        if (count($sMembers) < GWAR_MIN_GANG_MEMBERS) {
            throw new FailedResult(sprintf(GANG_WAR_CANT_START_NOT_ENOUGH_MEM, $startingGang->GetFormattedName(), GWAR_MIN_GANG_MEMBERS));
        }
        $tMembers = User::GetGangMembersByLevel($targetGangId);

        if (count($tMembers) < GWAR_MIN_GANG_MEMBERS) {
            throw new FailedResult(sprintf(GANG_WAR_CANT_START_NOT_ENOUGH_MEM, $targetGang->GetFormattedName(), GWAR_MIN_GANG_MEMBERS));
        }

        // Then we check the number of running wars

        $sWars = GangWar::GetAllWithGangInTimeline($startingGang);

        if (count($sWars) >= GWAR_MAX_RUNNING_WARS) {
            throw new FailedResult(sprintf(GANG_WAR_CANT_START_MAX_WARS, $startingGang->GetFormattedName(), GWAR_MAX_RUNNING_WARS));
        }
        $tWars = GangWar::GetAllWithGangInTimeline($targetGang);

        if (count($tWars) >= GWAR_MAX_RUNNING_WARS) {
            throw new FailedResult(sprintf(GANG_WAR_CANT_START_MAX_WARS, $targetGang->GetFormattedName(), GWAR_MAX_RUNNING_WARS));
        }

        return true;
    }

    public static function Start(GangWarNegociation $negociation = null, $type = 'Bilateral')
    {
        if ($negociation === null) {
            throw new FailedResult(GANG_WAR_CANT_WITHOUT_NEGOCIATIONS);
        } elseif (!isset($negociation->validated)) {
            throw new SoftException(GANG_WAR_NEGOCIATIONS_NOT_VALIDATED);
        } elseif ($negociation->startingGang == $negociation->targetGang) {
            throw new FailedResult(GANG_WAR_CANT_WITH_OWN_GANG);
        } elseif (GangWar::ExistsBetween($negociation->startingGang, $negociation->targetGang) === true) {
            throw new FailedResult(GANG_WAR_ALREADY_WITH_GANG);
        }
        $time = time();

        $self_reason = self::CanGetWOFPoints(new Gang($negociation->startingGang), new Gang($negociation->targetGang));

        if (count($self_reason) == 0) {
            $countPoints = true;
        } else {
            $countPoints = false;
        }

        $res = parent::AddRecords([
            'startingGang' => $negociation->startingGang,

            'targetGang' => $negociation->targetGang,

            'type' => $type,

            'startingGangWPoints' => GWAR_START_WPOINTS,

            'targetGangWPoints' => GWAR_START_WPOINTS,

            'pointsTribute' => $negociation->pointsTribute,

            'moneyTribute' => $negociation->moneyTribute,

            'startingDate' => $time,

            'endingDate' => $time + ($negociation->duration * 86400),

            'countPoints' => $countPoints,

            'reason' => DBi::$conn->real_escape_string(serialize($self_reason)),
        ],
            self::GetDataTable());

        if ($res === false) {
            throw new SoftException(GANG_WAR_CANT_CREATE_NEW_WAR);
        }
        //Send Pmail to gang war memebter

        //User Story US143: Add war notifications

        $time = time();

        $subject = WAR_NOTIFICATION_START_SUB;

        $message = sprintf(WAR_NOTIFICATION_START_MSG, addslashes(Gang::SGetPublicFormattedName($negociation->startingGang)), addslashes(Gang::SGetPublicFormattedName($negociation->targetGang)));

        // Adding all starting gang members as authorized hitters.

        $startingGangMembers = User::GetGangMembers($negociation->startingGang);

        foreach ($startingGangMembers as $startingGangMember) {
            HatePoints::AddPoint(UserFactory::getInstance()->getUser($startingGangMember->id), HatePoints::TASK_WAR); //add hate points

            Pms::Add($startingGangMember->id, ANONYMOUS_GUARD_ID, $time, $subject, $message);

            parent::AddRecords([
                'User' => $startingGangMember->id,

                'originalGang' => $negociation->startingGang,

                'targetGang' => $negociation->targetGang,

                'GangWar' => $res,
            ],
                'gang_wars_members');
        }

        // Adding all target gang members as authorized hitters.

        $targetGangMembers = User::GetGangMembers($negociation->targetGang);

        foreach ($targetGangMembers as $targetGangMember) {
            Pms::Add($targetGangMember->id, ANONYMOUS_GUARD_ID, $time, $subject, $message);

            parent::AddRecords([
                'User' => $targetGangMember->id,

                'originalGang' => $negociation->targetGang,

                'targetGang' => $negociation->startingGang,

                'GangWar' => $res,
            ],
                'gang_wars_members');
        }

        return $res;
    }

    public static function DeleteAll()
    {
        parent::sDelete(self::GetDataTable());
    }

    public function GetSGangFormattedWPoints()
    {
        return self::sGetSGangFormattedWPoints($this->startingGangWPoints, $this->targetGangWPoints);
    }

    public function GetTGangFormattedWPoints()
    {
        return self::sGetSGangFormattedWPoints($this->startingGangWPoints, $this->targetGangWPoints);
    }

    public static function sGetSGangFormattedWPoints($startingGangWPoints, $targetGangWPoints)
    {
        if ($startingGangWPoints == $targetGangWPoints) {
            return '<font color="#ffee11">' . $startingGangWPoints . '</font>';
        } elseif ($startingGangWPoints > $targetGangWPoints) {
            return '<font color="darkgreen">' . $startingGangWPoints . '</font>';
        }

        return '<font color="red">' . $startingGangWPoints . '</font>';
    }

    public static function sGetTGangFormattedWPoints($startingGangWPoints, $targetGangWPoints)
    {
        if ($startingGangWPoints == $targetGangWPoints) {
            return '<font color="#ffee11">' . $targetGangWPoints . '</font>';
        } elseif ($startingGangWPoints < $targetGangWPoints) {
            return '<font color="darkgreen">' . $targetGangWPoints . '</font>';
        }

        return '<font color="red">' . $targetGangWPoints . '</font>';
    }

    public static function CountAll()
    {
        return parent::sCount(self::GetIdentifierFieldName(), self::GetDataTable());
    }

    /*

     * Handles the calculation of points according to winning / losing user

     */

    public function CountAttack(User $attackingUser, User $defendingUser, User $winningUser, User $losingUser)
    {
        $wonPoints = 0;
        if ($attackingUser->id == $winningUser->id) {
            if ($winningUser->level - $losingUser->level >= 51) {
                $wonPoints = 1;
            } elseif ($winningUser->level - $losingUser->level >= 3) {
                $wonPoints = 2;
            } elseif (abs($winningUser->level - $losingUser->level) <= 2) {
                $wonPoints = 3;
            } else {
                $wonPoints = 4;
            }
        } elseif ($attackingUser->id == $losingUser->id) {
            $wonPoints = -1;
        }
        $attackingGang = $attackingUser->GetWarGang($defendingUser);

        $defendingGang = $defendingUser->GetWarGang($attackingUser);

        if ($wonPoints == 0) {
            GangWarLog::Add($this, $attackingUser, $defendingUser, $winningUser, $attackingGang, $defendingGang, $wonPoints);

            throw new FailedResult(sprintf(GANG_WAR_NOT_CONTRIBUTE, $attackingGang->GetFormattedName()), GANG_WARS);
        }

        if ($wonPoints > 0) {
            $wonPoints = $wonPoints * $winningUser->GetWarPointsMultiplier();

            if ($attackingGang->id == $this->startingGang) {
                $this->AddToAttribute('startingGangWPoints', $wonPoints);
                $this->RemoveFromAttribute('targetGangWPoints', -1);
                if ($this->startingGangWPoints >= GWAR_FINISH_WPOINTS && $this->HasEnded() === false) {
                    $this->End();

                    GangWarLog::Add($this, $attackingUser, $defendingUser, $winningUser, $attackingGang, $defendingGang, $wonPoints);

                    throw new SuccessResult(sprintf(GANG_WAR_ATK_MADE_WIN, $attackingGang->GetFormattedName()), GANG_WARS);
                }
            } else {
                $this->AddToAttribute('targetGangWPoints', $wonPoints);
                $this->RemoveFromAttribute('startingGangWPoints', -1);

                if ($this->targetGangWPoints >= GWAR_FINISH_WPOINTS && $this->HasEnded() === false) {
                    $this->End();

                    GangWarLog::Add($this, $attackingUser, $defendingUser, $winningUser, $attackingGang, $defendingGang, $wonPoints);

                    throw new SuccessResult(sprintf(GANG_WAR_ATK_MADE_WIN, $attackingGang->GetFormattedName()), GANG_WARS);
                }
            }

            GangWarLog::Add($this, $attackingUser, $defendingUser, $winningUser, $attackingGang, $defendingGang, $wonPoints);

            throw new SuccessResult(sprintf(GANG_WAR_ATK_CONTRIBUTED, $attackingGang->GetFormattedName()), GANG_WARS);
        }

        $wonPoints = $wonPoints * $attackingUser->GetWarPointsMultiplier();

        if ($attackingGang->id == $this->startingGang) {
            $this->RemoveFromAttribute('startingGangWPoints', -$wonPoints);
        } else {
            $this->RemoveFromAttribute('targetGangWPoints', -$wonPoints);
        }

        GangWarLog::Add($this, $attackingUser, $defendingUser, $winningUser, $attackingGang, $defendingGang, $wonPoints);

        throw new FailedResult(sprintf(GANG_WAR_ATK_CONTRIBUTED_NEGATIVELY, $attackingGang->GetFormattedName()), GANG_WARS);

        return $wonPoints;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,

            'startingGang',

            'targetGang',

            'type',

            'startingGangWPoints',

            'targetGangWPoints',

            'pointsTribute',

            'moneyTribute',

            'startingDate',

            'endingDate',

            'countPoints',

            'reason',
        ];
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }

    private function ComputeWOFPoints(Gang $startingGang, Gang $targetGang, $winnerGang = null, $loserGang = null)
    {
        $this->startingGangWOFPoints = 0;

        $this->targetGangWOFPoints = 0;

        /*if ($this->CanGetWOFPoints($startingGang, $targetGang) === false && $this->countPoints == 0)

            return false;*/

        if (!$this->countPoints) {
            return false;
        }

        $this->startingGangWOFPoints = $this->startingGangWPoints;
        $this->targetGangWOFPoints = $this->targetGangWPoints;

        if ($this->startingGangWOFPoints != 0) {
            $startingGang->AddToAttribute('WOFPoints', $this->startingGangWOFPoints);
        }

        if ($this->targetGangWOFPoints != 0) {
            $targetGang->AddToAttribute('WOFPoints', $this->targetGangWOFPoints);
        }

        return true;
    }

    private function Delete()
    {
        $idField = self::$idField;

        $idValue = $this->$idField;

        parent::sDelete(self::GetDataTable(), [$idField => $idValue]);

        return true;
    }
}
