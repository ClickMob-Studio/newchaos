<?php

    /**
     * discription: This class is used to manage hate points for quest level 3.
     *
     * @author: Harish<harish282@gmail.com>
     * @name: HatePoints
     * @package: includes
     * @subpackage: classes
     * @final: Final
     * @access: Public
     * @copyright: icecubegaming <http://www.icecubegaming.com>
     */
    class HatePoints extends BaseObject
    {
        /** Define constatns **/
        const MAX_POINTS = 10000;
        const MAX_RP_DAYS = 200;

        const TASK_WAR = 'war';
        const TASK_ONLINE_ATTACK = 'online attack';
        const TASK_OFFLINE_ATTACK = 'offline attack';
        const TASK_MUG = 'mug';
        const TASK_CRIME = 'Crime';
        const TASK_SEND_MONEY = 'send money';
        const TASK_SEND_ITEM = 'send item';
        const TASK_CRON = 'Cron';

        /** Static variables **/
        public static $idField = 'id';
        public static $dataTable = 'hate_points';

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

        public static function GetActive()
        {
            $where = ' finished = 0';

            $objs = self::GetAll($where);

            return $objs;
        }

        public static function GetUserTotalPoints(User $user = null)
        {
            if ($user != null) {
                return MySQL::GetSingle('SELECT SUM(points) FROM ' . self::GetDataTable() . ' WHERE finished = 0 AND user_id = ' . $user->id);
            }

            $query = 'SELECT user_id, SUM(points) as points FROM ' . self::GetDataTable() . ' WHERE finished = 0 GROUP BY user_id ORDER BY points DESC';

            return parent::GetPaginationResults($query, 'page', 'user_id');
        }

        /**
         * function used to add auction.
         *
         * @param int $type
         */
        public static function AddPoint(User $user, $task, $success = true, User $user2 = null)
        {
            if ($user->resetStatus != 'started' || $user->securityLevel != 2) {
                return;
            }
            if ((float) Variable::GetValue('riotStarted') != 0) {
                return;
            }

            $point = 0;
            switch ($task) {
                case self::TASK_WAR:
                    $point = 50;
                break;
                case self::TASK_ONLINE_ATTACK:
                    $point = $success ? $user2->level * .1 : -2;
                break;
                case self::TASK_OFFLINE_ATTACK:
                    $point = $success ? $user2->level * .05 : -0.5;
                break;
                case self::TASK_MUG:
                    $point = $success ? 1 : -0.2;
                break;
                case self::TASK_CRIME:
                    $point = $success ? .05 : -0.1;
                break;
                case self::TASK_SEND_ITEM:
                    $point = -1.5;
                break;
                case self::TASK_SEND_MONEY:
                    $point = -1;
                break;
                case self::TASK_CRON:
                    $point = -1;
                break;
                default:
                    throw new FailedResult(INVALID_ACTION);
            }

            $data = [
                        'user_id' => $user->id,
                        'points' => $point,
                        'task' => $task,
                        'created' => time(),
                ];
            $id = self::AddRecords($data, self::GetDataTable());

            $hatePoints = (float) Variable::GetValue('hatePoints');
            $hatePoints += $point;

            if ($hatePoints < 0) {
                $hatePoints = 0;
            }

            if ($hatePoints > self::MAX_POINTS) {
                $hatePoints = self::MAX_POINTS;
            }

            Variable::Save('hatePoints', $hatePoints);

            if ($hatePoints >= self::MAX_POINTS) {
                self::StartRiot();
            }

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

        public static function DecreasePoints($points = 1)
        {
            $riotStarted = (float) Variable::GetValue('riotStarted');
            if ($riotStarted > 0) {
                return;
            }

            $hatePoints = (float) Variable::GetValue('hatePoints');
            $hatePoints -= $points;

            if ($hatePoints < 0) {
                $hatePoints = 0;
            }

            Variable::Save('hatePoints', $hatePoints);

            $data = [
                        'user_id' => ANONYMOUS_GUARD_ID,
                        'points' => -$points,
                        'task' => self::TASK_CRON,
                        'created' => time(),
                ];
            self::AddRecords($data, self::GetDataTable());
        }

        public static function StartRiot()
        {
            Announcement::Add(ANONYMOUS_GUARD_ID, RIOT_STARTED, RIOT_STARTED_SUB);

            DBi::$conn->query('UPDATE grpgusers SET defense =5, strength =5, speed=5, bank = bank + money, money = 0 WHERE admin =1');

            DBi::$conn->query('UPDATE grpgusers SET resetStatus=\'ready\' WHERE securityLevel = 2 AND resetStatus = \'started\'');

            Variable::Save('hatePoints', 0);
            Variable::Save('riotRPDays', self::MAX_RP_DAYS);
            Variable::Save('riotStarted', time() + (6 * 3600));
        }

        public static function StopRiot()
        {
            Announcement::Add(ANONYMOUS_GUARD_ID, RIOT_STOPED, RIOT_STOPED_SUB);
            Variable::Save('riotStarted', 0);
            Variable::Save('hatePoints', 0);
            Variable::Save('riotRPDays', 0);

            DBi::$conn->query('UPDATE ' . self::GetDataTable() . ' SET finished = ' . time() . ' WHERE finished = 0');
        }

        public static function IsAnyAttempting()
        {
            $count = MySQL::GetSingle('SELECT COUNT(id) FROM ' . User::GetDataTable() . ' WHERE securityLevel = 2 AND resetStatus = \'started\'');

            return $count > 0;
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
                'points',
                'task',
                'created',
                'finished',
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
