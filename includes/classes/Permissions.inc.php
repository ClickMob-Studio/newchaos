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
    class Permissions extends BaseObject
    {
        public static $idField = 'id'; //id field
        public static $dataTable = 'permissions'; // table implemented

        /**
         * Constructor.
         */
        public function __construct($id)
        {
            try {
                parent::__construct($id);
            } catch (Exception $e) {
                self::AddRecords(['id' => $id], self::$dataTable);
            }
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
                'gang_member_equipped_items',
                'gang_member_total_stats',
                'gang_leader_equipped_items',
                'gang_leader_total_stats',
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
