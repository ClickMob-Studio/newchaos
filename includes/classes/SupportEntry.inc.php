<?php

class SupportEntry extends BaseObject
{
    const EPRIVATE = 1;
    const EPUBLIC = 0;

    public static $idField = 'id';
    public static $dataTable = 'support_entries';

    protected static $statusText = [
                                                self::EPUBLIC => 'Public',
                                                self::EPRIVATE => 'Private',
                                            ]; // Status text

    public function GetStatusText()
    {
        return self::$statusText;
    }

    public function IsAccessibleTo(User $user)
    {
        $thread = new SupportThread($this->thread);

        if ($user->id != $this->author && !$thread->IsAccessibleToSupportUser($user->GetSupportStatus())) {
            return false;
        }

        if ($this->status == self::EPRIVATE) {
            try {
                $sUser1 = new SupportUser($user->id);
                $sUser2 = new SupportUser($this->author);

                if ($sUser1->srid < $sUser2->srid) {
                    return false;
                }
            } catch (SoftException $e) {
                return false;
            }
        }

        return true;
    }

    public static function GetMostRecentForThread($tid, $authorId = 0)
    {
        if ($authorId == 0) {
            $res = DBi::$conn->query('SELECT `' . implode('`, `', self::GetDataTableFields()) . '` FROM `' . self::$dataTable . '` WHERE `thread` = \'' . $tid . '\' ORDER BY `creationdate` DESC LIMIT 1');
        } else {
            $res = DBi::$conn->query('SELECT se.`' . implode('`, se.`', self::GetDataTableFields()) . '` FROM `' . self::$dataTable . '` se  WHERE `thread` = \'' . $tid . '\' AND status =0 ORDER BY `creationdate` DESC LIMIT 1');
        } // private and user is support user.

        //$res = DBi::$conn->query('SELECT se.`'.implode('`, se.`', self::GetDataTableFields()).'` FROM `'.self::$dataTable.'` se  LEFT JOIN support_users su ON se.author = su.id WHERE `thread` = \''.$tid.'\' AND ( status =0  OR ( status = 1 AND `srid` <= (SELECT `srid` FROM support_users WHERE id = '.$authorId.'))) ORDER BY `creationdate` DESC LIMIT 1'); // for support user levels

        if ($res->num_rows == 0) {
            return null;
        }

        return mysqli_fetch_object($res);
    }

    public static function GetAllOpenedForThread($tid, User $user)
    {
        $i = 0;
        $objs = [];
        $res = DBi::$conn->query('SELECT `' . implode('`, `', self::GetDataTableFields()) . '` FROM `' . self::$dataTable . '` WHERE `thread` = \'' . $tid . '\' ORDER BY `creationdate` DESC');
        if (mysqli_num_rows($res) == 0) {
            return null;
        }

        while ($obj = mysqli_fetch_object($res)) {
            if (($obj->status == self::EPUBLIC) || ($obj->status == self::EPRIVATE && $user->IsSupportUser())) { // && $user->GetSupportStatus()->HasUserAccess($obj->author)
                $objs[$i++] = $obj;
            }
        }

        return $objs;
    }

    public static function sAdd($tid, $authorId, $text, $status = self::EPUBLIC, $lang = DEFAULT_LANGUAGE)
    {
        DBi::$conn->query('INSERT INTO `' . self::$dataTable . '` (`thread`, `author`, `text`, `creationdate`, `status`, `lang`) VALUES (\'' . $tid . '\', \'' . $authorId . '\',\'' . $text . '\', \'' . time() . '\', \'' . $status . '\', \'' . $lang . '\')');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(SUGGESTION_THREAD_ENTRY_CANT_ADDED, SUPPORT);
        }

        return DBi::$conn -> insert_id;
    }

    public function Delete(User $user)
    {
        $thread = new SupportThread($this->thread);
        if ($thread->IsAccessibleToSupportUser($user->GetSupportStatus()) === false
            && $this->IsAccessibleTo($user) === false) {
            throw new SoftException(SUGGESTION_CANT_DEL_ENTRY_NOT_CREATED, SUPPORT);
        }
        DBi::$conn->query('DELETE FROM `' . self::$dataTable . '` WHERE `id`=\'' . $this->id . '\'');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(SUGGESTION_CANT_DELETED, SUPPORT);
        }
        // We verify that it was not the last entry, otherwise, we also delete the thread.
        $entries = self::GetAllOpenedForThread($thread->id, $user);
        if ($entries == null) {
            $thread->Delete($user);
            throw new SuccessResult(SUGGESTION_TICKET_ENTRY_DELETED, SUPPORT);
        }
        $thread->SetAttribute('updatedate', $entries[0]->creationdate);
        $thread->SetAttribute('updateauthor', $entries[0]->author);

        return true;
    }

    public function Reply($authorId, $text, $status = self::EPUBLIC)
    {
        self::sAdd($this->thread, $authorId, $text, $status);
    }

    public function ChangeStatus($eid, $status)
    {
        self::sUpdate(self::$dataTable, ['status' => $status], ['id' => $eid]);

        return true;
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
