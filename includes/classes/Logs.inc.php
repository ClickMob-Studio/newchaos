<?php

final class Logs
{
    private function __construct()
    {
    }

    public static function AddBlackjackLog($time, $bet, $winnings, $outcome, $uid)
    {
        return DBi::$conn->query('INSERT INTO `logs_casino_blackjack` (`cTime`, `cAmount`, `cWinnings`, `cOutcome`, `uID`) VALUES (' . $time . ', ' . $bet . ', ' . $winnings . ', ' . $outcome . ', ' . $uid . ')');
    }

    public static function sGetVaultLogs($gangId, $page)
    {
        $objs = [];
        $timerun = time();
        $threedays = $timerun - 259200;
        $totalRecords = self::sGetTotalVaultLogs($gangId);
        $paginator = new Paginator();
        Paginator::$recordsOnPage = 50;
        $paginator->setPageVariableName('next');
        $paginator->setRecordsPerPage(Paginator::$recordsOnPage);
        $paginator->setTotalRecords($totalRecords);
        $query = 'SELECT `id`, `gangid`,`userid`, `action`, time, `action_type`, `action_value`
												FROM `vaultlog`
												WHERE
												`gangid` = \'' . $gangId . '\'  AND
												`time`>' . $threedays .
                                                ' ORDER BY `time` desc ';

        $query = $paginator->getLimitQuery($query);
        $res = DBi::$conn->query($query);

        while ($obj = mysqli_fetch_object($res)) {
            $strAction = '';
            switch ($obj->action_type) {
                case 'ChangeName':

                    $strAction = '<span style="color:red">' . COM_WITHDRAW . '</span> <b>' . sprintf(GANG_VAULT_LOG_CHANGENAME, $obj->action_value);
                    break;

                case 'Recovered':

                    $arrAction = explode('|@|', $obj->action);
                    $strAction = '<span style="color:darkgreen">' . COM_RECOVERED . '</span> ' . sprintf(GANG_VAULT_LOG_RECOVERED, '<b>' . $arrAction[0] . '</b>', stripslashes(html_entity_decode($arrAction[1])));
                    break;

                case 'DepositArm':

                    $strAction = '<span style="color:darkgreen">' . COM_DEPOSIT . '</span> ' . sprintf(GANG_VAULT_LOG_DEPOSITARM, $obj->action_value, ' <b>' . $obj->action . '</b>');
                    break;

                case 'WithdrawArm':

                    $strAction = '<span style="color:red">' . COM_WITHDRAW . '</span> ' . sprintf(GANG_VAULT_LOG_WITHDRAWARM, $obj->action_value, ' <b>' . $obj->action . '</b>');
                    break;

                case 'DepositM':

                    $strAction = '<span style="color:darkgreen">' . COM_DEPOSIT . '</span> ' . sprintf(GANG_VAULT_LOG_DEPOSITM, '<b>$' . $obj->action_value . '</b>');
                    break;

                case 'DepositP':

                    $strAction = '<span style="color:darkgreen">' . COM_DEPOSIT . '</span> ' . sprintf(GANG_VAULT_LOG_DEPOSITP, '<b>' . $obj->action_value . '</b>');
                    break;

                case 'WithdrawM':

                    $strAction = '<span style="color:red">' . COM_WITHDRAW . '</span> ' . sprintf(GANG_VAULT_LOG_WITHDRAWM, '<b>$' . $obj->action_value . '</b>');
                    break;

                case 'WithdrawP':

                    $strAction = '<span style="color:red">' . COM_WITHDRAW . '</span> ' . sprintf(GANG_VAULT_LOG_WITHDRAWP, '<b>' . $obj->action_value . '</b>');
                    break;

                case 'Bought':
                    $strAction = sprintf(GANG_VAULT_LOG_BOUGHT, '<span style="color:darkgreen">' . COM_BOUGHT . '</span> ', '<b>' . $obj->action . '</b>');
                    break;
                case 'Sold':
                    $strAction = sprintf(GANG_VAULT_LOG_SOLD, '<span style="color:red">' . COM_SOLD . '</span> ', '<b>' . $obj->action . '</b>');
                    break;
                case 'Loaned':
                    $strAction = sprintf(GANG_VAULT_LOG_LOANED, '<span style="color:red">' . COM_LOANED . '</span> ', '<b>' . $obj->action . '</b>', User::SGetFormattedName($obj->userid));
                    break;
                case 'RecoveredGC':
                    if ($obj->action_value <= 0) {
                        $strAction = sprintf(GANG_VAULT_LOG_RECOVERED_GC_1, '<span style="color:darkgreen">' . COM_RECOVERED . '</span> ', '<b>' . $obj->action . '</b>', User::SGetFormattedName($obj->userid) . 'damn', '<b>' . $obj->action . '</b>');
                    } else {
                        $house = new House($obj->action_value);
                        $strAction = sprintf(GANG_VAULT_LOG_RECOVERED_GC_2, '<span style="color:darkgreen">' . COM_RECOVERED . '</span> ', '<b>' . $obj->action . '</b>', User::SGetFormattedName($obj->userid), '<b>' . $obj->action . '</b>', '<b>' . $house->name . '</b>');
                    }
                    break;
                default:
                    $strAction = $obj->action;
            }
            $obj->strAction = $strAction;
            if ($obj->userid == 0) {
                $obj->intemate = 'GangCrime';
            } else {
                $obj->intemate = User::SGetFormattedName($obj->userid);
            }
            $obj->formattedTime = date('F d, Y g:i:sa', $obj->time);
            $objs[] = $obj;
        }
        $table = new HTMLTable('Vault Log');
        /* add header coulmns **/
        $table->addHeaderColumn('intemate', COM_INMATE, ['sortable' => false, 'align' => 'left', 'width' => '20%']);
        $table->addHeaderColumn('strAction', COM_LOG, ['sortable' => false, 'align' => 'left',  'width' => '20%']);
        $table->addHeaderColumn('formattedTime', COM_TIME, ['sortable' => false, 'align' => 'left', 'width' => '20%']);

        /* add records to show **/
        $table->addRowData($objs);
        //		$table->setPaginator($paginator); //Set paginator object
            $table->setNoDataMessage('No vault logs found.'); //Set message if no data returned from database

        return $table;
    }
    public static function staffGetVaultLogs($gangId, $page)
    {
        $objs = [];
        $timerun = time();
        $totalRecords = self::sGetTotalVaultLogs($gangId);
        $paginator = new Paginator();
        Paginator::$recordsOnPage = 50;
        $paginator->setPageVariableName('next');
        $paginator->setRecordsPerPage(Paginator::$recordsOnPage);
        $paginator->setTotalRecords($totalRecords);
        $query = 'SELECT `id`, `gangid`,`userid`, `action`, time, `action_type`, `action_value`
												FROM `vaultlog`
												WHERE
												`gangid` = \'' . $gangId . '\'
												 ORDER BY `time` desc ';

        $query = $paginator->getLimitQuery($query);
        $res = DBi::$conn->query($query);

        while ($obj = mysqli_fetch_object($res)) {
            $strAction = '';
            switch ($obj->action_type) {
                case 'ChangeName':

                    $strAction = '<span style="color:red">' . COM_WITHDRAW . '</span> <b>' . sprintf(GANG_VAULT_LOG_CHANGENAME, $obj->action_value);
                    break;

                case 'Recovered':

                    $arrAction = explode('|@|', $obj->action);
                    $strAction = '<span style="color:darkgreen">' . COM_RECOVERED . '</span> ' . sprintf(GANG_VAULT_LOG_RECOVERED, '<b>' . $arrAction[0] . '</b>', stripslashes(html_entity_decode($arrAction[1])));
                    break;

                case 'DepositArm':

                    $strAction = '<span style="color:darkgreen">' . COM_DEPOSIT . '</span> ' . sprintf(GANG_VAULT_LOG_DEPOSITARM, $obj->action_value, ' <b>' . $obj->action . '</b>');
                    break;

                case 'WithdrawArm':

                    $strAction = '<span style="color:red">' . COM_WITHDRAW . '</span> ' . sprintf(GANG_VAULT_LOG_WITHDRAWARM, $obj->action_value, ' <b>' . $obj->action . '</b>');
                    break;

                case 'DepositM':

                    $strAction = '<span style="color:darkgreen">' . COM_DEPOSIT . '</span> ' . sprintf(GANG_VAULT_LOG_DEPOSITM, '<b>$' . $obj->action_value . '</b>');
                    break;

                case 'DepositP':

                    $strAction = '<span style="color:darkgreen">' . COM_DEPOSIT . '</span> ' . sprintf(GANG_VAULT_LOG_DEPOSITP, '<b>' . $obj->action_value . '</b>');
                    break;

                case 'WithdrawM':

                    $strAction = '<span style="color:red">' . COM_WITHDRAW . '</span> ' . sprintf(GANG_VAULT_LOG_WITHDRAWM, '<b>$' . $obj->action_value . '</b>');
                    break;

                case 'WithdrawP':

                    $strAction = '<span style="color:red">' . COM_WITHDRAW . '</span> ' . sprintf(GANG_VAULT_LOG_WITHDRAWP, '<b>' . $obj->action_value . '</b>');
                    break;

                case 'Bought':
                    $strAction = sprintf(GANG_VAULT_LOG_BOUGHT, '<span style="color:darkgreen">' . COM_BOUGHT . '</span> ', '<b>' . $obj->action . '</b>');
                    break;
                case 'Sold':
                    $strAction = sprintf(GANG_VAULT_LOG_SOLD, '<span style="color:red">' . COM_SOLD . '</span> ', '<b>' . $obj->action . '</b>');
                    break;
                case 'Loaned':
                    $strAction = sprintf(GANG_VAULT_LOG_LOANED, '<span style="color:red">' . COM_LOANED . '</span> ', '<b>' . $obj->action . '</b>', User::SGetFormattedName($obj->userid));
                    break;
                case 'RecoveredGC':
                    if ($obj->action_value <= 0) {
                        $strAction = sprintf(GANG_VAULT_LOG_RECOVERED_GC_1, '<span style="color:darkgreen">' . COM_RECOVERED . '</span> ', '<b>' . $obj->action . '</b>', User::SGetFormattedName($obj->userid) . 'damn', '<b>' . $obj->action . '</b>');
                    } else {
                        $house = new House($obj->action_value);
                        $strAction = sprintf(GANG_VAULT_LOG_RECOVERED_GC_2, '<span style="color:darkgreen">' . COM_RECOVERED . '</span> ', '<b>' . $obj->action . '</b>', User::SGetFormattedName($obj->userid), '<b>' . $obj->action . '</b>', '<b>' . $house->name . '</b>');
                    }
                    break;
                default:
                    $strAction = $obj->action;
            }
            $obj->strAction = $strAction;
            if ($obj->userid == 0) {
                $obj->intemate = 'GangCrime';
            } else {
                $obj->intemate = User::SGetFormattedName($obj->userid);
            }
            $obj->formattedTime = date('F d, Y g:i:sa', $obj->time);
            $objs[] = $obj;
        }
        $table = new HTMLTable('Vault Log');
        /* add header coulmns **/
        $table->addHeaderColumn('intemate', COM_INMATE, ['sortable' => false, 'align' => 'left', 'width' => '20%']);
        $table->addHeaderColumn('strAction', COM_LOG, ['sortable' => false, 'align' => 'left',  'width' => '20%']);
        $table->addHeaderColumn('formattedTime', COM_TIME, ['sortable' => false, 'align' => 'left', 'width' => '20%']);

        /* add records to show **/
        $table->addRowData($objs);
        //		$table->setPaginator($paginator); //Set paginator object
        $table->setNoDataMessage('No vault logs found.'); //Set message if no data returned from database

        return $table;
    }

    public static function sGetTotalVaultLogs($gangId)
    {
        $objs = [];
        $timerun = time();
        $threedays = $timerun - 259200;
        $strSQL = 'SELECT  count(0) as total
												FROM `vaultlog`
												WHERE
												`gangid` = \'' . $gangId . '\'  AND
												`time`>' . $threedays .
                                                ' ORDER BY `time` desc';

        $res = DBi::$conn->query($strSQL);
        $arrTotal = mysqli_fetch_array($res);

        return $arrTotal['total'];
    }

    public static function SAddVaultLog($gangid, $userid, $action, $time, $action_type = 'Other', $action_value = 0)
    {
        if ($userid != 0) {
            $query = "INSERT INTO `vaultlog`
							(`gangid`,`userid`,`action`,`time`,`action_type`,`action_value`)
					VALUES  ('" . $gangid . "','" . $userid . "', '" . $action . "', '" . $time . "','" . $action_type . "','" . $action_value . "')";
        } else {
            $query = "INSERT INTO `vaultlog` (`gangid`, `action`,`time`,`action_type`,`action_value`)
						 			  VALUES ('" . $gangid . "', '" . $action . "', '" . $time . "','" . $action_type . "','" . $action_value . "')";
        }

        return DBi::$conn->query($query);
    }

    public static function SAddRealPaymentLog($userid, $for, $time, $type, $moneybefore, $moneyafter)
    {
        return DBi::$conn->query("INSERT INTO `paymentsrealmoney` (`userid`, `for`, `time`,`type`,`moneybefore`,`moneyafter`) VALUES ('" . $userid . "','" . $for . "','" . $time . "', \"" . $type . "\",'" . $moneybefore . "','" . $moneyafter . "')");
    }

    public static function SAddSpyLog($userid, $targetid, $str, $def, $speed, $bank, $points, $age, $armor = '', $weapon = '', $reg = 0)
    {
        return DBi::$conn->query("INSERT INTO `spylog` (`id`, `spyid`, `strength`, `defense`, `speed`, `bank`, `points`, `age`, `armor`, `weapon`, reg) VALUES ('" . $userid . "', '" . $targetid . "', '" . $str . "', '" . $def . "', '" . $speed . "', '" . $bank . "', '" . $points . "', '" . $age . "', '" . $armor . "', '" . $weapon . "', $reg)");
    }

    public static function sGetGangMemberLogs($gangId)
    {
        $objs = [];
        $res = DBi::$conn->query('SELECT `id`, `gangid`, `userid`, `action`, `actionon`, `time`  FROM `gangmemberlog` WHERE `gangid` = \'' . $gangId . '\' ORDER BY `time` desc limit 0,50');
        while ($obj = mysqli_fetch_object($res)) {
            $objs[] = $obj;
        }

        return $objs;
    }

    public static function sGetGangMemberJoinLog($gangId, $userId)
    {
        $res = DBi::$conn->query('SELECT `id`, `gangid`, `userid`, `action`, `actionon`, `time`  FROM `gangmemberlog` WHERE `gangid` = \'' . $gangId . '\' AND `actionon` = \'' . $userId . '\' ORDER BY `time` desc limit 0,50');

        if (mysqli_num_rows($res) <= 0) {
            return null;
        }

        return mysqli_fetch_object($res);
    }

    public static function SAddGangMemberLog($gangid, $userid, $action, $actionon, $time)
    {
        return DBi::$conn->query('INSERT INTO `gangmemberlog` (`gangid`,`userid`,`action`,`actionon`,`time`) VALUES (\'' . $gangid . '\',\'' . $userid . '\',\'' . $action . '\', \'' . $actionon . '\',\'' . $time . '\')');
    }

    public static function SAddUserGangLog($gangid, $userid, $action, $time)
    {
        if (empty($gangid)) {
            return;
        }
        $gangname = Gang::SGetPublicFormattedName($gangid);
        $gangtag = Gang::SGetTagName($gangid);
        $gangname = Utility::SmartEscape($gangname);
        $gangtag = Utility::SmartEscape($gangtag);

        return DBi::$conn->query('INSERT INTO `userganglog` (`gangid`,`userid`,`action`,`gangname`,`gangtag`,`time`) VALUES (\'' . $gangid . '\',\'' . $userid . '\',\'' . $action . '\', \'' . $gangname . '\', \'' . $gangtag . '\',\'' . $time . '\')');
    }

    public static function SAddSendLog($idfrom, $idto, $item, $ip, $seen, $fromPre = 0, $fromPost = 0, $toPre = 0, $toPost = 0)
    {
        return DBi::$conn->query('
          INSERT INTO 
            `sendLogs`  (`idfrom`, `idto`, `item`, `time`,`IP`,`seen`, `from_pre_quantity`, `from_post_quantity`, `to_pre_quantity`, `to_post_quantity`) 
          VALUES 
            (\'' . $idfrom . '\',\'' . $idto . '\',\'' . $item . '\',\'' . time() . '\',\'' . $ip . '\',\'' . $seen . '\',\'' . $fromPre . '\',\'' . $fromPost . '\',\'' . $toPre . '\',\'' . $toPost . '\')
        ');
    }

    public static function sGetCrimeLog($gangid, $userid, $crime)
    {
        $rsResult = DBi::$conn->query('SELECT result FROM `crimelog`  WHERE `crimeid`=' . $crime . ' AND `gangid`=' . $gangid . ' AND userid=' . $userid);
        $arrResult = mysqli_fetch_assoc($rsResult);

        return $arrResult['result'];
    }

    public static function sAddCrimeLog($gangid, $crime, $endtime, $result, $starttime, $userid)
    {
        return DBi::$conn->query('INSERT INTO `crimelog` (`gangid`,`crimeid`,`endtime`,`result`,`starttime`,`userid`)
		VALUES (\'' . $gangid . '\',\'' . $crime . '\',\'' . $endtime . '\',\'' . $result . '\',\'' . $starttime . '\',\'' . $userid . '\')');
    }

    public static function sUpdateCrimeLog($gangid, $crime, $endtime)
    {
        return DBi::$conn->query('UPDATE  `crimelog`
												SET `endtime`=\'' . $endtime . '\',
														`result`=3
												WHERE  `gangid`=' . $gangid . ' AND
																`crimeid`=' . $crime . ' AND
																 `endtime`>' . $endtime);
    }

    public static function sAddGangAtkLog($time, $defGangId, $atkId, $defId, $winner, $atkGangId, $gangexpwon = 0, $moneywon = 0, $atkexp = 0, $atkmoney = 0, $attackers = '')
    {
        if (time() - $defId->lastactive < 900) {
            $status = 1;
        } else {
            $status = 0;
        }
        $jointattack = 0;
        if (!empty($attackers)) {
            $jointattack = 1;
        }

        return DBi::$conn->query('INSERT INTO `ganglog` (`timestamp`, gangid, attacker, defender, winner, gangidatt, `expwon`, `moneywon`, `atkexp`, `atkmoney`, `jointattack`, `attackers`,`status`) VALUES (\'' . $time . '\', ' . $defGangId . ', \'' . $atkId . '\', \'' . $defId->id . '\', \'' . $winner . '\', ' . $atkGangId . ', \'' . $gangexpwon . '\', \'' . $moneywon . '\', \'' . $atkexp . '\', \'' . $atkmoney . '\', \'' . $jointattack . '\', \'' . $attackers . '\',\'' . $status . '\')');
    }

    public static function sAddPointsMarketLog($data)
    {
        return BaseObject::AddRecords($data, 'pointsmarketlogs', 'DELAYED');
    }

    public static function sAddLandMarketLog($data)
    {
        if (!isset($data['gangid'])) {
            $data['gangid'] = 0;
        }

        return BaseObject::AddRecords($data, 'landmarketlogs', 'DELAYED');
    }

    public static function sAddFertilizerMarketLog($data)
    {
        return BaseObject::AddRecords($data, 'fertilizermarketlogs', 'DELAYED');
    }

    public static function sAddItemMarketLog($data)
    {
        return BaseObject::AddRecords($data, 'itemmarketlogs', 'DELAYED');
    }

    public static function sAddRPShopLog($action, $seller, $buyer, $pack, $qty, $money = 0, $points = 0)
    {
        $data = [
                    'action' => $action,
                    'seller' => $seller,
                    'buyer' => $buyer,
                    'time' => time(),
                    'pack' => $pack,
                    'qty' => $qty,
                    'money' => $money,
                    'points' => $points,
                ];

        return BaseObject::AddRecords($data, 'logs_rpshop', 'DELAYED');
    }

    public static function sAddPShopLog($action, $seller, $buyer, $item, $qty, $pshop, $money = 0, $points = 0)
    {
        $data = [
                    'action' => $action,
                    'seller' => $seller,
                    'buyer' => $buyer,
                    'time' => time(),
                    'item' => $item,
                    'qty' => $qty,
                    'pshop' => $pshop,
                    'money' => $money,
                    'points' => $points,
                ];

        return BaseObject::AddRecords($data, 'logs_pshop', 'DELAYED');
    }

    public static function sDeleteOldMarketLogs()
    {
        DBi::$conn->query('DELETE FROM `pointsmarketlogs` WHERE `date` < ' . (time() - 2592000));
        DBi::$conn->query('DELETE FROM `landmarketlogs` WHERE `date` < ' . (time() - 2592000));
        DBi::$conn->query('DELETE FROM `itemmarketlogs` WHERE `date` < ' . (time() - 2592000));
    }

    public static function AddJointAttackLog($gang, $instigator, $target, $participats)
    {
        $data = [
                    'gang' => $gang,
                    'instigator' => $instigator,
                    'target' => $target,
                    'participats' => $participats,
                    'started' => time(),
                ];

        return BaseObject::AddRecords($data, 'logs_rpshop', 'DELAYED');
    }
}
