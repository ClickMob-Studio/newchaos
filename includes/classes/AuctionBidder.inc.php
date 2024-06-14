<?php
error_reporting(0);
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
    class AuctionBidder extends BaseObject
    {
        const WINNER = 1;		//Bid winner

        const CLAIMED = 2;		//Bid is claimed

        const PUNISHED = 3;		//Bid is claimed

        public static $idField = 'log_id'; //id field

        public static $dataTable = 'auction_bidders'; // table implemented

        protected static $statusText = [
                                                0 => '-',

                                                self::WINNER => WINNER,

                                                self::CLAIMED => CLAIMED,

                                                self::PUNISHED => PUNISHED,
                                            ]; // Status text

        /**
         * Constructor.
         */
        public function __construct()
        {
        }

        /**
         * Returns the status array.

         *

         * @return array
         */
        public static function GetStatusText()
        {
            return self::$statusText;
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

        public static function GetAuctionAll($auctionLogId)
        {
            if (is_object($auctionLogId)) {
                $auctionLogId = $auctionLogId->id;
            }

            $where = 'log_id = \'' . $auctionLogId . '\'';

            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'bid` DESC, `bidtime', 'ASC');
        }

        public static function GetLastBid($auctionLogId)
        {
            $where = 'log_id = \'' . $auctionLogId . '\'';

            $objs = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, 1, 1, 'bid', 'DESC');

            if (empty($objs)) {
                return $objs;
            }

            return $objs[0];
        }

        public static function GetAllForUser(User $user, array $logIds = [])
        {
            $where = 'user_id = \'' . $user->id . '\'';

            if (!empty($logIds)) {
                $where .= ' AND log_id IN (\'' . @implode("','", $logIds) . '\')';
            }

            return parent::GetAllById('log_id', self::GetDataTableFields(), self::GetDataTable(), $where);
        }

        public static function GetForUser(User $user, $logId)
        {
            $objs = self::GetAllForUser($user, [$logId]);

            if (empty($objs)) {
                return [];
            }

            return $objs[$logId];
        }

        public static function GetWinners($auctionLogId)
        {
            $where = 'log_id = \'' . $auctionLogId . '\' AND is_winner != 0';

            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
        }

        public static function GetNotClaimed($auctionLogId)
        {
            $where = 'log_id = \'' . $auctionLogId . '\' AND is_winner = ' . self::WINNER;

            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
        }

        public static function GetClaimed($auctionLogId)
        {
            $where = 'log_id = \'' . $auctionLogId . '\' AND is_winner = ' . self::CLAIMED;

            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where);
        }

        public static function GetBidCount($auctionLogId)
        {
            return MySQL::GetSingle('SELECT SUM(bid_count) FROM auction_bidders WHERE log_id = ' . $auctionLogId);
        }

        public static function Bid(User $user, $auctionLogId, $amount, $annon = 0)
        {
            $myBid = AuctionBidder::GetAllForUser($user, [$auctionLogId]);

            $lastBid = $myBid[$auctionLogId];

            if ($lastBid != null && $amount <= $lastBid->bid) {
                throw new FailedResult(sprintf(AUCTION_BID_AMOUNT_LESS_LAST, $lastBid->bid));
            }
            $time = time();

            $data = [
                        'log_id' => $auctionLogId,

                        'user_id' => $user->id,

                        'is_anonymous' => $annon,

                        'bid' => $amount,

                        'bidtime' => $time,
                ];

            return self::AddRecords($data, self::GetDataTable());
        }

        public static function EditBid(User $user, $auctionLogId, $amount, $annon = 0)
        {
            $myBid = AuctionBidder::GetAllForUser($user, [$auctionLogId]);

            $lastBid = $myBid[$auctionLogId];

            if ($lastBid != null && $amount <= $lastBid->bid) {
                throw new FailedResult(sprintf(AUCTION_BID_AMOUNT_LESS_LAST, $lastBid->bid));
            }
            $time = time();

            $sql = 'UPDATE  ' . self::GetDataTable() . ' SET bidtime=' . time() . ', bid = ' . $amount . ', is_anonymous = ' . $annon;

            if ($user->id != $lastBid->user_id) {
                $sql .= ', bid_count = bid_count + 1';
            }

            $sql .= ' WHERE log_id = ' . $auctionLogId . ' AND user_id =' . $user->id;

            return DBi::$conn->query($sql);
        }

        public static function Delete($id)
        {
            $where = ['id' => $id];

            return self::sDelete(self::GetDataTable(), $where);
        }

        public static function SelectWinners($auction)
        {
            $auctionId = $auction->id;

            $item = $auction->item;

            $auctionLogId = $auction->auctionlog;

            $winners = $auction->winners;

            $auctionLog = new AuctionLog($auctionLogId);

            if ($auctionLog->closed > 0) {
                throw new FailedResult(INVALID_ACTION);
            }
            $bids = self::GetAuctionAll($auctionLogId);

            $winners_ids = [];

            for ($i = 0, $count = count($bids); $i < $winners && $i < $count; ++$i) {
                $bid = $bids[$i];

                if ($auction->type == Auction::BOOK) {
                    if (UserBooks::UserHasBook($bid->user_id, $auction->itemid)) {
                        Pms::Add($bid->user_id, ANONYMOUS_GUARD_ID, time(), AUCTION_WON, addslashes(sprintf(BOOK_AUCTION_ALREADY_EXISTS, '<b>' . $item . '</b>')));

                        ++$winners;

                        continue;
                    }
                }

                $data = ['is_winner' => self::WINNER];

                Pms::Add($bid->user_id, ANONYMOUS_GUARD_ID, time(), AUCTION_WON, addslashes(sprintf(AUCTION_YOU_WIN, '<b>' . $item . '</b>', '<a href="auction.php?action=claim&id=' . $auctionId . '">', '</a>')));

                self::sUpdate(self::GetDataTable(), $data, ['log_id' => $auctionLogId, 'user_id' => $bid->user_id]);

                $winners_ids[] = $bid->user_id;
            }

            return $winners_ids;
        }

        public static function XGetWinners($auctionLogId)
        {
            $auctionLog = new AuctionLog($auctionLogId);

            $auction = new Auction($auctionLog->auction_id);

            $winners = $auction->winners;

            $bids = self::GetWinners($auctionLogId);

            $winners_ids = [];

            for ($i = 0, $count = count($bids); $i < $winners && $i < $count; ++$i) {
                $bid = $bids[$i];

                $winners_ids[] = $bid->user_id;
            }

            return $winners_ids;
        }

        public static function getBiggestBid($auctionLogId)
        {
            $bids = self::GetAuctionAll($auctionLogId);

            $biggest = $bids[0];

            return $biggest->bid;
        }

        public static function BidClaimed($userId, $auctionLogId)
        {
            $data = ['is_winner' => self::CLAIMED];

            self::sUpdate(self::GetDataTable(), $data, ['log_id' => $auctionLogId, 'user_id' => $userId]);
        }

        public static function BidPunished($userId, $auctionLogId)
        {
            $data = ['is_winner' => self::PUNISHED];

            self::sUpdate(self::GetDataTable(), $data, ['log_id' => $auctionLogId, 'user_id' => $userId]);
        }

        public static function IsWinner($userId, $auctionLogId)
        {
            return MySQL::GetSingle('SELECT COUNT(' . self::$idField . ') FROM ' . self::GetDataTable() . ' WHERE log_id = ' . $auctionLogId . ' AND user_id = ' . $userId . ' AND is_winner != 0');
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

                'user_id',

                'bid',

                'is_anonymous',

                'is_winner',

                'bid_count',

                'bidtime',
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
