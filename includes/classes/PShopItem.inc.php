<?php

/**
 * discription: This class is used to manage item for personal shop.
 *
 * @author: Harish<harish282@gmail.com>

 * @name: PShopItem

 * @package: includes

 * @subpackage: classes

 * @final: Final

 * @access: Public

 * @copyright: icecubegaming <http://www.icecubegaming.com>
 */
class PShopItem extends BaseObject
{
    /** Define constatns for status **/
    public static $idField = 'id'; //id field

    public static $dataTable = 'pshop_item'; // table implemented

    /**
     * Constructor.
     */
    public function __construct($id = null)
    {
        if ($id > 0) {
            parent::__construct($id);
        }
    }

    /**
     * Funtions return all returns.
     *
     * @return array
     */
    public static function GetAll(array $options = [])
    {
        return self::GetWhere('', $options);
    }

    public static function GetWhere($where, array $options = [])
    {
        $sql = 'SELECT ps.`' . implode('`, ps.`', self::GetDataTableFields()) . '`,i.itemname, i.offense, i.defense, i.type as itemtype  FROM `' . self::GetDataTable() . '` ps, items i WHERE ps.itemid = i.id ';

        if (!empty($where)) {
            $sql .= ' AND ' . $where;
        }

        if (!empty($options['orderBy'])) {
            $sql .= ' ORDER BY ' . $options['orderBy'];
        }

        if (!empty($options['sort'])) {
            $sql .= ' ' . $options['sort'];
        }

        $objs = self::GetPaginationResults($sql);

        foreach ($objs as $key => $obj) {
            $obj->itemname = constant($obj->itemname);

            $objs[$key] = $obj;
        }

        return $objs;
    }

    public static function sCountWhere($where)
    {
        $query = 'SELECT COUNT(`' . self::$idField . '`) as `totalCount` FROM `' . self::GetDataTable() . '` WHERE ' . $where;

        $res = DBi::$conn->query($query);

        if (mysqli_num_rows($res) == 0) {
            return 0;
        }

        $obj = mysqli_fetch_object($res);

        return $obj->totalCount;
    }

    public static function GetCountForUser($userid)
    {
        $where = 'userid = ' . $userid;

        return self::sCountWhere('userid = ' . $userid);
    }

    public static function GetAllForUser($userid, array $options = [])
    {
        $where = 'userid = ' . $userid;

        if (isset($options['itemType']) && !empty($options['itemType'])) {
            if ($options['itemType'] == 'Weapons') {
                $where .= ' AND i.offense > i.defense';
            } elseif ($options['itemType'] == 'Armors') {
                $where .= '  AND i.defense > i.offense';
            } elseif ($options['itemType'] == 'Misc') {
                $where .= ' AND i.defense = i.offense';
            } elseif (is_numeric($options['itemType'])) {
                $where .= ' AND ps.itemid = ' . $options['itemType'];
            }
        }

        return self::GetWhere($where, $options);
    }

    public static function Add(User $user, $item, $cost, $points, $quantity)
    {
        if (empty($item)) {
            throw new FailedResult(PSHOP_INVALID_ITEM);
        }
        if (0 >= (int) $quantity) {
            throw new FailedResult(PSHOP_INVALID_NUM_QTY);
        }
        if (($cost == 0 || !is_numeric($cost)) && ($points == 0 || !is_numeric($points))) {
            throw new FailedResult(PSHOP_INVALID_COST);
        }
        if (!empty($cost) && ($cost < 0 || !Validation::IsInteger($cost))) {
            throw new FailedResult(PSHOP_INVALID_COST);
        }
        if (!empty($points) && ($points < 0 || !Validation::IsInteger($points))) {
            throw new FailedResult(RPSHOP_INVALID_POINTS_QTY);
        }
        if ($cost < PSHOP_ITEM_MIN_COST && PSHOP_ITEM_MIN_COST > -1 && $cost != 0) {
            throw new FailedResult(sprintf(PSHOP_MIN_LIMIT_ERROR, strtolower(COM_ITEM), number_format(PSHOP_ITEM_MIN_COST)));
        }
        if ($cost > PSHOP_ITEM_MAX_COST && PSHOP_ITEM_MAX_COST > -1 && $cost != 0) {
            throw new FailedResult(sprintf(PSHOP_MAX_LIMIT_ERROR, strtolower(COM_ITEM), number_format(PSHOP_ITEM_MAX_COST)));
        }
        if ($points < PSHOP_ITEM_MIN_POINT && PSHOP_ITEM_MIN_POINT > -1 && $points != 0) {
            throw new FailedResult(sprintf(PSHOP_MIN_POINT_LIMIT_ERROR, strtolower(COM_ITEM), number_format(PSHOP_ITEM_MIN_POINT)));
        }
        if ($points > PSHOP_ITEM_MAX_POINT && PSHOP_ITEM_MAX_POINT > -1 && $points != 0) {
            throw new FailedResult(sprintf(PSHOP_MAX_POINT_LIMIT_ERROR, strtolower(COM_ITEM), number_format(PSHOP_ITEM_MAX_POINT)));
        }
        $itemid = strtok($item, '|');

        $awake = (int) strtok('|');

        $itemObj = new Item($itemid);

//        if ($itemObj->type == Item::AUCTION_ITEM && 101 >= $awake) {
//            throw new FailedResult(ITEM_CANT_SOLD_SEND);
//        }
        $sql = 'SELECT `itemid`, `quantity` FROM `inventory` WHERE `userid` = \'' . $user->id . '\' AND `itemid` = \'' . $itemid . '\' AND  `borrowed`=\'0\'';

        $result = DBi::$conn->query($sql);

        if (mysqli_num_rows($result) <= 0) {
            throw new FailedResult(PSHOP_INVALID_ITEM);
        }
        $itemrow = mysqli_fetch_object($result);

        if ($quantity > $itemrow->quantity) {
            throw new FailedResult(PSHOP_INVALID_QTY . '- 1');
        }
        if (!User::SRemoveItems($itemid, $user->id, $quantity, 0, 0)) {
            throw new FailedResult(PSHOP_INVALID_QTY . '- 2');
        }
        $time = time();

        $data = [
            'userid' => $user->id,

            'itemid' => $itemid,

            'cost' => $cost,

            'points' => $points,

            'qty' => $quantity,

            'timestamp' => $time,

            'awake' => 0,
        ];

        self::AddRecords($data, self::GetDataTable());

        Logs::sAddPShopLog('Supply', $user->id, $user->id, $itemid, $quantity, 'item');

        return true;
    }

    public function Delete()
    {
        $item = new Item($this->itemid);

        $userClass = UserFactory::getInstance()->getUser($this->userid);
        $userClass->AddItems($this->itemid, $this->qty, 0);

        self::sDelete(self::GetDataTable(), ['id' => $this->id]);

        Logs::sAddPShopLog('Removal', $this->userid, $this->userid, $this->itemid, $this->qty, 'item');

        throw new SuccessResult(sprintf(PSHOP_ITEM_DELETED, $this->qty, HTML::ShowItemPopup($item->itemname, $item->id)));
    }

    public function Buy(User $user, $qty)
    {
        if (!is_numeric($qty) || $qty <= 0) {
            throw new FailedResult(PSHOP_INVALID_BUY_QTY);
        }
        if ($qty > $this->qty) {
            throw new FailedResult(PSHOP_INVALID_BUY_QTY);
        }
        $item = new Item($this->itemid);

//        if ($item->type == Item::AUCTION_ITEM && 10 >= $this->awake) {
//            throw new FailedResult(ITEM_CANT_SOLD_SEND);
//        }
        // Lock starting

        if ($this->lock == 1) {
            throw new FailedResult(ITEM_CANT_SOLD_SEND);
        }
        $this->SetAttribute('lock', 1);

        $cost = $qty * $this->cost;

        $points = $qty * $this->points;

        if ($this->points > 0) {
            $owner = UserFactory::getInstance()->getUser($this->userid);

            if ($owner->points + $points > MAX_POINTS) {
                $this->SetAttribute('lock', 0);

                throw new FailedResult(sprintf(RPSHOP_OWNER_POINTS_MAX_ERROR, number_format(MAX_POINTS)));
            }

            if ($points > $user->points) {
                $this->SetAttribute('lock', 0);

                throw new FailedResult(POINTMARKET_NOT_ENOUGH_POINTS);
            }
        }

        if ($this->cost > 0) {
            if ($cost > $user->money) {
                $this->SetAttribute('lock', 0);

                throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
            }
        }

        if ($this->points > 0) {
            if (!$user->RemoveFromAttribute('points', $points)) {
                $this->SetAttribute('lock', 0);

                throw new FailedResult(POINTMARKET_NOT_ENOUGH_POINTS);
            }

            try {
                $owner->AddToAttribute('points', $points);
            } catch (Exception $e) {
            }
        }

        if ($this->cost > 0) {
            if (!$user->RemoveFromAttribute('money', $cost)) {
                $this->SetAttribute('lock', 0);

                throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
            }

            User::SAddBankMoney($this->userid, $cost);
        }

        $awake = 0;

        if ($item->type == Item::AUCTION_ITEM && $this->awake > 10) {
            $awake = $this->awake - 10;
        }

        $user->AddItems($this->itemid, $qty, 0);

        User::SNotify($this->userid, sprintf(PSHOP_ITEM_SOLD, $qty, $item->itemname, '<a href="profiles.php?id=' . $user->id . '">' . $user->username . '</a>', number_format($cost), number_format($points)), ITEMMARKET_ITEM_SOLD);

        if ($qty == $this->qty) {
            self::sDelete(self::GetDataTable(), ['id' => $this->id]);
        } else {
            $this->RemoveFromAttribute('qty', $qty);
        }

        $this->SetAttribute('lock', 0);

        Logs::sAddPShopLog('Transaction', $this->userid, $user->id, $this->itemid, $qty, 'item', $cost, $points);

        throw new SuccessResult(sprintf(PSHOP_ITEM_PURCHASED, $qty, HTML::ShowItemPopup($item->itemname, $item->id), number_format($cost), number_format($points)));
    }

    /**
     * Function used to get the data table name which is implemented by class.
     *

     * @return string
     */
    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    /**
     * Returns the fields of table.

     *

     * @return array
     */
    protected static function GetDataTableFields()
    {
        return [
            self::$idField,

            'itemid',

            'userid',

            'cost',

            'points',

            'qty',

            'timestamp',

            'awake',

            'lock',
        ];
    }

    /**
     * Returns the identifier field name.
     *

     * @return mixed
     */
    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    /**
     * Function returns the class name.
     *

     * @return string
     */
    protected function GetClassName()
    {
        return __CLASS__;
    }
}
