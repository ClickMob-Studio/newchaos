<?php

class GangTerritoryZoneBattle extends CachedObject
{
    public static $dataTable = 'gang_territory_zone_battle';
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
        return new GangTerritoryZoneBattle($id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    public static function create($gangTerritoryZone, $attackingGang)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert(self::$dataTable)
            ->values(
                [
                    'gang_territory_zone_id' => ':gang_territory_zone_id',
                    'attacking_gang_id' => ':attacking_gang_id',
                    'defending_gang_id' => ':defending_gang_id',
                    'time_started' => ':time_started',
                ]
            )
            ->setParameter('gang_territory_zone_id', $gangTerritoryZone->id)
            ->setParameter('attacking_gang_id', $attackingGang->id)
            ->setParameter('defending_gang_id', $gangTerritoryZone->owned_by_gang_id)
            ->setParameter('time_started', time())
        ;
        $queryBuilder->execute();
    }

    public function joinBattle($user, $spot)
    {
        if ($user->gang == $this->getAttackingGang()->id) {
            $validSpots = array(
                'strength_attacker',
                'defense_attacker',
                'speed_attacker'
            );
        } else {
            $validSpots = array(
                'strength_defender',
                'defense_defender',
                'speed_defender'
            );
        }

        if ($user->getActiveGangTerritoryZoneBattle()) {
            throw new SoftException('You can\'t join another regiment territory battle until your current one has taken place.');
        }

        if ($this->is_complete) {
            throw new SoftException('This battle has already taken place.');
        }

        if (!in_array($spot, $validSpots)) {
            throw new SoftException('Please ensure you are trying to fill a valid spot.');
        }

        if ($spot == 'strength_attacker') {
            if ($this->getStrengthAttackingUser()) {
                throw new SoftException('Someone has already occupied the spot your trying to fill.');
            } else {
                $this->SetAttribute('strength_attacking_user_id', $user->id);
            }
        }
        if ($spot == 'defense_attacker') {
            if ($this->getDefenseAttackingUser()) {
                throw new SoftException('Someone has already occupied the spot your trying to fill.');
            } else {
                $this->SetAttribute('defense_attacking_user_id', $user->id);
            }
        }
        if ($spot == 'speed_attacker') {
            if ($this->getSpeedAttackingUser()) {
                throw new SoftException('Someone has already occupied the spot your trying to fill.');
            } else {
                $this->SetAttribute('speed_attacking_user_id', $user->id);
            }
        }

        if ($spot == 'strength_defender') {
            if ($this->getStrengthDefendingUser()) {
                throw new SoftException('Someone has already occupied the spot your trying to fill.');
            } else {
                $this->SetAttribute('strength_defending_user_id', $user->id);
            }
        }
        if ($spot == 'defense_defender') {
            if ($this->getDefenseDefendingUser()) {
                throw new SoftException('Someone has already occupied the spot your trying to fill.');
            } else {
                $this->SetAttribute('defense_defending_user_id', $user->id);
            }
        }
        if ($spot == 'speed_defender') {
            if ($this->getSpeedDefendingUser()) {
                throw new SoftException('Someone has already occupied the spot your trying to fill.');
            } else {
                $this->SetAttribute('speed_defending_user_id', $user->id);
            }
        }

        throw new SuccessResult('You have successfully joined the battle. Ensure your in the correct city for the territory when the battle takes place otherwise you will be unavailable to fight');
    }

    public function getGangTerritoryZone()
    {
        return new GangTerritoryZone($this->gang_territory_zone_id);
    }

    public function getAttackingGang()
    {
        return new Gang($this->attacking_gang_id);
    }

    public function getDefendingGang()
    {
        return new Gang($this->defending_gang_id);
    }

    public function getWinningGang()
    {
        return new Gang($this->winning_gang_id);
    }

    public function getStrengthDefendingUser()
    {
        if ($this->strength_defending_user_id) {
            return UserFactory::getInstance()->getUser($this->strength_defending_user_id);
        }

        return null;
    }

    public function getDefenseDefendingUser()
    {
        if ($this->defense_defending_user_id) {
            return UserFactory::getInstance()->getUser($this->defense_defending_user_id);
        }

        return null;
    }

    public function getSpeedDefendingUser()
    {
        if ($this->speed_defending_user_id) {
            return UserFactory::getInstance()->getUser($this->speed_defending_user_id);
        }

        return null;
    }

    public function getStrengthAttackingUser()
    {
        if ($this->strength_attacking_user_id) {
            return UserFactory::getInstance()->getUser($this->strength_attacking_user_id);
        }

        return null;
    }

    public function getDefenseAttackingUser()
    {
        if ($this->defense_attacking_user_id) {
            return UserFactory::getInstance()->getUser($this->defense_attacking_user_id);
        }

        return null;
    }

    public function getSpeedAttackingUser()
    {
        if ($this->speed_attacking_user_id) {
            return UserFactory::getInstance()->getUser($this->speed_attacking_user_id);
        }

        return null;
    }

    public function getGangTerritoryZoneBattleLogs()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('gang_territory_zone_battle_log')
            ->where('gang_territory_zone_battle_id = :gang_territory_zone_battle_id')
            ->setParameter('gang_territory_zone_battle_id', $this->id)
        ;
        $result = $queryBuilder->execute()->fetchAll();

        $gangTerritoryZoneBattleLogs = array();
        foreach ($result as $res) {
            $gangTerritoryZoneBattleLogs[] = new GangTerritoryZoneBattleLog($res['id']);
        }

        return $gangTerritoryZoneBattleLogs;
    }

    public function getBattleInitiationTime()
    {
        // Battles take place 30 minutes after they are started
        $seconds = 30 * 60;

        return $this->time_started + $seconds;
    }

    public function getBattleInitiationTimeForDisplay()
    {
        $time = $this->getBattleInitiationTime() - time();

        return number_format(($time / 60), 0) . ' minutes until battle';
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'gang_territory_zone_id',
            'attacking_gang_id',
            'defending_gang_id',
            'strength_defending_user_id',
            'is_strength_defending_user',
            'defense_defending_user_id',
            'is_defense_defending_user',
            'speed_defending_user_id',
            'is_speed_defending_user',
            'strength_attacking_user_id',
            'is_strength_attacking_user',
            'defense_attacking_user_id',
            'is_defense_attacking_user',
            'speed_attacking_user_id',
            'is_speed_attacking_user',
            'attacking_total_stats',
            'defending_total_stats',
            'time_started',
            'winning_gang_id',
            'is_complete',
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