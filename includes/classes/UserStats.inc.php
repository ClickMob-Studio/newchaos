<?php

class UserStats
{
    public function __construct($wutever)
    {
        $playersTotalQuery = DBi::$conn->query('SELECT COUNT(id) as total FROM grpgusers');
        $playersTotal = mysqli_fetch_row($playersTotalQuery);
        $this->playerstotal = $playersTotal[0];

        $playersOnlineQuery = DBi::$conn->query('SELECT COUNT(id) as total FROM grpgusers WHERE lastactive > ' . (time() - 3600));
        $playersOnline = mysqli_fetch_row($playersOnlineQuery);
        $this->playersloggedin = $playersOnline[0];

        $playersOnlineDayQuery = DBi::$conn->query('SELECT COUNT(id) as total FROM grpgusers WHERE lastactive > ' . (time() - 86400));
        $playerOnlineDay = mysqli_fetch_row($playersOnlineDayQuery);
        $this->playersonlineinlastday = $playerOnlineDay[0];
    }
}
