<?php

final class GangWarFinished extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'gang_wars_finished';

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

    public static function ExistsBetween($startingGangId, $targetGangId)
    {
        $wars = parent::GetAll(
            self::GetDataTableFields(),
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
        $wars = parent::GetAll(
            self::GetDataTableFields(),
            self::GetDataTable(),
            '(`startingGang` = ' . $startingGangId . ' AND `targetGang` = ' . $targetGangId . ')
			OR (`startingGang` = ' . $targetGangId . ' AND `targetGang` = ' . $startingGangId . ')');
        if (count($wars) == 1) {
            return $wars[0];
        }

        return $wars;
    }

    public static function GetAllFromGang(Gang $gang)
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`startingGang` = ' . $gang->id, false, false, 'endingDate', 'DESC');
    }

    public static function GetAllOnGang(Gang $gang)
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`targetGang` = ' . $gang->id, false, false, 'endingDate', 'DESC');
    }

    public static function GetAllWithGang(Gang $gang)
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '(`startingGang` = ' . $gang->id . ' OR `targetGang` = ' . $gang->id . ')', false, false, 'endingDate', 'DESC');
    }

    public static function GetAllWithGangInTimeline(Gang $gang)
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '(`startingGang` = ' . $gang->id . ' OR `targetGang` = ' . $gang->id . ') AND `startingDate` > \'' . (time() - 864000) . '\'', false, false, 'startingDate', 'DESC');
    }

    public static function GetAllWithGangsInLast10Days(Gang $startingGang, Gang $targetGang)
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(),
        '((`startingGang` = ' . $startingGang->id . ' AND `targetGang` = ' . $targetGang->id . ') AND `endingDate` > \'' . (time() - 864000) . '\') OR
		((`startingGang` = ' . $targetGang->id . ' AND `targetGang` = ' . $startingGang->id . ') AND `endingDate` > \'' . (time() - 864000) . '\')
		', false, false, 'endingDate', 'DESC');
    }

    public static function DeleteAll()
    {
        parent::sDelete(self::GetDataTable());
    }

    public static function CountAll()
    {
        return parent::sCount(self::GetIdentifierFieldName(), self::GetDataTable());
    }

    public static function Add(GangWar $war)
    {
        $idField = self::$idField;
        $query = 'INSERT INTO `' . self::$dataTable . '` (`' . implode('`, `', self::GetDataTableFields()) . '`) VALUES
		(\'' . $war->$idField . '\', \'' . $war->startingGang . '\', \'' . $war->targetGang . '\', \'' . $war->type . '\',
		 \'' . $war->startingGangWPoints . '\', \'' . $war->targetGangWPoints . '\', \'' . $war->pointsTribute . '\', 
		 \'' . $war->moneyTribute . '\', \'' . $war->startingDate . '\', \'' . $war->endingDate . '\', \'' . $war->startingGangWPoints . '\'
		 , \'' . $war->targetGangWPoints . '\')';
        DBi::$conn->query($query);
        $startingGang = new Gang($war->startingGang);
        $targetGang = new Gang($war->targetGang);
        $startingGang->AddToAttribute('WOFPoints', $war->startingGangWPoints);
        $targetGang->AddToAttribute('WOFPoints', $war->targetGangWPoints);
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }

        return true;
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
            'startingGangWOFPoints',
            'targetGangWOFPoints',
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
