<?php

final class GrowingLand extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'growing';

    public static function GetAllForUser($userId)
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`userid`=\'' . $userId . '\'');
    }

    public static function GetAllHarvestableForUser(User $user)
    {
        $cities = City::GetAll();
        /*$condition = 'IN (';
        foreach ($cities as $city)
        {
            if ($city->levelreq <= $user->level)
                $condition .= '\''.$city->id.'\', ';
        }
        if (count($cities) > 0)
            $condition = substr($condition, 0, strlen($condition) - 2);
        $condition .= ')';*/
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '`userid`=\'' . $user->id . '\' AND `timedone` <= ' . time() . ' AND `cityid` ' . $condition);
    }

    public static function GetAllForUserAndCity($uid, $cid)
    {
        $i = 0;
        $objs = [];
        $res = DBi::$conn->query('SELECT `' . implode('`, `', self::GetDataTableFields()) . '` FROM `growing` WHERE `cityid`=\'' . $cid . '\' AND `userid`=\'' . $uid . '\'');
        if (mysqli_num_rows($res) == 0) {
            return null;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[$i++] = $obj;
        }

        return $objs;
    }

    public function Delete()
    {
        return parent::sDelete(self::GetDataTable(), ['id' => $this->id]);
    }

    public static function Add($userid, $cityid, $amount, $croptype)
    {
        $time = time();
        DBi::$conn->query('INSERT INTO `growing` (`userid`, `cityid`, `amount`, `croptype`, `cropamount`, `timeplanted`, `timedone`) VALUES (\'' . $userid . '\', \'' . $cityid . '\', \'' . $amount . '\', \'' . $croptype . '\', \'' . ($amount * 100) . '\', ' . $time . ', \'' . ($time + 604800) . '\')');
        if (DBi::$conn->affected_rows == 0) {
            throw new SoftException(GROWING_LAND_ERR_PLANTING);
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
            'userid',
            'cityid',
            'amount',
            'fertilizer',
            'croptype',
            'cropamount',
            'timeplanted',
            'timedone',
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
}
