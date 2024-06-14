<?php

final class GangMarketItem extends BaseObject
{
    public static $idField = 'id';

    public static $dataTable = 'gangitemmarket';

    public function __construct($id)
    {
        try {
            parent::__construct($id);
        } catch (SoftException $e) {
            throw new FailedResult(ITEMMARKET_ALREADY_REMOVED);
        }
    }

    public static function GetAllItemCount($gangid)
    {
        return MySQL::GetSingle('SELECT COUNT(`itemid`) FROM `gangitemmarket` WHERE gangid = \'' . $gangid . '\'');
    }

    public static function GetAllWeapons($gangid)
    {
        $objs = [];

        $res = DBi::$conn->query('SELECT DISTINCT `itemid` FROM `gangitemmarket` where `tipoItem`=0 AND gangid = ' . $gangid . ' ORDER BY `itemid` ASC');

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function GetAllArmors($gangid)
    {
        $objs = [];

        $res = DBi::$conn->query('SELECT DISTINCT `itemid` FROM `gangitemmarket` where `tipoItem`=1 AND gangid = ' . $gangid . ' ORDER BY `itemid` ASC');

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function GetAllSpeed($gangid)
    {
        $objs = [];

        $res = DBi::$conn->query('SELECT DISTINCT `itemid` FROM `gangitemmarket` where `tipoItem`=4 AND gangid = ' . $gangid . ' ORDER BY `itemid` ASC');

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function GetAllOtherItems($gangid, $tipoItem = 2)
    {
        $objs = [];

        $res = DBi::$conn->query('SELECT DISTINCT `itemid` FROM `gangitemmarket` where `tipoItem`=' . $tipoItem . ' AND gangid = ' . $gangid . ' ORDER BY `itemid` ASC');

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function FindItems($gangid, $itemType = 'All', $specialCondition = 'All', $userId = null)
    {
        $objs = [];

        $query = 'SELECT `id`, `itemid`, `userid`, `cost`, `tipoItem`, `timestamp`, `awake` FROM `gangitemmarket` ';

        $query .= ' WHERE  gangid = ' . $gangid;

        if ($itemType != 'All') {
            if ($itemType == 'Weapons') {
                $query .= ' AND `tipoItem` = 0 ';
            } elseif ($itemType == 'Armors') {
                $query .= ' AND `tipoItem` = 1 ';
            } elseif ($itemType == 'Speed') {
                $query .= ' AND `tipoItem` = 4 ';
            } elseif ($itemType == 'Items') {
                $query .= ' AND `tipoItem` = 2 ';
            } elseif ($itemType == 'UpgradeItems') {
                $query .= ' AND `tipoItem` = 3 ';
            } elseif ($itemType > 0) {
                $query .= ' AND `itemid`=\'' . $itemType . '\'';
            }

            if ($specialCondition == 'mine' && $userId !== null) {
                $query .= ' AND `userid`=\'' . $userId . '\' ';
            }
        } elseif ($itemType == 'All') {
            if ($specialCondition == 'mine' && $userId !== null) {
                $query .= ' AND `userid`=\'' . $userId . '\' ';
            }
        }

        $query .= ' ORDER BY `tipoItem` ASC, `cost` ASC,`itemid` ASC';

        if ($specialCondition == '5') {
            $query .= ' LIMIT 5';
        }

        $res = DBi::$conn->query($query);

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public function Delete()
    {
        try {
            $idField = self::GetIdentifierFieldName();

            parent::sDelete(self::GetDataTable(), [$idField => $this->$idField]);
        } catch (SoftException $e) {
            throw new FailedResult(ITEMMARKET_ALREADY_REMOVED);
        }
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,

            'gangid',

            'itemid',

            'userid',

            'cost',

            'tipoItem',

            'timestamp',

            'awake',
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
