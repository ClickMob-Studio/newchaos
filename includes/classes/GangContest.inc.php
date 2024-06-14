<?php

    /**
     * discription: This class is used to manage contest placed by users.
     *
     * @author: Harish<harish282@gmail.com>
     * @name: Contest
     * @package: includes
     * @subpackage: classes
     * @final: Final
     * @access: Public
     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    class GangContest extends BaseObject
    {
        /** Define constatns for status **/
        const MAX_SPOT = -1;
        const MAX_DAYS = 7;
        const MAX_MONEY_FEE = 5000000;
        const MAX_POINTS_FEE = 5000;

        const AVAILABLE = 1;
        const FILLED = 2;
        const EXPIRED = 3;
        const CANCELED = 4;
        const DRAW = 5;

        public static $idField = 'id'; //id field
        public static $dataTable = 'gang_contest'; // table implemented
        public static $statusText = [
                                                self::AVAILABLE => HITLIST_AVAILABLE,
                                                self::EXPIRED => HITLIST_COMPLETED,
                                                self::CANCELED => COM_CANCELED,
                                                self::DRAW => COM_DRAW,
                                            ]; // Status text

        /**
         * Constructor.
         */
        public function __construct($id = null)
        {
            if ($id > 0) {
                parent::__construct($id);
                $this->realtitle = $this->title;
                $this->title = constant($this->title);
            }
        }

        /**
         * Funtions return all returns.
         *
         * @return array
         */
        public static function GetAll($where = '', array $options = [])
        {
            if (!empty($options['order'])) {
                $order = $options['order'];
            }
            if (!empty($options['dir'])) {
                $dir = $options['dir'];
            }

            $objs = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, 0, $order, $dir);

            foreach ($objs as $key => &$obj) {
                $obj->realtitle = $obj->title;
                $obj->title = constant($obj->title);
            }

            return $objs;
        }

        public static function GetContest($gangid, $status = self::AVAILABLE, $order = '', $dir = '')
        {
            if (empty($order)) {
                $order = 'created';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            return self::GetAll('gangid=' . $gangid . ' AND status=' . $status, ['order' => $order, 'dir' => $dir]);
        }

        public static function GetContestExpired($all = true, $gangid = 0, $order = '', $dir = '')
        {
            if (empty($order)) {
                $order = 'finished';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $where = 'finished <=' . time();

            if (!empty($gangid)) {
                $where .= ' AND gangid =' . $gangid;
            }
            if (!$all) {
                $where .= ' AND status IN (' . self::AVAILABLE . ',' . self::FILLED . ') ';
            }

            return self::GetAll($where, ['order' => $order, 'dir' => $dir]);
        }

        public static function GetLastContests($gangid = 0, $order = '', $dir = '')
        {
            if (empty($order)) {
                $order = 'finished';
            }
            if (empty($dir)) {
                $dir = 'DESC';
            }

            $where = 'finished <=' . time();

            if (!empty($gangid)) {
                $where .= ' AND gangid =' . $gangid;
            }

            $where .= ' AND status IN (' . self::EXPIRED . ',' . self::CANCELED . ',' . self::DRAW . ') ';

            return self::GetAll($where, ['order' => $order, 'dir' => $dir]);
        }

        public static function AddContest(User $user, $data)
        {
            $gang = $user->GetGang();
            if ($gang->leader != $user->username) {
                throw new FailedResult(GANG_CONTEST_NOT_PERMIT);
            }
            if (!Validation::IsInteger($data['reward_points']) && !Validation::IsInteger($data['reward_money']) && empty($data['reward_items'])) {
                throw new FailedResult(CONTEST_REWARD_ERR);
            }
            if (!empty($data['reward_points']) && (!Validation::IsInteger($data['reward_points']) || $data['reward_points'] < 0)) {
                throw new FailedResult(INVALID_POINT_INPUT);
            }
            if (!empty($data['reward_money']) && (!Validation::IsInteger($data['reward_money']) || $data['reward_money'] < 0)) {
                throw new FailedResult(INVALID_AMOUNT_INPUT);
            }
            if (!empty($data['spots']) && (!Validation::IsInteger($data['spots']) || $data['spots'] > self::MAX_SPOT)) {
                throw new FailedResult(CONTEST_SPOTS_ERR, self::MAX_SPOT);
            }
            if (empty($data['days']) || !Validation::IsInteger($data['days']) || $data['days'] > self::MAX_DAYS) {
                throw new FailedResult(CONTEST_DAYS_ERR);
            }
            if (!empty($data['reward_money']) && $gang->vault < $data['reward_money']) {
                throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
            }
            if (!empty($data['reward_points']) && $gang->points < $data['reward_points']) {
                throw new FailedResult(USER_NOT_ENOUGH_POINTS);
            }
            if (!empty($data['reward_money']) && !$gang->RemoveFromAttribute('vault', $data['reward_money'])) {
                throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
            }
            if (!empty($data['reward_points']) && !$gang->RemoveFromAttribute('points', $data['reward_points'])) {
                throw new FailedResult(USER_NOT_ENOUGH_POINTS);
            }
            $reward_items = '';
            $coma = '';

            if (is_array($data['reward_items'])) {
                foreach ($data['reward_items'] as $item => $qty) {
                    $awake = (int) $data['items'][$item];

                    if (!Validation::IsInteger($qty)) {
                        continue;
                    }
                    if ($item > 10000) {
                        try {
                            $rpShopItem = new PShopRP($item - 10000);
                            $rpShopItem->addToContest();
                            $reward_items .= $coma . $item . '|' . $qty;
                            $coma = ',';
                        } catch (Exception $e) {
                        }
                    } else {
                        try {
                            Gang::RemoveFromArmory($user, new Item($item), $qty);
                            $reward_items .= $coma . $item . '|' . $qty;
                            $coma = ',';
                        } catch (Exception $e) {
                            throw $e;
                        }
                    }
                }
            }

            $data = [
                        'userid' => $user->id,
                        'gangid' => $user->gang,
                        'title' => Utility::SmartEscape($data['title']),
                        'description' => Utility::SmartEscape($data['description']),
                        'entry_money' => $data['entry_money'],
                        'entry_points' => $data['entry_points'],
                        'reward_money' => $data['reward_money'],
                        'reward_points' => $data['reward_points'],
                        'reward_items' => $reward_items,
                        'spots' => $data['spots'],
                        'created' => time(),
                        'finished' => time() + (int) $data['days'] * DAY_SEC,
                        'status' => self::AVAILABLE,
                ];
            $title = constant($data['title']);
            $subject = sprintf(GANG_CONTEST_START_SUB, '<b>' . $title . '</b>');
            $message = sprintf(GANG_CONTEST_START_MSG, '<b>' . $title . '</b>');

            $user->GetGang()->sendMailToMembers([$subject, $message]);

            return self::AddRecords($data, self::GetDataTable());
        }

        public function CancelContest(User $user, $draw = false)
        {
            if ($user->GetGang()->leader != $user->username) {
                throw new FailedResult(NOT_AUTHORIZED);
            }
            if ($this->status != self::AVAILABLE && $this->status != self::FILLED) {
                throw new FailedResult(INVALID_ACTION);
            }
            try {
                if ($this->reward_money > 0) {
                    $user->GetGang()->AddToAttribute('vault', $this->reward_money);
                }
                if ($this->reward_points > 0) {
                    $user->GetGang()->AddPoints($this->reward_points);
                }
            } catch (Exception $e) {
            }

            $itemnames = '';
            $coma = '';
            if (!empty($this->reward_items)) {
                $items = explode(',', $this->reward_items);
                $rpStore = new RPStore();

                for ($i = 0, $count = count($items); $i < $count; ++$i) {
                    list($itemid, $itemqty, $itemawake) = explode('|', $items[$i]);

                    if ($itemid > 10000) {
                        $shopitem = new PShopRP($itemid - 10000);
                        $shopitem->DeleteFromPShop();

                        $itemnames .= $coma . $rpStore->getItemName($shopitem->pack);

                    //$user->
                    } else {
                        $user->GetGang()->AddItemToArmory($itemid, $itemqty);
                        $item = new Item($itemid);
                        $itemnames .= $coma . $item->itemname;
                        $coma = ', ';
                    }
                }
            }

            if (empty($itemnames)) {
                $itemnames = COM_NONE;
            }

            $user->Notify(sprintf(CONTEST_CANCELLED_OWNER, $this->title, $this->reward_money, $this->reward_points, $itemnames, $citynames), OFFICIAL_CONTEST);

            if ($draw) {
                $sub = sprintf(CONTEST_DRAW_SUB, $this->title);
                $msg = sprintf(CONTEST_DRAW_MSG, $this->title);

                $user->GetGang()->sendMailToMembers([$sub, $msg]);
            }

            return $this->SetAttributes(['status' => ($draw ? self::DRAW : self::CANCELED), 'finished' => time()]);
        }

        public static function FinishExpiredContest()
        {
            $contests = self::GetContestExpired(false);
            foreach ($contests as $contest) {
                self::FinishContest($contest);
            }
        }

        public static function FinishContest($contest)
        {
            //$winnerSpot = mt_rand(1, $contest->spots);
            $winners = GangContest::ChooseWinner($contest);
            if (empty($winners)) {
                $contest = new GangContest($contest->id);

                return $contest->CancelContest(UserFactory::getInstance()->getUser($contest->userid));
            }
            $i = 0;
            $winnerUsers = '';
            foreach ($winners as $winnerId => $stat) {
                if ($i++ == 0) {
                    $winnerUser = $winnerId;
                    if ($stat <= 0) {
                        $contest = new GangContest($contest->id);

                        return $contest->CancelContest(UserFactory::getInstance()->getUser($contest->userid));
                    }
                }

                $winnerUsers .= $winnerId . '|' . $stat . ',';

                if ($winners[$winnerUser] == $stat && $winnerUser != $winnerId) {
                    $contest = new GangContest($contest->id);
                    self::sUpdate(self::GetDataTable(), ['winners' => $winnerUsers], ['id' => $contest->id]);

                    return $contest->CancelContest(UserFactory::getInstance()->getUser($contest->userid), true);
                }
            }

            $winnerUserObj = UserFactory::getInstance()->getUser($winnerUser);

            try {
                if ($contest->reward_money > 0) {
                    $winnerUserObj->AddBankMoney($contest->reward_money);
                }
                if ($contest->reward_points > 0) {
                    $winnerUserObj->AddPoints($contest->reward_points);
                }
            } catch (Exception $e) {
            }

            $itemnames = '';
            $coma = ', ';
            $reward_items = $contest->reward_items;
            if (!empty($contest->reward_items)) {
                $items = explode(',', $contest->reward_items);
                $rpStore = new RPStore();
                $reward_items = '';

                for ($i = 0, $count = count($items); $i < $count; ++$i) {
                    list($itemid, $itemqty, $itemawake) = explode('|', $items[$i]);

                    if ($itemid > 10000) {
                        try {
                            $shopitem = new PShopRP($itemid - 10000);
                            $shopitem->Buy($winnerUserObj, 1, 'contest');
                        } catch (Exception $e) {
                            if (is_a($e, 'SuccessResult')) {
                                $itemnames .= $coma . $rpStore->getItemName($shopitem->pack);
                                $packObj = InventoryRP::GetPack($winnerUserObj, $shopitem->pack, unserialize($shopitem->details));
                                $reward_items .= $coma . (10000 + $packObj->id) . '|' . $itemqty;
                                $coma = ', ';
                            }
                        }

                        //$user->
                    } else {
                        $item = new Item($itemid);
                        if (10 < $itemawake) {
                            $itemawake = $itemawake - 10;
                        }

                        $winnerUserObj->AddItems($itemid, $itemqty, 0, $itemawake);
                        $itemnames .= $coma . $itemqty . ' ' . $item->itemname;
                        $reward_items .= $coma . $itemid . '|' . $itemqty;
                        $coma = ', ';
                    }
                }
            }

            if (empty($itemnames)) {
                $itemnames = '';
            }

            $sub = sprintf(CONTEST_WIN_SUB, $contest->title);
            $msg = sprintf(CONTEST_WIN_MSG, addslashes(User::SGetFormattedName($winnerUserObj->id, 'Low')), $contest->title, $contest->reward_money, $contest->reward_points, $itemnames, $citynames);

            $winnerUserObj->GetGang()->sendMailToMembers([$sub, $msg]);

            //$winnerUserObj->Notify(sprintf(CONTEST_YOU_WIN, $contest->title, addslashes(User::SGetFormattedName($ownerUser->id,'Low')), $contest->reward_money, $contest->reward_points, $itemnames, $citynames), OFFICIAL_CONTEST);

            $winnerUserObj->__destruct();

            //$ownerUser->Notify(sprintf(CONTEST_YOUR_FINISHED, $contest->title, addslashes(User::SGetFormattedName($winnerUserObj->id,'Low')), $entry_money, $entry_points), OFFICIAL_CONTEST);

            self::sUpdate(self::GetDataTable(), ['winners' => $winnerUsers, 'reward_items' => $reward_items, 'status' => self::EXPIRED], ['id' => $contest->id]);
        }

        public static function ChooseWinner($contest)
        {
            $from = $contest->created;
            $to = $contest->finished;
            $limit = 5;

            $winners = [];

            $sql = 'SELECT id FROM grpgusers WHERE gang = ' . $contest->gangid . '
                      and gangentrance <' . $contest->created;
            $gang_elements = [];
            $rs =DBi::$conn->query($sql);
            while ($row = mysqli_fetch_object($rs)) {
                $gang_elements[] = $row->id;
            }

            if (count($gang_elements) == 0) {
                return [];
            }

            switch ($contest->realtitle) {
                case 'GANG_CONTEST_GOAL_1':
                    //bset strength

                    $sql = 'SELECT user_id, SUM(value) as stat FROM `action_track_log` 
                        WHERE ( action = \'Train Strength\' OR action = \'Refill and Train Strength\' )
                        AND timestamp BETWEEN ' . $from . ' AND ' . $to . '
                        AND user_id IN ( ' . implode(',', $gang_elements) . ' )
                        GROUP BY user_id ORDER BY stat DESC LIMIT ' . $limit;

                    $result = DBi::$conn->query($sql);

                    while ($row = mysqli_fetch_object($result)) {
                        $winners[$row->user_id] = $row->stat;
                    }

                break;
                case 'GANG_CONTEST_GOAL_2':
                    //best defense

                     $sql = 'SELECT user_id, SUM(value) as stat FROM `action_track_log`
                        WHERE ( action = \'Train Defense\'  OR action = \'Refill and Train Defense\' )
                        AND timestamp BETWEEN ' . $from . ' AND ' . $to . '
                        AND user_id IN ( ' . implode(',', $gang_elements) . ' )
                        GROUP BY user_id ORDER BY stat DESC LIMIT ' . $limit;

                    $result = DBi::$conn->query($sql);

                    while ($row = mysqli_fetch_object($result)) {
                        $winners[$row->user_id] = $row->stat;
                    }
                break;
                case 'GANG_CONTEST_GOAL_3':
                    //best speed
                     $sql = 'SELECT user_id, SUM(value) as stat FROM `action_track_log`
                       WHERE ( action = \'Train Speed\' OR action = \'Refill and Train Speed\' )
                        AND timestamp BETWEEN ' . $from . ' AND ' . $to . '
                        AND user_id IN ( ' . implode(',', $gang_elements) . ' )
                        GROUP BY user_id ORDER BY stat DESC LIMIT ' . $limit;
                    $result = DBi::$conn->query($sql);

                    while ($row = mysqli_fetch_object($result)) {
                        $winners[$row->user_id] = $row->stat;
                    }
                break;
                case 'GANG_CONTEST_GOAL_4':
                    //best overall
                    $sql = 'SELECT user_id, SUM(value) as stat FROM `action_track_log`
                       WHERE action IN (\'Train Speed\', \'Train Defense\', \'Train Strength\',
                          \'Refill and Train Speed\', \'Refill and Train Defense\', \'Refill and Train Strength\')
                        AND timestamp BETWEEN ' . $from . ' AND ' . $to . '
                        AND user_id IN ( ' . implode(',', $gang_elements) . ' )
                        GROUP BY user_id ORDER BY stat DESC LIMIT ' . $limit;

                    $result = DBi::$conn->query($sql);

                    while ($row = mysqli_fetch_object($result)) {
                        $winners[$row->user_id] = $row->stat;
                    }
                break;
                case 'GANG_CONTEST_GOAL_5':
                    //Best War points
                    $gang = new Gang($contest->gangid);
                    $members = $gang->GetAllMembers();
                    $gang->SumAllWarAttacks($members, $from);
                    usort($members, 'GangContest::WarPointsSorter');

                    $i = 0;
                    foreach ($members as $member) {
                        if ($i++ > 5) {
                            break;
                        }

                        $winners[$member->id] = $member->warAtkPointContributions;
                    }

                break;
                case 'GANG_CONTEST_GOAL_6':
                    //Best XP contribution

                    $gang = new Gang($contest->gangid);
                    $members = $gang->GetAllMembers();

                    $gang->SumAllAttacks($members, Gang::ANY_RESET_LOG, $from);
                    $gang->SumAllDefends($members, Gang::ANY_RESET_LOG, $from);
                    $gang->SumAllCrimes($members, $from);

                    usort($members, 'GangContest::WarXPSorter');

                    $i = 0;
                    foreach ($members as $member) {
                        if ($i++ > 5) {
                            break;
                        }

                        $winners[$member->id] = $member->atkXPContributions + $member->defXPContributions + $member->gangexp;
                    }

                break;
                case 'GANG_CONTEST_GOAL_7':
                    //Best Mug money
                    $gang = new Gang($contest->gangid);
                    $members = $gang->GetAllMembers();
                    $gang->SumAllMugs($members, $from);

                    usort($members, 'GangContest::MugSorter');

                    $i = 0;
                    foreach ($members as $member) {
                        if ($i++ > 5) {
                            break;
                        }

                        $winners[$member->id] = $member->mugContributions;
                    }
                break;
            }

            return $winners;
        }

        /**
         * Function used to get the data table name which is implemented by class.
         *
         * @return string
         */
        protected static function GetDataTable()
        {
            return self::$dataTable;
        }

        /**
         * Returns the fields of table.
         *
         * @return array
         */
        protected static function GetDataTableFields()
        {
            return [
                self::$idField,
                'userid',
                'gangid',
                'title',
                'description',
                'entry_money',
                'entry_points',
                'reward_money',
                'reward_points',
                'reward_items',
                'reward_land',
                'spots',
                'winners',
                'created',
                'finished',
                'status',
            ];
        }

        /**
         * Returns the identifier field name.
         *
         * @return mixed
         */
        protected function GetIdentifierFieldName()
        {
            return self::$idField;
        }

        /**
         * Function returns the class name.
         *
         * @return string
         */
        protected function GetClassName()
        {
            return __CLASS__;
        }

        private static function WarPointsSorter(stdClass $user1, stdClass $user2)
        {
            $total1 = $user1->warAtkPointContributions;
            $total2 = $user2->warAtkPointContributions;
            if ($total1 == $total2) {
                return 0;
            }

            return ($total1 > $total2) ? -1 : 1;
        }

        private static function MugSorter(stdClass $user1, stdClass $user2)
        {
            $total1 = $user1->mugContributions;
            $total2 = $user2->mugContributions;
            if ($total1 == $total2) {
                return 0;
            }

            return ($total1 > $total2) ? -1 : 1;
        }

        private static function WarXPSorter(stdClass $user1, stdClass $user2)
        {
            $total1 = $user1->atkXPContributions + $user1->defXPContributions + $user1->gangexp;
            $total2 = $user2->atkXPContributions + $user2->defXPContributions + $user2->gangexp;

            if ($total1 == $total2) {
                return 0;
            }

            return ($total1 > $total2) ? -1 : 1;
        }
    }
