<?php

final class ConcertRent extends ARObject
{
    public static $idField = 'id';

    public static $dataTable = 'ConcertRent';

    public $room;

    public $MusicianList;

    public $minimal_chance;

    public $max_chance;

    public function __construct($id)
    {
        parent::__construct($id);

        $this->room = new ConcertRoom($this->Room);

        $this->MusicianList = new ConcertMusicians($id);

        $this->TicketPrice = 100;

        $this->max_chance = $this->UpdateTotalChance();

        $this->minimal_chance = $this->max_chance - 35;

        if ($this->minimal_chance < 0) {
            $this->minimal_chance = 0;
        }

        if ($this->max_chance != $this->TotalChance) {
            $this->SetAttribute('TotalChance', $this->max_chance);

            $this->SetAttribute('MinChance', $this->minimal_chance);

            $this->TotalChance = $this->max_chance;

            $this->MinChance = $this->minimal_chance;
        }
    }

    public static function Expire()
    {
        $query = 'SELECT id FROM `' . self::$dataTable . '` WHERE `timestamp` < ' . (time() - (6 * 60 * 60)) . ' and Started=0';

        $rs = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($rs)) {
            $object = new ConcertRent($row->id);

            $object->Log(true);

            $object->delete();

            Event::Add($object->Creator, "Due to the fact that the operation wasn't arranged in less than 6 hours, it has been canceled");
        }
    }

    public static function UpdateDaily()
    {
        $query = 'update `' . self::$dataTable . '` set CurrentDay=CurrentDay+1';

        $rs = DBi::$conn->query($query);

        $query = 'SELECT id FROM `' . self::$dataTable . '` WHERE Started=1';

        $rs = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($rs)) {
            $object = new ConcertRent($row->id);

            if ($object->CurrentDay == 1) {
                $object->FirstDay();
            }

            $object->updateTickets();
        }

        $query = 'SELECT id FROM `' . self::$dataTable . '` WHERE CurrentDay>=TotalDays';

        $rs = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($rs)) {
            $object = new ConcertRent($row->id);

            $object->FinishConcerts();
        }
    }

    public function RemoveMe()
    {
        $sql = 'delete from ' . self::$dataTable . ' where id=' . $this->id;

        $rs = DBi::$conn->query($sql);
    }

    public function FinishConcerts()
    {
        //Payments

        $tota = $this->TicketSold * 1000;

        $endMoney = $tota;

        foreach ($this->MusicianList->musicians as $music) {
            $mr = SUserFactory::getInstance()->getUser($music->id);

            $mo = ($this->TicketSold / $this->room->MaxCapacity) * 8 * $this->TotalDays;

            if ($music->Profit > 0) {
                $user = UserFactory::getInstance()->getUser($music->id);

                $money = $tota * ($music->Profit / 100);

                $endMoney -= $money;

                $music->prize = $money;
                $points = mt_rand(10, 300);
                $newpoints = $user->points + $points;
                if ($newpoints > MAX_POINTS) {
                    $user->SetAttribute('points', MAX_POINTS);
                } else {
                    $user->AddToAttribute('points', $points);
                }
                
                    BattlePass::addExp($user->id, 200);
                    
                $tags = mt_rand(1, 45);
                $tags = $tags * $this->TotalDays;
                $user->AddToAttribute('dog_tags', $tags);

                $user->AddToAttribute('money', $money);

                $a = new ConcertsRank($music->id);

                $a->AddToAttribute('concertsentered', 1);

                $a->AddToAttribute('moneyfromentered', $money);

                Event::Add($user->id, 'Operation has ended. You earned $' . $money . ' from profits, ' . $points . ' points and ' . $tags . ' Dog Tags .');
            } else {
                Event::Add($music->id, 'Operation has ended. Your performance was brilliant, but are you ready for the next operation?');

                $a = new ConcertsRank($music->id);

                $a->AddToAttribute('concertsentered', 1);

                $a->AddToAttribute('moneyfromentered', 0);
            }

            $total = $mo;

            $mod = 0;

            if (UserBooks::UserHasStudied($music->id, 21)) {
                $mod = 0.2;
            }

            if (UserBooks::UserHasStudied($music->id, 22)) {
                $mod += 0.3;
            }

            $total += ceil($mod * $mo);

            $music->AddToAttribute('ConcertLevel', $total);

            $a->AddToAttribute('reputation', $total);

            Objectives::set($music->id, 'concertlevel', $total + $mr->ConcertLevel);

            if ($total != $mo) {
                self::AddRecords(['concertid' => $this->id, 'playerid' => $music->id, 'amount' => $total], 'bookrepbonus');
            }

            unset($mr);

            unset($a);
        }

        $user = UserFactory::getInstance()->getUser($this->Creator);

        $user->AddToAttribute('money', $endMoney);

        $user2 = SUserFactory::getInstance()->getUser($this->Creator);

        $mo = ($this->TicketSold / $this->room->MaxCapacity) * 8 * $this->TotalDays;

        $total = $mo;

        $mod = 0;

        if (UserBooks::UserHasStudied($user2->id, 21)) {
            $mod = 0.2;
        }

        if (UserBooks::UserHasStudied($user2->id, 22)) {
            $mod += 0.3;
        }

        $total += ceil($mod * $mo);

        if ($total != $mo) {
            self::AddRecords(['concertid' => $this->id, 'playerid' => $user2->id, 'amount' => $total], 'bookrepbonus');
        }

        $user2->AddToAttribute('ConcertLevel', $total);

        Objectives::set($user2->id, 'concertlevel', $total + $user2->ConcertLevel);

        $a = new ConcertsRank($this->Creator);

        $a->AddToAttribute('concertscreated', 1);

        $a->AddToAttribute('moneyfromcreated', $endMoney);

        $a->AddToAttribute('reputation', $total);
        $points = mt_rand(10, 300);
        $user->AddToAttribute('points', $points);

        $tags = mt_rand(1, 45);
        $user->AddToAttribute('dog_tags', $tags);
        Event::Add($user->id, sprintf('The operation has ended. You were an commander host. You earned $%s from profits, ' . $points . ' points and ' . $tags . ' Dog Tags .', $endMoney));

        $this->Log(4);

        $this->MusicianList->Finish();

        $this->RemoveMe();
    }

    public function FirstDay()
    {
        Event::Add($this->Creator, 'All arrangements for the operation were made, today we will start seeking funding');

        $this->MusicianList->FirstDay();
    }

    public function Start()
    {
        if (count($this->MusicianList->musicians) < 2) {
            throw new FailedResult('You need to hire at least 2 Soldiers');
        }
        Event::Add($this->Creator, sprintf('The %s has been scheduled. We will look for funding tomorrow.', $this->Name));

        $this->MusicianList->Start();

        $this->SetAttribute('Started', 1);
    }

    public function Cancelled()
    {
        if ($this->Started != 0) {
            throw new FailedResult('You can only cancel operations that were not started');
        }
        $this->Log(3);

        $this->MusicianList->Cancel();

        Event::Add($this->Creator, 'The operation was canceled by Commander');

        $this->RemoveMe();
    }

    public function updateTickets()
    {
        $query = DBi::$conn->query('SELECT * FROM ConcertRoom WHERE id = ' . $this->Room);
        $row = mysqli_fetch_assoc($query);
        //$ticket = mt_rand(1000,10000);
        $ticket = mt_rand(1, 60) / 100 * ($row['MaxCapacity']);
        $tmp = $ticket;
        $ticket += $this->TicketSold;

        if ($ticket > $row['MaxCapacity']) {
            $this->SetAttribute('TicketSold', $row['MaxCapacity']);
        } else {
            $this->SetAttribute('TicketSold', $ticket);
        }
        if ($this->CurrentDay > 1) {
            $sql = "insert into ConcertDaily (`Min`,`Max`,`id_concert`,`sold`,`day`, `perc`) values ('" . $this->MinChance . "','" . $this->TotalChance . "','" . $this->id . "','" . $tmp . "','" . $this->CurrentDay . "','" . ($tmp / $this->room->MaxCapacity * 100) . "')";
            $rs = DBi::$conn->query($sql);
        }
    }

    public function delete()
    {
        $this->MusicianList->Expire();

        $this->RemoveMe();
    }

    public static function ActiveConcerts($search)
    {
        $list = [];

        $query = 'SELECT * FROM `ConcertRent` WHERE Started=1 order by ' . $search;

        $rs = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($rs)) {
            $room = new ConcertRoom($row->Room);

            $a = new ConcertRent($row->id);

            $row->Room = $room->Name;

            $row->Name = stripslashes($row->Name);

            $row->Money = '$' . ($row->TicketSold * $row->TicketPrice);

            $row->ConcertLevel = round($a->MusicianList->average_level, 2);

            $row->NumRappers = count($a->MusicianList->musicians);

            $row->TotalDays = $row->CurrentDay . '/' . $row->TotalDays;

            $row->action = "<a style='cursor:pointer' onClick='ShowId(" . $row->id . ")'><img src=\"images/buttons/folder_magnify.png\"></a>";

            $list[] = $row;
        }

        return $list;
    }

    public function Log($expire = false)
    {
        $Name = $this->Name;

        $totalDays = $this->TotalDays;

        $ticketPrice = $this->TicketPrice;

        $room = $this->Room;

        $creator = $this->Creator;

        $timestamp = $this->Timestamp;

        $concertLevel = $this->MusicianList->average_level;

        $ticketSold = $this->TicketSold;

        $ticketPrice = $this->TicketPrice;

        $Reputation = ($this->TicketSold / $this->room->MaxCapacity) * 8 * $this->TotalDays;

        foreach ($this->MusicianList->musicians as $music) {
            $playerList[] = serialize($music);
        }

        $Players = '';

        foreach ($this->MusicianList->musicians as $music) {
            $Players .= $music->id . ',';
        }

        if ($expire == 4) {
            $money = $this->TicketSold * $this->TicketPrice;
        } else {
            $money = 0;
        }

        ConcertLog::Add($totalDays, $ticketPrice, $room, $creator, $timestamp, $expire, $concertLevel, $ticketSold, serialize($playerList), $money, 0, $Players, $Name, $Reputation, $this->id);
    }

    public static function SearchByMusician($userId)
    {
        $query = 'SELECT Concert FROM `ConcertMusicians` WHERE `Musician` = ' . $userId;

        $rs = DBi::$conn->query($query);

        if (mysqli_num_rows($rs) == 0) {
            throw new SoftException('This soldier has not got any operations');
        }

        return new ConcertMusicians(mysqli_result($rs, 0));
    }

    public static function SearchByCreator($userId)
    {
        $query = 'SELECT id FROM `' . self::$dataTable . '` WHERE `Creator` = ' . $userId;

        $rs = DBi::$conn->query($query);

        if (mysqli_num_rows($rs) == 0) {
            throw new SoftException('This soldier has not got any operations');
        }

        return new ConcertRent(mysqli_result($rs, 0));
    }

    public static function Create($name, SUser $creator, $totalDays, $ticketPrice, ConcertRoom $room)
    {
        $name = addslashes($name);

        if (strlen($name) < 5) {
            throw new FailedResult('The operation name must have at least 5 letters.');
        }
        $sql = 'select id from ' . self::$dataTable . " where Name like '" . $name . "'";

        $rs = DBi::$conn->query($sql);

        if (mysqli_num_rows($rs) != 0) {
            throw new FailedResult('There is already a operation with this name.');
        }
        $tag = DBi::$conn->query('SELECT dog_tags FROM  grpgusers WHERE id = ' . $creator->id);
        $tags = mysqli_fetch_assoc($tag);
        if ($tags['dog_tags'] < $room->MinLevel) {
            throw new FailedResult("You don't have the required dog tags to create a operation");
        }

        if (ConcertLog::ConcertsOnLast24hours($creator->id) == 2) {
            throw new FailedResult('You can only create two operation per day');
        }
        if ($totalDays > 10 || $totalDays < 2) {
            throw new FailedResult('The number of days to start a operation must be between 2 and 6.');
        }

        $user = UserFactory::getInstance()->getUser($creator->id);
        if ($user->money < $room->RoomCost) {
            throw new FailedResult("You don't have enough money on hand to start this operation.");
        }
        $user->RemoveFromAttribute('money', $room->RoomCost);

        self::AddRecords(
            [
                'Name' => $name,
                'TotalDays' => $totalDays,
                'TicketPrice' => 1000,
                'Room' => $room->id,
                'Creator' => $creator->id,
                'Timestamp' => time(),
                'Started' => 0,
            ],
            self::$dataTable);
    }

    public function UpdateTotalExtra()
    {
        $extra = floor($this->MusicianList->sum / 5);
        if ($extra < $this->MusicianList->average_level || $extra + $this->MusicianList->average_level == 0) {
            $extra = 0;
        } else {
            $extra = floor((($extra - $this->MusicianList->average_level) / ($extra + $this->MusicianList->average_level)) * 100);
        }

        return $extra;
    }

    public function UpdateTotalChance()
    {
        if ($this->CurrentDay == 0 || $this->CurrentDay == 1) {
            return;
        }

        $totalChance = floor(2 / $this->CurrentDay * $this->room->MainVariable($this->TicketPrice));
        $totalChance += $this->UpdateTotalExtra();

        if ($totalChance > 70) {
            return 70;
        }

        return $totalChance;
    }

    public function AddMusician(SUser $add)
    {
        if ($add->id == $this->Creator) {
            throw new FailedResult('You cannot take part at your own operation.');
        }
        ConcertWanted::Search($add->id);

        $object = ConcertWanted::GetObject($add->id);

        if ($object->Min_Capacity > $this->room->MaxCapacity) {
            throw new FailedResult('This soldier only works on bigger operations.');
        }
        $tag = DBi::$conn->query('SELECT dog_tags FROM grpgusers WHERE id = ' . $add->id);
        $tags = mysqli_fetch_assoc($tag);
        if ($tags['dog_tags'] < $this->room->MinLevel) {
            throw new FailedResult("This soldier doesn't have the minimal dog tags");
        }
        $creator = UserFactory::getInstance()->getUser($this->Creator);
        if ($creator->money < $object->Money) {
            throw new FailedResult("You don't have enough money to pay to thissoldier in your hand.");
        }
        if ($creator->points < $object->Points) {
            throw new FailedResult("You don't have enough points to pay to thissoldier.");
        }
        $this->MusicianList->isMaxProfitReached($object->Profit);

        $this->MusicianList->Add($add->id, $object->Money, $object->Points, $object->Profit, $object->Min_Capacity);

        $creator->RemoveFromAttribute('points', $object->Points);

        $creator->RemoveFromAttribute('money', $object->Money);

        $user = UserFactory::getInstance()->getUser($object->user_id);

        $user->AddToAttribute('points', $object->Points);

        $user->AddToAttribute('money', $object->Money);
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

            'CurrentDay',

            'TotalChance',

            'MinChance',

            'TicketSold',

            'Timestamp',

            'Room',

            'Creator',

            'TicketPrice',

            'Started',

            'Name',
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
