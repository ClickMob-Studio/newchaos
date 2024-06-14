<?php
class PlanMission extends BaseObject
{

    static $idField = 'mission_id'; //id field
    static $dataTable = 'vehicle_missions'; // table implemented

    /**
     * Constructor
     */
    public function __construct()
    {
        ;
    }

    /**
     * Function used to get the data table name which is implemented by class
     *
     * @return String
     */
    protected static function GetDataTable()
    {
        return self::$dataTable;
    }
    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable());
    }
    /**
     * Returns the fields of table.
     *
     * @return Array
     */
    protected static function GetDataTableFields()
    {
        return array(
            self::$idField,
            'mission_vehicle',
            'mission_name',
            'mission_desc',
            'mission_members',
            'mission_time',

        );
    }
    /**
     * Returns the identifier field name
     *
     * @return Mixed
     */
    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    /**
     * Function returns the class name
     *
     * @return String
     */
    protected function GetClassName()
    {
        return __CLASS__;
    }

}

?>