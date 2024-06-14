<?php

final class ARHouse extends ARObject
{
    public static $idField = 'id';
    public static $dataTable = 'houses';

    public function __construct($id)
    {
        if ($id == 0) {
            // Default home
            $this->id = 0;
            $this->name = 'Homeless';
            $this->awake = 100;
            $this->cost = 0;
        } else {
            parent::__construct($id);
        }
    }

    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '', false, false, 'cost', 'ASC');
    }

    public static function GetAllById()
    {
        return parent::GetAllById(self::GetIdentifierFieldName(), self::GetDataTableFields(), self::GetDataTable());
    }

    public static function CalculateDailyFee($cost)
    {
        return round($cost * 0.002);
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
            'awake',
            'cost',
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
