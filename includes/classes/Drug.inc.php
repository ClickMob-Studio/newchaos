<?php

final class Drug
{
    public static function GetEffectsOnUser($uid)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `id`, `userid`, `item_id`, `effect`, `timeleft` FROM `effects` WHERE `userid`=\'' . $uid . '\'');
        while ($obj = mysqli_fetch_object($res)) {
            $obj->item = new Item($obj->item_id);
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function CountEffectsOnUser($uid)
    {
        $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `effects` WHERE `userid`=\'' . $uid . '\'');
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $arr = mysqli_fetch_array($res);

        return $arr['total'];
    }

    /*
     * Add the effect for the drug used
     *
     * @param User $user
     * @param Item $item
     * @param integer $timeleft
     *
     * @return mixed
     */
    public static function AddEffect(User $user, Item $item, int $timeleft)
    {
        $query = 'INSERT INTO `effects` (`userid`, `item_id`, `effect`, `timeleft`) VALUES (\'' . $user->id . '\',  \'' . $item->id . '\',  \'' . $item->itemname . '\', \'' . $timeleft . '\')';
        DBi::$conn->query($query);
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException('Your drug was of poor quality, and you did not experience any effect.');
        }

        return true;
    }

    public static function UpdateEffect($id, $timeleft)
    {
        DBi::$conn->query('UPDATE `effects` SET qty = qty + 1, `timeleft` = ' . $timeleft . ' WHERE id = \'' . $id . '\'');

        return true;
    }

    public static function DrugTaken(User $user, $drug = '')
    {
        $where = '';
        if (!empty($drug)) {
            $where = ' AND `effect` = \'' . $drug . '\'';
        }

        $result = DBi::$conn->query('SELECT `id`, `userid`, `effect`, `timeleft`, `qty` FROM `effects` WHERE `userid`=\'' . $user->id . '\' ' . $where);
        if (mysqli_num_rows($result) <= 0) {
            return null;
        }

        return mysqli_fetch_object($result);
    }

    /*
     * Perform the use of an item with type
     *
     * @param User $user
     * @param Item $item
     * @param integer $timeleft
     *
     * @return array
     */
    public static function Take(User $user, Item $item, int $timeleft)
    {
        $response = [];
        $response['success'] = true;

        if ($item->item_type !== 'drug') {
            $response['success'] = false;
            $response['error'] = 'Something went wrong, if this issue persists please mail an Admin.';

            return $response;
        }
        if ($user->IsInHospital()) {
            $response['success'] = false;
            $response['error'] = DRUGS_OVERDOSE_HOSP;

            return $response;
        }

        $drugTaken = Drug::DrugTaken($user);
        if (!empty($drugTaken->effect)) {
            $response['success'] = false;
            $response['error'] = DRUGS_UNDER_EFFECT;

            return $response;
        }

        $item_quantity = Inventory::getItemQuantity($user->id, $item->id);
        if ($item_quantity === 0) {
            $response['success'] = false;
            $response['error'] = "You can't take a drug you don't own!";

            return $response;
        }

        Drug::AddEffect($user, $item, $timeleft);

        return $response;
    }

    public static function UseCaffeine(User $user)
    {
        if ($user->IsInHospital()) {
            throw new SoftException(DRUGS_OVERDOSE_HOSP);
        }
        $drugTaken = Drug::DrugTaken($user);
        if (!empty($drugTaken->effect) && $drugTaken->effect != 'Caffeine') {
            throw new FailedResult(DRUGS_UNDER_EFFECT);
        }
        $nbEffects = (int) $drugTaken->qty;

        if ($nbEffects >= 1) {
            throw new FailedResult(DRUGS_UNDER_EFFECT);
        }
        if ($user->caffeine >= 3) {
            throw new FailedResult(USER_CAFFEINE_MAX_USED);
        }
        $itemQty = $user->GetItemQuantity(117);

        if ($itemQty <= 0) {
            throw new FailedResult(USER_NOT_HAVING_DRUG);
        }
        $user->RemoveItems(new Item(117), 1);
        $user->AddToAttribute('caffeine', 1);
        Drug::AddEffect($user->id, 'Caffeine', 120);
    }
}
