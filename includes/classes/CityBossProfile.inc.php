<?php

class CityBossProfile extends CachedObject
{
    public static $dataTable = 'city_boss_profile';
    public static $idField = 'id';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function getForCity(int $cityId)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('city_boss_profile')
            ->where('city_id = :city_id')
            ->setParameter('city_id', $cityId)
        ;
        $ids = $queryBuilder->execute()->fetchAll();

        $cityBossProfiles = array();
        foreach ($ids as $id) {
            $cityBossProfiles[] = new CityBossProfile($id['id']);
        }

        return $cityBossProfiles;
    }

    public function getItems()
    {
        $itemIds = explode(',', $this->payout_item_ids);
        $items = array();
        foreach ($itemIds as $itemId) {
            $items[] = new Item($itemId);
        }

        return $items;
    }

    public function getMiningDrones()
    {
        $miningDroneIds = explode(',', $this->payout_mining_drone_ids);
        $miningDrones = array();
        foreach ($miningDroneIds as $miningDroneId) {
            $miningDrones[] = new MiningDrone($miningDroneId);
        }

        return $miningDrones;
    }

    public function getCity()
    {
        return new City($this->city_id);
    }

    public static function SGet($id)
    {
        return new CityBossProfile($id);
    }

    public static function GetName(int $id)
    {
        $cityBossProfile = self::SGet($id);

        return $cityBossProfile->name;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'mascot_image_url',
            'name',
            'city_id',
            'health',
            'speed',
            'defense',
            'strength',
            'fight_users_required',
            'payout_item_ids',
            'points_payout',
            'exp_payout',
            'fight_cost',
            'dog_tag_payout',
            'payout_mining_drone_ids'
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