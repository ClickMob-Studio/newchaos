<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LittlePlayer.
 *
 * @author teixeira
 */
class GangLittlePlayer
{
    public $files;
    public $autostart;
    public $gang;

    public function GangLittlePlayer($gang_class, $resize = 1, $lvl = '', $autostart = 0)
    {
        $this->autostart = $autostart;
        $this->gang = $gang_class;
        $files = [0 => GangMediaFile::GetGangFiles($gang_class->id)];
        if (count($files[0]) == 0) {
            throw new Exception();
        }
    }

    public function generate_code()
    {
        $code = '<object type="application/x-shockwave-flash" width="400" height="15"
            data="flashPlayer/player_slim.swf?playlist_url=playlist.php%3Fgang=' . $this->gang->id . '&repeat_playlist=false&player_title=GeneralForces' . ($this->autostart != 0 ? '&autoload=true&autoplay=true' : '') . '">
            <param name="movie"
            value="flashPlayer/player_slim.swf?playlist_url=playlist.php%3Fgang=' . $this->gang->id . '&repeat_playlist=false&player_title=GeneralForces' . ($this->autostart != 0 ? '&autoload=true&autoplay=true' : '') . '">
            </object>';

        return $code;
    }

    public static function has_elements($gang_class)
    {
        $files = [0 => GangMediaFile::GetGangFiles($gang_class->id)];
        if (count($this->files[0]) == 0) {
            return false;
        }

        return true;
    }

    public static function generatePlaylist($gang_class)
    {
        $files = GangMediaFile::GetGangFiles($gang_class->id);
        if (count($files[0]) == 0) {
            return null;
        }

        $string = '<?xml version="1.0" encoding="UTF-8"?>';
        $string .= '<playlist version="1" xmlns="http://xspf.org/ns/0/">';
        $string .= '<trackList>';
        foreach ($files as $elements) {
            $string .= '<track><location>' . MEDIA_FILES_PATH . 'gang/' . $gang_class->id . '/' . addslashes($elements->path) . '</location><title>' . $elements->name . '</title></track>';
        }
        $string .= '</trackList>';
        $string .= '</playlist>';

        return $string;
    }
}
