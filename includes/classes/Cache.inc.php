<?php

/**
 * Class used to handle caching.
 *
 * @author Harish <harish282@gmail.com>
 * @copyright http://www.prisionstruggle.com
 */
final class Cache
{
    private static $mem = null;
    private static $enabled = null;
    private static $host = 'localhost';
    private static $port = '11211';

    /**
     * Private constructor to prevent to be initialized.
     */
    private function __construct()
    {
    }

    public static function Prepare()
    {
        if (is_a(self::$mem, 'Memcache')) {
            return self::$mem;
        }

        if (self::$enabled == null) {
            self::$enabled = Variable::GetValue('memcached_enabled');
            self::$host = Variable::GetValue('memcached_host');
            self::$port = Variable::GetValue('memcached_port');
        }

        if (!self::$enabled || !function_exists('memcache_connect')) {
            return null;
        }

        self::$mem = @memcache_connect(self::$host, self::$port);
        if (!is_a(self::$mem, 'Memcache')) {
            return null;
        }

        return self::$mem;
    }

    /**
     * @see Memcache::delete()
     *
     * @param string        $key
     * @param int[optional] $timeout
     *
     * @return bool
     */
    public static function Delete($key)
    {
        if (self::Prepare() === null) {
            return false;
        }

        return self::$mem->delete($key);
    }

    /**
     * @see Memcache::flush()
     *
     * @return bool
     */
    public function Flush()
    {
        if (self::Prepare() === null) {
            return false;
        }

        return self::$mem->flush();
    }

    /**
     * @see Memcache::get()
     *
     * @param string        $key
     * @param int[optional] $flags
     *
     * @return string the string associated with the key or
     */
    public static function Get($key)
    {
        if (self::Prepare() === null) {
            return false;
        }

        return self::$mem->get($key);
    }

    /**
     * @see Memcache::set()
     *
     * @param string        $key
     * @param mixed         $var
     * @param int[optional] $flag
     * @param int[optional] $expire
     *
     * @return bool
     */
    public static function Set($key, $var)
    {
        if (self::Prepare() === null) {
            return false;
        }
        $expire = MC_EXPIRE * DAY_SEC;

        return self::$mem->set($key, $var, false, $expire);
    }
}
