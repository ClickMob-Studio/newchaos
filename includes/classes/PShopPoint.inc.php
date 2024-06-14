<?php

    /**
     * discription: This class is used to manage item for personal shop.
     *
     * @author: Harish<harish282@gmail.com>

     * @name: PShopPoint

     * @package: includes

     * @subpackage: classes

     * @final: Final

     * @access: Public

     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    class PShopPoint extends BaseObject
    {
        /** Define constatns for status **/
        public static $idField = 'id'; //id field

        public static $dataTable = 'pshop_point'; // table implemented

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
            return self::sCountWhere('userid = ' . $userid);
        }

        public static function GetAllForUser($userid, array $options = [])
        {
            return self::GetWhere('userid = ' . $userid, $options);
        }

        public static function Add(User $user, $cost, $quantity, $pointsMinCost, $pointsMaxCost)
        {
            if (0 >= (int) $quantity) {
                throw new FailedResult(POINTMARKET_ENTER_VALID_POINTS);
            }
            if (0 >= (int) $cost || !is_numeric($cost)) {
                throw new FailedResult(POINTMARKET_ENTER_VALID_AMT);
            }
            if ($cost < $pointsMinCost && $pointsMinCost > -1) {
                throw new FailedResult(sprintf(PSHOP_MIN_LIMIT_ERROR, strtolower(COM_POINT), number_format($pointsMinCost)));
            }
            if ($cost > $pointsMaxCost && $pointsMaxCost > -1) {
                throw new FailedResult(sprintf(PSHOP_MAX_LIMIT_ERROR, strtolower(COM_POINT), number_format($pointsMaxCost)));
            }
            if ($quantity > $user->points) {
                throw new FailedResult(POINTMARKET_NOT_HAVE_THAT_MANY);
            }
            if (!$user->RemoveFromAttribute('points', $quantity)) {
                throw new SoftException(POINTMARKET_NOT_ENOUGH_POINTS);
            }
            $time = time();

            $data = [
                        'userid' => $user->id,

                        'cost' => $cost,

                        'qty' => $quantity,

                        'timestamp' => $time,
                ];

            self::AddRecords($data, self::GetDataTable());

            Logs::sAddPShopLog('Supply', $user->id, $user->id, 0, $quantity, 'points');

            return true;
        }

        public function Delete()
        {
            $user = UserFactory::getInstance()->getUser($this->userid);

            if ($user->points + $this->qty > MAX_POINTS) {
                throw new FailedResult(sprintf(USER_POINTS_MAX_ERROR, number_format(MAX_POINTS)));
            }
            $user->AddToAttribute('points', $this->qty);

            Logs::sAddPShopLog('Removal', $this->userid, $this->userid, 0, $this->qty, 'points');

            return self::sDelete(self::GetDataTable(), ['id' => $this->id]);
        }

        public function Buy(User $user, $qty)
        {
            if (!is_numeric($qty) || $qty <= 0) {
                throw new FailedResult(PSHOP_INVALID_BUY_QTY);
            }
            if ($qty > $this->qty) {
                throw new FailedResult(PSHOP_INVALID_BUY_QTY);
            }
            if ($user->points + $this->qty > MAX_POINTS) {
                throw new FailedResult(sprintf(USER_POINTS_MAX_ERROR, number_format(MAX_POINTS)));
            }
            $cost = $qty * $this->cost;

            if ($cost > $user->money) {
                throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
            }
            if (!$user->RemoveFromAttribute('money', $cost)) {
                throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
            }
            User::SAddBankMoney($this->userid, $cost);

            $user->AddToAttribute('points', $qty);

            if ($qty == $this->qty) {
                self::sDelete(self::GetDataTable(), ['id' => $this->id]);
            } else {
                $this->RemoveFromAttribute('qty', $qty);
            }

            User::SNotify($this->userid, sprintf(PSHOP_POINT_SOLD, $qty, number_format($cost), addslashes($user->formattedname)), PSHOP_POINT_SHOP);

            Logs::sAddPShopLog('Transaction', $this->userid, $user->id, 0, $qty, 'points', $cost);

            throw new SuccessResult(sprintf(PSHOP_POINT_PURCHASED, $qty, number_format($cost)));
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

                'cost',

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
