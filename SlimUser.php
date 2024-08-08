<?php

class SlimUser {
    public $data;
    public $gang;
    public $id;
    public $mprotection;
    public $fbitime;
    public $city;
    public $nerve;
    public $level;
    public $jail;
    public $hospital;
    public $username;
    public $relplayer;
    public $admin;

    public $bustpill;

    public $formattedname;

    public $lastactive;

    function __construct($id) {    
        global $db;

       
        $db->query("SELECT * FROM grpgusers WHERE id = ? LIMIT 1");
        
        $db->bind(1, $id);
    
        $db->execute();
        
        $this->data = $db->fetch_row(true);

        if ($this->data) {
            $this->gang = isset($this->data['gang']) ? $this->data['gang'] : null;
            $this->id = isset($this->data['id']) ? $this->data['id'] : null;
            $this->mprotection = isset($this->data['mprotection']) ? $this->data['mprotection'] : null;
            $this->fbitime = isset($this->data['fbitime']) ? $this->data['fbitime'] : null;
            $this->city = isset($this->data['city']) ? $this->data['city'] : null;
            $this->nerve = isset($this->data['nerve']) ? $this->data['nerve'] : null;
            $this->level = isset($this->data['level']) ? $this->data['level'] : null;
            $this->jail = isset($this->data['jail']) ? $this->data['jail'] : null;
            $this->hospital = isset($this->data['hospital']) ? $this->data['hospital'] : null;
            $this->username = isset($this->data['username']) ? $this->data['username'] : null;
            $this->relplayer = isset($this->data['relplayer']) ? $this->data['relplayer'] : null;
            $this->admin = isset($this->data['admin']) ? $this->data['admin'] : null;
            $this->formattedname = formatName($this->id);
            $this->lastactive = isset($this->data['lastactive']) ? $this->data['lastactive'] : null;
            $this->bustpill = isset($this->data['bustpill'])? $this->data['bustpill'] : null;
        } else {
    
            $this->data = array();
            $this->gang = null;
            $this->id = null;
            $this->mprotection = null;
            $this->fbitime = null;
            $this->city = null;
            $this->nerve = null;
            $this->level = null;
            $this->jail = null;
            $this->hospital = null;
            $this->username = null;
            $this->relplayer = null;
            $this->admin = null;
            $this->formattedname = null;
            $this->lastactive = null;
            $this->bustpill = null;
        }
    }
}
?>
