<?php

    /**
     * discription: This class is used to manage land for personal shop.
     *
     * @author: Harish<harish282@gmail.com>

     * @name: PShopLand

     * @package: includes

     * @subpackage: classes

     * @final: Final

     * @access: Public

     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    class PShopLand extends BaseObject
    {
        /** Define constatns for status **/
        public static $idField = 'id'; //id field

        public static $dataTable = 'pshop_land'; // table implemented

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
            $sql = 'SELECT ps.`' . implode('`, ps.`', self::GetDataTableFields()) . '`,c.name as cityname  FROM `' . self::GetDataTable() . '` ps, cities c WHERE ps.city = c.id ';

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
                $obj->cityname = constant($obj->cityname);

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
            return self::sCountWhere('userid = ' . $userid);
        }

        public static function GetAllForUser($userid, array $options = [])
        {
            return self::GetWhere('userid = ' . $userid, $options);
        }

        public static function Add(User $user, $city, $cost, $points, $quantity)
        {
            $quantity = (int) $quantity;

            if (0 >= (int) $city) {
                throw new FailedResult(PSHOP_INVALID_CITY);
            }
            if (0 >= (int) $quantity) {
                throw new FailedResult(LANDMARKET_ENTER_VALID_ACRES);
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
            $cost = (int) $cost;

            if ($cost < PSHOP_LAND_MIN_COST && PSHOP_LAND_MIN_COST > -1 && $cost != 0) {
                throw new FailedResult(sprintf(PSHOP_MIN_LIMIT_ERROR, strtolower(COM_ACRE), number_format(PSHOP_LAND_MIN_COST)));
            }
            if ($cost > PSHOP_LAND_MAX_COST && PSHOP_LAND_MAX_COST > -1 && $cost != 0) {
                throw new FailedResult(sprintf(PSHOP_MAX_LIMIT_ERROR, strtolower(COM_ACRE), number_format(PSHOP_LAND_MAX_COST)));
            }
            if ($points < PSHOP_LAND_MIN_POINT && PSHOP_LAND_MIN_POINT > -1 && $points != 0) {
                throw new FailedResult(sprintf(PSHOP_MIN_POINT_LIMIT_ERROR, strtolower(COM_ACRE), number_format(PSHOP_LAND_MIN_POINT)));
            }
            if ($points > PSHOP_LAND_MAX_POINT && PSHOP_LAND_MAX_POINT > -1 && $points != 0) {
                throw new FailedResult(sprintf(PSHOP_MAX_POINT_LIMIT_ERROR, strtolower(COM_ACRE), number_format(PSHOP_LAND_MAX_POINT)));
            }
            $userland = User::SGetLandQuantity($city, $user->id);

            $cityObj = new City($city);

            if ($userland < $quantity) {
                throw new FailedResult(sprintf(PSHOP_NOT_HAVE_THAT_MANY_LAND, $quantity, $cityObj->name));
            }
            if (!$user->RemoveLand($city, $quantity)) {
                throw new FailedResult(sprintf(PSHOP_NOT_HAVE_THAT_MANY_LAND, $quantity, $cityObj->name));
            }
            $time = time();

            $data = [
                        'userid' => $user->id,

                        'city' => $city,

                        'cost' => $cost,

                        'points' => $points,

                        'qty' => $quantity,

                        'timestamp' => $time,
                ];

            self::AddRecords($data, self::GetDataTable());

            Logs::sAddPShopLog('Supply', $user->id, $user->id, $city, $quantity, 'land');

            throw new SuccessResult(sprintf(PSHOP_LAND_ADDED, $quantity, $cityObj->name));
        }

        public function Delete()
        {
            User::SAddLand($this->city, $this->userid, $this->qty);

            self::sDelete(self::GetDataTable(), ['id' => $this->id]);

            Logs::sAddPShopLog('Removal', $this->userid, $this->userid, $this->city, $this->qty, 'land');

            $cityObj = new City($this->city);

            throw new SuccessResult(sprintf(PSHOP_LAND_DELETED, $this->qty, $cityObj->name));
        }

        public function Buy(User $user, $qty)
        {
            if (!is_numeric($qty) || $qty <= 0) {
                throw new FailedResult(PSHOP_INVALID_BUY_QTY);
            }
            if ($qty > $this->qty) {
                throw new FailedResult(PSHOP_INVALID_BUY_QTY);
            }
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

            $user->AddLand($this->city, $qty);

            if ($qty == $this->qty) {
                self::sDelete(self::GetDataTable(), ['id' => $this->id]);
            } else {
                $this->RemoveFromAttribute('qty', $qty);
            }

            $cityObj = new City($this->city);

            User::SNotify($this->userid, sprintf(PSHOP_LAND_SOLD, $qty, $cityObj->name, number_format($cost), number_format($points), addslashes($user->formattedname)), PSHOP_LAND_SHOP);

            Logs::sAddPShopLog('Transaction', $this->userid, $user->id, $this->city, $qty, 'land', $cost, $points);

            throw new SuccessResult(sprintf(PSHOP_LAND_PUCRCHASED, $qty, $cityObj->name, number_format($cost), number_format($points)));
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

                'city',

                'cost',

                'points',

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
