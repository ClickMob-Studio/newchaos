<?php

final class Skill extends CachedObject
{
    public static $idField = 'id';
    public static $dataTable = 'skills';

    public function __construct($id)
    {
        parent::__construct($id);
        $this->name = constant($this->name);
        $this->description = constant($this->description);
    }

    public static function GetAll()
    {
        $objs = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '', false, false, 'id', 'ASC');

        return self::ConvertConsts($objs);
    }

    public static function  GetAllSecurity()
    {
        $objs = parent::GetAllById('id', self::GetDataTableFields(), self::GetDataTable(), '`securitySkill`=1');

        return self::ConvertConsts($objs);
    }

    public static function GetAllById()
    {
        $objs = parent::GetAllById(self::GetIdentifierFieldName(), self::GetDataTableFields(), self::GetDataTable());

        return self::ConvertConsts($objs);
    }

    public static function ResetActivations()
    {
        DBi::$conn->query('UPDATE `user_skills` SET `activated`=0, `activationsToday`=0');
    }

    public static function ConvertConsts($objs)
    {
        foreach ($objs as $key => $obj) {
            $obj->name = constant($obj->name);
            $obj->description = constant($obj->description);
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
            'description',
            'type',
            'activationsPerDay',
            'bonusActivations',
            'maxLvl',
            'securitySkill',
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
