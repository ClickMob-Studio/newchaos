<?php

final class GangBonus extends BaseObject
{
    public static $idField = 'id';

    public static $dataTable = 'gangbonus';

    public static $BONUS_EXP = 0;

    public static $BONUS_SPEED = 1;

    public static $BONUS_ATTACK = 2;

    public static $BONUS_DEFENSE = 3;

    public static $TRAIN = 1;

    public static $STATS = 2;

    public $bonus;

    public function __construct($id)
    {
        parent::__construct($id);

        $bonus = new GangBonusItem($this->bonus_id);
    }

    public static function GetAllGangBonus($gangid)
    {
        $sql = 'select id from ' . self::$dataTable . 'where gangid=' . $gangid;

        $res =DBi::$conn->query($sql);

        $obj = [];

        while ($row = mysqli_fetch_object($res)) {
            $obj[] = new GangBonus($row->id);
        }

        return $obj;
    }

    public function finish()
    {
        if ($this->finish_time > time()) {
            return;
        }

        self::sDelete(self::$dataTable, ['id' => $this->id]);
    }

    public static function getAwake(User $user)
    {
        $train = 0;

        if ($user->gang == 0) {
            return $train;
        }

        if (Gang::XMemberFor($user->gang, $user->id) < 15 * 24 * 60 * 60) {
            return $train;
        }

        $sql = 'select * from ' . self::$dataTable . ' where gangid=' . $user->gang;

        $res =DBi::$conn->query($sql);

        $bonus = [];

        while ($row = mysqli_fetch_object($res)) {
            $bonus[] = new GangBonusItem($row->bonus_id);
        }

        foreach ($bonus as $bon) {
            $train += $bon->mStat();
        }

        return $train;
    }

    public static function getTrain(User $user, $status)
    {
        $train = 0;

        if ($user->gang == 0) {
            return $train;
        }

        if (Gang::XMemberFor($user->gang, $user->id) < 15 * 24 * 60 * 60) {
            return $train;
        }

        $sql = 'select * from ' . self::$dataTable . ' where gangid=' . $user->gang;

        $res =DBi::$conn->query($sql);

        $bonus = [];

        while ($row = mysqli_fetch_object($res)) {
            $bonus[] = new GangBonusItem($row->bonus_id);
        }

        foreach ($bonus as $bon) {
            $train += $bon->mTrain($status);
        }

        return $train;
    }

    public static function AffectedBy(User $user)
    {
        if ($user->gang == 0) {
            return [];
        }

        if (Gang::XMemberFor($user->gang, $user->id) < 15 * 24 * 60 * 60) {
            return [];
        }

        $sql = 'select * from ' . self::$dataTable . ' where gangid=' . $user->gang;

        $res =DBi::$conn->query($sql);

        $bonus = [];

        while ($row = mysqli_fetch_object($res)) {
            $bon = new GangBonusItem($row->bonus_id);

            $bon->uniqueid = $row->id;

            $bon->timeLeft = $row->finish_time;

            $bonus[] = $bon;
        }

        return $bonus;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,

            'bonus_id',

            'gangid',

            'finish_time',
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

