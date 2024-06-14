<?php

final class GangPermission extends BaseObject
{
    public static $idField = 'id_rank';
    public static $dataTable = 'gangperm';

    public static function GetAll()
    {
        return parent::GetAllById(self::GetIdentifierFieldName(), self::GetDataTableFields(), self::GetDataTable());
    }

    public static function GetAllForGangRank($gandId, $rank)
    {
        return parent::GetAllById('perm', self::GetDataTableFields(), self::GetDataTable(), '`id_gang` = \'' . $gandId . '\' AND `name_rank`=\'' . $rank . '\'');
    }

    public static function sDelete($gangId, $nameRank)
    {
        return parent::sDelete(self::$dataTable,
            ['id_gang' => $gangId, 'name_rank' => $nameRank]);
    }

    public static function sAdd($state, $gangId, $perm, $nameRank, $permorder = 0)
    {
        return parent::AddRecords(
            [
                'id_gang' => $gangId,
                'perm' => $perm,
                'name_rank' => $nameRank,
                'state' => $state,
                'permorder' => $permorder,
            ],
            self::$dataTable);
    }

    public static function sUpdate($state, $gangId, $perm, $nameRank, $permorder = 0)
    {
        return parent::sUpdate(self::$dataTable,
            ['state' => $state, 'permorder' => $permorder],
            ['id_gang' => $gangId, 'perm' => $perm, 'name_rank' => $nameRank]);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'id_gang',
            'name_rank',
            'perm',
            'state',
            'permorder',
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
