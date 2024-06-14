<?php

class ItemSets extends CachedObject
{
    public static $dataTable = 'item_sets';
    public static $idField = 'id';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function SGet($id)
    {
        return new ItemSets($id);
    }

    public function getItemsAsArray()
    {
        $items = array();

        if ($this->weapon_item_id) {
            $items[] = Item::SGet($this->weapon_item_id);
        }

        if ($this->head_item_id) {
            $items[] = Item::SGet($this->head_item_id);
        }

        if ($this->chest_item_id) {
            $items[] = Item::SGet($this->chest_item_id);
        }

        if ($this->legs_item_id) {
            $items[] = Item::SGet($this->legs_item_id);
        }

        if ($this->boots_item_id) {
            $items[] = Item::SGet($this->boots_item_id);
        }

        if ($this->gloves_item_id) {
            $items[] = Item::SGet($this->gloves_item_id);
        }

        return $items;
    }

    public function checkHasItemSetEquipped($user)
    {
        $weaponItemId = 0;
        if ($user->GetWeapon()) {
            $weaponItemId = $user->GetWeapon()->id;
        }
        $headItemId = 0;
        if ($user->getArmorForType('head')) {
            $headItemId = $user->getArmorForType('head')->id;
        }
        $chestItemId = 0;
        if ($user->getArmorForType('chest')) {
            $chestItemId = $user->getArmorForType('chest')->id;
        }
        $legsItemId = 0;
        if ($user->getArmorForType('legs')) {
            $legsItemId = $user->getArmorForType('legs')->id;
        }
        $bootsItemId = 0;
        if ($user->getArmorForType('boots')) {
            $bootsItemId = $user->getArmorForType('boots')->id;
        }
        $glovesItemId = 0;
        if ($user->getArmorForType('gloves')) {
            $glovesItemId = $user->getArmorForType('gloves')->id;
        }

        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('set_name')
            ->from('item_sets')
            ->where('(weapon_item_id = :weapon_item_id OR weapon_item_id = 0)')
            ->setParameter('weapon_item_id', $weaponItemId)
            ->andWhere('(head_item_id = :head_item_id OR head_item_id = 0)')
            ->setParameter('head_item_id', $headItemId)
            ->andWhere('(chest_item_id = :chest_item_id OR chest_item_id = 0)')
            ->setParameter('chest_item_id', $chestItemId)
            ->andWhere('(legs_item_id = :legs_item_id OR legs_item_id = 0)')
            ->setParameter('legs_item_id', $legsItemId)
            ->andWhere('(boots_item_id = :boots_item_id OR boots_item_id = 0)')
            ->setParameter('boots_item_id', $bootsItemId)
            ->andWhere('(gloves_item_id = :gloves_item_id OR gloves_item_id = 0)')
            ->setParameter('gloves_item_id', $glovesItemId)
            ->setMaxResults(1)
        ;
        $itemSets = $queryBuilder->execute()->fetchAssociative();

        return $itemSets;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'set_name',
            'weapon_item_id',
            'head_item_id',
            'chest_item_id',
            'legs_item_id',
            'boots_item_id',
            'gloves_item_id'
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