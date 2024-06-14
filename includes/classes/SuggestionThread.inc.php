<?php

final class SuggestionThread extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'suggestion_threads';

    public static function sOpen($authorId, $title, $text, $level, $category = 1, $lang = DEFAULT_LANGUAGE)
    {
        $tid = self::sAdd($authorId, $title, $level, $category, $lang);
        SuggestionEntry::sAdd($tid, $authorId, $text, $lang);

        return $tid;
    }

    public static function sColorizeScore($score)
    {
        if ($score < 0) {
            return '<font color="#CC0000"><b>' . $score . '</b></font>';
        } elseif ($score > 0) {
            return '<font color="#669900"><b>' . $score . '</b></font>';
        }

        return '<font color="#CCCC00"><b>' . $score . '</b></font>';
    }

    public function IsClosed()
    {
        if ($this->closed == 1) {
            return true;
        }

        return false;
    }

    public function HasVoted(User $user)
    {
        $query = 'SELECT `User` FROM `suggestion_threads_votes` WHERE `User` = \'' . $user->id . '\' AND `Thread` = \'' . $this->id . '\'';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) >= 1) {
            return true;
        }

        return false;
    }

    public static function sHasVoted($tid, User $user)
    {
        $query = 'SELECT `User` FROM `suggestion_threads_votes` WHERE `User` = \'' . $user->id . '\' AND `Thread` = \'' . $tid . '\'';
        $res = DBi::$conn->query($query);
        if (mysqli_num_rows($res) >= 1) {
            return true;
        }

        return false;
    }

    public function AddVote(User $user, $vote = 0)
    {
        if ($this->HasVoted($user) === true) {
            throw new FailedResult(SUGGESTION_ALREADY_VOTED);
        }
        DBi::$conn->query('INSERT INTO `suggestion_threads_votes` (`User`, `Thread`, `Vote`) VALUES (\'' . $user->id . '\', \'' . $this->id . '\', \'' . $vote . '\')');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(SUGGESTION_VOTE_CANT_ADDED);
        }
        if ($vote > 0) {
            $this->AddToAttribute('score', $vote);
        } elseif ($vote < 0) {
            $this->ForceRemoveFromAttribute('score', -$vote);
        }

        return true;
    }

    public function DeleteVote(User $user)
    {
        DBi::$conn->query('DELETE FROM `suggestion_threads_votes` WHERE `User` = \'' . $user->id . '\' AND `Thread` = \'' . $this->id . '\' LIMIT 1');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(SUGGESTION_VOTE_CANT_DELETED);
        }

        return true;
    }

    public function IsAccessibleTo(User $user)
    {
        if ($user->id != $this->author
            && $this->IsClosed() === true
            && $this->IsAccessibleToSupportUser($user->GetSupportStatus()) === false) {
            return false;
        }

        return true;
    }

    public function IsAccessibleToSupportUser($suser)
    {
        if ($suser === null) {
            return false;
        } elseif ($suser->GetLevel() < $this->level) {
            return false;
        }

        return true;
    }

    public function ChangeCategory(User $user, $category)
    {
        if ($category <= 0) {
            throw new SoftException('Invalid category.', 'edit');
        } elseif ($user->id != $this->author
            && SupportUser::HasSUserAccess($user->id, $this->level) === false) {
            throw new SoftException(SUGGESTION_CANT_CHANGE_CATEGORY, 'edit');
        }
        $this->SetAttribute('category', $category);

        return true;
    }

    public function Reopen(User $user)
    {
        if ($this->closed == 0) {
            throw new SoftException(SUGGESTION_CANT_REOPEN_OPENED, SUGGESTION_THREAD);
        } elseif ($user->id != $this->author
            && SupportUser::HasSUserAccess($user->id, $this->level) === false) {
            throw new SoftException(SUGGESTION_CANT_REOPEN_NOT_CREATED, SUGGESTION_THREAD);
        }
        $this->SetAttribute('closed', 0);
        $this->SetNullAttribute('closedby');

        return true;
    }

    public function Close(User $user)
    {
        if ($this->closed == 1) {
            throw new SoftException(SUGGESTION_CANT_CLOSE_CLOSED, SUGGESTION_THREAD);
        } elseif ($this->IsAccessibleTo($user) === false) {
            throw new SoftException(SUGGESTION_CANT_CLOSE_NOT_CREATED, SUGGESTION_THREAD);
        }
        $this->SetAttribute('closed', 1);
        $this->SetAttribute('closedby', $user->id);
        $this->SetAttribute('closedate', time());
        if ($this->author != $user->id) {
            $author = UserFactory::getInstance()->getUser($this->author);
            $author->Notify(sprintf(SUGGESTION_YOUR_CLOSED, $this->id), SUGGESTION_THREAD);
        }

        return true;
    }

    public function Promote(User $user)
    {
        if ($user->GetSupportStatus()->HasAccess(2) === false
        && $this->IsAccessibleToSupportUser($user->GetSupportStatus()) === false) {
            throw new SoftException(SUGGESTION_NOT_HAVE_ACCESS, SUGGESTION_THREAD);
        } elseif ($this->level >= 3) {
            throw new SoftException(SUGGESTION_CANNOT_PROMOTE, SUGGESTION_THREAD);
        }
        $promMsg = '';
        if ($this->level == 1) {
            $promMsg = SUGGESTION_REVIEWED_BY_ADMIN;
        } elseif ($this->level == 2) {
            $promMsg = SUGGESTION_INTEGRATED_INTO_GAME;
        }
        User::SNotify($this->author, sprintf(SUGGESTION_PROMOTED_TO_LEVEL, $this->id, ($this->level + 1)) . ' ' . $promMsg, SUGGESTION_THREAD);

        return $this->AddToAttribute('level', 1);
    }

    public function Demote(User $user)
    {
        if ($user->GetSupportStatus()->HasAccess(2) === false
        && $this->IsAccessibleToSupportUser($user->GetSupportStatus()) === false) {
            throw new SoftException(SUGGESTION_NOT_HAVE_ACCESS, SUGGESTION_THREAD);
        } elseif ($this->level <= 1) {
            throw new SoftException(SUGGESTION_CANNOT_DEMOTE, SUGGESTION_THREAD);
        }

        return $this->RemoveFromAttribute('level', 1);
    }

    public static function CountAllOpenedForLevel($level, $uid = null, $search = [])
    {
        $where = '';

        if (isset($search['stitle']) && !empty($search['stitle'])) {
            $where .= ' AND title like  "%' . $search['stitle'] . '%"';
        }

        if (isset($search['category']) && !empty($search['category'])) {
            $where .= ' AND category = ' . $search['category'];
        }

        if ($uid !== null) {
            $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `' . self::$dataTable . '` WHERE `level` <= \'' . $level . '\' AND `author`=\'' . $uid . '\' AND `closed`=0' . $where);
        } else {
            $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `' . self::$dataTable . '` WHERE `level` <= \'' . $level . '\' AND `closed`=0' . $where);
        }
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    public function GetMostRecentEntry()
    {
        return SuggestionEntry::GetMostRecentForThread($this->id);
    }

    public static function GetAllOpenedForLevelByScore($level, $uid = null, $search = [], $onlyForLangs = false)
    {
        parent::$usePaging = true;
        $objs = [];
        $where = '';

        if (isset($search['stitle']) && !empty($search['stitle'])) {
            $where .= ' AND title like  "%' . $search['stitle'] . '%"';
        }

        if (isset($search['category']) && !empty($search['category'])) {
            $where .= ' AND category = ' . $search['category'];
        }

        if ($uid !== null) {
            $where = '`level` <= \'' . $level . '\' AND `author`=\'' . $uid . '\' AND `closed`=0' . $where;
        } else {
            $where = '`level` <= \'' . $level . '\' AND `closed`=0 ' . $where;
        }
        //if ($onlyForLangs) {
        //	$where .= 'AND `lang` IN (\''.join('\',\'', $onlyForLangs).'\')';
        //}
        self::$generatePagingQryString = false;
        $res = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'level', 'DESC , `score` DESC, `updatedate`');

        if (count($res) == 0) {
            return null;
        }

        foreach ($res as $k => $obj) {
            if (($obj->lastisprivate) && $level == 1) {// && $user->GetSupportStatus()->HasUserAccess($obj->author)
                $rec = SuggestionEntry::GetLastPublicRecord($obj->id);
                if ($rec) {
                    $res[$k]->updateauthor = $rec->author;
                    $res[$k]->updatedate = $rec->creationdate;
                } else {
                    unset($res[$k]);
                }
                unset($rec);
            }
        }

        // return with reindex
        return array_values($res);
    }

    public static function GetAllOpenedForLevelByTime($level, $uid = null, $search = [], $onlyForLangs = false)
    {
        parent::$usePaging = true;
        $objs = [];
        $where = '';

        if (isset($search['stitle']) && !empty($search['stitle'])) {
            $where .= ' AND title like  "%' . $search['stitle'] . '%"';
        }

        if (isset($search['category']) && !empty($search['category'])) {
            $where .= ' AND category = ' . $search['category'];
        }

        if ($uid !== null) {
            $where = '`level` <= \'' . $level . '\' AND `author`=\'' . $uid . '\' AND `closed`=0 ' . $where;
        } else {
            $where = '`level` <= \'' . $level . '\' AND `closed`=0 ' . $where;
        }
        //if ($onlyForLangs) {
        //	$where .= ' AND `lang` IN (\''.join('\',\'', $onlyForLangs).'\')';
        //}
        self::$generatePagingQryString = false;
        $res = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'creationdate', 'DESC');

        if (count($res) == 0) {
            return null;
        }

        foreach ($res as $k => $obj) {
            if (($obj->lastisprivate) && $level == 1) {// && $user->GetSupportStatus()->HasUserAccess($obj->author)
                $rec = SuggestionEntry::GetLastPublicRecord($obj->id);
                if ($rec) {
                    $res[$k]->updateauthor = $rec->author;
                    $res[$k]->updatedate = $rec->creationdate;
                } else {
                    unset($res[$k]);
                }
                unset($rec);
            }
        }

        // return with reindex
        return array_values($res);
    }

    public static function CountAllOpenedForAuthor($authorId, $search = [])
    {
        $where = '';

        if (isset($search['stitle']) && !empty($search['stitle'])) {
            $where .= ' AND title like  "%' . $search['stitle'] . '%"';
        }

        if (isset($search['category']) && !empty($search['category'])) {
            $where .= ' AND category = ' . $search['category'];
        }

        $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `' . self::$dataTable . '` WHERE `author` = \'' . $authorId . '\' AND `closed`=0' . $where . '');
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    public static function GetAllOpenedForAuthor($author, $search = [])
    {
        if ($author instanceof User) {
            $authorId = $author->id;
        } else {
            $authorId = $author;
        }
        parent::$usePaging = true;
        $i = 0;
        $objs = [];
        $where = '';

        if (isset($search['stitle']) && !empty($search['stitle'])) {
            $where .= ' AND title like  "%' . $search['stitle'] . '%"';
        }

        if (isset($search['category']) && !empty($search['category'])) {
            $where .= ' AND category = ' . $search['category'];
        }

        $where = '`author` = \'' . $authorId . '\' AND `closed`=0 ' . $where;

        self::$generatePagingQryString = false;
        $res = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'level', 'DESC , `score` DESC, `updatedate`');

        if (count($res) == 0) {
            return null;
        }

        if ($author instanceof User) {
            foreach ($res as $k => $obj) {
                if (($obj->lastisprivate) && !$author->IsSupportUser()) {// && $user->GetSupportStatus()->HasUserAccess($obj->author)
                    $rec = SuggestionEntry::GetLastPublicRecord($obj->id);
                    if ($rec) {
                        $res[$k]->updateauthor = $rec->author;
                        $res[$k]->updatedate = $rec->creationdate;
                    } else {
                        unset($res[$k]);
                    }
                    unset($rec);
                }
            }

            // return with reindexing
            return array_values($res);
        }

        return $res;
    }

    public static function CountAllClosedForLevel($level, $uid = null, $search = [])
    {
        $where = '';

        if (isset($search['stitle']) && !empty($search['stitle'])) {
            $where .= ' AND title like  "%' . $search['stitle'] . '%"';
        }

        if (isset($search['category']) && !empty($search['category'])) {
            $where .= ' AND category = ' . $search['category'];
        }

        if ($uid !== null) {
            $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `' . self::$dataTable . '` WHERE `level` <= \'' . $level . '\' AND `author`=\'' . $uid . '\' AND `closed`=1' . $where . ' order by closedate DESC');
        } else {
            $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `' . self::$dataTable . '` WHERE `level` <= \'' . $level . '\' AND `closed`=1' . $where . ' order by closedate DESC');
        }

        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    public static function GetAllClosedForLevel($level, $uid = null, $search = [], $onlyForLangs = false)
    {
        parent::$usePaging = true;
        $i = 0;
        $objs = [];
        $where = '';

        if (isset($search['stitle']) && !empty($search['stitle'])) {
            $where .= ' AND title like  "%' . $search['stitle'] . '%"';
        }

        if (isset($search['category']) && !empty($search['category'])) {
            $where .= ' AND category = ' . $search['category'];
        }

        if ($uid !== null) {
            $where = '`level` <= \'' . $level . '\' AND `author`=\'' . $uid . '\' AND `closed`=1 ' . $where;
        } else {
            $where = '`level` <= \'' . $level . '\' AND `closed`=1 ' . $where;
        }
        //if ($onlyForLangs) {
        //	$where .= ' AND `lang` IN (\''.join('\',\'', $onlyForLangs).'\')';
        //}
        self::$generatePagingQryString = false;
        $res = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'closedate', 'DESC');
        if (count($res) == 0) {
            return null;
        }

        /*		foreach($res as $k => $obj)
                {
                    if (($obj->lastisprivate) && $level == 1) {// && $user->GetSupportStatus()->HasUserAccess($obj->author)
                        $rec = SuggestionEntry::GetLastPublicRecord($obj->id);
                        if ($rec) {
                            $res[$k]->updateauthor = $rec->author;
                            $res[$k]->updatedate = $rec->creationdate;
                        }
                        else {
                            unset($res[$k]);
                        }
                        unset($rec);
                    }
                }

                // return with reindex
                return array_values($res);
        */

        return $res;
    }

    public static function GetAllReviewedForLevel($uid = null, $search = [])
    {
        parent::$usePaging = true;
        $i = 0;
        $objs = [];
        $where = '';

        if (isset($search['stitle']) && !empty($search['stitle'])) {
            $where .= ' AND title like  "%' . $search['stitle'] . '%"';
        }

        if (isset($search['category']) && !empty($search['category'])) {
            $where .= ' AND category = ' . $search['category'];
        }

        if ($uid !== null) {
            $where = '`author`=\'' . $uid . '\' AND `closed`=1 AND approved > 0 ' . $where;
        } else {
            $where = '`closed`=1 AND approved > 0' . $where;
        }

        self::$generatePagingQryString = false;
        $res = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'closedate', 'DESC');
        if (count($res) == 0) {
            return null;
        }

        return $res;
    }

    public static function CountAllClosedForAuthor($authorId, $search = [])
    {
        $where = '';
        if (isset($search['stitle']) && !empty($search['stitle'])) {
            $where .= ' AND title like  "%' . $search['stitle'] . '%"';
        }
        if (isset($search['category']) && !empty($search['category'])) {
            $where .= ' AND category = ' . $search['category'];
        }
        $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `' . self::$dataTable . '` WHERE `author` = \'' . $authorId . '\' AND `closed`=1' . $where);
        $res = DBi::$conn->query('SELECT COUNT(`id`) AS total FROM `' . self::$dataTable . '` WHERE `author` = \'' . $authorId . '\' AND `closed`=1' . $where);
        if (mysqli_num_rows($res) == 0) {
            return 0;
        }
        $obj = mysqli_fetch_object($res);

        return $obj->total;
    }

    public static function GetAllClosedForAuthor($author, $search = [])
    {
        if ($author instanceof User) {
            $authorId = $author->id;
        } else {
            $authorId = $author;
        }
        parent::$usePaging = true;
        $i = 0;
        $objs = [];
        $where = '';

        if (isset($search['stitle']) && !empty($search['stitle'])) {
            $where .= ' AND title like  "%' . $search['stitle'] . '%"';
        }

        if (isset($search['category']) && !empty($search['category'])) {
            $where .= ' AND category = ' . $search['category'];
        }

        $where = '`author` = \'' . $authorId . '\' AND `closed`=1 ' . $where;
        self::$generatePagingQryString = false;
        $res = parent::GetAll(self::GetDataTableFields(), self::GetDataTable(), $where, false, false, 'creationdate', 'DESC');

        if (count($res) == 0) {
            return null;
        }

        if ($author instanceof User) {
            foreach ($res as $k => $obj) {
                if (($obj->lastisprivate) && !$author->IsSupportUser()) {// && $user->GetSupportStatus()->HasUserAccess($obj->author)
                    $rec = SuggestionEntry::GetLastPublicRecord($obj->id);
                    if ($rec) {
                        $res[$k]->updateauthor = $rec->author;
                        $res[$k]->updatedate = $rec->creationdate;
                    } else {
                        unset($res[$k]);
                    }
                    unset($rec);
                }
            }

            // return with reindexing
            return array_values($res);
        }

        return $res;
    }

    public function Delete(User $user)
    {
        if ($user->id != $this->author
            && $this->IsAccessibleToSupportUser($user->GetSupportStatus()) === false) {
            throw new SoftException('You cannot delete a suggestion you did not submit.', SUGGESTION_THREAD);
        }
        DBi::$conn->query('DELETE FROM `' . self::$dataTable . '` WHERE `id`=\'' . $this->id . '\'');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(SUGGESTION_THREAD_CANT_DELETED, SUGGESTION_THREAD);
        }
        if ($this->author != $user->id) {
            $author = UserFactory::getInstance()->getUser($this->author);
            $author->Notify(sprintf(SUGGESTION_THREAD_DELETED, $this->id), SUGGESTION_THREAD);
        }

        return true;
    }

    public function Reply(User $user, $text, $status = SupportEntry::EPUBLIC, $lang = DEFAULT_LANGUAGE)
    {
        if ($this->closed == true && $user->IsSupportUser() === false) {
            throw new SoftException(SUGGESTION_CANT_REPLY_CLOSED, SUGGESTION_THREAD);
        } elseif ($this->IsAccessibleTo($user) === false) {
            throw new SoftException(SUGGESTION_CANT_REPLY, SUGGESTION_THREAD);
        }
        $this->SetAttribute('updatedate', time());
        $this->SetAttribute('updateauthor', $user->id);
        $this->SetAttribute('lastisprivate', $status == 1 ? 1 : 0);
        SuggestionEntry::sAdd($this->id, $user->id, $text, $status, 'eng');
        //if ($this->author != $user->id && $status == SupportEntry::EPUBLIC) // You can add events here to notify the author
        //$author = UserFactory::getInstance()->getUser($this->author);

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
            'author',
            'title',
            'level',
            'category',
            'approved',
            'closed',
            'closedby',
            'closedate',
            'score',
            'creationdate',
            'updateauthor',
            'updatedate',
            'lang',
            'lastisprivate',
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

    private static function sAdd($authorId, $title, $level, $category = 1, $lang = DEFAULT_LANGUAGE)
    {
        DBi::$conn->query('INSERT INTO `' . self::$dataTable . '` (`author`, `title`, `level`, `category`, `creationdate`, `updateauthor`, `updatedate`, `lang`) VALUES (\'' . $authorId . '\',\'' . $title . '\',\'' . $level . '\',\'' . $category . '\', \'' . time() . '\', \'' . $authorId . '\',  \'' . time() . '\', \'' . $lang . '\')');
        if (DBi::$conn -> affected_rows == 0) {
            throw new SoftException(SUGGESTION_THREAD_CANT_ADDED, SUGGESTION_THREAD);
        }
        $id = DBi::$conn -> insert_id;

        return $id;
    }
}
