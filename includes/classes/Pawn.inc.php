<?php

define('PAWN_TAX', '0.02');
class Pawn extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'pawn';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function BuyBack(User $user, $itemID)
    {
        $pawn = new Pawn($itemID);

        if ($user->id != $pawn->owner) {
            throw new FailedResult(sprintf("You don't own this item"));
        }
        if ($pawn->valuepaid * (1 + PAWN_TAX) > $user->bank) {
            throw new FailedResult(sprintf('You need to have at least $%s.', number_format($pawn->valuepaid * (1 + PAWN_TAX))));
        }
        User::SRemoveBankMoney($user->id, $pawn->valuepaid * (1 + PAWN_TAX));

        $user->AddItems($pawn->itemid, 1);
        $item = new Item($pawn->itemid);
        self::sDelete(self::$dataTable, ['id' => $itemID]);
        throw new SuccessResult(sprintf('You have bought back your %s for $%s.', $item->itemname, number_format($pawn->valuepaid * (1 + PAWN_TAX))));
    }

    public static function CreatePawn(User $user, $itemID, $pawnValue)
    {
        if (count(self::ItemsAtPawn($user)) > 4) {
            throw new FailedResult("You can't have more than 5 items pawned at the same time.");
        }
        $item = new Item($itemID);
        if ($item->cost * 0.4 < $pawnValue) {
            throw new FailedResult(sprintf('The max pawn value is $%s', number_format($item->cost * 0.4)));
        }
        if (User::SRemoveItems($itemID, $user->id) === true) {
            User::SAddBankMoney($user->id, $pawnValue);
            $data = [
                'owner' => $user->id,
                'itemid' => $itemID,
                'sellingday' => time(),
                'valuepaid' => $pawnValue,
                'itemvalue' => $item->cost,
            ];
            self::AddRecords($data, self::$dataTable);
            throw new SuccessResult('You have pawned a ' . $item->itemname . ' for $' . number_format($pawnValue));
        }

        throw new FailedResult('The item could not be pawned.');
    }

    public static function ItemsAtPawn(User $user)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT * FROM `pawn` where owner=' . $user->id . ' and to_everyone=0');
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function ItemsAtPawnForEveryone()
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT * FROM `pawn` where to_everyone=1');
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function Resell(User $user, $itemID)
    {
        $pawn = new Pawn($itemID);
        $item = new Item($item->itemid);
        if ($pawn->to_everyone < 1) {
            throw new FailedResult('This pawn is not public');
        }
        $item = new Item($pawn->itemid);
        if ($user->money < $item->cost * 0.70) {
            throw new FailedResult(sprintf('You need to have at least $%s in hand.', number_format($item->cost * 0.70)));
        }
        User::SRemoveMoney($user->id, $item->cost * 0.70);
        $user->AddItems($pawn->itemid, 1);
        User::SAddBankMoney($pawn->owner, $item->cost * 0.70 - $pawn->valuepaid * (1 + PAWN_TAX));
        Event::Add($pawn->owner, sprintf('The %s has been sold for %s at the resale shop. You earned $%s from the sale.',
            $item->itemname, number_format($item->cost * 0.70), number_format($item->cost * 0.70 - $pawn->valuepaid * (1 + PAWN_TAX))));

        self::sDelete(self::$dataTable, ['id' => $itemID]);
        throw new SuccessResult(sprintf('You have bought a %s for $%d.', $item->itemname, number_format($item->cost * 0.70)));
    }

    public static function EachDay()
    {
        $items[] = [];
        $twentyNineDay = 28 * 24 * 60 * 60;
        $thirteenDay = 30 * 24 * 60 * 60;
        $sql = 'SELECT * FROM `pawn` where sellingday>= ' . (time() - $twentyNineDay) . ' and sellingday<' . (time() - $thirteenDay);

        $res = DBi::$conn->query($sql);
        while ($obj = mysqli_fetch_object($res)) {
            if (!isset($items[$obj->itemid])) {
                $items[$obj->itemid] = new Item($obj->itemid);
            }
            Event::Add($obj->owner, 'You have 1 day left to recover your pawned ' . $items[$obj->itemid]->itemname . '.');
        }

        $sql = 'select * from `pawn` where to_everyone=0 and sellingday + 2592000 < ' . time();
        $res = DBi::$conn->query($sql);
        while ($obj = mysqli_fetch_object($res)) {
            if (!isset($items[$obj->itemid])) {
                $items[$obj->itemid] = new Item($obj->itemid);
            }

            Event::Add($obj->owner, 'Your ' . $items[$obj->itemid]->itemname . ' is now for sale at the resale shop for 70% of its real value.');
        }

        $sql = 'update `pawn` set to_everyone=1 where to_everyone=0 and sellingday + 2592000 < ' . time();
        $res = DBi::$conn->query($sql);

        $sql = 'select * from `pawn` where to_everyone=0';
        $res = DBi::$conn->query($sql);
        $users = [];
        while ($obj = mysqli_fetch_object($res)) {
            if (!isset($items[$obj->itemid])) {
                $items[$obj->itemid] = new Item($obj->itemid);
            }
            if (!isset($users[$obj->owner])) {
                $users[$obj->owner] = 0;
            }
            $users[$obj->owner] += ceil($obj->valuepaid * PAWN_TAX);
        }

        return $users;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'owner',
            'itemid',
            'sellingday',
            'valuepaid',
            'to_everyone',
            'itemvalue',
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
