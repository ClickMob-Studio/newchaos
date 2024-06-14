<?php

final class Job extends CachedObject
{
    public static $idField = 'id';
    public static $dataTable = 'jobs';

    public function __construct($id)
    {
        if ($id == 0) {
            $this->id = 0;
            $this->name = 'COM_NONE';
        } else {
            parent::__construct($id);
        }

        if (!empty($this->name)) {
            $this->name = constant($this->name);
        }
    }

    public static function GetAllByMoney()
    {
        $objs = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '', false, false, 'money', 'ASC');

        foreach ($objs as $key => $obj) {
            $obj->name = constant($obj->name);
            $objs[$key] = $obj;
        }

        return $objs;
    }

    public function MatchUser(User $user)
    {
        if ($this->level <= $user->level) {
            $player = [$user->strength, $user->defense, $user->speed];
            rsort($player);
            $jobs = [$this->strength, $this->defense, $this->speed];
            rsort($jobs);

            if ($player[0] >= $jobs[0] && $player[1] >= $jobs[1] && $player[2] >= $jobs[2]) {
                return true;
            }
        }

        return false;
    }

    public static function sMatchUser(stdClass $job, User $user)
    {
        if ($job->level <= $user->level) {
            $player = [$user->strength, $user->defense, $user->speed];
            rsort($player);
            $jobs = [$job->strength, $job->defense, $job->speed];
            rsort($jobs);

            if ($player[0] >= $jobs[0] && $player[1] >= $jobs[1] && $player[2] >= $jobs[2]) {
                return true;
            }
        }

        return false;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'name',
            'money',
            'strength',
            'defense',
            'speed',
            'level',
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
