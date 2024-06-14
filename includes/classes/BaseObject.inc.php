<?php

abstract class BaseObject
{
    public static $usePaging = false; //boolean to use paging into GetAll and GetAllById
    public static $recordsOnPage = null; // Allow overriding records per page on entity level
    public static $paginator = null;
    public static $generatePagingQryString = true;
    public static $cache = false;

    // Holds all object properties (for php4 compat)
    protected $properties;

    /*
     * Generic constructor
     */
    public function __construct($id)
    {
        if (!is_numeric($id) || $id < 0) {
            $uid = (isset($_SESSION['id']) ? $_SESSION['id'] : 'Unknown');
            throw new CheatingException('User (' . $uid . ') is trying to build a ' . $this->GetClassName() . ' with invalid id: (' . $id . ') !');
        } elseif ($id === 0) {
            return null;
        }
        $sql = 'SELECT `' . implode('`, `', $this->GetDataTableFields()) . '` FROM `' . $this->GetDataTable() . '` WHERE `' . $this->GetIdentifierFieldName() . '`=\'' . $id . '\'';

        if (self::$cache) {
            $cachekey = md5('PS::' . $this->GetClassName() . '::' . $id);
            $this->properties = Cache::Get($cachekey);
        }

        if (!is_array($this->properties) || empty($this->properties)) {
            $res = DBi::$conn->query($sql);
            if (mysqli_num_rows($res) == 0) {
                throw new SoftException('This ' . $this->GetClassName() . ' does not exist !');
            }
            $this->properties = mysqli_fetch_array($res, MYSQLI_ASSOC);

            if (self::$cache) {
                Cache::Set($cachekey, $this->properties);
            }
        }

        foreach ($this->properties as $key => $value) {
            $this->$key = $value;
        }
        // For php4 compat, remove this
        $this->properties = null;
    }

    /*
     * Generic data manipulation methods
     */

    public function SyncFields(array $fields)
    {
        $idField = $this->GetIdentifierFieldName();
        $res = DBi::$conn->query('SELECT `' . implode('`, `', $fields) . '` FROM `' . $this->GetDataTable() . '` WHERE `' . $idField . '`=\'' . $this->$idField . '\'');
        if (mysqli_num_rows($res) == 0) {
            throw new SoftException('This ' . $this->GetClassName() . ' or fields does not exist !');
        }
        $this->properties = mysqli_fetch_array($res, MYSQLI_ASSOC);
        foreach ($this->properties as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function AddRecords($data, $dataTable, $priority = '')
    {
        if (empty($data) || !is_array($data)) {
            return false;
        }

        if (!isset($data[0]) || !is_array($data[0])) {
            $data = [$data];
        }

        $fields = array_keys($data[0]);

        $query = 'INSERT ' . $priority . ' INTO `' . $dataTable . '`(`' . join('`, `', $fields) . '`) VALUES ';
        foreach ($data as $d) {
            $insertData = join("', '", array_map(function ($value) {
                return mysqli_real_escape_string(DBi::$conn, $value);
            }, $d));
            $query .= '(\'' . $insertData . '\'),';
        }
        $query = substr($query, 0, -1);

        $res = DBi::$conn->query($query);
        if (!$res) {
            throw new SQLException(DBi::$conn->error . ' Query: ' . $query);
        }
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }
        return $res ? DBi::$conn -> insert_id : false;
    }

    public static function Get($dataFields, $dataTable, $condKey, $condValue)
    {
        $objs = [];
        $sql = 'SELECT `' . implode('`, `', $dataFields) . '` FROM `' . $dataTable . '` WHERE `' . $condKey . '` = \'' . $condValue . '\'';

        if (self::$cache) {
            $objs = Cache::Get(md5($sql));
        }

        if (empty($objs)) {
            $res = DBi::$conn->query($sql);

            $totalRecords = mysqli_num_rows($res);
            if ($totalRecords == 0) {
                return $objs;
            }

            while ($obj = mysqli_fetch_object($res)) {
                $objs[] = $obj;
            }
        }

        if (self::$cache) {
            Cache::Set(md5($sql), $objs);
        }

        if (count($objs) == 1) {
            return $objs[0];
        } elseif (count($objs) == 0) {
            return null;
        }

        return $objs;
    }

    public function GetAttribute($attrName)
    {
        if (isset($this->$attrName)) {
            return $this->$attrName;
        }

        return null;
    }

    public function SetAttribute($attrName, $value, $max = null)
    {
        if (is_numeric($value) && ($value < 0 || $max < 0)) {
            return false;
        }
        if (is_numeric($value) && $max !== null && $value > $max) {
            $value = $max;
        }
        if($attrName == 'jail'){
            $value = $value + time();
        }
        //if (is_numeric($value))
        //	$value = (int)$value;
        $idField = $this->GetIdentifierFieldName();
        $query = 'UPDATE `' . $this->GetDataTable() . '` SET `' . $attrName . '`=\'' . mysqli_real_escape_string(DBi::$conn, $value) . '\' WHERE `' . $idField . '`=\'' . $this->$idField . '\'';
       DBi::$conn->query($query);
        if (isset($this->$attrName)) {
            $this->$attrName = $value;
        }

        return true;
    }

    /**
     * Set multiple attributes.
     */
    public function SetAttributes(array $attributes)
    {
        $fields = '';
        $coma = '';
        foreach ($attributes as $attribute => $value) {
            $fields .= $coma . '`' . $attribute . '`=\'' . mysqli_real_escape_string(DBi::$conn, $value) . '\'';
            if (isset($this->$attribute)) {
                $this->$attribute = $value;
            }
            $coma = ',';
        }

        $idField = $this->GetIdentifierFieldName();
        $query = 'UPDATE `' . $this->GetDataTable() . '` SET ' . $fields . ' WHERE `' . $idField . '`=\'' . $this->$idField . '\'';
        DBi::$conn->query($query);

        return true;
    }

    public function SetNullAttribute($attrName)
    {
        $idField = $this->GetIdentifierFieldName();
        $query = 'UPDATE `' . $this->GetDataTable() . '` SET `' . $attrName . '`=NULL WHERE `' . $this->GetIdentifierFieldName() . '`=\'' . $this->$idField . '\'';
        DBi::$conn->query($query);
        if (isset($this->$attrName)) {
            $this->$attrName = null;
        }

        return true;
    }

    public function AddToAttribute($attrName, $value, $max = null)
    {
        if (!is_numeric($value) || $value < 0 || $max < 0) {

            return false;
        }
        if (isset($this->$attrName)) {
            $oldAmount = $this->$attrName;
        } else {
            $oldAmount = 0;
        }
        $newAmount = $oldAmount + $value;
        $idField = $this->GetIdentifierFieldName();
        if ($max !== null) {
            if ($newAmount > $max) {
                return $this->SetAttribute($attrName, $max, $max);
            }
            DBi::$conn->query('UPDATE `' . $this->GetDataTable() . '` SET `' . $attrName . '`=(`' . $attrName . '`+' . $value . ') WHERE `' . $idField . '`=\'' . $this->$idField . '\' AND `' . $attrName . '`<=\'' . ($max - $value) . '\'');
        } else {
            DBi::$conn->query('UPDATE `' . $this->GetDataTable() . '` SET `' . $attrName . '`=(`' . $attrName . '`+' . $value . ') WHERE `' . $idField . '`=\'' . $this->$idField . '\'');
        }
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }
        $this->$attrName += $value;

        return true;
    }

    public function RemoveFromAttribute($attrName, $value)
    {
        if (!is_numeric($value) || $value < 0) {
            return false;
        }
        if (isset($this->$attrName)) {
            $oldAmount = $this->$attrName;
        } else {
            $oldAmount = 0;
        }
        //	$value = (int)$value;
        $newAmount = $oldAmount - $value;
        $idField = $this->GetIdentifierFieldName();
        if ($newAmount < 0) {
            return $this->SetAttribute($attrName, 0);
        }
        DBi::$conn->query('UPDATE `' . $this->GetDataTable() . '` SET `' . $attrName . '`=(`' . $attrName . '`-' . $value . ') WHERE `' . $idField . '`=\'' . $this->$idField . '\' AND `' . $attrName . '`>=\'' . $value . '\'');
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }
        $this->$attrName -= $value;

        return true;
    }

    public function ForceRemoveFromAttribute($attrName, $value)
    {
        if (!is_numeric($value) || $value < 0) {
            return false;
        }
        $idField = $this->GetIdentifierFieldName();
        //	$value = (int)$value;
        DBi::$conn->query('UPDATE `' . $this->GetDataTable() . '` SET `' . $attrName . '`=(`' . $attrName . '`-' . $value . ') WHERE `' . $idField . '`=\'' . $this->$idField . '\'');
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }
        $this->$attrName -= $value;

        return true;
    }

    public static function FoundRows()
    {
        $res = DBi::$conn->query('SELECT FOUND_ROWS()');

        return (int) mysqli_result($res, 0, 0);
    }

    //return the paginator object
    public function getPaginator()
    {
        return self::$paginator;
    }

    public static function GetPaginationResults($query, $pagevar = 'page', $byfield = '')
    {
        $objs = [];
        /**Pagination **/

        /**Pagination **/
        if (self::$usePaging) {//If doing paging for records
            $query = preg_replace('/^SELECT/i', 'SELECT SQL_CALC_FOUND_ROWS', $query); // insert SQL_CALC_FOUND_ROWS into query to find total rows into table
            self::$paginator = new Paginator($pagevar);

            self::$paginator->setRecordsPerPage(Paginator::$recordsOnPage);
            $res = DBi::$conn->query(self::$paginator->getLimitQuery($query));

            $totalRecords = MySQL::GetSingle('Select FOUND_ROWS()');

            self::$paginator->setQueryString();
            self::$paginator->setPageData('', $totalRecords, Paginator::$recordsOnPage, Paginator::$scrollPages, true, false, true);
        } else {
            $res = DBi::$conn->query($query);
        }

        $totalRecords = mysqli_num_rows($res);
        if ($totalRecords == 0) {
            return $objs;
        }

        while ($obj = mysqli_fetch_object($res)) {
            if (!empty($byfield)) {
                $objs[$obj->$byfield] = $obj;
            } else {
                $objs[] = $obj;
            }
        }

        return $objs;
    }

    /*
     * Create a doctrine query builder
     */
    public static function createQueryBuilder()
    {
        global $doctrine;

        return $doctrine->createQueryBuilder();
    }

    /*
     * Generic abstracts methods
     */
    abstract protected function GetIdentifierFieldName();

    abstract protected function GetClassName();

    abstract protected static function GetDataTable();

    abstract protected static function GetDataTableFields();

    protected static function sCount($idField, $dataTable, array $conditions = [])
    {
        $query = 'SELECT COUNT(`' . $idField . '`) as `totalCount` FROM `' . $dataTable . '`';
        if (count($conditions) > 0) {
            $query .= ' WHERE ';
        }
        foreach ($conditions as $key => $value) {
            if (is_numeric($value)) {
                $query .= '`' . $key . '` = ' . $value . ' AND ';
            } else {
                $query .= '`' . $key . '` = \'' . $value . '\' AND ';
            }
        }
        if (count($conditions) > 0) {
            $query = substr($query, 0, strlen($query) - 4);
        }
        //echo $query;
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->totalCount;
    }

    protected static function sDelete($dataTable, array $conditions = [])
    {
        $query = 'DELETE FROM `' . $dataTable . '`';
        if (count($conditions) > 0) {
            $query .= ' WHERE ';
        }
        foreach ($conditions as $key => $value) {
            if (is_numeric($value)) {
                $query .= '`' . $key . '` = ' . $value . ' AND ';
            } else {
                $query .= '`' . $key . '` = \'' . $value . '\' AND ';
            }
        }
        if (count($conditions) > 0) {
            $query = substr($query, 0, strlen($query) - 4);
        }
        DBi::$conn->query($query);
        //if (DBi::$conn -> affected_rows == 0)
        //	return false;
        return true;
    }

    protected static function sUpdate($dataTable, array $updates, array $conditions, $quote = true)
    {
        if (count($updates) == 0) {
            return false;
        }
        $query = 'UPDATE `' . $dataTable . '` SET ';
        foreach ($updates as $key => $value) {
            $safeKey = mysqli_real_escape_string(DBi::$conn, $key);
            $safeValue = mysqli_real_escape_string(DBi::$conn, $value);
            if (is_numeric($value) || !$quote) {
                $query .= '`' . $safeKey . '` = ' . $safeValue . ' , ';
            } else {
                $query .= '`' . $safeKey . '` = \'' . $safeValue . '\' , ';
            }
        }
        $query = substr($query, 0, strlen($query) - 3);
        if (count($conditions) > 0) {
            $query .= ' WHERE ';
        }
        foreach ($conditions as $key => $value) {
            $safeKey = mysqli_real_escape_string(DBi::$conn, $key);
            $safeValue = mysqli_real_escape_string(DBi::$conn, $value);
            if (is_numeric($value)) {
                $query .= '`' . $safeKey . '` = ' . $safeValue . ' AND ';
            } else {
                $query .= '`' . $safeKey . '` = \'' . $safeValue . '\' AND ';
            }
        }
        if (count($conditions) > 0) {
            $query = substr($query, 0, strlen($query) - 4);
        }
        //echo $query."<br>";
        DBi::$conn->query($query);

        return true;
    }

    protected static function XGet($dataFields, $dataTable, $whereClause = '')
    {
        $objs = [];

        $query = 'SELECT `' . implode('`, `', $dataFields) . '` FROM `' . $dataTable . '` WHERE ' . $whereClause;

        if (self::$cache) {
            $objs = Cache::Get(md5($query));
        }

        if (empty($objs)) {
            $res = DBi::$conn->query($query);
            if (mysqli_num_rows($res) == 0) {
                return $objs;
            }
            while ($obj = mysqli_fetch_object($res)) {
                $objs[] = $obj;
            }
            if (count($objs) == 1) {
                return $objs[0];
            } elseif (count($objs) == 0) {
                return null;
            }
        }

        if (self::$cache) {
            Cache::Set(md5($query), $objs);
        }

        return $objs;
    }

    /**
     * Returns all rows by some criterias
     * TODO: suggest to refactor it.. in CakePHP's way: having single params called $options
     * with keys as params like: array('fields'=>array(<list goes here>), 'where' => <str>, 'limit'=><num> etc..).
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
        $objs = [];
        $query = 'SELECT ';
        if ($calcFoundRows) {
            $query .= 'SQL_CALC_FOUND_ROWS ';
        }

        if ($quoteFields) {
            $query .= '`' . implode('`, `', $dataFields) . '` FROM `' . $dataTable . '`';
        } else {
            $query .= '' . implode(', ', $dataFields) . ' FROM `' . $dataTable . '`';
        }

        if ($whereClause) {
            $query .= ' WHERE ' . $whereClause;
        }
        if ($orderBy && $quoteFields) {
            $query .= ' ORDER BY `' . $orderBy . '` ' . $dir;
        } elseif ($orderBy) {
            $query .= ' ORDER BY ' . $orderBy . ' ' . $dir;
        }

        /**Pagination **/
        if (self::$usePaging) {//If doing paging for records
            if (!$calcFoundRows) {
                $query = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query); // insert SQL_CALC_FOUND_ROWS into query to find total rows into table
            }

            self::$paginator = new Paginator(Paginator::$variableName);
            if (self::$recordsOnPage) {
                self::$paginator->setRecordsPerPage(self::$recordsOnPage);
            } else {
                self::$paginator->setRecordsPerPage(Paginator::$recordsOnPage);
            }

            $res = DBi::$conn->query(self::$paginator->getLimitQuery($query));
            $totalRecords = MySQL::GetSingle('SELECT FOUND_ROWS()');

            self::$paginator->setQueryString('', self::$generatePagingQryString);
            self::$paginator->setPageData('', $totalRecords, Paginator::$recordsOnPage, Paginator::$scrollPages, true, false, true);
        } else {
            if ($page || $limit) {
                $start = ($page - 1 < 1 ? 0 : ($page - 1)) * $limit;
                $query .= ' LIMIT ' . $start . ', ' . $limit;
            }

            if (self::$cache) {
                $objs = Cache::Get(md5($query));
            }

            if (empty($objs)) {
                $res = DBi::$conn->query($query);
            }
        }

        if (empty($objs)) {
            $totalRecords = mysqli_num_rows($res);
            if ($totalRecords == 0) {
                return $objs;
            }

            while ($obj = mysqli_fetch_object($res)) {
                $objs[] = $obj;
            }
        }

        if (self::$cache) {
            Cache::Set(md5($query), $objs);
        }

        return $objs;
    }

    protected static function GetAllById($idField, $dataFields, $dataTable, $conditionStr = '')
    {
        $objs = [];
        $query = 'SELECT `' . implode('`, `', $dataFields) . '` FROM `' . $dataTable . '` ';
        if ($conditionStr != '') {
            $conditionStr = 'WHERE ' . $conditionStr;
        }
        $query .= $conditionStr;

        /**Pagination **/
        if (self::$usePaging) {//If doing paging for records
            $query = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query); // insert SQL_CALC_FOUND_ROWS into query to find total rows into table
            self::$paginator = new Paginator(Paginator::$variableName);
            self::$paginator->setRecordsPerPage(Paginator::$recordsOnPage);
            //echo self::$paginator->getLimitQuery($query);
            $res = DBi::$conn->query(self::$paginator->getLimitQuery($query));

            $totalRecords = MySQL::GetSingle('Select FOUND_ROWS()');

            self::$paginator->setQueryString('', self::$generatePagingQryString);
            self::$paginator->setPageData('', $totalRecords, Paginator::$recordsOnPage, Paginator::$scrollPages, true, false, true);
        } else {
            if (self::$cache) {
                $objs = Cache::Get(md5($query));
            }

            if (empty($objs)) {
                $res = DBi::$conn->query($query);
            }
        }

        if (empty($objs)) {
            $totalRecords = mysqli_num_rows($res);
            if ($totalRecords == 0) {
                return $objs;
            }

            while ($obj = mysqli_fetch_object($res)) {
                $objs[$obj->$idField] = $obj;
            }
        }

        if (self::$cache) {
            Cache::Set(md5($query), $objs);
        }

        return $objs;
    }

    protected static function GetAllByField($dataFields, $dataTable, $field = 'id', $order = 'ASC')
    {
        $objs = [];
        $query = 'SELECT `' . implode('`, `', $dataFields) . '` FROM `' . $dataTable . '` ORDER BY `' . $field . '` ' . $order;

        if (self::$cache) {
            $objs = Cache::Get(md5($query));
        }

        if (empty($objs)) {
            $res = DBi::$conn->query($query);
            if (mysqli_num_rows($res) == 0) {
                return $objs;
            }
            while ($obj = mysqli_fetch_object($res)) {
                $objs[] = $obj;
            }
        }

        if (self::$cache) {
            Cache::Set(md5($query), $objs);
        }

        return $objs;
    }

    protected static function GetAllByFieldLimited($dataFields, $dataTable, $field = 'id', $order = 'ASC', $limit = 50)
    {
        $objs = [];
        $query = 'SELECT `' . implode('`, `', $dataFields) . '` FROM `' . $dataTable . '` ORDER BY `' . $field . '` ' . $order . ' LIMIT ' . $limit;
        if (self::$cache) {
            $objs = Cache::Get(md5($query));
        }

        if (empty($objs)) {
            $res = DBi::$conn->query($query);

            if (mysqli_num_rows($res) == 0) {
                return $objs;
            }
            while ($obj = mysqli_fetch_object($res)) {
                $objs[] = $obj;
            }
        }

        if (self::$cache) {
            Cache::Set(md5($query), $objs);
        }

        return $objs;
    }
}
