<?php

class SpokenLanguage extends BaseObject
{
    public static $usePaging = false; //boolean to use paging into GetAll and GetAllById
    public static $paginator = null;
    public static $idField = 'id'; //id field

    public static $dataTable = 'spoken_languages'; // table implemented

    /**
     * Constructor.
     */
    public function __construct()
    {
    }

    /**
     * Returns the fields of table.
     *
     * @todo: use somesort of caching (memcache?)
     *
     * @return array
     */
    public static function GetDataTableFields()
    {
        return MySQL::GetFields(self::$dataTable);
    }

    /**
     * Read func. name.
     *
     * @param int $uid
     * @param array It may be: array('eng', 'ita' ..)
     *
     * @return void
     */
    public static function ResetUserLanguages($uid, $langs = [])
    {
        // Reset
        $query = 'DELETE FROM `' . self::$dataTable . '` WHERE user_id=\'' . $uid . '\'';
        DBi::$conn->query($query);

        // Add new records
        $data = [];
        foreach ($langs as $code) {
            $data[] = [
                'user_id' => $uid,
                'lang' => $code,
            ];
        }
        parent::AddRecords($data, SpokenLanguage::$dataTable);
    }

    /**
     * Returns an array of user spoken languages.
     *
     * @param int $uid
     *
     * @return array (array-map (id => lang))
     */
    public static function GetByUser($uid)
    {
        if (!is_numeric($uid)) {
            return [];
        }

        $ret = [];
        $langs = parent::GetAll(self::GetDataTableFields(), self::$dataTable, '`user_id` = \'' . $uid . '\'');
        foreach ($langs as $obj) {
            $ret[$obj->id] = $obj->lang;
        }

        return $ret;
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
