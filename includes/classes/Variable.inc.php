<?php

final class Variable extends BaseObject
{
    public static $idField = 'field';
    public static $dataTable = 'server_variables';

    public function __construct($keyValue)
    {
        $cache = self::$cache;
        self::$cache = false;
        $idField = self::$idField;
        $obj = parent::Get(self::GetDataTableFields(), self::GetDataTable(), $idField, $keyValue);
        $this->$idField = $obj->$idField;
        $this->value = $obj->value;
        self::$cache = $cache;
    }

    public static function GetAllValues()
    {
        return parent::GetAllById(self::$idField, self::GetDataTableFields(), self::GetDataTable());
    }

    public static function GetValue($field)
    {
        $cache = self::$cache;
        self::$cache = false;

        $idField = self::$idField;
        $obj = parent::Get(self::GetDataTableFields(), self::GetDataTable(), $idField, $field);
        self::$cache = $cache;

        return isset($obj->value) ? $obj->value : null;
    }

    public static function Save($field, $value)
    {
        $sql = 'UPDATE server_variables SET value = ' . $value . ' WHERE field = \'' . $field . '\'';

        return DBi::$conn->query($sql);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'value',
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
