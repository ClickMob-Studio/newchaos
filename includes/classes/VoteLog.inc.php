<?php

class VoteLog extends BaseObject
{
    public static $dataTable = 'votesite_log';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    /*
     * Retrieve any votesite_logs for a date and user_id
     *
     * @param DateTime $date
     * @param integer $userId
     *
     * @return array
     */
    public static function GetForToday(\DateTime $date, int $userId)
    {
        $sql = 'date_voted = "' . $date->format('Y-m-d') . '" AND user_id = ' . $userId;

        return VoteLog::GetAll($sql);
    }

    /*
     * Create a VoteLog
     *
     * @param array $data
     *
     * return array
     */
    public static function createVoteLog(array $data)
    {
        $response = [];
        $response['success'] = true;

        if (!isset($data['user_id'])) {
            $response['success'] = false;
        }

        if (!isset($data['votesite_id'])) {
            $response['success'] = false;
        }

        if ($response['success'] === true) {
            $now = new \DateTime();
            DBi::$conn->query('INSERT INTO `votesite_log` SET `user_id`="' . $data['user_id'] . '", `votesite_id`="' . $data['votesite_id'] . '", `date_voted`="' . $now->format('Y-m-d') . '"');
        }

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
            'votesite_id',
            'date_voted',
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
