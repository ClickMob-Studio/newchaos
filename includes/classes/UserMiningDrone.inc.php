<?php

class UserMiningDrone extends CachedObject
{
    public static $dataTable = 'user_mining_drone';
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
        return new UserMiningDrone($id);
    }

    public static function getForUser(int $userid, int $miningDroneId = 0)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('*')
            ->from('user_mining_drone')
            ->where('user_id = :userid')
            ->setParameter('userid', $userid)
        ;

        if ($miningDroneId && $miningDroneId > 0) {
            $queryBuilder
                ->andWhere('mining_drone_id = :mining_drone_id')
                ->setParameter('mining_drone_id', $miningDroneId)
            ;
        }

        $userMiningDrones = $queryBuilder->execute()->fetchAllAssociative();

        foreach ($userMiningDrones as $key => $userMiningDrone) {
            $userMiningDrones[$key]['MiningDrone'] = MiningDrone::SGet($userMiningDrone['mining_drone_id']);
        }

        return $userMiningDrones;
    }

    public function getMiningDrone()
    {
        return MiningDrone::SGet($this->mining_drone_id);
    }

    public static function create(int $userid, $miningDroneId = null)
    {
        // 5% Chance Level 5, 25% Chance Level 3 & 70% Chance Level 1
        if (!$miningDroneId) {
            $chance = mt_rand(1,100);

            if ($chance <= 85) {
                $level = 1;
            } else if ($chance <= 98) {
                $level = 3;
            } else {
                $secondChance = mt_rand(1,15);
                if ($secondChance == 2) {
                    $level = 7;
                } else {
                    $level = 5;
                }
            }

            if ($level === 7) {
                $typeChoices = array('strength', 'defense', 'speed', 'exp', 'points');
            } else {
                $typeChoices = array('strength', 'defense', 'speed', 'exp');
            }
            $type = $typeChoices[mt_rand(0,3)];

            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->select('*')
                ->from('mining_drone')
                ->where('level = :level')
                ->setParameter('level', $level)
                ->andWhere('type = :type')
                ->setParameter('type', $type)
                ->setMaxResults(1)
            ;
            $miningDrone = $queryBuilder->execute()->fetchAssociative();
        } else {
            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->select('*')
                ->from('mining_drone')
                ->where('id = :id')
                ->setParameter('id', $miningDroneId)
                ->setMaxResults(1)
            ;
            $miningDrone = $queryBuilder->execute()->fetchAssociative();
        }

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert('user_mining_drone')
            ->values(
                [
                    'user_id' => ':user_id',
                    'mining_drone_id' => ':mining_drone_id',
                    'time_used' => ':time_used',
                    'is_in_use' => ':is_in_use',
                    'total_earnings' => ':total_earnings',

                ]
            )
            ->setParameter('user_id', $userid)
            ->setParameter('mining_drone_id', $miningDrone['id'])
            ->setParameter('time_used', 0)
            ->setParameter('is_in_use', 0)
            ->setParameter('total_earnings', 0)
        ;
        $queryBuilder->execute();

        $response = array(
            'level' => $level,
            'type' => $type,
            'mining_drone' => $miningDrone,
        );

        return $response;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'user_id',
            'mining_drone_id',
            'time_used',
            'is_in_use',
            'total_earnings'
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
?>