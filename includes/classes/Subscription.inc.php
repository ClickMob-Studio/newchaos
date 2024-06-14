<?php

class Subscription
{
    public static $types = null;

    public function __construct()
    {
    }

    public static function GetTypes()
    {
        if (Subscription::$types == null) {
            $objs = [];
            $query = 'SELECT `Id`, `Name`, `Price`, `RPDays`, `AWPills`, `Points` FROM `subscription_types`';
            $res = DBi::$conn->query($query);
            if (mysqli_num_rows($res) == 0) {
                return null;
            }
            while ($obj = mysqli_fetch_object($res)) {
                $objs[$obj->Id] = $obj;
            }
            Subscription::$types = $objs;
        }

        return Subscription::$types;
    }

    public static function Get($sid)
    {
        $objs = [];
        $query = 'SELECT `Id`, `User`, `Type`, `State`, `StartDate`, `EndDate` FROM `user_subscriptions` WHERE `Id`="' . $sid . '"';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return null;
        }

        return mysqli_fetch_object($res);
    }

    public static function GetActive($uid)
    {
        $objs = [];
        $query = 'SELECT `Id`, `User`, `Type`, `State`, `StartDate`, `EndDate` FROM `user_subscriptions` WHERE `User`="' . $uid . '" AND `State` = 1';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return null;
        }

        return mysqli_fetch_object($res);
    }

    public static function GetForUser($uid)
    {
        $objs = [];
        $query = 'SELECT `Id`, `User`, `Type`, `State`, `StartDate`, `EndDate` FROM `user_subscriptions` WHERE `User`="' . $uid . '"';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return null;
        }

        return mysqli_fetch_object($res);
    }

    public static function GetAll()
    {
        $objs = [];
        $query = 'SELECT `u`.`username`, `s`.`Id`, `s`.`User`, `s`.`Type`, `s`.`State`, `s`.`StartDate`, `s`.`EndDate` FROM `grpgusers` u, `user_subscriptions` s WHERE `u`.`id`=`s`.`User`';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return null;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[$obj->Id] = $obj;
        }

        return $objs;
    }

    public static function GetAllFormated()
    {
        $subTypes = self::GetTypes();
        $objs = [];
        $query = 'SELECT `u`.`username`, `s`.`Id`, `s`.`User`, `s`.`Type`, `s`.`State`, `s`.`StartDate`, `s`.`EndDate` FROM `grpgusers` u, `user_subscriptions` s WHERE `u`.`id`=`s`.`User` ORDER BY `s`.`StartDate` DESC ';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return null;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $obj->Price = $subTypes[$obj->Type]->Price;
            $obj->Type = $subTypes[$obj->Type]->Name;
            $obj->State = $obj->State == 1 ? 'Active' : 'Inactive';
            $objs[$obj->Id] = $obj;
        }

        return $objs;
    }

    public static function GetAllPayments()
    {
        $objs = [];
        $query = 'SELECT `u`.`username`, `sp`.`User`, `sp`.`Amount`, `sp`.`Date` FROM `grpgusers` u, `subscription_payments` sp WHERE `u`.`id`=`sp`.`User` ORDER BY `sp`.`Date` DESC';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return null;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function GetEndDate($sid)
    {
        $objs = [];
        $query = 'SELECT `EndDate` FROM `user_subscriptions` WHERE `Id`="' . $sid . '"';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }

        return mysqli_result($res, 0, 0);
    }

    public static function Exists($sid)
    {
        $query = 'SELECT `Id` FROM `user_subscriptions` WHERE `Id`="' . $sid . '"';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return false;
        }

        return true;
    }

    public static function Add($sid, $uid, $type)
    {
        $subTypes = Subscription::GetTypes();
        $query = 'INSERT INTO `user_subscriptions` (`Id`, `User`, `Type`, `State`, `StartDate`) VALUES ("' . $sid . '", ' . $uid . ', ' . $type . ', 0, ' . time() . ')';
        $res = DBi::$conn->query($query);
        Subscription::Activate($sid, $uid, $type);
        Subscription::AddPayment($sid, $uid, $subTypes[$type]->Price);

        return true;
    }

    public static function Activate($sid, $uid, $type)
    {
        if (Subscription::AddMonth($sid)) {
            $subTypes = Subscription::GetTypes();

            // First we update the pills
            $result = DBi::$conn->query('SELECT `userid`, `itemid`, `quantity`, `borrowed` FROM `inventory` WHERE `itemid`=14 and `userid`=' . $uid);
            $arr = mysqli_fetch_array($result);
            if ($arr) {
                $newpill = $arr['quantity'] + $subTypes[$type]->AWPills;
                DBi::$conn->query('UPDATE `inventory` SET quantity=' . $newpill . " WHERE `itemid`=14 and `userid`={$uid}");
            } else {
                DBi::$conn->query("INSERT INTO `inventory` (`userid`,`itemid`,`quantity`) VALUES ('{$uid}','14','" . $subTypes[$type]->AWPills . "')");
            }

            // Then we update the points and rpdays
            $result = DBi::$conn->query('SELECT `points`, `rmdays` FROM `grpgusers` WHERE id = ' . $uid);
            $work = mysqli_fetch_array($result);
            $newpoints = $work['points'] + $subTypes[$type]->Points;
            $newdays = $work['rmdays'] + $subTypes[$type]->RPDays;
            DBi::$conn->query('UPDATE `grpgusers` SET `points`=' . $newpoints . ", `rmdays`='" . $newdays . "' WHERE id={$uid}");

            return true;
        }

        return false;
    }

    public static function Renew($sid, $uid, $type)
    {
        $subTypes = Subscription::GetTypes();
        $sub = Subscription::Get($sid);
        Subscription::AddPayment($sid, $uid, $subTypes[$type]->Price);
        if (!$sub) {
            return Subscription::Add($uid, $type);
        }

        return Subscription::Activate($sid, $uid, $type);
    }

    public static function AddPayment($sid, $uid, $amount)
    {
        if (!self::Exists($sid)) {
            return false;
        }
        $query = 'INSERT INTO `subscription_payments` (`Subscription`, `User`, `Amount`, `Date`) VALUES ("' . $sid . '", ' . $uid . ', ' . $amount . ', ' . time() . ')';
        DBi::$conn->query($query);

        return true;
    }

    public static function AddMonth($sid)
    {
        $sub = Subscription::Get($sid);
        if (!$sub) {
            return false;
        }
        if ($sub->EndDate < time()) {
            $query = 'UPDATE `user_subscriptions` SET `State`=1, `EndDate` = ' . mktime(date('H'), date('i'), date('s'), date('m') + 1, date('d'), date('Y')) . ' WHERE `Id`="' . $sid . '"';
        } else {
            $query = 'UPDATE `user_subscriptions` SET `State`=1, `EndDate` = ' . mktime(date('H', $sub->EndDate), date('i', $sub->EndDate), date('s', $sub->EndDate), date('m', $sub->EndDate) + 1, date('d', $sub->EndDate), date('Y', $sub->EndDate)) . ' WHERE `Id`="' . $sid . '"';
        }
        DBi::$conn->query($query);

        return true;
    }

    public static function AddDays($sid, $days)
    {
        $sub = Subscription::Get($sid);
        if (!$sub) {
            return false;
        }
        if ($sub->EndDate < time()) {
            $query = 'UPDATE `user_subscriptions` SET `State`=1, `EndDate` = ' . (time() + 86400 * $days) . ' WHERE `Id`="' . $sid . '"';
        } else {
            $query = 'UPDATE `user_subscriptions` SET `State`=1, `EndDate` = `EndDate`+' . (86400 * $days) . ' WHERE `Id`="' . $sid . '"';
        }
        DBi::$conn->query($query);

        return true;
    }

    public static function RemoveMonth($sid)
    {
        $sub = Subscription::Get($sid);
        if (!$sub) {
            return false;
        }

        $prevMonth = mktime(
        date('H', $sub->EndDate),
        date('i', $sub->EndDate),
        date('s', $sub->EndDate),
        date('m', $sub->EndDate) - 1,
        date('d', $sub->EndDate),
        date('Y', $sub->EndDate));

        if ($prevMonth < time()) {
            $query = 'UPDATE `user_subscriptions` SET `State`=0, `EndDate` = ' . $prevMonth . ' WHERE `Id`="' . $sid . '"';
        } else {
            $query = 'UPDATE `user_subscriptions` SET `EndDate` = ' . $prevMonth . ' WHERE `Id`="' . $sid . '"';
        }
        DBi::$conn->query($query);

        return true;
    }

    public static function RemoveDays($sid, $days)
    {
        $sub = Subscription::Get($sid);
        if (!$sub) {
            return false;
        }

        $oldDays = $sub->EndDate;
        $newDays = $oldDays - (86400 * $days);

        if ($newDays < time()) {
            $query = 'UPDATE `user_subscriptions` SET `EndDate` = ' . $newDays . ', `State`=0 WHERE `Id`="' . $sid . '"';
        } else {
            $query = 'UPDATE `user_subscriptions` SET `EndDate` = ' . $newDays . ' WHERE `Id`="' . $sid . '"';
        }
        DBi::$conn->query($query);

        return true;
    }

    public static function Delete($sid)
    {
        $query = 'DELETE FROM `user_subscriptions` WHERE `Id`="' . $sid . '"';
        DBi::$conn->query($query);

        return true;
    }

    public static function ChangeType($sid, $type)
    {
        if (!self::Exists($sid)) {
            return false;
        }
        $query = 'UPDATE `user_subscriptions` SET `Type`=' . $type . ' WHERE `Id`="' . $sid . '"';
        DBi::$conn->query($query);

        return true;
    }

    public static function Cancel($sid)
    {
        if (!self::Exists($sid)) {
            return false;
        }
        $query = 'UPDATE `user_subscriptions` SET `State`=0, `EndDate`=0 WHERE `Id`="' . $sid . '"';
        DBi::$conn->query($query);

        return true;
    }

    public static function CancelExpired()
    {
        // Query time is checked against 1 month + 5 days
        $queryTime = time() - 3024000;
        $query = 'UPDATE `user_subscriptions` SET `State`=0, `EndDate`=0 WHERE `EndDate`<"' . $queryTime . '"';
        DBi::$conn->query($query);

        return true;
    }
}
