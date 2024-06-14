<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class gang_member_contribution extends BaseObject
{
    public static $idField = 'id'; //id field
            public static $dataTable = 'gang_member_contributions'; // table implemented

    /**
     * Constructor.
     */
    public function __construct($id = null)
    {
        if ($id > 0) {
            parent::__construct($id);
        }
    }

    public static function GetContributionbyUser($userid)
    {
        $res = DBi::$conn->query('SELECT id ,gang_id,user_id FROM gang_member_contributions WHERE user_id=' . $userid);
        if (mysqli_num_rows($res) == 0) {
            return null;
        }
        $row = mysqli_fetch_assoc($res);

        return $row['id'];
    }

    /**
     * Funtions return all returns.
     *
     * @return array
     */
    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable());
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
                                'gang_id',
                                  'user_id',
                                'atk_money',
                                'atk_xp',
                                'def_money',
                                'def_xp',
                                'mug',
                                'money',
                                'points',
                                'gangcrimes',
                                'crime_money',
                                'atk_point',
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
