<?php

abstract class CachedObject extends BaseObject
{
    public function __construct($id)
    {
        self::$cache = true;
        parent::__construct($id);
        self::$cache = false;
    }

    /**
     * @param array  $dataFields
     * @param string $dataTable
     * @param string $condKey
     * @param string $condValue
     *
     * @return array
     *
     *@see BaseObject::Get()
     */
    public static function Get($dataFields, $dataTable, $condKey, $condValue)
    {
        self::$cache = true;
        $objs = parent::Get($dataFields, $dataTable, $condKey, $condValue);
        self::$cache = false;

        return $objs;
    }

    /**
     * Function used to.
     *
     * @param mixed $class
     * @param mixed $id
     */
    final public static function DeleteCache($class, $id)
    {
        $cachekey = md5('PS::' . $class . '::' . $id);
        $obj = Cache::Get($cachekey);
        if (empty($obj)) {
            return false;
        }

        return Cache::Delete($cachekey);
    }

    /**
     * @see BaseObject::GetAll()
     *
     * @param array  $dataFields
     * @param string $dataTable
     * @param string $whereClause
     * @param int    $page
     * @param int    $limit
     * @param string $orderBy
     * @param string $dir
     * @param bool   $calcFoundRows
     * @param bool   $quoteFields
     *
     * @return array
     */
    protected static function GetAll($dataFields, $dataTable, $whereClause = '', $page = false, $limit = false, $orderBy = false, $dir = 'ASC', $calcFoundRows = false, $quoteFields = true)
    {
        self::$cache = true;
        $objs = parent::GetAll($dataFields, $dataTable, $whereClause, $page, $limit, $orderBy, $dir, $calcFoundRows, $quoteFields);
        self::$cache = false;

        return $objs;
    }

    /**
     * @see BaseObject::GetAllByField()
     *
     * @param array  $dataFields
     * @param string $dataTable
     * @param string $field
     * @param string $order
     *
     * @return array
     */
    protected static function GetAllByField($dataFields, $dataTable, $field = 'id', $order = 'ASC')
    {
        self::$cache = true;
        $objs = parent::GetAllByField($dataFields, $dataTable, $field, $order);
        self::$cache = false;

        return $objs;
    }

    /**
     * @see BaseObject::GetAllByFieldLimited()
     *
     * @param array  $dataFields
     * @param string $dataTable
     * @param string $field
     * @param string $order
     * @param string $limit
     *
     * @return array
     */
    protected static function GetAllByFieldLimited($dataFields, $dataTable, $field = 'id', $order = 'ASC', $limit = 50)
    {
        self::$cache = true;
        $objs = parent::GetAllByFieldLimited($dataFields, $dataTable, $field, $order, $limit);
        self::$cache = false;

        return $objs;
    }

    /**
     * @see BaseObject::GetAllById()
     *
     * @param string $idField
     * @param array  $dataFields
     * @param string $dataTable
     * @param string $conditionStr
     *
     * @return array
     */
    protected static function GetAllById($idField, $dataFields, $dataTable, $conditionStr = '')
    {
        self::$cache = true;
        $objs = parent::GetAllById($idField, $dataFields, $dataTable, $conditionStr);
        self::$cache = false;

        return $objs;
    }

    /**
     * @see BaseObject::XGet()
     *
     * @param unknown_type $dataFields
     * @param unknown_type $dataTable
     * @param unknown_type $whereClause
     *
     * @return unknown
     */
    protected static function XGet($dataFields, $dataTable, $whereClause = '')
    {
        self::$cache = true;
        $objs = parent::XGet($dataFields, $dataTable, $whereClause = '');
        self::$cache = false;

        return $objs;
    }
}
