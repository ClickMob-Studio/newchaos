<?php

final class GangCrime extends CachedObject
{
    public static $idField = 'id';
    public static $dataTable = 'gangcrimes';

    public function __construct($id)
    {
        parent::__construct($id);
        $this->crime = constant($this->crime);
        $this->success = constant($this->success);
        $this->failed = constant($this->failed);
    }

    public static function GetAll()
    {
        $objs = [];
        $objs = parent::GetAllById(self::$idField, self::GetDataTableFields(), self::GetDataTable());

        foreach ($objs as $key => $obj) {
            $obj->crime = constant($obj->crime);
            $obj->success = constant($obj->success);
            $obj->failed = constant($obj->failed);
            $objs[$key] = $obj;
        }

        return $objs;
    }

    public static function GetAllByTime()
    {
        $objs = [];
        $objs = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '', false, false, 'time');

        foreach ($objs as $key => $obj) {
            $obj->crime = constant($obj->crime);
            $obj->success = constant($obj->success);
            $obj->failed = constant($obj->failed);

            $objs[$key] = $obj;
        }

        return $objs;
    }

    public static function GetPendingCrimes($gangid)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `gangid`, `crimeid`, `endtime`, `result`, `starttime`, `userid` FROM `crimelog` WHERE `gangid`=\'' . $gangid . '\' AND `result`=\'0\'');
        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function GetCurrentCrimes(Gang $gang, array $gangCrimes)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `gangid`, `crimeid`, `endtime`, `result`, `starttime`, `userid` FROM `crimelog` WHERE `gangid`=\'' . $gang->id . '\' ORDER BY `endtime` DESC LIMIT 0,15');
        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }

        while ($obj = mysqli_fetch_object($res)) {
            $obj->formattedEndtime = date('F d, Y g:i:sa', $obj->endtime);
            if ($obj->result == 0) {
                if ($obj->endtime > time()) {
                    $obj->crimeResult = GANG_CRIME_HAPPENING;
                    $obj->crimeMessage = '-';
                } else {
                    $obj->crimeResult = GANG_CRIME_WAITING;
                    $obj->crimeMessage = '-';
                }
            } elseif ($obj->result == 1) {
                $obj->crimeResult = '<span style="color:darkgreen;">' . GANG_CRIME_SUCCESS . '</span>';
                $obj->crimeMessage = $gangCrimes[$obj->crimeid]->success;
            } elseif ($obj->result == 3) {
                $obj->crimeResult = '<span style="color:red;">' . GANG_CRIME_STOPPED . '</span>';
                $obj->crimeMessage = '-';
            } else {
                $obj->crimeResult = '<span style="color:red;">' . GANG_CRIME_FAILED . '. The Police arrested all regiment members connected to this crime.</span>';
                $obj->crimeMessage = $gangCrimes[$obj->crimeid]->failed;
            }
            $obj->crime = $gangCrimes[$obj->crimeid]->crime;
            $obj->startingUser = User::SGetFormattedName($obj->userid);
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function TimeCompare($a, $b)
    {
        if ($a->time == $b->time) {
            return 0;
        }

        return ($a->time < $b->time) ? -1 : 1;
    }

    public static function GetCountByUsers($gang_id, $days = 7, $timestamp = null)
    {
        $time = time() - ($days * 86400);
        if ($timestamp != null) {
            $query = 'SELECT userid, count(userid) as gangcrimes,crimeid FROM crimelog WHERE starttime >= \'' . $timestamp . '\' and gangid=\'' . $gang_id . '\' and result=1 GROUP BY crimeid, userid order by userid';
        } else {
            $query = 'SELECT userid, count(userid) as gangcrimes,crimeid FROM crimelog WHERE starttime >= \'' . $time . '\' and gangid=\'' . $gang_id . '\' and result=1 GROUP BY crimeid, userid order by userid';
        }

        return self::GetPaginationResults($query, 'page');
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'crime',
            'minonline',
            'mingang',
            'success',
            'failed',
            'gangexp',
            'money',
            'time',
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
