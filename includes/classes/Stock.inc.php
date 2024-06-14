<?php

final class Stock extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'stocks';

    public static function GetAll()
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `' . implode('`, `', self::GetDataTableFields()) . '` FROM `' . self::$dataTable . '`');
        if ($res->num_rows == 0) {
            return null;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    /**
     * Get a stock by ID
     *
     * @param $id
     * @return array|bool|mixed|string|null
     */
    public static function GetById($id)
    {
        return self::Get(self::GetDataTableFields(), self::$dataTable, self::$idField, (int) $id);
    }

    public static function GetAllByFields($fieldStr)
    {
        $i = 0;
        $objs = [];
        $res = DBi::$conn->query('SELECT ' . $fieldStr . ' FROM `' . self::$dataTable . '`');
        if ($res->num_rows == 0) {
            return null;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[$i++] = $obj;
        }

        return $objs;
    }

    public static function SetCost($sid, $value, $direction = 'static')
    {
        $updateStock = BaseObject::createQueryBuilder();
        $updateStock->update('stocks')
            ->set('cost', (int) $value)
            ->set('direction', '"' . $direction . '"')
            ->where('id = ?')
            ->setParameter(0, (int) $sid)
            ->execute();

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder->insert('stocks_history')
            ->values([
                'stock_id' => $sid,
                'cost' => $value,
            ])
            ->execute();
    }

    /**
     * Get history information for a stock.
     *
     * @param int $stockId
     * @return mixed[]
     */
    public static function getHistory(int $stockId)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $allHistory = $queryBuilder->select(['cost', 'time'])
            ->from('stocks_history')
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

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'company_name',
            'cost',
            'direction',
            'color',
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
