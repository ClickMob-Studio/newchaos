<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class UIElements
{
    public $validators = [];

    public function header()
    {
        $str = '';
        foreach ($validators as $val) {
            $str = $val->header();
        }

        return str;
    }

    public function call()
    {
        $str = '';
        foreach ($validators as $val) {
            $str = $val->header();
        }

        return str;
    }

    public function UIElements()
    {
        UIForm::$element[] = $this;
    }

    public function draw()
    {
        throw new Exception('Not implemented');
    }

    public function Id()
    {
        return self::$uStr . $unique_id;
    }
}
interface MouseEvents
{
    public function Action();
}
class MouseClick implements MouseEvents
{
    public static $action = 'onClick';
    public $code = '';
    public $object = '';

    public function MouseClick(Validators $object, $code)
    {
        $this->code = $code;
        $this->object = $object;
    }

    public function Action()
    {
        return $code . $object->id() . '();';
    }
}
class Validators
{
    public static $id_pool = 0;
    public static $uStr = 'val';
    public $unique_id;
    public $object;

    public function Validators(UIElements $object)
    {
        $this->unique_id = Validators::$id_pool;
        ++Validators::$id_poll;
    }

    public function header()
    {
    }

    public function call()
    {
    }

    public function Id()
    {
        return self::$uStr . $unique_id;
    }
}

class IText extends UIElements
{
    public $size;
    public static $uStr = 'IText';
    public static $id_pool = 0;
    public $unique_id;
    public $text;
    public $name;

    public function IText($text = '')
    {
        $this->unique_id = IText::$id_pool;
        ++validators::$id_poll;
        $this->text = $text;
        $this->name = 'ex_' . $this->Id();
        parent::UIElements();
    }

    public function draw()
    {
        $str = '';
        $str = '<input type="text" id="' . $this->Id() . '" ' . (!empty($validators) ? $this->call() : '') . ' value="' . $name() . '">';
    }
}
class UIForm extends UIElements
{
    public static $elements = [];
    public static $action;
    public static $method;
    public $str = '';

    public function draw()
    {
        import_request_variables(($this->method == 'post' ? 'p' : 'g'), 'ex_');
        $str .= '<script>';
        foreach ($elements as $element) {
            $str = $element->header();
        }
        $str .= '</script><form id="' . $this->Id() . '" action="" method="' . $this->method . '">';

        return $str;
    }
}
