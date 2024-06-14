<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Emotion
{
    public $emoji;
    public $symbol;

    public function __construct($emoji, $symbol)
    {
        $this->emoji = $emoji;
        $this->symbol = $symbol;
    }
}

class Emotions
{
    public $emotions = [];
    public static $WIDTH = 25;
    public static $HEIGHT = 25;

    public function __construct()
    {
        if (count($this->emotions) > 1) {
            return;
        }

        $emotions = [];
        $emotions[] = new Emotion('🙂', ':)');
        $emotions[] = new Emotion('🙁', ':(');
        $emotions[] = new Emotion('🙃', '(:');
        $emotions[] = new Emotion('😐', ':|');
        $emotions[] = new Emotion('😮', ':O');
        $emotions[] = new Emotion('😀', ':D');
        $emotions[] = new Emotion('🤬', ':@');
        $emotions[] = new Emotion('😉', ';)');
        $emotions[] = new Emotion('😳', ':$');
        $emotions[] = new Emotion('😴', ':8');
        $emotions[] = new Emotion('😕', ':/');
        $emotions[] = new Emotion('😎', '8)');
        $emotions[] = new Emotion('👿', '(6)');
        $emotions[] = new Emotion('🤷', '^)');
        $emotions[] = new Emotion('😘', ':*');
        $emotions[] = new Emotion('😤', '8(');
        $emotions[] = new Emotion('❤️', '<3');

        $this->emotions = $emotions;
    }

    public function getEmotion($e)
    {
        foreach ($this->emotions as $emotion) {
            if ($emotion->symbol != '') {
                $e = str_replace([$emotion->symbol . ' ', ' ' . $emotion->symbol], $emotion->emoji, $e);
            }
        }

        return $e;
    }
}
