<?php

/**
 * discription: This class is used to manage possibility for any player to pay for a hit (a kill) on another player
 * 1) A player (called the buyer) goes on a hitlist page.
 * 2) The buyer adds a new hit (or contract) on another player (called the target) by choosing the hospital time required to validate the hit, the name of the player (by entering the id) and the money reward (any amount). Once the contract is placed, the buyer money reward is taken from the buyer bank and placed on escrow. Once the hit is added, a time limit is automatically calculated for the hit. The contract is now opened for providers (assassins). The contract must be accepted by a provider before it starts.
 * 3) Another player (called the provider or hitman) goes on the hitlist page. He sees the list of contracts available. Each contract only shows the target, the hospital time, reward, and time limit (it doesnt show the buyer, it never will). The provider can accept any contract he wants, he can even have multiple contracts accepted at the same time.
 * 4) Once the provider accepted the contract, the contract starts. There can only be one provider per contract. The buyer knows nothing about the provider (he doesnt know his name etc).
 * 5) The provider must now hit the target enough time to reach the hospital time asked by the buyer. Normal hits are 20 minutes for offline hits (target is offline) or 10 minutes for online hits (target is online) but it also depends on a skill that player can gain. So for example is the contract requires 90 minutes of hospital time, the target must be hit 5 times offline by a normal provider, before the time limit (because it will add up to be 100 minutes of hospital).
 * 6) If the time limit expires before the target has been hit for enough hospital time, the contract ends. The money is sent back to the buyer, the contract is removed from the hitlist, and both the buyer and provider get events about the failure. Nothing else happens.
 * 7) If the hospital time is reached before the time limit,
 * the contract is completed. The money is sent to the provider, deducted from a bribing fee of 5%.
 * The buyer and provider get events about the success.
 * Done.
 *
 */
class HitList extends BaseObject
{
    /** Define constatns for status **/
    const AVAILABLE = 1;    //Contract is available i.e not accepted by any provider
    const ACCEPTED = 2;    //Contract is accepted by provider.
    const COMPLETED = 3;    //Contract completed
    const FAILED = 4;    //Contract failed
    const ALL = null; //All Status

    const MAXPOST = 5;    //Maximum number of contracts a buyer can post
    const DAYMAX = 2;    //Maximum number of contracts a buyer can post with in 24 hrs
    const TIMELIMIT = 3;    //multiple of hospital time
    const BRIBEMONEY = 10;
    const SAFETYMONEY = 10;
    const MINREWARDMONEY = 50000;

    const BRIBEPOINTS = 10;
    const SAFETYPOINTS = 10;
    const MINREWARDPOINTS = 30;

    public static $idField = 'id'; //id field
    public static $dataTable = 'hitman'; // table implemented
    protected static $statusText = [
        self::AVAILABLE => HITLIST_AVAILABLE,
        self::ACCEPTED => HITLIST_ACCEPTED,
        self::COMPLETED => HITLIST_COMPLETED,
        self::FAILED => HITLIST_FAILED,
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
    public static function GetAllHitList($status = null, $orderby = '', $sort = '', $extraFields = '')
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
        $extraFields = ', ( started + (60 * timelimit) - ' . $_SERVER['REQUEST_TIME'] . ') as timeleft, grpgusers.gprot, grpgusers.hospital';

        return self::GetAllByUserId($uid, 'provider', $status, $orderby, $sort, $extraFields);
    }

    /**
     * Funtions used to create contract.
     *
     * @param $provide Number current user id
     * @param $target Number target user id
     * @param $money Number Awared
     * @param $hospitaltime Number
     *
     * @return Boleean
     */
    public static function CreateContract($buyer, $target, $money, $hospitaltime, $points)
    {
        if ($buyer == $target) {  //Check buyer and target are not same
            throw new FailedResult(HITLIST_BUYER_TARGET_MUST_DIFFERENT);
        }
        $result = 0;

        if (HitList::MINREWARDMONEY > floatval($money)) {
            ++$result;
        }
        if (HitList::MINREWARDPOINTS > floatval($points)) {
            $result += 2;
        }
        if ($result == 3) {
            throw new FailedResult(sprintf(HITLIST_REWARD_MIN_MONEY, number_format(HitList::MINREWARDMONEY)));
        }
        if (10 > $hospitaltime || $hospital > 120) { //Check hospital time is positive non zero number
            throw new FailedResult(HITLIST_HOSPITAL_TIME_BETWEEN_ERR);
        }
        //echo 'Buyer:'.$buyer.' / Target: '.$target.'<br>';
        //die;
        $target = UserFactory::getInstance()->getUser($target);
        if ($target->IsAdmin()) {
            throw new FailedResult(HITLIST_CANT_CONTRACT_ADMIN);
        }
        $contracts = self::GetAllByBuyer($buyer, self::AVAILABLE);
        if (self::MAXPOST <= count($contracts)) { //Check of max limit of contract posted by buyer
            throw new FailedResult(sprintf(HITLIST_POST_MAX_CONTRACTS_ERR, self::MAXPOST));
        }
        $daycount = 0;
        foreach ($contracts as $contract) {
            if ((time() - DAY_SEC) < $contract->created) {
                ++$daycount;
            }

            if ($daycount >= self::DAYMAX) {
                throw new FailedResult(sprintf(HITLIST_MAX_CONTRACT_DAY, self::DAYMAX));
            }
        }

        $buyer = UserFactory::getInstance()->getUser($buyer);

        if ($money > $buyer->money) { //Check buyer has enough bank money to post
            throw new FailedResult(HITLIST_NOT_ENOUGH_MONEY);
        }
        if ($points > $buyer->points) { //Check buyer has enough bank money to post
            throw new FailedResult(HITLIST_NOT_ENOUGH_POINTS);
        }
        if ($money > 0) {
            if (!$buyer->RemoveFromAttribute('money', $money)) {
                throw new FailedResult(HITLIST_NOT_ENOUGH_MONEY);
            }
        }
        if ($points > 0) {
            if (!$buyer->RemoveFromAttribute('points', $points)) {
                throw new FailedResult(HITLIST_NOT_ENOUGH_POINTS);
            }
        }
        $safetymoney = (int) (($money * self::SAFETYMONEY) / 100);
        $safetypoints = (int) (($points * self::SAFETYPOINTS) / 100);

        $data = [
            'buyer' => $buyer->id,
            'target' => $target->id,
            'money' => $money,
            'points' => $points,
            'safetymoney' => $safetymoney,
            'safetypoints' => $safetypoints,
            'hospitaltime' => $hospitaltime,
            'timelimit' => self::TIMELIMIT * $hospitaltime,
            'created' => time(),
        ];
        self::AddRecords($data, self::GetDataTable());

        return true;
    }

    /**
 * Functions used to delete a contract.
 */
public function DeleteContract($id)
{
    // Delete the contract from the database
    self::sDelete(self::GetDataTable(), ['id' => $id, 'buyer' => $this->buyer, 'status' => self::AVAILABLE]);

    // Check if the contract was deleted
    if (DBi::$conn->affected_rows <= 0) {
        throw new FailedResult(HITLIST_NOT_ACCESS_TO_DELETE);
    }

    // Handle money and points
    $money = $this->money;
    $points = $this->points;
    $bankMoney = User::SGetBankedMoney($this->buyer);

    // Handle money exceeding bank limit
    if ($bankMoney + $money > DEFAULT_MAX_BANK) {
        $handMoney = $bankMoney + $money - DEFAULT_MAX_BANK;

        if ($handMoney > $money) {
            $handMoney = $money;
        }

        $money = $money - $handMoney;

        if ($handMoney > 0) {
            User::SAddMoney($this->buyer, $handMoney);
        }
    }

    // Revert remaining money to buyer's bank
    if ($money > 0) {
        User::SAddBankMoney($this->buyer, $money);
    }

    // Add points if applicable
    if ($points > 0) {
        $user = UserFactory::getInstance()->getUser($this->buyer);
        $user->AddPoints($points);
        $user->__destruct();
    }

    return true; // Contract deletion and adjustments were successful
}


    /**
     * Function used to accept contract.
     **/
    public function AcceptContract($provider)
    {
        if ($this->buyer == $provider) {
            throw new FailedResult(HITLIST_PROVIDER_BUYER_MUST_DIFFERENT);
        }
        if ($this->target == $provider) {
            throw new FailedResult(HITLIST_PROVIDER_TARGET_MUST_DIFFERENT);
        }
        if ($this->status == self::ACCEPTED) {
            throw new FailedResult(HITLIST_CONTRACT_NOT_AVAILABLE);
        }
        $contracts = self::GetAllByProvider($provider, self::ACCEPTED);
        if (self::MAXPOST <= count($contracts)) { //Check of max limit of contract posted by buyer
            throw new FailedResult(sprintf(HITLIST_PROVIDER_MAX_CONTRACT, self::MAXPOST));
        }
        foreach ($contracts as $mycontract) {
            if ($mycontract->target == $this->target) {
                throw new FailedResult(HITLIST_PROVIDER_HAVE_CONTRACT_ON_TARGET);
            }
        }

        $providerObj = UserFactory::getInstance()->getUser($provider);
        if ($this->safetymoney > $providerObj->money) { //Check buyer has enough bank money to post
            throw new FailedResult(sprintf(HITLIST_CANT_AFFORD_SAFTEY_FEE, $this->safetymoney));
        }
        if ($this->safetymoney > 0) {
            if (!$providerObj->RemoveFromAttribute('money', $this->safetymoney)) {
                throw new FailedResult(sprintf(HITLIST_CANT_AFFORD_SAFTEY_FEE, $this->safetymoney));
            }
        }
        if ($this->safetypoints > $providerObj->points) { //Check buyer has enough bank money to post
            throw new FailedResult(sprintf(HITLIST_CANT_AFFORD_SAFTEY_FEE_POINTS, $this->safetypoints));
        }
        if ($this->safetypoints > 0) {
            if (!$providerObj->RemoveFromAttribute('points', $this->safetypoints)) {
                throw new FailedResult(sprintf(HITLIST_CANT_AFFORD_SAFTEY_FEE_POINTS, $this->safetypoints));
            }
        }
        $updates = ['provider' => $provider,
            'status' => self::ACCEPTED,
            'started' => time(),
        ];

        self::sUpdate(self::$dataTable, $updates, ['id' => $this->id, 'status' => self::AVAILABLE]);

        if (DBi::$conn -> affected_rows <= 0) {
            $providerObj->AddToAttribute('points', $this->safetypoints);
            $providerObj->AddToAttribute('money', $this->safetymoney);
            throw new FailedResult(HITLIST_CONTRACT_NOT_AVAILABLE);
        }

        return true;
    }

    public function Hit(User $provider, &$attacks = [], &$hitdetals = [])
    {
        if ($this->provider != $provider->id) {
            throw new SoftException(HITLIST_NOT_AUTH_COMPLETE_CONTRACT);
        }
        $timeleft = $this->TimeLeft(); //calculate remaining time for hit the target

        if ($timeleft <= 0) {
            $this->ContractFailed();
        }

        $targetUser = UserFactory::getInstance()->getUser($this->target);

        //$attacks = array();
        //$hitdetals = array();
        $hitdetals['thiswasahit'] = 1;
        try {
            $provider->Attack($targetUser, $attacks, $hitdetals);
        } catch (SuccessResult $e) {
            if (isset($hitdetals['hospitalTime']) && $hitdetals['hospitalTime'] > 0) {
                if ($this->hospitaltime > ($this->hospitaltimedone + $hitdetals['hospitalTime'])) {
                    $this->AddToAttribute('hospitaltimedone', $hitdetals['hospitalTime']);
                } else {
                    $this->ContractCompleted($hitdetals['hospitalTime']);
                }
            }
            throw new SuccessResult($e->getMessage());
        }
    }

    public function TimeLeft()
    {
        return $this->started + (60 * $this->timelimit) - $_SERVER['REQUEST_TIME']; //calculate remaining time for hit the target
    }

    public function ContractFailed()
    {
        $updates = [
            'status' => self::FAILED,
            'finished' => time(),
        ];

        self::sUpdate(self::$dataTable, $updates, ['id' => $this->id]);

        $targetName = User::SGetFormattedName($this->target);

        $money = $this->money;
        $points = $this->points;

        User::SAddBankMoney($this->buyer, $money); //revert money to buyer bank

        $user = UserFactory::getInstance()->getUser($this->buyer);
        $user->AddPoints($points);
        $user->__destruct();
        User::SNotify($this->buyer, sprintf(HITLIST_CONTRACT_FAILED_NOTIFY_BUYER, $targetName, number_format($this->money)), HITLIST_HITMAN_CONTRACT);

        $msg = sprintf(HITLIST_CONTRACT_FAILED_NOTIFY_PROVIDER, $targetName, $this->safetymoney);
        User::SNotify($this->provider, $msg, HITLIST_HITMAN_CONTRACT);

        throw new FailedResult($msg);
    }

    public function ContractCompleted($hospitalTime = 0)
    {
        $updates = [
            'hospitaltimedone' => $this->hospitaltimedone + $hospitalTime,
            'status' => self::COMPLETED,
            'finished' => time(),
        ];

        self::sUpdate(self::$dataTable, $updates, ['id' => $this->id]);

        $targetName = User::SGetFormattedName($this->target);

        User::SNotify($this->buyer, sprintf(HITLIST_CONTRACT_COMPLETED_NOTIFY_BUYER, $targetName, number_format($this->money), $this->points), HITLIST_HITMAN_CONTRACT);

        //$money = (int)( $this->money * ((100 - self::BRIBEMONEY)/100) ); //cut the bribe fee from rewarded money
        $bribe = (int) (($this->money * self::BRIBEMONEY) / 100); // calculate the bribe money
        $money = $this->money - $bribe; //cut the bribe fee from rewarded money
        $moneyWon = $this->safetymoney + $money;

        User::SAddMoney($this->provider, $moneyWon);

        $points = $this->points - (int) (($this->points * self::BRIBEPOINTS) / 100); // calculate the bribe money
        $inicial = $points;
        $points += $this->safetypoints;
        $user = UserFactory::getInstance()->getUser($this->provider);
        $user->AddPoints($points);
        $user->__destruct();
        $bribepoints = (int) (($this->points * self::BRIBEPOINTS) / 100);
        $msg = sprintf(HITLIST_CONTRACT_COMPLETED_NOTIFY_PROVIDER, $targetName, $this->safetymoney, $this->safetypoints, number_format($money), $inicial, number_format($bribe), $bribepoints);
        User::SNotify($this->provider, $msg, HITLIST_HITMAN_CONTRACT);

        throw new SuccessResult($msg, 'contract_completed');
    }

    /**checkes provider has any contract on target, if yes then contract id is returned else false returned
     */
    public static function HasContract($provider, $target)
    {
        return MySQL::GetSingle('SELECT id FROM ' . self::$dataTable . ' WHERE provider = ' . $provider . ' AND target = ' . $target . ' AND status = ' . self::ACCEPTED);
    }

    public static function IsUserTarget($target)
    {
        return MySQL::GetSingle('SELECT id FROM ' . self::$dataTable . ' WHERE target = ' . $target . ' AND status = ' . self::ACCEPTED);
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
            'target',
            'money',
            'points',
            'safetymoney',
            'safetypoints',
            'hospitaltime',
            'hospitaltimedone',
            'timelimit',
            'created',
            'started',
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
    protected static function XGetAll($where, $status, $orderby = '', $sort = '', $extraFields = '')
    {
        $objs = [];

        if (empty($orderby)) {
            $orderby = 'created';
        }

        $query = 'SELECT hitman.*, username as targetName ' . $extraFields . ' FROM hitman , grpgusers WHERE hitman.target = grpgusers.id ';

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
            $obj->formattedTargetName = User::SGetFormattedName($obj->target);
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
    /**
     * Generate the function comment for the createContractTable function.
     *
     * @param array $contracts The array of contracts.
     * @return HTMLTable The generated HTML table.
     */
    function createContractTable($contracts) {
        $table = new HTMLTable('contracts');
        $table->addForm('myform');
        $table->addHiddenField('action', 'accept_contract');
        $table->addHiddenField('contract_id', '');
        $table->addSerialNoHeaderColumn();
        $table->addHeaderColumn('formattedTargetName', HITLIST_TARGET, [
            'sortable' => true,
            'field' => 'targetName',
            'align' => 'left',
        ]);
        $table->addHeaderColumn('hospitaltime', HITLIST_HP_TIME_REQUIRED, [
            'sortable' => true,
        ]);
        $table->addHeaderColumn('formatedMoney', HITLIST_MONEY_REWARDED, [
            'sortable' => true,
            'align' => 'right',
            'field' => 'money',
        ]);
        $table->addHeaderColumn('safetymoney', HITLIST_SAFETY_FEE, [
            'sortable' => true,
            'align' => 'right',
            'type' => 'money',
        ]);
        $table->addHeaderColumn('points', HITLIST_POINTS_REWARDED, [
            'sortable' => true,
            'align' => 'right',
            'field' => 'points',
        ]);
        $table->addHeaderColumn('safetypoints', HITLIST_POINTS_FEE, [
            'sortable' => true,
            'align' => 'right',
            'field' => 'safetypoints',
        ]);
        $table->addHeaderColumn('timelimit', HITLIST_TIME_LIMIT, [
            'sortable' => true,
        ]);
        $table->addHeaderColumn('action', COM_ACTION);
        $table->addRowData($contracts);
        $table->setPaginator(HitList::$paginator);
        $table->setNoDataMessage(HITLIST_NO_ACTIVE_CONTRACTS);
    
        return $table;
    }
    /**
     * Handles exceptions and displays appropriate messages.
     *
     * @param mixed $exception The exception to be handled.
     * @return void
     */
    function handleException($exception) {
        if ($exception instanceof SuccessResult) {
            echo HTML::ShowSuccessMessage($exception->getMessage());
        } elseif ($exception instanceof FailedResult) {
            echo HTML::ShowFailedMessage($exception->getMessage());
        } elseif ($exception instanceof SoftException) {
            echo HTML::ShowErrorMessage($exception->getMessage());
            require_once 'footer.php';
            die();
        }
    }
}
