<?php

final class MugLog extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'logs_mugs';

    public static function GetDataTableFields()
    {
        return [
            self::$idField,
            'user',
            'targetUser',
            'result',
            'moneyReward',
            'contributedGang',
            'gangMoneyReward',
            'expReward',
            'time',
        ];
    }

    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable());
    }

    public static function GetAllForGang(Gang $gang, $timestamp = null)
    {
        if ($timestamp != null) {
            $string = '`contributedGang` = \'' . $gang->id . '\'' . ($timestamp != null ? ' and time >' . $timestamp . ' ' : '') . '';
        } else {
            $string = '`contributedGang` = \'' . $gang->id . '\' and `reset_log` = 1';
        }

        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $string);
    }

    public static function GetAllForUser(User $user, array $order = ['oby' => 'time', 'sort' => 'DESC'])
    {
        if ($order['oby'] == null) {
            $order['oby'] = 'time';
        }
        if ($order['sort'] == null) {
            $order['sort'] = 'DESC';
        }

        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '(`user` = \'' . $user->id . '\' OR `targetUser` = \'' . $user->id . '\' )', false, false, $order['oby'], $order['sort']);
    }

    public static function DeleteAll()
    {
        parent::sDelete(self::GetDataTable());
    }

    public static function CountAll()
    {
        return parent::sCount(self::GetIdentifierFieldName(), self::GetDataTable());
    }

    public static function Add(User $user, User $targetUser, $result = 'Canceled', $moneyReward = 0, $gangMoneyReward = 0, $expReward = 0)
    {
        return parent::AddRecords(
            [
                'user' => $user->id,
                'targetUser' => $targetUser->id,
                'result' => $result,
                'moneyReward' => $moneyReward,
                'contributedGang' => $user->GetGang()->id,
                'gangMoneyReward' => $gangMoneyReward,
                'expReward' => $expReward,
                'time' => time(),
            ],
            self::GetDataTable());
    }

    /**
     * Function used to reset logs for gang
     * It will just change reset_log field for all mug log entries
     * of all the current gang members to 0 instead of 1.
     *
     * @param $gang Gang
     *
     * @return int number of updated rows
     */
    public static function ResetAllForGang(Gang $gang)
    {
        $memberIds = array_keys($gang->GetAllMembers());
        if (empty($memberIds) || !is_array($memberIds)) {
            throw new FailedResult('Unexpected result (either a gang with no members or Gang::GetAllMembers doesnt return array anymore');
        }
        $str = join(',', $memberIds);
        $query = 'UPDATE `' . self::GetDataTable() . '` SET `reset_log`=0 WHERE `user` IN(' . $str . ') AND `reset_log` = 1';
        DBi::$conn->query($query);

        return DBi::$conn -> affected_rows;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
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
