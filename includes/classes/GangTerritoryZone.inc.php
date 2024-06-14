<?php

class GangTerritoryZone extends CachedObject
{
    public static $dataTable = 'gang_territory_zone';
    public static $idField = 'id';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public function claim($user_class)
    {
        if ($this->owned_by_gang_id) {
            throw new SoftException('You can\'t claim a territory that is already owned by a regiment.');
        }

        $gang = $user_class->GetGang();

        if (!$user_class->IsGangPermitted($gang, 'GATERW')) {
            throw new SoftException('You don\'t have the permissions to claim a territory.');
        }

        $shieldTime = time() + 7200;
        $this->SetAttribute('owned_by_gang_id', $gang->id);
        $this->SetAttribute('shield_time', $shieldTime);

        foreach ($gang->GetAllMembers() as $gangMember) {
            Event::Add($gangMember->id, 'Your regiment has claimed the territory ' . $this->name . '. Keep an eye out for any potential takeover attempts from other regiments.');
        }

        GangTerritoryZoneHistory::create($this, $gang->id);

        throw new SuccessResult('You have claimed the territory ' . $this->name .'. The territory will be under a default shield for the next 120 minutes before other regiments can attempt a takeover.');
    }

    public function startAttack($user_class)
    {
        $gang = $user_class->GetGang();
        if (!$user_class->IsGangPermitted($gang, 'GATERW')) {
            throw new SoftException('You must have permissions to claim a territory.');
        }

        if (!$this->owned_by_gang_id) {
            throw new SoftException('You can only attempt a takeover on a territory that has already been claimed.');
        }

        if ($this->owned_by_gang_id == $gang->id) {
            throw new SoftException('You can\'t takeover a territory that your regiment already owns.');
        }

        if ($this->shieldTime() > 0) {
            throw new SoftException('You can\'t takeover a territory that is under shield.');
        }

        if ($this->getActiveGangTerritoryZoneBattle()) {
            throw new SoftException('You can\'t takeover a territory that is already in a takeover attempt.');
        }

        if (count($gang->getAttackingGangTerritoryZoneBattles()) > 0) {
            throw new SoftException('Your regiment can only attempt one takeover at a time.');
        }

        GangTerritoryZoneBattle::create($this, $gang);

        $defendingGang = new Gang($this->owned_by_gang_id);
        foreach ($defendingGang->GetAllMembers() as $defendingGangMember) {
            Event::Add($defendingGangMember->id, 'Soldier, ready yourself for battle! ' . Gang::StaticGetPublicFormattedName($gang->id) . ' are attempting a takeover on one of your territories. <a href="gang_territories.php">Go to Territories</a>');
        }

        foreach ($gang->GetAllMembers() as $attackingGangMember) {
            Event::Add($attackingGangMember->id, 'Soldier, ready yourself for battle! Your regiment is attempting a territory takeover. <a href="gang_territories.php">Go to Territories</a>');
        }

        throw new SuccessResult('You have successfully initiated a takeover of the territory. All regiment members will be informed to prepare for the battle. The battle will commence in 30 minutes time.');
    }

    public function shieldTime()
    {
        if ($this->shield_time > time()) {
            $remaining = $this->shield_time - time();
            $remaining = $remaining / 60;

            return $remaining;
        }

        return 0;
    }

    public function getActiveGangTerritoryZoneBattle()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('gang_territory_zone_battle')
            ->where('gang_territory_zone_id = :gang_territory_zone_id')
            ->setParameter('gang_territory_zone_id', $this->id)
            ->andWhere('(is_complete IS NULL OR is_complete = 0)')
            ->setMaxResults(1)
        ;
        $gangTerritoryZoneBattle = $queryBuilder->execute()->fetchAll();

        if (isset($gangTerritoryZoneBattle[0])) {
            return GangTerritoryZoneBattle::SGet($gangTerritoryZoneBattle[0]['id']);
        } else {
            return null;
        }
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function SGet($id)
    {
        return new GangTerritoryZone($id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'city_id',
            'name',
            'owned_by_gang_id',
            'daily_points_payout',
            'daily_money_payout',
            'daily_dog_tag_payout',
            'daily_exp_payout',
            'daily_item_payout',
            'shield_time',
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