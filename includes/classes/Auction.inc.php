<?php

    /**
     * discription: This class is used to manage auctions placed by users.
     *
     * @author: Harish<harish282@gmail.com>

     * @name: Auction

     * @package: includes

     * @subpackage: classes

     * @final: Final

     * @access: Public

     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    class Auction extends BaseObject
    {
        /** Define constatns for status **/
        const GENERIC = 1;

        const SHOP = 2;

        const ITEM = 3;

        const BOOK = 4;

        const FINE = 100000;	//10mil	fine.

        public static $idField = 'id'; //id field

        public static $dataTable = 'auction'; // table implemented

        protected static $statusText = [
                                                self::GENERIC => AUCTION_GENERIC,
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
        public static function GetAll()
        {
            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable());
        }

        public static function GetAllActive()
        {
            $sql = 'SELECT a.id, a.item, a.itemid, a.desc, a.type, a.creator, a.period, a.winners, a.minbid, a.anonymous, a.auctionlog, a.topbids, a.announce_subject, a.announce_message, al.id as logid, al.started, al.finished, (SELECT sum(bid_count) FROM auction_bidders ab1 WHERE ab1.log_id = a.auctionlog) as totalbid  FROM auction a, auction_log al WHERE a.id = al.auction_id AND al.finished >= \'' . time() . '\' ORDER BY al.finished';

            $objs = self::GetPaginationResults($sql);

            foreach ($objs as $key => $obj) {
                $obj->avgbid = self::GetAvgBidAmount($obj->logid, $obj->topbids);

                $objs[$key] = $obj;
            }

            return $objs;
        }

        public static function GetAllExpired()
        {
            return self::GetWhere(['allexpired' => true], 'finished', 'DESC');
        }

        public static function GetWhere(array $search = [], $orderBy = '', $orderSort = 'ASC')
        {
            $where = '';

            if (!empty($search['id'])) {
                $where .= ' AND a.id = \'' . $search['id'] . '\' AND al.id = a.auctionlog';
            }

            if (!empty($search['log_id'])) {
                $where .= ' AND al.id = \'' . $search['log_id'] . '\'';
            }

            if (isset($search['active']) && $search['active']) {
                $where .= ' AND al.finished > \'' . time() . '\'';
            } elseif (isset($search['expired']) && $search['expired']) {
                $where .= ' AND al.finished <= \'' . time() . '\' AND al.closed = 0';
            } elseif (isset($search['closed']) && $search['closed']) {
                $where .= ' AND al.finished <= \'' . (time() - DAY_SEC) . '\' AND al.closed = 1';
            } elseif (isset($search['sheduled']) && $search['sheduled']) {
                $where .= ' AND al.finished <= \'' . time() . '\' AND a.auctionlog = al.id AND a.itration > 0 AND (al.finished + a.itration * ' . DAY_SEC . ') <= \'' . time() . '\'';
            } elseif (isset($search['allexpired']) && $search['allexpired']) {
                $where .= ' AND al.finished <= \'' . time() . '\'';
            }

            $sql = 'SELECT a.id, a.item, a.itemid, a.desc, a.type, a.creator, a.period, a.winners, a.minbid, a.anonymous, a.auctionlog, a.topbids, a.announce_subject, a.announce_message, al.id as logid, al.started, al.finished, (SELECT MAX(bid) FROM auction_bidders ab WHERE ab.log_id = al.id) as currbid FROM auction a, auction_log al WHERE a.id = al.auction_id ' . $where;

            if (!empty($orderBy)) {
                $sql .= ' ORDER BY ' . $orderBy . ' ' . $orderSort;
            }

            return self::GetPaginationResults($sql);
        }

        public static function GetAllForUser(User $user, array $search = [])
        {
            $where = '';

            if (!empty($search['id'])) {
                $where .= ' AND a.id = \'' . $search['id'] . '\'';
            }

            if (!empty($search['log_id'])) {
                $where .= ' AND al.id = \'' . $search['log_id'] . '\'';
            }

            if (isset($search['active']) && $search['active']) {
                $where .= ' AND al.finished >= \'' . time() . '\' AND a.auctionlog = al.id';
            } elseif (isset($search['finished']) && $search['finished']) {
                $where .= ' AND al.finished < \'' . time() . '\'';
            } elseif (isset($search['closed']) && $search['closed']) {
                $where .= ' AND al.finished > \'' . (time() - 4 * DAY_SEC) . '\'';
            }

            $sql = 'SELECT a.id, a.item, a.itemid, a.desc, a.type, a.creator, a.period, a.winners, a.minbid, a.anonymous, a.auctionlog, a.topbids, a.announce_subject, a.announce_message, al.id as logid, al.started, al.finished, ab1.bid as mybid, ab1.is_winner as mybidstatus FROM auction a LEFT JOIN auction_log al ON al.auction_id = a.id , auction_bidders ab1 WHERE ab1.log_id = al.id AND ab1.user_id = \'' . $user->id . '\'' . $where;

            $objs = self::GetPaginationResults($sql);

            foreach ($objs as $key => $obj) {
                $obj->avgbid = self::GetAvgBidAmount($obj->logid, $obj->topbids);

                $objs[$key] = $obj;
            }

            return $objs;
        }

        public static function GetAllWhere(array $search = [])
        {
            $where = '';

            if (!empty($search['id'])) {
                $where .= ' AND a.id = \'' . $search['id'] . '\'';
            }

            if (!empty($search['log_id'])) {
                $where .= ' AND al.id = \'' . $search['log_id'] . '\'';
            }

            if (isset($search['active']) && $search['active']) {
                $where .= ' AND al.finished >= \'' . time() . '\' AND a.auctionlog = al.id';
            } elseif (isset($search['finished']) && $search['finished']) {
                $where .= ' AND al.finished < \'' . time() . '\'';
            } elseif (isset($search['closed']) && $search['closed']) {
                $where .= ' AND al.finished > \'' . (time() - 4 * DAY_SEC) . '\'';
            }

            if (!empty($search['user_id'])) {
                $whereLeft1 .= ' AND ab1.user_id = \'' . $search['user_id'] . '\'';
            }

            $sql = 'SELECT a.id, a.item, a.itemid, a.desc, a.type, a.creator, a.period, a.winners, a.minbid, a.anonymous, a.auctionlog, a.topbids, a.announce_subject, a.announce_message, al.id as logid, al.started, al.finished, ab1.bid as mybid, ab1.is_winner as mybidstatus FROM auction a LEFT JOIN auction_log al ON al.auction_id = a.id LEFT JOIN auction_bidders ab1 ON ab1.log_id = al.id ' . $whereLeft1 . ' WHERE  1 = 1 ' . $where . '';

            $objs = self::GetPaginationResults($sql);

            foreach ($objs as $key => $obj) {
                $obj->avgbid = self::GetAvgBidAmount($obj->logid, $obj->topbids);

                $objs[$key] = $obj;
            }

            return $objs;
        }

        /**
         * gets the average bid amount.
         *

         * @param int $actionLog
         * @param int $topbids
         *
         * @return float
         */
        public static function GetAvgBidAmount($actionLog, $topbids)
        {
            $sql = 'SELECT bid FROM auction_bidders where log_id = ' . $actionLog . ' ORDER BY bid DESC LIMIT ' . $topbids;

            $result = DBi::$conn->query($sql);

            $bidsum = 0;

            $totalrecords = mysqli_num_rows($result);

            while ($row = mysqli_fetch_assoc($result)) {
                $bidsum += $row['bid'];
            }

            if ($totalrecords <= 0) {
                return 0;
            }

            return ceil($bidsum / $totalrecords);
        }

        /**
         * function used to add auction.
         *

         * @param mixed $data
         */
        public static function Add(User $user, array $data)
        {
            if (empty($data['item'])) {
                throw new FailedResult(AUCTION_EMPTY_ITEM);
            }
            if (0 >= (int) $data['winners']) {
                throw new FailedResult(AUCTION_WRONG_WINNERS_NUM);
            }
            if (0 >= (int) $data['period']) {
                throw new FailedResult(AUCTION_WRONG_PERIOD);
            }
            if (0 >= (int) $data['minbid'] || !Validation::IsInteger($data['minbid'])) {
                throw new FailedResult(AUCTION_WRONG_MIN_BID);
            }
            if (0 >= (int) $data['topbids']) {
                throw new FailedResult(AUCTION_WRONG_TOP_BIDS);
            }
            $time = time();

            $data = [
                        'item' => Utility::SmartEscape($data['item']),

                        'itemid' => (float) $data['itemid'],

                        'desc' => Utility::SmartEscape($data['desc']),

                        'type' => (!isset($data['type']) || empty($data['type'])) ? self::GENERIC : (int) $data['type'],

                        'creator' => $user->id,

                        'created' => $time,

                        'period' => (int) $data['period'],

                        'winners' => (int) $data['winners'],

                        'minbid' => (int) $data['minbid'],

                        'itration' => (int) $data['itration'],

                        'anonymous' => (int) $data['anonymous'],

                        'topbids' => (int) $data['topbids'],

                        'announce_subject' => $data['announce_subject'],

                        'announce_message' => $data['announce_message'],
                ];

            $id = self::AddRecords($data, self::GetDataTable());

            $auctionLogId = AuctionLog::Add($id, (int) $data['period']);

            $data = [
                    'auctionlog' => (int) $auctionLogId,
                ];

            self::sUpdate(self::GetDataTable(), $data, ['id' => $id]);

            return $id;
        }

        /**
         * Function used to modify auction.
         *

         * @param mixed $data
         */
        public static function Modify(User $user, array $data)
        {
            if (empty($data['id'])) {
                throw new FailedResult(AUCTION_INVALID_ID);
            }
            if (empty($data['item'])) {
                throw new FailedResult(AUCTION_EMPTY_ITEM);
            }
            if (0 >= (int) $data['winners']) {
                throw new FailedResult(AUCTION_WRONG_WINNERS_NUM);
            }
            if (0 >= (int) $data['period']) {
                throw new FailedResult(AUCTION_WRONG_PERIOD);
            }
            if (0 >= (int) $data['minbid']) {
                throw new FailedResult(AUCTION_WRONG_MIN_BID);
            }
            $time = time();

            $id = $data['id'];

            $auction = new Auction($id);
            $auction->SetAttribute('item', Utility::SmartEscape($data['item']));
            $auction->SetAttribute('itemid', (float) $data['itemid']);
            $auction->SetAttribute('desc', Utility::SmartEscape($data['desc']));
            $auction->SetAttribute('period', (int)$data['period']);
            $auction->SetAttribute('winners', (int)$data['winners']);
            $auction->SetAttribute('minbid', (int)$data['minbid']);
            $auction->SetAttribute('itration', (int)$data['itration']);
            $auction->SetAttribute('anonymous', (int)$data['anonymous']);

//            $data = [
//                        'item' => Utility::SmartEscape($data['item']),
//
//                        'itemid' => (float) $data['itemid'],
//
//                        'desc' => Utility::SmartEscape($data['desc']),
//
//                        'period' => (int) $data['period'],
//
//                        'winners' => (int) $data['winners'],
//
//                        'minbid' => (int) $data['minbid'],
//
//                        'itration' => (int) $data['itration'],
//
//                        'anonymous' => (int) $data['anonymous'],
//                ];
//
//            self::sUpdate(self::GetDataTable(), $data, ['id' => $id, 'creator' => $user->id]);

            return true;
        }

        /**
         * delete the delete an auction.
         *

         * @param int $id
         *
         * @return mixed
         */
        public static function Delete(User $user, $id)
        {
            $where = ['id' => $id];

            if (!$user->IsAdmin()) {
                $where[creator] = $user->id;
            }

            return self::sDelete(self::GetDataTable(), $where);
        }

        public static function DeleteCurrent(User $user, $id)
        {
            $where = ['id' => $id];

            $auction = self::GetWhere($where);

            return AuctionLog::Delete($auction[0]->auctionlog);
        }

        /**
         * Function used to start the auction.
         */
        public function Start(User $user)
        {
            if (!$user->IsAdmin() && $this->creator != $user->id) {
                throw new FailedResult(AUCTION_INVALID_OWNER);
            }

            $auctionLogId = AuctionLog::Add($this->id, $this->period);

            $this->SetAttribute('auctionlog', $auctionLogId);

            return true;
        }

        /**
         * function used to put bid.
         *

         * @param int $amount
         * @param int $annon
         */
        public function Bid(User $user, $amount, $annon = 0)
        {
            if ($amount < $this->minbid) {
                throw new FailedResult(AUCTION_INVALID_BID_AMOUNT);
            }
            if ($amount > AUCTION_MAX_BID) {
                throw new FailedResult(sprintf(AUCTION_BID_AMOUNT_MAX_ERR, number_format(AUCTION_MAX_BID)));
            }
//            if ($user->GetField('age') < 5) {
//                throw new FailedResult(AUCTION_AGE_ERR);
//            }
            if ($user->bank < $this->minbid) {
                throw new FailedResult(AUCTION_MIN_BANK_ERR);
            }
            AuctionBidder::Bid($user, $this->auctionlog, $amount, $annon);

            return true;
        }

        /**
         * function used to edit the bid.
         *

         * @param int $amount
         * @param int $annon
         */
        public function EditBid(User $user, $amount, $annon = 0)
        {
            if ($amount < $this->minbid) {
                throw new FailedResult(AUCTION_INVALID_BID_AMOUNT);
            }
            if ($amount > AUCTION_MAX_BID) {
                throw new FailedResult(sprintf(AUCTION_BID_AMOUNT_MAX_ERR, number_format(AUCTION_MAX_BID)));
            }
            AuctionBidder::EditBid($user, $this->auctionlog, $amount, $annon);

            return true;
        }

        /**
         * used to stop expired auctions.
         */
        public static function StopExpired()
        {
            $auctions = self::GetWhere(['expired' => true]);

            foreach ($auctions as $key => $auction) {
                $winners = AuctionBidder::SelectWinners($auction); //Select winners

                AuctionLog::Finish($auction->auctionlog);				//Finish auction

                //User::sNotify(ANONYMOUS_GUARD_ID, sprintf(AUCTION_AUCTION_EXPIRED_MSG, $auction->id, '<a href="auction.php?section=view&id=' . $auction->id . '">', '</a>'));

                if (!empty($auction->announce_subject)) {
                    $winnerNames = '';

                    $coma = '';

                    foreach ($winners as $winner) {
                        $winnerNames .= $coma . User::SGetFormattedName($winner);

                        $coma = ', ';
                    }

                    //$message = str_replace('%winners%', addslashes($winnerNames), $auction->announce_message);

                    //$message = str_replace('%auction%', $auction->item, $message);

                    //Announcement::Add(ADMIN_USER_ID, $message, $auction->announce_subject);
                }
            }
        }

        /**
         * used to run itrative auctions.
         */
        public static function RunSheduled()
        {
            $auctions = self::GetWhere(['sheduled' => true]);

            foreach ($auctions as $key => $auction) {
                $auctionLogId = AuctionLog::Add($auction->id, (int) $auction->period);

                $data = ['auctionlog' => (int) $auctionLogId];

                self::sUpdate(self::GetDataTable(), $data, ['id' => $auction->id]);
            }
        }

        public static function FineDefaulters()
        {
            $auctions = self::GetWhere(['closed' => true]);

            foreach ($auctions as $key => $auction) {
                $bidders = AuctionBidder::GetNotClaimed($auction->logid); //Get winners

                foreach ($bidders as $bidder) {
                    if (User::SRemoveBankMoney($bidder->user_id, self::FINE)) {
                        $msg = sprintf(AUCTION_FINE_TOOK, "<a href='auction.php?section=view&lid=" . $auction->logid . "'>", '</a>', number_format(self::FINE));

                        AuctionBidder::BidPunished($bidder->user_id, $bidder->log_id);

                        Pms::Add($bidder->user_id, ANONYMOUS_GUARD_ID, time(), PMS_ADMIN_AUTO_RESPONDER, $msg);

                        User::sNotify($bidder->user_id, $msg);
                    } else {
                        User::sNotify(ANONYMOUS_GUARD_ID, sprintf(AUCTION_FINE_FAILED, User::SGetFormattedName($user->id), '<a href="auction.php?section=view&id=' . $this->id . '">', '</a>'));

                        User::sNotify($bidder->user_id, sprintf(AUCTION_YOU_FINE_FAILED, '<a href="auction.php?section=view&lid=' . $auction->logid . '">', '</a>'));
                    }
                }

                DBi::$conn->query('UPDATE ' . AuctionLog::GetDataTable() . ' SET closed = 2 WHERE id = \'' . $auction->logid . '\'');
            }

            return true;
        }

        /**
         * Function used to claim the bid.
         */
        public function Claim(User $user)
        {
            $auctionLog = AuctionLog::GetLast($this->id);

            if (empty($auctionLog)) {
                throw new SoftException(AUCTION_CANT_CLAIM);
            }
            if ($auctionLog->finished + 3 * DAY_SEC < time()) {
                throw new SoftException(AUCTION_CANT_CLAIM);
            }
            $bidDetail = AuctionBidder::GetForUser($user, $auctionLog->id);

            if (!$bidDetail->is_winner) {
                throw new SoftException(AUCTION_NOT_WINNER);
            }
            if ($bidDetail->is_winner == AuctionBidder::CLAIMED) {
                throw new SoftException(AUCTION_ALREADY_CLAIM);
            }
            $usermoney = $user->money;

            $userbank = $user->bank;

            if ($bidDetail->bid > ($usermoney + $userbank)) {
                throw new SoftException(USER_HAVE_NOT_ENOUGH_MONEY);
            }
            $bidmoney = $bidDetail->bid;

            if ($bidmoney > $usermoney) {
                $takemoney = $usermoney;

                $bidmoney = $bidmoney - $usermoney;
            } else {
                $takemoney = $bidmoney;

                $bidmoney = 0;
            }

            if (!$user->RemoveFromAttribute('money', $takemoney) && $takemoney > 0) {
                throw new SoftException(USER_HAVE_NOT_ENOUGH_MONEY);
            }
            if ($bidmoney > 0) {
                if (!$user->RemoveFromAttribute('bank', $bidmoney)) {
                    $user->AddToAttribute('money', $takemoney);

                    throw new SoftException(USER_HAVE_NOT_ENOUGH_MONEY);
                }
            }

            AuctionBidder::BidClaimed($user->id, $auctionLog->id);

            //User::sNotify(ANONYMOUS_GUARD_ID, sprintf(AUCTION_USER_CLAIMED, User::SGetFormattedName($user->id), '<a href="auction.php?section=view&lid=' . $auctionLog->id . '">', '</a>'));

            if ($this->itemid == 1000001) {
                $user->SetAttribute('itemshop', '1');

                throw new SuccessResult(sprintf(AUCTION_CLAIMED_SHOP, $this->item));
            } elseif ($this->itemid == 1000002) {
                $user->SetAttribute('pointshop', '1');

                throw new SuccessResult(sprintf(AUCTION_CLAIMED_SHOP, $this->item));
            } elseif ($this->itemid == 1000003) {
                $user->SetAttribute('rpshop', '1');

                throw new SuccessResult(sprintf(AUCTION_CLAIMED_SHOP, $this->item));
            } elseif ($this->itemid == 1000004) {
                $user->SetAttribute('contest_permit', '1');

                throw new SuccessResult(sprintf(AUCTION_CLAIMED_SHOP, $this->item));
            } elseif ($this->type == self::BOOK) {
                UserBooks::Add($user->id, $this->itemid);

                throw new SuccessResult(sprintf(AUCTION_CLAIMED_ITEM, $this->item));
            }

            $item = new Item($this->itemid);
            if($item->awake == 0){
            $user->AddItems($item->id, 1, 0, $item->awake);
            }else{
                User::AddAwakeItems($user->id, $item->id, 1);
            }
            throw new SuccessResult(sprintf(AUCTION_CLAIMED_ITEM, $this->item));

            return true;
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

                'item',

                'itemid',

                'desc',

                'type',

                'creator',

                'created',

                'period',

                'winners',

                'minbid',

                'itration',

                'anonymous',

                'auctionlog',

                'topbids',

                'announce_subject',

                'announce_message',
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
