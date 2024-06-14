<?php

final class GangWarNegociation extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'gang_wars_negociations';

    public function GetFormattedStatus()
    {
        if ($this->status == 'Started') {
            return '<font color="darkgreen">' . $this->status . '</font>';
        }

        return '<font color="red">' . $this->status . '</font>';
    }

    public static function sGetFormattedStatus($status)
    {
        if ($status == 'Started') {
            return '<font color="darkgreen">' . $status . '</font>';
        }

        return '<font color="red">' . $status . '</font>';
    }

    public function InvolvesGang($gangId)
    {
        return $this->startingGang == $gangId || $this->targetGang == $gangId;
    }

    public static function ExistsBetween(Gang $startingGang, Gang $targetGang)
    {
        $objs = parent::GetAll(
            self::GetDataTableFields(),
            self::GetDataTable(),
            '(`startingGang` = ' . $startingGang->id . ' AND `targetGang` = ' . $targetGang->id . ')
			OR (`startingGang` = ' . $targetGang->id . ' AND `targetGang` = ' . $startingGang->id . ')');

        return count($objs) > 0;
    }

    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable());
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

    public static function Start(Gang $startingGang, Gang $targetGang, $duration = 3, $points = 10, $money = 10000)
    {
        if ($startingGang->id == $targetGang->id) {
            throw new FailedResult(GANG_WAR_NEGOTIATIONS_NOT_PROPOSE_SELF);
        } elseif (GangWarNegociation::ExistsBetween($startingGang, $targetGang) === true) {
            throw new FailedResult(sprintf(GANG_WAR_NEGOTIATIONS_ALREADY, $targetGang->GetFormattedName()));
        } elseif (GangWar::ExistsBetween($startingGang->id, $targetGang->id) === true) {
            throw new FailedResult(sprintf(GANG_WAR_ALREADY, $targetGang->GetFormattedName()));
        }
        $res = parent::AddRecords(
            [
                'startingGang' => $startingGang->id,
                'targetGang' => $targetGang->id,
                'status' => 'Started',
                'duration' => $duration,
                'pointsTribute' => $points,
                'moneyTribute' => $money,
                'startingDate' => time(),
                'endingDate' => time(),
            ],
            self::GetDataTable());
        if ($res === false) {
            throw new SoftException(GANG_WAR_NEGOTIATIONS_CANT_CREATE);
        }

        return $res;
    }

    public static function RefuseLateNegotiations()
    {
        $query = 'UPDATE ' . self::GetDataTable() . ' SET `status` = \'Refused\' WHERE `startingDate` < ' . (time() - 172800) . ' AND `status` = \'Started\'';
        DBi::$conn->query($query);
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }

        return true;
    }

    public static function DeleteRefusedNegotiations()
    {
        $query = 'DELETE FROM ' . self::GetDataTable() . ' WHERE `status` = \'Refused\' AND `startingDate` < ' . (time() - (7 * DAY_SEC)) . '';
        DBi::$conn->query($query);
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }

        return true;
    }

    public function Cancel(Gang $gang)
    {
        if ($this->startingGang != $gang->id) {
            throw new FailedResult(GANG_WAR_NEGOTIATIONS_CANT_CANCEL_NOT_START);
        }
        $this->Delete();

        return true;
    }

    public function Accept(Gang $gang)
    {
        if ($this->targetGang != $gang->id) {
            throw new FailedResult(GANG_WAR_NEGOTIATIONS_CANT_ACCEPT_STARTED);
        } elseif ($gang->vault < $this->moneyTribute || $gang->points < $this->pointsTribute) {
            throw new FailedResult(sprintf(GANG_WAR_NEGOTIATIONS_NOT_MATCH_TRIBUTE, number_format($this->moneyTribute), number_format($this->pointsTribute)));
        }
        $startingGang = new Gang($this->startingGang);
        if ($startingGang->vault < $this->moneyTribute || $startingGang->points < $this->pointsTribute) {
            $userId = User::GetFromUsername($startingGang->leader);
            if ($userId === null) {
                throw new SoftException(GANG_WAR_NEGOTIATIONS_PROBLEM_NOTIFY_LEADER);
            }
            User::SNotify($userId, sprintf(GANG_WAR_NEGOTIATIONS_ACCEPT_OFFER, $gang->GetFormattedName(), number_format($this->moneyTribute), number_format($this->pointsTribute)), GANG_WARS);
            throw new FailedResult(sprintf(GANG_WAR_NEGOTIATIONS_NOT_MATCH_TRIBUTE_REQ, number_format($this->moneyTribute), number_format($this->pointsTribute)));
        }

        GangWar::CheckRequirements($this->startingGang, $this->targetGang);

        if ($gang->RemoveFromAttribute('vault', $this->moneyTribute)
        && $gang->RemoveFromAttribute('points', $this->pointsTribute)) {
            $startingGang->RemoveFromAttribute('vault', $this->moneyTribute);
            $startingGang->RemoveFromAttribute('points', $this->pointsTribute);
        } else {
            throw new SoftException(GANG_WAR_NEGOTIATIONS_PRBLEM_REMOVE_TRIBUTE);
        }
        $this->Validate();
        $id = GangWar::Start($this, 'Bilateral');
        $this->Delete();

        return (int) $id;
    }

    public function StartUnilateralWar(Gang $gang)
    {
        if ($this->startingGang != $gang->id) {
            throw new FailedResult(GANG_WAR_NEGOTIATIONS_NOT_START_UNILATERAL);
        } elseif ($this->status != 'Refused') {
            throw new FailedResult(GANG_WAR_NEGOTIATIONS_NOT_START_UNILATERAL_1);
        }
        GangWar::CheckRequirements($this->startingGang, $this->targetGang);
        $this->Validate();
        $id = GangWar::Start($this, 'Unilateral');
        $this->Delete();

        return $id;
    }

    public function IsValidated()
    {
        if ($this->validated === true) {
            return true;
        }

        return false;
    }

    public function Validate()
    {
        $this->validated = true;
    }

    public function Refuse(Gang $gang)
    {
        if ($this->targetGang != $gang->id) {
            throw new FailedResult(GANG_WAR_NEGOTIATIONS_CANT_REFUSE);
        }
        $this->SetAttribute('status', 'Refused');

        return true;
    }

    public static function DeleteAll()
    {
        return parent::sDelete(self::$dataTable);
    }

    public static function CountAll()
    {
        return parent::sCount(self::$idField, self::$dataTable);
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
            'status',
            'duration',
            'pointsTribute',
            'moneyTribute',
            'startingDate',
            'endingDate',
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
        parent::sDelete(self::GetDataTable(), [$idField => $this->$idField]);

        return true;
    }
}
