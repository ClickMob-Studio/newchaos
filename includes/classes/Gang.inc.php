<?php
class Gang extends BaseObject
{
    const ANY_RESET_LOG = 'yes,any :)';
    public static $idField = 'id';
    public static $dataTable = 'gangs';

    public function __construct($id)
    {
        if ($id == 0 || $id == '' || strlen($id) == 0) {
            // Default
            $this->id = 0;
            $this->name = 'No Regiment';
            $this->leader = 'Mr.T';
            $this->description = 'Unaffiliated soldier.';
        } else {
            parent::__construct($id);
            $this->UpdateMemberCount();
            if($this->banner != ''){
                $this->formattedname = '<a href=\'viewgang.php?id=' . $this->id . '\'><img style="max-width:200px; max-height:50px" src="' . $this->banner.'"> </a>';
            }else {
                $this->formattedname = '<a href=\'viewgang.php?id=' . $this->id . '\'>' . $this->name . '</a>';
            }
            $this->banner = $this->banner;
            $this->updatelvl();
            $this->maxexp = User::GetNeededXPForLevel($this->level);
            $this->maxexpold = User::GetNeededXPForLevel($this->level);
            $this->exppercent = ($this->exp == 0) ? 0 : floor((($this->exp) / ($this->maxexp)) * 100);
            $this->formattedexp = ($this->exp) . ' / ' . ($this->maxexp) . ' [' . $this->exppercent . '%]';
        }
    }

    public function __destruct()
    {
    }

    /*
     * Retrieve the leader of the Gang
     *
     * @return User
     */
    public function getGangLeader()
    {
        return UserFactory::getInstance()->getUser(User::GetFromUsername($this->leader));
    }

    /*
     * Retrieve the all gangarmory for the Gang
     *
     * @return array
     */
    public function getGangArmories()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id, itemid, count(itemid) qu, quantity, gangid, borrowerid, borrowed_to_user_id')
            ->from('gangarmory')
            ->where('gangid = :gang_id')
            ->groupBy('itemid')
            ->setParameter('gang_id', $this->id)
        ;
        $gangArmories = $queryBuilder->execute()->fetchAll();

        foreach ($gangArmories as $key => $gangArmory) {
            $gangArmories[$key]['item'] = new Item($gangArmory['itemid']);

            if (isset($gangArmory['borrowed_to_user_id']) && $gangArmory['borrowed_to_user_id']) {
                $borrowedToUser = UserFactory::getInstance()->getUser($gangArmory['borrowed_to_user_id']);
                $gangArmories[$key]['borrowed_to_user'] = $borrowedToUser;
            }
        }

        return $gangArmories;
    }
    /*
        * Retrieve the all gangarmory for the Gang
        *
        * @return array
        */
    public function getGangNonLoanedArmories()
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id, itemid, count(itemid) as qu, gangid, borrowerid, borrowed_to_user_id')
            ->from('gangarmory')
            ->where('gangid = :gang_id')
            ->andWhere('borrowed_to_user_id = 0')
            ->GroupBy('itemid')
            ->setParameter('gang_id', $this->id)
        ;
        $gangArmories = $queryBuilder->execute()->fetchAll();

        foreach ($gangArmories as $key => $gangArmory) {
            $gangArmories[$key]['item'] = new Item($gangArmory['itemid']);

            if (isset($gangArmory['borrowed_to_user_id']) && $gangArmory['borrowed_to_user_id']) {
                $borrowedToUser = UserFactory::getInstance()->getUser($gangArmory['borrowed_to_user_id']);
                $gangArmories[$key]['borrowed_to_user'] = $borrowedToUser;
            }
        }

        return $gangArmories;
    }

    /*
     * Get the GangArmory has an Item in the GangArmory
     *
     * @param integer $itemId
     *
     * @return array
     */
    public function getGangArmoryForItem(int $itemId)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id, itemid, gangid, borrowerid, borrowed_to_user_id')
            ->from('gangarmory')
            ->where('gangid = :gang_id')
            ->setParameter('gang_id', $this->id)
            ->andWhere('itemid = :item_id')
            ->setParameter('item_id', $itemId)
            ->andWhere('(borrowed_to_user_id IS NULL OR borrowed_to_user_id = 0)')
            ->setMaxResults(1)
        ;
        $gangArmoryForItem = $queryBuilder->execute()->fetch();

        return $gangArmoryForItem;
    }

    public function updatelvl()
    {
        if ($this->exp > User::GetNeededXPForLevel($this->level)) {
            $this->SetAttribute('exp', $this->exp - User::GetNeededXPForLevel($this->level));
            $this->AddToAttribute('level', 1);
        }
    }

    public static function RemoveFromArmory(User $user, Item $item, $quantity = 1)
    {
        if ($quantity <= 0) {
            throw new SoftException(ITEM_INVALID_QTY);
        } elseif ($user->IsGangPermitted($user->GetGang(), 'ARMOR') === false) {
            throw new SoftException(NOT_AUTHORIZED);
        }
        $nbItems = Item::CountItemInArmory($item->id, $user->gang);
        if ($nbItems < $quantity) {
            throw new FailedResult(ITEM_NOT_ENOUGH_ARMORY_ITEMS);
        }
        DBi::$conn->query('DELETE FROM `gangarmory` WHERE `itemid`=\'' . $item->id . '\' and `gangid`=\'' . $user->gang . '\' AND `borrowered_to_user_id` = 0  LIMIT ' . $quantity);
        $affected = DBi::$conn -> affected_rows;
        if ($affected == 0) {
            throw new SoftException(ITEM_CANT_TAKEN);
        } elseif ($affected < $quantity) {
            $quantity = $affected;
        }
        Logs::SAddVaultLog($user->GetGang()->id, $user->id, '<b>' . $item->itemname . '(s)</b>', time(), 'WithdrawArm', $quantity);

        return true;
    }

    public function GetLowestMembers($limit = 200, $maxLevel = 500)
    {
        $res = parent::GetAll(['id'], 'grpgusers', '`gang`=\'' . $this->id . '\' AND `level` < \'' . $maxLevel . '\'', false, $limit, 'level', 'ASC');
        $users = [];
        foreach ($res as $entry) {
            $users[] = UserFactory::getInstance()->getUser($entry->id);
        }

        return $users;
    }

    public function GetAllMembers($pagingMode = true)
    {
        return parent::GetAllById('id', User::GetDataFields(), 'grpgusers', '`gang`=\'' . $this->id . '\'');
    }

    public static function SGetAllMembers($id, $fields = [])
    {
        if (empty($fields)) {
            $fields = User::GetDataFields();
        }

        return parent::GetAllById('id', $fields, 'grpgusers', '`gang`=\'' . $id . '\'');
    }

    public function GetAllMembersByXp($orderby = '', $sort = '')
    {
        if (empty($orderby)) {
            $orderby = 'level';
        }

        $objs = [];
        $query = 'SELECT grpgusers.*, IFNULL(gangperm.permorder,9999) as gang_rank FROM grpgusers  LEFT JOIN gangperm on grpgusers.gang = gangperm.id_gang and grpgusers.id_rank = gangperm.name_rank WHERE gang = \'' . $this->id . '\' GROUP BY grpgusers.id ORDER BY ' . $orderby . ' ' . $sort;

        /**Pagination **/
        if (self::$usePaging) {//If doing paging for records
            $query = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query); // insert SQL_CALC_FOUND_ROWS into query to find total rows into table
            Paginator::$recordsOnPage = 50;
            self::$paginator = new Paginator();
            self::$paginator->setRecordsPerPage(Paginator::$recordsOnPage);
            $res = DBi::$conn->query(self::$paginator->getLimitQuery($query));

            $totalRecords = MySQL::GetSingle('Select FOUND_ROWS()');

            self::$paginator->setQueryString();
            self::$paginator->setPageData('', $totalRecords, Paginator::$recordsOnPage, Paginator::$scrollPages, true, false, true);
        } else {
            $res = DBi::$conn->query($query);
        }

        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $obj->formattedTime = date('F d, Y g:i:sa', $obj->timestamp);
            $objs[] = $obj;
        }

        return $objs;
    }

    public function GetMemberCountPerRank()
    {
        $membersPerRank = ['All' => 0];
        $members = $this->GetAllMembers(false);
        foreach ($members as $member) {
            ++$membersPerRank[$member->id_rank];
            ++$membersPerRank['All'];
        }

        return $membersPerRank;
    }

    public function GetBalance($intUserId, $timestamp = null, $total = 0)
    {
        $arrWithdrawResult = [];

        $strSQL = 'SELECT action,action_type,action_value from vaultlog where';

        if ($timestamp != null) {
            $strSQL .= ' time > ' . $timestamp . ' and';
        }
        if ($total == 0) {
            $strSQL .= ' `reset_log` = 1 and';
        }

        $strSQL .= " userid='" . $intUserId . "' and gangid='" . $this->id . "' and action_type in ('WithdrawM','WithdrawP','DepositM','DepositP')
                           order by action_type";
        $res = DBi::$conn->query($strSQL);
        $intPoints = 0;
        $intMoney = 0;
        while ($arrLog = mysqli_fetch_array($res)) {
            switch ($arrLog['action_type']) {
                case 'WithdrawM':
                    $intMoney -= $arrLog['action_value'];
                    break;
                case 'WithdrawP':
                    $intPoints -= $arrLog['action_value'];
                    break;
                case 'DepositM':
                    $intMoney += $arrLog['action_value'];
                    break;
                case 'DepositP':
                    $intPoints += $arrLog['action_value'];
                    break;
            }
        }
        $arrResult['money'] = $intMoney;
        $arrResult['points'] = $intPoints;

        return $arrResult;
    }

    public function SumAllAttacks(&$members, $log = self::ANY_RESET_LOG, $timestamp = null)
    {
        $atks = $this->GetAllAttacks($log, $timestamp);

        foreach ($atks as $atk) {
            if (isset($members[$atk->attacker]) && $atk->attacker == $atk->winner) {
                $members[$atk->attacker]->atkMoneyContributions += $atk->moneywon;
                $members[$atk->attacker]->atkXPContributions += $atk->expwon;
            }
        }
        unset($atk, $atks);

        return $members;
    }

    public function SumAllDefends(&$members, $log = self::ANY_RESET_LOG, $timestamp = null)
    {
        $defs = $this->GetAllDefenses($log, $timestamp);
        foreach ($defs as $def) {
            if (isset($members[$def->defender]) && $def->defender == $def->winner) {
                $members[$def->defender]->defMoneyContributions += $def->moneywon;
                $members[$def->defender]->defXPContributions += $def->expwon;
            }
        }
        unset($def, $defs);

        return $members;
    }

    public function SumAllMugs(&$members, $timestamp = null)
    {
        $mugLogs = MugLog::GetAllForGang($this, $timestamp);
        foreach ($mugLogs as $mugLog) {
            if (isset($members[$mugLog->user])) {
                $members[$mugLog->user]->mugContributions += $mugLog->gangMoneyReward;
            }
        }
        unset($mugLog, $mugLogs);
    }

    public function SumAllCrimes(&$members, $timestamp = null)
    {
        $value = [];
        $res = DBi::$conn->query('select id, money, gangexp from gangcrimes');

        while ($arrLog = mysqli_fetch_array($res)) {
            $value[$arrLog['id']] = $arrLog;
        }

        if ($timestamp != null) {
            $crimes = GangCrime::GetCountByUsers($this->id, $timestamp, $timestamp);
        } else {
            $crimes = GangCrime::GetCountByUsers($this->id);
        }
        foreach ($crimes as $crime) {
            if (isset($members[$crime->userid])) {
                $members[$crime->userid]->gangcrimes += $crime->gangcrimes;
                $members[$crime->userid]->gangcrimemoney += $crime->gangcrimes * $value[$crime->crimeid]['money'];
                $members[$crime->userid]->gangexp += $crime->gangcrimes * $value[$crime->crimeid]['gangexp'];
            }
        }
        unset($crime, $crimes);
    }

    public function SumAllWarAttacks(&$members, $timestamp = null)
    {
        if ($timestamp != null) {
            $waratks = GangWarLog::GetAllForTime($this->id, $timestamp, $timestamp);
        } else {
            $waratks = GangWarLog::GetAllForTime($this->id);
        }

        foreach ($waratks as $waratk) {
            if (isset($members[$waratk->attackingUser]) && $waratk->attackingUser == $waratk->winningUser) {
                $members[$waratk->attackingUser]->warAtkPointContributions += $waratk->earnedWPoints;
            }
        }
        unset($waratk, $waratks);
    }

    public function GetXAllMembersWithContributions()
    {
        $members = $this->GetAllMembers();

        $timestamp = time() - (7 * DAY_SEC);
        if ($this->lastreset > $timestamp) {
            $timestamp = $this->lastreset;
        }

        foreach ($members as $strkey => $arrMember) {
            $members[$strkey]->gangcrimes = 0;
            $members[$strkey]->gangcrimemoney = 0;

            $arrResult = $this->GetBalance($members[$strkey]->id, $timestamp);

            $members[$strkey]->CurrentMoney = $arrResult['money'];
            $members[$strkey]->CurrentPoints = $arrResult['points'];
        }
        unset($strkey, $arrMember);

        $this->SumAllAttacks($members, 1);
        $this->SumAllDefends($members, 1);
        $this->SumAllMugs($members);
        $this->SumAllCrimes($members, $timestamp);
        $this->SumAllWarAttacks($members, $timestamp);

        usort($members, 'Gang::ContributionsSorter');

        return $members;
    }

    public function GetXAllMembersWithContributionsFixed($timestamp)
    {
        $members = $this->GetAllMembers();

        foreach ($members as $strkey => $arrMember) {
            $members[$strkey]->gangcrimes = 0;
            $members[$strkey]->gangcrimemoney = 0;
            $arrResult = $this->GetBalance($members[$strkey]->id, $timestamp, 1);
            $members[$strkey]->CurrentMoney = $arrResult['money'];
            $members[$strkey]->CurrentPoints = $arrResult['points'];
        }
        unset($strkey, $arrMember);

        $this->SumAllAttacks($members, self::ANY_RESET_LOG, $timestamp);
        $this->SumAllDefends($members, self::ANY_RESET_LOG, $timestamp);
        $this->SumAllMugs($members, $timestamp);
        $this->SumAllCrimes($members, $timestamp);
        $this->SumAllWarAttacks($members, $timestamp);

        usort($members, 'Gang::ContributionsSorter');

        return $members;
    }

    public function GetAllMembersWithContributions($fixed = null, array $options = [])
    {
        if (empty($options['oby'])) {
            $options['oby'] = 'atk_xp';
            $options['sort'] = 'DESC';
        }

        $dataFields = [
            'gang_id',
            'user_id',
            'atk_money',
            'atk_xp',
            'def_money',
            'def_xp',
            'mug',
            'money',
            'points',
            'gangcrimes',
            'crime_money',
            'atk_point',
        ];

        $memberContributions = parent::GetAll($dataFields, ($fixed != null ? 'gang_member_contributions' : 'gang_member_contributions'), '`gang_id`=\'' . $this->id . '\'', false, false, $options['oby'], $options['sort']);

        foreach ($memberContributions as $id => $member) {
            $memberContributions[$id]->id = $member->user_id;
            $memberContributions[$id]->atkMoneyContributions = $member->atk_money;
            $memberContributions[$id]->atkXPContributions = $member->atk_xp;
            $memberContributions[$id]->defMoneyContributions = $member->def_money;
            $memberContributions[$id]->defXPContributions = $member->def_xp;
            $memberContributions[$id]->mugContributions = $member->mug;
            $memberContributions[$id]->CurrentMoney = $memberContributions[$id]->money;
            $memberContributions[$id]->CurrentPoints = $memberContributions[$id]->points;
        }

        return $memberContributions;
    }

    public function GetAllWarMembers()
    {
        $res = parent::GetAll(['User', 'originalGang', 'targetGang', 'GangWar'], 'gang_wars_members', '`originalGang` = ' . $this->id);
        $users = [];
        foreach ($res as $entry) {
            $entry->formattedName = User::SGetFormattedName($entry->User, 'Low');
            $users[] = $entry;
        }

        return $users;
    }

    public function AddItemToArmory($itemId, $quantity)
    {
        while ($quantity--) {
            parent::AddRecords(['itemid' => $itemId, 'gangid' => $this->id], 'gangarmory');
        }

        return true;
    }

    public static function GetAllGang()
    {
        return parent::GetAllById(self::GetIdentifierFieldName(), self::GetDataTableFields(), self::GetDataTable());
    }

    public static function GetAllList()
    {
        $gangs = parent::GetAll(['id', 'leader'], self::GetDataTable(), '', false, false, 'level', 'DESC');

        foreach ($gangs as $key => $gang) {
            $userId = User::GetFromUsername($gang->leader);
            $gangobj = new Gang($gang->id);
            $gangobj->leaderFormatedName = '';
            if ($userId) {
                $gangobj->leaderFormatedName = User::SGetFormattedName($userId);
            }
            $gangs[$key] = $gangobj;
        }

        return $gangs;
    }

    public static function ResetWOFPoints()
    {
        DBi::$conn->query('UPDATE `' . self::$dataTable . '` SET `AlltimeWOFPoints`=`AlltimeWOFPoints`+`WOFPoints`, `WOFPoints`= 0');
        if (DBi::$conn -> affected_rows == 0) {
            return false;
        }

        return true;
    }

    public static function GetSetByWOFPoints($limit = 10)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `id`,`name`, `tag`, `WOFPoints` FROM `' . self::$dataTable . '` ORDER BY `WOFPoints` DESC LIMIT ' . $limit);
        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function GetSetByAllTimeWOFPoints($limit = 10)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `id`,`name`, `tag`, `AlltimeWOFPoints` FROM `' . self::$dataTable . '` ORDER BY `AlltimeWOFPoints` DESC LIMIT ' . $limit);
        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function GetAllByName()
    {
        $res = DBi::$conn->query('SELECT `id`,`name`, `tag` FROM `' . self::$dataTable . '` ORDER BY `name` ASC');
        if (mysqli_num_rows($res) == 0) {
            return null;
        }
        $i = 0;
        $objs = [];
        while ($obj = mysqli_fetch_object($res)) {
            $objs[$i++] = $obj;
        }

        return $objs;
    }

    public static function GetRanks($gangid)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `name_rank` from `gangperm` WHERE `id_gang`=' . $gangid . ' GROUP BY name_rank');
        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $obj->rank = $obj->name_rank;
            $objs[] = $obj;
        }

        return $objs;
    }

    public function GetAllDefensesById($id, $resetLog = self::ANY_RESET_LOG, $timestamp = null)
    {
        $string = '';
        $l = 0;
        foreach ($id as $key => $id) {
            ++$l;
            $string .= ($l != 0 ? ' and ' : '') . ' (attacker=' . $id . ' or defender=' . $id . ' ) ';
        }
        $objs = [];

        if ($resetLog == self::ANY_RESET_LOG) {
            $query = 'SELECT `id`, `timestamp`, `gangid`, `attacker`, `defender`, `winner`, `gangidatt`, `expwon`, `moneywon`, `atkexp`, `atkmoney` from `ganglog` WHERE `gangid` = \'' . $this->id . '\'' . ($timestamp != null ? ' and timestamp >' . $timestamp . ' ' : '') . ' ORDER BY `timestamp` DESC';
        } else {
            $query = 'SELECT `id`, `timestamp`, `gangid`, `attacker`, `defender`, `winner`, `gangidatt`, `expwon`, `moneywon`, `atkexp`, `atkmoney` from `ganglog` WHERE `gangid` = \'' . $this->id . '\' AND `reset_log`=' . ($resetLog ? 1 : 0) . ' ORDER BY `timestamp` DESC';
        }

        /**Pagination **/
        if (self::$usePaging) {//If doing paging for records
            $query = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query); // insert SQL_CALC_FOUND_ROWS into query to find total rows into table
            self::$paginator = new Paginator();
            self::$paginator->setRecordsPerPage(Paginator::$recordsOnPage);
            $res = DBi::$conn->query(self::$paginator->getLimitQuery($query));

            $totalRecords = MySQL::GetSingle('Select FOUND_ROWS()');

            self::$paginator->setQueryString();
            self::$paginator->setPageData('', $totalRecords, Paginator::$recordsOnPage, Paginator::$scrollPages, true, false, true);
        } else {
            //$query .= ' LIMIT 200';
            $res = DBi::$conn->query($query);
        }

        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $obj->formattedTime = date('F d, Y g:i:sa', $obj->timestamp);
            $objs[] = $obj;
        }

        return $objs;
    }

    /**
     * Read func name.
     *
     * @param int $resetLog value for reset_log (either 1 or 0)
     *
     * @return array
     */
    public function GetAllDefenses($resetLog = self::ANY_RESET_LOG, $timestamp = null)
    {
        $objs = [];

        if ($resetLog == self::ANY_RESET_LOG) {
            $query = 'SELECT `id`, `timestamp`, `gangid`, `attacker`, `defender`, `winner`, `gangidatt`, `expwon`, `moneywon`, `atkexp`, `atkmoney`,`status` from `ganglog` WHERE `gangid` = \'' . $this->id . '\'' . ($timestamp != null ? ' and timestamp >' . $timestamp . ' ' : '') . ' ORDER BY `timestamp` DESC';
        } else {
            $query = 'SELECT `id`, `timestamp`, `gangid`, `attacker`, `defender`, `winner`, `gangidatt`, `expwon`, `moneywon`, `atkexp`, `atkmoney`,`status` from `ganglog` WHERE `gangid` = \'' . $this->id . '\' AND `reset_log`=' . ($resetLog ? 1 : 0) . ' ORDER BY `timestamp` DESC';
        }

        /**Pagination **/
        if (self::$usePaging) {//If doing paging for records
            $query = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query); // insert SQL_CALC_FOUND_ROWS into query to find total rows into table
            self::$paginator = new Paginator();
            self::$paginator->setRecordsPerPage(Paginator::$recordsOnPage);
            $res = DBi::$conn->query(self::$paginator->getLimitQuery($query));

            $totalRecords = MySQL::GetSingle('Select FOUND_ROWS()');

            self::$paginator->setQueryString();
            self::$paginator->setPageData('', $totalRecords, Paginator::$recordsOnPage, Paginator::$scrollPages, true, false, true);
        } else {
            //$query .= ' LIMIT 200';
            $res = DBi::$conn->query($query);
        }

        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $obj->formattedTime = date('F d, Y g:i:sa', $obj->timestamp);
            $objs[] = $obj;
        }

        return $objs;
    }

    /**
     * Read func name.
     *
     * @param int $resetLog value for reset_log (either 1 or 0)
     *
     * @return array
     */
    public function GetAllAttacks($resetLog = self::ANY_RESET_LOG, $timestamp = null)
    {
        $objs = [];

        if ($resetLog == self::ANY_RESET_LOG) {
            $query = 'SELECT `id`, `timestamp`, `gangid`, `attacker`, `defender`, `winner`, `gangidatt`, `expwon`, `moneywon`, `atkexp`, `atkmoney`,`status` from `ganglog` WHERE `gangidatt` = \'' . $this->id . '\'' . ($timestamp != null ? ' and timestamp >' . $timestamp . ' ' : '') . ' ORDER BY `timestamp` DESC';
        } else {
            $query = 'SELECT `id`, `timestamp`, `gangid`, `attacker`, `defender`, `winner`, `gangidatt`, `expwon`, `moneywon`, `atkexp`, `atkmoney`,`status` from `ganglog` WHERE `gangidatt` = \'' . $this->id . '\'' . ($timestamp != null ? ' and timestamp >' . $timestamp . ' ' : '') . ' AND `reset_log`=' . ($resetLog ? 1 : 0) . ' ORDER BY `timestamp` DESC';
        }

        /**Pagination **/
        if (self::$usePaging) {//If doing paging for records
            $query = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query); // insert SQL_CALC_FOUND_ROWS into query to find total rows into table
            self::$paginator = new Paginator();
            self::$paginator->setRecordsPerPage(Paginator::$recordsOnPage);
            $res = DBi::$conn->query(self::$paginator->getLimitQuery($query));

            $totalRecords = MySQL::GetSingle('Select FOUND_ROWS()');

            self::$paginator->setQueryString();
            self::$paginator->setPageData('', $totalRecords, Paginator::$recordsOnPage, Paginator::$scrollPages, true, false, true);
        } else {
            //$query .= ' LIMIT 200';
            $res = DBi::$conn->query($query);
        }

        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }
        while ($obj = mysqli_fetch_object($res)) {
            $obj->formattedTime = date('F d, Y g:i:sa', $obj->timestamp);
            $objs[] = $obj;
        }

        return $objs;
    }

    public function GetVaultLogs($page)
    {
        return Logs::sGetVaultLogs($this->id, $page);
    }

    public function GetMemberLogs()
    {
        return Logs::sGetGangMemberLogs($this->id);
    }

    public static function GetWithTemplate($gid)
    {
        $res = DBi::$conn->query('SELECT `id`,`name` FROM `gangs` WHERE `id`=' . $gid);
        $arr = mysqli_fetch_array($res);
        if (!$arr) {
            return '<i>None</i>';
        }

        return '<a href=\'viewgang.php?id=' . $arr['id'] . '\'>' . $arr['name'] . '</a>';
    }

    public function UpdateMemberCount()
    {
        $res = DBi::$conn->query('SELECT count(`id`) as total FROM `grpgusers` WHERE `gang`=\'' . $this->id . '\'');
        $arr = mysqli_fetch_array($res);
        $this->members = $arr['total'];

        return $this->members;
    }

    public static function Create($name, $tag, $leaderName)
    {
        DBi::$conn->query('INSERT INTO `gangs` (`name`, `tag`, `leader`) VALUES (\'' . $name . '\', \'' . $tag . '\', \'' . $leaderName . '\')');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException('Your gang could not be created because of an internal error. Please try again later.');
        }

        return DBi::$conn -> insert_id;
    }

    public static function GetUserInvites(User $user)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `User`, `invitedby`, `gangid`, `time` FROM `ganginvites` WHERE `User` = \'' . $user->id . '\'');
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function GetInvites($gid)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `User`, `invitedby`, `gangid`, `time` FROM `ganginvites` WHERE `gangid` = ' . $gid);
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function InviteUser($gid, User $inviter, $invitedId)
    {
        $user = UserFactory::getInstance()->getUser($invitedId);
        if ($user->IsInAGang() && $user->GetGang()->id == $gid) {
            throw new FailedResult('That user is already a label member.');
        } elseif (Gang::UserIsInvited($gid, $invitedId)) {
            throw new FailedResult('That user has already been invited.');
        }
        DBi::$conn->query('INSERT INTO `ganginvites` (`User`, `invitedby`, `gangid`, `time`) VALUES (\'' . $user->id . '\', \'' . $inviter->id . '\', \'' . $gid . '\', \'' . time() . '\')');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException('There was an error while inviting the user. Please try again later.');
        }

        return true;
    }

    public static function UserIsGangMember($uid, $gid)
    {
        $res = DBi::$conn->query('SELECT `gang` FROM `grpgusers` WHERE `id`=\'' . $uid . '\'');
        if (mysqli_num_rows($res) == 0) {
            return false;
        }
        $arr = mysqli_fetch_array($res);
        if ($arr['gang'] == $gid) {
            return true;
        }

        return false;
    }

    public static function UserIsGangMemberByName($username, $gid)
    {
        $query = 'SELECT `gang` FROM `grpgusers` WHERE `username`="' . $username . '"';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return false;
        }
        $arr = mysqli_fetch_array($res);
        if ($arr['gang'] == $gid) {
            return true;
        }

        return false;
    }

    public static function UserIsInvited($gid, $userId)
    {
        $res = DBi::$conn->query('SELECT `User` FROM `ganginvites` WHERE `User`="' . $userId . '" AND `gangid`=\'' . $gid . '\'');

        return mysqli_num_rows($res) > 0;
    }

    public static function DeleteOldInvites($nbDays = 3)
    {
        $query = 'DELETE FROM `ganginvites` WHERE `time` < ' . (time() - $nbDays * 86400);
        //echo $query;
        return DBi::$conn->query($query);
    }

    public static function NameExists($name, $id = null)
    {
        $query = 'SELECT `id` FROM `gangs` WHERE `name`=\'' . $name . '\'';
        if ($id !== null) {
            $query .= ' AND `id`!=' . $id;
        }
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) > 0) {
            return true;
        }

        return false;
    }

    public static function TagExists($tag, $id = null)
    {
        $query = 'SELECT `id` FROM `gangs` WHERE `tag`=\'' . $tag . '\'';
        if ($id !== null) {
            $query .= ' AND `id`!=' . $id;
        }
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) > 0) {
            return true;
        }

        return false;
    }

    public function GetRank()
    {
        $query = 'SELECT `rank` FROM `top1000gang` WHERE `gid`=\'' . $this->id . '\'';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return '>1000';
        }
        $obj = mysqli_fetch_object($res);

        return $obj->rank;
    }

    public static function sAddMembershipRequest()
    {
    }

    public function CountMembershipRequests()
    {
        $res = DBi::$conn->query('SELECT count(`id`) AS total FROM `joingang` WHERE `gangid`=\'' . $this->id . '\'');
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    public function GetMembershipRequests()
    {
        $objs = [];

        $query = 'SELECT `gangid`, `id`, `username`, `reason` FROM `joingang` WHERE `gangid`=\'' . $this->id . '\'';

        /**Pagination **/
        if (self::$usePaging) {//If doing paging for records
            $query = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query); // insert SQL_CALC_FOUND_ROWS into query to find total rows into table
            self::$paginator = new Paginator();
            self::$paginator->setRecordsPerPage(Paginator::$recordsOnPage);
            $res = DBi::$conn->query(self::$paginator->getLimitQuery($query));

            $totalRecords = MySQL::GetSingle('Select FOUND_ROWS()');

            self::$paginator->setQueryString();
            self::$paginator->setPageData('', $totalRecords, Paginator::$recordsOnPage, Paginator::$scrollPages, true, false, true);
        } else {
            $res = DBi::$conn->query($query);
        }

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public function GetExtMembershipRequests()
    {
        $requests = $this->GetMembershipRequests();
        foreach ($requests as $request) {
            $request->oUser = UserFactory::getInstance()->getUser($request->id);
        }

        return $requests;
    }

    public function SGetTagName($id)
    {
        try {
            $gang = new Gang($id);

            $colors[] = $gang->Color_1;
            $colors[] = $gang->Color_2;
            $colors[] = $gang->Color_3;
            $tag = User::ProcessTag($gang->tag, $colors);

            return '<a href="viewgang.php?id=' . $gang->id . '">[' . $tag . ']</a>';
        } catch (Exception $e) {
            return false;
        }
    }

    public function SGetPublicFormattedName($id)
    {
        try {
            if ($id <= 0) {
                return false;
            }

            $gang = new Gang($id);

            $colors[] = $gang->Color_1;
            $colors[] = $gang->Color_2;
            $colors[] = $gang->Color_3;
            $tag = User::ProcessTag($gang->tag, $colors);

            return '<a href=\'viewgang.php?id=' . $gang->id . '\'>[' . $tag . ']' . $gang->name . '</a>';
        } catch (Exception $e) {
            return false;
        }
    }

    public function GetPublicFormattedName()
    {
        return '<a href=\'viewgang.php?id=' . $this->id . '\'>' . $this->GetFormattedName() . '</a>';
    }

    public static function StaticGetPublicFormattedName(int $id)
    {
        $gang = new Gang($id);

        return $gang->GetPublicFormattedName();
    }

    public function GetFormattedName()
    {
        if($this->banner == '') {
            return '[' . $this->tag . '] ' . $this->name;
        }else{
            return '<img style="max-width:200px; max-height:50px;" src="'.$this->banner.'"/>';
        }
    }

    public function GetPrivateFormattedName()
    {
        return '<a href=\'gang.php\'>' . $this->GetFormattedName() . '</a>';
    }

    public function IsAtWar()
    {
        $wars = GangWar::GetAllWithGang($this);

        return count($wars) > 0;
    }

    public function IsAtWarWith(Gang $targetGang)
    {
        return GangWar::ExistsBetween($this->id, $targetGang->id);
    }

    /**
     * Function used to reset member contributions.
     */
    public function ResetMemberContribution()
    {
        //delete logs from ganglog
        //$query = 'DELETE FROM `ganglog` WHERE `gangidatt` = \''.$this->id.'\' OR `gangid` = \''.$this->id.'\'';
        //$res = DBi::$conn->query($query);
        $query = 'UPDATE `ganglog`
			SET `reset_log`=0
			WHERE (`gangidatt` = \'' . $this->id . '\'
				OR `gangid` = \'' . $this->id . '\')
			AND `reset_log` = 1';
        DBi::$conn->query($query);
        $query = 'UPDATE `vaultlog`
			SET `reset_log`=0
			WHERE  `gangid` = \'' . $this->id . '\'
			AND `reset_log` = 1';
        DBi::$conn->query($query);

        $this->SetAttribute('lastreset', time());

        //delete logs from temprary calculated results table
        $query = 'DELETE FROM `gang_member_contributions` WHERE `gang_id` = \'' . $this->id . '\'';
        $res = DBi::$conn->query($query);

        MugLog::ResetAllForGang($this);
    }

    /**
     *over riding the AddToAttribute so that check max point limit before adding the points.
     */
    public function AddToAttribute($attrName, $value, $max = null)
    {
        if ($attrName == 'points') {
            return $this->AddPoints($value);
        }

        return parent::AddToAttribute($attrName, $value, $max);
    }

    public function AddPoints($points)
    {
        $currentpoints = $this->points;

        if ($currentpoints + $points > GANG_MAX_POINTS) {
            $max = GANG_MAX_POINTS - $currentpoints;
            if ($max > 0 && $max <= $points) {
                $result = parent::AddToAttribute('points', $max);
            }
            throw new FailedResult(sprintf(GANG_POINTS_MAX_ERROR, number_format(GANG_POINTS_MAX_ERROR)), 'POINTS_ERR|' . (GANG_POINTS_MAX_ERROR - $currentpoints));
        }
        $result = parent::AddToAttribute('points', $points);

        return $result;
    }

    /**
     * @desc This function saves the welcome message of gang
     *
     * @params $subject String
     * @params $message String
     */
    public function SaveWelcomeMessage($activated, $subject, $message)
    {
        if (empty($subject)) {
            throw new FailedResult(GANG_SUBJECT_EMPTY);
        }
        if (empty($message)) {
            throw new FailedResult(GANG_MSG_EMPTY);
        }
        $data = ['welcomesubject' => Utility::SmartEscape($subject),
            'welcomemsg' => Utility::SmartEscape($message),
            'welcomemsgstatus' => (int) $activated,
        ];
        $idField = self::$idField;

        return self::sUpdate(self::GetDataTable(), $data, [$idField => $this->$idField]);
    }

    public function SaveMarketTax($gangmarkettax)
    {
        $this->SetAttribute('gangmarkettax', $gangmarkettax);
    }

    public static function CountJointAttacks($gangId)
    {
        $query = 'SELECT count(`gangidatt`) FROM ganglog WHERE gangidatt = \'' . $gangId . '\' AND `timestamp` > \'' . (time() - DAY_SEC) . '\' AND jointattack = 1';

        return MySQL::GetSingle($query);
    }

    public static function MemberFor($gangid, $userid)
    {
        if (empty($gangid)) {
            return 0;
        }
        $time = MySQL::GetSingle('SELECT time FROM userganglog WHERE gangid = \'' . $gangid . '\' AND userid = \'' . $userid . '\' ORDER BY time DESC LIMIT 1');

        return Utility::GetDaysPassedSince($time);
    }

    public static function XMemberFor($gangid, $userid)
    {
        if (empty($gangid)) {
            return 0;
        }
        $time = MySQL::GetSingle('SELECT time FROM userganglog WHERE gangid = \'' . $gangid . '\' AND userid = \'' . $userid . '\' ORDER BY time DESC LIMIT 1');

        return time() - $time;
    }

    public function sendMailToMembers($args = '')
    {
        if (is_array($args)) {
            $subject = $args[0];
            $text = $args[1];
        } elseif (is_numeric($args)) {
            switch ($args) {
                case 1:
                    $subject = sprintf(GANG_MASS_MAIL_200_SUB, $this->GetFormattedName());
                    $text = sprintf(GANG_MASS_MAIL_200_MSG, $this->GetFormattedName());
                    break;
                default:
                    return;
            }
        }

        $memebers = $this->GetAllMembers(false);

        foreach ($memebers as $member) {
            try {
                User::sSendPmail($member->id, ANONYMOUS_GUARD_ID, time(), addslashes($subject), addslashes($text), Pms::GANG_MASS);
            } catch (Exception $e) {
            }
        }

        $this->SetAttribute('mailsent', 1);

        return true;
    }

    public function getAttackingGangTerritoryZoneBattles()
    {
        $res = GangTerritoryZoneBattle::GetAll('(is_complete IS NULL OR is_complete = 0) AND attacking_gang_id = ' . $this->id);

        $attackingGangTerritoryZoneBattles = array();
        if ($res) {
            foreach ($res as $r) {
                $attackingGangTerritoryZoneBattles[] = new GangTerritoryZoneBattle($r->id);
            }
        }

        return $attackingGangTerritoryZoneBattles;
    }

    public function getDefendingGangTerritoryZoneBattles()
    {
        $res = GangTerritoryZoneBattle::GetAll('defending_gang_id = ' . $this->id);

        $defendingGangTerritoryZoneBattles = array();
        if ($res) {
            foreach ($res as $r) {
                $defendingGangTerritoryZoneBattles[] = new GangTerritoryZoneBattle($r->id);
            }
        }

        return $defendingGangTerritoryZoneBattles;
    }

    public function getGangCompoundType()
    {
        if ($this->gang_compound_type_id) {
            return new GangCompoundType($this->gang_compound_type_id);
        }

        return null;
    }

    public function getGangCompoundTypeAwakeBonus()
    {
        if ($this->getGangCompoundType()) {
            $awakeBoost = $this->getGangCompoundType()->awake_boost / count($this->GetAllMembers());

            return ceil($awakeBoost);
        }

        return 0;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'leader',
            'description',
            'name',
            'tag',
            'exp',
            'vault',
            'privateD',
            'gangtax',
            'points',
            'rank',
            'WOFPoints',
            'AlltimeWOFPoints',
            'memberfee',
            'welcomesubject',
            'welcomemsg',
            'welcomemsgstatus',
            'gangmarkettax',
            'Color_1',
            'Color_2',
            'Color_3',
            'level',
            'lastreset',
            'membertaxmoney',
            'banner',
            'house',
            'armoryFix',
            'vault_level',
            'gang_compound_type_id'
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

    private static function ContributionsSorter(stdClass $user1, stdClass $user2)
    {
        $total1 = $user1->atkMoneyContributions +
            $user1->atkXPContributions +
            $user1->defMoneyContributions +
            $user1->defXPContributions +
            $user1->mugContributions;
        $total2 = $user2->atkMoneyContributions +
            $user2->atkXPContributions +
            $user2->defMoneyContributions +
            $user2->defXPContributions +
            $user2->mugContributions;
        if ($total1 == $total2) {
            return 0;
        }

        return ($total1 > $total2) ? -1 : 1;
    }
}
