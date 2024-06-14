<?php

class Vote extends BaseObject
{
    public static $dataTable = 'votesite';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function GetActive()
    {
        $votesites = self::GetAll('is_active=1');

        return $votesites;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'name',
            'vote_url',
            'script_callback',
            'points_reward',
            'money_reward',
            'is_active',
            'is_incentivised',
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
