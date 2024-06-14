<?php

/**
 * discription: This class is used to manage item for personal shop.
 *
 * @author: Harish<harish282@gmail.com>
 * @name: InventoryRP
 * @package: includes
 * @subpackage: classes
 * @final: Final
 * @access: Public
 * @copyright: icecubegaming <http://www.icecubegaming.com>
 */
class InventoryRP extends BaseObject
{
    /** Define constatns for status **/
    public static $idField = 'itemid'; //id field
    public static $dataTable = 'inventory'; // table implemented

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
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, $options['orderBy'], $options['sort']);
    }

    public static function sCountWhere($where)
    {
        $query = 'SELECT COUNT(`itemid`) as `totalCount` FROM `' . self::GetDataTable() . '` WHERE ' . $where;

        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->totalCount;
    }

    public static function GetCountForUser($userid)
    {
        return self::sCountWhere('userid = ' . $userid . ' AND qty > 0 ', $options);
    }

    public static function GetAllForUser($userid, array $options = [])
    {
        $where = 'userid = ' . $userid;

        return self::GetWhere($where, $options);
    }

    public static function GetPack(User $user, $pack, array $details)
    {
        $objs = self::GetWhere('userid = ' . $user->id . ' AND pack = \'' . $pack . '\' AND details = \'' . serialize($details) . '\'');
        if (!empty($objs[0])) {
            return new InventoryRP($objs[0]->id);
        }
    }

    public static function Add(User $user, $pack, array $details)
    {
        if (empty($pack)) {
            throw new FailedResult(RPSHOP_INVALID_PACK);
        }
        $packObj = self::GetPack($user, $pack, $details);

        if (is_a($packObj, 'InventoryRP')) {
            $packObj->AddToAttribute('qty', 1);
        } else {
            $time = time();
            $data = [
                'userid' => $user->id,
                'pack' => $pack,
                'qty' => 1,
                'timestamp' => $time,
            ];

            return self::AddRecords($data, self::GetDataTable());
        }

        return true;
    }

    public function Delete()
    {
        self::sDelete(self::GetDataTable(), ['id' => $this->id]);
    }

    public function UseItem(User $user, $qty = 1)
    {
        if (!is_numeric($qty) || $qty <= 0) {
            throw new FailedResult(PSHOP_INVALID_BUY_QTY);
        }
        if ($qty > $this->qty) {
            throw new FailedResult(PSHOP_INVALID_BUY_QTY);
        }
        $rpStore = new RPStore();
        $packname = $rpStore->getItemName($this->pack);

        $details = unserialize($this->details);
        if (is_array($details['attr'])) {
            foreach ($details['attr'] as $attr => $val) {
                try {
                    $user->AddToAttribute($attr, $val);
                } catch (FailedResult $e) {
                    $error = $e->getView();
                    if (strpos($error, 'POINTS_ERR') !== false) {
                        $temp = explode('|', $error);
                        $pointsCredited = (int) $temp[1];
                    }

                    User::SNotify($user->id, sprintf(MAXPOINTS_USER_NOTIFY, $packname, MAX_POINTS, $pointsCredited), COM_ERROR);
                    User::SNotify(ADMIN_USER_ID, sprintf(MAXPOINTS_USER_NOTIFY, $packname, $user->id, MAX_POINTS, $pointsCredited), COM_ERROR);
                }
            }
        }

        if (is_array($details['item'])) {
            foreach ($details['item'] as $itemid => $qnty) {
                $user->AddItems($itemid, $qnty);
            }
        }

        if (is_array($details['land'])) {
            foreach ($details['land'] as $city => $qnty) {
                $user->AddLand($city, $qnty);
            }
        }

        if ($details['cat'] == 'rpnames') {
            $user->Notify(PAYMENT_SOMEONECONTACT, 'Donation');
            User::SNotify(ADMIN_USER_ID, sprintf(PAYMENT_NEWNAME, $user->id, $packname));
        }

        if ($qty == $this->qty) {
            self::sDelete(self::GetDataTable(), ['id' => $this->id]);
        } else {
            $this->RemoveFromAttribute('qty', $qty);
        }

        return true;
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
            'userid',
            'itemid',
            'quantity'
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