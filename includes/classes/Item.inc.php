<?php

class Item extends CachedObject
{
    const NORMAL_ITEM = 0;
    const AUCTION_ITEM = 1;
    const UPGRADE_ITEM = 2;
    const UPGRADE_ITEM_WEAPON = 3;
    const UPGRADE_ITEM_ARMOR = 4;
    const UPGRADE_ITEM_USPECIAL = 5;
    const UPGRADE_ITEM_LSPECIAL = 6;

    const RENDER_TYPE_DEFAULT = 'default';
    const RENDER_TYPE_INVENTORY = 'inventory';
    const RENDER_TYPE_EQUIPPED = 'equipped';
    const RENDER_TYPE_STORE = 'store';
    const RENDER_TYPE_STORE_POINTS = 'storep';

    public static $idField = 'id';
    public static $dataTable = 'items';

    public function __construct($id)
    {
        if ($id == 0) {
            // Default item
            $this->id = 0;
            $this->itemname = 'COM_NONE'; //'None';
            $this->description = 'COM_NONE'; //'None';
            $this->cost = 0;
        } else {
            parent::__construct($id);
        }

        $this->itemname = constant($this->itemname);
        $this->description = defined($this->description) ? constant($this->description) : '';

        if ($this->cost > 0) {
            $this->sellprice = $this->cost * 0.4;
        }
    }

    public static function SGet($id)
    {
        return new Item($id);
    }

    public static function GetAll()
    {
        $objs = parent::GetAllById(self::$idField, self::GetDataTableFields(), self::GetDataTable());

        return $objs;
    }

    public static function getSpecialItems() : array
    {
        return [
            Item::GetItemId('GOLDEN_PUMPKIN_NAME')
        ];
    }

    public static function GetItemId($name)
    {
        $result = DBi::$conn->query('SELECT id  FROM `items` WHERE `itemname` = "' . $name . '"');

        $row = mysqli_fetch_array($result);

        return $row['id'];
    }
    public static function GetItemName($id)
    {
        $result = DBi::$conn->query('SELECT itemname  FROM `items` WHERE `id` = "' . $id . '"');

        $row = mysqli_fetch_array($result);

        return $row['itemname'];
    }


    public static function GetAllItem()
    {
        $objs = parent::GetAllById(self::$idField, self::GetDataTableFields(), self::GetDataTable());

        return $objs;
    }

    public static function GetAllWhere($whereStr, $order = '', $dir = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::$dataTable, $whereStr, false, false, $order, $dir, false, false);
    }

    public static function GetBy($type){
        $armorType= ['head','chest','legs','gloves','boots'];
        $types = ['weapon','misc', 'armor', 'drug', 'special', 'upgrade', 'House', 'fertilizer', '', 'medical', 'attachment', 'Misc'];
        if(!in_array($type, $armorType) &&   !in_array($type, $types)){
            return;
        }
        if(in_array($type, $armorType)){

        return DBi::$conn->query("SELECT * FROM items WHERE armortype='$type'");
    }else {
            return DBi::$conn->query("SELECT * FROM items WHERE item_type='$type'");
        }
    }

    public static function GetAllRepItems(User $user)
    {
        $repItems = self::GetAllWhere('is_rep_store = "1"');

        return $repItems;
    }

    public static function GetAllArmors(User $user)
    {
        $armors = self::GetAllForShop($user, 'item_type = "armor"', ['cost', 'defense', 'type']);

        return $armors;
    }

    public static function GetAllSpeed(User $user)
    {
        $objs = self::GetAllForShop($user, '', ['cost', 'speed', 'type']);

        $speed = [];
        foreach ($objs as $obj) {
            if (($obj->speed > 0  /*|| $obj->type == Item::UPGRADE_ITEM_ARMOR*/) && $obj->buyable == 1) {
                $speed[] = $obj;
            }
        }

        return $speed;
    }
    public static function getAllPawn()
    {
        $sql = 'select id from ' . self::$dataTable . ' where cost > 0 and (offense>0 or speed> 0 or defense >0)';
        $rs = DBi::$conn->query($sql);
        while ($row = mysqli_fetch_object($rs)) {
            $list[] = new Item($row->id);
        }

        return $list;
    }

    public static function GetAllWeapons(User $user)
    {
        $objs = self::GetAllForShop($user, 'item_type = "weapon"', ['cost', 'offense', 'type']);

        $weapons = [];
        foreach ($objs as $obj) {
            if (($obj->offense > 0 /*|| $obj->type == Item::UPGRADE_ITEM_WEAPON*/) && $obj->buyable == 1) {
                $weapons[] = $obj;
            }
        }

        return $weapons;
    }

    public static function GetAllForShop(User $user, $whereClause = '', array $order = [], $dir = 'ASC')
    {
        $whereStr = '`buyable` = "1" AND prison like "%,' . $user->city . ',%"';
        if (!empty($whereClause)) {
            $whereStr .= ' AND ' . $whereClause;
        }

        return parent::GetAll(self::GetDataTableFields(), self::$dataTable, $whereStr, false, false, implode('`, `', $order), $dir);
    }

    public static function CountItemInArmory($itemid, $gangid)
    {
        $res = DBi::$conn->query("SELECT `itemid` FROM `gangarmory` WHERE `itemid`='" . $itemid . "' AND `gangid`='" . $gangid . "'");

        return mysqli_num_rows($res);
    }

    public static function GetItemInArmory($itemid, $gangid)
    {
        $res = DBi::$conn->query("SELECT `id`, `itemid`, `gangid`, `borrowerid` FROM `gangarmory` WHERE `itemid`='" . $itemid . "' AND `borrowerid` IS NULL and `gangid`='" . $gangid . "' LIMIT 1");
        if (mysqli_num_rows($res) == 0) {
            return null;
        }

        return mysqli_fetch_object($res);
    }

    public function AddToGangArmory(User $user, $quantity = 1)
    {
        if ($quantity <= 0)
            throw new SoftException(ITEM_INVALID_QTY);
        else if ($user == null)
            throw new SoftException(ITEM_BAD_USER_INFO);
        else if ($user->GetItemQuantity($this->id) < $quantity)
            throw new SoftException(ITEM_NOT_ENOUGH_ITEMS);
        if (!$user->RemoveItems($this, $quantity))
            throw new SoftException(ITEM_CANT_DEPOSIT_ITEMS);
        $nbDeposits = $quantity;
        while ($quantity--)
            DBi::$conn->query("INSERT INTO `gangarmory` (itemid, gangid)" . "VALUES ('$this->id', '$user->gang')");
        Logs::SAddVaultLog($user->gang
            , $user->id
            , '<b>' . $this->itemname . '(s)</b>'
            , time()
            , 'DepositArm'
            , $nbDeposits);

        return true;
    }

    public static function RemoveFromGangArmory(User $user, Item $item, $quantity = 1)
    {
        if ($quantity <= 0) {
            throw new SoftException(ITEM_INVALID_QTY);
        } elseif ($user->IsGangPermitted($user->GetGang(), 'ARMOR') === false) {
            throw new SoftException(NOT_AUTHORIZED);
        }
        $nbItems = Item::CountItemInArmory($item->id, $user->gang);

        if ($nbItems < $quantity) {
            throw new FailedResult(ITEM_NOT_ENOUGH_ARMORY_ITEMS);
        }
       $query = DBi::$conn->query('DELETE FROM `gangarmory` WHERE `itemid`=\'' . $item->id . '\' and `gangid`=\'' . $user->gang . '\' AND `borrowerid` = 0 LIMIT ' . $quantity);

        $affected = DBi::$conn->affected_rows;

        if ($affected == 0) {
            throw new SoftException(ITEM_CANT_TAKEN);
        } elseif ($affected < $quantity) {
            $quantity = $affected;
        }

        Logs::SAddVaultLog($user->GetGang()->id, $user->id, '<b>' . $item->itemname . '(s)</b>', time(), 'WithdrawArm', $quantity);

        $user->AddItems($item->id, $quantity);

        return true;
    }

    public static function RemoveAlbumfromGang(User $user, Item $item, $quantity = 1)
    {
        if ($quantity <= 0) {
            throw new SoftException(ITEM_INVALID_QTY);
        } elseif ($user->IsGangPermitted($user->GetGang(), 'ARMOR') === false) {
            throw new SoftException(NOT_AUTHORIZED);
        }
        $nbItems = Item::CountItemInArmory($item->id, $user->gang);
        if ($nbItems < $quantity) {
            throw new FailedResult(ITEM_NOT_ENOUGH_ARMORY_ITEMS);
        }
        DBi::$conn->query('DELETE FROM `gangarmory` WHERE `itemid`=\'' . $item->id . '\' and `gangid`=\'' . $user->gang . '\' AND `borrowerid` = 0 LIMIT ' . $quantity);
        $affected = DBi::$conn -> affected_rows;
        if ($affected == 0) {
            throw new SoftException(ITEM_CANT_TAKEN);
        } elseif ($affected < $quantity) {
            $quantity = $affected;
        }
        Logs::SAddVaultLog($user->GetGang()->id, $user->id, '<b>' . $item->itemname . '(s)</b>', time(), 'WithdrawArm', $quantity);
        //$user->AddItems($item->id, $quantity);
        return true;
    }

    static function SumItemsInArmory($gangid)
    {
        $res = DBi::$conn->query('SELECT `itemid`, SUM(CASE WHEN `borrowerid` = 0 THEN 1 ELSE 0 END) `avail`, SUM(CASE WHEN `borrowerid` =0 THEN 0 ELSE 1 END) `loaned` FROM `gangarmory` WHERE `gangid`=\'' . $gangid . '\' GROUP BY itemid');

        if (mysqli_num_rows($res) == 0)
            return null;
        $objs = array();
        while ($obj = mysqli_fetch_object($res))
            $objs[] = $obj;

        return $objs;
    }

    public function GetItemUsageLimitForUser($userid)
    {
        $item = parent::GetAll(['usage_limit'], 'item_usage', '`item_id`=' . $this->id . ' AND `user_id`=' . $userid);
        if (empty($item) || !isset($item[0]->usage_limit)) {
            return false;
        }

        return $item[0]->usage_limit;
    }



    public static function SGetFormattedName($id)
    {
        if ($id <= 0) {
            return '';
        }
        try {
            $item = new Item($id);

            return HTML::ShowItemPopup($item->itemname, $item->id . '.&awake=' . $item->awake);
        } catch (Exception $e) {
            return false;
        }
    }

    public function SGetFormattedImage($id)
    {
        try {
            if ($id <= 0) {
                return '';
            }

            $item = new Item($id);

            return HTML::ShowItemPopup('<img src="' . $item->image . '" border=0>', $item->id . '.&awake=' . $item->awake);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Use an item.
     */
    public function useItem(User $user)
    {
        $response = [];
        $response['success'] = true;

        if ($this->item_type === 'weapon' || $this->item_type === 'armor') {
            $response['success'] = false;
            $response['error'] = "This item can't be used!";

            return $response;
        }

        if ($this->item_type === 'drug') {
            $response = Drug::Take($user, $this, 30);

            return $response;
        }
        if($this->id == 36){
            if ($user->awake >= $user->GetMaxAwake()) {
                $response['success'] = false;
                $response['error'] = 'Your awake is already full!';

                return $response;
            }
            $user->SetAttribute('awake', $user->GetMaxAwake());
         }
        if ($this->awake) {
            if ($user->awake >= $user->GetMaxAwake()) {
                $response['success'] = false;
                $response['error'] = 'Your awake is already full!';

                return $response;
            }

            $awakeValue = (((int) $user->GetMaxAwake() / 100) * $this->awake);
            $newAwake = $user->awake + $awakeValue;
            if ($newAwake >= $user->GetMaxAwake()) {
                $newAwake = $user->GetMaxAwake();
            }

            $user->SetAttribute('awake', $newAwake);
        }

        if ($this->heal) {
            if ($user->hp >= $user->GetMaxHp() && !$user->IsInHospital()) {
                $response['success'] = false;
                $response['error'] = 'Your health is already full!';

                return $response;
            }

            $healValue = (((int) $user->GetMaxHP() / 100) * $this->heal);
            $newHeal = $user->hp + $healValue;
            if ($newHeal >= $user->GetMaxHP()) {
                $newHeal = $user->GetMaxHP();
            }

            $user->SetAttribute('hp', $newHeal);

            if ($this->reduce_hosp_time && $user->IsInHospital()) {
                $user->ReduceHospitalByMinutes($this->reduce_hosp_time);
            }
        }

        if ($this->rp_days) {
            $newRmDays = $user->rmdays + $this->rp_days;
            $user->SetAttribute('rmdays', $newRmDays);
        }

        if ($this->money) {
            User::SAddBankMoney($user->id, $this->money);
        }

        if ($this->points) {
            $user->AddPoints($this->points);
        }

        return $response;
    }

    /**
     * Render an already loaded item.
     *
     * @param string $type
     * @param null   $quantity
     * @param string $actionHtml
     *
     * @return string
     */
    public function render($type = self::RENDER_TYPE_DEFAULT, $quantity = null, $actionHtml = '')
    {
        $view = new View('components/item');
        $view->RegisterVariable('item', $this);
        $view->RegisterVariable('type', $type);
        $view->RegisterVariable('quantity', $quantity);
        if (!empty($actionHtml)) {
            $view->RegisterVariable('actionHtml', $actionHtml);
        }

        return $view->Render(true);
    }

    /**
     * Render item by ID.
     *
     * @param string $type
     * @param null   $quantity
     * @param string $actionHtml
     */
    public static function RenderItem(int $id, $type = self::RENDER_TYPE_DEFAULT, $quantity = null, $actionHtml = ''): string
    {
        $item = new Item($id);

        return $item->render($type, $quantity, $actionHtml);
    }

    /**
     * Retrieve the item modifiers are an array.
     */
    public function getModifiers($hideLevel = false): array
    {
        $modifiers = [];
        if ($this->type == Item::AUCTION_ITEM) {
            $modifiers[MAX_AWAKE_BONUS] = [
                'value' => $this->awake,
            ];
        }
        if ($this->type == Item::NORMAL_ITEM) {
            if ($this->offense != 0) {
                $modifiers[DESC_ATTACK_MODIFIER] = [
                    'value' => ($this->offense > 0 ? '+' : '') . $this->offense . '%',
                    'rawValue' => $this->offense,
                ];
            }
            if ($this->defense != 0) {
                $modifiers[DESC_DEFENSE_MODIFIER] = [
                    'value' => ($this->defense > 0 ? '+' : '') . $this->defense . '%',
                    'rawValue' => $this->defense,
                ];
            }
            if ($this->speed != 0) {
                $modifiers[DESC_SPEED_MODIFIER] = [
                    'value' => ($this->speed > 0 ? '+' : '') . $this->speed . '%',
                    'rawValue' => $this->speed,
                ];
            }
            if ($this->strength_boost != 0) {
                $modifiers[DESC_ATTACK_MODIFIER] = [
                    'value' => ($this->strength_boost > 0 ? '+' : '') . $this->strength_boost . '%',
                    'rawValue' => $this->strength_boost,
                ];
                $modifiers['<i class="fas fa-clock"></i>'] = [
                    'value' => '30 min',
                ];
            }
            if ($this->defense_boost != 0) {
                $modifiers[DESC_DEFENSE_MODIFIER] = [
                    'value' => ($this->defense_boost > 0 ? '+' : '') . $this->defense_boost . '%',
                    'rawValue' => $this->defense_boost,
                ];
                $modifiers['<i class="fas fa-clock"></i>'] = [
                    'value' => '30 min',
                ];
            }
            if ($this->speed_boost != 0) {
                $modifiers[DESC_SPEED_MODIFIER] = [
                    'value' => ($this->speed_boost > 0 ? '+' : '') . $this->speed_boost . '%',
                    'rawValue' => $this->speed_boost,
                ];
                $modifiers['<i class="fas fa-clock"></i>'] = [
                    'value' => '30 min',
                ];
            }
            if ($this->energy_boost != 0) {
                $modifiers['Energy'] = [
                    'value' => ($this->energy_boost > 0 ? '+' : '') . $this->energy_boost . '%',
                    'rawValue' => $this->energy_boost,
                ];
                $modifiers['<i class="fas fa-clock"></i>'] = [
                    'value' => '30 min',
                ];
            }
            if ($this->nerve_boost != 0) {
                $modifiers['Nerve'] = [
                    'value' => ($this->nerve_boost > 0 ? '+' : '') . $this->nerve_boost . '%',
                    'rawValue' => $this->nerve_boost,
                ];
                $modifiers['<i class="fas fa-clock"></i>'] = [
                    'value' => '30 min',
                ];
            }
            if ($this->awake_boost != 0) {
                $modifiers['Awake'] = [
                    'value' => ($this->awake_boost > 0 ? '+' : '') . $this->awake_bonus . '%',
                    'rawValue' => $this->awake_boost,
                ];
                $modifiers['<i class="fas fa-clock"></i>'] = [
                    'value' => '30 min',
                ];
            }
            if ($this->awake != 0) {
                $modifiers['Awake'] = [
                    'value' => ($this->awake > 0 ? '+' : '') . $this->awake . '%',
                    'rawValue' => $this->awake,
                ];
            }
            if ($this->heal != 0) {
                $modifiers['HP'] = [
                    'value' => ($this->heal > 0 ? '+' : '') . $this->heal . '%',
                    'rawValue' => $this->heal,
                ];
            }
        } elseif ($this->type == Item::NORMAL_ITEM && $this->dailypayment > 0) {
            $modifiers['Gas and Maintenance'] = [
                'value' => $this->dailypayment,
            ];
        }
        if ($this->level > 1 && !$hideLevel) {
            $modifiers[DESC_REQ_LEVEL] = [
                'value' => $this->level,
            ];
        }

        return $modifiers;
    }

    /**
     * Retrieve all locations for the item.
     */
    public function getLocations(): array
    {
        if (strlen(trim($this->prison)) > 0) {
            $locationIds = array_filter(explode(',', $this->prison));

            return array_map(function (int $id) {
                return CityName::get($id);
            }, $locationIds);
        }

        return [];
    }

    public static function getHIItemIds()
    {
        return array(242, 243, 244, 245, 246, 247, Item::GetItemId('NEW_YEAR_BALLOONS_NAME'), Item::GetItemId('NEW_YEAR_GOLD_GARLAND_NAME'), Item::GetItemId('FIREPLACE_NAME'), Item::GetItemId('RABBIT_CAGE_NAME'));
    }
    
    public function getIsHiItem()
    {
        if (in_array($this->id, self::getHIItemIds())) {
            return true;
        }
        
        return false;
    }


    public static function getAwakePillId()
    {
        $item = Item::GetAllWhere('awake = 100');
        if ($item && count($item) > 0) {
            return $item[0]->id;
        }
    }

    public static function getUpgradePackId()
    {
        $item = Item::GetAllWhere('rp_days = 31');
        if ($item && count($item) > 0) {
            return $item[0]->id;
        }
    }

    public static function getDiscId()
    {
        $item = Item::GetAllWhere('itemname = "DISK_NAME"');
        if ($item && count($item) > 0) {
            return $item[0]->id;
        }
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'itemname',
            'description',
            'item_type',
            'armortype',
            'cost',
            'image',
            'offense',
            'defense',
            'heal',
            'awake',
            'buyable',
            'level',
            'security_level',
            'prison',
            'type',
            'linked',
            'speed',
            'dailypayment',
            'reduce_hosp_time',
            'rp_days',
            'money',
            'points',
            'strength_boost',
            'defense_boost',
            'speed_boost',
            'energy_boost',
            'nerve_boost',
            'awake_boost',
            'is_rep_store',
            'rep_cost',
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
