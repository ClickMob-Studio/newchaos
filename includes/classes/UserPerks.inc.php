<?php

class UserPerks extends CachedObject
{
    public static $dataTable = 'user_perks';
    public static $idField = 'id';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function SGet($id)
    {
        return new UserPerks($id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    public function hasAvailableSlots()
    {
        if ($this->perk_1 && $this->perk_2 && $this->perk_3) {
            return false;
        }

        return true;
    }

    public function getAvailableSlot()
    {
        if (!$this->perk_1) {
            return 'perk_1';
        }

        if (!$this->perk_2) {
            return 'perk_2';
        }

        if (!$this->perk_3) {
            return 'perk_3';
        }

        return false;
    }

    public function hasPerkActivated(int $id)
    {
        if ($this->perk_1 == $id) {
            return true;
        }

        if ($this->perk_2 == $id) {
            return true;
        }

        if ($this->perk_3 == $id) {
            return true;
        }

        return false;
    }

    public function getPerkOneType()
    {
        if ($this->perk_1) {
            return UserPerkTypes::SGet($this->perk_1);
        }

        return null;
    }

    public function getPerkTwoType()
    {
        if ($this->perk_2) {
            return UserPerkTypes::SGet($this->perk_2);
        }

        return null;
    }

    public function getPerkThreeType()
    {
        if ($this->perk_3) {
            return UserPerkTypes::SGet($this->perk_3);
        }

        return null;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'user_id',
            'perk_1',
            'perk_2',
            'perk_3',
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