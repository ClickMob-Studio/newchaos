<?php

    /**
     * discription: This class is used to manage stock logs.
     *
     * @author: Harish<harish282@gmail.com>
     * @name: cryptologs
     * @package: includes
     * @subpackage: classes
     * @final: Final
     * @access: Public
     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    final class CryptoLogs extends BaseObject
    {
        public static $idField = 'log_id'; //id field
        public static $dataTable = 'cryptologs'; // table implemented

        /**
         * Constructor.
         */
        public function __construct()
        {
        }

        /**
         * Funtions used to save logs.
         *
         * @param $user_id Number current user id
         * @param $action String action to be logged
         * @param $amount Number
         * @param $unit_price Number
         *
         * @return Boleean
         */
        public static function Log($user_id, $action, $amount, $unit_price, $stock_id)
        {
            //check whether action tracking is enabled or not
            //$actionTracking = new Variable('actionTracking');
            //if ($actionTracking->value != 1) return false;

            $data = [
                        'user_id' => $user_id,
                        'action' => $action,
                        'amount' => $amount,
                        'unit_price' => $unit_price,
                        'time' => time(),
                        'stock_id' => $stock_id,
                ];

            return parent::AddRecords($data, self::GetDataTable());
        }

        /**
         * Funtions return all returns.
         *
         * @param User $user_class
         *
         * @return array
         */
        public static function GetAll()
        {
            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable());
        }

        /**
         * Funtions return all returns.
         *
         * @param User $user_class
         *
         * @return array
         */
        public static function GetAllByUserId($user_id)
        {
            $objs = [];
            $query = 'SELECT cryptologs.*, company_name FROM cryptologs , stocks WHERE cryptologs.stock_id = stocks.id AND user_id = \'' . $user_id . '\' ORDER BY cryptologs.time DESC';

            /**Pagination **/
            if (self::$usePaging) {//If doing paging for records
                $query = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query); // insert SQL_CALC_FOUND_ROWS into query to find total rows into table
                Paginator::$recordsOnPage = 50;
                self::$paginator = new Paginator();
                self::$paginator->setRecordsPerPage(Paginator::$recordsOnPage);
                $res = DBi::$conn->query(self::$paginator->getLimitQuery($query));

                $totalRecords = MySQL::GetSingle('Select FOUND_ROWS()');

                self::$paginator->setQueryString();
                self::$paginator->setPageData('', $totalRecords, Paginator::$recordsOnPage, Paginator::$scrollPages, true, false, true);
            } else {
                $res = DBi::$conn->query($query);
            }

            if (mysqli_num_rows($res) == 0) {
                return $objs;
            }
            while ($obj = mysqli_fetch_object($res)) {
                $objs[] = $obj;
            }

            return $objs;

            //return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), 'user_id = \''.$user_id.'\'');
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
                'user_id',
                'action',
                'amount',
                'unit_price',
                'time',
                'stock_id',
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
