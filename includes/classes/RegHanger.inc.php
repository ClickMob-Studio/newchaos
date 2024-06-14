<?php


class RegHanger extends BaseObject
{
    public static $idField = 'hanger_id';
    public static $dataTable = 'hangers';

    public static function GetRegHanger($gangid){
        $query = BaseObject::createQueryBuilder();
        $sql = $query->select(['rh.id', 'rh.regid', 'rh.hangid', 'rh.vehicleid', 'ft.hanger_id', 'ft.hanger_name', 'ft.hanger_price', 'ft.hanger_image'])
            ->from('reg_hangers', 'rh')
            ->leftJoin('rh', 'hangers', 'ft', 'rh.hangid = ft.hanger_id')
            ->where('rh.regid = :gangid')
            ->setParameter('gangid', $gangid)
            ->execute();
        return $sql;
    }
    

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'hanger_name',
            'hanger_members',
            'hanger_price',
            'hanger_image',
        ];
    }
}