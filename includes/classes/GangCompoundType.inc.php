<?php

class GangCompoundType extends CachedObject
{
    public static $dataTable = 'gang_compound_type';
    public static $idField = 'id';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public function buy($user_class)
    {
        $gang = $user_class->GetGang();

        if (!$user_class->IsGangLeader($gang)) {
            throw new SoftException('You must be the regiment leader to purchase a compound.');
        }

        if ($this->members_required > count($gang->GetAllMembers())) {
            throw new SoftException('Your regiment doesn\'t have enough members to purchase this compound.');
        }

        $currentGangCompound = $gang->getGangCompoundType();
        if ($currentGangCompound) {
            throw new SoftException('You need to sell your current regiment compound before you purchase another.');
        }

        if ($gang->vault < $this->cost) {
            throw new SoftException('Your regiment doesn\'t have enough money in the vault to purchase this compound.');
        }

        $gang->SetAttribute('gang_compound_type_id', $this->id);
        $gang->RemoveFromAttribute('vault', $this->cost);

        throw new SuccessResult('You have successfully purchased a new compound for your regiment.');
    }

    public function sell($user_class)
    {
        $gang = $user_class->GetGang();

        if (!$user_class->IsGangLeader($gang)) {
            throw new SoftException('You must be the regiment leader to purchase a compound.');
        }

        $currentGangCompound = $gang->getGangCompoundType();
        if (!$currentGangCompound) {
            throw new SoftException('Your regiment doesn\'t have a compound to sell.');
        }

        $gang->SetAttribute('gang_compound_type_id', 0);
        $gang->AddToAttribute('vault', $this->cost);

        throw new SuccessResult('You have successfully sold your regiment compound, the original purchase price has been added to your vault.');
    }

    public function CalculateDailyFee()
    {
        return round($this->cost * 0.003);
    }

    public static function SGet($id)
    {
        return new GangCompoundType($id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'name',
            'members_required',
            'awake_boost',
            'cost',
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