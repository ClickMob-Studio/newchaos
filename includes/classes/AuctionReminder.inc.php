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
    class AuctionReminder extends BaseObject
    {
        const FEE = 50000;

        public static $idField = 'log_id'; //id field

        public static $dataTable = 'auction_reminder'; // table implemented

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
        public static function GetAll($where = '', $orderBy = '', $orderSort = 'ASC')
        {
            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, $orderBy, $orderSort);
        }

        public static function GetAllForUser(User $user)
        {
            $where = 'user_id = ' . $user->id;

            return self::GetAll($where);
        }

        public static function Add(User $user, AuctionLog $log, $beforehour)
        {
            if (0 >= (int) $beforehour || time() > ($log->finished - 3600 * $beforehour)) {
                throw new FailedResult(INVALID_BEFORE_TIME);
            }
            $data = [
                        'log_id' => $log->id,

                        'user_id' => $user->id,

                        'beforehour' => $beforehour,

                        'hourtime' => $log->finished - (3600 * $beforehour),
                ];

            $id = self::AddRecords($data, self::GetDataTable());

            return $id;
        }

        /**
         * delete the delete an auction.
         *

         * @param int $id
         *
         * @return mixed
         */
        public static function Delete(User $user, $log_id = null)
        {
            $where = ['user_id' => $user->id];

            if (!empty($log_id)) {
                $where['log_id'] = $log_id;
            }

            return self::sDelete(self::GetDataTable(), $where);
        }

        public static function DeleteForAuction($log_id)
        {
            $where = ['log_id' => $log_id];

            return self::sDelete(self::GetDataTable(), $where);
        }

        public static function SendPmail()
        {
            $time = time();

            $reminders = self::GetAll('hourtime <= ' . $time);

            foreach ($reminders as $reminder) {
                $user = UserFactory::getInstance()->getUser($reminder->user_id);

                if ($user->bank < self::FEE) {
                    $user->__destruct();

                    continue;
                }

                if (!$user->RemoveFromAttribute('bank', self::FEE)) {
                    $user->__destruct();

                    continue;
                }

                $auction = Auction::GetAllWhere(['log_id' => $reminder->log_id, 'user_id' => $reminder->user_id]);

                $auction = $auction[0];

                if (empty($auction)) {
                    continue;
                }

                $totalbids = AuctionBidder::GetBidCount($auction->logid);

                $msg = sprintf(AUCTION_REMINDER_MSG, $auction->item, date('d-m-Y h:i:s', $auction->finished), $auction->avgbid, $auction->mybid, $totalbids);

                Pms::Add($reminder->user_id, ANONYMOUS_GUARD_ID, $time, AUCTION_REMINDER_SUB, $msg);

                $user->__destruct();
            }

            return  DBi::$conn->query('DELETE FROM ' . self::GetDataTable() . ' WHERE hourtime <= ' . $time);
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

                'beforehour',

                'hourtime',
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
