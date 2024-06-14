<?php

class Inventory extends BaseObject
{
    public static $dataTable = 'inventory';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    /*
     * Define the valid action methods that can be used in the inventory
     *
     * @return array
     */
    public static function defineValidActionMethods()
    {
        return ['equip', 'unequip', 'use', 'sellitem', 'senditem', 'putonmarket', 'return', 'equiploadout', 'sendbook'];
    }

    /*
     * Create a fully loaded inventory for a user
     *
     * @param integer $user_id
     *
     * @return array
     */
    public static function createLoadedInventoryForUser(int $user_id)
    {
        $inventories = Inventory::GetAll('userid = ' . $user_id);

        foreach ($inventories as &$inventory) {
            $item = Item::SGet($inventory->itemid);
            $inventory->item = $item;
        }

        return $inventories;
    }

    /*
     * Perform an action
     *
     * @param string $action
     * @param integer $itemid
     * @param integer $userid
     * @param $postedData
     *
     * @return array
     */
    public static function performAction(string $action, int $itemid, int $userid, $postedData)
    {
        if ($action == 'equip') {
            if (isset($postedData['borrowed']) && $postedData['borrowed']) {
                return UserEquipped::equipItemForUser($itemid, $userid, $postedData['borrowed']);
            }

            return UserEquipped::equipItemForUser($itemid, $userid);
        }

        if ($action == 'unequip') {
            return UserEquipped::unequipItemForUser($itemid, $userid);
        }

        if ($action == 'use') {
            return Inventory::userUseItem($userid, $itemid);
        }

        if ($action == 'sellitem') {
            return Inventory::userSellItem($userid, $itemid, $postedData['quantity']);
        }

        if ($action == 'senditem') {
            return Inventory::userSendItem($userid, $itemid, $postedData['quantity'], (int) $postedData['to_user_id']);
        }

        if ($action == 'putonmarket') {
            return Inventory::putOnMarket($userid, $itemid, $postedData['quantity'], $postedData['price']);
        }

        if ($action == 'return') {
            return Inventory::returnItem($userid, $itemid);
        }
    }

    /*
     * Check a user has an item
     *
     * @param integer $userid
     * @param integer $itemid
     * @param integer $borrowed 0
     *
     * @return boolean
     */
    public static function getItemQuantity(int $userid, int $itemid, int $borrowed = 0)
    {
        $result = DBi::$conn->query('SELECT `quantity` FROM `inventory` WHERE `userid`=\'' . $userid . '\' AND `itemid`=\'' . $itemid . '\' AND `borrowed`=\'' . $borrowed . '\'');
        if ($result->num_rows == 0) {
            return 0;
        }
        $arr = mysqli_fetch_array($result);

        return $arr['quantity'];
    }

    /*
     * Sell an item for a user
     *
     * @param integer $user_id
     * @param integer $item_id
     * @param integer $quantity
     *
     * @return boolean
     */
    public static function userSellItem(int $user_id, int $item_id, int $quantity = 1)
    {
        $response = [];
        $response['success'] = true;

        try {
            $item = Item::SGet($item_id);
            $user = UserFactory::getInstance()->getUser($user_id);

            if (!is_object($item) || !is_object($user)) {
                $response['success'] = false;
                $response['error'] = 'Something went wrong, if this issue persists please message an admin!';

                return $response;
            }
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['error'] = 'Something went wrong, if this issue persists please message an admin!';

            return $response;
        }

        if ($quantity <= 0) {
            $response['success'] = false;
            $response['error'] = 'You must provide a quantity when selling an item!';

            return $response;
        }

        $item_quantity = Inventory::getItemQuantity($user->id, $item->id);
        if ($item_quantity < $quantity) {
            $response['success'] = false;
            $response['error'] = "You don't have " . $quantity . ' ' . $item->itemname . '(s) to sell!';

            return $response;
        }

        $total_sale_price = $item->sellprice * $quantity;
        $user->AddToAttribute('money', number_format($total_sale_price, 0, '.', ''));
        $user->RemoveItems($item, $quantity);

        $response['message'] = 'You have successfully sold ' . $quantity . ' x ' . $item->itemname . ' for $' . number_format($total_sale_price) . '!';

        return $response;
    }

    /*
     * Send an item to a user
     *
     * @param integer $user_id
     * @param integer $item_id
     * @param integer $quantity
     * @param integer $to_user_id
     *
     * @return boolean
     */
    public static function userSendItem(int $user_id, int $item_id, int $quantity, int $to_user_id)
    {
        $response = [];
        $response['success'] = true;

        try {
            $item = Item::SGet($item_id);
            $user = UserFactory::getInstance()->getUser($user_id);
            $toUser = UserFactory::getInstance()->getUser($to_user_id);

            if (!is_object($item) || !is_object($user) || !is_object($toUser)) {
                $response['success'] = false;
                $response['error'] = 'Something went wrong, if this issue persists please message an admin!';

                return $response;
            }
        } catch (\Exception $e) {
            $response['success'] = false;
            $response['error'] = 'Something went wrong, if this issue persists please message an admin!';

            return $response;
        }

        if ($quantity <= 0) {
            $response['success'] = false;
            $response['error'] = 'You must provide a quantity when sending an item!';

            return $response;
        }
        if ($item->type == 'house') {
            $result = DBi::$conn->query('SELECT `awake` FROM `inventory` WHERE `userid`=\'' . $user_class->id . '\' AND `itemid`=' . $item->id);
            $awake = mysqli_fetch_assoc($result);
            $per = ($awake['awake'] / 100) * 20;
            $awa = $awake['awake'] - $per;
        }
        $item_quantity = Inventory::getItemQuantity($user->id, $item->id);
        if ($item_quantity < $quantity) {
            $response['success'] = false;
            $response['error'] = "You don't have " . $quantity . ' ' . $item->itemname . '(s) to send!';

            return $response;
        }

        $user->RemoveItems($item, $quantity);

            $toUser->AddItems($item->id, $quantity);
        if ($item->id == 226) {
            $toUser->RemoveItems($item, $quantity);
            $toUser->AddToAttribute('exp', 5000);
        }
        if ($awa >= 0) {
            DBi::$conn->query('UPDATE inventory SET awake = ' . $awa . ' WHERE userid = ' . $toUser->id . ' AND itemid = ' . $item.' - id');
        }
        Logs::SAddSendLog($user->id, $toUser->id, $item->itemname . ' - ' . $quantity, $user->ip, 0);
        if ($item->id != 226) {
            $toUser->Notify($user->formattedname . ' has sent you ' . $quantity . ' x ' . $item->itemname);
        } else {
            $toUser->Notify($user->formattedname . ' has sent you ' . $quantity . ' x ' . $item->itemname . ' and 5000 xp, these will not appear in your inventory');
        }
        $response['message'] = 'You have sent ' . $quantity . ' x ' . $item->itemname . ' to ' . $toUser->username . '!';

        return $response;
    }

    /*
     * Return an item to a label
     *
     * @param integer $user_id
     * @param integer $item_id
     *
     * @return boolean
     */
    public static function returnItem(int $user_id, int $item_id)
    {
        $response = [];
        $response['success'] = true;

        $item = Item::SGet($item_id);
        $user = UserFactory::getInstance()->getUser($user_id);
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id, itemid, gangid, borrowerid, borrowed_to_user_id')
            ->from('gangarmory')
            ->where('itemid = :item_id')
            ->setParameter('item_id', $item->id)
            ->andWhere('borrowed_to_user_id = :borrowed_to_user_id')
            ->setParameter('borrowed_to_user_id', $user_id)
            ->setMaxResults(1)
        ;
        $gangArmory = $queryBuilder->execute()->fetch();

        if (!is_object($item) || !is_object($user)) {
            $response['success'] = false;
            $response['error'] = 'Something went wrong, if this issue persists please message an Admin!';

            return $response;
        }

        $item_quantity = Inventory::getItemQuantity($user->id, $item->id, 1);
        if ($item_quantity < 1) {
            $response['success'] = false;
            $response['error'] = "You don't have " . 1 . ' ' . $item->itemname . ' to return to your regiment!';

            return $response;
        }

        $user->RemoveItems($item, 1, 1);

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->update('gangarmory')
            ->set('borrowed_to_user_id', ':borrowed_to_user_id')
            ->where('id = :id')
            ->setParameter('borrowed_to_user_id', 0)
            ->setParameter('id', $gangArmory['id'])
            ->execute()
        ;

        $response['message'] = 'You have returned ' . 1 . ' x ' . $item->itemname . ' to your regiment!';

        return $response;
    }

    /*
     * Use an item for a user
     *
     * @param integer $user_id
     * @param integer $item_id
     *
     * @return boolean
     */
    public static function userUseItem(int $user_id, int $item_id)
    {
        $response = [];
        $response['success'] = true;

        $item = Item::SGet($item_id);
        $user = UserFactory::getInstance()->getUser($user_id);
        $user2 = SUserFactory::getInstance()->getUser($user_id);

        if (!is_object($item) || !is_object($user)) {
            $response['success'] = false;
            $response['error'] = 'Something went wrong, if this issue persists please message an Admin!';

            return $response;
        }

        $item_quantity = Inventory::getItemQuantity($user->id, $item->id);
        if ($item_quantity === 0) {
            $response['success'] = false;
            $response['error'] = "You can't use an item you don't own!";

            return $response;
        }

        if($item->id == 51){
            $response['success'] = false;
            $response['error'] = "You can't use this item!";
        }

        ActionLogs::Log($user->id, 'Use Item', $item->id); // Log the action


        $response = $item->useItem($user);
        if (isset($response['error'])) {
            return $response;
        }

        $user->RemoveItems($item, 1);

        if ($item->id == Item::GetItemId('BIGMEDPACK_NAME_V2') && $user2->tutorial_v2 == 'inventory_1') {
            $user2->SetAttribute('tutorial_v2', 'gym_1');
            $user->AddToAttribute('points', 120);
        }

        $response['message'] = 'You have successfully used a ' . $item->itemname . '!';

        return $response;
    }

    /*
     * Put an item on the market
     *
     * @param integer $user_id
     * @param integer $item_id
     * @param integer $quantity
     * @param integer $price
     *
     * @return boolean
     */
    public static function putOnMarket(int $user_id, int $item_id, int $quantity, int $price)
    {
        $response = [];
        $response['success'] = true;

        $item = Item::SGet($item_id);
        $user = UserFactory::getInstance()->getUser($user_id);

        if (!is_object($item) || !is_object($user)) {
            $response['success'] = false;
            $response['error'] = 'Something went wrong, if this issue persists please message an admin!';

            return $response;
        }

        if ($price <= 0) {
            $response['success'] = false;
            $response['error'] = 'You must enter a valid price to post onto the market.';

            return $response;
        }

        if ($quantity <= 0) {
            $response['success'] = false;
            $response['error'] = 'You must enter a valid quantity to post onto the market.';

            return $response;
        }

        $item_quantity = Inventory::getItemQuantity($user->id, $item->id);
        if ($item_quantity === 0) {
            $response['success'] = false;
            $response['error'] = "You can't put an item on the market that you don't own!";

            return $response;
        }

        if ($quantity > $item_quantity) {
            $response['success'] = false;
            $response['error'] = "You don't have that many to put on the market!";

            return $response;
        }

        for ($i = 0; $i < $quantity; ++$i) {
            ($result = DBi::$conn->query(
                'INSERT INTO `itemmarket` SET `itemid`="' .
                $item->id .
                '", `userid`="' .
                $user->id .
                '", `cost`="' .
                $price .
                '", `timestamp`="' .
                time() .
                '"'
            )) or exit(DBi::$conn->error);

            $user->RemoveItems($item, 1);
        }

        $response['message'] = 'You have successfully listed ' . $quantity . ' x ' . $item->itemname . ' on the market!';

        return $response;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'userid',
            'itemid',
            'quantity',
            'borrowed',
            'awake',
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
