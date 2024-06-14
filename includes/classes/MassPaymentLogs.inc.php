<?php

class MassPaymentLogs extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'masspaymentlogs';

    public static function GetDataTableFields()
    {
        return [
            self::$idField,
            'gangid',
            'sender',
            'receiver',
            'amount',
            'type',
            'time',
        ];
    }

    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable());
    }

    public static function Add($gangid, $sender, $receiver, $amount, $type = 'money')
    {
        $data = [
                    'gangid' => $gangid,
                    'sender' => $sender,
                    'receiver' => $receiver,
                    'amount' => $amount,
                    'type' => $type,
                    'time' => time(),
                ];
        return parent::AddRecords($data, self::GetDataTable());
    }

    public static function Delete($id)
    {
        parent::sDelete(self::GetDataTable(), [self::$idField => $id]);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
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
