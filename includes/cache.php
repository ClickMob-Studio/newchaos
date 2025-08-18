<?php

require_once __DIR__ . '/../config.php';

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

    public function hSet(string $key, string $field, string $value): bool
    {
        return $this->enabled ? $this->redis->hSet($key, $field, $value) : false;
    }

    public function hIncrBy(string $key, string $field, int $value): int
    {
        return $this->enabled ? $this->redis->hIncrBy($key, $field, $value) : 0;
    }

    public function del(string $key): bool
    {
        return $this->enabled ? $this->redis->del($key) : false;
    }

    public function exists(string $key): bool
    {
        return $this->enabled ? $this->redis->exists($key) : false;
    }

    public function incr(string $key, int $value = 1): int
    {
        return $this->enabled ? $this->redis->incrBy($key, $value) : 0;
    }

    public function decr(string $key, int $value = 1): int
    {
        return $this->enabled ? $this->redis->decrBy($key, $value) : 0;
    }

    public function flushAll(): bool
    {
        return $this->enabled ? $this->redis->flushAll() : false;
    }
}
