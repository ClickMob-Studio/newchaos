<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class ChatFriends extends BaseObject
{
    public static $idField = 'id'; //id field
    public static $dataTable = 'chatbuddies'; // table implemented

    public function __construct($id = null)
    {
        if ($id > 0) {
            parent::__construct($id);
        }
    }

    public static function canbeAttacked($user, $target)
    {
        if ($user->city != $target->city) {
            return false;
        }
        if ($target->hospital > time()) {
            return false;
        }
        if ($target->gprot > time()) {
            return false;
        }
        if ($target->jail > time()) {
            return false;
        }
        if ($target->hp == 0) {
            return false;
        }
        if ($target->admin > 0) {
            return false;
        }

        return true;
    }

    public static function fields($user, $user_class)
    {
        $str = [];
        $user = UserFactory::getInstance()->getUser($user);
        $val = self::canbeAttacked(UserFactory::getInstance()->getUser($user_class), $user);

        $str['online'] = $user->GetField('onlineStatus');
        $str['money'] = number_format($user->money);
        $str['lvl'] = $user->level;
        $str['pm'] = '<a href="pms.php?to=' . $user->id . '" >' . MAIL_SEND_PMAIL . '</a>';
        $str['attack'] = '<a href="attack.php?attack=' . $user->id . '" ><font color="' . ($val == true ? 'darkgreen' : 'darkred') . '" >' . COM_ATTACK . '</font> </a>';
        $str['mug'] = '<a href="index.php?mug=' . $user->id . '"  > <font color="' . ($val == true ? 'darkgreen' : 'darkred') . '" > ' . COM_MUG . '</font></a>';

        return $str;
    }

    public static function myChatFriends($env, $user_class)
    {
        $proc = [];
        self::$usePaging = true;
        $MyList = self::GetAllByList($user_class, $_GET['oby'], $_GET['sort']);
        $paginator = self::$paginator;
        self::$usePaging = false;

        foreach ($MyList as $key => $elem) {
            $str = self::fields($elem->userid, $user_class);
            $str['remove'] = '<a class="link" onClick="RemoveElement(' . $elem->buddiefriend . ')" >' . COM_REMOVE . '</a>';
            $elem->action = $str['pm'] . '&nbsp;|&nbsp;' . $str['remove'] . '</td></tr>';
            // $elem->money=$str['money'];
            $elem->status = $str['online'];
            $elem->lvl = $str['lvl'];
            if ($elem->userid != $elem->buddy) {
                $elem->request =
                    ($elem->pending == 1 ? "<a href='?accept=" . $elem->buddiefriend . "'>Accept</a> | <a href='?refuse=" . $elem->buddiefriend . "'>Refuse</a>"
                        : ($elem->pending == 2 ? "<span class='Bad'>Refused</span>" : "<span class='Ok'>Accepted</span>"));
            } else {
                $elem->request =
                    ($elem->pending == 1 ? 'Pending'
                        : ($elem->pending == 2 ? "<span class='Bad'>Refused</span>" : "<span class='Ok'>Accepted</span>"));
            }
        }

        $table = new HTMLTable('MyList');
        $table->addForm('myform');
        //$paginator = MyList::$paginator;
        /* add header coulmns **/

        $table->addHeaderColumn('userid', ucwords(COM_USERNAME), ['sortable' => true,'field' => 'username', 'align' => 'left', 'type' => 'user', 'width' => '20%','qryStr' => $qryStr]);
        $table->addHeaderColumn('money', COM_MONEY_M, ['sortable' => true, 'align' => 'right',  'type' => 'money', 'width' => '10%','qryStr' => $qryStr]);
        $table->addHeaderColumn('level', COM_LEVEL, ['sortable' => true, 'align' => 'right',  'width' => '10%','qryStr' => $qryStr]);
        $table->addHeaderColumn('status', COM_STATUS, ['sortable' => true, 'field' => 'lastactive', 'align' => 'right',  'width' => '10%','qryStr' => $qryStr]);
        $table->addHeaderColumn('request', 'Request', ['sortable' => true,  'field' => 'pending','align' => 'right',  'width' => '20%','qryStr' => $qryStr]);
        $table->addHeaderColumn('action', COM_ACTION, ['width' => '20%']);
        $table->addRowData($MyList);
        $table->setPaginator($paginator);
        $table->setNoDataMessage('No inmates'); //Set message if no data returned from database
        $proc['Table'] = $table;

        return $proc['Table'];
    }

    public static function GetAllByList($user_id, $orderby = '', $sort = '')
    {
        $order = '';
        if (!empty($orderby)) {
            $order = ' ORDER BY ' . $orderby . ' ' . $sort;
        }

        $query = 'SELECT ml.*, u.money, u.level, u.lastactive, u.username,u.id as userid, ml.id as buddiefriend FROM ' . self::$dataTable .
        " ml, grpgusers u WHERE (ml.buddy=$user_id and u.id=ml.user ) or (ml.user=$user_id and u.id=ml.buddy )" . ' ' . $order;

        return self::GetPaginationResults($query);
    }

    public static function Remove(User $user, $id)
    {
        $obj = new self($id);
        if ($obj->buddy != $user->id && $obj->user != $user->id) {
            throw new FailedResult("You can't cancel another chat requests");
        }
        $query = 'select count(id) num from ' . self::$dataTable . " where id=$id";
        $res = DBi::$conn->query($query);
        $obj = mysqli_fetch_object($res);
        if ($obj->num == 0) {
            throw new SoftException(LIST_NO_ENTRANCE);
        }
        $query = 'delete from ' . self::$dataTable . " where id=$id";
        $res = DBi::$conn->query($query);
    }

    public static function Accept(User $user, $id)
    {
        $obj = new self($id);
        if ($obj->buddy != $user->id) {
            throw new FailedResult('You are not allowed to accept this invite');
        }
        $obj->SetAttribute('pending', 0);
        Event::Add($obj->user, 'The Soldier' . $user->formattedname . ' has accepted your chat invitation');
    }

    public static function Refuse(User $user, $id)
    {
        $obj = new self($id);
        if ($obj->buddy != $user->id) {
            throw new FailedResult('You are not allowed to refuse this invite');
        }
        $obj->SetAttribute('pending', 2);
        Event::Add($obj->user, 'The Soldier' . $user->formattedname . ' has refuse your chat invitation');
    }

    public static function GetReports()
    {
        $results = [];
        $sql = 'select * from imreports where view=0';
        $rs = DBi::$conn->query($sql);
        while ($row = mysqli_fetch_object($rs)) {
            $results[] = $row;
        }

        return $results;
    }

    public static function MarkReportAsView($report)
    {
        self::sUpdate('imreports', ['view' => 1], ['id' => $report]);
    }

    public static function ReportUser(User $from, $who, $time, $comments)
    {
        $data = [
           'from' => $from->id,
           'who' => $who,
           'time' => $time,
           'comments' => $comments,
        ];

        self::AddRecords($data, 'imreports');
    }

    public static function addElement($user, $id)
    {
        if ($user == $id) {
            throw new  FailedResult(LIST_CANNOT_ADD_YOURSELF);
        }
        try {
            $users = UserFactory::getInstance()->getUser($id);
        } catch (CheatingException $ex) {
            throw new  FailedResult(USER_DONT_EXIST);
        }
        $sql = 'select count(id) as num from ' . self::$dataTable . " where ((buddy=$id and user=$user)or(user=$id and buddy=$user))";
        $res = DBi::$conn->query($sql);
        $obj = mysqli_fetch_object($res);
        if ($obj->num == 0) {
            $data = [
                'buddy' => $id,
                'user' => $user,
                'pending' => 1,
            ];
            self::AddRecords($data, self::$dataTable);
            Event::Add($id, 'You have a new chat invitation at your chat list');
        } else {
            throw new  FailedResult(USER_ALREADY_EXIST);
        }
    }

    protected static function GetDataTableFields()
    {
        return [
                self::$idField,
                'user',
                'buddy',
                'group',
            ];
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected static function GetAllByUserId($user_id, $field, $status = null, $orderby = '', $sort = '', $extraFields = '')
    {
        return self::XGetAll($field . ' = \'' . $user_id . '\'', $status, $orderby, $sort, $extraFields);
    }
}
