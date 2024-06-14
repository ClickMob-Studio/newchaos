<?php

class CityBossFightUser extends CachedObject
{
    public static $dataTable = 'city_boss_fight_user';
    public static $idField = 'id';

    private $user;
    private $cityBossFight;

    public function __construct($id)
    {
        parent::__construct($id);

        $this->user = $this->getUser();
        $this->cityBossFight = $this->getCityBossFight();
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function SGet($id)
    {
        return new CityBossFightUser($id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    public static function create(int $cityBossFightId, $user)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert(self::$dataTable)
            ->values(
                [
                    'city_boss_fight_id' => ':city_boss_fight_id',
                    'user_id' => ':user_id'
                ]
            )
            ->setParameter('city_boss_fight_id', $cityBossFightId)
            ->setParameter('user_id', $user->id)
        ;

        return $queryBuilder->execute();
    }

    public static function delete(int $cityBossFightId, int $userId)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->delete('city_boss_fight_user')
            ->where('city_boss_fight_id = :city_boss_fight_id')
            ->setParameter('city_boss_fight_id', $cityBossFightId)
            ->andWhere('user_id = :user_id')
            ->setParameter('user_id', $userId)
            ->execute()
        ;
    }

    public function getUser()
    {
        if ($this->user) {
            return $this->user;
        }
        return UserFactory::getInstance()->getUser($this->user_id);
    }

    public function getCityBossFight()
    {
        if ($this->cityBossFight) {
            return $this->cityBossFight;
        }
        return new CityBossFight($this->city_boss_fight_id);
    }

    public function getIsUserReady()
    {
        $user = $this->getUser();
        $cityBossFight = $this->getCityBossFight();
        $isUserReady = true;

        if ($user->jail > time()) {
            $isUserReady = false;
        }

        if ($user->hospital > time()) {
            $isUserReady = false;
        }

        $eventL = Utility::IsEventRunning('bosslocations');
        if (!$eventL) {
            if ($user->city !== $cityBossFight->getCityBossProfile()->city_id) {
                $isUserReady = false;
            }
        }

        if ($user->energy < $user->GetMaxEnergy()) {
            $isUserReady = false;
        }

        return $isUserReady;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'city_boss_fight_id',
            'user_id'
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