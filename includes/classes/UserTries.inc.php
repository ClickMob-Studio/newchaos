<?php

    /**
     * discription: This class is used to manage User refreshes on gym and crime page.
     *
     * @author: Harish<harish282@gmail.com>
     * @name: UserTries
     * @package: includes
     * @subpackage: classes
     * @final: Final
     * @access: Public
     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    class UserTries extends BaseObject
    {
        const IS_REFRESH = 1;
        const IS_WRONG_ENTRY = 2;

        const EQ = 1;
        const NORMAL = 2;

        public static $idField = 'id'; //id field
        public static $dataTable = 'user_tries'; // table implemented

        /**
         * Constructor.
         */
        public function __construct()
        {
        }

        /**
         * Funtions used to add user tries.
         *
         * @return Boleean
         */
        public static function Add(User $user, $type = self::IS_REFRESH, $botType = self::EQ)
        {
            $query = 'INSERT INTO ' . self::GetDataTable() . ' SET user_id=' . $user->id . ', type=' . $type . ', bottype=' . $botType . ', time_stamp=' . time();
            DBi::$conn->query($query);
        }

        /**
         * Funtions used to delete all user tries.
         *
         * @return resultset
         */
        public static function DeleteAll($botType = '')
        {
            $query = 'DELETE FROM ' . self::GetDataTable();
            if (!empty($botType)) {
                $query .= ' WHERE bottype = ' . $botType;
            }

            return DBi::$conn->query($query);
        }

        /**
         * Funtions used to delete user tries.
         *
         * @return Boleean
         */
        public static function Delete($user_id, $botType = '')
        {
            if (!is_array($user_id)) {
                $user_id = [$user_id];
            }

            $query = 'DELETE FROM ' . self::GetDataTable() . ' WHERE user_id IN ("' . implode('","', $user_id) . '")';
            if (!empty($botType)) {
                $query .= ' AND bottype = ' . $botType;
            }

            DBi::$conn->query($query);

            return true;
        }

        /**
         * Alias to Delete.
         *
         * @return Boleean
         */
        public static function Reset($user_id, $botType = '')
        {
            return self::Delete($user_id, $botType);
        }

        /**
         * Funtions return all records.
         *
         * @return array
         */
        public static function GetAll($botType = '')
        {
            $where = '';
            if (!empty($botType)) {
                $where = ' bottype = ' . $botType;
            }

            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'user_id', 'ASC');
        }

        public static function GetAllCount($botType = '', $order = 'refreshes', $sort = 'DESC')
        {
            $typeOrder = 'ASC';
            if ($order == 'tries') {
                $typeOrder = 'DESC';
            }

            if (empty($sort)) {
                $sort = 'DESC';
            }

            $where = '';
            if (!empty($botType)) {
                $where = ' AND bottype = ' . $botType;
            }

            $query = 'SELECT `user_id`, `type`, COUNT(`type`) AS total FROM user_tries WHERE 1=1 ' . $where . ' GROUP BY user_id,type ORDER BY `type` ' . $typeOrder . ', `total` ' . $sort;

            if (self::$usePaging) {
                $query = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query); // insert SQL_CALC_FOUND_ROWS into query to find total rows into table
                self::$paginator = new Paginator($pagevar);

                self::$paginator->setRecordsPerPage(Paginator::$recordsOnPage);
                $res = DBi::$conn->query(self::$paginator->getLimitQuery($query));

                $totalRecords = MySQL::GetSingle('Select FOUND_ROWS()');
                $totalRecords = (int) $totalRecords / 2;

                self::$paginator->setQueryString();
                self::$paginator->setPageData('', $totalRecords, Paginator::$recordsOnPage, Paginator::$scrollPages, true, false, true);
            } else {
                $res = DBi::$conn->query($query);
            }

            $objs = [];
            while ($row = mysqli_fetch_assoc($res)) {
                if (!is_object($objs[$row['user_id']])) {
                    $objs[$row['user_id']] = new stdclass();
                }

                $objs[$row['user_id']]->user_id = $row['user_id'];
                if ($row['type'] == self::IS_REFRESH) {
                    $objs[$row['user_id']]->refreshes = $row['total'];
                } else {
                    $objs[$row['user_id']]->tries = $row['total'];
                }
            }

            return $objs;
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
                'type',
                'time_stamp',
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
