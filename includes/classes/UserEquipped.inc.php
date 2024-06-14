<?php

class UserEquipped extends BaseObject
{
    public static $dataTable = 'user_equipped';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    /*
     * Equip an item for the User
     *
     * @param integer $item_id
     * @param integer $user_id
     * @param integer $borrowed 0
     *
     * @return array
     */
    public static function equipItemForUser(int $item_id, int $user_id, int $borrowed = 0)
    {
        $response = [];
        $response['success'] = true;
        $response['message'] = 'You have successfully equipped your item!';

        $item = Item::SGet($item_id);
        $user = UserFactory::getInstance()->getUser($user_id);
        $user2 = SUserFactory::getInstance()->getUser($user->id);

        if (!is_object($item) || !is_object($user)) {
            $response['success'] = false;
            $response['error'] = 'Something went wrong, if this issue persists please message an Admin!';

            return $response;
        }

        $itemQuantity = Inventory::getItemQuantity($user->id, $item->id);
        $itemQuantityBorrowed = Inventory::getItemQuantity($user->id, $item->id, 1);
        if ($itemQuantity <= 0 && $itemQuantityBorrowed <= 0) {
            $response['success'] = false;
            $response['error'] = "You can't equip an item you don't own!";

            return $response;
        }
        if ($item->security_level > $user->securityLevel){
            $response['success'] = false;
            $response['error'] = "You can't equip an item above your prestige!";

            return $response;
        }
        $userEquippedItemForItemType = null;
        $userEquippedItemForItemType = null;
        if ($item->item_type === 'armor') {
            $userEquippedItemForItemTypeBorrowed = UserEquipped::getUserEquippedItemForItemType($user->id, $item->item_type, $item->armortype, 1);
            if (!$userEquippedItemForItemTypeBorrowed) {
                $userEquippedItemForItemType = UserEquipped::getUserEquippedItemForItemType($user->id, $item->item_type, $item->armortype);
            }
        } else {
            $userEquippedItemForItemTypeBorrowed = UserEquipped::getUserEquippedItemForItemType($user->id, $item->item_type, $item->armortype, 1);
            if (!$userEquippedItemForItemTypeBorrowed) {
                $userEquippedItemForItemType = UserEquipped::getUserEquippedItemForItemType($user->id, $item->item_type);
            }
        }
        if ($userEquippedItemForItemTypeBorrowed) {
            UserEquipped::deleteUserEquippedItemForItemType($user->id, $item->item_type, $item->armortype, 1);
            $user->AddItems($userEquippedItemForItemTypeBorrowed, 1, 1);
        } else if ($userEquippedItemForItemType) {
            UserEquipped::deleteUserEquippedItemForItemType($user->id, $item->item_type, $item->armortype);
            $user->AddItems($userEquippedItemForItemType, 1);
        }

        if ($borrowed > 0) {
            $user->RemoveItems($item, 1, 1);
            $createData = ['user_id' => $user->id, 'item_id' => $item->id, 'borrowed' => 1];
            $response = UserEquipped::createUserEquipped($createData);
        } else {
            $createData = ['user_id' => $user->id, 'item_id' => $item->id];
            $response = UserEquipped::createUserEquipped($createData);
            $user->RemoveItems($item, 1);
        }

        if ($user2->tutorial_v2 == 'inventory_equip_2') {
            $user2->SetAttribute('tutorial_v2', 'attack_2');
        }

        $response['message'] = 'You have equipped a ' . $item->itemname;

        return $response;
    }

    /*
    * Unequip an item for the User
    *
    * @param integer $item_id
    * @param integer $user_id
    *
    * @return array
    */
    public static function unequipItemForUser(int $item_id, int $user_id)
    {
        $response = [];
        $response['success'] = true;
        $response['message'] = 'You have successfully unequipped your item!';

        $item = Item::SGet($item_id);
        $user = UserFactory::getInstance()->getUser($user_id);

        if (!is_object($item) || !is_object($user)) {
            $response['success'] = false;
            $response['error'] = 'Something went wrong, if this issue persists please message an Admin!';

            return $response;
        }

        if (!UserEquipped::getUserEquippedItemForItemId($user->id, $item->id)) {
            $response['success'] = false;
            $response['error'] = "You can't unequip an item you don't own!";

            return $response;
        }

        $armorType = null;
        if ($item->item_type == 'armor') {
            $armorType = $item->armortype;
        }

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('borrowed')
            ->from('user_equipped')
            ->where('item_id = :item_id')
            ->setParameter('item_id', $item->id)
            ->andWhere('user_id = :user_id')
            ->setParameter('user_id', $user->id)
            ->setMaxResults(1)
        ;
        $borrowed = $queryBuilder->execute()->fetch();

        UserEquipped::deleteUserEquippedItemForItemType($user->id, $item->item_type, $armorType);
        if ($borrowed['borrowed']) {
            $user->AddItems($item->id, 1, 1);
        } else {
            $user->AddItems($item->id, 1, 0);
        }

        if ($item->item_type == 'weapon') {
            unset($user->weaponobj);
        } else {
            $armorobjFieldName = 'armorobj' . $item->armortype;
            unset($user->$armorobjFieldName);
        }

        return $response;
    }

    /*
     * Check if a user has a specific item type equipped
     *
     * @param integer $user_id
     * @param string $item_type
     * @param string $armor_type null
     * @param string $borrowed 0
     *
     * @return mixed
     */
    public static function getUserEquippedItemForItemType(int $user_id, string $item_type, string $armor_type = null, int $borrowed = 0)
    {
        $sql = '
          SELECT 
              item_id 
          FROM 
              user_equipped AS ue
              LEFT JOIN items AS i ON i.id = ue.item_id
          WHERE 
              ue.user_id=\'' . $user_id . '\' AND 
              i.item_type=\'' . $item_type . '\'
        ';
        if ($armor_type) {
            $sql .= '
                AND i.armortype=\'' . $armor_type . '\' 
            ';
        }
        if ($borrowed) {
            $sql .= '
                AND ue.borrowed=\'' . $borrowed . '\' 
            ';
        }

        $result = DBi::$conn->query($sql);
        if ($result->num_rows == 0) {
            return false;
        }

        $arr = mysqli_fetch_array($result);

        return $arr[0];
    }

    /*
     * Check if a user has a specific item id equipped
     *
     * @param integer $user_id
     * @param integer $item_id
     *
     * @return mixed
     */
    public static function getUserEquippedItemForItemId(int $user_id, int $item_id)
    {
        $sql = '
          SELECT 
              item_id 
          FROM 
              user_equipped AS ue
          WHERE 
              ue.user_id=\'' . $user_id . '\' AND 
              ue.item_id=\'' . $item_id . '\'
        ';

        $result = DBi::$conn->query($sql);
        if ($result->num_rows == 0) {
            return false;
        }

        $arr = mysqli_fetch_array($result);

        return $arr[0];
    }

    /*
     * Create a UserEquipped
     *
     * @param array $data
     *
     * return array
     */
    public static function createUserEquipped(array $data)
    {
        $response = [];
        $response['success'] = true;

        if (!isset($data['user_id'])) {
            $response['success'] = false;
        }

        if (!isset($data['item_id'])) {
            $response['success'] = false;
        }

        if ($response['success'] === true) {
            $now = new \DateTime();

            $sql = 'INSERT INTO `user_equipped` SET `user_id`="' . $data['user_id'] . '", `item_id`="' . $data['item_id'] . '"';
            if (isset($data['borrowed'])) {
               $sql .=  ', `borrowed` = "' . $data['borrowed'] . '"';
            }
            
            DBi::$conn->query($sql);
        }

        return $response;
    }

    /*
     * Delete UserEquipped for a specific item_type
     *
     * @param integer $user_id
     * @param string $item_type
     * @param string $armor_type null
     * @param integer $borrowed 0
     *
     * return boolean
     */
    public static function deleteUserEquippedItemForItemType(int $user_id, string $item_type, string $armor_type = null, int $borrowed = 0)
    {
        $sql = '
          SELECT 
              ue.id 
          FROM 
              user_equipped AS ue
              LEFT JOIN items AS i ON i.id = ue.item_id
          WHERE 
              ue.user_id=\'' . $user_id . '\' AND 
              i.item_type=\'' . $item_type . '\'
        ';
        if ($armor_type) {
            $sql .= '
                AND i.armortype=\'' . $armor_type . '\' 
            ';
        }
        if ($borrowed) {
            $sql .= '
                AND ue.borrowed=\'' . $borrowed . '\' 
            ';
        }
        $result = DBi::$conn->query($sql);

        if ($result->num_rows == 0) {
            return true;
        }

        $arr = mysqli_fetch_array($result);
        $id = $arr[0];

        if ($id) {
            DBi::$conn->query('DELETE FROM `user_equipped` WHERE `id` ="' . $id . '"');
        }

        return true;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'user_id',
            'item_id',
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
