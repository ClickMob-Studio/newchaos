<?php

final class ConcertLog extends ARObject
{
    public static $idField = 'id';

    public static $dataTable = 'ConcertLog';

    public $players;

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function Add($totalDays, $ticketPrice, $room, $creator, $timestamp, $expire, $concertLevel, $ticketSold, $playerList, $money, $points, $Players, $Name, $Reputation, $id)
    {
        $Name = addslashes($Name);

        self::AddRecords(['id' => $id, 'TotalDays' => $totalDays, 'TicketPrice' => $ticketPrice, 'Room' => $room,
            'Creator' => $creator, 'Timestamp' => $timestamp, 'Expire' => $expire, 'ConcertLevel' => $concertLevel,

            'TicketSold' => $ticketSold, 'TicketPrice' => $ticketPrice, 'PlayerList' => $playerList, 'Money' => $money,
            'Points' => $points, 'Players' => $Players, 'Name' => $Name, 'Dog Tags' => $Reputation, ], self::$dataTable);
    }

    public static function ConcertsOnLast24hours($userid)
    {
        $query = 'SELECT count(id) FROM `ConcertLog` WHERE `timestamp` > ' . (time() - 24 * 60 * 60) . ' and Creator =' . $userid;

        $rs = DBi::$conn->query($query);

        return mysqli_result($rs, 0);
    }

    public static function SearchByParticipant($userid, $search)
    {
        $list = [];

        $query = "SELECT * FROM `ConcertLog` WHERE `Players` like '%" . $userid . "%' and Expire=4 order by " . $search;

        $rs = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($rs)) {
            $room = new ConcertRoom($row->Room);

            if (($rep = self::CheckBonus($userid, $row->id)) !== false) {
                $row->Reputation = $rep;
            }

            $row->Room = $room->Name;

            $row->Name = stripslashes($row->Name);

            $row->Money = '$' . $row->Money;

            $row->action = "<a style='cursor:pointer' onClick='ShowId(" . $row->id . ")'><img src=\"images/buttons/folder_magnify.png\"></a>";

            $list[] = $row;
        }

        return $list;
    }

    public static function Last50Concerts($search)
    {
        $list = [];

        $query = 'SELECT * FROM `ConcertLog` WHERE Expire=4 order by id desc, ' . $search . ' limit 50';

        $rs = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($rs)) {
            $room = new ConcertRoom($row->Room);

            $row->Room = $room->Name;

            $row->ConcertLevel = round($row->ConcertLevel, 2);

            $row->Name = stripslashes($row->Name);

            $row->Money = '$' . $row->Money;

            $row->action = "<a style='cursor:pointer' onClick='ShowId(" . $row->id . ")'><img src=\"images/buttons/folder_magnify.png\"></a>";

            $list[] = $row;
        }

        return $list;
    }

    public static function SearchByCreator($userid, $search)
    {
        $list = [];

        $query = 'SELECT * FROM `ConcertLog` WHERE `Creator`=' . $userid . ' and Expire=4 order by ' . $search;

        $rs = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($rs)) {
            $room = new ConcertRoom($row->Room);

            $row->Room = $room->Name;

            if (($rep = self::CheckBonus($userid, $row->id)) !== false) {
                $row->Reputation = $rep;
            }

            $row->Name = stripslashes($row->Name);

            $row->Money = '$' . $row->Money;

            $row->action = "<a style='cursor:pointer' onClick='ShowId(" . $row->id . ")'><img src=\"images/buttons/folder_magnify.png\"></a>";

            $list[] = $row;
        }

        return $list;
    }

    public static function getHistoric($id)
    {
        $historic = new ConcertLog($id);

        $player = [];

        $historic->Name = stripslashes($historic->Name);

        $tmp = unserialize($historic->PlayerList);

        foreach ($tmp as $key => $value) {
            $tmp[$key] = unserialize($value);
        }

        $historic->players = $tmp;

        return $historic;
    }

    public static function CheckBonus($userId, $concertId)
    {
        $rs = DBi::$conn->query("select amount from bookrepbonus where concertid=$concertId and playerid=$userId");

        if (mysqli_num_rows($rs) == 0) {
            return false;
        }

        $obj = mysqli_fetch_object($rs);

        return $obj->amount;
    }

    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '', false, false, 'cost', 'ASC');
    }

    public static function GetAllById()
    {
        return parent::GetAllById(self::GetIdentifierFieldName(), self::GetDataTableFields(), self::GetDataTable());
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,

            'TotalDays',

            'ConcertLevel',

            'TicketSold',

            'Room',

            'Creator',

            'Timestamp',

            'TicketPrice',

            'PlayerList',

            'Expire',

            'Money',

            'Points',

            'Players',

            'Name',

            'Reputation',
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

