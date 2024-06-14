<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class LastFeatures extends BaseObject
{
    public static $idField = 'id'; //id field
             public static $dataTable = 'lastfeatures'; // table implemented

    /**
     * Constructor.
     */
    public function __construct($id = null)
    {
        if ($id > 0) {
            parent::__construct($id);
        }
    }

    public static function CreateEntry($description)
    {
        $res = DBi::$conn->query('insert into lastfeatures (time,description) value (\'' . time() . '\',\'' . $description . '\')');
    }

    public static function Remove($id)
    {
        $res = DBi::$conn->query('delete from lastfeatures where id=' . $id);
    }

    /**
     * Funtions return all returns.
     *
     * @return array
     */
    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '', false, false, 'time', 'DESC');
    }

    /**
     * Function used to get the data table name which is implemented by class.
     *
     * @return string
     */
    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    /**
     * Returns the fields of table.
     *
     * @return array
     */
    protected static function GetDataTableFields()
    {
        return [
                self::$idField,
                                'time',
                                  'description',
            ];
    }

    /**
     * Returns the identifier field name.
     *
     * @return mixed
     */
    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    /**
     * Function returns the class name.
     *
     * @return string
     */
    protected function GetClassName()
    {
        return __CLASS__;
    }
}
