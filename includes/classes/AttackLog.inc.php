<?php
error_reporting(0);
final class AttackTurn
{
    public $critical = 0;
    public $damage = 0;
}

final class AttackLog extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'attacks';

    public function __construct($id)
    {
        parent::__construct($id);
        $this->turns = unserialize(stripslashes($this->turns));
        if (!is_array($this->turns)) {
            $this->turns = [];
        }
        $this->speedcompare = stripslashes($this->speedcompare);
    }

    public static function GetAllAttacks(User $user, array $order = ['oby' => 'timestamp', 'sort' => 'DESC'])
    {
        $objs = [];

        if (!isset($order['oby']) || empty($order['oby'])) {
            $order['oby'] = 'timestamp';
        }

        if (!isset($order['sort']) || empty($order['sort'])) {
            $order['sort'] = 'DESC';
        }

        $query = 'SELECT `id`, attacker, defender, winner,exp,online,timestamp FROM `attacks` WHERE `attacker` = \'' . $user->id . '\' ORDER BY `' . $order['oby'] . '` ' . $order['sort'];

        return self::GetPaginationResults($query);
    }

    public static function GetAllDefenses(User $user, array $order = ['oby' => 'timestamp', 'sort' => 'DESC'])
    {
        $objs = [];

        if (!isset($order['oby']) || empty($order['oby'])) {
            $order['oby'] = 'timestamp';
        }

        if (!isset($order['sort']) || empty($order['sort'])) {
            $order['sort'] = 'DESC';
        }

        $query = 'SELECT `id`, attacker, defender, exp,winner,online,timestamp FROM `attacks` WHERE `defender` = \'' . $user->id . '\' ORDER BY `' . $order['oby'] . '` ' . $order['sort'];

        return self::GetPaginationResults($query);
    }

    public static function GetLast(User $attacker, User $defender, $timestamp)
    {
        $result = MySQL::GetSingle('select id from ' . self::$dataTable . ' where attacker=' . $attacker->id . ' and defender=' . $defender->id . ' and timestamp>=' . $timestamp . ' order by timestamp desc limit 1');
        if (is_numeric($result) && $result > 0) {
            return new self($result);
        }

        return null;
    }

    public function Save(User $winner, $money, $exp, $gang_money, $gang_exp)
    {
        $data = [
            'money' => $money,
            'exp' => $exp,
            'turns' => addslashes(serialize($this->turns)),
            'gangexp' => $gang_exp,
            'gangmoney' => $gang_money,
            'winner' => $winner->id,
        ];
        self::sUpdate(self::$dataTable, $data, ['id' => $this->id]);
    }

    public static function Create(User $a, User $b, $speed_attacker, $speed_defender)
    {
        $data = [
            'attacker' => $a->id,
            'attackerhp' => $a->hp,
            'speed_attack' => $speed_attacker,
            'speed_defender' => $speed_defender,
            'defender' => $b->id,
            'online' => time() - $b->lastactive < 900 ? 1 : 0,
            'defenderhp' => $b->hp,
            'timestamp' => time(),
            'maxhpdefender' => $b->GetMaxHP(),
            'maxhpattacker' => $a->GetMaxHP(),
            'turns' => serialize([]),
        ];
        $id = self::AddRecords($data, self::$dataTable);
        WeeklyMissions::AddAttacks($a->id);
        return new AttackLog($id);
    }

    public function AddNewTurn(User $attacker, User $defender, User $turn, $turns_attack, $yourhp, $theirhp)
    {
        $attackerWeapon = null;
        if ($attacker->GetWeapon()) {
            $attackerWeapon = $attacker->GetWeapon()->id;
        }

        $defenderWeapon = null;
        if ($defender->GetWeapon()) {
            $defenderWeapon = $defender->GetWeapon()->id;
        }

        $this->turns[] = [
            'attackerId' => $attacker->id,
            'attackerhp' => $yourhp,
            'attackerWeapon' => $attacker->GetWeapon()->id,
            'attackerArmor' => $attacker->eqarmor,
            'attackerstrength' => $attacker->GetModdedStrength(),
            'attackerdefense' => $attacker->GetModdedDefense(),
            'defenderId' => $defender->id,
            'defenderhp' => $theirhp,
            'defenderstrength' => $defender->GetModdedStrength(),
            'defenserdefense' => $defender->GetModdedDefense(),
            'defenderWeapon' => $defenderWeapon,
            'defenderArmor' => $defender->eqarmor,
            'attackTurn' => $turn->id,
            'turns_attack' => serialize($turns_attack),
        ];
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'attacker',
            'attackerhp',
            'defender',
            'online',
            'defenderhp',
            'timestamp',
            'speed_defender',
            'speed_attack',
            'turns',
            'winner',
            'save',
            'exp',
            'money',
            'maxhpdefender',
            'maxhpattacker',
            'gangexp',
            'gangmoney',
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
