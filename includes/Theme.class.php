<?php
class OPSTheme
{
    public $theme;
    public $defaultTheme;
    public $parentDir;
    public $themeDir;
    public $pageDir;
    public $partDir;
    public $pagename;
    public $pagepath;

    public $cssFiles = array();
    public $jsFiles = array('header' => array(), 'footer' => array());

    public $content;

    public function __construct()
    {
        $this->theme = THEME;
        $this->defaultTheme = 'bs4';
        $this->themeDir = "themes/{$this->theme}";
        $this->parentDir = '';

        if (!file_exists($this->themeDir)) {
            $this->parentDir = '../' . $this->parentDir;
            $this->themeDir = $this->parentDir . $this->themeDir;
        }

        if (!file_exists($this->themeDir)) {
            $this->parentDir = '../' . $this->parentDir;
            $this->themeDir = $this->parentDir . $this->themeDir;
        }

        $this->pageDir = "{$this->themeDir}/html/pages";
        $this->partDir = "{$this->themeDir}/html/parts";
    }

    public function addVariable($name, $value)
    {
        if (is_object($value))
            $value = (array)$value;

        if (!isset($GLOBALS['themeVars']))
            $GLOBALS['themeVars'] = array();

        global $themeVars;
        $themeVars[$name] = $value;
    }

    public function addCss($name, $d = false)
    {
        if (preg_match('/^(https\:\/\/|http\:\/\/|\/\/)/i', $name)) {
            $this->cssFiles[] = $name;
            return true;
        }

        $name = str_replace('.css', '', strtolower($name));
        $filepath = "{$this->themeDir}/css/{$name}.css";

        if ($this->theme !== $this->defaultTheme && !file_exists($filepath))
            $filepath = $this->parentDir . "themes/{$this->defaultTheme}/css/{$name}.css";

        if (file_exists($filepath)) {
            $this->cssFiles[] = $filepath;
            return true;
        }

        if (!$d)
            return '';

        $filepath = str_replace(array($this->themeDir, "themes/{$this->defaultTheme}"), $d . '/theme', $filepath);
        $this->cssFiles[] = $filepath;
        return true;
    }

    public function getCss()
    {
        $css = '';
        foreach ($this->cssFiles as $cssFile) {
            $eTime = (preg_match('/^(https\:\/\/|http\:\/\/|\/\/)/i', $cssFile)) ? '' : '?t=' . filemtime($cssFile);
            $cssLink = $cssFile . $eTime;
            $css .= '<link rel="stylesheet" href="' . $cssLink . '">';
        }
        return $css;
    }

    public function addJs($name, $location = 'header', $d = false)
    {
        if (!isset($this->jsFiles[$location]))
            $this->jsFiles[$location] = array();

        if (preg_match('/^(https\:\/\/|http\:\/\/|\/\/)/i', $name)) {
            $this->jsFiles[$location][] = $name;
            return true;
        }

        $name = str_replace('.js', '', strtolower($name));
        $filepath = "{$this->themeDir}/js/{$name}.js";

        if ($this->theme !== $this->defaultTheme && !file_exists($filepath))
            $filepath = $this->parentDir . "themes/{$this->defaultTheme}/js/{$name}.js";

        if (file_exists($filepath)) {
            $this->jsFiles[$location][] = $filepath;
            return true;
        }

        if (!$d)
            return '';

        $filepath = str_replace(array($this->themeDir, "themes/{$this->defaultTheme}"), $d . '/theme', $filepath);
        $this->jsFiles[$location][] = $filepath;
        return true;
    }

    public function getJs($location = 'header')
    {
        if (!isset($this->jsFiles[$location]))
            return '';

        $js = '';
        foreach ($this->jsFiles[$location] as $jsFile) {
            $eTime = (preg_match('/^(https\:\/\/|http\:\/\/|\/\/)/i', $jsFile)) ? '' : '?t=' . filemtime($jsFile);
            $jsLink = $jsFile . $eTime;
            $js .= '<script src="' . $jsLink . '"></script>';
        }
        return $js;
    }

    public function viewPage($pagename, $d = false)
    {
        $this->pagename = $pagename;
        $this->pagepath = "{$this->pageDir}/{$this->pagename}.html";

        if ($this->theme !== $this->defaultTheme && !file_exists($this->pagepath))
            $this->pagepath = $this->parentDir . "themes/{$this->defaultTheme}/html/pages/{$this->pagename}.html";

        if (!file_exists($this->pagepath)) {
            if (!$d)
                return '';

            $this->pagepath = str_replace(array($this->themeDir, "themes/{$this->defaultTheme}"), $d . '/theme', $this->pagepath);
        }

        $open = fopen($this->pagepath, 'r');
        $this->content = @fread($open, filesize($this->pagepath));
        fclose($open);

        $this->processVariables();
        return ops_minify_html($this->content);
    }

    public function viewPart($partname, $d = false)
    {
        $this->pagename = $partname;
        $this->pagepath = "{$this->partDir}/{$this->pagename}.html";

        if ($this->theme !== $this->defaultTheme && !file_exists($this->pagepath))
            $this->pagepath = $this->parentDir . "themes/{$this->defaultTheme}/html/parts/{$this->pagename}.html";

        if (!file_exists($this->pagepath)) {
            if (!$d)
                return '';

            $this->pagepath = str_replace(array($this->themeDir, "themes/{$this->defaultTheme}"), $d . '/theme', $this->pagepath);
        }

        $open = fopen($this->pagepath, 'r');
        $this->content = @fread($open, filesize($this->pagepath));
        fclose($open);

        $this->processVariables();
        return ops_minify_html($this->content);
    }

    public function getVariable($var)
    {
        $name = trim($var);

        if (preg_match('/[^a-zA-Z0-9._]/', $name))
            return '';

        if (preg_match('/^[A-Z_]+$/', $name) && defined($name))
            return constant($name);

        $arrayKeys = explode('.', $name);

        if (!isset($GLOBALS['themeVars']))
            $GLOBALS['themeVars'] = array();

        global $themeVars;

        if (count($arrayKeys) === 1) {
            $themeVar = isset($themeVars[$name]) ? $themeVars[$name] : '';

            if (is_array($themeVar))
                $themeVar = json_encode($themeVar);

            return $themeVar;
        }

        $arrayVars = $themeVars;

        foreach ($arrayKeys as $key) {
            if (!isset($arrayVars[$key])) {
                $arrayVars = '';
                break;
            }

            $arrayVars = $arrayVars[$key];
        }

        if (is_array($arrayVars))
            $arrayVars = json_encode($arrayVars);

        return $arrayVars;
    }

    public function processVariables()
    {
        $this->content = preg_replace_callback(
            '/\{\$(.*?)\}/',
            function ($matches) {
                $var = explode('<', $matches[1]);
                $name = trim($var[0]);

                if (preg_match('/[^a-zA-Z0-9._]/', $name))
                    return $matches[0];

                if (preg_match('/^[A-Z_]+$/', $name) && defined($name))
                    return constant($name);

                $arrayKeys = explode('.', $name);

                if (!isset($GLOBALS['themeVars']))
                    $GLOBALS['themeVars'] = array();

                global $themeVars;

                if (count($arrayKeys) === 1) {
                    if (!isset($themeVars[$name]))
                        return '';

                    $themeVar = $themeVars[$name];

                    if (is_array($themeVar))
                        $themeVar = json_encode($themeVar);

                    return $themeVar;
                }

                $arrayVars = $themeVars;

                foreach ($arrayKeys as $key) {
                    if (!isset($arrayVars[$key])) {
                        $arrayVars = '';
                        break;
                    }

                    $arrayVars = $arrayVars[$key];
                }

                if (is_array($arrayVars))
                    $arrayVars = json_encode($arrayVars);

                return $arrayVars;
            },
            $this->content
        );
    }
}

$opsTheme = new OPSTheme();
$opsTheme->addVariable('get', $_GET);
$opsTheme->addVariable('post', $_POST);
$opsTheme->addVariable('theme', array(
    'id' => THEME,
    'url' => 'themes/' . THEME
));
?>
