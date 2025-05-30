<?php

class Config
{
    private static $db = null;     // DBConfig|null
    private static $redis = null;  // RedisConfig|null

    /**
     * @return DBConfig
     */
    public static function db()
    {
        if (self::$db === null) {
            self::$db = new DBConfig();
        }
        return self::$db;
    }

    /**
     * @return RedisConfig
     */
    public static function redis()
    {
        if (self::$redis === null) {
            self::$redis = new RedisConfig();
        }
        return self::$redis;
    }
}

class RedisConfig
{
    public $host;
    public $port;

    public function __construct()
    {
        $this->host = '127.0.0.1';
        $this->port = 6379;
    }
}

class DBConfig
{
    public $host;
    public $port;
    public $username;
    public $password;
    public $database;

    public function __construct()
    {
        $this->host = '127.0.0.1';
        $this->port = 3306;
        $this->username = 'chaoscit_user';
        $this->password = '3lrKBlrfMGl2ic14';
        $this->database = 'chaoscit_game';
    }
}
