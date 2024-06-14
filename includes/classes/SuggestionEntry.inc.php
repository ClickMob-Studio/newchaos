<?php

final class SuggestionEntry extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'suggestion_entries';

    public function IsAccessibleTo(User $user)
    {
        if ($user->id != $this->author) {
            return false;
        }

        return true;
    }

    public static function GetMostRecentForThread($tid)
    {
        $res = DBi::$conn->query('SELECT `' . implode('`, `', self::GetDataTableFields()) . '` FROM `' . self::$dataTable . '` WHERE `thread` = \'' . $tid . '\' and status=\'0\' ORDER BY `creationdate` DESC LIMIT 1');
        if (mysqli_num_rows($res) == 0) {
            return null;
        }

        return mysqli_fetch_object($res);
    }

    public static function CountAllOpenedForThread($tid)
    {
        $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `' . self::$dataTable . '` WHERE `thread` = \'' . $tid . '\'');
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    public static function GetAllOpenedForThread($tid, User $user)
    {
        parent::$usePaging = true;
        $i = 0;
        //$objs = array();
        self::$generatePagingQryString = false;
        $where = "`thread` = '" . $tid . "'";
        $res = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'creationdate', 'DESC ');
        if (count($res) == 0) {
            return null;
        }

        //while ($obj = mysqli_fetch_object($res))
        foreach ($res as $k => $obj) {
            if (($obj->status == SupportEntry::EPRIVATE) && !$user->IsSupportUser()) { // && $user->GetSupportStatus()->HasUserAccess($obj->author)
                unset($res[$k]);
            }
        }

        // returning with reindexing
        return array_values($res);
    }

    public static function GetLastOpenedForThread($tid)
    {
        $query = 'SELECT `' . implode('`, `', self::GetDataTableFields()) . '` FROM `' . self::$dataTable . '` WHERE `thread` = \'' . $tid . '\' ORDER BY `creationdate` ASC LIMIT 1';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) == 0) {
            return null;
        }

        return mysqli_fetch_object($res);
    }

    public static function sAdd($tid, $authorId, $text, $status = SupportEntry::EPUBLIC, $lang = DEFAULT_LANGUAGE)
    {
        DBi::$conn->query('INSERT INTO `' . self::$dataTable . '` (`thread`, `author`, `text`, `creationdate`,`status`, `lang`) VALUES (\'' . $tid . '\', \'' . $authorId . '\',\'' . $text . '\', \'' . time() . '\', \'' . $status . '\',  \'' . $lang . '\')');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(SUGGESTION_THREAD_ENTRY_CANT_ADDED, SUGGESTION_THREAD);
        }

        return DBi::$conn -> insert_id;
    }

    public function Delete(User $user)
    {
        $thread = new SuggestionThread($this->thread);

        if ($thread->IsAccessibleToSupportUser($user->GetSupportStatus()) === false
            && $this->IsAccessibleTo($user) === false) {
            throw new SoftException(SUGGESTION_CANT_DEL_ENTRY_NOT_CREATED, SUGGESTION_THREAD);
        }
        DBi::$conn->query('DELETE FROM `' . self::$dataTable . '` WHERE `id`=\'' . $this->id . '\'');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(SUGGESTION_CANT_DELETED, SUGGESTION_THREAD);
        }
        // We verify that it was not the last entry, otherwise, we also delete the thread.
        $entries = self::GetAllOpenedForThread($thread->id, $user);
        if ($entries == null) {
            $thread->Delete($user);
            throw new SuccessResult(SUGGESTION_TICKET_ENTRY_DELETED, SUGGESTION_THREAD);
        }
        $thread->SetAttribute('updatedate', $entries[0]->creationdate);
        $thread->SetAttribute('updateauthor', $entries[0]->author);

        return true;
    }

    public function Reply($authorId, $text)
    {
        self::sAdd($this->thread, $authorId, $text);
    }

    public function sChangeStatus($eid, $status)
    {
        self::sUpdate(self::$dataTable, ['status' => $status], ['id' => $eid]);

        return true;
    }

    public function ChangeStatus($status)
    {
        self::sUpdate(self::$dataTable, ['status' => $status], ['id' => $this->id]);

        return true;
    }

    /**
     * Returns a record object which last not private.
     */
    public function GetLastPublicRecord($threadId)
    {
        $res = parent::GetAll(['author', 'creationdate'], self::GetDataTable(), '`thread`=\'' . $threadId . '\' AND `status` = 0', false, $limit = 1, 'creationdate', 'DESC');
        if (!is_array($res) || count($res) == 0) {
            return null;
        }

        return $res[0];
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'thread',
            'author',
            'text',
            'creationdate',
            'status',
            'lang',
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
