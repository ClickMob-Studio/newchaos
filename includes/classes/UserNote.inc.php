<?php

final class UserNote extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'usernotes';

    public static function Get($id)
    {
        return new UserNote($id);
    }

    public static function GetAll($userId, $page = false, $limit = 10, $orderBy = false, $dir = 'ASC', $calcFoundRows = true)
    {
        $where = '`user_id` = \'' . $userId . '\'';

        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, $page, $limit, $orderBy, $dir, $calcFoundRows);
    }

    public static function AddRecords($data, $priority = '')
    {
        return parent::AddRecords($data, self::GetDataTable(), $priority);
    }

    public static function GetUsersNote($userId, $toUser)
    {
        $res = DBi::$conn->query('SELECT `' . implode('`, `', self::GetDataTableFields()) . '` FROM `' . self::GetDataTable() . '`
			WHERE `user_id` = \'' . $userId . '\' AND `to_user` = \'' . $toUser . '\'');

        if (mysqli_num_rows($res) == 1) {
            return mysqli_fetch_object($res);
        }

        return null;
    }

    public static function Exists($userId, $toUser)
    {
        $res = DBi::$conn->query('SELECT `id` FROM `' . self::GetDataTable() . '`
			WHERE `user_id` = \'' . $userId . '\' AND `to_user` = \'' . $toUser . '\'');

        return mysqli_num_rows($res) == 1;
    }

    public static function Update($data)
    {
        if (empty($data) || !is_array($data)) {
            return false;
        }

        DBi::$conn->query('UPDATE `' . self::GetDataTable() . '`
			SET `text` = \'' . $data['text'] . '\'
			WHERE `user_id` = \'' . $data['user_id'] . '\' AND `to_user` = \'' . $data['to_user'] . '\'');

        return DBi::$conn -> affected_rows;
    }

    public static function Delete($data)
    {
        if (empty($data) || !is_array($data)) {
            return false;
        }

        DBi::$conn->query('DELETE FROM `' . self::GetDataTable() . '`
			WHERE `user_id` = \'' . $data['user_id'] . '\' AND `to_user` = \'' . $data['to_user'] . '\'');

        return DBi::$conn -> affected_rows;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'user_id',
            'to_user',
            'text',
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
