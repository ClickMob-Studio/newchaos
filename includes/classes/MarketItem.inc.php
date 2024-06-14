<?php

final class MarketItem extends BaseObject
{
    public static $idField = 'id';

    public static $dataTable = 'itemmarket';

    public function __construct($id)
    {
        try {
            parent::__construct($id);
        } catch (SoftException $e) {
            throw new FailedResult(ITEMMARKET_ALREADY_REMOVED);
        }
    }

    public static function GetAllWeapons()
    {
        $objs = [];

        $res = DBi::$conn->query('SELECT DISTINCT `itemid` FROM `itemmarket` where `tipoItem`=0 ORDER BY `itemid` ASC');

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function GetAllArmors()
    {
        $objs = [];

        $res = DBi::$conn->query('SELECT DISTINCT `itemid` FROM `itemmarket` where `tipoItem`=1 ORDER BY `itemid` ASC');

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function GetAllSpeed()
    {
        $objs = [];

        $res = DBi::$conn->query('SELECT DISTINCT `itemid` FROM `itemmarket` where `tipoItem`=4 ORDER BY `itemid` ASC');

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function GetAllOtherItems($tipoItem = 2)
    {
        $objs = [];

        $res = DBi::$conn->query('SELECT DISTINCT `itemid` FROM `itemmarket` where `tipoItem`=' . $tipoItem . ' ORDER BY `itemid` ASC');

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    /*
     * Retrieve all active marketitems
     *
     * @param string $itemType
     *
     * @return array
     */
    public function findAllActive($itemType = null)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('im.id, im.itemid, im.userid, im.cost, im.tipoItem, im.timestamp, im.awake')
            ->from('itemmarket', 'im')
            ->orderBy('timestamp')
        ;
        if ($itemType) {
            $queryBuilder
                ->leftJoin('im', 'items', 'i', 'im.itemid = i.id')
                ->where('i.item_type = :item_type')
                ->setParameter('item_type', $itemType)
            ;
        }

        $itemmarkets = $queryBuilder->execute()->fetchAll();

        foreach ($itemmarkets as $key => $itemmarket) {
            $itemmarkets[$key]['user'] = UserFactory::getInstance()->getUser($itemmarket['userid']);
            $itemmarkets[$key]['item'] = new Item($itemmarket['itemid']);
        }

        return $itemmarkets;
    }

    public static function FindItems($itemType = 'All', $specialCondition = 'All', $userId = null)
    {
        $objs = [];

        $query = 'SELECT `id`, `itemid`, `userid`, `cost`, `tipoItem`, `timestamp`, `awake` FROM `itemmarket` ';

        if ($itemType != 'All') {
            $query .= ' WHERE ';

            if ($itemType == 'Weapons') {
                $query .= ' `tipoItem` = 0 ';
            } elseif ($itemType == 'Armors') {
                $query .= ' `tipoItem` = 1 ';
            } elseif ($itemType == 'Speed') {
                $query .= ' `tipoItem` = 4 ';
            } elseif ($itemType == 'Items') {
                $query .= ' `tipoItem` = 2 ';
            } elseif ($itemType == 'UpgradeItems') {
                $query .= ' `tipoItem` = 3 ';
            } elseif ($itemType > 0) {
                $query .= ' `itemid`=\'' . $itemType . '\'';
            }

            if ($specialCondition == 'mine' && $userId !== null) {
                $query .= ' AND `userid`=\'' . $userId . '\' ';
            }
        } elseif ($itemType == 'All') {
            if ($specialCondition == 'mine' && $userId !== null) {
                $query .= ' WHERE `userid`=\'' . $userId . '\' ';
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
