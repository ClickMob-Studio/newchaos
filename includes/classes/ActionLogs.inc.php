<?php

    /**
     * discription: This class is used to manage left links of site.
     *
     * @author: Harish<harish282@gmail.com>
     * @name: ActionLogs
     * @package: includes
     * @subpackage: classes
     * @final: Final
     * @access: Public
     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    final class ActionLogs extends BaseObject
    {
        public static $idField = 'log_id'; //id field
        public static $dataTable = 'action_track_log'; // table implemented

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
         * @param $desc String
         *
         * @return Boleean
         */
        public static function Log($user_id, $action, $desc = '', $value = 0)
        {
            //check whether action tracking is enabled or not
            $actionTracking = new Variable('actionTracking');
//            if ($actionTracking->value != 1) {
//                return false;
//            }

            $data = [
                                                'timestamp' => time(),
                                                'page' => basename($_SERVER['PHP_SELF']),
                                                'user_id' => $user_id,
                                                'action' => addslashes($action),
                                                'desc' => addslashes($desc),
                                                'value' => $value,
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
                'timestamp',
                'page',
                'user_id',
                'action',
                'desc',
                'value',
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
