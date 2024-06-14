<?php

final class ConcertMusicians
{
    public $musicians = [];
    public $average_level = 0;
    public $sum = 0;
    public $concert = 0;

    public function __construct($id)
    {
        $query = 'SELECT Musician,Profit,Money,Points,Min_Capacity FROM `ConcertMusicians` WHERE `Concert` = ' . $id;

        $rs = DBi::$conn->query($query);

        while ($row = mysqli_fetch_object($rs)) {
            $tmp = SUserFactory::getInstance()->getUser($row->Musician);

            $tmp->Profit = $row->Profit;

            $tmp->Money = $row->Money;

            $tmp->Points = $row->Points;

            $tmp->Min_Capacity = $row->Min_Capacity;

            $this->musicians[] = $tmp;
        }

        $this->AverageLevel();

        $this->concert = $id;
    }

    public function __toString()
    {
        if (count($this->musicians) == 0) {
            return '';
        }

        $string = "<table width='60%' class='cleanTable'> <tr><th align='left' class='headerCell'>Username</th><th align='left' class='headerCell'>Dog Tags</th><th align='left' class='headerCell'>Profit</th><th class='headerCell' align='left'>Money</th><th class='headerCell' align='left'>Points</th></tr>";

        foreach ($this->musicians as $music) {
            $user = UserFactory::getInstance()->getUser($music->id);
            $string .= "<tr><td class='dottedRow'>" . $user->formattedname . "</td><td class='dottedRow'>" . $music->ConcertLevel . "</td><td class='dottedRow'>" . $music->Profit . "%</td><td class='dottedRow'>$" . $music->Money . "</td><td class='dottedRow'>" . $music->Points . '</td></tr>';
        }

        return '</table>' . $string;
    }

    public function isMaxProfitReached($value)
    {
        $profit = 0;
        foreach ($this->musicians as $music) {
            $profit += $music->Profit;
        }

        if ($profit + $value > 90) {
            throw new FailedResult('You can not allow to share more than 90% of total profit');
        }

        return true;
    }

    public function AverageLevel()
    {
        $total_count = 0;
        foreach ($this->musicians as $music) {
            if ($music->ConcertLevel != 0) {
                $this->average_level += $music->ConcertLevel;
                ++$total_count;
            }
        }

        $this->sum = $this->average_level;

        if ($total_count > 0) {
            $this->average_level /= $total_count;
        }
    }

    public function Add($userid, $Money, $Points, $profit = 0, $Min_Capacity = 0)
    {
        $query = 'SELECT id FROM `ConcertMusicians` WHERE `Musician` = ' . $userid;
        $rs = DBi::$conn->query($query);
        if (mysqli_num_rows($rs) > 0) {
            $this->Clean();
            throw new FailedResult('This soldier is already conducting an operation.');
        }

        if (count($this->musicians) > 4) {
            throw new FailedResult("You can't have more than 5 soldiers.");
        }
        $query = 'SELECT id FROM `ConcertWanted` WHERE `user_id` = ' . $userid;

        $rs = DBi::$conn->query($query);

        if (mysqli_num_rows($rs) == 0) {
            throw new FailedResult('This soldier is not available anymore.');
        }
        ConcertWanted::XDelete(ConcertWanted::GetObject($userid));

        $sql = "insert into ConcertMusicians (`Concert`,`Musician`,`Profit`, `Money`,`Points`,`Min_Capacity`) values ('" . $this->concert . "','" . $userid . "','" . $profit . "','" . $Money . "','" . $Points . "','" . $Min_Capacity . "')";

        $rs = DBi::$conn->query($sql);

        $this->musicians[] = SUserFactory::getInstance()->getUser($userid);

        $concerts = new ConcertRent($this->concert);

        Event::Add($userid, 'You were chosen among many to take part at the operation ' . $concerts->Name . '.');

        return DBi::$conn -> affected_rows;
    }

    public function Expire()
    {
        foreach ($this->musicians as $music) {
            Event::Add($music->id, "Due to the fact that the operation wasn't arranged in less than 6 hours, it has been canceled.");
        }

        $this->Fired();
        $query = 'delete from `ConcertMusicians` where Concert=' . $this->concert;
        $rs = DBi::$conn->query($query);
    }

    public function FirstDay()
    {
        foreach ($this->musicians as $music) {
            Event::Add($music->id, 'All arrangements for the operation were made, today we will start seeking funding.');
        }
    }

    public function Fired()
    {
        foreach ($this->musicians as $music) {
            if ($music->NoAutomaticConcert) {
                continue;
            }

            $money = $music->Money;
            $points = $music->Points;
            $Timestamp = time();
            $profit = $music->Profit;
            $Min_Capacity = $music->Min_Capacity;
            $userid = $music->id;
            ConcertWanted::AddRecords(['user_id' => $userid, 'Money' => $money, 'Points' => $points, 'Profit' => $profit,
                'Min_Capacity' => $Min_Capacity, 'Timestamp' => time(), ], ConcertWanted::$dataTable);
        }
    }

    public function Start()
    {
        foreach ($this->musicians as $music) {
            Event::Add($music->id, 'The operation has been scheduled.');
        }
    }

    public function Cancel()
    {
        foreach ($this->musicians as $music) {
            Event::Add($music->id, 'The operation was canceled');
        }

        $this->Fired();
        $query = 'delete from `ConcertMusicians` where Concert=' . $this->concert;
        $rs = DBi::$conn->query($query);
    }

    public function Clean()
    {
        $query = 'delete from ConcertWanted where user_id in ( select Musician from ConcertMusicians )';
        $rs = DBi::$conn->query($query);
    }

    public function Finish()
    {
        $this->Fired();
        $query = 'delete from `ConcertMusicians` where Concert=' . $this->concert;
        $rs = DBi::$conn->query($query);
    }
}
