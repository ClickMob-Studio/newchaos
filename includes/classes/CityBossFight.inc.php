<?php

class CityBossFight extends CachedObject
{
    public static $dataTable = 'city_boss_fight';
    public static $idField = 'id';

    private $cityBossProfile ;
    private $organisedByUser;

    public function __construct($id)
    {
        parent::__construct($id);

        $this->cityBossProfile = $this->getCityBossProfile();
        $this->organisedByUser = $this->getOrganisedByUser();
    }

    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    public static function SGet($id)
    {
        return new CityBossFight($id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    public static function getAvailable()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('cbf.id')
            ->from('city_boss_fight', 'cbf')
            ->leftJoin('cbf', 'city_boss_profile', 'cbp', 'cbp.id = cbf.city_boss_profile_id')
            ->andWhere('(cbf.is_fight_complete IS NULL OR cbf.is_fight_complete = 0)')

        ;
        $result = $queryBuilder->execute()->fetchAll();

        $cityBossFights = array();
        foreach ($result as $res) {
            $cityBossFights[] = new CityBossFight($res['id']);
        }

        return $cityBossFights;
    }

    public static function getAvailableForCity(int $cityId)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('cbf.id')
            ->from('city_boss_fight', 'cbf')
            ->leftJoin('cbf', 'city_boss_profile', 'cbp', 'cbp.id = cbf.city_boss_profile_id')
            ->where('cbp.city_id = :city_id')
            ->setParameter('city_id', $cityId)
            ->andWhere('(cbf.is_fight_complete IS NULL OR cbf.is_fight_complete = 0)')

        ;
        $result = $queryBuilder->execute()->fetchAll();

        $cityBossFights = array();
        foreach ($result as $res) {
            $cityBossFights[] = new CityBossFight($res['id']);
        }

        return $cityBossFights;
    }

    public static function create(int $cityBossProfileId, $user)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->insert(self::$dataTable)
            ->values(
                [
                    'city_boss_profile_id' => ':city_boss_profile_id',
                    'organised_by_user_id' => ':organised_by_user_id'
                ]
            )
            ->setParameter('city_boss_profile_id', $cityBossProfileId)
            ->setParameter('organised_by_user_id', $user->id)
        ;

        return $queryBuilder->execute();
    }

    public static function delete(int $id)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->delete('city_boss_fight')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
        ;
    }

    public function getCityBossProfile()
    {
        if ($this->cityBossProfile) {
            return $this->cityBossProfile;
        }
        return new CityBossProfile($this->city_boss_profile_id);
    }

    public function getOrganisedByUser()
    {
        if ($this->organisedByUser) {
            return $this->organisedByUser;
        }
        return UserFactory::getInstance()->getUser($this->organised_by_user_id);
    }

    public function getCityBossFightUsers()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id')
            ->from('city_boss_fight_user')
            ->where('city_boss_fight_id = :city_boss_fight_id')
            ->setParameter('city_boss_fight_id', $this->id)
        ;
        $ids = $queryBuilder->execute()->fetchAll();

        $cityBossFightUsers = array();
        foreach ($ids as $id) {
            $cityBossFightUsers[] = new CityBossFightUser($id['id']);
        }

        return $cityBossFightUsers;
    }

    public function getAreUsersReady()
    {
        $cityBossFightUsers = $this->getCityBossFightUsers();

        if (count($cityBossFightUsers) < $this->getCityBossProfile()->fight_users_required) {
            return false;
        }

        foreach ($cityBossFightUsers as $cbfu) {
            if (!$cbfu->getIsUserReady()) {
                return false;
            }
        }

        return true;
    }

    public function completeFight()
    {
        $cityBossFightUsers = $this->getCityBossFightUsers();
        $cityBossProfile = $this->getCityBossProfile();

        $totalUserHealth = 0;
        $totalUserSpeed = 0;
        $totalUserDefense = 0;
        $totalUserStrength = 0;

        foreach ($cityBossFightUsers as $cityBossFightUser) {
            $cityBossFightUser = $cityBossFightUser->getUser();

            $totalUserHealth += $cityBossFightUser->hp;
            $totalUserSpeed += $cityBossFightUser->GetModdedSpeed();
            $totalUserDefense += $cityBossFightUser->GetModdedDefense();
            $totalUserStrength += $cityBossFightUser->GetModdedStrength();
        }

        $bossHp = $cityBossFightUser->health;

        while ($totalUserHealth > 0 && $bossHp > 0) {
            $damage = $cityBossProfile->strength - $totalUserDefense;
            $damage = ($damage < 1) ? 1 : $damage;
            $damage = rand(1, $damage);

            if ($damage == 1) {
                if ($cityBossProfile->speed < 100) {
                    $damage = rand(1, 3);
                } else if ($cityBossProfile->speed < 500) {
                    $damage = rand(2, 6);
                } elseif ($cityBossProfile->speed < 2500) {
                    $damage = rand(5, 25);
                } elseif ($cityBossProfile->speed < 25000) {
                    $damage = rand(25, 100);
                } else {
                    $damage = rand(100, 200);
                }
            }

            $totalUserHealth = $totalUserHealth - $damage;

            if ($totalUserHealth > 0) {
                $damage = $totalUserStrength - $cityBossProfile->defense;
                $damage = ($damage < 1) ? 1 : $damage;
                $damage = rand(1, $damage);
                if ($damage == 1) {
                    if ($totalUserSpeed < 100) {
                        $damage = rand(1, 3);
                    } elseif ($totalUserSpeed < 500) {
                        $damage = rand(2, 6);
                    } elseif ($totalUserSpeed < 2500) {
                        $damage = rand(5, 25);
                    } elseif ($totalUserSpeed < 25000) {
                        $damage = rand(25, 100);
                    } else {
                        $damage = rand(100, 200);
                    }
                }

                $bossHp = $bossHp - $damage;
            }
        }

        if ($bossHp <= 0) {
            $payoutItem = null;
            if ($cityBossProfile->payout_item_ids) {

                $itemChance = mt_rand(1, 100);
                // 80% chance you'll get an item
                if ($itemChance <= 80) {
                    $items = $cityBossProfile->getItems();
                    $rand = shuffle($items);
                    $payoutItem = $items[$rand];
                }
            }

            $payoutMiningDrone = null;
            if ($cityBossProfile->payout_mining_drone_ids) {
                $miningChance = mt_rand(1, 100);
                // 75% chance you'll get a drone
                if ($miningChance <= 75) {
                    $miningDrones = $cityBossProfile->getMiningDrones();
                    $rand = shuffle($miningDrones);
                    $payoutMiningDrone = $miningDrones[$rand];
                }
            }

            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->update('city_boss_fight')
                ->set('is_fight_complete', 1)
                ->set('is_fight_won', 1)
                ->set('	time_completed', ':time_completed')
                ->setParameter('time_completed', time())
                ->set('payout_item_ids', ':payout_item_id')
                ->setParameter('payout_item_id', $payoutItem->id)
                ->set('payout_mining_drone_ids', ':payout_mining_drone_ids')
                ->setParameter('payout_mining_drone_ids', $payoutMiningDrone->id)
                ->set('points_payout', ':points_payout')
                ->setParameter('points_payout', $cityBossProfile->points_payout)
                ->set('exp_payout', ':exp_payout')
                ->setParameter('exp_payout', $cityBossProfile->exp_payout)
                ->set('dog_tag_payout', ':dog_tag_payout')
                ->setParameter('dog_tag_payout', $cityBossProfile->dog_tag_payout)
                ->where('id = :id')
                ->setParameter('id', $this->id)
                ->execute()
            ;

            foreach ($cityBossFightUsers as $cityBossFightUser) {
                $cityBossFightUser = $cityBossFightUser->getUser();
                DailyTasks::recordUserTaskAction( DailyTasks::USE_MAX_NERVE, $cityBossFightUser,1 );
                if ($payoutItem && $this->getOrganisedByUser()->id == $cityBossFightUser->id) {
                    $cityBossFightUser->AddItems($payoutItem->id, 1);
                }

                if ($payoutMiningDrone  && $this->getOrganisedByUser()->id == $cityBossFightUser->id) {
                    UserMiningDrone::create($cityBossFightUser->id, $payoutMiningDrone->id);
                }

                if ($cityBossProfile->points_payout) {
                    $cityBossFightUser->AddToAttribute('points', $cityBossProfile->points_payout);
                }

                if ($cityBossProfile->exp_payout) {
                    $exp = $cityBossProfile->exp_payout;

                    $itemSet = ItemSets::checkHasItemSetEquipped($cityBossFightUser);
                    if ($itemSet && isset($itemSet['set_name']) && $itemSet['set_name'] === 'ULTIMATE_SURVIVAL_SET') {
                        $exp = $exp + ($exp / 2);

                        Event::Add($cityBossFightUser->id, 'Due to wearing your Ultimate Survival set, you gained ' . number_format($exp, 0) . ' EXP from your last boss fight.');
                    }

                    $cityBossFightUser->AddToAttribute('exp', $exp);
                }

                if ($cityBossProfile->dog_tag_payout) {
                    $cityBossFightUser->AddToAttribute('dog_tags', $cityBossProfile->dog_tag_payout);
                }
                if($cityBossFightUser->level > 699 && $cityBossFightUser->securityLevel == 6){
                    $black = DBi::$conn->query('SELECT * FROM prestige7 WHERE userid = ' . $cityBossFightUser->id);

                    if (mysqli_num_rows($black)) {

                        DBi::$conn->query('UPDATE prestige7 SET boss = boss + 1 WHERE userid = ' . $cityBossFightUser->id . ' AND boss < 101');
                    } else {
                        DBi::$conn->query('INSERT INTO prestige7 (userid, boss) VALUES(' . $cityBossFightUser->id . ', 1)');
                    }
                }

                $cityBossFightUser->SetAttribute('energy', 0);
                $cityBossFightUser->performUserQuestAction('boss_fights', $cityBossProfile->id);

                UserBarracksRecord::recordAction('bossfight', $cityBossFightUser->id, 1);
                DailyTasks::recordUserTaskAction(DailyTasks::BOSS_FIGHTS, $cityBossFightUser, 1);

                BattlePass::addExp($cityBossFightUser->id, 100);
                $cityBossFightUser->addActivityPoint();

                Event::Add($cityBossFightUser->id, 'Your boss fight is now complete, <a href="boss_fights.php?action=results&id=' . $this->id . '">click here to view the results</a>.');
            }

            return true;
        } else {
            // Lost Fight
            foreach ($cityBossFightUsers as $cityBossFightUser) {
                $cityBossFightUser = $cityBossFightUser->getUser();

                $cityBossFightUser->SetAttribute('hp', 0);
                $cityBossFightUser->SetAttribute('energy', 0);

                $time = mt_rand(10,15);
                $ti = ($time * 60) + time();
                $cityBossFightUser->SetAttribute('hospital', $ti);

                Event::Add($cityBossFightUser->id, 'Your boss fight is now complete, <a href="boss_fights.php?action=results&id=' . $this->id . '">click here to view the results</a>.');
            }

            $queryBuilder = BaseObject::createQueryBuilder();
            $queryBuilder
                ->update('city_boss_fight')
                ->set('is_fight_complete', 1)
                ->set('time_completed', ':time_completed')
                ->setParameter('time_completed', time())
                ->where('id = :id')
                ->setParameter('id', $this->id)
                ->execute()
            ;

            return false;
        }
    }

    public function getItems()
    {
        $itemIds = explode(',', $this->payout_item_ids);
        $items = array();
        foreach ($itemIds as $itemId) {
            $items[] = new Item($itemId);
        }

        return $items;
    }

    public function getMiningDrones()
    {
        $miningDroneIds = explode(',', $this->payout_mining_drone_ids);
        $miningDrones = array();
        foreach ($miningDroneIds as $miningDroneId) {
            $miningDrones[] = new MiningDrone($miningDroneId);
        }

        return $miningDrones;
    }

    protected static function GetDataTableFields()
    {
        return [
            'id',
            'city_boss_profile_id',
            'is_fight_complete',
            'is_fight_won',
            'payout_item_ids',
            'points_payout',
            'exp_payout',
            'organised_by_user_id',
            'is_ready',
            'time_completed',
            'payout_mining_drone_ids',
            'dog_tag_payout',
            'is_auto_complete_set',
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