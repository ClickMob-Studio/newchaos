<?php

final class Book extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'books';

    public function __construct($id)
    {
        parent::__construct($id);
        $this->name = constant($this->name);
        $this->desc = constant($this->desc);
    }

    public static function Get($id)
    {
        return new Book($id);
    }

    public static function GetAll($where = '')
    {
        $objs = parent::GetAllById(
            self::GetIdentifierFieldName(),
            self::GetDataTableFields(),
            self::GetDataTable(),
            $where
        );

        foreach ($objs as $key => $obj) {
            $obj->name = constant($obj->name);
            $obj->desc = constant($obj->desc);
            $objs[$key] = $obj;
        }

        return $objs;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'name',
            'desc',
            'duration',
            'predecessors',
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
