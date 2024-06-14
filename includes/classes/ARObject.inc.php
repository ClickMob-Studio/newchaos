<?php

abstract class ARObject
{
    //public static $dirty = false; // Variable that says if we need to update the object or not
    public static $usePaging = false; //boolean to use paging into GetAll and GetAllById
    public static $paginator = null;

    // Holds all object properties (for php4 compat)
    protected $autoCommit = false;
    protected $autoReload = false;
    protected $dirty = false;
    protected $properties;
    protected $changedProperties;
    //protected $dirtyProperties;

    /*
     * Generic constructor
     */
    public function __construct($id)
    {
        $this->Load($id);
    }

    public function __set($field, $value)
    {
        if (!isset($this->properties[$field])) {
            throw new SoftException('Invalid assignment in ' . $this->GetClassName() . ' object. Field ' . $field . ' does not exist.');
        }
        //$this->properties[$field] = $value;
        if ($value != $this->properties[$field]) {
            $this->changedProperties[$field] = $value;
            $this->dirty = true;
            if ($this->autoCommit === true) {
                $this->Save();
            }
        }
    }

    public function __get($field)
    {
        if (!isset($this->properties[$field])) {
            throw new SoftException('Invalid fetch in ' . $this->GetClassName() . ' object. Field ' . $field . ' does not exist.');
        }
        if (isset($this->changedProperties[$field])) {
            return $this->changedProperties[$field];
        }

        return $this->properties[$field];
    }

    // Configuration methods

    public function AutoReload() // Activates the automatic reload of an object fields when concurrency has prevented an update.
    {
        $this->autoReload = true;
    }

    public function ManualReload()
    {
        $this->autoReload = false;
    }

    public function AutoCommit()
    {
        $this->autoCommit = true;
    }

    public function ManualCommit()
    {
        $this->autoCommit = false;
    }

    public function Save()
    {
        if ($this->dirty === false) {
            return true;
        }
        $idField = $this->GetIdentifierFieldName();
        $query = 'UPDATE `' . $this->GetDataTable() . '` SET ';
        foreach ($this->changedProperties as $key => $value) {
            $query .= ' `' . $key . '` = \'' . $value . '\' , ';
        }
        $query = substr($query, 0, strlen($query) - 2);
        $query .= ' WHERE `' . $idField . '`=\'' . $this->$idField . '\' ';
        foreach ($this->properties as $key => $value) {
            if ($key != $idField) {
                $query .= ' AND `' . $key . '` = \'' . $value . '\'';
            }
        }
        echo $query;
        DBi::$conn->query($query);
        if (DBi::$conn -> affected_rows == 0) { // Update failed, probably because of concurrency issues
            echo 'Update failed';
            if ($autoReload !== false) {
                $this->Load();
                $this->Save();
            }

            return false;
        }

        // If update is good
        foreach ($this->changedProperties as $key => $value) {
            $this->properties[$key] = $value;
        }
        $this->changedProperties = [];
        $this->dirty = false;
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

        $query = 'INSERT ' . $priority . ' INTO `' . $dataTable . '`(`' . join('`, `', $fields) . '`) VALUES';
        foreach ($data as $d) {
            $query .= '(\'' . join("', '", $d) . '\'),';
        }
        $query = substr($query, 0, -1);

        $res = DBi::$conn->query($query);
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }

        return $res ? DBi::$conn -> insert_id : false;
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
        //if (is_numeric($value))
        //	$value = (int)$value;
        $idField = $this->GetIdentifierFieldName();
        $query = 'UPDATE `' . $this->GetDataTable() . '` SET `' . $attrName . '`=\'' . $value . '\' WHERE `' . $idField . '`=\'' . $this->$idField . '\'';
        DBi::$conn->query($query);
        if (isset($this->$attrName)) {
            $this->$attrName = $value;
        }

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

    public static function GetPaginationResults($query, $pagevar = 'page')
    {
        $objs = [];
        /**Pagination **/

        $query = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query); // insert SQL_CALC_FOUND_ROWS into query to find total rows into table
        self::$paginator = new Paginator($pagevar);

        self::$paginator->setRecordsPerPage(Paginator::$recordsOnPage);
        $res = DBi::$conn->query(self::$paginator->getLimitQuery($query));

        $totalRecords = MySQL::GetSingle('Select FOUND_ROWS()');

        self::$paginator->setQueryString();
        self::$paginator->setPageData('', $totalRecords, Paginator::$recordsOnPage, Paginator::$scrollPages, true, false, true);

        $totalRecords = mysqli_num_rows($res);
        if ($totalRecords == 0) {
            return $objs;
        }

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
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

        return true;
    }

    protected static function sUpdate($dataTable, array $updates, array $conditions)
    {
        if (count($updates) == 0) {
            return false;
        }
        $query = 'UPDATE `' . $dataTable . '` SET ';
        foreach ($updates as $key => $value) {
            if (is_numeric($value)) {
                $query .= '`' . $key . '` = ' . $value . ' , ';
            } else {
                $query .= '`' . $key . '` = \'' . $value . '\' , ';
            }
        }
        $query = substr($query, 0, strlen($query) - 3);
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
        //echo $query."<br>";
        DBi::$conn->query($query);

        return true;
    }

    protected static function XGet($dataFields, $dataTable, $whereClause = '')
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `' . implode('`, `', $dataFields) . '` FROM `' . $dataTable . '` WHERE ' . $whereClause);
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

        return $objs;
    }

    protected static function Get($dataFields, $dataTable, $condKey, $condValue)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `' . implode('`, `', $dataFields) . '` FROM `' . $dataTable . '` WHERE `' . $condKey . '` = \'' . $condValue . '\'');

        $totalRecords = mysqli_num_rows($res);
        if ($totalRecords == 0) {
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

        return $objs;
    }

    protected static function GetAll($dataFields, $dataTable, $whereClause = '', $page = false, $limit = false, $orderBy = false, $dir = 'ASC', $calcFoundRows = false)
    {
        $objs = [];
        $query = 'SELECT ';
        if ($calcFoundRows) {
            $query .= 'SQL_CALC_FOUND_ROWS ';
        }
        $query .= '`' . implode('`, `', $dataFields) . '` FROM `' . $dataTable . '`';
        if ($whereClause) {
            $query .= ' WHERE ' . $whereClause;
        }
        if ($orderBy) {
            $query .= ' ORDER BY `' . $orderBy . '` ' . $dir;
        }

        /**Pagination **/
        if (self::$usePaging) {//If doing paging for records
            if (!$calcFoundRows) {
                $query = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query); // insert SQL_CALC_FOUND_ROWS into query to find total rows into table
            }

            self::$paginator = new Paginator(Paginator::$variableName);
            self::$paginator->setRecordsPerPage(Paginator::$recordsOnPage);
            //echo self::$paginator->getLimitQuery($query);
            $res = DBi::$conn->query(self::$paginator->getLimitQuery($query));

            $totalRecords = MySQL::GetSingle('Select FOUND_ROWS()');

            self::$paginator->setQueryString();
            self::$paginator->setPageData('', $totalRecords, Paginator::$recordsOnPage, Paginator::$scrollPages, true, false, true);
        } else {
            if ($page || $limit) {
                $start = ($page - 1 < 1 ? 0 : ($page - 1)) * $limit;
                $query .= ' LIMIT ' . $start . ', ' . $limit;
            }

            $res = DBi::$conn->query($query);
        }

        $totalRecords = mysqli_num_rows($res);
        if ($totalRecords == 0) {
            return $objs;
        }

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
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
            $objs[$obj->$idField] = $obj;
        }

        return $objs;
    }

    protected static function GetAllByField($dataFields, $dataTable, $field = 'id', $order = 'ASC')
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `' . implode('`, `', $dataFields) . '` FROM `' . $dataTable . '` ORDER BY `' . $field . '` ' . $order);
        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    protected static function GetAllByFieldLimited($dataFields, $dataTable, $field = 'id', $order = 'ASC', $limit = 50)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `' . implode('`, `', $dataFields) . '` FROM `' . $dataTable . '` ORDER BY `' . $field . '` ' . $order . ' LIMIT ' . $limit);
        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    /*
     * Generic data manipulation methods
     */
    private function Load($id = null)
    {
        if ($id === null) {
            $idField = $this->GetIdentifierFieldName();
            if ($this->$idField == null) {
                throw new SoftException('This ' . $this->GetClassName() . ' does not exist !');
            }
            $id = $this->$idField;
        }
        $res = DBi::$conn->query('SELECT `' . implode('`, `', $this->GetDataTableFields()) . '` FROM `' . $this->GetDataTable() . '` WHERE `' . $this->GetIdentifierFieldName() . '`=\'' . $id . '\'');
        if (mysqli_num_rows($res) == 0) {
            throw new SoftException('This ' . $this->GetClassName() . ' does not exist !');
        }
        $this->properties = mysqli_fetch_array($res, MYSQLI_ASSOC);
        //foreach ($this->properties as $key => $value)
        //$this->dirtyProperties[$key] = false;
        $this->dirty = false;
    }
}
