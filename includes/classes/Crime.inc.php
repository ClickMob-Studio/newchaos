<?php

final class Crime extends CachedObject
{
    public static $idField = 'id';
    public static $dataTable = 'crimes';

    public function __construct($id)
    {
        parent::__construct($id);
        $this->name = constant($this->name);
        $this->descriptive = constant($this->descriptive);
        $this->stext = constant($this->stext);
        $this->ftext = constant($this->ftext);
        $this->ctext = constant($this->ctext);
    }

    public static function GetAllByNerve($nerve = null)
    {
        if ($nerve && $nerve != 5000) {
            $objs = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), 'nerve >= ' . $nerve, false, false, 'nerve', 'ASC');
        } else {
            $objs = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '', false, false, 'nerve', 'ASC');
        }

        foreach ($objs as $key => $obj) {
            $obj->name = constant($obj->name);
            $obj->descriptive = constant($obj->descriptive);
            $obj->stext = constant($obj->stext);
            $obj->ftext = constant($obj->ftext);
            $obj->ctext = constant($obj->ctext);
            $obj->impact_stat = $obj->impact_stat;
            $obj->required_security_level = $obj->required_security_level;
            $objs[$key] = $obj;
        }

        return $objs;
    }
    public static function update_missions($level, $securityLevel, $table, $maxcrimes)
    {
        global $user_class, $crimesucceeded;
        if ($user_class->level > $level || $user_class->securityLevel == $securityLevel) {
            $black = DBi::$conn->query('SELECT * FROM ' . $table . ' WHERE userid = ' . $user_class->id);
            if ($black && mysqli_num_rows($black)) {
                DBi::$conn->query('UPDATE ' . $table . ' SET missions = missions + ' . $crimesucceeded . ' WHERE userid = ' . $user_class->id . ' AND missions < '.$maxcrimes);
            } else {
                DBi::$conn->query('INSERT INTO ' . $table . ' (userid, missions) VALUES(' . $user_class->id . ', ' . $crimesucceeded . ')');
            }
        }
    }
    public static function GetByNerve($nerve, $securityLevel = 0)
    {
        if ($securityLevel > 0) {
            $objs = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), 'required_security_level > 0 AND nerve <= ' . $nerve, 0, 1, 'nerve', 'DESC');
        } else {
            $objs = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), 'required_security_level IS NULL AND nerve <= ' . $nerve, 0, 1, 'nerve', 'DESC');
        }


        if (empty($objs)) {
            return [];
        }

        foreach ($objs as $key => $obj) {
            $obj->name = constant($obj->name);
            $obj->descriptive = constant($obj->descriptive);
            $obj->stext = constant($obj->stext);
            $obj->ftext = constant($obj->ftext);
            $obj->ctext = constant($obj->ctext);
            $objs[$key] = $obj;
        }

        return $objs[0];
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'name',
            'descriptive',
            'nerve',
            'jail',
            'stext',
            'ftext',
            'ctext',
            'impact_stat',
            'required_security_level'
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
