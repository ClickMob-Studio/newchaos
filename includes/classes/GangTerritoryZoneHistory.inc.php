<?php

class GangTerritoryZoneHistory extends CachedObject
{
    public static $dataTable = 'gang_territory_zone_history';
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
        return new GangTerritoryZoneHistory($id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    public static function create($gangTerritoryZone, $gangId)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert(self::$dataTable)
            ->values(
                [
                    'gang_territory_zone_id' => ':gang_territory_zone_id',
                    'gang_id' => ':gang_id',
                    'takeover_time' => ':takeover_time'
                ]
            )
            ->setParameter('gang_territory_zone_id', $gangTerritoryZone->id)
            ->setParameter('gang_id', $gangId)
            ->setParameter('takeover_time', time())
        ;
        $queryBuilder->execute();
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'gang_territory_zone_id',
            'gang_id',
            'takeover_time',
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