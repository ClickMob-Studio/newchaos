<?php

final class KingGroup extends ARObject
{
    public static $idField = 'id';
    public static $dataTable = 'kinggroup';
    public static $START = 1;
    public static $FINISHED = 2;
    public static $ROLE_SINGER = 1;
    public static $ROLE_GUITAR = 2;
    public $time;
    public static $ROLE_DRUMMER = 3;

    public $elements;

    public function __construct($id)
    {
        parent::__construct($id);
        if ($this->element_1 != 0) {
            $this->elements[$this->rule_1] = $this->element_1;
        }
        if ($this->element_2 != 0) {
            $this->elements[$this->rule_2] = $this->element_2;
        }
        if ($this->element_3 != 0) {
            $this->elements[$this->rule_3] = $this->element_3;
        }
    }

    public function getUser($user_id)
    {
        switch ($user_id) {
                case $this->element_1:
                      return ['rule' => $this->rule_1,'ready' => $this->ready_1];
                case $this->element_2:
                      return ['rule' => $this->rule_2,'ready' => $this->ready_2];
                 case $this->element_3:
                      return ['rule' => $this->rule_3,'ready' => $this->ready_3];
            }
    }

    public function setReady($user_id)
    {
        switch ($user_id) {
                case $this->element_1:
                      $this->SetAttribute('ready_1', 1);
                    break;
                case $this->element_2:
                      $this->SetAttribute('ready_2', 1);
                    break;
                 case $this->element_3:
                      $this->SetAttribute('ready_3', 1);
                     break;
            }
    }

    public static function CreateGroup(User $user, KingList $king, $rule)
    {
        $ks = new KingPlayers($user->id);
        $boss = $ks->getBossStats($king->id);

        if ($boss['when'] != 0) {
            //throw new FailedResult('You already killed this boss');
        }
        $sql = "select rules,element,target from king_forgroup where element=$user->id";
        if (mysqli_num_rows(DBi::$conn->query($sql)) > 0) {
            self::removeForGroup($user);
        }

        if (self::searchGroup($user->id) != -1) {
            throw new FailedResult('You already are at a group');
        }
        $arr = [
                'formation' => time(),
                'target' => $king->id,
                'element_1' => $user->id,
                'rule_1' => $rule,
                'group_stat' => self::$START,
            ];
        self::AddRecords($arr, self::$dataTable);
    }

    public static function is_LFG(User $user, $city = -1)
    {
        $sql = "select id from king_forgroup where element=$user->id";
        if ($city != -1) {
            $king = KingList::GetKing($city);
            $sql .= " and target=$king->id";
        }
        if (mysqli_num_rows(DBi::$conn->query($sql)) > 0) {
            return true;
        }

        return false;
    }

    public static function addForGroup(User $user, KingList $king, $rule)
    {
        $ks = new KingPlayers($user->id);
        $boss = $ks->getBossStats($king->id);

        if ($boss['when'] != 0) {
            //throw new FailedResult('You already killed this boss');
        }
        if (self::is_LFG($user)) {
            self::removeForGroup($user);
        }

        $arr = [
                'target' => $king->id,
                'element' => $user->id,
                'rules' => $rule,
            ];
        self::AddRecords($arr, 'king_forgroup');
    }

    public static function removeForGroup(User $user)
    {
        $sql = "delete from king_forgroup where element=$user->id";

       DBi::$conn->query($sql);
    }

    public static function forGroup($city_id)
    {
        $king = KingList::GetKing($city_id);
        $sql = "select * from king_forgroup where target=$king->id";

        $res =DBi::$conn->query($sql);
        $arr = [];

        while ($row = mysqli_fetch_object($res)) {

            $user = UserFactory::getInstance()->getUser($row->element);
            if ($user->city != $city_id) {
                continue;
            }
            $row->user_id = $row->element;
            $row->level = $user->level;
            $row->name = $user->formattedname;
            $arr[] = $row;
        }

        return $arr;
    }

    public static function searchGroup($user)
    {
        $sql = 'select id from ' . self::$dataTable . " where (element_1=$user or element_2=$user or element_3=$user) and group_stat=" . self::$START;
        $rs =DBi::$conn->query($sql);
        if (mysqli_num_rows($rs) == 0) {
            return -1;
        }
        $row = mysqli_fetch_object($rs);

        return $row->id;
    }

    public function nmbr_elements()
    {
        $i = 0;
        if ($this->element_1 != 0) {
            ++$i;
        }
        if ($this->element_2 != 0) {
            ++$i;
        }
        if ($this->element_3 != 0) {
            ++$i;
        }

        return $i;
    }

    public function getOwnerRule($rule)
    {
        if ($this->rule_1 == $rule) {
            return $this->element_1;
        }

        if ($this->rule_2 == $rule) {
            return $this->element_2;
        }

        if ($this->rule_3 == $rule) {
            return $this->element_3;
        }

        return -1;
    }

    public function cleanGroups()
    {
        $time_limit =
            $sql = 'update table ' . self::$dataTable . ' set group_stat=' . self::$FINISHED . ' where
                    formation + 86400 <' . time() . '  and group_stat=' . self::$START;
       DBi::$conn->query($sql);
    }

    public function addElement(User $user, $rule)
    {
        if ($this->group_stat == self::$FINISHED) {
            throw new FailedResult('The time for this group already expired');
        }
        if ($this->searchGroup($user->id) != -1) {
            throw new FailedResult('This soldier is already on a group');
        }
        if ($this->nmbr_elements() == 3) {
            throw new FailedResult('This group is already full');
        }
        if ($this->getOwnerRule($rule) != -1) {
            throw new FailedResult('This rule already belong to another soldier');
        }
        $leader = UserFactory::getInstance()->getUser($this->element_1);
        $king = KingList::GetKing($this->target);
        if ($king->city_id != $user->city) {
            throw new FailedResult('The element is not at same city as target');
        }
        $this->removeForGroup($user);
        if ($this->element_2 == 0) {
            $this->SetAttribute('element_2', $user->id);
            $this->SetAttribute('rule_2', $rule);
        } else {
            $this->SetAttribute('element_3', $user->id);
            $this->SetAttribute('rule_3', $rule);
        }
    }

    public static function getLogs($user, $king)
    {
        $logs = [];
        $sql = 'select id from ' . self::$dataTable . " where (element_1=$user or element_2=$user or element_3=$user) and target=" . $king . '  LIMIT 0, 7 ';
        $rs = DBi::$conn->query($sql);
        while ($row = mysqli_fetch_array($rs)) {
            $sql = 'select id from kingattacks where id_group=' . $row['id'];
            $res = DBi::$conn->query($sql);
            while ($row1 = mysqli_fetch_array($res)) {
                $ls = new KingCombat($row1['id']);
                $ls->group = new self($row['id']);
                $logs[] = $ls;
            }
        }

        return $logs;
    }

    public function getGroupLog()
    {
        $logs = [];
        $sql = 'select id from kingattacks where id_group=' . $this->id;
        $res = DBi::$conn->query($sql);
        while ($row1 = mysqli_fetch_array($res)) {
            $ls = new KingCombat($row1['id']);
            $ls->group = new self($this->id);
            $logs[] = $ls;
        }

        return $logs;
    }

    public function getMissingGuards()
    {
        $boss = new KingList($this->target);

        if ($boss->num_guards == 0) {
            return 0;
        }

        $pl1 = new KingPlayers($this->element_1);
        $pl2 = new KingPlayers($this->element_2);
        $pl3 = new KingPlayers($this->element_3);
        $guards1 = $pl1->getBossStats($this->target);
        $guards1 = $guards1['guards'];
        $guards2 = $pl2->getBossStats($this->target);
        $guards2 = $guards2['guards'];
        $guards3 = $pl3->getBossStats($this->target);
        $guards3 = $guards3['guards'];

        return $boss->num_guards - min([$guards1,$guards2,$guards3]);
    }

    public function getTarget()
    {
        if ($this->getMissingGuards() > 0) {
            $goon = new KingList(300, $this->target);
            $boss_anterior = new KingList($this->target - 1);
            $boss = new KingList($this->target);
            $speed = $boss->speed - $boss_anterior->speed;
            $defense = $boss->defense - $boss_anterior->defense;
            $strengh = $boss->strengh - $boss_anterior->strengh;
            $spd_step = $speed / ($boss->num_guards + 1);
            $def_step = $defense / ($boss->num_guards + 1);
            $str_step = $strength / ($boss->num_guards + 1);
            $nm = $boss->num_guards + 1 - $this->getMissingGuards();
            $goon->hp = $boss->hp;

            $goon->defense = $boss_anterior->defense + $def_step * $nm;
            $goon->strengh = $boss_anterior->strengh + $str_step * $nm;
            $goon->speed = $boss_anterior->speed + $spd_step * $nm;

            return $goon;
        }

        return new KingList($this->target);
    }

    public function makeCombat()
    {
        if ($this->ready_1 != 1 || $this->ready_2 != 1 || $this->ready_3 != 1) {
            throw new FailedResult('You must wait until everyone is ready');
        }
        try {
            KingCombat::makeCombat($this->id);
        } catch (Exception $e) {
            throw $e;
        }
        $this->setResetStat();
    }

    public function setResetStat()
    {
        self::sUpdate(self::$dataTable, ['ready_1' => 0,'ready_2' => 0,'ready_3' => 0], ['id' => $this->id]);
    }

    public function Cancel($grp)
    {
        self::sUpdate(self::$dataTable, ['group_stat' => self::$FINISHED], ['id' => $grp]);
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
                          'formation',
                          'target',
                          'element_1',
                          'rule_1',
                          'ready_1',
                          'element_2',
                          'rule_2',
                          'ready_2',
                          'element_3',
                          'rule_3',
                          'ready_3',
                          'group_stat',
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


