<?php

final class Crypto extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'crypto';

    public static function getHistory(int $stockId){
        $queryBuilder = BaseObject::createQueryBuilder();
             $allHistory = $queryBuilder->select(['cost', 'time'])
                 ->from('crypto_history')
                 ->where('stock_id = ?')
                 ->andWhere('time > NOW() - INTERVAL 12 HOUR')
                 ->setParameter(0, $stockId)
                 ->execute();
     
             $graph = [];
             foreach ($allHistory->fetchAll() as $history) {
                 $graph[] = [
                     'x' => $history['time'],
                     'y' => $history['cost'],
                 ];
             }
     
             return $graph;
    }
    public static function Get($id)
    {
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'id',
            'company_name',
            'cost',
            'direction',
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
