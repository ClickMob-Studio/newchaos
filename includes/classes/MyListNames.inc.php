<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 *//*
CREATE TABLE `prisonst_prisondb`.`MyListNames` (
`id` INT NOT NULL ,
`user` INT NOT NULL ,
`ListNumber` INT NOT NULL ,
`Name` VARCHAR( 255 ) NOT NULL DEFAULT '',
PRIMARY KEY ( `id` )
) ENGINE = MYISAM */
class MyListNames extends BaseObject
{
    public static $idField = 'id'; //id field
    public static $dataTable = 'MyListNames'; // table implemented

    public function __construct($id = null)
    {
        if ($id > 0) {
            parent::__construct($id);
        }
    }

    public static function getAll($user, $field = null, $order = null)
    {
        $objs = [];
        $str = 'SELECT id, Name from MyListNames WHERE user=' . $user;
        $res = DBi::$conn->query($str);
        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function getName($user, $listNumber)
    {
        return MySQL::GetSingle('SELECT Name FROM ' . MyListNames::$dataTable . ' WHERE user = ' . $user . ' AND id = ' . $listNumber);
    }

    public static function addElement($user, $name)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT count(id) num from  MyListNames WHERE user=' . $user);

        if (mysqli_num_rows($res) != 0) {
            while ($obj = mysqli_fetch_object($res)) {
                $objs[] = $obj;
            }
        }

        if ($objs[0]->num < 8) {
            $num = $objs[0];

            if ($num->num == 0) {
                $id = 0;
            } else {
                $res = DBi::$conn->query('SELECT id from  MyListNames WHERE user=' . $user);
                while ($obj = mysqli_fetch_object($res)) {
                    $objs[] = $obj;
                }
                $id = 0;
                $old = 1;
                while ($id != $old) {
                    $old = $id;
                    foreach ($objs as $key => $obj) {
                        if ($obj->id == $id) {
                            ++$id;
                        }
                    }
                }
            }

            DBi::$conn->query('insert into MyListNames (user,Name) values(' . $user . ',\'' . $name . '\')');
        } else {
            throw new  FailedResult(NUMBER_MAX_OF_LIST_REACHED);
        }
    }

    public static function NumberOfElements($user, $listNumber)
    {
        return MySQL::GetSingle('SELECT count(id) FROM ' . MyList::$dataTable . ' WHERE user = ' . $user . ' AND listNumber = ' . $listNumber);
    }

    public static function Remove($user, $listNumber)
    {
        DBi::$conn->query('delete from MyListNames WHERE user=' . $user . ' and id=' . $listNumber);
    }

    public static function ExistsList($user, $listNumber)
    {
        return MySQL::GetSingle('SELECT count(id) FROM ' . MyListNames::$dataTable . ' WHERE user = ' . $user . ' AND id = ' . $listNumber);
    }

    protected static function GetDataTableFields()
    {
        return [
                self::$idField,
                'user',
                'Name',
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
}
