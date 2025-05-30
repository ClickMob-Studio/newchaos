<?php

class Config
{
    private static ?DBConfig $db = null;
    private static ?RedisConfig $redis = null;

    public static function db(): DBConfig
    {
        if (self::$db === null) {
            self::$db = new DBConfig();
        }
        return self::$db;
    }

    public static function redis(): RedisConfig
    {
        if (self::$redis === null) {
            self::$redis = new RedisConfig();
        }
        return self::$redis;
    }
}

class RedisConfig
{
    public string $host;
    public int $port;

    public function __construct()
    {
        $this->host = '127.0.0.1';
        $this->port = 6379;
    }
}


class DBConfig
{
    public string $host;
    public int $port;
    public string $username;
    public string $password;
    public string $database;

    public function __construct()
    {
        $this->host = '127.0.0.1';
        $this->port = 3306;
        $this->username = 'chaoscit_user';
        $this->password = '3lrKBlrfMGl2ic14';
        $this->database = 'chaoscit_game';
    }
}