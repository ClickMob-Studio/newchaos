<?php

class BackAlleyBot extends CachedObject
{
    public static $dataTable = 'back_alley_bot';
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
        return new BackAlleyBot($id);
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    public function givePotentialItem()
    {
        $potentialItems = $this->getPotentialItems();

        $maxNumber = 10000;
        $currentNumber = 1;
        $rand = mt_rand($currentNumber, $maxNumber);

        foreach ($potentialItems as $itemId => $percent) {
            $triggerPoint = $maxNumber / 100 * $percent;
            $triggerPoint = $currentNumber + $triggerPoint;

            if ($rand <= $triggerPoint) {
                return $itemId;
            }

            $currentNumber = $triggerPoint + 1;
        }
    }

    public function getPotentialItems()
    {
        $potentialItems = array();

        if ($this->level == 1) {
            $potentialItems[Item::GetItemId('MEDPACK_NAME_V2')] = 50;
            $potentialItems[Item::GetItemId('BIGMEDPACK_NAME_V2')] = 50;
        }

        if ($this->level == 2) {
            $potentialItems[Item::GetItemId('MEDPACK_NAME_V2')] = 25;
            $potentialItems[Item::GetItemId('BIGMEDPACK_NAME_V2')] = 25;
            $potentialItems[Item::GetItemId('TESTOSTERONE_NAME_V2')] = 25;
            $potentialItems[Item::GetItemId('COM_FERTILIZER')] = 15;
            $potentialItems[Item::GetItemId('TRAINING_DUMMY_TOKEN_NAME')] = 10;
        }

        if ($this->level == 3) {
            $potentialItems[Item::GetItemId('MEDPACK_NAME_V2')] = 25;
            $potentialItems[Item::GetItemId('BIGMEDPACK_NAME_V2')] = 25;
            $potentialItems[Item::GetItemId('TESTOSTERONE_NAME_V2')] = 25;
            $potentialItems[Item::GetItemId('REFILL_SLOTS')] = 5;
            $potentialItems[Item::GetItemId('TRAINING_DUMMY_TOKEN_NAME')] = 5;
            $potentialItems[Item::GetItemId('APPLE_NAME')] = 10;
            $potentialItems[Item::GetItemId('NEW_YEAR_GOLD_GARLAND_NAME')] = 5;
        }

        if ($this->level == 4) {
            $potentialItems[Item::GetItemId('REFILL_SLOTS')] = 20;
            $potentialItems[Item::GetItemId('APPLE_NAME')] = 20;
            $potentialItems[Item::GetItemId('AWAKE_PILL_NAME')] = 20;
            $potentialItems[Item::GetItemId('MORPHINE_NAME_V2')] = 20;
            $potentialItems[Item::GetItemId('JAIL_KEY_NAME')] = 9;
            $potentialItems[Item::GetItemId('NEW_YEAR_GOLD_GARLAND_NAME')] = 1;
            $potentialItems[Item::GetItemId('CHRISTMAS_COOKIE_NAME')] = 5;
            $potentialItems[Item::GetItemId('TRAINING_DUMMY_TOKEN_NAME')] = 5;
        }

        if ($this->level == 5) {
            $potentialItems[Item::GetItemId('APPLE_NAME')] = 20;
            $potentialItems[Item::GetItemId('AWAKE_PILL_NAME')] = 20;
            $potentialItems[Item::GetItemId('MORPHINE_NAME_V2')] = 20;
            $potentialItems[Item::GetItemId('NEW_YEAR_GOLD_GARLAND_NAME')] = 1;
            $potentialItems[Item::GetItemId('JAIL_KEY_NAME')] = 9;
            $potentialItems[Item::GetItemId('CHRISTMAS_COOKIE_NAME')] = 10;
            $potentialItems[Item::GetItemId('NEW_YEAR_CHAMPAGNE_NAME')] = 5;
            $potentialItems[Item::GetItemId('TRAINING_DUMMY_TOKEN_NAME')] = 5;
            $potentialItems[Item::GetItemId('PERFUME_BOTTLE_NAME')] = 5;
            $potentialItems[Item::GetItemId('NCO_LOOT_CRATE_NAME')] = 5;
        }

        if ($this->level == 6) {
            $potentialItems[Item::GetItemId('AWAKE_PILL_NAME')] = 20;
            $potentialItems[Item::GetItemId('TRAINING_DUMMY_TOKEN_NAME')] = 10;
            $potentialItems[Item::GetItemId('CHRISTMAS_CANDY_CANE_NAME')] = 20;
            $potentialItems[Item::GetItemId('CHRISTMAS_COOKIE_NAME')] = 20;
            $potentialItems[Item::GetItemId('NEW_YEAR_CHAMPAGNE_NAME')] = 10;
            $potentialItems[Item::GetItemId('PERFUME_BOTTLE_NAME')] = 10;
            $potentialItems[Item::GetItemId('NCO_LOOT_CRATE_NAME')] = 10;
        }

        if ($this->level == 7) {
            $potentialItems[Item::GetItemId('AWAKE_PILL_NAME')] = 20;
            $potentialItems[Item::GetItemId('TRAINING_DUMMY_TOKEN_NAME')] = 10;
            $potentialItems[Item::GetItemId('CURE_VIAL_NAME')] = 20;
            $potentialItems[Item::GetItemId('FUNGAL_VIAL_NAME')] = 20;
            $potentialItems[Item::GetItemId('NEW_YEAR_CHAMPAGNE_NAME')] = 10;
            $potentialItems[Item::GetItemId('PERFUME_BOTTLE_NAME')] = 10;
            $potentialItems[Item::GetItemId('NCO_LOOT_CRATE_NAME')] = 10;
        }

        if ($this->level == 8) {
            $potentialItems[Item::GetItemId('AWAKE_PILL_NAME')] = 20;
            $potentialItems[Item::GetItemId('TRAINING_DUMMY_TOKEN_NAME')] = 10;
            $potentialItems[Item::GetItemId('CURE_VIAL_NAME')] = 10;
            $potentialItems[Item::GetItemId('FUNGAL_VIAL_NAME')] = 10;
            $potentialItems[Item::GetItemId('NEW_YEAR_CHAMPAGNE_NAME')] = 10;
            $potentialItems[Item::GetItemId('PERFUME_BOTTLE_NAME')] = 10;
            $potentialItems[Item::GetItemId('GYM_GREENS_PILL_NAME')] = 10;
            $potentialItems[Item::GetItemId('GYM_PROTEIN_BAR_NAME')] = 10;
            $potentialItems[Item::GetItemId('GYM_SUPER_PILLS_NAME')] = 10;
        }

        if ($this->level == 9) {
            $potentialItems[Item::GetItemId('AWAKE_PILL_NAME')] = 20;
            $potentialItems[Item::GetItemId('TRAINING_DUMMY_TOKEN_NAME')] = 10;
            $potentialItems[Item::GetItemId('CURE_VIAL_NAME')] = 20;
            $potentialItems[Item::GetItemId('FUNGAL_VIAL_NAME')] = 20;
            $potentialItems[Item::GetItemId('NEW_YEAR_COSMO_COCKTAIL_NAME')] = 20;
            $potentialItems[Item::GetItemId('DISK_NAME')] = 10;
        }

        if ($this->level == 10) {
            $potentialItems[Item::GetItemId('AWAKE_PILL_NAME')] = 20;
            $potentialItems[Item::GetItemId('CURE_VIAL_NAME')] = 20;
            $potentialItems[Item::GetItemId('FUNGAL_VIAL_NAME')] = 20;
            $potentialItems[Item::GetItemId('NEW_YEAR_COSMO_COCKTAIL_NAME')] = 10;
            $potentialItems[Item::GetItemId('DISK_NAME')] = 10;
            $potentialItems[Item::GetItemId('LOVE_POTION_NAME')] = 10;
            $potentialItems[Item::GetItemId('HI_RADIO')] = 5;
            $potentialItems[Item::GetItemId('HI_TV')] = 2;
            $potentialItems[Item::GetItemId('TRAINING_DUMMY_TOKEN_NAME')] = 3;
        }

        if ($this->level == 11) {
            $potentialItems[Item::GetItemId('AWAKE_PILL_NAME')] = 20;
            $potentialItems[Item::GetItemId('CURE_VIAL_NAME')] = 20;
            $potentialItems[Item::GetItemId('FUNGAL_VIAL_NAME')] = 20;
            $potentialItems[Item::GetItemId('NEW_YEAR_COSMO_COCKTAIL_NAME')] = 10;
            $potentialItems[Item::GetItemId('DISK_NAME')] = 10;
            $potentialItems[Item::GetItemId('LOVE_POTION_NAME')] = 10;
            $potentialItems[Item::GetItemId('HI_RADIO')] = 5;
            $potentialItems[Item::GetItemId('HI_TV')] = 2;
            $potentialItems[Item::GetItemId('TRAINING_DUMMY_TOKEN_NAME')] = 3;
        }

        return $potentialItems;
    }

    public function getSpecialItem()
    {
        if ($this->level == 1) {
            return Item::SGet(Item::GetItemId('MORPHINE_NAME_V2'));
        }

        if ($this->level == 2) {
            return Item::SGet(Item::GetItemId('JAIL_KEY_NAME'));
        }

        if ($this->level == 3) {
            return Item::SGet(Item::GetItemId('CHRISTMAS_COOKIE_NAME'));
        }

        if ($this->level == 4) {
            return Item::SGet(Item::GetItemId('NEW_YEAR_FIREWORK_NAME'));
        }

        if ($this->level == 5) {
            return Item::SGet(Item::GetItemId('NCO_LOOT_CRATE_NAME'));
        }

        if ($this->level == 6) {
            return Item::SGet(Item::GetItemId('TAG_CRATE_NAME'));
        }

        if ($this->level == 7) {
            return Item::SGet(Item::GetItemId('CURE_VIAL_NAME'));
        }

        if ($this->level == 8) {
            return Item::SGet(Item::GetItemId('PERFUME_BOTTLE_NAME'));
        }

        if ($this->level == 9) {
            return Item::SGet(Item::GetItemId('NEW_YEAR_COSMO_COCKTAIL_NAME'));
        }

        if ($this->level == 10) {
            return Item::SGet(Item::GetItemId('LOVE_POTION_NAME'));
        }

        if ($this->level == 11) {
            return Item::SGet(Item::GetItemId('POLICE_BADGE_NAME'));
        }
    }

    public function completeFight($user, $backAlleyBotUserLevel)
    {
        $totalUserHealth = $user->hp;
        $totalUserSpeed = $user->GetModdedSpeed();
        $totalUserDefense = $user->GetModdedDefense();
        $totalUserStrength = $user->GetModdedStrength();

        if ($backAlleyBotUserLevel) {
            $botStrength = $this->strength + (($this->strength / 100) * ($backAlleyBotUserLevel->level * 5));
            $botDefence = $this->defence + (($this->defence / 100) * ($backAlleyBotUserLevel->level * 5));
            $botSpeed = $this->speed + (($this->speed / 100) * ($backAlleyBotUserLevel->level * 5));
            $bossHp = $this->health + (($this->health / 100) * ($backAlleyBotUserLevel->level * 5));

        } else {
            $botStrength = $this->strength;
            $botDefence = $this->defence;
            $botSpeed = $this->speed;
            $bossHp = $this->health;
        }

        while ($totalUserHealth > 0 && $bossHp > 0) {
            $damage = $botStrength - $totalUserDefense;
            $damage = ($damage < 1) ? 1 : $damage;
            $damage = rand(1, $damage);

            if ($damage == 1) {
                if ($botSpeed < 100) {
                    $damage = rand(1, 3);
                } else if ($botSpeed < 500) {
                    $damage = rand(2, 6);
                } elseif ($botSpeed < 2500) {
                    $damage = rand(5, 25);
                } elseif ($botSpeed < 25000) {
                    $damage = rand(25, 100);
                } else {
                    $damage = rand(100, 200);
                }
            }

            $totalUserHealth = $totalUserHealth - $damage;

            if ($totalUserHealth > 0) {
                $damage = $totalUserStrength - $botDefence;
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
            // Won Fight
            $check = DBi::$conn->query("SELECT * FROM training_dummies_fights");
            if(mysqli_num_rows($check) > 0) {
                DBi::$conn->query("UPDATE training_dummies_fights SET fought = fought + 1");
            }else{
                DBi::$conn->query("INSERT INTO training_dummies_fights (fought) VALUES (1)");
            }
            return true;
        } else {
            // Lost Fight
            return false;
        }
    }


    protected static function GetDataTableFields()
    {
        return [
            'id',
            'name',
            'health',
            'level',
            'speed',
            'defense',
            'strength',
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