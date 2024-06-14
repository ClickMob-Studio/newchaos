<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class TCity extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('City.php', $step, $user);
    }

    public function after()
    {
    }

    public function Message()
    {
        $str = sprintf(TUTORIAL_MESSAGE_CITY, $this->user->formattedname, $this->user->GetCity()->name);

        return $str;
    }
}
class TCrimeContract extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('crimeContract', $step, $user);
    }

    public function after()
    {
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_CRIME_CONTRACT;

        return $str;
    }
}
class THitlist extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('Hitlist', $step, $user);
    }

    public function after()
    {
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_HITLIST;

        return $str;
    }
}
class TSkins extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('Skins', $step, $user);
    }

    public function after()
    {
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_SKIN;

        return $str;
    }
}
class TKings extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('Kings', $step, $user);
    }

    public function after()
    {
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_KINGS;

        return $str;
    }
}
class TConcerts extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('Concerts', $step, $user);
    }

    public function after()
    {
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_CONCERTS;

        return $str;
    }
}

class TDowntown extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('downtown.php', $step, $user);
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_TDOWNTOWN;

        return $str;
    }
}
class TTrain extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('Trained', $step, $user);
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_TRAIN;

        return $str;
    }
}
class TEnergyAwake extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('EnergyAwake', $step, $user);
    }

    public function Message()
    {
        $str = sprintf(TUTORIAL_MESSAGE_AWAKE, $this->user->GetCity()->name);

        return $str;
    }
}
class TCell extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('Cell', $step, $user);
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_CELL;

        return $str;
    }
}
class TCrime extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('Crime', $step, $user);
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_CRIME;

        return $str;
    }
}
class TVote extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('Vote', $step, $user);
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_VOTE;

        return $str;
    }

    public function after(&$tutorial)
    {
    }
}
class TBank extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('bank', $step, $user);
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_BANK;

        return $str;
    }
}
class TMenu extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('Menu', $step, $user);
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_MENU;

        return $str;
    }
}
class TMail extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('Mail', $step, $user);
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_MAIL;

        return $str;
    }
}
class TRules extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('Rules', $step, $user);
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_RULES;

        return $str;
    }
}
class TSupport extends Generic
{
    public function __construct($step, $user)
    {
        parent::__construct('Support', $step, $user);
    }

    public function Message()
    {
        $str = TUTORIAL_MESSAGE_SUPPORT;

        return $str;
    }
}

abstract class Generic
{
    public $ObjectClassName = '';
    public $step;
    public $user;

    public function __construct($objectName, $step, $user)
    {
        $this->ObjectClassName = $objectName;
        $this->step = $step;
        $this->user = $user;
    }

    public function getMessage($update = 0)
    {
        if ($update) {
            $str = $this->Message();
        } else {
            $str = '<div id="Tutorial">' . $this->Message() . '</div>';
        }

        return $str;
    }

    abstract public function Message();

    public function before(&$tutorial)
    {
        return '';
    }

    public function after(&$tutorial)
    {
        return '';
    }
}
class Tutorial
{
    public $user;
    public $id;
    public $step = 0;
    public $update = 0;
    public $step_objects = [];
    public $vote = 0;

    public $fabrica = ['TCity','TDowntown','TTrain','TEnergyAwake','TCrime','THitlist',
        'TCrimeContract','TSkins','TKings','TConcerts','TVote','TBank','TMenu','TMail','TRules',
            'TSupport', ];

    public function __construct($user)
    {
        $this->id = $user->id;
        $this->user = $user;
        $this->object_factory();
    }

    public function __wakeup()
    {
        $this->user = UserFactory::getInstance()->getUser($_SESSION['id']);
    }

    public function getMessage()
    {
        if (isset($this->step_objects[$this->step])) {
            $str = $this->step_objects[$this->step]->getMessage();
        } else {
            $str = 'Tutorial Error';
        }
        $str .= '<br><br><a href="?tutorial_finish">' . TUTORIAL_CANCEL . '</a>';

        return HTML::ShowMessagewithoutdots($str, '<img src="images/buttons/information.png">' . TUTORIAL . ' <span id="contador">' . ($this->step + 1) . '</span>/' . count($this->fabrica));
    }

    public function setVote()
    {
        if ($this->vote == 0) {
            $this->vote = 1;

            return true;
        }

        return false;
    }

    public function setDone($var)
    {
        if ($var == $this->step_objects[$this->step]->ObjectClassName) {
            echo $this->step_objects[$this->step]->after($this);
            unset($this->step_objects[$this->step]);
            ++$this->step;
            if ($this->step == count($this->fabrica)) {
                $_SESSION['final_tutorial'] = 1;
                unset($_SESSION['tutorial']);
                $this->finish();
                echo '<script>window.location.reload();</script>';
                exit();
            }
            $this->object_factory();
            $this->update = 1;
            echo preg_replace("/[\n\r]/", '', '<script>document.getElementById("Tutorial").innerHTML="' . $this->step_objects[$this->step]->getMessage(1) . '";</script>');
            echo '<script>document.getElementById("contador").innerHTML="' . ($this->step + 1) . '";</script>';
            echo $this->step_objects[$this->step]->before($this);
        }
    }

    public function finish()
    {
        $this->user->SetAttribute('tutorial', 1);
    }

    public function object_factory()
    {
        $class = new ReflectionClass($this->fabrica[$this->step]);
        $instance = $class->newInstance($this->step, $this->user);
        $this->step_objects[$this->step] = $instance;
    }
}
