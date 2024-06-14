<?php

class DailyDuties extends CachedObject
{
    public static $dataTable = 'daily_duties';
    public static $idField = 'id';

    public function a__construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function SGet($id)
    {
        return new DailyDuties($id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    public function getItem()
    {
        return Item::SGet($this->item_id);
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'attacks',
            'crimes',
            'mugs',
            'busts',
            'points',
            'item_id'
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