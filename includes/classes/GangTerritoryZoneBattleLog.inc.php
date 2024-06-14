<?php

class GangTerritoryZoneBattleLog extends CachedObject
{
    public static $dataTable = 'gang_territory_zone_battle_log';
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
        return new GangTerritoryZoneBattle($id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    public static function create($gangTerritoryZoneBattle, $attackingGang, $defendingGang, $isFirstAttack, $damage)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert(self::$dataTable)
            ->values(
                [
                    'gang_territory_zone_battle_id' => ':gang_territory_zone_battle_id',
                    'attacking_gang_id' => ':attacking_gang_id',
                    'defending_gang_id' => ':defending_gang_id',
                    'is_first_attack' => ':is_first_attack',
                    'damage' => ':damage',
                ]
            )
            ->setParameter('gang_territory_zone_battle_id', $gangTerritoryZoneBattle->id)
            ->setParameter('attacking_gang_id', $attackingGang->id)
            ->setParameter('defending_gang_id', $defendingGang->id)
            ->setParameter('is_first_attack', $isFirstAttack)
            ->setParameter('damage', $damage)
        ;
        $queryBuilder->execute();
    }

    public function getGangTerritoryZoneBattle()
    {
        return new GangTerritoryZoneBattle($this->gang_territory_zone_battle_id);
    }

    public function getAttackingGang()
    {
        return new Gang($this->attacking_gang_id);
    }

    public function getDefendingGang()
    {
        return new Gang($this->defending_gang_id);
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'gang_territory_zone_battle_id',
            'attacking_gang_id',
            'defending_gang_id',
            'is_first_attack',
            'damage',
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