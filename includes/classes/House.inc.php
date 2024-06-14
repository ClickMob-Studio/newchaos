<?php

final class House extends CachedObject
{
    public static $idField = 'id';
    public static $dataTable = 'houses';

    public function __construct($id)
    {
        if ($id == 0) {
            // Default home
            $this->id = 0;
            $this->name = 'COM_NONE';
            $this->awake = 100;
            $this->cost = 0;
        } else {
            parent::__construct($id);
        }

        if (!empty($this->name)) {
            $this->name = constant($this->name);
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

    public static function GetLowerCell($id)
    {
        $sql = 'SELECT h1.* FROM ' . self::GetDataTable() . ' h1,' . self::GetDataTable() . ' h2 WHERE h1.cost < h2.cost AND h2.id = \'' . $id . '\' ORDER BY COST DESC LIMIT 1';
        $result = DBi::$conn->query($sql);

        if (mysqli_num_rows($result) == 0) {
            return null;
        }

        return mysqli_fetch_object($result);
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
            'security_level',
                        'image',
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
