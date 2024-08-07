<?php
error_reporting(E_ALL);
error_reporting(-1);
ini_set('error_reporting', E_ALL);
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

    public $money;

    function __construct($id) {    
        global $db;

        $query = $db->prepare("SELECT * FROM grpgusers WHERE id = ? LIMIT 1");
        $query->execute(array($id));
        $this->data = $query->fetch_row();

        if ($this->data) {
            $this->money = $this->data['money'];
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
        } 
    }
}
?>
