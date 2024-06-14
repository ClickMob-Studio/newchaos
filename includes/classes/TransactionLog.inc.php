<?php

/**
 * discription: This class is used to manage transaction logs.
 *
 * @author: Harish<harish282@gmail.com>
 * @name: TransactionLog
 * @package: includes
 * @subpackage: classes
 * @final: Final
 * @access: Public
 * @copyright: icecubegaming <http://www.icecubegaming.com>
 */
final class TransactionLog extends BaseObject
{
    public static $idField = '';
    public static $dataTable = 'transactionlogs';

    public static function GetAll()
    {
        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable());
    }

    public static function GetAllForUser(User $user, array $order = ['oby' => 'time', 'sort' => 'DESC'])
    {
        if ($order['oby'] == null) {
            $order['oby'] = 'time';
        }
        if ($order['sort'] == null) {
            $order['sort'] = 'DESC';
        }

        return parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), '(`sender` = \'' . $user->id . '\' OR `receiver` = \'' . $user->id . '\' )', false, false, $order['oby'], $order['sort']);
    }

    public static function DeleteAll()
    {
        parent::sDelete(self::GetDataTable());
    }

    public static function CountAll()
    {
        return parent::sCount(self::GetIdentifierFieldName(), self::GetDataTable());
    }

    /**
     * Function used to reset logs for gang.
     *
     * @param $gang Gang
     */
    public static function FillData()
    {
        DBi::$conn->query('TRUNCATE `' . self::GetDataTable() . '`');

        $time = time() - 432000; //120 hours;
        //$sql = 'SELECT s.`idfrom`, s.`idto`, s.`points`, s.`money`, s.`item`, s.`time`, u1.username as sender, u2.username as receiver FROM `sendLogs` s , grpgusers u1, grpgusers u2 WHERE s.`idfrom` = u1.id AND s.`idto` = u2.id AND s.`time` >= \''.$time.'\'';
        $sql = 'SELECT s.`idfrom`, s.`idto`, s.`points`, s.`money`, s.`item`, s.`mining_drone`, s.`credits`, s.`time` FROM `sendLogs` s  WHERE s.`time` >= \'' . $time . '\'';
        $res = DBi::$conn->query($sql);

        if (mysqli_num_rows($res) <= 0) {
            return true;
        }

        $formatedNames = [];

        $coma = '';
        $insertsql = 'INSERT INTO ' . self::GetDataTable() . ' (`sender`, `receiver`, `type`, `transaction`, `time`) VALUES ';
        while ($log = mysqli_fetch_object($res)) {
            if ($log->money > 0) {
                $type = 'Money';
                $transaction = '$' . number_format($log->money);
            } elseif (!empty($log->item)) {
                $type = 'Item';
                $transaction = $log->item;
            } elseif (!empty($log->mining_drone)) {
                $type = 'Drone';
                $transaction = $log->mining_drone;
            } elseif ($log->points > 0) {
                $type = 'Points';
                $transaction = number_format($log->points);
            }
            elseif ($log->credits > 0) {
                $type = 'Credits';
                $transaction = number_format($log->credits);
            }

            $insertsql .= $coma . '(\'' . $log->idfrom . '\', \'' . $log->idto . '\', \'' . $type . '\', \'' . $transaction . '\', \'' . $log->time . '\') ';
            $coma = ', ';
        }

        DBi::$conn->query($insertsql);

        return true;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            'sender',
            'receiver',
            'type',
            'transaction',
            'time',
        ];
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    public static function LogPacks(User $buyer, $pack){
        //inert into pack logs
        $text = $pack . ' - ' . $buyer->credits . ' credits left';
        $sql = "INSERT INTO `packlogs` (`buyer`, `pack`, `time`) VALUES ('".$buyer->id."', '".$text."', '".time()."')";
        DBi::$conn->query($sql);
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }
}
