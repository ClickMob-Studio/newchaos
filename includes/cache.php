<?php

require_once __DIR__ . '/../config.php';

$cache = new Cache(Config::redis());

class Cache
{
    private $redis;
    private $enabled;

    public function __construct($config)
    {
        $this->enabled = true;
        $this->redis = new \Redis();

        try {
            $this->redis->connect($config->host, $config->port);
        } catch (\RedisException $e) {
            $this->enabled = false;
            $this->redis = null;
            error_log('Redis connection failed: ' . $e->getMessage());
        }
    }

    public function get($key)
    {
        return $this->enabled ? $this->redis->get($key) : false;
    }

    public function setEx($key, $ttl, $value)
    {
        return $this->enabled ? $this->redis->setEx($key, $ttl, $value) : false;
    }

    public function del($key)
    {
        return $this->enabled ? $this->redis->del($key) : false;
    }

    public function exists($key)
    {
        return $this->enabled ? $this->redis->exists($key) : false;
    }
}
