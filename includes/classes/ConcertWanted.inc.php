<?php

final class ConcertWanted extends ARObject
{
    public static $idField = 'id';
    public static $dataTable = 'ConcertWanted';
    public $ConcertLevel;
    public $action;

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function Add($userid, $money, $points, $profit, $minCapacity)
    {
        try {
            ConcertRent::SearchByMusician($userid);
            throw new Exception('This soldier is already conducting an operation');
        } catch (SoftException $e) {
        } catch (Exception $e) {
            echo $e->getMessage();
            throw new FailedResult('This soldier is already conducting an operation');
        }

        try {
            self::Search($userid);
            throw new FailedResult('This soldier already posted an announcement for an operation.');
        } catch (Exception $e) {
            if (!is_numeric($money) || !is_numeric($points) || !is_numeric($profit) || !is_numeric($minCapacity)) {
                throw new FailedResult('Invalid values');
            }
            self::AddRecords(['user_id' => $userid, 'Money' => $money,'Points' => $points,'Profit' => $profit,
                    'Min_Capacity' => $minCapacity,'Timestamp' => time(), ], self::$dataTable);
        }
    }

    public static function Search($userid)
    {
        $sql = 'select id from ' . self::$dataTable . ' where user_id=' . $userid;
        $rs = DBi::$conn->query($sql);
        if (mysqli_num_rows($rs) == 0) {
            throw new FailedResult('This soldier is not looking for an operation');
        }

        return  mysqli_result($rs, 0);
    }

    public static function GetObject($userid)
    {
        $sql = 'select id from ' . self::$dataTable . ' where user_id=' . $userid;
        $rs = DBi::$conn->query($sql);
        if (mysqli_num_rows($rs) > 0) {
            return new ConcertWanted(mysqli_result($rs, 0));
        }

        return null;
    }

    public static function Remove($userid)
    {
        $sql = 'delete from ' . self::$dataTable . ' where user_id=' . $userid;
        $rs = DBi::$conn->query($sql);

        return DBi::$conn -> affected_rows;
    }

    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '', false, false, 'cost', 'ASC');
    }

    public static function GetAllById()
    {
        return parent::GetAllById(self::GetIdentifierFieldName(), self::GetDataTableFields(), self::GetDataTable());
    }

    public static function lookingForElements(array $elements, $order = 'grpgusers.dog_tags', $position = 'desc')
    {
        $list = [];
        $search = '';
        $order .= ' ' . $position;
        foreach ($elements as $key => $value) {
            $search .= ' and ' . $key . "='" . $value . "' ";
        }
        $sql = 'select ConcertWanted.id cid ,grpgusers.dog_tags as level   from  ConcertWanted, grpgusers where grpgusers.id=ConcertWanted.user_id ' . $search . ' order by ' . $order;
        $rs = DBi::$conn->query($sql);

        while ($row = mysqli_fetch_object($rs)) {
            $a = new ConcertWanted($row->cid);
            $a->ConcertLevel = $row->level;
            $a->action = "<a style='cursor:pointer;' onclick='join(" . $a->user_id . ")'>Hire</a>";
            $list[] = $a;
        }

        return $list;
    }

    public static function XDelete(ConcertWanted $object)
    {
        if ($object == null) {
            throw new FailedResult("This soldier don't have any proposal");
        }
        $sql = 'delete from ' . self::$dataTable . " where id='" . $object->id . "'";
        DBi::$conn->query($sql);
    }

    public static function Show($user_id)
    {
        $sql = "select ConcertWanted.id cid ,grpgusers.dog_tags AS level   from  ConcertWanted, grpgusers where grpgusers.id=ConcertWanted.user_id ConcertWanted.user_id='" . $user_id . '';
        $rs = DBi::$conn->query($sql);
        if (mysqli_num_rows($rs) == 0) {
            throw new FailedResult('This soldier is not looking for an operation');
        }
        $row = mysqli_fetch_object($rs);
        $a = new ConcertWanted($row->cid);
        $a->ConcertLevel = $row->level;
        $user = UserFactory::getInstance()->getUser($a->user_id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
                        'Money',
                        'Points',
                        'Profit',
                        'Min_Capacity',
                        'user_id',
                        'Timestamp',
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

?>


















