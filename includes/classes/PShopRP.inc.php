<?php

    /**
     * discription: This class is used to manage item for personal shop.
     *
     * @author: Harish<harish282@gmail.com>
     * @name: PShopRP
     * @package: includes
     * @subpackage: classes
     * @final: Final
     * @access: Public
     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    class PShopRP extends BaseObject
    {
        /** Define constatns for status **/
        public static $idField = 'id'; //id field
        public static $dataTable = 'rp_shop_store'; // table implemented

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
            return self::sCountWhere('userid = ' . $userid . ' AND qty > 0 ', $options);
        }

        public static function GetAllForUser($userid, array $options = [])
        {
            $where = 'userid = ' . $userid;

            if (isset($options['qty']) && !empty($options['qty'])) {
                $where .= ' AND qty > 0 ';
            }
            if (isset($options['qtyleft']) && !empty($options['qtyleft'])) {
                $where .= ' AND qtyleft > 0 ';
            }

            return self::GetWhere($where, $options);
        }

        public static function GetPack(User $user, $pack, array $details)
        {
            $objs = self::GetWhere('userid = ' . $user->id . ' AND pack = \'' . $pack . '\' AND details = \'' . serialize($details) . '\'');
            if (!empty($objs[0])) {
                return new PShopRP($objs[0]->id);
            }
        }

        public static function Add(User $user, $pack, array $details)
        {
            if (empty($pack)) {
                throw new FailedResult(RPSHOP_INVALID_PACK);
            }
            $time = time();
            $data = [
                        'userid' => $user->id,
                        'pack' => $pack,
                        'details' => serialize($details),
                        'qtyleft' => 1,
                        'timestamp' => $time,
                ];

            return self::AddRecords($data, self::GetDataTable());
        }

        public function AddToPShop($cost, $qty, $points = 0, $pointsMinCost, $pointsMaxCost)
        {
            if ($qty <= 0 || !is_numeric($qty)) {
                throw new FailedResult(PSHOP_INVALID_NUM_QTY);
            }
            if (($cost == 0 || !is_numeric($cost)) && ($points == 0 || !is_numeric($points))) {
                throw new FailedResult(PSHOP_INVALID_COST);
            }
            if (!empty($cost) && $cost < 0) {
                throw new FailedResult(PSHOP_INVALID_COST);
            }
            if (!empty($points) && $points < 0) {
                throw new FailedResult(RPSHOP_INVALID_POINTS_QTY);
            }
            if ($qty > $this->qtyleft) {
                throw new FailedResult(RPSHOP_INVALID_QTY);
            }
            if ($cost < RPSHOP_ITEM_MIN_COST && RPSHOP_ITEM_MIN_COST > -1 && $cost != 0) {
                throw new FailedResult(sprintf(PSHOP_MIN_LIMIT_ERROR, strtolower(COM_ITEM), number_format(RPSHOP_ITEM_MIN_COST)));
            }
            if ($cost > RPSHOP_ITEM_MAX_COST && RPSHOP_ITEM_MAX_COST > -1 && $cost != 0) {
                throw new FailedResult(sprintf(PSHOP_MAX_LIMIT_ERROR, strtolower(COM_ITEM), number_format(RPSHOP_ITEM_MAX_COST)));
            }
            if ($points < $pointsMinCost && $pointsMinCost > -1 && $points != 0) {
                throw new FailedResult(sprintf(PSHOP_MIN_POINT_LIMIT_ERROR, strtolower(COM_ITEM), number_format($pointsMinCost)));
            }
            if ($points > $pointsMaxCost && $pointsMaxCost > -1 && $points != 0) {
                throw new FailedResult(sprintf(PSHOP_MAX_POINT_LIMIT_ERROR, strtolower(COM_ITEM), number_format($pointsMaxCost)));
            }
            $updates = [
                            'qtyleft' => $this->qtyleft - $qty,
                            'qty' => $this->qty + $qty,
                            'cost' => $cost,
                            'points' => $points,
                        ];

            Logs::sAddRPShopLog('Supply', $this->userid, $this->userid, $this->pack, $qty);

            return self::sUpdate(self::GetDataTable(), $updates, ['id' => $this->id]);
        }

        public function Delete()
        {
            self::sDelete(self::GetDataTable(), ['id' => $this->id]);
        }

        public function DeleteFromPShop()
        {
            $updates = [
                        'qtyleft' => $this->qtyleft + $this->qty,
                        'qty' => 0,
                        ];

            Logs::sAddRPShopLog('Removal', $this->userid, $this->userid, $this->pack, $this->qty);

            return self::sUpdate(self::GetDataTable(), $updates, ['id' => $this->id]);
        }

        public function Buy(User $user, $qty)
        {
            if (!is_numeric($qty) || $qty <= 0) {
                throw new FailedResult(PSHOP_INVALID_BUY_QTY);
            }
            if ($qty > $this->qty) {
                throw new FailedResult(PSHOP_INVALID_BUY_QTY);
            }
            $rpStore = new RPStore();
            $packname = $rpStore->getItemName($this->pack);

            $cost = $qty * $this->cost;
            $points = $qty * $this->points;

            if ($this->points > 0) {
                $owner = UserFactory::getInstance()->getUser($this->userid);

                if ($owner->points + $points > MAX_POINTS) {
                    throw new FailedResult(sprintf(RPSHOP_OWNER_POINTS_MAX_ERROR, number_format(MAX_POINTS)));
                }
                if ($points > $user->points) {
                    throw new FailedResult(POINTMARKET_NOT_ENOUGH_POINTS);
                }
            }
            if ($this->cost > 0) {
                if ($cost > $user->money) {
                    throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
                }
            }

            if ($this->points > 0) {
                if (!$user->RemoveFromAttribute('points', $points)) {
                    throw new FailedResult(POINTMARKET_NOT_ENOUGH_POINTS);
                }
                try {
                    $owner->AddToAttribute('points', $points);
                } catch (Exception $e) {
                }
            }

            if ($this->cost > 0) {
                if (!$user->RemoveFromAttribute('money', $cost)) {
                    throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
                }
                User::SAddBankMoney($this->userid, $cost);
            }
            $user->AddItems($this->pack, 1);
            //Inventory::Add($user, $this->pack, unserialize($this->details));

            Logs::sAddRPShopLog('Transaction', $this->userid, $user->id, $this->pack, $qty, $cost, $points);

            if ($qty == $this->qty && $this->qtyleft <= 0) {
                self::sDelete(self::GetDataTable(), ['id' => $this->id]);
            } else {
                $this->RemoveFromAttribute('qty', $qty);
            }

            User::SNotify($this->userid, sprintf(PSHOP_ITEM_SOLD, $qty, $rpStore->getItemName($this->pack), '<a href="profiles.php?id=' . $user->id . '">' . $user->username . '</a>', number_format($cost), number_format($points)), PSHOP_RP_SHOP);

            throw new SuccessResult(sprintf(RPSHOP_PACK_PURCHASED, $rpStore->getItemName($this->pack)));
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
                'userid',
                'pack',
                'details',
                'cost',
                'points',
                'qtyleft',
                'qty',
                'timestamp',
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
