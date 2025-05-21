<?php

require_once '../config.php';

$cache = new Cache(Config::redis());

class Cache
{
    private \Redis|null $redis;
    private bool $enabled;

    public function __construct(RedisConfig $config)
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

    public function get(string $key): mixed
    {
        return $this->enabled ? $this->redis->get($key) : false;
    }

    public function setEx(string $key, int $ttl, string $value): bool
    {
        return $this->enabled ? $this->redis->setEx($key, $ttl, $value) : false;
    }

    public function del(string $key): bool
    {
        return $this->enabled ? $this->redis->del($key) : false;
    }

    public function exists(string $key): bool
    {
        return $this->enabled ? $this->redis->exists($key) : false;
    }
}
