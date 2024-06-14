<?php

class Top500CL extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'top500CL';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAllTop500($where = '')
    {
        return parent::GetAllById(self::GetIdentifierFieldName(), self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'exp',
            'nivel',
            'country',
            'position',
        ];
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }
}
