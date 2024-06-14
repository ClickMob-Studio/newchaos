<?php

class BankLog extends CachedObject
{
    public static $dataTable = 'bank_log';
    public static $idField = 'id';

    public function __construct($id)
    {
        parent::__construct($id);

        $this->user = null;
    }

    /*
     * Create a BankLog
     *
     * @param User $user
     * @param string $type
     * @param integer $amount
     */
    public static function create(User $user, string $type, int $amount)
    {
        $now = new \DateTime();

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert(self::$dataTable)
            ->values(
                [
                    'user_id' => ':user_id',
                    'date_time' => ':date_time',
                    'type' => ':type',
                    'amount' => ':amount',
                    'balance' => ':balance',
                ]
            )
            ->setParameter('user_id', $user->id)
            ->setParameter('date_time', $now->format('Y-m-d H:i:s'))
            ->setParameter('type', $type)
            ->setParameter('amount', $amount)
            ->setParameter('balance', $user->bank)
        ;

        return $queryBuilder->execute();

    }

    public static function SGet($id)
    {
        return new BankLog($id);
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
            'date_time',
            'type',
            'amount',
            'balance'
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
