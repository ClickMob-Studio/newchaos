<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Skin extends BaseObject
{
    public static $idField = 'id'; //id field
    public static $dataTable = 'themes'; // table implemented

    public function __construct($id = null)
    {
        if ($id != null) {
            parent::__construct($id);
        }
    }

    public static function GetAllSkins()
    {
        $arr = [];
        $sql = 'select id from ' . self::$dataTable . ' order by id asc';
        $res = DBi::$conn->query($sql);
        while ($row = mysqli_fetch_object($res)) {
            $arr[] = new Skin($row->id);
        }

        return $arr;
    }

    public static function GetPath($id)
    {
        $sql = 'select path from ' . self::$dataTable . ' where id=' . $id;
        $res = DBi::$conn->query($sql);
        $res = mysqli_fetch_object($res);

        return $res->path;
    }

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
            'description',
            'path',
            'preview',
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
