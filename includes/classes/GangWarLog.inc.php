<?php

final class GangWarLog extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'gang_wars_logs';

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
        return $this->attackingGang == $gangId || $this->targetGang == $gangId;
    }

    public static function ExistsBetween($attackingGangId, $targetGangId)
    {
        $wars = parent::GetAll(
            self::GetDataTableFields(),
            self::GetDataTable(),
            '(`attackingGang` = ' . $attackingGangId . ' AND `targetGang` = ' . $targetGangId . ')
			OR (`attackingGang` = ' . $targetGangId . ' AND `targetGang` = ' . $attackingGangId . ')');

        return count($wars) > 0;
    }

    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable());
    }

    public static function GetAllForTime($gangid, $days = 7, $timestamp = null)
    {
        $time = time() - ($days * 86400);
        if ($timestamp != null) {
            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), 'date >= \'' . $timestamp . '\' and ( attackingGang=\'' . $gangid . '\' or `targetGang` = ' . $gangid . ')');
        }

        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), 'date >= \'' . $time . '\' and ( attackingGang=\'' . $gangid . '\' or `targetGang` = ' . $gangid . ')');
    }

    public static function GetBetweenUsers(User $firstUser, User $secondUser)
    {
        return self::GetBetweenGangs($firstUser->GetGang()->id, $secondUser->GetGang()->id);
    }

    public static function GetTotalContribution(GangWarFinished $war, User $user, &$gang1, &$gang2)
    {
        $gangid = $user->GetGang()->id;
        $wars = parent::GetAll(
            self::GetDataTableFields(),
            self::GetDataTable(),
            '((`attackingGang` = ' . $war->startingGang . ' AND `targetGang` = ' . $war->targetGang . ')
			OR (`attackingGang` = ' . $war->targetGang . ' AND `targetGang` = ' . $war->startingGang . '))
                        and War=' . $war->id);

        foreach ($wars as $value) {
            if ($value->attackingGang == $gangid) {
                $gang1[$value->attackingUser]->id = $value->attackingUser;
                $gang1[$value->attackingUser]->points += $value->earnedWPoints;
            } else {
                $gang2[$value->attackingUser]->id = $value->attackingUser;
                $gang2[$value->attackingUser]->points += $value->earnedWPoints;
            }
        }
        usort($gang1, 'GangWarLog::orderbyPoints');
        usort($gang2, 'GangWarLog::orderbyPoints');
    }

    public static function orderbyPoints($a, $b)
    {
        if ($a->points > $b->points) {
            return -1;
        }

        if ($a->points < $b->points) {
            return 1;
        }

        return 0;
    }

    public static function GetContribution(GangWarFinished $war, User $user)
    {
        $gangid = $user->GetGang()->id;
        $wars = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), 'War=' . $war->id . ' order by date asc');
        $i = 0;
        foreach ($wars as $key => $value) {
            if ($value->attackingUser == $value->winningUser) {
                $victorious_gang = $value->attackingGang;
            } else {
                $victorious_gang = ($value->attackingGang == $war->startingGang ? $war->targetGang : $war->startingGang);
            }
            $points = $value->earnedWPoints;
            $attackerPoints = $value->attackingGangWPoints;
            $value->player_gang1 = $value->attackingUser;
            $value->player_gang2 = $value->targetUser;

            $value->number_of_points = $value->earnedWPoints;
            ++$i;
        }

        return $wars;
    }

    public static function GetBetweenGangs($attackingGangId, $targetGangId)
    {
        $wars = parent::GetAll(
            self::GetDataTableFields(),
            self::GetDataTable(),
            '(`attackingGang` = ' . $attackingGangId . ' AND `targetGang` = ' . $targetGangId . ')
			OR (`attackingGang` = ' . $targetGangId . ' AND `targetGang` = ' . $attackingGangId . ')');
        if (count($wars) == 1) {
            return $wars[0];
        }

        return $wars;
    }

    public static function GetAllFromGang(Gang $gang)
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`attackingGang` = ' . $gang->id, false, false, 'date0', 'DESC');
    }

    public static function GetAllOnGang(Gang $gang)
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`targetGang` = ' . $gang->id, false, false, 'date', 'DESC');
    }

    public static function GetAllWithGang(Gang $gang, $page = 0, $limit = 100)
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '(`attackingGang` = ' . $gang->id . ' OR `targetGang` = ' . $gang->id . ')', $page, $limit, 'date', 'DESC');
    }

    public static function DeleteAll()
    {
        parent::sDelete(self::GetDataTable());
    }

    public static function CountAll()
    {
        return parent::sCount(self::GetIdentifierFieldName(), self::GetDataTable());
    }

    public static function Add(GangWar $war, User $attackingUser, User $targetUser, User $winningUser, $attackingGang, $targetGang, $wPoints = 0)
    {
        $aWPs = ($attackingGang !== null && $attackingGang->id == $war->startingGang) ? $war->startingGangWPoints : $war->targetGangWPoints;
        $tWPs = ($targetGang !== null && $targetGang->id == $war->startingGang) ? $war->startingGangWPoints : $war->targetGangWPoints;
        $aID = ($attackingGang !== null) ? $attackingGang->id : 0;
        $tID = ($targetGang !== null) ? $targetGang->id : 0;

        $res = parent::AddRecords(
            [
                'War' => $war->id,
                'attackingUser' => $attackingUser->id,
                'targetUser' => $targetUser->id,
                'winningUser' => $winningUser->id,
                'attackingGang' => $aID,
                'targetGang' => $tID,
                'earnedWPoints' => $wPoints,
                'date' => time(),
                'attackingGangWPoints' => $aWPs,
                'targetGangWPoints' => $tWPs, ],
            self::$dataTable);

        return $res;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'War',
            'attackingUser',
            'targetUser',
            'winningUser',
            'attackingGang',
            'targetGang',
            'earnedWPoints',
            'date',
            'attackingGangWPoints',
            'targetGangWPoints',
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

    private function Delete()
    {
        $idField = self::$idField;
        $idValue = $this->$idField;
        parent::sDelete(self::GetDataTable(), [$idField => $idValue]);

        return true;
    }
}
