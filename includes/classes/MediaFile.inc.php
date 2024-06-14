<?php

class MediaFile extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'files';

    private static $mimeTypes = [
        'audio/mpeg' => 'mp3',
        'audio/x-mpeg' => 'mp3',
        'audio/mp3' => 'mp3',
        'audio/x-mp3' => 'mp3',
        'audio/mpeg3' => 'mp3',
        'audio/mpg' => 'mp3',
        'audio/x-mpg' => 'mp3',
        'audio/x-mpegaudio' => 'mp3',

        'audio/mp4' => 'aac',
        'audio/aac' => 'aac',
        'audio/aacp' => 'aac',
        'audio/MP4A-LATM' => 'aac',
    ];

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public function GetDataFields()
    {
        return self::GetDataTableFields();
    }

    public static function getFileSize($filePath)
    {
        return filesize($filePath);
    }

    /**
     * TBD
     * Audio file duration in seconds.
     *
     * @param $filePath full path
     *
     * @return int (seconds)
     */
    public static function getDuration($filePath)
    {
        return 0;
    }

    /**
     * @param int $userId
     *
     * @return unknown_type
     */
    public static function GetUserFiles($userId)
    {
        return parent::GetAll(self::GetDataFields(), self::$dataTable, '`user_id`=\'' . $userId . '\'');
    }

    /**
     * @param int $fileId
     *
     * @return unknown_type
     */
    public static function GetById($fileId)
    {
        $data = parent::GetAll(self::GetDataFields(), self::$dataTable, '`id`=\'' . $fileId . '\'', false, 1);
        if (!$data) {
            return false;
        }

        return $data[0];
    }

    /**
     * @param int $userId
     *
     * @return unknown_type
     */
    public static function countFiles($userId)
    {
        $query = 'SELECT count(*) FROM `' . self::$dataTable . '` WHERE `user_id` = ' . $userId;
        $rs = DBi::$conn->query($query);

        return mysqli_result($rs, 0);
    }

    public static function isValidType($type)
    {
        return array_key_exists($type, self::$mimeTypes);
    }

    public static function deleteFiles($user_id, array $items)
    {
        if (!$items) {
            return false;
        }
        $in = join(',', $items);

        $items = parent::GetAll(['id', 'path'], self::$dataTable, '`user_id`=\'' . $user_id . '\' AND `id` IN (' . $in . ')');
        unset($in);

        if (!$items) {
            return false;
        }

        $in = [];
        foreach ($items as $item) {
            $in[] = $item->id;
            self::deleteFile($user_id, $item->path);
        }
        $in = join(',', $in);

        $query = 'DELETE FROM `' . self::$dataTable . '` WHERE `id` IN(' . $in . ')';

        DBi::$conn->query($query);

        return DBi::$conn -> affected_rows;
    }

    /**
     * @param $user_id
     * @param $file
     *
     * @return int last id(if 0 it means operation was unsuccessful)
     */
    public static function storeUploadedFile($user_id, $file)
    {
        $data = [];
        $data['user_id'] = $user_id;

        if (($pos = strrpos($file['name'], '.')) !== false) {
            $name = substr($file['name'], 0, $pos);
        }

        //TODO: support other formats
        $ext = 'mp3';

        $data['name'] = $name;
        $data['path'] = self::escapeFileName($name) . '.' . $ext;
        $data['duration'] = self::getDuration($file['tmp_name']);
        $data['filesize'] = self::getFileSize($file['tmp_name']);
        //$data['time'] = time();

        $id = parent::AddRecords($data, self::GetDataTable());

        if (!$id) {
            return false;
        }
        // if storing locally
        if (!FILES_HOSTER) {
            // getting folder 2 levels up
            $dir = self::getFolder() . $user_id;
            if (!is_dir($dir)) {
                mkdir($dir, 0775);
                file_put_contents($dir . '/index.php', '');
            }

            if (move_uploaded_file($file['tmp_name'], $dir . '/' . $data['path'])) {
                return $id;
            }
        }
        // TBD: S3 or other CDN logic
        // in this case FILES_HOSTER should contain S3 bucket name

        return false;
    }

    /**
     * Returns full path of the folder where media files are stored.
     *
     * @return string
     */
    public static function getFolder()
    {
        return dirname(dirname(dirname(__FILE__))) . '/' . MEDIA_FILES_PATH;
    }

    public static function deleteFile($user_id, $path)
    {
        if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
            return false;
        }
        $file = self::getFolder() . $user_id . '/' . $path;
        if (file_exists($file)) {
            return unlink($file);
        }

        return false;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return MySQL::GetFields(self::$dataTable);
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }

    /**
     * Returns better filename.
     *
     * e.g: Hello World will become Hello_World
     * or
     *  -09--9-9- will become 09_9_9
     *  etc
     *
     * @param string $name (filename without extension)
     *
     * @return string
     */
    private static function escapeFileName($name)
    {
        $name = preg_replace('/[^0-9a-z]+/i', '_', $name);
        $name = trim($name, '_');

        if (empty($name)) {
            return Security::RandomToken(10, $name);
        } elseif (strlen($name) > 100) {
            return substr($name, 0, 100);
        }

        return $name;
    }
}
