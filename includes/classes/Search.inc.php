<?php

final class Search
{
    public static function GetSavedSlot($uid, $slot)
    {
        $res = DBi::$conn->query('SELECT * FROM `search_save` WHERE `userid`=\'' . $uid . '\' and `slot`=\'' . $slot . '\'');

        if (mysqli_num_rows($res) == 0) {
            return null;
        }

        return mysqli_fetch_object($res);
    }

    public static function GetAllSavedSlots($uid)
    {
        $res = DBi::$conn->query('SELECT `userid`, `sid`, `susername`, `slevel1`, `slevel2`, `smoney`, `sonline`, `sattack`, `sprison`, `sgang`, `slot`, `name`, `sexclude` , `psort`, `psorts`  FROM `search_save` WHERE `userid`=\'' . $uid . '\'');

        if (mysqli_num_rows($res) == 0) {
            return null;
        }

        $objs = [];

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function Save(
    $uid,

    $sid,

    $username,

    $minLevel,

    $maxLevel,

    $minMoney,

    $connected,

    $attackable,

    $prisonId,

    $gangId,

    $slotId,

    $slotName,
    $exOwnGang = 0, $psort, $psorts)
    {
        if (strlen($slotName) == 0) {
            throw new SoftException(SEARCH_INVALID_SAVED_NAME);
        } elseif (!is_numeric($slotId) || $slotId < 1 || $slotId > 8) {
            throw new SoftException(SEARCH_INVALID_SLOT);
        } elseif (!is_numeric($sid) || !is_numeric($prisonId) || !is_numeric($gangId)
        || !is_numeric($minLevel) || !is_numeric($maxLevel) || !is_numeric($minMoney)
        || !is_numeric($connected) || !is_numeric($attackable)) {
            throw new SoftException(SEARCH_INVALID_INPUT);
        }
        if ($exOwnGang === null || $exOwnGang == '') {
            $exOwnGang = 0;
        }

        $savedSearch = Search::GetSavedSlot($uid, $slotId);

        if ($savedSearch !== null) {
            Search::Update($uid, $sid, $username, $minLevel, $maxLevel, $minMoney, $connected, $attackable, $prisonId, $gangId, $slotId, $slotName, $exOwnGang, $psort, $psorts);
        } else {
            Search::SaveNew($uid, $sid, $username, $minLevel, $maxLevel, $minMoney, $connected, $attackable, $prisonId, $gangId, $slotId, $slotName, $exOwnGang, $psort, $psorts);
        }

        return true;
    }

    private static function SaveNew($userid, $sid, $susername, $slevel1, $slevel2, $smoney, $sonline, $sattack, $sprison, $sgang, $slot, $name, $sexclude, $psort, $psorts)
    {
        DBi::$conn->query('INSERT INTO `search_save` (`userid`, `sid`, `susername`, `slevel1`, `slevel2`, `smoney`, `sonline`, `sattack`, `sprison`, `sgang`, `slot`, `name`,`sexclude`, `psort`,`psorts`) VALUES (\'' . $userid . '\',\'' . $sid . '\',\'' . $susername . '\',\'' . $slevel1 . '\',\'' . $slevel2 . '\',\'' . $smoney . '\',\'' . $sonline . '\',\'' . $sattack . '\',\'' . $sprison . '\',\'' . $sgang . '\',\'' . $slot . '\',\'' . $name . '\',\'' . $sexclude . '\',\'' . $psort . '\',\'' . $psorts . '\')');

        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(SEARCH_CANT_CREATE_SLOT);
        }

        return true;
    }

    private static function Update($userid, $sid, $susername, $slevel1, $slevel2, $smoney, $sonline, $sattack, $sprison, $sgang, $slot, $name, $sexclude, $psort, $psorts)
    {
        DBi::$conn->query('UPDATE `search_save` SET `sid`=\'' . Utility::SmartEscape($sid) . '\', `susername`=\'' . $susername . '\', `slevel1`=\'' . Utility::SmartEscape($slevel1) . '\', `slevel2`=\'' . Utility::SmartEscape($slevel2) . '\', `smoney`=\'' . Utility::SmartEscape($smoney) . '\', `sonline`=\'' . Utility::SmartEscape($sonline) . '\', `sattack`=\'' . Utility::SmartEscape($sattack) . '\', `sprison`=\'' . Utility::SmartEscape($sprison) . '\', `sgang`=\'' . Utility::SmartEscape($sgang) . '\',`name`=\'' . $name . '\',`sexclude`=\'' . $sexclude . '\' ,`psort`=\'' . $psort . '\',`psorts`=\'' . $psorts . '\' WHERE `userid`=\'' . $userid . '\' and `slot`=\'' . $slot . '\'');

        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(SEARCH_NOT_FOUND);
        }

        return true;
    }
}

?>

