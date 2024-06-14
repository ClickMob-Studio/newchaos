<?php

use s9e\TextFormatter\Bundles\Forum\Renderer;
use s9e\TextFormatter\Plugins\BBCodes\Parser;

class BBCode
{
    public $tags;

    public $settings;

    /**
     * @var Renderer
     */
    private $renderer;

    /**
     * @var Parser
     */
    private $parser;

    public function __construct()
    {
        $configurator = new s9e\TextFormatter\Configurator();

        $configurator->loadBundle('Forum');
        $configurator->BBCodes->addFromRepository('background');
        $configurator->BBCodes->addFromRepository('align');
        $configurator->BBCodes->addCustom(
            '[player={TEXT?}]{TEXT}[/player]',
            '<a data-player-id="{TEXT}"></a>'
        );
        $configurator->BBCodes->addCustom(
            '[item={TEXT?}]{TEXT}[/item]',
            '<a data-item-id="{TEXT}"></a>'
        );
        $configurator->BBCodes->addCustom(
            '[gang={TEXT?}]{TEXT}[/gang]',
            '<a data-gang-id="{TEXT}"></a>'
        );
        $configurator->BBCodes->addCustom(
            '[gangtag={TEXT?}]{TEXT}[/gangtag]',
            '<a data-gangtag-id="{TEXT}"></a>'
        );
        $configurator->BBCodes->addCustom(
            '[itemimage={TEXT?}]{TEXT}[/itemimage]',
            '<a data-itemimage-id="{TEXT}"></a>'
        );

        $parser = $configurator->finalize();

        $this->renderer = $parser['renderer'];
        $this->parser = $parser['parser'];

        $this->tags = [];

        $this->settings = ['enced' => true];
    }

    public static function SGetFormattedUserName($id)
    {
        if (!User::Exists($id)) {
            return 'Invalid user';
        }

        return User::SGetFormattedName($id);
    }

    public static function SGetFormattedItemName($id)
    {
        return Item::SGetFormattedName($id);
    }

    public static function SGetFormattedItemImage($id)
    {
        return Item::SGetFormattedImage($id);
    }

    public static function SGetPublicFormattedGangName($id)
    {
        return Gang::SGetPublicFormattedName($id);
    }

    public static function SGetGangTagName($id)
    {
        return Gang::SGetTagName($id);
    }

    public function parse_bbcode(string $text)
    {
        $xml = $this->parser->parse(stripslashes($text));
        $output = $this->renderer->render($xml);

        return $this->processCustom($output);
    }

    private function processCustom($output)
    {
        $output = preg_replace_callback(
            '/<a data-(\w++)-id="(\d++)"><\/a>/m',
            function ($m) {
                $type = $m[1];
                $id = (int) $m[2];
                switch ($type) {
                    case 'player':
                        return self::SGetFormattedUserName($id);
                    case 'item':
                        return self::SGetFormattedItemName($id);
                    case 'gang':
                        return self::SGetPublicFormattedGangName($id);
                    case 'gangtag':
                        return self::SGetGangTagName($id);
                    case 'itemimage':
                        return self::SGetFormattedItemImage($id);
                }
            },
            $output
        );
        $output = str_replace('&quot;', '"', $output);

        return $output;
    }
}
