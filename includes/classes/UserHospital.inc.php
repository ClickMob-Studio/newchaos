<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class UserHospital
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getCause()
    {
        if ($this->user->hhow == 'russian'){
            return 'Lost in Russian Roulette';
        }
        if ($this->user->hhow == 'Roulette') {
            return 'Knife Game';
        }
        if ($this->user->hhow == 'wasmugged') {
            return  'Mugged by ';
        }
        if ($this->user->hhow == 'wasattacked') {
            return  HOSPITAL_ATTACKED_BY;
        }
        if ($this->user->hhow == 'drug overdose') {
            return  DRUGS_OVERDOSE_TXT;
        }
        if ($this->user->hhow == 'firework') {
            return  'Shot with a firework by ';
        }
        if ($this->user->hhow == 'trainingdummy') {
            return  'Lost to a training dummy';
        }
        if ($this->user->hhow == 'carbomb') {
            return  'Blown up by a Car Bomb';
        }
        if ($this->user->hhow == 'vault') {
            return  'Set on fire trying to access vault 516 ';
        }
        
        return  HOSPITAL_LOST_TO;
    }

    public function getAttacker()
    {
        if ($this->user->hhow == 'Knife Game' || $this->user->hhow == 'drug overdose') {
            return ',';
        }
        $test = UserFactory::getInstance()->getUser($this->user->hwhoID);

        return ' ' . $test->username . ', ';
    }

    public function getSAttacker()
    {
        if ($this->user->hhow == 'Knife Game' || $this->user->hhow == 'drug overdose') {
            return ',';
        }

        return ' ' . User::SGetFormattedName($this->user->hwhoID) . ' ';
    }

    public function getTime()
    {
        return Utility::GetMinutesLeftUntil($this->user->hospital);
    }
}
