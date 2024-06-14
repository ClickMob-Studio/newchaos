<?php

/**
 * discription: This class is used to manage possibility for any player to pay for a fail mug contract

 *  1. Player puts up contract for how many failed mugs they want done on themself.

  3. Player submits contract (Money/Poins go into escrow).

 * @author: Harish<harish282@gmail.com>

 * @name: MugContract

 * @package: includes

 * @subpackage: classes

 * @access: Public

 * @copyright: prisonstruggle <http://www.prisonstruggle.com>
 */
class CrimesContract extends BaseObject
{
    /** Define constatns for status * */
    const AVAILABLE = 1; //Contract is available i.e not accepted by any provider

    const ACCEPTED = 2; //Contract is accepted by provider.

    const COMPLETED = 3; //Contract completed

    const FAILED = 4;    //Contract failed

    const CANCELED = 5; //Contract failed

    const ALL = null; //All Status

    const MAXPOST = 3;    //Maximum number of contracts a buyer can post

    const MAXACCEPT = 1;    //Maximum number of contracts a buyer can accept

    const MAXACCEPTDAY = 5; //Maximum number of contracts a provider can accept a day

    const TIMELIMIT = 3; //multiple of hospital time

    const MAXSECLVL = 2;    //Maximum security level of players

    const MINLVL = 5;    //Minimum level of players

    const MINREWARDMONEY = 0;

    const MINREWARDPOINTS = 0;

    public static $idField = 'id';             //id field

    public static $dataTable = 'crimescontract';    // table implemented

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
        $extraFields = ', ( started + (' . DAY_SEC . ' * timelimit) - ' . $_SERVER['REQUEST_TIME'] . ') as timeleft, grpgusers.gprot, grpgusers.hospital';

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
    public static function CreateContract(User $buyer, $money, $points, $mugcount, $timelimit, $level)
    {
        $result = 0;

        if (1000 > intval($mugcount)) {
            throw new FailedResult(MUGC_FAILED_REQUIRED_ERR);
        }
        if ($level > $buyer->level) {
            throw new FailedResult('The Min level cannot exceed your own level.');
        }
        if (0 >= intval($timelimit)) {
            throw new FailedResult(MUGC_TIMELIMIT_ERR);
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
            throw new FailedResult(MUGC_MONEY_OR_POINT);
        }
        if (intval($points) > 0 && intval($points) < 2) {
            throw new FailedResult('You must pay at least 2 points per 1000 EXP.');
        }
        if (intval($money) > 0 && intval($money) < 1000) {
            throw new FailedResult('You must pay at least $1,000 per 1000 EXP.');
        }
        if ($money > 0 && self::MINREWARDMONEY > $money) {
            throw new FailedResult(sprintf(HITLIST_REWARD_MIN_MONEY, number_format(HitList::MINREWARDMONEY)));
        } elseif ($points > 0 && self::MINREWARDPOINTS > $points) {
            throw new FailedResult(sprintf(MUGC_REWARD_MIN_POINTS, number_format(HitList::MINREWARDPOINTS)));
        }
        if ($level < self::MINLVL) {
            throw new FailedResult(sprintf(MUGC_PROVIDER_LVL_MIN_ERR, self::MINLVL));
        }
        $contracts = self::GetAllByBuyer($buyer->id, self::AVAILABLE);

        if (self::MAXPOST <= count($contracts)) { //Check of max limit of contract posted by buyer
            throw new FailedResult(sprintf(HITLIST_POST_MAX_CONTRACTS_ERR, self::MAXPOST));
        }
        $totalMoney = $money * ($mugcount / 1000);

        $totalPoints = $points * ($mugcount / 1000);

        if ($totalMoney > 0 && $totalMoney > $buyer->money) { //Check buyer has enough bank money to post
            throw new FailedResult(HITLIST_NOT_ENOUGH_MONEY);
        }
        if ($totalPoints > 0 && $totalPoints > $buyer->points) { //Check buyer has enough bank money to post
            throw new FailedResult(HITLIST_NOT_ENOUGH_POINTS);
        }
        if ($totalMoney > 0 && !$buyer->RemoveFromAttribute('money', $totalMoney)) {
            throw new FailedResult(HITLIST_NOT_ENOUGH_MONEY);
        }
        if ($totalPoints > 0 && !$buyer->RemoveFromAttribute('points', $totalPoints)) {
            throw new FailedResult(HITLIST_NOT_ENOUGH_POINTS);
        }
        $data = [
            'buyer' => $buyer->id,
            'money' => $money,
            'points' => $points,
            'crimeexpneed' => $mugcount,
            'level' => $level,
            'timelimit' => $timelimit,
            'created' => time(),
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
            throw new FailedResult(MUGC_ALREDY_CANCELED);
        }
        $updates = [
            'status' => self::CANCELED,

            'canceledby' => $user->id,

            'finished' => time(),
        ];

        self::sUpdate(self::$dataTable, $updates, ['id' => $this->id]);

        // }

        $result = $this->DistributeMoney('Slave');

        if ($reason == 1) {
            $buyerMsg = "'%s\'s level is now higher than yours. $%s and %s points have been credited to %s for %s Exp. $%s and %s points have been re-credited to you.";

            $providerMsg = 'Your crime contract with %s has been canceled because your level is now higher than their\'s. $%s and %s have been credited to you for %s Exp. $%s and %s points have been re-credited to %s.';
        } else {
            $buyerMsg = 'Your crime contract accepted by %s has been canceled. $%s and %s points have been credited to %s for %s Exp. $%s and %s points have been re-credited to you.';

            $providerMsg = "'Your crime contract with %s has been canceled. $%s and %s points have been credited to you for %s Exp. $%s and %s points have been re-credited to %s.'";
        }

        if (!empty($this->provider)) {
            User::SNotify($this->buyer, sprintf($buyerMsg,
                 addslashes(User::SGetFormattedName($this->provider, 'Low')),

                number_format($result['providerMoney']),

                number_format($result['providerPoints']),

                addslashes(User::SGetFormattedName($this->provider, 'Low')),

                $this->crimeexpdone,

                number_format($result['buyerMoney']),
                number_format($result['buyerPoints'])), MUGC_CONTRACT);
        }

        if (!empty($this->provider)) {
            User::SNotify($this->provider, sprintf($providerMsg,
              addslashes(User::SGetFormattedName($this->buyer, 'Low')),

                    number_format($result['providerMoney']),

                    number_format($result['providerPoints']),

                    $this->crimeexpdone,

                    number_format($result['buyerMoney']),

                    number_format($result['buyerPoints']),
                    addslashes(User::SGetFormattedName($this->buyer, 'Low'))), MUGC_CONTRACT);
        }

        return true;
    }

    /**
      Function used to accept contract

     * */
    public function AcceptContract(User $provider)
    {
        if (self::UnderContract($provider->id) != false) {
            throw new FailedResult('You can only have one contract.');
        }
        $buyer = UserFactory::getInstance()->getUser($this->buyer);

        if ($provider->level > $buyer->level) {
            throw new FailedResult(sprintf('You cannot accept contracts from soldiers with lower level.'));
        }
        if ($provider->level < $this->level) {
            throw new FailedResult(sprintf(MUGC_REQ_LVL_ERR, $this->level));
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
                throw new FailedResult(sprintf(MUGC_PROVIDER_MAX_CONTRACT_DAY, self::MAXACCEPTDAY));
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

        if (DBi::$conn -> affected_rows <= 0) {
            throw new FailedResult(HITLIST_CONTRACT_NOT_AVAILABLE);
        }

        User::SNotify($this->buyer, sprintf(MUGC_CONTRACT_ACCEPTED_MSG, addslashes(User::SGetFormattedName($provider->id, 'Low'))), MUGC_CONTRACT);

        return true;
    }

    public static function Crimes(User $user, $exp)
    {
        $ctr = self::UnderContract($user->id);

        $cCrime = new self($ctr);

        $provider = $user;

        $buyer = UserFactory::getInstance()->getUser($cCrime->provider);

        if ($provider->level > $buyer->level) {
            return $cCrime->CancelContract($provider, 1);
        }

        $timeleft = $cCrime->TimeLeft(); //calculate remaining time for hit the target

        if ($timeleft <= 0) {
            $cCrime->ContractFailed();
        }

        $cCrime->AddToAttribute('crimeexpdone', $exp);

        if ($cCrime->crimeexpneed <= $cCrime->crimeexpdone) {
            $cCrime->ContractCompleted($message);
        }
    }

    public function TimeLeft()
    {
        return $this->started + ($this->timelimit * 60 * 60 * 24) - time(); //calculate remaining time for hit the target
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

        $targetName = User::SGetFormattedName($this->buyer, 'Low');

        $result = $this->DistributeMoney('Slave');

        User::SNotify($this->buyer, sprintf('Your crime contract has been failed. $%s and %s points have been credited to the provider for %s Exp. $%s and %s points have been re-credited to you.'
                                            , number_format($result['providerMoney']),

                                            number_format($result['providerPoints']),

                                            $this->crimeexpdone,

                                            number_format($result['buyerMoney']),
                                            number_format($result['buyerPoints'])), MUGC_CONTRACT);

        $msg = sprintf('You have failed the contract you had with %s because the time limit expired. $%s and %s have been credited to you for %s Exp. $%s and %s points have been re-credited to your employer.',
                        $targetName,

                        number_format($result['providerMoney']),

                        number_format($result['providerPoints']),

                        $this->crimeexpneed,
                        number_format($result['buyerMoney']), number_format($result['buyerPoints']));

        User::SNotify($this->provider, $msg, MUGC_CONTRACT);

        throw new FailedResult($msg);
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

        $targetName = User::SGetFormattedName($this->buyer, 'Low');

        $result = $this->DistributeMoney('Master');

        User::SNotify($this->buyer, sprintf('Your crime contract has been completed by %s. The $%s and %s points reward has been credited to %s.',
                                User::SGetFormattedName($this->provider, 'Low'),

                                number_format($result['providerMoney']),

                                number_format($result['providerPoints']),
                                 User::SGetFormattedName($this->provider, 'Low')), MUGC_CONTRACT);

        $msg = sprintf('You have completed your contract with %s. The $%s money and %s points reward has been credited to you.', $targetName, number_format($result['providerMoney']), number_format($result['providerPoints']));

        User::SNotify($this->provider, $msg, MUGC_CONTRACT);

        throw new SuccessResult($message . '<br>' . $msg, 'contract_completed');
    }

    /*     * checkes provider has any contract on target, if yes then contract id is returned else false returned

     */

    public static function UnderContract($provider)
    {
        return MySQL::GetSingle('SELECT id FROM ' . self::$dataTable . ' WHERE provider = \'' . $provider . '\'  AND status = ' . self::ACCEPTED);
    }

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
        $contracts = self::XGetAll(' created < ' . (time() - 5 * 24 * 60 * 60), self::AVAILABLE);

        foreach ($contracts as $contract) {
            self::sDelete(self::GetDataTable(), ['id' => $contract->id, 'buyer' => $contract->buyer, 'status' => self::AVAILABLE]);

            $buyerMoney = floor($contract->money * max((($contract->crimeexpneed) / 1000), 0));

            $buyerPoints = floor($contract->points * max((($contract->crimeexpneed) / 1000), 0));

            $userBuyer = UserFactory::getInstance()->getUser($contract->buyer);

            if ($buyerMoney > 0) {
                $userBuyer->AddBankMoney($buyerMoney);
            }

            if ($buyerPoints > 0) {
                $userBuyer->AddPoints($buyerPoints);
            }

            User::SNotify($contract->buyer, sprintf(MUGC_CONTRACT_EXPIRE_NOTIFY_BUYER, number_format($buyerMoney), number_format($buyerPoints)), MUGC_CONTRACT);

            $userBuyer->__destruct();
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

            'buyer',

            'provider',

            'money',

            'points',

            'crimeexpneed',

            'crimeexpdone',

            'level',

            'timelimit',

            'created',

            'started',

            'finished',

            'status',

            'canceledby',
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

        $query = 'SELECT crimescontract.*, username as targetName ' . $extraFields . ' FROM crimescontract , grpgusers WHERE crimescontract.buyer = grpgusers.id ';

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

        /*         * Pagination * */

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

    protected function DistributeMoney($who = 'Master')
    {
        $money = $this->money;

        $points = $this->points;

        switch ($who) {
            case 'Master':

               $buyerMoney = floor($this->money * max((($this->crimeexpneed - $this->crimeexpdone) / 1000), 0));

                $buyerPoints = floor($this->points * max((($this->crimeexpneed - $this->crimeexpdone) / 1000), 0));

                $providerMoney = floor($this->money * $this->crimeexpneed / 1000) - $buyerMoney;

                $providerPoints = floor($this->points * $this->crimeexpneed / 1000) - $buyerPoints;

                break;

            default:

                 $buyerMoney = ceil($this->money * max((($this->crimeexpneed - $this->crimeexpdone) / 1000), 0));

                $buyerPoints = ceil($this->points * max((($this->crimeexpneed - $this->crimeexpdone) / 1000), 0));

                 $providerMoney = floor($this->money * $this->crimeexpneed / 1000) - $buyerMoney;

                $providerPoints = floor($this->points * $this->crimeexpneed / 1000) - $buyerPoints;

                break;
        }

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

        return [
            'buyerMoney' => $buyerMoney,

            'buyerPoints' => $buyerPoints,

            'providerMoney' => $providerMoney,

            'providerPoints' => $providerPoints,
        ];
    }
}
