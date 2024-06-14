<?php

class View
{
    private static $path = 'views/';
    private static $ext = '.inc.php';

    private $viewVariables;
    private $viewName;

    public function __construct($viewName = null)
    {
        $this->viewName = $viewName;
        $this->viewVariables = [];

        global $availableLanguages;
        $this->RegisterVariable('availableLanguages', $availableLanguages);
    }

    public function __get($key)
    {
        if (isset($this->viewVariables) && isset($this->viewVariables[$key])) {
            return $this->viewVariables[$key];
        }

        return null;
    }

    public function __isset($key)
    {
        return isset($this->viewVariables[$key]);
    }

    public function RegisterVariable($key, $value)
    {
        $this->viewVariables[$key] = $value;

        return true;
    }

    public function RegisterVariables($varArray)
    {
        foreach ($varArray as $key => $value) {
            $this->RegisterVariable($key, $value);
        }

        return true;
    }

    public function Render($return = false)
    {
        if ($this->viewName === null) {
            return false;
        }

        if ($return === true) {
            ob_start();
        }

        if ($return === 'JSON') {
            ob_end_clean();
        }

        $view = $this;

        require $_SERVER['DOCUMENT_ROOT'] . DS . self::$path . $this->viewName . self::$ext;

        if ($return === true) {
            $r = ob_get_contents();
            ob_end_clean();

            return $r;
        }
    }

    /**
     * Render a template with variables and return the output.
     */
    public static function renderWithVariables(string $viewName, array $variables = [], bool $cache = false): string
    {
        global $user_class;

        $view = new View($viewName);
        if ($cache) {
            $cacheHit = View::getCache($view->generateCacheKey($user_class));
            if ($cacheHit) {
                return $cacheHit;
            }
        }

        $view->RegisterVariables($variables);

        $response = $view->Render(true);

        if ($cache) {
            View::storeCache($view->generateCacheKey($user_class), $response);
        }

        return $response;
    }

    /**
     * Return the cache key.
     *
     * @return string
     */
    public function generateCacheKey(User $user_class)
    {
        return str_replace(['/', '\\'], '_', $this->viewName) . '_' . $user_class->id;
    }

    /**
     * Store some basic data into the cache.
     *
     * @param $key
     * @param $value
     */
    public static function storeCache(string $key, string $value)
    {
        $dir = self::getCacheDir();
        $path = $dir . DS . $key;

        file_put_contents($path, $value);
    }

    /**
     * Get the cache entry from the file system.
     *
     * @return false|string
     */
    public static function getCache(string $key)
    {
        $dir = self::getCacheDir();
        $path = $dir . DS . $key;

        if (file_exists($path)) {
            return file_get_contents($path);
        }
    }

    /**
     * Clear a cache entry.
     */
    public static function clearCacheForUser(string $viewName)
    {
        global $user_class;

        $view = new View($viewName);
        $dir = self::getCacheDir();
        $path = $dir . DS . $view->generateCacheKey($user_class);

        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * Clear all cache for view
     *
     * @param string $viewName
     */
    public static function clearCache(string $viewName)
    {
        $dir = self::getCacheDir();
        $results = glob($dir . DS . str_replace(['/', '\\'], '_', $viewName) . '*');
        foreach ($results as $result) {
            unlink($result);
        }
    }

    /**
     * Return the cache dir.
     */
    public static function getCacheDir(): string
    {
        return $_SERVER['DOCUMENT_ROOT'] . DS . 'var' . DS . 'cache';
    }

    public static function sRender($view, $variables)
    {
        require $_SERVER['DOCUMENT_ROOT'] . DS . self::$path . $view . self::$ext;
    }

    public static function Clear()
    {
        ob_end_clean();
    }
}
