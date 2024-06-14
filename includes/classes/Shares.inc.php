<?php

class Shares extends BaseObject
{
    public static $idField = 'userid';
    public static $dataTable = 'shares';

    public function __construct($id)
    {
        parent::__construct($id);
    }

    /**
     * Retrieve all shares for a specific company.
     *
     * @param int $companyId
     *
     * @return array
     */
    public static function GetSharesForCompany(int $companyId)
    {
        return parent::GetAll(self::GetDataTableFields(), self::$dataTable, 'companyid = ' . $companyId, false, false, 'amount', 'desc');
    }

    public static function GetSharesForCompanyUser(int $companyId, int $userId)
    {
        return parent::GetAll(self::GetDataTableFields(), self::$dataTable, 'companyid = ' . $companyId . ' AND userid = ' . $userId, false, false, 'amount', 'desc');
    }

    public static function GetPurchaseAmount(int $userId, int $companyId, int $stockAmount)
    {
        $stockLogs = self::createQueryBuilder()
            ->select(['unit_price', 'amount', 'action', 'time'])
            ->from('stocklogs')
            ->where('user_id = :userId')
            ->andWhere('stock_id = :companyId')
            ->setParameter('userId', $userId)
            ->setParameter('companyId', $companyId)
            ->orderBy('time', 'DESC')
            ->execute();

        $totalAmountRemaining = $stockAmount;
        $totalPurchasePrice = 0;
        foreach ($stockLogs->fetchAll() as $stockLog) {
            if ($totalAmountRemaining <= 0) {
                break;
            }

            if ($stockLog['action'] === 'Buy') {
                if ($stockLog['amount'] > $totalAmountRemaining) {
                    $stockLog['amount'] = $totalAmountRemaining;
                }
                $totalAmountRemaining -= $stockLog['amount'];
                $totalPurchasePrice += ($stockLog['amount'] * $stockLog['unit_price']) * 1.1;
            } elseif ($stockLog['action'] === 'Sell') {
                if ($totalAmountRemaining + $stockLog['amount'] > $stockAmount) {
                    $stockLog['amount'] = $stockAmount - $totalAmountRemaining;
                }
                $totalAmountRemaining += $stockLog['amount'];
                $totalPurchasePrice -= $stockLog['amount'] * $stockLog['unit_price'];
            }
        }

        return $totalPurchasePrice;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'userid',
            'companyid',
            'amount',
        ];
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }
}
