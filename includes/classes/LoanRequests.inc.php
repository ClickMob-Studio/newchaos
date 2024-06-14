<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class LoanRequests extends BaseObject
{
    public static $idField = 'id'; //id field
    public static $dataTable = 'loanrequests'; // table implemented

    /**
     * Constructor.
     */
    public function __construct($id = null)
    {
        if ($id > 0) {
            parent::__construct($id);
        }
    }
    public static function LoanRequestUser($id)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('userId')
            ->from('loanrequests')
            ->where('id = :id')
            ->setParameter('id', $id)
        ;
        $loanRequests = $queryBuilder->execute()->fetch();
        return UserFactory::getInstance()->getUser($loanRequests['userId']);
    }
    public static function GetAllLoanRequests($gang_id)
    {
        $queryBuilder = BaseObject::createQueryBuilder();
        $queryBuilder
            ->select('id, gangid, userId, itemId')
            ->from('loanrequests')
            ->where('gangid = :gang_id')
            ->setParameter('gang_id', $gang_id)
        ;
        $loanRequests = $queryBuilder->execute()->fetchAll();

        foreach ($loanRequests as $key => $loanRequest) {
            $loanRequests[$key]['item'] = new Item($loanRequest['itemId']);
            $loanRequests[$key]['user'] = UserFactory::getInstance()->getUser($loanRequest['userId']);
        }

        return $loanRequests;
    }

    public static function CreateLoanRequests($gang_id, $user_id, $item_id)
    {
    DBi::$conn->query("INSERT INTO loanrequests (gangid,userid,itemid) VALUES (".$gang_id.", ".$user_id.", ".$item_id.")");
    }

    public static function DeleteLoanRequests($id)
    {
        $res = DBi::$conn->query('delete from loanrequests where id=' . $id);
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
            'gangId',
            'userId',
            'itemId',
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