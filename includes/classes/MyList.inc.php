<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class MyList extends BaseObject
{
    public static $idField = 'id'; //id field
    public static $dataTable = 'MyList'; // table implemented

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

    public static function manage($env, $user_class)
    {
        $proc = [];
        MyList::$usePaging = false;

        $MyList1 = MyListNames::getAll($user_class);

        foreach ($MyList1 as $key => $elem) {
            $elem->action = '<a class="link" onclick="seeList(' . $elem->id . ')"><img src="images/buttons/eye.png" title="view" ></a>&nbsp;&nbsp;<a class="link" onclick="RemoveList(' . $elem->id . ')"><img src="images/buttons/delete.png" title="Remove"></a>';
            $elem->Number = MyListNames::NumberOfElements($user_class, $elem->id);
        }

        $table = new HTMLTable('MyList1');
        $table->addForm('myform', 'post');
        $table->addHiddenField('action', MANAGE);
        /* add header coulmns **/
        $table->addHeaderColumn('Name', LISTS_NAMES, ['align' => 'left', 'width' => '40%']);
        $table->addHeaderColumn('Number', NUMBER_ELEMENTS, ['align' => 'center', 'width' => '20%']);
        $table->addHeaderColumn('action', COM_ACTION, ['align' => 'center', 'width' => '20%']);
        $table->addRowData($MyList1);
        $table->setNoDataMessage('No lists found.'); //Set message if no data returned from database
        $proc['Table'] = $table;

        return $proc['Table'];
    }

    public static function fields($user, $user_class)
    {
        $str = [];
        $user = UserFactory::getInstance()->getUser($user);
        $val = MyList::canbeAttacked(UserFactory::getInstance()->getUser($user_class), $user);

        $str['online'] = $user->GetField('onlineStatus');
        $str['money'] = number_format($user->money);
        $str['lvl'] = $user->level;
        $str['pm'] = '<a href="pms.php?to=' . $user->id . '" >' . MAIL_SEND_PMAIL . '</a>';
        $str['attack'] = '<a href="attack.php?attack=' . $user->id . '" ><font color="' . ($val == true ? 'darkgreen' : 'darkred') . '" >' . COM_ATTACK . '</font> </a>';
        $str['mug'] = '<a href="index.php?mug=' . $user->id . '"  > <font color="' . ($val == true ? 'darkgreen' : 'darkred') . '" > ' . COM_MUG . '</font></a>';

        return $str;
    }

    public static function mylist($env, $user_class)
    {
        $proc = [];
        self::$usePaging = true;
        $MyList = self::GetAllByList($user_class, $env['list'], $_GET['oby'], $_GET['sort']);
        $paginator = self::$paginator;
        self::$usePaging = false;

        foreach ($MyList as $key => $elem) {
            $str = MyList::fields($elem->element, $user_class);
            $str['remove'] = '<a class="link" onClick="RemoveElement(' . $elem->element . ',' . $env['list'] . ')" >' . COM_REMOVE . '</a>';
            $elem->action = $str['pm'] . '&nbsp;|&nbsp;' . $str['remove'] . '&nbsp;|&nbsp;' . $str['attack'] . '&nbsp;|&nbsp;' . $str['mug'] . '</td></tr>';
            // $elem->money=$str['money'];
            $elem->status = $str['online'];
            $elem->lvl = $str['lvl'];
        }

        $table = new HTMLTable('MyList');
        $table->addForm('myform');
        $qryStr = 'list=' . $env['list'] . '&action=' . VIEW;
        //$paginator = MyList::$paginator;
        /* add header coulmns **/

        $table->addHeaderColumn('element', ucwords(COM_USERNAME), ['sortable' => true,'field' => 'username', 'align' => 'left', 'type' => 'user', 'width' => '30%','qryStr' => $qryStr]);
        $table->addHeaderColumn('money', COM_MONEY_M, ['sortable' => true, 'align' => 'right',  'type' => 'money', 'width' => '10%','qryStr' => $qryStr]);
        $table->addHeaderColumn('level', COM_LEVEL, ['sortable' => true, 'align' => 'right',  'width' => '10%','qryStr' => $qryStr]);
        $table->addHeaderColumn('status', COM_STATUS, ['sortable' => true, 'field' => 'lastactive', 'align' => 'right',  'width' => '10%','qryStr' => $qryStr]);
        $table->addHeaderColumn('action', COM_ACTION, ['width' => '40%']);
        $table->addRowData($MyList);
        $table->setPaginator($paginator);
        $table->setNoDataMessage('No inmates'); //Set message if no data returned from database
        $proc['Table'] = $table;

        return $proc['Table'];
    }

    public static function GetAllByList($user_id, $list, $orderby = '', $sort = '')
    {
        $order = '';
        if (!empty($orderby)) {
            $order = ' ORDER BY ' . $orderby . ' ' . $sort;
        }

        $query = 'SELECT ml.*, u.money, u.level, u.lastactive, u.username FROM MyList ml, grpgusers u WHERE u.id = ml.element AND ml.user=' . $user_id . ' AND ml.listNumber=' . $list . ' ' . $order;

        return self::GetPaginationResults($query);
    }

    public static function Remove($user, $listNumber, $id)
    {
        $query = "select count(id) num from MyList where user=$user and listNumber=$listNumber and element=$id";
        $res = DBi::$conn->query($query);
        $obj = mysqli_fetch_object($res);
        if ($obj->num == 0) {
            throw new SoftException(LIST_NO_ENTRANCE);
        }
        $query = "delete from MyList where user=$user and listNumber=$listNumber and element=$id";
        $res = DBi::$conn->query($query);
    }

    public static function addElement($user, $listNumber, $id)
    {
        if ($user == $id) {
            throw new  FailedResult(LIST_CANNOT_ADD_YOURSELF);
        }
        try {
            $users = UserFactory::getInstance()->getUser($id);
        } catch (CheatingException $ex) {
            throw new  FailedResult(USER_DONT_EXIST);
        }
        $query = "select id from MyListNames where user=$user and id=$listNumber";
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            throw new  FailedResult(NOT_LIST);
        }
        $query = "select count(id) num from MyList where user=$user and listNumber=$listNumber and element=$id";
        $res = DBi::$conn->query($query);
        $obj = mysqli_fetch_object($res);
        if ($obj->num == 0) {
            DBi::$conn->query('insert into MyList (user,listNumber,element) values(' . $user . ',' . $listNumber . ',\'' . $id . '\')');
        } else {
            throw new  FailedResult(USER_ALREADY_EXIST);
        }
    }

    protected static function GetDataTableFields()
    {
        return [
                self::$idField,
                'user',
                'listNumber',
                'element',
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
