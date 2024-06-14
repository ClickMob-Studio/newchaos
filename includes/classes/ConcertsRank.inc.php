<?php

class ConcertsRank extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'ConcertRank';

    public function __construct($id)
    {
        try {
            parent::__construct($id);
        } catch (Exception $e) {
            self::AddRecords(['id' => $id], self::$dataTable);
            parent::__construct($id);
        }
    }

    public static function QueryResult($query)
    {
        $results = [];
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return null;
        }
        while ($row = mysqli_fetch_object($res)) {
            $results[] = $row;
        }

        return $results;
    }

    public static function Reputation()
    {
        $query = 'SELECT * from ' . self::$dataTable . ' order by reputation desc limit 50';

        return self::QueryResult($query);
    }

    public static function ConcertsCreated()
    {
        $query = 'SELECT * from ' . self::$dataTable . ' order by concertscreated desc limit 50';

        return self::QueryResult($query);
    }

    public static function ConcertsEntered()
    {
        $query = 'SELECT * from ' . self::$dataTable . ' order by concertsentered desc limit 50';

        return self::QueryResult($query);
    }

    public static function MoneyFromConcerts()
    {
        $query = 'SELECT * from ' . self::$dataTable . ' order by moneyfromcreated desc limit 50';

        return self::QueryResult($query);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'reputation',
            'concertsentered',
            'concertscreated',
            'moneyfromcreated',
            'moneyfromentered',
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
