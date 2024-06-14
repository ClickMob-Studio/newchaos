<?php

class Transactions
{
    public $units = [];
    public $owner;
    public $buyer;

    public function Transactions($owner, $buyer)
    {
        $this->owner = $owner;
        $this->buyer = $buyer;
        $units['money'] = 0;
        $units['points'] = 0;
    }

    public function validate()
    {
        if ($this->buyer->money < $this->units['money']) {
            throw new Exception(NOT_ENOUGH_MONEY);
        }
        if ($this->buyer->points < $this->units['points']) {
            throw new Exception(NOT_ENOUGH_POINTS);
        }
    }

    public function finishIt()
    {
        try {
            $this->validate();
        } catch (Exception $e) {
            throw $e;
        }
        if (isset($this->units['money'])) {
            User::SRemoveMoney($this->buyer->id, $this->units['money']);
            User::SAddMoney($this->owner->id, $this->units['money']);
        }
        if (isset($this->units['points'])) {
            $this->owner->AddPoints($this->units['points']);
            $this->buyer->RemoveFromAttribute($this->units['points']);
        }
    }

    public function AddMoney($money)
    {
        $this->units['money'] += $money;
    }

    public function AddPoints($points)
    {
        $this->units['points'] += $points;
    }
}
