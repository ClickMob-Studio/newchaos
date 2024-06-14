<?php

final class Forum extends BaseObject
{
    protected static $idField = 'id';
    protected static $nameField = 'name';
    protected static $orderField = 'ordering';
    protected static $authField = 'auth';
    protected static $binField = 'recycle_bin';
    protected static $timePostedField = 'time_posted';
    protected static $timeEditedField = 'time_last_edited';
    protected static $posterIdField = 'poster_id';
    protected static $descField = 'description';
    protected static $subjectField = 'subject';
    protected static $hiddenField = 'is_hidden';
    protected static $deletedField = 'is_deleted';
    protected static $avatarField = 'avatar';
    protected static $contentField = 'content';

    protected static $categoriesTable = 'forum_categories';
    protected static $boardTable = 'forum_boards';
    protected static $topicTable = 'forum_topics';
    protected static $postTable = 'forum_posts';
    protected static $usersTable = 'grpgusers';

    protected static $boardCategoryParent = 'parent_category';
    protected static $topicBoardParent = 'parent_board';
    protected static $postTopicParent = 'parent_topic';

    public function __construct($id)
    {
        try {
            parent::__construct($id);
        } catch (\Exception $e) {
            // do nothing
        }
    }

    public static function BoardExists(?int $id = null): bool
    {
        if ($id !== null) {
            $query = 'SELECT COUNT(' . self::$idField . ') FROM ' . self::$boardTable . ' WHERE ' . self::$idField . ' = ' . $id;
            try {
                $selectBoard = DBi::$conn->query($query);
            } catch (\Exception $e) {
                throw new SoftException('Couldn\'t execute query in BoardExists.<br>' . $query);
            }
            $cnt = mysqli_fetch_row($selectBoard);

            return (bool) $cnt;
        }

        return false;
    }

    public static function TopicExists(int $id): bool
    {
        $query = 'SELECT COUNT(' . self::$idField . ') FROM ' . self::$topicTable . ' WHERE ' . self::$idField . ' = ' . $id;
        try {
            $selectTopic = DBi::$conn->query($query);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in BoardExists.<br>' . $query);
        }
        $cnt = mysqli_fetch_row($selectTopic);

        return (bool) $cnt;
    }

    public static function PostExists(?int $id = null): bool
    {
        if ($id !== null) {
            $query = 'SELECT COUNT(' . self::$idField . ') FROM ' . self::$postTable . ' WHERE ' . self::$idField . ' = ' . $id;
            try {
                $selectPost = DBi::$conn->query($query);
            } catch (\Exception $e) {
                throw new SoftException('Couldn\'t execute query in PostExists.<br>' . $query . '<br>' . $e->getMessage());
            }
            $row = mysqli_fetch_row($selectPost);

            return (bool) $row[0];
        }

        return false;
    }

    public static function getCategories(User $user): ?array
    {
        $query = 'SELECT ' . self::$idField . ', ' . self::$nameField . ', ' . self::$authField . ' FROM ' . self::$categoriesTable . ' ORDER BY ' . self::$orderField . ' ASC';
        try {
            $selectCategories = DBi::$conn->query($query);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in getCategories.<br>' . $query . '<br>Error: ' . $e->getMessage());
        }
        if (!mysqli_num_rows($selectCategories)) {
            return null;
        }
        $data = [];
        while ($row = mysqli_fetch_assoc($selectCategories)) {
            if (self::handleAuth($user, $row[self::$authField]) === true) {
                $data[$row[self::$idField]] = $row;
            }
        }

        return $data;
    }

    public static function handleAuth(User $user, ?string $auth, ?string $access = 'read')
    {
        if (in_array($auth, ['', null], true)) {
            return true;
        }
        if ($user->mods > 0) {
            return true;
        }
        $parts = explode(';', $auth);
        if (count($parts) > 0) {
            foreach ($parts as $part) {
                list($rank, $perms) = explode(':', $part);
                $each = explode(',', $perms);
                if (in_array($access, $each)) {
                    if (($rank == 'mod' && ($user->IsSuperModerator() === true or $user->IsModerator() === true)) or $rank == 'public') {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public static function getBoards(User $user): ?array
    {
        $query = 'SELECT ' . self::$idField . ', ' . self::$nameField . ', ' . self::$descField . ', ' . self::$authField . ', ' . self::$boardCategoryParent . ' FROM ' . self::$boardTable . ' ORDER BY ' . self::$orderField . ' ASC, ' . self::$nameField . ' ASC';
        try {
            $selectBoards = DBi::$conn->query($query);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in getBoard.<br>' . $query . '<br>Error: ' . $e->getMessage());
        }
        if (!mysqli_num_rows($selectBoards)) {
            return null;
        }
        $selectLatest = 'SELECT fp.' . self::$idField . ' AS postID, fp.' . self::$posterIdField . ', ft.' . self::$idField . ' AS topicID, ft.' . self::$nameField . ', fb.' . self::$idField . ' AS boardID
            FROM ' . self::$postTable . ' AS fp
            INNER JOIN ' . self::$topicTable . ' AS ft ON fp.' . self::$postTopicParent . ' = ft.' . self::$idField . '
            INNER JOIN ' . self::$boardTable . ' AS fb ON ft.' . self::$topicBoardParent . ' = fb.' . self::$idField . '
            GROUP BY ft.' . self::$idField . '
            ORDER BY fp.' . self::$timePostedField . ' ASC';
        try {
            $getLatest = DBi::$conn->query($selectLatest);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in getBoard.<br>' . $selectLatest . '<br>Error: ' . $e->getMessage());
        }
        $topicsGroupedQuery = 'SELECT COUNT(' . self::$idField . ') AS topics, ' . self::$topicBoardParent . ' FROM ' . self::$topicTable . ' GROUP BY ' . self::$topicBoardParent;
        try {
            $topicsGrouped = DBi::$conn->query($topicsGroupedQuery);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in getBoard.<br>' . $topicsGroupedQuery . '<br>Error: ' . $e->getMessage());
        }
        $postsGroupedQuery = 'SELECT COUNT(' . self::$idField . ') AS posts, ' . self::$postTopicParent . ' FROM ' . self::$postTable . ' GROUP BY ' . self::$postTopicParent;
        try {
            $postsGrouped = DBi::$conn->query($postsGroupedQuery);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in getBoard.<br>' . $postsGroupedQuery . '<br>Error: ' . $e->getMessage());
        }
        $conf = [
            'latest' => [],
            'topics' => [],
            'posts' => [],
        ];
        while ($late = mysqli_fetch_assoc($getLatest)) {
            $conf['latest'][$late['boardID']] = $late;
        }
        while ($top = mysqli_fetch_assoc($topicsGrouped)) {
            $conf['topics'][$top[self::$topicBoardParent]] = $top['topics'];
        }
        while ($post = mysqli_fetch_assoc($postsGrouped)) {
            $conf['posts'][$post[self::$postTopicParent]] = $post['posts'];
        }
        $data = [];
        while ($row = mysqli_fetch_assoc($selectBoards)) {
            if (self::handleAuth($user, $row[self::$authField])) {
                $data[$row[self::$idField]] = $row;
                $topicExists = array_key_exists($row[self::$idField], $conf['topics']);
                $postExists = array_key_exists($conf['topics'][$row[self::$idField]], $conf['posts']);
                $latestExists = array_key_exists($row[self::$idField], $conf['latest']);
                $data[$row[self::$idField]]['topic_cnt'] = $topicExists === true ? $conf['topics'][$row[self::$idField]] : 0;
                $data[$row[self::$idField]]['post_cnt'] = $topicExists === true && $postExists === true ? $conf['topics'][$row[self::$idField]] : 0;
                $data[$row[self::$idField]]['latest_post'] = $latestExists === true ? $conf['latest'][$row[self::$idField]] : null;
            }
        }

        return $data;
    }

    public static function getBoard(User $user, int $id)
    {
        $query = 'SELECT ' . self::$idField . ', ' . self::$nameField . ', ' . self::$authField . ' FROM ' . self::$boardTable . ' WHERE ' . self::$idField . ' = ' . $id;
        try {
            $selectBoard = DBi::$conn->query($query);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in getBoard.<br>' . $query . '<br>Error: ' . $e->getMessage());
        }
        if (!mysqli_num_rows($selectBoard)) {
            return null;
        }
        $row = mysqli_fetch_assoc($selectBoard);

        return self::handleAuth($user, $row[self::$authField]) === true ? $row : null;
    }

    public static function getTopics(User $user, int $id): ?array
    {
        $queryBoard = 'SELECT ' . self::$idField . ', ' . self::$nameField . ', ' . self::$authField . ' FROM ' . self::$boardTable . ' WHERE ' . self::$idField . ' = ' . $id;
        try {
            $selectBoard = DBi::$conn->query($queryBoard);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in getTopics.<br>' . $queryBoard . '<br>Error: ' . $e->getMessage());
        }
        if (!mysqli_num_rows($selectBoard)) {
            return null;
        }
        $board = mysqli_fetch_assoc($selectBoard);
        $queryTopics = 'SELECT ' . self::$idField . ', ' . self::$nameField . ', ' . self::$topicBoardParent . ', ' . self::$posterIdField . ', ' . self::$timePostedField . ', ' . self::$subjectField . ' FROM ' . self::$topicTable . ' WHERE ' . self::$topicBoardParent . ' = ' . $id . ' ORDER BY ' . self::$timePostedField . ' DESC';
        try {
            $selectTopics = DBi::$conn->query($queryTopics);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in getTopics.<br>' . $queryTopics . '<br>Error: ' . $e->getMessage());
        }
        if (!mysqli_num_rows($selectTopics)) {
            return null;
        }
        $selectLatestQuery = 'SELECT ' . self::$idField . ', ' . self::$posterIdField . ' FROM ' . self::$postTable . ' GROUP BY ' . self::$postTopicParent . ' ORDER BY ' . self::$timePostedField . ' DESC LIMIT 1';
        try {
            $getLatest = DBi::$conn->query($selectLatestQuery);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in getTopics.<br>' . $selectLatestQuery . '<br>Error: ' . $e->getMessage());
        }
        $postsGroupedQuery = 'SELECT COUNT(' . self::$idField . ') AS posts, ' . self::$postTopicParent . ' FROM ' . self::$postTable . ' GROUP BY ' . self::$postTopicParent;
        try {
            $postsGrouped = DBi::$conn->query($postsGroupedQuery);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in getTopics.<br>' . $postsGroupedQuery . '<br>Error: ' . $e->getMessage());
        }
        $conf = [
            'latest' => [],
            'posts' => [],
        ];
        while ($late = mysqli_fetch_assoc($getLatest)) {
            $conf['latest'][$late['boardID']] = $late;
        }
        while ($post = mysqli_fetch_assoc($postsGrouped)) {
            $conf['posts'][$post[self::$postTopicParent]] = $post['posts'];
        }
        $data = [];
        while ($row = mysqli_fetch_assoc($selectTopics)) {
            if (self::handleAuth($user, $board[self::$authField]) === true) {
                $data[$row[self::$idField]] = $row;
                $postExists = array_key_exists($conf['posts'][$row[self::$idField]], $conf['posts']);
                $latestExists = array_key_exists($row[self::$idField], $conf['latest']);
                $data[$row[self::$idField]]['post_cnt'] = $postExists === true ? $conf['posts'][$row[self::$idField]] : 0;
                $data[$row[self::$idField]]['latest_post'] = $latestExists === true ? $conf['latest'][$row[self::$idField]] : null;
            }
        }

        return $data;
    }

    public static function getPosts(User $user, int $id)
    {
        $topic = self::getTopic($user, $id);
        if ($topic === null) {
            return null;
        }
        $queryPosts = 'SELECT fp.' . self::$idField . ', fp.' . self::$contentField . ', fp.' . self::$timePostedField . ', fp.' . self::$posterIdField . ', u.' . self::$avatarField . '
            FROM ' . self::$postTable . ' AS fp
            INNER JOIN ' . self::$usersTable . ' AS u ON fp.' . self::$posterIdField . ' = u.' . self::$idField . '
            WHERE ' . self::$postTopicParent . ' = ' . $topic[self::$idField] . '
            ORDER BY fp.' . self::$timePostedField . ' ASC';
        try {
            $selectPosts = DBi::$conn->query($queryPosts);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t run query in getPosts.<br>' . $queryPosts . '<br>Error:' . $e->getMessage());
        }
        if (!mysqli_num_rows($selectPosts)) {
            return null;
        }
        $data = [];
        while ($row = mysqli_fetch_assoc($selectPosts)) {
            if (self::handleAuth($user, $topic[self::$authField]) === true) {
                $data[$row[self::$idField]] = $row;
            }
        }

        return $data;
    }

    public static function getPost(User $user, int $id)
    {
        $query = 'SELECT fp.*, ft.' . self::$idField . ' AS topicID, fb.' . self::$idField . ' AS boardID
            FROM ' . self::$postTable . ' AS fp
            INNER JOIN ' . self::$topicTable . ' AS ft ON ft.' . self::$idField . ' = fp.' . self::$postTopicParent . '
            INNER JOIN ' . self::$boardTable . ' AS fb ON fb.' . self::$idField . ' = ft.' . self::$topicBoardParent . '
            WHERE fp.' . self::$idField . ' = ' . $id;
        try {
            $selectPost = DBi::$conn->query($query);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t run query in getPost.<br>' . $query . '<br>Error: ' . $e->getMessage());
        }
        if (!mysqli_num_rows($selectPost)) {
            return null;
        }
        $row = mysqli_fetch_assoc($selectPost);

        return self::handleAuth($user, $row[self::$authField]) === true ? $row : null;
    }

    public static function getPoster(int $id)
    {
        return User::GetUsername($id);
    }

    public static function getTopic(User $user, int $id)
    {
        if ($id === null) {
            return null;
        }
        $query = 'SELECT ft.' . self::$idField . ', ft.' . self::$nameField . ', ft.' . self::$topicBoardParent . ', fb.' . self::$authField . '
            FROM ' . self::$topicTable . ' AS ft
            INNER JOIN ' . self::$boardTable . ' AS fb ON ft.' . self::$topicBoardParent . ' = fb.' . self::$idField . '
            WHERE ft.' . self::$idField . ' = ' . $id;
        try {
            $selectTopic = DBi::$conn->query($query);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in getTopic.<br>' . $query . '<br>Error: ' . $e->getMessage());
        }
        if (!mysqli_num_rows($selectTopic)) {
            return null;
        }
        $row = mysqli_fetch_assoc($selectTopic);

        return self::handleAuth($user, $row['auth']) === true ? $row : null;
    }

    public static function isDuplicateTopic(int $boardID, string $title, ?string $description = null)
    {
        if ($description === null) {
            $description = '';
        }
        $selectDuplicateTopic = 'SELECT COUNT(' . self::$idField . ') FROM ' . self::$topicTable . ' WHERE ' . self::$topicBoardParent . ' = ' . $boardID . ' AND (LOWER(' . self::$nameField . ') = "' . strtolower(mysqli_real_escape_string(DBi::$conn,
                $title)) . '" OR LOWER(' . self::$subjectField . ') = "' . strtolower(mysqli_real_escape_string(DBi::$conn,
                $description)) . '")';
        try {
            $selectDuplicate = DBi::$conn->query($selectDuplicateTopic);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in isDuplicateTopic.<br>' . $selectDuplicateTopic . '<br>Error: ' . $e->getMessage());
        }
        if (!mysqli_num_rows($selectDuplicate)) {
            return false;
        }
        $row = mysqli_fetch_row($selectDuplicate);

        return (bool) $row[0];
    }

    public static function isDuplicatePost(int $topicID, string $content)
    {
        $selectDuplicatePost = 'SELECT COUNT(' . self::$idField . ') FROM ' . self::$postTable . ' WHERE ' . self::$postTopicParent . ' = ' . $topicID . ' AND LOWER(' . self::$contentField . ') = "' . strtolower(mysqli_real_escape_string(DBi::$conn,
                $content)) . '"';
        try {
            $selectDuplicate = DBi::$conn->query($selectDuplicatePost);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in isDuplicatePost.<br>' . $selectDuplicatePost . '<br>Error: ' . $e->getMessage());
        }
        if (!mysqli_num_rows($selectDuplicate)) {
            return false;
        }
        $row = mysqli_fetch_row($selectDuplicate);

        return (bool) $row[0];
    }

    public static function getLatestPost(?int $id = null, $scope = 'topic')
    {
        if ($id === null) {
            return null;
        }
        $where = $scope === 'board'
            ? 'WHERE ft.' . self::$topicBoardParent . ' = ' . $id
            : 'WHERE fp.' . self::$postTopicParent . ' = ' . $id;
        $query = 'SELECT fp.*, ft.' . self::$topicBoardParent . ', ft.' . self::$nameField . '
            FROM ' . self::$postTable . ' AS fp
            INNER JOIN ' . self::$topicTable . ' AS ft ON fp.' . self::$postTopicParent . ' = ft.' . self::$idField . '
            ' . $where . '
            ORDER BY fp.' . self::$timePostedField . ' DESC
            LIMIT 1';
        try {
            $select = DBi::$conn->query($query);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in getLatestPost.<br>' . $query . '<br>Error: ' . $e->getMessage());
        }
        if (!mysqli_num_rows($select)) {
            return null;
        }

        return mysqli_fetch_assoc($select);
    }

    public static function insertTopic(
        User $user,
        int $boardID,
        ?string $title = null,
        ?string $subject = null,
        ?string $content = null
    ) {
        if ($title !== null && $content !== null) {
            $insertTopic = sprintf('INSERT INTO %s (%s, %s, %s, %s) VALUES (%u, %u, "%s", "%s")',
                self::$topicTable,
                self::$topicBoardParent, self::$posterIdField, self::$nameField, self::$subjectField,
                $boardID, $user->id,
                mysqli_real_escape_string(DBi::$conn, $title),
                $subject !== null ? mysqli_real_escape_string(DBi::$conn, $subject) : ''
            );
            try {
                DBi::$conn->query($insertTopic);
            } catch (\Exception $e) {
                throw new SoftException('Couldn\'t execute query in insertTopic.<br>' . $insertTopic . '<br>Error: ' . $e->getMessage());
            }
            $topicID = mysqli_insert_id(DBi::$conn);
            
            self::insertPost($user, $topicID, $content);

            return $topicID;
        }
    }

    public static function deleteTopic(int $topicID)
    {
        DBi::$conn->query('DELETE FROM ' . self::$topicTable . ' WHERE ' . self::$idField . ' = ' . $topicID);
        DBi::$conn->query('DELETE FROM ' . self::$postTable . ' WHERE ' . self::$postTopicParent . ' = ' . $topicID);
    }

    public static function insertPost(User $user, int $topicID, ?string $content = null)
    {
        $insertPost = sprintf('INSERT INTO %s (%s, %s, %s) VALUES (%u, %u, "%s")',
            self::$postTable,
            self::$postTopicParent, self::$posterIdField, self::$contentField,
            $topicID, $user->id, mysqli_real_escape_string(DBi::$conn, $content)
        );
        try {
            DBi::$conn->query($insertPost);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in insertTopic.<br>' . $insertPost . '<br>Error: ' . $e->getMessage());
        }
    }

    public static function updatePost(int $postID, ?string $content = null)
    {
        $query = 'UPDATE ' . self::$postTable . ' SET content = "' . mysqli_real_escape_string(DBi::$conn, $content) . '" WHERE ' . self::$idField . ' = ' . $postID;
        try {
            DBi::$conn->query($query);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in updatePost.<br>' . $query . '<br>Error: ' . $e->getMessage());
        }
    }

    public static function deletePost(int $postID)
    {
        $query = 'DELETE FROM ' . self::$postTable . ' WHERE ' . self::$idField . ' = ' . $postID;
        try {
            DBi::$conn->query($query);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in deletePost.<br>' . $query . '<br>Error: ' . $e->getMessage());
        }
    }

    public static function isBanned(User $user): bool
    {
        $query = 'SELECT ' . self::$idField . ', `time` FROM bans WHERE ' . self::$idField . ' = ' . $user->id;
        try {
            $select = DBi::$conn->query($query);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in isBanned.<br>' . $query . '<br>Error: ' . $e->getMessage());
        }
        if (!mysqli_num_rows($select)) {
            return false;
        }
        $res = mysqli_fetch_assoc($select);

        return $res['time'] > 0;
    }

    public static function getBanData(User $user): ?array
    {
        $data = null;
        $query = 'SELECT id, reason, `time` FROM bans WHERE ' . self::$idField . ' = ' . $user->id;
        try {
            $selectBanData = DBi::$conn->query($query);
        } catch (\Exception $e) {
            throw new SoftException('Couldn\'t execute query in getBanData.<br>' . $query . '<br>Error: ' . $e->getMessage());
        }
        if (!mysqli_num_rows($selectBanData)) {
            return null;
        }
        $row = mysqli_fetch_assoc($query);
        $data = [
            'duration' => $row->time,
            'reason' => Utility::FormatDBString($row->reason),
        ];

        return $data;
    }

    public static function Get($dataFields, $dataTable = '', $condKey = '', $condVal = '')
    {
        return parent::Get($dataFields, $dataTable, $condKey, $condVal);
    }

    protected static function GetDataTableFields(?string $which = 'boards')
    {
        if (!self::CheckTable($which)) {
            return null;
        }
        if ($which == 'categories') {
            return [
                self::$idField,
                self::$nameField,
                self::$authField,
                self::$orderField,
            ];
        } elseif ($which == 'boards') {
            return [
                self::$idField,
                self::$nameField,
                self::$authField,
                self::$orderField,
                self::$descField,
                self::$binField,
            ];
        } elseif ($which == 'topics') {
            return [
                self::$idField,
                self::$topicBoardParent,
                self::$nameField,
                self::$subjectField,
                self::$posterIdField,
                self::$hiddenField,
                self::$deletedField,
            ];
        } elseif ($which == 'posts') {
            return [
                self::$idField,
                self::$postTopicParent,
                self::$posterIdField,
                self::$timePostedField,
                self::$hiddenField,
                self::$deletedField,
                self::$timeEditedField,
                'poster_avatar',
            ];
        }

        return null;
    }

    protected static function GetDataTable(?string $which = 'boards')
    {
        if (!self::CheckTable($which)) {
            return null;
        }
        if ($which == 'boards') {
            return self::$boardTable;
        } elseif ($which == 'topics') {
            return self::$topicTable;
        } elseif ($which == 'posts') {
            return self::$postTable;
        }

        return null;
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }

    private static function CheckTable(?string $which = null)
    {
        return in_array($which, ['boards', 'topics', 'posts'], true);
    }
}
