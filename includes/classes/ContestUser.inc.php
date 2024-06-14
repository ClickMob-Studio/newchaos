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
    class ContestUser extends BaseObject
    {
        /** Define constatns for status **/
        const MAX_USER_SPOTS = 3; //i.e 1/x of total spots

        public static $idField = null; //id field

        public static $dataTable = 'contest_user'; // table implemented

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
                $order = $options['dir'];
            }

            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, 0, $order, $dir);
        }

        public static function GetAllForContest($contestId)
        {
            return self::GetAll('cid=' . $contestId);
        }

        public static function GetAllForUser($userid)
        {
            return self::GetAll('userid=' . $userid);
        }

        public static function GetAllForContestBySpot($contestId)
        {
            $objs = self::GetAll('cid=' . $contestId);

            $records = [];

            foreach ($objs as $obj) {
                $records[$obj->spot] = $obj;
            }

            return $records;
        }

        public static function DeleteAllForContest($contestId)
        {
            return self::sDelete(self::GetDataTable(), ['cid' => $contestId]);
        }

        public static function PickSpot(User $user, Contest $contest, $spot)
        {
            if ($user->id == $contest->userid) {
                throw new FailedResult(INVALID_ACTION);
            }
            $participants = self::GetAllForContest($contest->id);

            if ($contest->spots <= count($participants)) {
                throw new FailedResult(CONTEST_SPOTS_FILLED);
            }
            $spotsTaken = 0;

            foreach ($participants as $participant) {
                if ($participant->userid == $user->id && $spot == $participant->spot) {
                    throw new FailedResult(CONTEST_USER_ALREADY_PICKED);
                } elseif ($spot == $participant->spot) {
                    throw new FailedResult(CONTEST_SPOT_ALREADY_PICKED);
                }
                if ($participant->userid == $user->id) {
                    ++$spotsTaken;
                }

                if ($spotsTaken > floor($contest->spots / self::MAX_USER_SPOTS)) {
                    throw new FailedResult(CONTEST_USER_MAX_PICKED);
                }
            }

            if ($contest->entry_money > 0 && $user->bank < $contest->entry_money) {
                throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
            }
            if ($contest->entry_points > 0 && $user->points < $contest->entry_points) {
                throw new FailedResult(USER_NOT_ENOUGH_POINTS);
            }
            if ($contest->entry_money > 0 && !$user->RemoveFromAttribute('bank', $contest->entry_money)) {
                throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
            }
            if ($contest->entry_points > 0 && !$user->RemoveFromAttribute('points', $contest->entry_points)) {
                throw new FailedResult(USER_NOT_ENOUGH_POINTS);
            }
            $data = [
                        'cid' => $contest->id,

                        'userid' => $user->id,

                        'spot' => $spot,

                        'winner' => 0,
                ];

            self::AddRecords($data, self::GetDataTable());

            $spotFilled = self::GetCountFilledSpots($contest->id);

            if ($spotFilled == $contest->spots) {
                Contest::FinishContest($contest);
            }

            throw new SuccessResult(CONTEST_SPOT_PICKED);
        }

        public static function ChooseWinner($cid)
        {
            $data = ['winner' => 1];

            $sql = 'UPDATE ' . self::GetDataTable() . ' SET winner = 1 WHERE cid = ' . $cid . ' AND spot != 0 ORDER BY RAND() LIMIT 1';

            DBi::$conn->query($sql);

            if (DBi::$conn -> affected_rows == 0) {
                return false;
            }

            return true;

            //return self::sUpdate(self::GetDataTable(), $data, array('cid' => $cid, 'spot' =>$winnerSpot));
        }

        public static function GetWinnerForContest($cid)
        {
            self::$usePaging = false;

            $objs = self::GetAll('winner = 1 AND cid=' . $cid);
            if($objs) {
                return $objs[0]->userid;
            }
        }

        public static function GetCountFilledSpots($cid)
        {
            return MySQL::GetSingle('SELECT COUNT(userid) as filledspot FROM ' . self::GetDataTable() . ' WHERE cid = ' . $cid);
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
                'cid',

                'userid',

                'spot',

                'winner',
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
