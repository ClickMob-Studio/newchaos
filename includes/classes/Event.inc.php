<?php

class Event extends BaseObject
{
    const ARCHIVED = 1;

    public static $idField = 'id';
    public static $dataTable = 'events';

    public static function GetAllForUser($uid, $search = [])
    {
        parent::$usePaging = true;
        $where = '`to`=\'' . $uid . '\'';

        if (isset($search['timesent']) && !empty($search['timesent'])) {
            $day = 86400;
            $where .= ' AND `timesent` >= \'' . (time() - $day * $search['timesent']) . '\'';
        }

        if (isset($search['viewed']) && !empty($search['viewed'])) {
            $where .= ' AND `viewed` = \'' . $search['viewed'] . '\'';
        }

        if (isset($search['type']) && !empty($search['type'])) {
            if (is_array($search['type'])) {
                //$search['type'] = array($search['type']);
                $where .= ' AND `type` IN (\'' . implode("','", $search['type']) . '\')';
            } else {
                $where .= ' AND `type` = \'' . $search['type'] . '\'';
            }
        }

        if (isset($search['box'])) {
            $where .= ' AND `box` = \'' . $search['box'] . '\'';
        }

        self::$generatePagingQryString = false;

        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'timesent', $dir = 'DESC');
    }

    public static function GetArchivedCount($uid)
    {
        return MySQL::GetSingle('SELECT COUNT(id) FROM `events` WHERE box = 1 AND `to`=\'' . $uid . '\'');
    }

    public static function Add($id, $text, $type = 'Normal', $box = 0)
    {
        $text = stripslashes($text);
        $text = mysqli_real_escape_string(DBi::$conn, trim($text));
        $text = str_replace('\r', ' ', $text);
        $text = str_replace('\n', ' ', $text);
        DBi::$conn->query('INSERT INTO `events` SET `to`="' . $id . '", `timesent`="' . time() . '", `text`="' . $text . '", `type`="' . $type . '", `box`="' . $box . '"');
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }

        return true;
    }

    public static function AddForAll($text, $type = 'Normal', $box = 0)
    {
        $text = stripslashes($text);
        $text = mysqli_real_escape_string(DBi::$conn, trim($text));
        $text = str_replace('\r', ' ', $text);
        $text = str_replace('\n', ' ', $text);

        $userCount = DBi::$conn->query("SELECT COUNT(id) as user_count FROM grpgusers");
        $userCount = mysqli_fetch_assoc($userCount);

        $i = 1;
        $sql = 'INSERT INTO `events` (`to`, `timesent`, `text`, `type`, `box`) VALUES ';
        while ($i <= $userCount['user_count']) {
            $sql .= '("' . $i . '", "' . time() . '", "' . $text . '", "' . $type . '", "' . $box . '")';

            if ($i % 100 == 0) {
                $sql .= ';';

                DBi::$conn->query($sql);
                $sql = 'INSERT INTO `events` (`to`, `timesent`, `text`, `type`, `box`) VALUES ';
            } else {
                if ($i == $userCount['user_count']) {
                    $sql .= ';';
                } else {
                    $sql .= ',';
                }
            }

            $i++;
        }

        DBi::$conn->query($sql);


        return true;
    }

    public static function Send($id, $text, $type = 'Normal', $box = 0)
    {
        return Event::Add($id, $text, $type, $box);
    }

    public static function Archive($ids)
    {
        if (!is_array($ids)) {
            $ids = [$ids];
        }

        return DBi::$conn->query('UPDATE `events` SET box = 1 WHERE id IN (' . implode(',', $ids) . ')');
    }

    public static function Delete($id)
    {
        if (!is_array($id)) {
            $id = [$id];
        }
        DBi::$conn->query('DELETE FROM `events` WHERE `id` IN (' . implode(',', $id) . ')');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(sprintf(EVENTS_CANT_DELETE, ''));
        }

        return true;
    }

    public static function MarkAsReadForUser($uid)
    {
        DBi::$conn->query('UPDATE `events` SET `viewed` = \'2\' WHERE `to`="' . $uid . '"');
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }

        return true;
    }

    public static function DeleteAllForUser($uid, $box = 0)
    {
        DBi::$conn->query('DELETE FROM `events` WHERE `to`="' . $uid . '" AND `box` = "' . $box . '"');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(sprintf(EVENTS_NOT_DELETED, $uid));
        }

        return true;
    }

    public static function GetTypesForUser($uid)
    {
        $objs = [];

        $res = DBi::$conn->query('SELECT DISTINCT `type` FROM `events` WHERE `to`="' . $uid . '" AND `type` !=\'\'');

        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj->type;
        }

        return $objs;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'to',
            'timesent',
            'text',
            'viewed',
            'type',
            'box',
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
