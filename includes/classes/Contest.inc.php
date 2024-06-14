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
    class Contest extends BaseObject
    {
        /** Define constatns for status **/
        const MAX_SPOT = 50;

        const MAX_DAYS = 14;

        const MAX_MONEY_FEE = 5000000;

        const MAX_POINTS_FEE = 5000;

        const AVAILABLE = 1;

        const FILLED = 2;

        const EXPIRED = 3;

        const CANCELED = 4;

        public static $idField = 'id'; //id field

        public static $dataTable = 'contest'; // table implemented

        public static $statusText = [
                                                self::AVAILABLE => HITLIST_AVAILABLE,

                                                self::EXPIRED => HITLIST_COMPLETED,

                                                self::CANCELED => COM_CANCELED,
                                            ]; // Status text

        /**
         * Constructor.
         */
        public function __construct($id = null)
        {
            if ($id > 0) {
                parent::__construct($id);
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

            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, 0, $order, $dir);
        }

        public static function GetContest($status = self::AVAILABLE, $order = '', $dir = '')
        {
            if (empty($order)) {
                $order = 'created';
            }

            if (empty($dir)) {
                $dir = 'DESC';
            }

            return self::GetAll('status=' . $status . ' AND finished >= ' . time(), ['order' => $order, 'dir' => $dir]);
        }

        public static function GetContestExpired($order = '', $dir = '')
        {
            if (empty($order)) {
                $order = 'finished';
            }

            if (empty($dir)) {
                $dir = 'DESC';
            }

            $where = 'finished <=' . time() . ' AND status IN (' . self::AVAILABLE . ',' . self::FILLED . ') ';

            return self::GetAll($where, ['order' => $order, 'dir' => $dir]);
        }

        public static function GetContestPosted(User $user, $order = '', $dir = '', array $filter = [])
        {
            if (empty($order)) {
                $order = 'created';
            }

            if (empty($dir)) {
                $dir = 'DESC';
            }

            $where = 'userid=' . $user->id . '';

            if (!empty($filter['status'])) {
                $where .= ' AND status IN (' . implode(',', $filter['status']) . ')';
            } else {
                $where .= ' AND status IN (' . self::AVAILABLE . ',' . self::FILLED . ')';
            }

            return self::GetAll($where, ['order' => $order, 'dir' => $dir]);
        }

        public static function GetContestSubscribed(User $user, $order = '', $dir = '', array $filter = [])
        {
            if (empty($order)) {
                $order = 'created';
            }

            if (empty($dir)) {
                $dir = 'DESC';
            }

            /*$contests = ContestUser::GetAllForUser($user->id);

            $cid = array();

            foreach($contests as $contest)

            {

                $cid[] = $contest->cid;

            }*/

            $where = 'id IN (SELECT cid FROM ' . ContestUser::GetDataTable() . ' WHERE userid = \'' . $user->id . '\') ';

            if (!empty($filter['status'])) {
                $where .= ' AND status IN (' . implode(',', $filter['status']) . ')';
            } else {
                $where .= ' AND status IN (' . self::AVAILABLE . ',' . self::FILLED . ')';
            }

            return self::GetAll($where, ['order' => $order, 'dir' => $dir]);
        }

        public static function AddContest(User $user, $data)
        {
            if (!$user->contest_permit && !$user->IsAdmin()) {
                throw new FailedResult(CONTEST_NOT_PERMIT);
            }
            if (!Validation::IsInteger($data['reward_points']) && !Validation::IsInteger($data['reward_money']) && empty($data['reward_items']) && empty($data['reward_lands'])) {
                throw new FailedResult(CONTEST_REWARD_ERR);
            }
            if (!empty($data['reward_points']) && (!Validation::IsInteger($data['reward_points']) || $data['reward_points'] < 0)) {
                throw new FailedResult(INVALID_POINT_INPUT);
            }
            if (!empty($data['reward_money']) && (!Validation::IsInteger($data['reward_money']) || $data['reward_money'] < 0)) {
                throw new FailedResult(INVALID_AMOUNT_INPUT);
            }
            /*if(!Validation::IsInteger($data['entry_points']) && !Validation::IsInteger($data['entry_money']))

                throw new FailedResult(CONTEST_FEE_ERR);*/

            if (!empty($data['entry_points']) && (!Validation::IsInteger($data['entry_points']) || $data['entry_points'] < 0)) {
                throw new FailedResult(INVALID_POINT_INPUT);
            }
            if (!empty($data['entry_money']) && (!Validation::IsInteger($data['entry_money']) || $data['entry_money'] < 0)) {
                throw new FailedResult(INVALID_AMOUNT_INPUT);
            }
            if ($data['reward_money'] > 0 && $data['reward_points'] <= 0 && $data['entry_money'] >= $data['reward_money'] && empty($data['reward_items']) && empty($data['reward_lands'])) {
                throw new FailedResult(CONTEST_MONEY_ERR_1);
            }
            if ($data['reward_points'] > 0 && $data['reward_money'] <= 0 && $data['entry_points'] >= $data['reward_points'] && empty($data['reward_items']) && empty($data['reward_lands'])) {
                throw new FailedResult(CONTEST_POINTS_ERR_1);
            }
            if (empty($data['spots']) || !Validation::IsInteger($data['spots']) || $data['spots'] > self::MAX_SPOT) {
                throw new FailedResult(CONTEST_SPOTS_ERR, self::MAX_SPOT);
            }
            if (empty($data['days']) || !Validation::IsInteger($data['days']) || $data['days'] > self::MAX_DAYS) {
                throw new FailedResult(CONTEST_DAYS_ERR);
            }
            if (!empty($data['reward_money']) && $user->bank < $data['reward_money']) {
                throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
            }
            if (!empty($data['reward_points']) && $user->points < $data['reward_points']) {
                throw new FailedResult(USER_NOT_ENOUGH_POINTS);
            }
            if (!empty($data['reward_money']) && !$user->RemoveFromAttribute('bank', $data['reward_money'])) {
                throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
            }
            if (!empty($data['reward_points']) && !$user->RemoveFromAttribute('points', $data['reward_points'])) {
                throw new FailedResult(USER_NOT_ENOUGH_POINTS);
            }
            $reward_items = '';

            $coma = '';

            if (is_array($data['reward_items'])) {
                $i = 0;

                foreach ($data['reward_items'] as $item => $qty) {
                    if($i >= count($data['reward_items'])){
                        break;
                    }

                    foreach ($data['itemsID'] as $items) {
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
                                $reward_items .= $coma . $items . '|' . $qty;

                                $coma = ',';
                                $ie = new Item($items);
                                $user->RemoveItems($ie, $qty);
                            } catch (Exception $e) {
                                throw $e;
                            }

                        }
                        $i++;
                    }
                }
            }

            $reward_land = '';

            if (is_array($data['reward_lands'])) {
                $coma = '';

                foreach ($data['reward_lands'] as $city => $qty) {
                    if (!Validation::IsInteger($qty)) {
                        continue;
                    }

                    try {
                        $user->RemoveLand($city, $qty);

                        $reward_land .= $coma . $city . '|' . $qty;

                        $coma = ',';
                    } catch (Exception $e) {
                    }
                }
            }

            $data = [
                        'userid' => $user->id,

                        'title' => Utility::SmartEscape($data['title']),

                        'description' => Utility::SmartEscape($data['description']),

                        'entry_money' => $data['entry_money'],

                        'entry_points' => $data['entry_points'],

                        'reward_money' => $data['reward_money'],

                        'reward_points' => $data['reward_points'],

                        'reward_items' => $reward_items,

                        'reward_land' => $reward_land,

                        'spots' => $data['spots'],

                        'created' => time(),

                        'finished' => time() + (int) $data['days'] * DAY_SEC,

                        'status' => self::AVAILABLE,
                ];

            return self::AddRecords($data, self::GetDataTable());
        }

        public function PickSpot(User $user, $spot)
        {
            if ($this->status != self::AVAILABLE) {
                throw new SoftException(CONTEST_NOT_PARTICIPATED);
            }
            if ($this->finished < time()) {
                throw new SoftException(CONTEST_NOT_PARTICIPATED);
            }

            return ContestUser::PickSpot($user, $this, $spot);
        }

        public function CancelContest(User $user)
        {
            if ($user->id != $this->userid) {
                throw new FailedResult(NOT_AUTHORIZED);
            }
            if ($this->status != self::AVAILABLE && $this->status != self::FILLED) {
                throw new FailedResult(INVALID_ACTION);
            }
            $participants = $this->GetParticipants();

            if ($this->finished < time() && !empty($participants)) {
                throw new FailedResult(INVALID_ACTION);
            }
            $participants_temp = [];

            foreach ($participants as $participant) {
                if (!isset($participants_temp[$participant->userid])) {
                    $participant->spottaken = 1;

                    $participants_temp[$participant->userid] = $participant;
                } else {
                    ++$participants_temp[$participant->userid]->spottaken;
                }
            }

            $participants = $participants_temp;

            unset($participants_temp);

            foreach ($participants as $participant) {
                try {
                    $pUser = UserFactory::getInstance()->getUser($participant->userid);

                    if ($this->entry_money > 0) {
                        $pUser->AddBankMoney($this->entry_money * $participant->spottaken);
                    }

                    if ($this->entry_points > 0) {
                        $pUser->AddPoints($this->entry_points * $participant->spottaken);
                    }

                    $msg = sprintf(CONTEST_CANCELLED_SUBS, $this->title, addslashes(User::SGetFormattedName($user->id, 'Low')), $this->entry_money * $participant->spottaken, $this->entry_points * $participant->spottaken);

                    $pUser->SendPmail(ANONYMOUS_GUARD_ID, time(), 'Contest cancelled', $msg);

                    $pUser->Notify($msg, OFFICIAL_CONTEST);

                    $pUser->__destruct();
                } catch (Exception $e) {
                }
            }

            try {
                if ($this->reward_money > 0) {
                    $user->AddBankMoney($this->reward_money);
                }

                if ($this->reward_points > 0) {
                    $user->AddPoints($this->reward_points);
                }
            } catch (Exception $e) {
            }

            $itemnames = '';

            $coma = '';

            if (!empty($this->reward_items)) {
                $cd = 0;
                $items = explode(',', $this->reward_items);

                $rpStore = new RPStore();

                for ($i = 0, $count = count($items); $i < $count; ++$i) {
                    if($cd >= count($items)){
                        break;
                    }
                    list($itemid, $itemqty, $itemawake) = explode('|', $items[$i]);

                    if ($itemid > 10000) {
                        $shopitem = new PShopRP($itemid - 10000);

                        $shopitem->DeleteFromPShop();

                        $itemnames .= $coma . $rpStore->getItemName($shopitem->pack);

                    //$user->
                    } else {
                        $user->AddItems($itemid, $itemqty, 0, $itemawake);

                        $item = new Item($itemid);

                        $itemnames .= $coma . $item->itemname;

                        $coma = ', ';
                    }
                    $cd++;
                }
            }

            $citynames = '';

            $coma = ', ';

            if (!empty($this->reward_land)) {
                $cities = explode(',', $this->reward_land);

                for ($i = 0, $count = count($cities); $i < $count; ++$i) {
                    list($city, $qty) = explode('|', $cities[$i]);

                    $user->AddLand($city, $qty);

                    $cityObj = new City($city);

                    $citynames .= $coma . $qty . strtolower(COM_ACRE) . '(s) in ' . $cityObj->name;
                }
            }

            if (empty($itemnames)) {
                $itemnames = COM_NONE;
            }

            $user->Notify(sprintf(CONTEST_CANCELLED_OWNER, $this->title, $this->reward_money, $this->reward_points, $itemnames, $citynames), OFFICIAL_CONTEST);

            return $this->SetAttributes(['status' => self::CANCELED, 'finished' => time()]);

            //$this->DeleteAllParticipants();

            //return self::sDelete(self::GetDataTable(), array('id'=> $this->id));
        }

        public function GetParticipants()
        {
            return ContestUser::GetAllForContest($this->id);
        }

        public function GetParticipantsBySpot()
        {
            return ContestUser::GetAllForContestBySpot($this->id);
        }

        public function DeleteAllParticipants()
        {
            return ContestUser::DeleteAllForContest($this->id);
        }

        public static function FinishExpiredContest()
        {
            $contests = self::GetContestExpired();

            foreach ($contests as $contest) {
                self::FinishContest($contest);
            }
        }

        public static function FinishContest($contest)
        {
            //$winnerSpot = mt_rand(1, $contest->spots);

            if (!ContestUser::ChooseWinner($contest->id)) {
                $contest = new Contest($contest->id);

                return $contest->CancelContest(UserFactory::getInstance()->getUser($contest->userid));
            }

            $winnerUser = ContestUser::GetWinnerForContest($contest->id);

            $winnerUserObj = UserFactory::getInstance()->getUser($winnerUser);

            $ownerUser = UserFactory::getInstance()->getUser($contest->userid);

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

            $coma = '';

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
                    $cd++;
                }
            }

            $citynames = '';

            $coma = ', ';

            if (!empty($contest->reward_land)) {
                $cities = explode(',', $contest->reward_land);

                for ($i = 0, $count = count($cities); $i < $count; ++$i) {
                    list($city, $qty) = explode('|', $cities[$i]);

                    $winnerUserObj->AddLand($city, $qty);

                    $cityObj = new City($city);

                    $citynames .= $coma . $qty . strtolower(COM_ACRE) . '(s) in ' . $cityObj->name;
                }
            }

            if (empty($itemnames)) {
                $itemnames = COM_NONE;
            }

            $winnerUserObj->Notify(sprintf(CONTEST_YOU_WIN, $contest->title, addslashes(User::SGetFormattedName($ownerUser->id, 'Low')), $contest->reward_money, $contest->reward_points, $itemnames, $citynames), OFFICIAL_CONTEST);

            $winnerUserObj->__destruct();

            try {
                $filledSpot = ContestUser::GetCountFilledSpots($contest->id);

                $entry_money = $filledSpot * $contest->entry_money;

                $entry_points = $filledSpot * $contest->entry_points;

                $ownerUser->AddBankMoney($entry_money);

                $ownerUser->AddPoints($entry_points);
            } catch (Exception $e) {
            }

            $ownerUser->Notify(sprintf(CONTEST_YOUR_FINISHED, $contest->title, addslashes(User::SGetFormattedName($winnerUserObj->id, 'Low')), $entry_money, $entry_points), OFFICIAL_CONTEST);

            self::sUpdate(self::GetDataTable(), ['reward_items' => $reward_items, 'status' => self::EXPIRED], ['id' => $contest->id]);
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

                'title',

                'description',

                'entry_money',

                'entry_points',

                'reward_money',

                'reward_points',

                'reward_items',

                'reward_land',

                'spots',

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
    }
