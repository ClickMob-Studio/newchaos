<?php

class BackAlleyBotUserLevel extends CachedObject
{
    public static $dataTable = 'back_alley_bot_user_level';
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
        return new BackAlleyBotUserLevel($id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    public function getBackAlleyBot()
    {
        return new BackAlleyBot($this->back_alley_bot_id);
    }

    public function getUser()
    {
        return UserFactory::getInstance()->getUser($this->back_alley_bot_id);
    }

    public function calculateNextLevelPerc()
    {

        $expRequired = 100;

        if ((($this->exp / $expRequired) * 100) > 100) {
            return 100;
        }

        return ($this->exp / $expRequired) * 100;
    }

    public static function getForUser(int $backAlleyBotId, int $userid)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('back_alley_bot_user_level')
            ->where('user_id = :user_id')
            ->setParameter('user_id', $userid)
            ->andWhere('back_alley_bot_id = :back_alley_bot_id')
            ->setParameter('back_alley_bot_id', $backAlleyBotId)
            ->setMaxResults(1)
        ;
        $id = $queryBuilder->execute()->fetch();

        if (isset($id['id'])) {
            return new BackAlleyBotUserLevel($id['id']);
        } else {
            self::create($backAlleyBotId, $userid);

            return self::getForUser($backAlleyBotId, $userid);
        }

        return null;
    }

    public static function create(int $backAlleyBotId, int $userid)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert('back_alley_bot_user_level')
            ->values(
                [
                    'back_alley_bot_id' => ':back_alley_bot_id',
                    'user_id' => ':user_id',
                    'level' => ':level'
                ]
            )
            ->setParameter('back_alley_bot_id', $backAlleyBotId)
            ->setParameter('user_id', $userid)
            ->setParameter('level', 1)
        ;
        $queryBuilder->execute();
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'back_alley_bot_id',
            'user_id',
            'level',
            'exp',
            'is_fight_today_complete',
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