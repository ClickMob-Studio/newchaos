<?php
error_reporting(0);
final class KingCombat extends ARObject
{
    public static $idField = 'id';
    public static $dataTable = 'kingattacks';
    public $left;
    public $defeat;
    public $group;

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function Log(array $attacker, array $defender)
    {
        $attackStr .= sprintf(ATK_OPPONENT_HIT, $targetUser->formattedname, number_format($damage)) . ' ';
        $attackStr .= sprintf(ATK_WITH_STRENGTH, Utility::GetStatsAdj(Utility::GetPercDiff($attacker['str'], $defender['str']))) . ' ';
        if ($attacker['str'] >= $defender['str']) {
            $attackStr .= sprintf(ATK_YOUR_HIT_MODED_STRENGTH, Utility::GetStatsAdj(Utility::GetPercDiff($attacker['str'], $defender['str'])));
        } else {
            $attackStr .= sprintf(ATK_YOUR_HIT_NORMAL_STRENGTH, Utility::GetStatsAdj(Utility::GetPercDiff($attacker['str'], $defender['str'])));
        }

        $attackStr .= '<br>';

        return $attackStr;
    }

    public static function makeCombat($group)
    {
        $message = '';
        $group = new KingGroup($group);
        $king = $group->getTarget();
        $users = [];

        foreach ($group->elements as $role => $userid) {
            $users[$role] = UserFactory::getInstance()->getUser($userid);
        }

        $your_hp = $users[KingGroup::$ROLE_SINGER]->hp * 2 + 1;

        if ($king->currenthospital > time() && $king->id != 300) {
            self::AddRecords([
                'king' => ($king->id == 300 ? $king->bg : $king->id),
                'id_group' => $group->id,
                'guards' => ($king->id == 300 ? 1 : -1),
                'result' => 0,
                'attack_log' => addslashes('This king was at hospital when you attack him.'),
                'ptime' => time(),
                'bosshp' => $king->hp,
                'teamhp' => $your_hp,
                'totalbosshp' => $king->hp,
                'totalteamhp' => $your_hp,
            ], self::$dataTable);

            return;
        }
        try {
            $armorTypes = ['head', 'chest', 'legs', 'boots', 'gloves'];
            foreach ($users as $user) {
                if ($user->GetWeapon() === null) {
                    throw new FailedResult('The battle didn\'t occur: ' . $user->username . ' does not have a weapon equipped.');
                }
                if (!in_array($user->city, explode(',', $user->GetWeapon()->prison))) {
                    throw new FailedResult('The battle didn\'t occur: ' . $user->username . ' is not wielding a weapon from ' . CityName::get($user->city) . '.');
                }
                foreach ($armorTypes as $armorType) {
                    $armor = $user->getArmorForType($armorType);
                    if (!$armor) {
                        throw new FailedResult('The battle didn\'t occur: ' . $user->username . ' is not fully equipped with armor.');
                    }
                    if (!in_array($user->city, explode(',', $armor->prison))) {
                        throw new FailedResult('The battle didn\'t occur: ' . $user->username . ' is not fully equipped with armor from ' . CityName::get($user->city) . '.');
                    }
                }
                if ($user->nerve < $user->GetMaxNerve() || $user->hp < $user->GetMaxHP() || $user->hospital > time() || $user->jail > time()) {
                    throw new FailedResult("The battle didn't occur: All soldiers must have full nerve, HP and not be in hospital or jail.");
                }
            }
        } catch (Exception $e) {
            self::AddRecords([
                'king' => ($king->id == 300 ? $king->bg : $king->id),
                'id_group' => $group->id,
                'guards' => ($king->id == 300 ? 1 : -1),
                'result' => 0,
                'attack_log' => addslashes($e->getMessage()),
                'ptime' => time(),
                'bosshp' => $king->hp,
                'teamhp' => $your_hp,
                'totalbosshp' => $king->hp,
                'totalteamhp' => $your_hp,
            ], self::$dataTable);

            return;
        }
        $defense = $users[KingGroup::$ROLE_SINGER]->defense * ($users[KingGroup::$ROLE_SINGER]->GetArmor()->defense * .01 + 1);
        if ($users[KingGroup::$ROLE_SINGER]->cocktailsteroid > time()) {
            $defense += $users[KingGroup::$ROLE_SINGER]->bonusdefense;
        }

        $attack = $users[KingGroup::$ROLE_DRUMMER]->strength * ($users[KingGroup::$ROLE_DRUMMER]->GetWeapon()->offense * .01 + 1);
        $drugTaken = Drug::DrugTaken($users[KingGroup::$ROLE_DRUMMER], 'Generic Steroids');
        if (!empty($drugTaken)) {
            $strGain = [0, 15, 22, 28, 33, 37, 40, 42];
            $strBonus = (int) ($users[KingGroup::$ROLE_DRUMMER]->strength * $strGain[$drugTaken->qty]) / 100;
        }
        $attack += $strBonus;
        if ($users[KingGroup::$ROLE_DRUMMER]->cocktailsteroid > time()) {
            $attack += $users[KingGroup::$ROLE_DRUMMER]->bonusstrength;
        }

        $speed = $users[KingGroup::$ROLE_GUITAR]->speed * ($users[KingGroup::$ROLE_GUITAR]->GetSpeed()->speed * .01 + 1);
        $drugTaken = Drug::DrugTaken($users[KingGroup::$ROLE_GUITAR], 'Cocaine');
        if (!empty($drugTaken)) {
            $speedGain = [0, 30, 37, 43, 48, 52, 55, 57];
            $speed *= ($speedGain[$drugTaken->qty]) / 100;
        }

        if ($users[KingGroup::$ROLE_GUITAR]->cocktailsteroid > time()) {
            $speed += $users[KingGroup::$ROLE_GUITAR]->bonusspeed;
        }

        $st = [];
        $st['Boss'] = ['hp' => $king->hp, 'str' => $king->strengh, 'def' => $king->strengh, 'spd' => $king->speed, 'Name' => 'King'];
        $st['Team'] = ['hp' => $your_hp, 'str' => $attack, 'def' => $defense, 'spd' => $speed, 'Name' => 'Band'];
        $wait = 0;
        if ($st['Boss']['spd'] > $st['Team']['spd']) {
            $wait = 1;
        }

        if ($st['Team']['spd'] > $st['Boss']['spd']) {
            $message .= sprintf(ATK_OPPONENT_SPEED_1, Utility::GetStatsAdj(Utility::GetPercDiff($st['Team']['spd'], $st['Boss']['spd']))) . '<br>';
        } else {
            $message .= sprintf(ATK_OPPONENT_SPEED_2, Utility::GetStatsAdj(Utility::GetPercDiff($st['Team']['spd'], $st['Boss']['spd']))) . '<br>';
        }

        $str = $st['Team']['str'];
        $def = $st['Boss']['def'];
        $spd = $st['Team']['spd'];

        while ($st['Team']['hp'] > 0 && $st['Boss']['hp'] > 0) {
            for ($i = 0; $i < 2; ++$i) {
                if ($st['Team']['hp'] > 0 && $st['Boss']['hp'] > 0 ) {
                    $damage = $str - $def;
                    $damage = ($damage < 1) ? 1 : $damage;
                    $damage = rand(1, $damage);
                    if ($damage == 1) {
                        if (($spd) < 100) {
                            $damage = rand(1, 3);
                        } elseif (($spd) < 500) {
                            $damage = rand(2, 6);
                        } elseif (($spd) < 2500) {
                            $damage = rand(5, 25);
                        } elseif (($spd) < 25000) {
                            $damage = rand(25, 100);
                        } else {
                            $damage = rand(100, 200);
                        }
                    }
                    if ($wait) {
                        $wait = 0;
                    } elseif ($i == 0) {
                        $st['Boss']['hp'] -= $damage;
                        $messge .= self::Log($st['Team'], $st['Boss']);
                        if ($king->id == 300) {
                            $message .= 'Body Guard';
                        } else {
                            $message .= 'King';
                        }
                        $message .= ' Suffered ' . Utility::GetStatsAdj($damage) . ' damage<br>';
                    } else {
                        $st['Team']['hp'] -= $damage;
                        $messge .= self::Log($st['Boss'], $st['Team']);
                        $message .= 'Squad Suffered ' . Utility::GetStatsAdj($damage) . ' damage<br>';
                    }
                    $str = $st['Boss']['str'];
                    $def = $st['Team']['def'];
                    $spd = $st['Boss']['spd'];

                }
            }
        }

        if ($st['Team']['hp'] > $st['Boss']['hp']) {
            if ($st['Boss']['hp'] < 0) {
                $st['Boss']['hp'] = 0;
            }
            if ($st['Team']['hp'] < 0) {
                $st['Team']['hp'] = 0;
            }

            foreach ($users as $user) {
                $user->SetAttributes(['nerve' => 0, 'energy' => 0]);
                $ps = new KingPlayers($user->id);
                $psStats = $ps->getBossStats($king->id);
                if ($psStats['id'] != 0) {
                    // Do nothing - already killed
                } else {
                    if ($king->id == 300) {
                        $ps->GooseKilled($king->bg);
                        Event::Add($user->id, $king->won);
                    } else {
                        $ps->BossKilled($king->id);
                        Event::Add($user->id, $king->won);
                        Event::Add($user->id, 'You have earned ' . User::GetNeededXPForLevel($king->level + 1) . ' exp for defeating the king.');
                        $user->AddToAttribute('exp', User::GetNeededXPForLevel($king->level + 1));
                        switch ($king->level) {
                            case 1:
                            case 2:
                            case 3:
                                Event::Add($user->id, 'You have earned 3 RS days for defeating the king.');
                                $user->AddToAttribute('rmdays', 3);
                                break;
                        }
                    }

                }
            }
            self::AddRecords([
                'king' => ($king->id == 300 ? $king->bg : $king->id),
                'id_group' => $group->id,
                'guards' => ($king->id == 300 ? 1 : -1),
                'result' => 1,
                'attack_log' => addslashes($message),
                'ptime' => time(),
                'bosshp' => $st['Boss']['hp'],
                'teamhp' => $st['Team']['hp'],
                'totalbosshp' => $king->hp,
                'totalteamhp' => $your_hp,
            ], self::$dataTable);

            if ($king->id != 300) {
                $king->setAttribute('currenthospital', time() + $king->hospitaltime);
                KingGroup::Cancel($group->id);
            }
        } else {
            if ($st['Boss']['hp'] < 0) {
                $st['Boss']['hp'] = 0;
            }
            if ($st['Team']['hp'] < 0) {
                $st['Team']['hp'] = 0;
            }

            foreach ($users as $user) {
                $user->SetAttributes(['nerve' => 0, 'hp' => 0, 'energy' => 0]);
                $user->SetAttribute('jail', 600);
                Event::Add($user->id, $king->lost);
            }
            self::AddRecords([
                'king' => ($king->id == 300 ? $king->bg : $king->id),
                'id_group' => $group->id,
                'guards' => ($king->id == 300 ? 1 : -1),
                'result' => -1,
                'attack_log' => addslashes($message),
                'ptime' => time(),
                'bosshp' => $st['Boss']['hp'],
                'teamhp' => $st['Team']['hp'],
                'totalbosshp' => $king->hp,
                'totalteamhp' => $your_hp,
            ], self::$dataTable);
        }
    }

    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '', false, false, 'MinLevel', 'ASC');
    }

    public static function GetAllById()
    {
        return parent::GetAllById(self::GetIdentifierFieldName(), self::GetDataTableFields(), self::GetDataTable());
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'king',
            'id_group',
            'guards',
            'result',
            'attack_log',
            'ptime',
            'bosshp',
            'teamhp',
            'totalbosshp',
            'totalteamhp',
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

?>


