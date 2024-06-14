<?php

    /**
     * discription: This class is used to manage auctions placed by users.
     *
     * @author: Harish<harish282@gmail.com>
     * @name: Auction
     * @package: includes
     * @subpackage: classes
     * @final: Final
     * @access: Public
     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    class AuctionLog extends BaseObject
    {
        public static $idField = 'id'; //id field
        public static $dataTable = 'auction_log'; // table implemented

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
        public static function GetAll()
        {
            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable());
        }

        public static function GetAllActive()
        {
            $where = 'finished < \'' . time() . '\'';

            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
        }

        public static function getLastTen()
        {
            $query = 'select id,auction_id, finished from ' . self::$dataTable . ' where closed > 0 order by finished desc limit 1000';
            $auctions = [];
            $result = DBi::$conn->query($query);
            while ($row = mysqli_fetch_assoc($result)) {
                $auctions[] = new AuctionLog($row['id']);
            }

            return $auctions;
        }

        public static function GetAllExpired()
        {
            $where = 'finished >= \'' . time() . '\' AND closed = 0';

            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
        }

        public static function GetLast($auctionId)
        {
            $time = time() - 3 * DAY_SEC;
            $where = 'finished >= \'' . $time . '\' AND auction_id = ' . $auctionId;
            $objs = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);

            if (empty($objs)) {
                return null;
            }

            return $objs[0];
        }

        public static function GetActive($id)
        {
            $where = 'finished < \'' . time() . '\' AND auction_id = \'' . $id . '\'';

            $objs = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);

            if (empty($objs)) {
                return null;
            }

            return $objs[0];
        }

        public static function Add($auction_id, $period)
        {
            $time = time();

            $data = [
                        'auction_id' => $auction_id,
                        'started' => $time,
                        'finished' => $time + ($period * DAY_SEC), //86400 seconds in a day.
                ];

            return self::AddRecords($data, self::GetDataTable());
        }

        public static function Delete($id)
        {
            $where = ['id' => $id];

            return self::sDelete(self::GetDataTable(), $where);
        }

        public static function DeleteForAuction($id)
        {
            $where = ['auction_id' => $id];

            return self::sDelete(self::GetDataTable(), $where);
        }

        public static function Finish($id)
        {
            $time = time();

            DBi::$conn->query('UPDATE ' . self::GetDataTable() . ' SET closed = 1 WHERE id = \'' . $id . '\'');

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
                self::$idField,
                'auction_id',
                'started',
                'finished',
                'closed',
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
