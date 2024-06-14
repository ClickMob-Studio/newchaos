<?php

final class GangBonusItem extends BaseObject
{
    public static $idField = 'id';

    public static $dataTable = 'gangbonusitem';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAll()
    {
        $sql = 'select id from ' . self::$dataTable;

        $res =DBi::$conn->query($sql);

        $obj = [];

        while ($row = mysqli_fetch_object($res)) {
            $obj[] = new GangBonusItem($row->id);
        }

        return $obj;
    }

    public function buy_bonus(User $user)
    {
        if ($user->GetGang()->leader != $user->username) {
            throw new FailedResult("You don't have permission to do that");
        }
        $albums_number = Item::CountItemInArmory(Item::getDiscId(), $user->gang);

        if ($albums_number < $this->price) {
            throw new FailedResult("Your regiment doesn't have enough medals");
        }
        $data = ['gangid' => $user->gang,'bonus_id' => $this->id,'finish_time' => time() + (24 * 60 * 60 * 7)];
        $gang = new Gang($user->gang);
        GangBonus::AddRecords($data, GangBonus::$dataTable);
        //DBi::$conn->query('UPDATE gangarmory SET quantity = quantity - ' . $this->price . ' WHERE gangid = ' . $user->gang . ' AND itemid = 51 ');
        $nbItems = Item::CountItemInArmory(51, $user->gang);
        if ($nbItems < $this->price) {
            throw new FailedResult(ITEM_NOT_ENOUGH_ARMORY_ITEMS);
        }
     DBi::$conn->query('DELETE FROM `gangarmory` WHERE `itemid`=51 and `gangid`= '.$user->gang.' LIMIT ' . $this->price);
        $affected = DBi::$conn -> affected_rows;
        if ($affected == 0) {
            throw new SoftException(ITEM_CANT_TAKEN);
        } elseif ($affected < $this->price) {
            $quantity = $affected;
        }
        //$gang->RemoveFromArmory($user, new Item(51), $this->price );
        //  Item::RemoveAlbumfromGang($user, new Item(Item::getDiscId()), $this->price);
    }

    public function mStat()
    {
        if ($this->type == GangBonus::$STATS) {
            return $this->value;
        }

        return 0;
    }

    public function mTrain($status)
    {
        if ($this->type == GangBonus::$TRAIN && $this->status == $status) {
            return $this->value;
        }

        return 0;
    }

    public static function Get($id)
    {
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,

            'id',

            'value',

            'name',

            'status',

            'type',

            'price',

            'description',

            'icon',
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

