<?php

final class KingList extends ARObject
{
    public static $idField = 'id';
    public static $dataTable = 'kings';
    public $left;
    public $defeat;
    public $bg;

    public function __construct($id, $bg = 0)
    {
        parent::__construct($id);
        if ($id == 300) {
            $this->bg = $bg;
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

    public static function whoDefeatHim($boss)
    {
        $sql = 'select id_group, ptime from ' . KingCombat::$dataTable . ' where king=' . $boss . ' and guards=-1 and result=1 ';

        $rs = DBi::$conn->query($sql);
        $arr = [];
        while ($row = mysqli_fetch_object($rs)) {
            $ts = new KingGroup($row->id_group);
            $ts->time = $row->ptime;
            $arr[] = $ts;
        }

        return $arr;
    }

    public static function GetKing($cityid)
    {
        $sql = 'select id from ' . self::$dataTable . ' where city_id=' . $cityid;
        $rs =DBi::$conn->query($sql);
        $row = mysqli_fetch_object($rs);

        return new KingList($row->id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
                          'city_id',
                          'name',
                          'level',
                          'speed',
                          'defense',
                          'strengh',
                          'house',
                          'hp',
                          'crimes',
                          'profile',
                          'avatar',
                          'quote',
                          'hospitaltime',
                          'num_guards',
                          'won','lost',
                          'currenthospital',
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


