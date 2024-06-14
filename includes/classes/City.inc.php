<?php

final class City extends CachedObject
{
    public static $idField = 'id';
    public static $dataTable = 'cities';

    public function __construct($id)
    {
        parent::__construct($id);
        $this->name = constant($this->name);
    }

    public static function Get($id)
    {
        return new City($id);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAllById(self::GetIdentifierFieldName(), self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function GetAllCities($where = '')
    {
        $objs = self::GetAll($where);
        foreach ($objs as $key => $obj) {
            $obj->name = constant($obj->name);
            $objs[$key] = $obj;
        }

        return $objs;
    }

    public static function GetAllByReq()
    {
        return parent::GetAllByField(self::GetDataTableFields(), self::GetDataTable(), 'levelreq');
    }

    public static function GetTotalUsers($cityId)
    {
        $city = new City($cityId);

        return $city->nbPlayers;
    }

    public static function UpdateTotalUsers()
    {
        $cities = self::GetAll();
        foreach ($cities as $id => $city) {
            $query = 'UPDATE `' . self::GetDataTable() . '` SET `nbPlayers` = (SELECT count(id) as total FROM `grpgusers` WHERE `city`=\'' . $id . '\') WHERE `' . self::GetIdentifierFieldName() . '` = ' . $id;
            DBi::$conn->query($query);
        }

        return true;
    }

    /*
     * Retrive the users in a city
     *
     * @return int
     */
    public function getUserCount()
    {
        $query = DBi::$conn->query('SELECT count(id) as total FROM grpgusers WHERE city=\'' . $this->id . '\'');
        $result = mysqli_fetch_array($query);

        return $result['total'];
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
            'levelreq',
            'description',
            'landleft',
            'landprice',
            'landprice1',
            'nbPlayers',
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

/**
 * Efficiently convert city ID into it's name.
 */
final class CityName
{
    public static $cities = null;

    /**
     * Get a city name by ID.
     *
     * @return string
     */
    public static function get(int $id)
    {
        if (self::$cities === null) {
            foreach (City::GetAll() as $city) {
                self::$cities[$city->id] = constant($city->name);
            }
        }

        return self::$cities[$id];
    }
}
