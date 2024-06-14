<?php

/**
 * discription: This class is used to manage possibility for any player to pay for a fail mug contract
 *  1. Player puts up contract for how many failed mugs they want done on themself.
 *
 * 2. Player enters how much points/ cash willing to pay for each failed mug.
 *
 * 3. Player submits contract (Money/Poins go into escrow).
 *
 * @author: Harish<harish282@gmail.com>
 * @name: MugContract
 * @package: includes
 * @subpackage: classes
 * @access: Public
 * @copyright: prisonstruggle <http://www.prisonstruggle.com>
 */
class MugContract extends BaseObject
{
    /** Define constatns for status **/
    const AVAILABLE = 1;    //Contract is available i.e not accepted by any provider

    const ACCEPTED = 2;    //Contract is accepted by provider.

    const COMPLETED = 3;    //Contract completed

    const FAILED = 4;    //Contract failed

    const CANCELED = 5;    //Contract failed

    const ALL = null; //All Status

    const MAXPOST = 3;    //Maximum number of contracts a buyer can post

    const MAXACCEPT = 5;    //Maximum number of contracts a buyer can accept

    const MAXACCEPTDAY = 5;    //Maximum number of contracts a provider can accept a day

    const TIMELIMIT = 3;    //multiple of hospital time

    const MAXSECLVL = 2;    //Maximum security level of players

    const MINLVL = 5;    //Minimum level of players

    const MINREWARDMONEY = 0;

    const MINREWARDPOINTS = 0;

    public static $idField = 'id';             //id field

    public static $dataTable = 'mugcontract';    // table implemented

    protected static $statusText = [
        self::AVAILABLE => HITLIST_AVAILABLE,

        self::ACCEPTED => HITLIST_ACCEPTED,

        self::COMPLETED => HITLIST_COMPLETED,

        self::FAILED => HITLIST_FAILED,

        self::CANCELED => COM_CANCELED,
    ]; // Status text

    protected $user_id = null;

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
     * @param status Mixed Status of contract. May be single or array
     * @param orderby String order field
     * @param sort String Sort type i.e ASC or DESC
     * @param extraFields String Extra field if needed from database
     *
     * @return array
     */
    public static function GetAll($status = null, $orderby = '', $sort = '', $extraFields = '')
    {
        return self::XGetAll('', $status, $orderby, $sort, $extraFields);
    }

    /**
     * Funtions return all returns.
     *
     * @param status Mixed Status of contract. May be single or array
     * @param orderby String order field
     * @param sort String Sort type i.e ASC or DESC
     * @param extraFields String Extra field if needed from database
     *
     * @return array
     */
    public static function GetContract($id)
    {
        return self::XGetAll('`id`=' . $id);
    }

    /**
     * Funtions return all contracts posted by buyer.
     *
     * @param $uid Number
     * @param status Mixed Status of contract. May be single or array
     * @param orderby String order field
     * @param sort String Sort type i.e ASC or DESC
     * @param extraFields String Extra field if needed from database
     *
     * @return array
     */
    public static function GetAllByBuyer($uid, $status = null, $orderby = '', $sort = '', $extraFields = '')
    {
        $extraFields = ', ( started + (' . DAY_SEC . ' * timelimit) - ' . $_SERVER['REQUEST_TIME'] . ') as timeleft';

        return self::GetAllByUserId($uid, 'buyer', $status, $orderby, $sort, $extraFields);
    }

    /**
     * Funtions return all contracts accepted by provider.
     *
     * @param $uid Number
     * @param status Mixed Status of contract. May be single or array
     * @param orderby String order field
     * @param sort String Sort type i.e ASC or DESC
     * @param extraFields String Extra field if needed from database
     *
     * @return array
     */
    public static function GetAllByProvider($uid, $status = null, $orderby = '', $sort = '', $extraFields = '')
    {
        $extraFields = ', ( started + (' . DAY_SEC . ' * timelimit) - ' . $_SERVER['REQUEST_TIME'] . ') as timeleft, grpgusers.gprot, grpgusers.hospital';

        return self::GetAllByUserId($uid, 'provider', $status, $orderby, $sort, $extraFields);
    }

    public static function GetStats($whatType, $orderby = '', $sort = '')
    {
        $objs = [];

        if (empty($orderby)) {
            $orderby = $whatType . '_mugs';
        }

        if (empty($sort)) {
            $sort = 'desc';
        }

        $campos = 'user_id, ' . $whatType . '_mugs, ' . $whatType . '_total, ' . $whatType . '_completed, ' . $whatType . '_avgtime, ' . $whatType . '_avg100';

        $query = 'SELECT ' . $campos . ' FROM mugcontract_stats';

        $query .= ' ORDER BY ' . $orderby . ' ' . $sort . ' Limit 50';

        $res = DBI::$conn->query($query);

        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    /**
     * Funtions used to create contract.
     *
     * @param $provide Number current user id
     * @param $money Number Awared
     * @param $points Number Awared
     * @param $mugcount Number
     *
     * @return Boleean
     */
    public static function CreateContract(User $buyer, $money, $points, $mugcount, $timelimit, $level, $speed, $minlevel)
    {
        $result = 0;

       
        if (0 >= intval($mugcount)) {
            throw new FailedResult(MUGCC_FAILED_REQUIRED_ERR);
        }
        if (!is_numeric($speed)) {
            throw new FailedResult(MUGCC_MAX_SPEED_ERR);
        }
        if (0 >= intval($timelimit)) {
            throw new FailedResult(MUGCC_TIMELIMIT_ERR);
        }
        if (0 > intval($money) || 0 > intval($points)) {
            throw new FailedResult(INVALID_AMOUNT_INPUT);
        }
        if (!Validation::IsInteger($money) || !Validation::IsInteger($points)) {
            throw new FailedResult(INVALID_AMOUNT_INPUT);
        }
        $points = (int) $points;

        $money = (int) $money;

        if ((0 < intval($money) && 0 < intval($points)) || (0 >= intval($money) && 0 >= intval($points))) {
            throw new FailedResult(MUGCC_MONEY_OR_POINT);
        }
        if ($money > 0 && self::MINREWARDMONEY > $money) {
            throw new FailedResult(sprintf(HITLIST_REWARD_MIN_MONEY, number_format(HitList::MINREWARDMONEY)));
        } elseif ($points > 0 && self::MINREWARDPOINTS > $points) {
            throw new FailedResult(sprintf(MUGCC_REWARD_MIN_POINTS, number_format(HitList::MINREWARDPOINTS)));
        }
        if ($minlevel <= self::MINLVL) {
            throw new FailedResult(sprintf(MUGCC_PROVIDER_MINLVL_MIN_ERR, self::MINLVL + 1));
        }
        if ($minlevel >= $level) {
            throw new FailedResult(sprintf(MUGCC_PROVIDER_MINLVL_MAX_ERR, $level));
        }
        if ($level <= self::MINLVL) {
            throw new FailedResult(sprintf(MUGCC_PROVIDER_LVL_MIN_ERR, self::MINLVL + 1));
        }
        $contracts = self::GetAllByBuyer($buyer->id, self::AVAILABLE);

        if (self::MAXPOST <= count($contracts)) { //Check of max limit of contract posted by buyer

            throw new FailedResult(sprintf(HITLIST_POST_MAX_CONTRACTS_ERR, self::MAXPOST));
        }
        $totalMoney = $money * $mugcount;

        $totalPoints = $points * $mugcount;

        if ($totalMoney > 0 && $totalMoney > $buyer->bank) { //Check buyer has enough bank money to post

            throw new FailedResult(HITLIST_NOT_ENOUGH_MONEY);
        }
        if ($totalPoints > 0 && $totalPoints > $buyer->points) { //Check buyer has enough bank money to post

            throw new FailedResult(HITLIST_NOT_ENOUGH_POINTS);
        }
        if ($totalMoney > 0 && !$buyer->RemoveFromAttribute('bank', $totalMoney)) {
            throw new FailedResult(HITLIST_NOT_ENOUGH_MONEY);
        }
        if ($totalPoints > 0 && !$buyer->RemoveFromAttribute('points', $totalPoints)) {
            throw new FailedResult(HITLIST_NOT_ENOUGH_POINTS);
        }
        $data = [
            'buyer' => $buyer->id,

            'money' => $money,

            'points' => $points,

            'mugcount' => $mugcount,

            'level' => $level,

            'minlevel' => $minlevel,

            'timelimit' => $timelimit,

            'created' => time(),

            'speed' => $speed,

            'paid' => 0,
        ];

        self::AddRecords($data, self::GetDataTable());

        return true;
    }

    /**
     * Funtions used to cancel a contract.
     */
    public function CancelContract(User $user, $reason = 0)
    {
        if ($this->buyer != $user->id && $this->provider != $user->id) {
            throw new FailedResult(NOT_AUTHORIZED);
        }
        if ($this->status != self::AVAILABLE && $this->status != self::ACCEPTED) {
            throw new FailedResult(MUGCC_ALREDY_CANCELED);
        }
        /*if($this->status == self::AVAILABLE)

            self::sDelete( self::GetDataTable(), array('id' => $this->id, 'buyer' => $this->buyer, 'status' => self::AVAILABLE));

        else

        {*/

        $updates = [
            'status' => self::CANCELED,

            'canceledby' => $user->id,

            'finished' => time(),
        ];

        self::sUpdate(self::$dataTable, $updates, ['id' => $this->id]);

        // }

        $result = $this->DistributeMoney();

        if ($reason == 1) {
            $buyerMsg = MUGCC_CONTRACT_CANCEL_NOTIFY_BUYER_LVL;

            $providerMsg = MUGCC_CONTRACT_CANCEL_NOTIFY_PROVIDER_LVL;
        } else {
            $buyerMsg = MUGCC_CONTRACT_CANCEL_NOTIFY_BUYER;

            $providerMsg = MUGCC_CONTRACT_CANCEL_NOTIFY_PROVIDER;
        }

        $buyerName = addslashes(User::SGetFormattedName($this->buyer, 'Low'));

        $providerName = addslashes(User::SGetFormattedName($this->provider, 'Low'));

        User::SNotify($this->buyer, sprintf($buyerMsg, $providerName, number_format($result['providerTotalMoney']), number_format($result['providerTotalPoints']), $this->mugdone, number_format($result['buyerMoney']), number_format($result['buyerPoints'])), MUGCC_CONTRACT);

        if (!empty($this->provider)) {
            User::SNotify($this->provider, sprintf($providerMsg, $buyerName, number_format($result['providerTotalMoney']), number_format($result['providerTotalPoints']), $this->mugdone, number_format($result['buyerMoney']), number_format($result['buyerPoints']), $buyerName), MUGCC_CONTRACT);
        }

        return true;
    }

    public function Collect(User $user, $reason = 0)
    {
        if ($this->provider != $user->id) {
            throw new FailedResult(NOT_AUTHORIZED);
        }
        if ($this->status != self::AVAILABLE && $this->status != self::ACCEPTED) {
            throw new FailedResult(MUGCC_ALREDY_CANCELED);
        }
        $result = $this->DistributeMoney(true);

        $msg = 'You have collected the partial reward of $%s and %s points for %s failed mugs on %s. You must fail %s more mugs on %s to complete the contract.';

        User::SNotify($user->id, sprintf($msg, number_format($result['providerMoney']), number_format($result['providerPoints']), $this->mugdone - $result['before'], addslashes(User::SGetFormattedName($this->buyer, 'Low')), $this->mugcount - $this->mugdone, addslashes(User::SGetFormattedName($this->buyer, 'Low')), MUGCC_CONTRACT));

        throw new SuccessResult(sprintf($msg, number_format($result['providerMoney']), number_format($result['providerPoints']), $this->mugdone - $result['before'], User::SGetFormattedName($this->buyer, 'Low'), $this->mugcount - $this->mugdone, User::SGetFormattedName($this->buyer, 'Low')));
    }

    /**
     * Function used to accept contract.
     **/
    public function AcceptContract(User $provider)
    {
        if (self::MINLVL > $provider->level) {
            throw new FailedResult(sprintf(MUGCC_SEC_LVL_ERR,  self::MINLVL));
        }
        if ($provider->speed > $this->speed && $this->speed > 0) {
            throw new FailedResult(sprintf(MUGCC_REQ_MAX_SPEED_ERR, $this->speed));
        }
        if ($provider->level < $this->minlevel) {
            throw new FailedResult(MUGCC_REQ_MINLVL_ERR);
        }
        if ($provider->level > $this->level) {
            throw new FailedResult(sprintf(MUGCC_REQ_LVL_ERR, $this->level));
        }
        if ($this->buyer == $provider->id) {
            throw new FailedResult(HITLIST_PROVIDER_BUYER_MUST_DIFFERENT);
        }
        if ($this->status == self::ACCEPTED) {
            throw new FailedResult(HITLIST_CONTRACT_NOT_AVAILABLE);
        }
        $contracts = self::GetAllByProvider($provider->id);

        $daycount = 0;

        $countcontracts = 0;

        foreach ($contracts as $mycontract) {
            if ($mycontract->buyer == $this->buyer && $mycontract->status == self::ACCEPTED) {
                throw new FailedResult(HITLIST_PROVIDER_HAVE_CONTRACT_ON_TARGET);
            }
            if ((time() - DAY_SEC) < $mycontract->started) {
                ++$daycount;
            }

            if ($mycontract->status == self::ACCEPTED) {
                ++$countcontracts;
            }

            if ($daycount >= self::MAXACCEPTDAY) {
                throw new FailedResult(sprintf(MUGCC_PROVIDER_MAX_CONTRACT_DAY, self::MAXACCEPTDAY));
            }
            if (self::MAXACCEPT <= $countcontracts) { //Check of max limit of contract posted by buyer

                throw new FailedResult(sprintf(HITLIST_PROVIDER_MAX_CONTRACT, self::MAXACCEPT));
            }
        }

        $updates = ['provider' => $provider->id,
            'status' => self::ACCEPTED,

            'started' => time(),
        ];

        self::sUpdate(self::$dataTable, $updates, ['id' => $this->id, 'status' => self::AVAILABLE]);

        if (DBi::$conn ->affected_rows <= 0) {
            throw new FailedResult(HITLIST_CONTRACT_NOT_AVAILABLE);
        }

        User::SNotify($this->buyer, sprintf(MUGCC_CONTRACT_ACCEPTED_MSG, addslashes(User::SGetFormattedName($provider->id, 'Low'))), MUGCC_CONTRACT);

        return true;
    }

    public function Mug(User $provider)
    {
        if ($this->provider != $provider->id) {
            throw new SoftException(HITLIST_NOT_AUTH_COMPLETE_CONTRACT);
        }
        if ($provider->level > $this->level) {
            return $this->CancelContract($provider, 1);
        }

        $timeleft = $this->TimeLeft(); //calculate remaining time for hit the target

        if ($timeleft <= 0) {
            $this->ContractFailed();
        }

        $targetUser = UserFactory::getInstance()->getUser($this->buyer);

        try {
            $provider->Mug($targetUser);
        } catch (FailedResult $e) {
            $message = $e->getMessage();

            if ($message == USER_MUGGED_FAILED_MSG) {
                $this->AddToAttribute('mugdone', 1);

                if ($this->mugcount <= $this->mugdone) {
                    $this->ContractCompleted($message);
                }
            }

            throw new FailedResult($message);
        }
    }

    public function TimeLeft()
    {
        return $this->started + ($this->timelimit * DAY_SEC) - $_SERVER['REQUEST_TIME']; //calculate remaining time for hit the target
    }

    public function ContractFailed()
    {
        if ($this->status != self::ACCEPTED) {
            throw new FailedResult(INVALID_ACTION);
        }
        $updates = [
            'status' => self::FAILED,

            'finished' => time(),
        ];

        self::sUpdate(self::$dataTable, $updates, ['id' => $this->id]);

        $buyerName = User::SGetFormattedName($this->buyer, 'Low');

        $providerName = User::SGetFormattedName($this->provider, 'Low');

        $result = $this->DistributeMoney();

        User::SNotify($this->buyer, sprintf(MUGCC_CONTRACT_FAILED_NOTIFY_BUYER, addslashes($providerName), number_format($result['providerTotalMoney']), number_format($result['providerTotalPoints']), $this->mugdone, number_format($result['buyerMoney']), number_format($result['buyerPoints'])), MUGCC_CONTRACT);

        $msg = sprintf(MUGCC_CONTRACT_FAILED_NOTIFY_PROVIDER, addslashes($buyerName), number_format($result['providerTotalMoney']), number_format($result['providerTotalPoints']), $this->mugdone, number_format($result['buyerMoney']), number_format($result['buyerPoints']), addslashes($buyerName));

        $msg1 = sprintf(MUGCC_CONTRACT_FAILED_NOTIFY_PROVIDER, $buyerName, number_format($result['providerTotalMoney']), number_format($result['providerTotalPoints']), $this->mugdone, number_format($result['buyerMoney']), number_format($result['buyerPoints']), $buyerName);

        User::SNotify($this->provider, $msg, MUGCC_CONTRACT);

        throw new FailedResult($msg1);
    }

    public function ContractCompleted($message = '')
    {
        if ($this->status != self::ACCEPTED) {
            throw new FailedResult(INVALID_ACTION);
        }
        $updates = [
            'status' => self::COMPLETED,

            'finished' => time(),
        ];

        self::sUpdate(self::$dataTable, $updates, ['id' => $this->id]);

        $buyerName = User::SGetFormattedName($this->buyer, 'Low');

        $providerName = User::SGetFormattedName($this->provider, 'Low');

        $result = $this->DistributeMoney();

        User::SNotify($this->buyer, sprintf(MUGCC_CONTRACT_COMPLETED_NOTIFY_BUYER, addslashes($providerName), number_format($result['providerTotalMoney']), number_format($result['providerTotalPoints']), $this->mugdone), MUGCC_CONTRACT);

        $msg = sprintf(MUGCC_CONTRACT_COMPLETED_NOTIFY_PROVIDER, addslashes($buyerName), number_format($result['providerTotalMoney']), number_format($result['providerTotalPoints']), $this->mugdone);

        $msg1 = sprintf(MUGCC_CONTRACT_COMPLETED_NOTIFY_PROVIDER, $buyerName, number_format($result['providerTotalMoney']), number_format($result['providerTotalPoints']), $this->mugdone);

        User::SNotify($this->provider, $msg, MUGCC_CONTRACT);

        throw new SuccessResult($message . '<br />' . $msg1, 'contract_completed');
    }

    /**checkes provider has any contract on target, if yes then contract id is returned else false returned
     */

    public static function HasContract($provider, $target)
    {
        return MySQL::GetSingle('SELECT id FROM ' . self::$dataTable . ' WHERE provider = \'' . $provider . '\' AND buyer = \'' . $target . '\' AND status = ' . self::ACCEPTED);
    }

    public static function IsUserTarget($target)
    {
        return MySQL::GetSingle('SELECT id FROM ' . self::$dataTable . ' WHERE buyer = ' . $target . ' AND status = ' . self::ACCEPTED);
    }

    public static function FinishOldContracts()
    {
        $contracts = self::XGetAll(' created < ' . (time() - 5 * DAY_SEC), self::AVAILABLE);

        foreach ($contracts as $contract) {
            self::sDelete(self::GetDataTable(), ['id' => $contract->id, 'buyer' => $contract->buyer, 'status' => self::AVAILABLE]);

            $buyerMoney = $contract->money * $contract->mugcount;

            $buyerPoints = $contract->points * $contract->mugcount;

            $userBuyer = UserFactory::getInstance()->getUser($contract->buyer);

            if ($buyerMoney > 0) {
                $userBuyer->AddBankMoney($buyerMoney);
            }

            if ($buyerPoints > 0) {
                $userBuyer->AddPoints($buyerPoints);
            }

            User::SNotify($contract->buyer, sprintf(MUGCC_CONTRACT_EXPIRE_NOTIFY_BUYER, number_format($buyerMoney), number_format($buyerPoints)), MUGCC_CONTRACT);

            $userBuyer->__destruct();
        }
    }

    public static function MemberFor($time)
    {
        $d = (int) ($time / DAY_SEC);

        $time = $time % DAY_SEC;

        $h = (int) ($time / 3600);

        $time = $time % 3600;

        $m = (int) ($time / 60);

        $return = $d > 0 ? $d . 'd ' : '';

        $return .= $h > 0 ? $h . 'h ' : '';

        $return .= $m . 'm';

        return $return;
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

            'buyer',

            'provider',

            'money',

            'points',

            'mugcount',

            'mugdone',

            'mugdone',

            'minlevel',

            'level',

            'timelimit',

            'created',

            'started',

            'finished',

            'status',

            'canceledby',

            'speed',

            'paid',
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

    /**
     * Funtions return all returns.
     *
     * @param where String condition
     * @param status Mixed Status of contract. May be single or array
     * @param orderby String order field
     * @param sort String Sort type i.e ASC or DESC
     * @param extraFields String Extra field if needed from database
     *
     * @return array
     */
    protected static function XGetAll($where, $status, $orderby = '', $sort = '', $extraFields = '')
    {
        $objs = [];

        if (empty($orderby)) {
            $orderby = 'created';
        }

        $query = 'SELECT mugcontract.*, username as targetName ' . $extraFields . ' FROM mugcontract , grpgusers WHERE mugcontract.buyer = grpgusers.id ';

        if (!empty($where)) {
            $query .= ' AND ' . $where;
        }

        if ($status != null && !is_array($status)) {
            $status = [$status];
        }

        if ($status != null && is_array($status)) {
            $query .= ' AND status IN (' . implode(',', $status) . ')';
        }

        $query .= ' ORDER BY ' . $orderby . ' ' . $sort;

        /**Pagination **/

        if (self::$usePaging) {//If doing paging for records
            $query = preg_replace('/^SELECT/', 'SELECT SQL_CALC_FOUND_ROWS', $query); // insert SQL_CALC_FOUND_ROWS into query to find total rows into table

            self::$paginator = new Paginator();

            self::$paginator->setRecordsPerPage(Paginator::$recordsOnPage);

            $res = DBI::$conn->query(self::$paginator->getLimitQuery($query));

            $totalRecords = MySQL::GetSingle('Select FOUND_ROWS()');

            self::$paginator->setQueryString();

            self::$paginator->setPageData('', $totalRecords, Paginator::$recordsOnPage, Paginator::$scrollPages, true, false, true);
        } else {
            $res = DBI::$conn->query($query);
        }

        if (mysqli_num_rows($res) == 0) {
            return $objs;
        }

        while ($obj = mysqli_fetch_object($res)) {
            $obj->formattedCreated = date('F d, Y g:i:sa', $obj->created);

            $obj->formattedStarted = date('F d, Y g:i:sa', $obj->started);

            $obj->formattedFinished = date('F d, Y g:i:sa', $obj->finished);

            $obj->formattedTargetName = User::SGetFormattedName($obj->buyer, 'Low');

            $obj->statusText = self::$statusText[$obj->status];

            $obj->formatedMoney = '$' . number_format($obj->money);

            $objs[] = $obj;
        }

        return $objs;
    }

    /**
     * Funtions return all returns.
     *
     * @param user_id Number
     * @param field String Buyer or Provider
     * @param status Mixed Status of contract. May be single or array
     * @param orderby String order field
     * @param sort String Sort type i.e ASC or DESC
     * @param extraFields String Extra field if needed from database
     *
     * @return array
     */
    protected static function GetAllByUserId($user_id, $field, $status, $orderby = '', $sort = '', $extraFields = '')
    {
        return self::XGetAll($field . ' = \'' . $user_id . '\'', $status, $orderby, $sort, $extraFields);
    }

    protected function DistributeMoney($collect = false)
    {
        $money = $this->money;

        $points = $this->points;

        if (!$collect) {
            $buyerMoney = $this->money * ($this->mugcount - $this->mugdone);

            $buyerPoints = $this->points * ($this->mugcount - $this->mugdone);
        } else {
            $buyerMoney = 0;

            $buyerPoints = 0;
        }

        $mugsdone = (int) $this->mugdone;

        $mugspaid = (int) $this->paid;

        $providerMoney = $this->money * ($mugsdone - $mugspaid);

        $providerPoints = $this->points * ($mugsdone - $mugspaid);

        $providerTotalMoney = $this->money * $mugsdone;

        $providerTotalPoints = $this->points * $mugsdone;

        $before = $this->paid;

        $this->setAttribute('paid', $this->mugdone);

        $userBuyer = UserFactory::getInstance()->getUser($this->buyer);

        if (!empty($this->provider)) {
            $userProvider = UserFactory::getInstance()->getUser($this->provider);
        }

        if ($buyerMoney > 0) {
            $userBuyer->AddBankMoney($buyerMoney);
        }

        if ($buyerPoints > 0) {
            try {
                $userBuyer->AddPoints($buyerPoints);
            } catch (Exception $e) {
            }
        }

        if ($providerMoney > 0) {
            $userProvider->AddBankMoney($providerMoney);
        }

        if ($providerPoints > 0) {
            try {
                $userProvider->AddPoints($providerPoints);
            } catch (Exception $e) {
            }
        }

        self::AddRecords([
            'contract' => $this->id,

            'buyerMoney' => $buyerMoney,

            'buyerPoints' => $buyerPoints,

            'providerMoney' => $providerMoney,

            'providerPoints' => $providerPoints,

            'paidbefore' => $before,

            'paid' => $this->mugdone,

            'timestamp' => time(),
        ], 'mugslogsrequest');

        return [
            'buyerMoney' => $buyerMoney,

            'buyerPoints' => $buyerPoints,

            'providerMoney' => $providerMoney,

            'providerPoints' => $providerPoints,

            'providerTotalMoney' => $providerTotalMoney,

            'providerTotalPoints' => $providerTotalPoints,

            'before' => $before,
        ];
    }
}
