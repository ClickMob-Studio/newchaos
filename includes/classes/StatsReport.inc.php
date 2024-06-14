<?php

final class StatsReport extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'trainhistory';

    public function __construct($id)
    {
        parent::__construct($id);
        $this->name = constant($this->name);
        $this->desc = constant($this->desc);
    }

    public static function getTrains(User $user)
    {
        $arr = [];
        $results = DBi::$conn->query('select * from ' . self::$dataTable . ' where userid=' . $user->id);
        while ($row = mysqli_fetch_object($results)) {
            $row->dd = date('j', $row->day);
            $row->mm = date('M', $row->day);
            $row->yy = date('y', $row->day);
            $arr[] = $row;
        }

        return $arr;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'day',
            'userid',
            'strength',
                        'defense',
                        'speed',
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
