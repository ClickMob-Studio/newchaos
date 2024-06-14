<?php

final class ConcertRoom extends ARObject
{
    public static $idField = 'id';
    public static $dataTable = 'ConcertRoom';

    public function __construct($id)
    {
        parent::__construct($id);
    }
    public function getImage()
    {
        return 'images/venues/' . $this->ConcertImage . '.png';
    }

    public function MainVariable($value)
    {
        if ($value < $this->TCostMin || $value > $this->TCostMax) {
            throw new Exception('Invalid value');
        }
        switch ($value - $this->TCostMin) {
                 case 0: return 45;
                 case 1: return 40;
                 case 2: return 35;
                 case 3: return 30;
                 case 4: return 25;
                 case 5: return 20;
             }
    }

    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '', false, false, 'MinLevel', 'ASC');
    }

    public static function GetAllById()
    {
        return parent::GetAllById(self::GetIdentifierFieldName(), self::GetDataTableFields(), self::GetDataTable());
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'id',
                          'Name',
                          'MaxCapacity',
                          'TCostMin',
                          'TCostMax',
                          'RoomCost',
                          'MinLevel',
                          'Description',
                          'ConcertImage',
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

?>


