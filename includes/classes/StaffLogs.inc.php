<?php

final class StaffLogs extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'staffLogs';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function logAction($userid, $action)
    {
        $sql = 'INSERT INTO staffLogs (`userid`, `action`) VALUES ('.$userid.', "'.$action.'")';
            echo DBi::$conn->query($sql);
     }
    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'userid',
            'action',
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
