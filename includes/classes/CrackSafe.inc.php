<?php

    /**
     * discription: This class is used to manage safe game.
     *
     * @author: Harish<harish282@gmail.com>
     * @name: CrackSafe
     * @package: includes
     * @subpackage: classes
     * @final: Final
     * @access: Public
     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    class CrackSafe extends BaseObject
    {
        /** Define constatns **/
        const DEALERS_SAFE = 1;
        const GUARDS_SAFE = 2;
        const WARDENS_SAFE = 3;

        const OPEN = 1;
        const WON = 2;
        const FINISHED = 3;

        const MAX_TRIES = 10;       //Maximum tries a user can
        const DISP_TRIES = 5000;    //After these tries first number will be shown

        /** Static variables **/
        public static $idField = 'id';
        public static $dataTable = 'crack_safe';

        public static $safeText = [
                                        self::DEALERS_SAFE => DEALERS_SAFE,self::GUARDS_SAFE => GUARDS_SAFE,self::WARDENS_SAFE => WARDENS_SAFE,
                                    ];

        public static $baseRewards = [
                                    self::DEALERS_SAFE => 10,self::GUARDS_SAFE => 1000,self::WARDENS_SAFE => 15000,
                                ];

        public static $cost = [
                                    self::DEALERS_SAFE => 1000,self::GUARDS_SAFE => 50000,self::WARDENS_SAFE => 500000,
                                ];

        public static $rewardBonus = [
                                    self::DEALERS_SAFE => ['attempts' => 50, 'bonus' => 1, 'after' => 0],self::GUARDS_SAFE => ['attempts' => 4, 'bonus' => 1, 'after' => 2500],self::WARDENS_SAFE => ['attempts' => 10, 'bonus' => 20, 'after' => 3000],
                                ];

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
         * Funtions return all results.
         *
         * @return array
         */
        public static function GetAll($where = '', array $options = [])
        {
            $order = isset($options['order']) ? $options['order'] : false;
            $dir = isset($options['dir']) ? $options['dir'] : 'ASC';

            return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, $order, $dir);
        }

        public static function GetActive($type = 0)
        {
            $where = ' status = ' . self::OPEN;
            if (!empty($type)) {
                $where .= ' AND type = ' . $type;
            }

            $objs = self::GetAll($where);

            if (!empty($type)) {
                return $objs[0];
            }

            return $objs;
        }

        public static function GetLastWinners($type = self::DEALERS_SAFE, $num = 10)
        {
            $where = 'winner IS NOT NULL';
            if (!empty($type)) {
                $where .= ' AND type = ' . $type;
            }

            self::$usePaging = true;
            Paginator::$recordsOnPage = $num;
            $objs = self::GetAll($where, ['order' => 'finished', 'dir' => 'DESC']);
            self::$usePaging = false;

            return $objs;
        }

        public static function CrackSafeTries($safeId, User $user = null, array $options = [])
        {
            $where = '';
            if (is_a($user, 'User')) {
                $where .= ' AND user_id =\'' . $user->id . '\' ';
            }

            if (isset($options['today']) && $options['today'] == true) {
                $fromTime = mktime(10, 0, 0, date('m'), date('d'), date('Y'));
                if ($fromTime >= time()) {
                    $options = ['fromTime' => strtotime('-1 Day', $fromTime), 'toTime' => time()];
                } else {
                    $options = ['fromTime' => $fromTime, 'toTime' => time()];
                }
            }

            if (isset($options['fromTime']) && !empty($options['fromTime'])) {
                $where .= ' AND trytime >=\'' . $options['fromTime'] . '\' ';
            }
            if (isset($options['toTime']) && !empty($options['toTime'])) {
                $where .= ' AND trytime <=\'' . $options['toTime'] . '\' ';
            }

            return MySQL::GetSingle('SELECT COUNT(code) FROM crack_safe_tries WHERE safe_id = \'' . $safeId . '\'' . $where);
        }

        public static function UserAttempts($safeId, User $user = null, array $options = [])
        {
            $order = isset($options['order']) ? $options['order'] : false;
            $dir = isset($options['dir']) ? $options['dir'] : 'ASC';

            $where = 'safe_id = \'' . $safeId . '\'';
            if (is_a($user, 'User')) {
                $where .= ' AND user_id =\'' . $user->id . '\' ';
            }

            if (isset($options['code']) && !empty($options['code'])) {
                $where .= ' AND code =\'' . $options['code'] . '\' ';
            }
            if (isset($options['range']) && is_array($options['range'])) {
                $where .= ' AND CONVERT(code,SIGNED) BETWEEN \'' . $options['range'][0] . '\' AND \'' . $options['range'][1] . '\'';
            }

            return parent::GetAll(['safe_id', 'user_id', 'code', 'trytime'], 'crack_safe_tries', $where, false, false, $order, $dir);
        }

        /**
         * function used to add auction.
         *
         * @param int $type
         */
        public static function Create($type = self::DEALERS_SAFE)
        {
            if (empty($type)) {
                throw new FailedResult(SAFE_TYPE_ERR);
            }
            $currentSafe = self::GetActive($type);

            if (!empty($currentSafe)) {
                throw new FailedResult(sprintf(SAFE_IS_ALREADY_OPEN, self::$safeText[$type]));
            }
            $time = time();

            $data = [
                        'code' => Utility::RandomCode(4),
                        'type' => $type,
                        'reward' => self::$baseRewards[$type],
                        'created' => time(),
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
        public static function Delete(User $user, $id)
        {
            if (!$user->IsAdmin()) {
                throw new FailedResult(NOT_AUTHORIZED);
            }

            $where = ['id' => $id];

            return self::sDelete(self::GetDataTable(), $where);
        }

        /**
         * Function used to crack safe.
         *
         * @param mixed $code
         */
        public function Crack(User $user, $code)
        {
            if (empty($code) || !preg_match('/^\d{4}$/', $code)) {
                throw new FailedResult(SAFE_CODE_EMPTY);
            }
            $options = ['today' => true];
            $userTries = self::CrackSafeTries($this->id, $user, $options);
            if (self::MAX_TRIES <= $userTries) {
                throw new FailedResult(sprintf(SAFE_MAX_TRY_ERR, self::MAX_TRIES));
            }
            $tried = self::UserAttempts($this->id, $user, ['code' => $code]);
            if (!empty($tried)) {
                throw new FailedResult(SAFE_CODE_TRIED);
            }
            $money = self::$cost[$this->type];

            if ($user->money < $money) {
                throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
            }
            if (!$user->RemoveFromAttribute('money', $money) && $money > 0) {
                throw new FailedResult(USER_HAVE_NOT_ENOUGH_MONEY);
            }
            $time = time();

            $data = [
                'safe_id' => $this->id,
                'user_id' => $user->id,
                'code' => $code,
                'trytime' => $time,
            ];

            parent::AddRecords($data, 'crack_safe_tries');

            if ($this->code == $code && $this->status == self::OPEN) {
                $data = [
                            'status' => self::WON,
                            'winner' => $user->id,
                            'finished' => $time,
                        ];

                $this->SetAttributes($data);

                $points = $this->reward + $this->bonus;

                try {
                    $user->AddPoints($points);
                } catch (Exception $e) {
                    //points limit exception
                }

                $this->Reset();

                throw new SuccessResult(sprintf(SAFE_WON, self::$safeText[$this->type], $points));
            }

            $tries = $this->tries + 1;
            extract(self::$rewardBonus[$this->type]);

            $data = ['tries' => $tries];

            if ($tries > $after) {
                $multiply = (int) (($tries - $after) / $attempts);
                $data['bonus'] = $bonus * $multiply;
            }
            $this->SetAttributes($data);

            throw new FailedResult(sprintf(SAFE_FAILED, self::$safeText[$this->type]));
        }

        /**
         * Reset a safe code.
         */
        public function Reset()
        {
            if ($this->status == self::OPEN) {
                $data = [
                            'status' => self::FINISHED,
                            'finished' => time(),
                        ];

                $this->SetAttributes($data);
            }

            return self::Create($this->type);
        }

        public static function ResetAll()
        {
            $safes = self::GetActive();

            if (!empty($safes)) {
                foreach ($safes as $safe) {
                    $safeObj = new CrackSafe($safe->id);
                    $safeObj->Reset();
                }
            } else {
                CrackSafe::Create(self::DEALERS_SAFE);
                CrackSafe::Create(self::GUARDS_SAFE);
                CrackSafe::Create(self::WARDENS_SAFE);
            }
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
                'code',
                'type',
                'created',
                'reward',
                'bonus',
                'winner',
                'finished',
                'status',
                'tries',
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
