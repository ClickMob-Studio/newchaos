<?php

final class GangCell extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'gang_cell';
    private $houseObj = null;

    /**
     * Constructor.
     *
     * @param mixed $id
     *
     * @return GangCell
     */
    public function __construct($id = 0)
    {
        if (!empty($id)) {
            parent::__construct($id);
        }
    }

    /**
     * Get All record.
     *
     * @param mixed $where
     *
     * @return mixed
     */
    public static function GetAll($where = '')
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'cell_id', 'ASC');
    }

    /**
     * Get all record by id.
     *
     * @param mixed $where
     *
     * @return mixed
     */
    public static function GetAllById($where = '')
    {
        return parent::GetAllById(self::GetIdentifierFieldName(), self::GetDataTableFields(), self::GetDataTable(), $where);
    }

    /**
     * Return all reocrds from given gang.
     *
     * @param number $gangId
     *
     * @return mixed
     */
    public static function GetAllForGang($gangId)
    {
        return self::GetAll('gang_id = \'' . $gangId . '\'');
    }

    /**
     * Return all reocrds for given gang by id.
     *
     * @param number $gangId
     *
     * @return mixed
     */
    public static function GetAllForGangById($gangId)
    {
        return self::GetAllById('gang_id = \'' . $gangId . '\'');
    }

    public static function GetForLoanedUser($userid)
    {
        $objs = self::GetAll('loan_id = \'' . $userid . '\'');

        if (empty($objs)) {
            return null;
        }

        return $objs[0];
    }

    /**
     * Get all houses for given user.
     *
     * @param number $userId
     *
     * @return mixed
     */
    public static function GetAllForUser($userId)
    {
        return self::GetAll('buyer_id = \'' . $userId . '\'');
    }

    /**
     * Add house to gang cell.
     *
     * @param number $cellId
     */
    public static function Add(User $user, House $cell)
    {
        $data = [
                  'gang_id' => $user->gang,
                  'buyer_id' => $user->id,
                  'cell_id' => $cell->id,
                ];

        Logs::SAddVaultLog($user->GetGang()->id, $user->id, '<b>' . $cell->name . '</b>', time(), 'Bought', 1);

        parent::AddRecords($data, self::GetDataTable());
    }

    /**
     * Loan house to user.
     */
    public function Loan(User $loanUser, User $user)
    {
        if (!empty($this->loan_id)) {
            throw new FailedResult(GANG_CELL_ALREADY_LOANED);
        }
        if ($loanUser->HasCell()) {
            throw new FailedResult(USER_ALREADY_HAVE_HOUSE);
        }
        if ($loanUser->HasLoanedCell()) {
            throw new FailedResult(USER_ALREADY_LOANED_HOUSE);
        }
        if ($loanUser->gang != $this->gang_id || $user->gang != $this->gang_id) {
            throw new FailedResult(USER_NOT_BELONG_GANG);
        }
        if ($this->GetCell()->security_level > $loanUser->securityLevel) {
            throw new SoftException(sprintf(GANG_CELL_REQ_SEC_LVL, $this->GetCell()->security_level));
        }
        if (!$loanUser->SetAttribute('loanhouse', $this->id)) {
            throw new FailedResult(UNKNOWN_ERROR);
        }
        $this->SetAttribute('loan_id', $loanUser->id);

        $loanUser->Notify(sprintf(LOANARMORY_RECEIVED_LOAN, $this->GetCell()->name, $user->formattedname), GANG_CELL);

        Logs::SAddVaultLog($loanUser->GetGang()->id, $loanUser->id, '<b>' . $this->GetCell()->name . '</b>', time(), 'Loaned', 1);
    }

    /**
     * revocer a house if it is loaned.
     */
    public function Recover(User $user)
    {
        if (!$this->IsLoaned()) {
            return false;
        }

        if ($this->loan_id == $user->id) {
            $loanUser = $user;
        } else {
            $loanUser = UserFactory::getInstance()->getUser($this->loan_id);
        }

        $this->SetAttribute('loan_id', 0);
        $loanUser->SetAttribute('loanhouse', 0);
        $loanUser->SetAttribute('awake', 100);
        $loanUser->loanhouseobj = null;

        $lowerCell = House::GetLowerCell($this->cell_id);

        Logs::SAddVaultLog($loanUser->GetGang()->id, $loanUser->id, '<b>' . $this->GetCell()->name . '</b>', time(), 'RecoveredGC', $lowerCell == null ? 0 : $lowerCell->id);

        if ($lowerCell == null) {
            parent::sDelete(self::GetDataTable(), ['id' => $this->id]);
        } else {
            $this->SetAttribute('cell_id', $lowerCell->id);
        }

        $loanUser->Notify(sprintf(CANG_CELL_LOAN_RESTORED, $this->GetCell()->name, $user->formattedname), GANG_CELL);
    }

    public static function RecoverFromUser(User $loanUser)
    {
        $objs = self::GetAll('loan_id = \'' . $loanUser->id . '\'');

        if (empty($objs)) {
            return;
        }

        $houseObj = $objs[0];
        $house = new GangCell($houseObj->id);

        $house->SetAttribute('loan_id', 0);
        $loanUser->SetAttribute('loanhouse', 0);
        $loanUser->SetAttribute('awake', 100);
        $loanUser->loanhouseobj = null;

        $lowerCell = House::GetLowerCell($house->cell_id);

        Logs::SAddVaultLog($loanUser->GetGang()->id, $loanUser->id, '<b>' . $house->GetCell()->name . '</b>', time(), 'RecoveredGC', $lowerCell == null ? 0 : $lowerCell->id);

        if ($lowerCell == null) {
            parent::sDelete(self::GetDataTable(), ['id' => $house->id]);
        } else {
            $house->SetAttribute('cell_id', $lowerCell->id);
        }

        $loanUser->Notify(sprintf(GANG_CELL_BORROWED_ITEM_RETURNED, $house->GetCell()->name), GANG_CELL);
    }

    /**
     * Returns whether house is loaned or not.
     *
     * @return bool
     */
    public function IsLoaned()
    {
        return $this->loan_id > 0;
    }

    /**
     * Check user is buyer of this house or not.
     *
     * @return bool
     */
    public function IsBuyer(User $user)
    {
        return $this->buyer_id == $user->id;
    }

    /**
     * Return the house object for cell.
     *
     * @return House $houseobj
     */
    public function GetCell()
    {
        if (is_a($this->houseObj, 'House')) {
            return $this->houseObj;
        }

        $this->houseObj = new House($this->cell_id);

        return $this->houseObj;
    }

    /**
     * Delete all houses purchased by user.
     *
     * @return mixed
     */
    public static function DeleteAllForBuyer(User $user)
    {
        /**set null to all loaned houses **/

        $myCells = self::GetAllForUser($user->id);

        foreach ($myCells as $myCell) {
            $gangHouse = new GangCell($myCell->id);
            $gangHouse->Delete($user);
        }

        return true;
    }

    public function Delete(User $user)
    {
        if (!$this->IsBuyer($user)) {
            throw new FailedResult(HOUSE_NOT_HAVE_CELL);
        }
        $newmoney = $this->GetCell()->cost * .75;
        $newmoney = round($newmoney);

        $user->AddToAttribute('bank', $newmoney);

        $sql = 'UPDATE grpgusers SET loanhouse = 0, awake=100 WHERE loanhouse IN (SELECT id FROM gang_cell WHERE buyer_id = \'' . $user->id . '\' AND id = \'' . $this->id . '\')';
        DBi::$conn->query($sql);

        Logs::SAddVaultLog($user->GetGang()->id, $user->id, '<b>' . $this->GetCell()->name . '</b>', time(), 'Sold', 1);

        return parent::sDelete(self::GetDataTable(), ['buyer_id' => $user->id, 'id' => $this->id]);
    }

    /**
     * Get the table name.
     */
    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    /**
     * Get table fields.
     */
    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'gang_id',
            'buyer_id',
            'loan_id',
            'cell_id',
        ];
    }

    /**
     * Get id field.
     */
    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    /**
     * return Class name.
     */
    protected function GetClassName()
    {
        return __CLASS__;
    }
}
