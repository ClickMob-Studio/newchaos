<?php

class UserBarracksRecord extends CachedObject
{
    public const MUG = 'mugging';
    public const CRIME = 'crimes';
    public const ATTACKS = 'attacks';
    public const HOSPITAL = 'hospital';
    public const TRAINING = 'training';
    public const BOSSFIGHT = 'bossfight';

    public static $dataTable = 'user_barracks_record';
    public static $idField = 'id';

    private $jobRole = null;

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
        return new UserBarracksRecord($id);
    }

    public static function recordAction(string $type, int $userid, int $amount)
    {
        $timeframes = array('hourly', 'daily', 'weekly');

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('user_barracks_record')
            ->where('userid = :userid')
            ->setParameter('userid', $userid)
            ->andWhere('type = :type')
            ->setParameter('type', $type)
        ;
        $userBarracksRecord = $queryBuilder->execute()->fetchAll();

        if (count($userBarracksRecord) === 0) {
            foreach ($timeframes as $timeframe) {
                $queryBuilder = BaseObject::createQueryBuilder();
                $queryBuilder
                    ->insert('user_barracks_record')
                    ->values(
                        [
                            'type' => '?',
                            'userid' => '?',
                            'points' => '?',
                            'timeframe' => '?',
                        ]
                    )
                    ->setParameter(0, $type)
                    ->setParameter(1, $userid)
                    ->setParameter(2, 0)
                    ->setParameter(3, $timeframe)
                ;
                $queryBuilder->execute();
            }
        }

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->update('user_barracks_record')
            ->set('points', 'points+' . $amount)
            ->where('userid = :userid')
            ->setParameter('userid', $userid)
            ->andWhere('type = :type')
            ->setParameter('type', $type)
            ->execute()
        ;
    }

    public static function getForLeaderboard($type, $timeframe, $maxResults = 3)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id, type, userid, points, timeframe')
            ->from('user_barracks_record')
            ->where('type = :type')
            ->setParameter('type', $type)
            ->andWhere('timeframe = :timeframe')
            ->setParameter('timeframe', $timeframe)
            ->orderBy('points', 'DESC')
            ->setMaxResults($maxResults)
        ;
        $userBarracksRecords = $queryBuilder->execute()->fetchAll();

        return $userBarracksRecords;
    }

    public static function getOverallTotal($type, $timeframe)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('SUM(points) as total_points')
            ->from('user_barracks_record')
            ->where('type = :type')
            ->setParameter('type', $type)
            ->andWhere('timeframe = :timeframe')
            ->setParameter('timeframe', $timeframe)

        ;
        $userBarracksRecords = $queryBuilder->execute()->fetchNumeric();

        return $userBarracksRecords;
    }


    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'type',
            'userid',
            'points',
            'timeframe'
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