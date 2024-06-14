<?php
error_reporting(0);
class BuyVehicle extends BaseObject
{
    public static $idField = 'vehicle_id';
    public static $dataTable = 'vehicle';

    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable());
    }

    public static function GetCurrent($gangid){
        $query = BaseObject::createQueryBuilder();
        $sql = $query->select(['rh.*', 'ft.*'])
            ->from('reg_vehicle', 'rh')
            ->leftJoin('rh', 'vehicle', 'ft', 'rh.vehicle_id = ft.vehicle_id')
            ->where('rh.reg_id = :gangid')
            ->setParameter('gangid', $gangid)
            ->execute();
        return $sql;
    }
    public static function VehicleExists($id){
        $query = BaseObject::createQueryBuilder();
        $sql = $query->select("*")
            ->from("vehicle")
            ->where("vehicle_id = :vehicleid")
            ->setParameter("vehicleid", $id)
            ->execute();
        return $sql->fetchColumn();
    }
    public static function VehicleById($id){
        $query = BaseObject::createQueryBuilder();
        $sql = $query->select("*")
            ->from("vehicle")
            ->where("vehicle_id = :vehicleid")
            ->setParameter("vehicleid", $id)
            ->execute();
        return $sql->fetch();
    }
    public static function VehicleName($id){
        $query = BaseObject::createQueryBuilder();
        $sql = $query->select("vehicle_name")
            ->from("vehicle")
            ->where("vehicle_id = :vehicleid")
            ->setParameter("vehicleid", $id)
            ->execute();
        $name = $sql->fetch();
        return $name['vehicle_name'];
    }
    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'vehicle_id',
            'vehicle_name',
            'vehicle_description',
            'vehicle_type',
            'vehicle_stat',
            'vehicle_price',
            'vehicle_minhanger',
            'vehicle_image'
        ];
    }
}