<?php

abstract class Leaderboards extends BaseObject
{
    public static $limit = 20;
    protected static $idField = 'id';
    protected static $boardTypes = [
        'virus',
    ];

    public static function GetLeaders(string $board): ?array
    {
        if (!in_array($board, self::$boardTypes, true)) {
            return null;
        }
        $column = $board . '_infected_points';
        $result = DBi::$conn->query('SELECT ' . self::$idField . ', ' . $column . ' FROM grpgusers WHERE ' . $column . ' > 0 ORDER BY ' . $column . ' DESC ' . (self::$limit > 0 ? 'LIMIT ' . self::$limit : ''));
        if (!mysqli_num_rows($result)) {
            return null;
        }
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        return $data;
    }
}
