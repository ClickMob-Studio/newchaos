<?php

include_once 'includes/classes/HTMLParser.inc.php';
include_once 'includes/classes/TidyCss/class.csstidy.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Themeinc.
 *
 * @author teixeira
 */
class Themes
{
    // 1. Write a function with parameter "$element"
    public static $css = [];

    public static function array_mix()
    {
        $array = [];
        $arrays = func_get_args();

        foreach ($arrays as $array_i) {
            if (is_array($array_i)) {
                $array = Themes::array_mixer($array, $array_i);
            }
        }

        return $array;
    }

    public static function array_mix_recursive()
    {
        $array = [];
        $arrays = func_get_args();

        foreach ($arrays as $array_i) {
            if (is_array($array_i)) {
                $array = Themes::array_mixer($array, $array_i, true);
            }
        }

        return $array;
    }

    public static function array_mixer($array_o, $array_i, $recursive = false)
    {
        foreach ($array_i as $k => $v) {
            if (!isset($array_o[$k])) {
                $array_o[$k] = $v;
            } else {
                if (is_array($array_o[$k])) {
                    if (is_array($v)) {
                        if ($recursive) {
                            $array_o[$k] = Themes::array_mixer($array_o[$k], $v);
                        } else {
                            $array_o[$k] = $v;
                        }
                    } else {
                        $array_o[$k][] = $v;
                    }
                } else {
                    if (!isset($array_o[$k])) {
                        $array_o[$k] = $v;
                    } else {
                        $array_o[$k] = [$array_o[$k]];
                        $array_o[$k][] = $v;
                    }
                }
            }
        }

        return $array_o;
    }

    public static function search($name)
    {
        foreach (Themes::$css as $a) {
            if (isset($a[$name])) {
                return $name;
            }
        }

        return null;
    }

    public static function my_callback($element)
    {
        if ($element->tag == 'link' && $element->getAttribute('type') == 'text/css') {
            $element->getAttribute('href');
            $cssi = new csstidy();
            $cssi->parse_from_url($element->getAttribute('href'));
            Themes::$css = Themes::array_mix_recursive($cssi->css, Themes::$css);

            return;
        }
        $parent = $element;
        $class = [];
        while ($parent != null) {
            $parent->removeAttribute('title');

            if ($parent->hasAttribute('class')) {
                if (($str = Themes::search('.' . $parent->getAttribute('class'))) != null) {
                    $class[] = $str;
                }
            } elseif ($parent->hasAttribute('id')) {
                if (($str = Themes::search('#' . $parent->getAttribute('id'))) != null) {
                    $class[] = $str;
                }
            }
            if (($str = Themes::search($parent->tag)) != null) {
                $class[] = $str;
            }
            $parent = $parent->parentNode();
        }
        $styles = implode('=>', $class);
        $element->setAttribute('title', 'Style:' . $styles);
    }

    public function styles($out)
    {
        $html = HTMLParser::str_get_html($out);

        // 3. Register the callback function with it's function name
        $html->set_callback('Themes::my_callback');

        // 4. Callback function will be invoked while dumping

        return  $html;
    }
}
