<?php

class BossUser extends CachedObject
{
    public static $dataTable = 'boss_user';
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
        return new BossUser($id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    public static function getForCity(int $cityId)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('boss_user')
            ->where('city_id = :city_id')
            ->setParameter('city_id', $cityId)
            ->andWhere('hours_alive > 0')
            ->andWhere('lives > 0')
            ->setMaxResults(1)
        ;
        $bossUser = $queryBuilder->execute()->fetchAll();

        if (isset($bossUser[0])) {
            return self::SGet($bossUser[0]['id']);
        } else {
            return null;
        }
    }

    private static function calculateStat(int $level)
    {
        // TODO: Figure out formula based on in-game stats at the time of creation


        if ($level < 5) {
            // 15,000
            $stat = mt_rand(5000, 6000);
        } else if ($level < 10) {
            // 100,000
            $stat = mt_rand(30000, 50000);
        } else if ($level < 20) {
            // 500,000
            $stat = mt_rand(175000, 200000);
        } else if ($level < 25) {
            // 1,000,000 + stats
            $stat = mt_rand(300000, 400000);
        } else if ($level < 50) {
            // 5,000,000 + stats
            $stat = mt_rand(1500000, 2000000);
        } else if ($level < 75) {
            // 750,000,000 + stats
            $stat = mt_rand(225000000, 275000000);
        } else {
            // 1,800,000,000 + stats
            $stat = mt_rand(600000000, 750000000);
        }

        return $stat;
    }

    public static function getNameOption()
    {
        $names = array(
            'Slavik Dogron',
            'Tony Dahmer'
        );

        return $names[mt_rand(0, 1)];
    }

    public static function createBossUser()
    {
        $level = mt_rand(1,49);

        $cities = array(1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5, 6 => 6, 7 => 7, 8 => 8, 9 => 9, 10 => 10);
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('city_id')
            ->from('boss_user')
        ;
        $bossUserCities = $queryBuilder->execute()->fetchAll();

        foreach ($bossUserCities as $key => $bossUserCity) {
            if (isset($cities[$bossUserCity['city_id']])) {
                unset($cities[$bossUserCity['city_id']]);
            }
        }

        if (count($cities) === 0) {
            return false;
        }

        $city = $cities[array_rand($cities)];
        $cityClass = City::Get($city);

        $item = 0;
        if (mt_rand(1, 8) === 1) {
            // Hot Coco - 175
            // Lollipop - 249
            // Large Med Pack - 33
            // Morphine - 35
            $itemIds = array(175, 249, 33, 35);

            $item = $itemIds[mt_rand(0, (count($itemIds) - 1))];
        }

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert('boss_user')
            ->values(
                [
                    'city_id' => ':city_id',
                    'name' => ':name',
                    'level' => ':level',
                    'speed' => ':speed',
                    'defense' => ':defense',
                    'strength' => ':strength',
                    'health' => ':health',
                    'hours_alive' => ':hours_alive',
                    'start_time' => ':start_time',
                    'lives' => ':lives',
                    'fight_users_required' => ':fight_users_required',
                    'money_payout' => ':money_payout',
                    'points_payout' => ':points_payout',
                    'exp_payout' => ':exp_payout',
                    'item_payout' => ':item_payout',
                ]
            )
            ->setParameter('city_id', $city)
            ->setParameter('name', self::getNameOption())
            ->setParameter('level', $level)
            ->setParameter('speed', self::calculateStat($level))
            ->setParameter('defense', self::calculateStat($level))
            ->setParameter('strength', self::calculateStat($level))
            ->setParameter('health', ($level * 50))
            ->setParameter('hours_alive', mt_rand(4,8))
            ->setParameter('start_time', time())
            ->setParameter('lives', mt_rand(5,9))
            ->setParameter('fight_users_required', mt_rand(2,4))
            ->setParameter('money_payout', ($level * mt_rand(5000, 20000)))
            ->setParameter('points_payout', ($level * mt_rand(10, 20)))
            ->setParameter('exp_payout', ($level * mt_rand(1000, 5000)))
            ->setParameter('item_payout', $item)
        ;
        $queryBuilder->execute();

        $admin = UserFactory::getInstance()->getUser(2);
        UserAds::Add($admin, $cityClass->name . ' is under attack!', true);
    }

    public static function hasUserBeatBoss(int $userid, int $bossUserId)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('boss_user_fight')
            ->where('boss_user_id = :boss_user_id')
            ->setParameter('boss_user_id', $bossUserId)
            ->andWhere('userids = :userid')
            ->setParameter('userid', $userid)
            ->andWhere('is_fight_won = 1')
        ;
        $bossUserFight = $queryBuilder->execute()->fetchAll();

        if (count($bossUserFight) > 0) {
            return true;
        } else {
            return false;
        }
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'city_id',
            'name',
            'level',
            'speed',
            'defense',
            'strength',
            'health',
            'hours_alive',
            'start_time',
            'lives',
            'fight_users_required',
            'money_payout',
            'points_payout',
            'exp_payout',
            'item_payout',
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