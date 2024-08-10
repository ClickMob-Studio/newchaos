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
    public $jail_bot_credits;
    public $exp;
    public $crimesucceeded;
    public $nerref;
    public $maxnerve;
    public $points;
    public $money;

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
            $this->jail_bot_credits = isset($this->data['jail_bot_credits']) ? $this->data['jail_bot_credits'] : null;
            $this->is_jail_bots_active = isset($this->data['is_jail_bots_active'])? $this->data['is_jail_bots_active'] : null;
            $this->exp = isset($this->data['exp']) ? $this->data['exp'] : null;
            $this->crimesucceeded = isset($this->data['crimesucceeded'])? $this->data['crimesucceeded'] : null;
            $this->nerref = isset($this->data['nerref']) ? $this->data['nerref'] : null;
            $this->maxnerve = 4 + $this->level;
            $this->points = isset($this->data['points'])? $this->data['points'] : null;
            $this->money = isset($this->data['money'])? $this->data['money'] : null;
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
            $this->jail_bot_credits = null;
            $this->is_jail_bots_active = null;
            $this->exp = null;
            $this->crimesucceeded = null;

        }
    }
}
?>
