<?php

final class GangWarAttack extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'gang_wars_attacks';

    public function __construct(User $user, User $targetUser, GangWar $war)
    {
        $id = self::GetIdFromUserAndWar($user, $targetUser, $war);
        if ($id != 0) {
            parent::__construct($id);
        } else {
            $this->id = 0;
        }
    }

    public static function ExistsForUserAndWar(User $user, GangWar $war)
    {
        return parent::sCount(self::GetIdentifierFieldName(), self::GetDataTable(), ['User' => $user->id, 'GangWar' => $war->id]) > 0;
    }

    public function IsNull()
    {
        return $this->id === 0;
    }

    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable());
    }

    public static function DeleteAll()
    {
        return parent::sDelete(self::GetDataTable());
    }

    public static function ResetAll()
    {
        return parent::sUpdate(self::GetDataTable(), ['Attacks' => 0]);
    }

    public static function CountAll()
    {
        return parent::sCount(self::GetIdentifierFieldName(), self::GetDataTable());
    }

    public static function UserCanAttack(User $user, User $targetUser)
    {
        $userWars = parent::GetAll(['targetGang','GangWar'], 'gang_wars_members', '`User` = ' . $user->id);
        $targetUserWars = parent::GetAll(['originalGang','GangWar'], 'gang_wars_members', '`User` = ' . $targetUser->id);

        foreach ($userWars as $userWar) {
            foreach ($targetUserWars as $targetUserWar) {
                if ($userWar->targetGang == $targetUserWar->originalGang && $userWar->GangWar == $targetUserWar->GangWar) {
                    return true;
                }
            }
        }

        return false;
    }

    public static function NumberOfAttacks(User $attacker, User $defender)
    {
        $maxAttacks = GWAR_HIGH_MAX_ATTACKS_COUNT;
        if ($attacker->level - $defender->level >= GWAR_LOW_MAX_ATTACKS_DIFF) {
            $maxAttacks = GWAR_LOW_MAX_ATTACKS_COUNT;
        } elseif ($attacker->level - $defender->level >= GWAR_MEDIUM_MAX_ATTACKS_DIFF) {
            $maxAttacks = GWAR_MEDIUM_MAX_ATTACKS_COUNT;
        }

        return $maxAttacks;
    }

    public static function AttacksMade(User $attacker, User $defender, $gangwar)
    {
        $war = new GangWar($gangwar);
        if ($war == null) {
            return -1;
        }

        $warAttack = new GangWarAttack($attacker, $defender, $war);

        if ($warAttack->id == 0) {
            return 0;
        }

        return $warAttack->Attacks;
    }

    public static function Add(User $attackingUser, User $defendingUser, User $winningUser, User $losingUser)
    {
        if ($winningUser->IsAtWarWith($losingUser) === false) {
            throw new SoftException(ATK_GANG_WAR_ADD_FAILED_1);
        }
        $war = $winningUser->GetWarAgainst($losingUser);
        if ($war === null) {
            return false;
        }
        if ($war->HasEnded() === true) {
            throw new FailedResult(GANG_WAR_ALREADY_ENDED);
        }
        $warAttack = new GangWarAttack($attackingUser, $defendingUser, $war);
        //fail if user has hit maxattacks
        //calculate dofference of levels
        $difference = $attackingUser->level - $defendingUser->level;
        // Computing max attacks
        $maxAttacks = GWAR_HIGH_MAX_ATTACKS_COUNT;
        if ($difference >= GWAR_LOW_MAX_ATTACKS_DIFF) {
            $maxAttacks = GWAR_LOW_MAX_ATTACKS_COUNT;
        } elseif ($difference >= GWAR_MEDIUM_MAX_ATTACKS_DIFF) {
            $maxAttacks = GWAR_MEDIUM_MAX_ATTACKS_COUNT;
        }
        if ($warAttack->Attacks >= $maxAttacks) {
            return false;
        }
        if ($warAttack->IsNull()) {
            // We add a new counted attack.
            parent::AddRecords(['User' => $attackingUser->id, 'targetUser' => $defendingUser->id, 'GangWar' => $war->id, 'Attacks' => 1], self::$dataTable);

            return $war->CountAttack($attackingUser, $defendingUser, $winningUser, $losingUser);
        }



        // If everything is valid, we add a counted attack.
        $warAttack->AddToAttribute('Attacks', 1, $maxAttacks);

        return $war->CountAttack($attackingUser, $defendingUser, $winningUser, $losingUser);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'User',
            'targetUser',
            'GangWar',
            'Attacks',
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

    private static function GetIdFromUserAndWar(User $user, User $targetUser, GangWar $war)
    {
        $results = parent::GetAll([self::$idField], self::GetDataTable(), '`User` = ' . $user->id . ' AND `targetUser` = ' . $targetUser->id . ' AND `GangWar` = ' . $war->id);
        $idField = self::$idField;
        if (count($results) == 0) {
            return 0;
        }

        return $results[0]->$idField;
    }
}
