<?php

class UserPerkTypes extends CachedObject
{
    public static $dataTable = 'user_perk_types';
    public static $idField = 'id';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAll($where = '')
    {
        $result = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);

        $userPerkTypes = array();
        foreach ($result as $res) {
            $userPerkTypes[] = self::SGet($res->id);
        }

        return $userPerkTypes;
    }

    public static function SGet($id)
    {
        return new UserPerkTypes($id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    public function getName()
    {
        return constant($this->name);
    }

    public function getDescription()
    {
        return constant($this->description);
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'name',
            'description',
            'image_url',
            'required_level'
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