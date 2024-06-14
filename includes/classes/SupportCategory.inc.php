<?php

    /**
     * discription: This class is used to manage support categories.
     *
     * @author: Harish<harish282@gmail.com>
     * @name: SupportCategory
     * @package: includes
     * @subpackage: classes
     * @final: Final
     * @access: Public
     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    final class SupportCategory extends BaseObject
    {
        public static $idField = 'id'; //id field
        public static $dataTable = 'support_categories'; // table implemented

        /**
         * Constructor.
         */
        public function __construct()
        {
        }

        /**
         * Funtions used to save category.
         *
         * @param $name String
         *
         * @return Boleean
         */
        public static function Add($name)
        {
            $data = [
                        'name' => $name,
                ];

            return parent::AddRecords($data, self::GetDataTable());
        }

        /**
         * Funtions used to save category.
         *
         * @param $id Number
         * @param $name String
         *
         * @return Boleean
         */
        public static function Modify($id, $name)
        {
            $data = [
                        'name' => $name,
                ];

            return parent::sUpdate(self::GetDataTable(), $updates, ['id' => $id]);
        }

        /**
         * Funtions return all categories.
         *
         * @return array
         */
        public static function GetAll()
        {
            $categories = [];
            $objs = parent::GetAll(self::GetDataTableFields(), self::GetDataTable());
            foreach ($objs as $obj) {
                $categories[$obj->id] = constant($obj->name);
            }

            return $categories;
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
                'name',
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
