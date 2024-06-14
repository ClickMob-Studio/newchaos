<?php

class usersOnline
{
    public static function GetOnline1Hour(){
        $online = DBi::$conn->query("SELECT * FROM grpgusers WHERE lastactive > " . (time() - 3600) ." ORDER BY lastactive DESC");
        return $online;
    }

}