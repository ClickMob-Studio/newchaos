<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ImageName extends BaseObject
{
    public static $idField = 'id'; //id field
    public static $dataTable = 'username_requests'; // table implemented

    public function __construct($id = null)
    {
        if ($id > 0) {
            parent::__construct($id);
        }
    }
    public function GetAll(){
        $sql DBi::$conn->query('SELECT * FROM '. $dataTable);
        return $sql;
    }
    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'userid',
            'image',
            'time',
        ];
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected static function GetAllByUserId($user_id, $field, $status = null, $orderby = '', $sort = '', $extraFields = '')
    {
        return self::XGetAll($field . ' = \'' . $user_id . '\'', $status, $orderby, $sort, $extraFields);
    }
}
