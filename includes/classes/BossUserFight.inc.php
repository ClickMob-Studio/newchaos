<?php

class BossUserFight extends CachedObject
{
    public static $dataTable = 'boss_user_fight';
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
        return new BossUserFight($id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    public static function createBossUserFight(int $bossUserId, int $userid, int $isFightWon)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert('boss_user_fight')
            ->values(
                [
                    'boss_user_id' => ':boss_user_id',
                    'userids' => ':userids',
                    'is_fight_complete' => ':is_fight_complete',
                    'is_fight_won' => ':is_fight_won',
                ]
            )
            ->setParameter('boss_user_id', $bossUserId)
            ->setParameter('userids', $userid)
            ->setParameter('is_fight_complete', 1)
            ->setParameter('is_fight_won', $isFightWon)
        ;
        $queryBuilder->execute();

        if ($isFightWon) {
            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->update('boss_user')
                ->set('lives', 'lives-1')
                ->where('id = :id')
                ->setParameter('id', $bossUserId)
                ->execute()
            ;
        }
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'boss_user_id',
            'userids',
            'is_fight_complete',
            'is_fight_won'
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