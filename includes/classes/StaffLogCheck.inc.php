<?php

final class StaffLogCheck extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'staffLogs';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function getConflicts()
    {
        $sql = "SELECT ip, COUNT(*) as count FROM grpgusers GROUP BY ip HAVING count > 1";
        $result = DBi::$conn->query($sql);
        return $result;
    }

    public static function Information($ip){
        //filter the ip
        $ip = filter_var($ip, FILTER_VALIDATE_IP);
        $sql = DBi::$conn->query("SELECT id FROM grpgusers WHERE ip = '$ip'");
        return $sql;
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
