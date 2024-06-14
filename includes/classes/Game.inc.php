<?php

final class Game
{
    public function __construct()
    {
    }

    public static function GetAll5050PGBets()
    {
        $res = DBi::$conn->query('SELECT `owner`, `amount`, `id` FROM `5050game1`');
        if (mysqli_num_rows($res) == 0) {
            return null;
        }
        $bets = [];
        while ($bet = mysqli_fetch_object($res)) {
            $bets[] = $bet;
        }

        return $bets;
    }

    public static function GetAll5050PGBetsByAmount()
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `owner`, `amount`, `id` FROM `5050game1` ORDER BY `amount` DESC');
        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $obj->owner = UserFactory::getInstance()->getUser($obj->owner);
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function Get5050PGBet($id)
    {
        $query = 'SELECT `owner`, `amount`, `id` FROM `5050game1` WHERE `id`=\'' . $id . '\'';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return null;
        }

        return mysqli_fetch_object($res);
    }

    public static function Add5050PGBet($owner, $amount)
    {
        return DBi::$conn->query('INSERT INTO `5050game1` (owner, amount) VALUES (\'' . $owner . '\', \'' . $amount . '\')');
    }

    public static function Delete5050PGBet($id)
    {
        DBi::$conn->query("DELETE FROM `5050game1` WHERE `id`='" . $id . "'");
        if (DBi::$conn -> affected_rows > 0) {
            return true;
        }

        return false;
    }

    public static function GetAll5050MGBetsByAmount()
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `owner`, `amount`, `id` FROM `5050game` ORDER BY `amount` DESC');
        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $obj->owner = UserFactory::getInstance()->getUser($obj->owner);
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function Get5050MBetsCountByOwner($owner)
    {
        $res = DBi::$conn->query("SELECT count(*) FROM `5050game` WHERE `owner`='" . $owner . "'");

        return mysqli_result($res, 0);
    }

    public static function Get5050PBetsCountByOwner($owner)
    {
        $res = DBi::$conn->query("SELECT count(*) FROM `5050game1` WHERE `owner`='" . $owner . "'");

        return mysqli_result($res, 0);
    }

    public static function Get5050MGBet($id)
    {
        $res = DBi::$conn->query("SELECT `owner`, `amount`, `id` FROM `5050game` WHERE `id`='" . $id . "'");
        if (mysqli_num_rows($res) == 0) {
            return null;
        }

        return mysqli_fetch_object($res);
    }

    public static function Add5050MGBet($owner, $amount)
    {
        return DBi::$conn->query('INSERT INTO `5050game` (owner, amount) VALUES (\'' . $owner . '\', \'' . $amount . '\')');
    }

    public static function Delete5050MGBet($id)
    {
        DBi::$conn->query("DELETE FROM `5050game` WHERE `id`='" . $id . "'");
        if (DBi::$conn -> affected_rows > 0) {
            return true;
        }

        return false;
    }

    public static function GetAvailableBlackjackGames($uid)
    {
        $res = DBi::$conn->query('SELECT `blackjack` from `user_available_games` where `uid`=\'' . $uid . '\'');
        if (mysqli_num_rows($res) == 0) {
            DBi::$conn->query('INSERT INTO `user_available_games` (`uid`, `dice`, `blackjack`) VALUES (\'' . $uid . '\', \'150\', \'50\')');

            return 50;
        }
        $arr = mysqli_fetch_array($res);

        return $arr['blackjack'];
    }

    public static function DecrementAvailableBlackjackGames($uid)
    {
        $availableGames = self::GetAvailableBlackjackGames($uid);
        if ($availableGames == 0) {
            return false;
        }
        DBi::$conn->query('UPDATE `user_available_games` SET `blackjack`=`blackjack`-1 WHERE `blackjack` > 0 AND `uid`=\'' . $uid . '\'');
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }

        return true;
    }

    public static function ResetAllAvailableBlackjackGames()
    {
        DBi::$conn->query('UPDATE `user_available_games` SET `blackjack`=\'50\'');
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }

        return true;
    }

    public static function GetAvailableDiceGames($uid)
    {
        $res = DBi::$conn->query('SELECT `dice` from `user_available_games` where `uid`=\'' . $uid . '\'');
        if (mysqli_num_rows($res) == 0) {
            DBi::$conn->query('INSERT INTO `user_available_games` (`uid`, `dice`, `blackjack`) VALUES (\'' . $uid . '\', \'150\', \'50\')');

            return 150;
        }
        $arr = mysqli_fetch_array($res);

        return $arr['dice'];
    }

    public static function DecrementAvailableDiceGames($uid)
    {
        $availableGames = self::GetAvailableDiceGames();
        if ($availableGames == 0) {
            return false;
        }
        DBi::$conn->query('UPDATE `user_available_games` SET `dice`=`dice`-1 WHERE `dice` > 0 AND `uid`=\'' . $uid . '\'');
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }

        return true;
    }

    public static function ResetAllAvailableDiceGames()
    {
        DBi::$conn->query('UPDATE `user_available_games` SET `dice`=\'150\'');
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }

        return true;
    }

    public static function ResetAllAvailableGames()
    {
        DBi::$conn->query('UPDATE `user_available_games` SET `dice`=\'150\', `blackjack`=\'50\'');
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }

        return true;
    }
}
