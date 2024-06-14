<?php

abstract class Singleton implements ISingleton
{
    /**
     * Store the instance, if it exists.
     *
     * @var null
     */
    protected static $instances = [];

    /**
     * Protected so no one else can instance it.
     */
    protected function __construct()
    {
    }

    /**
     * Protected to prevent clonning.
     */
    protected function __clone()
    {
    }

    /**
     * Retrieve an instance of this singleton.
     *
     * @return Singleton
     */
    public static function getInstance()
    {
        $calledClass = get_called_class();
        if (!isset(static::$instances[$calledClass])) {
            static::$instances[$calledClass] = new static();
        }

        return static::$instances[$calledClass];
    }
}
