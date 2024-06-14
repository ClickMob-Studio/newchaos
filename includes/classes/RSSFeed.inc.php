<?php

final class RSSFeed
{
    // VARIABLES

    // channel vars

    public $channel_url;

    public $channel_title;

    public $channel_description;

    public $channel_lang;

    public $channel_copyright;

    public $channel_date;

    public $channel_creator;

    public $channel_subject;

    // image

    public $image_url;

    // items

    public $items = [];

    public $nritems;

    // FUNCTIONS

    // constructor

    public function RSSFeed()
    {
        $this->nritems = 0;

        $this->channel_url = '';

        $this->channel_title = '';

        $this->channel_description = '';

        $this->channel_lang = '';

        $this->channel_copyright = '';

        $this->channel_date = '';

        $this->channel_creator = '';

        $this->channel_subject = '';

        $this->image_url = '';
    }

    // set channel vars

    public function SetChannel($url, $title, $description, $lang, $copyright, $creator, $subject)
    {
        $this->channel_url = $url;

        $this->channel_title = $title;

        $this->channel_description = $description;

        $this->channel_lang = $lang;

        $this->channel_copyright = $copyright;

        $this->channel_date = date('Y-m-d') . 'T' . date('H:i:s') . '+01:00';

        $this->channel_creator = $creator;

        $this->channel_subject = $subject;
    }

    // set image

    public function SetImage($url)
    {
        $this->image_url = $url;
    }

    // set item

    public function AddItem($url, $title, $description, $image)
    {
        $this->items[$this->nritems]['url'] = $url;

        $this->items[$this->nritems]['title'] = $title;

        $this->items[$this->nritems]['description'] = $description;

        switch ($image) {
            case 'down':

                $this->items[$this->nritems]['image'] = 'images/buttons/down.png';

                break;

            case 'static':

                $this->items[$this->nritems]['image'] = 'images/buttons/equal.png';

                break;

            case 'up':

                $this->items[$this->nritems]['image'] = 'images/buttons/up.png';

                break;
        }

        ++$this->nritems;
    }

    // output feed

    public function Output()
    {
        $output = '<?xml version="1.0" encoding="iso-8859-1"?>' . "\n";

        $output .= '<rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://purl.org/rss/1.0/" xmlns:slash="http://purl.org/rss/1.0/modules/slash/" xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:syn="http://purl.org/rss/1.0/modules/syndication/" xmlns:admin="http://webns.net/mvcb/" xmlns:feedburner="http://rssnamespace.org/feedburner/ext/1.0">' . "\n";

        $output .= '<channel rdf:about="' . $this->channel_url . '">' . "\n";

        $output .= '<title>' . $this->channel_title . '</title>' . "\n";

        $output .= '<link>' . $this->channel_url . '</link>' . "\n";

        $output .= '<description>' . $this->channel_description . '</description>' . "\n";

        $output .= '<dc:language>' . $this->channel_lang . '</dc:language>' . "\n";

        $output .= '<dc:rights>' . $this->channel_copyright . '</dc:rights>' . "\n";

        $output .= '<dc:date>' . $this->channel_date . '</dc:date>' . "\n";

        $output .= '<dc:creator>' . $this->channel_creator . '</dc:creator>' . "\n";

        $output .= '<dc:subject>' . $this->channel_subject . '</dc:subject>' . "\n";

        $output .= '<items>' . "\n";

        $output .= '<rdf:Seq>';

        for ($k = 0; $k < $this->nritems; ++$k) {
            $output .= '<rdf:li rdf:resource="' . $this->items[$k]['url'] . '">' . "\n";
        }

        $output .= '</rdf:Seq>' . "\n";

        $output .= '</items>' . "\n";

        $output .= '<image rdf:resource="' . $this->image_url . '">' . "\n";

        $output .= '</channel>' . "\n";

        for ($k = 0; $k < $this->nritems; ++$k) {
            $output .= '<item rdf:about="' . $this->items[$k]['url'] . '">' . "\n";

            $output .= '<title>' . $this->items[$k]['title'] . '</title>' . "\n";

            $output .= '<link>' . $this->items[$k]['url'] . '</link>' . "\n";

            $output .= '<image>https://generalforces.com/' . $this->items[$k]['image'] . '</image>' . "\n";

            $output .= '<description>' . $this->items[$k]['description'] . '</description>' . "\n";

            $output .= '<feedburner:origLink>' . $this->items[$k]['url'] . '</feedburner:origLink>' . "\n";

            $output .= '</item>' . "\n";
        }

        $output .= '</rdf:RDF>' . "\n";

        return $output;
    }
}

?>

