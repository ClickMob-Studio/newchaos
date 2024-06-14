<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*
 * Description of Objectives
 *
 * @author teixeira
 */

define('VAR_CHATA', 2);

final class Objectives extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'objectives';

    public function __construct($id)
    {
        try {
            parent::__construct($id);
        } catch (SoftException $e) {
            parent::AddRecords(['id' => $id], self::GetDataTable());
            parent::__construct($id);
        }
    }

    public static function fValue($const, $level, $degree)
    {
        return $const * $level + ceil(pow($level, $degree));
    }

    public static function fAccValue($const, $level, $degree)
    {
        $v = 0;
        for ($i = 0; $i < $level + 1; ++$i) {
            $v += self::fValue($const, $i, $degree);
        }

        return $v;
    }

    public static function flPbyValue($const, $degree, $value)
    {
        $lp = 0;
        $v = self::fValue($const, 1, $degree);
        while ($value >= $v) {
            ++$lp;
            $v = self::fValue($const, $lp + 1, $degree);
        }

        $lp += ($value - self::fValue($const, $lp, $degree)) / ($v - self::fValue($const, $lp, $degree));

        return $lp;
    }

    public static function flPbyAcc($const, $degree, $value)
    {
        $lp = 0;
        $v = self::fValue($const, 1, $degree);

        while ($value >= $v) {
            ++$lp;
            $v += self::fValue($const, $lp + 1, $degree);
        }

        $lp += ($value + self::fValue($const, $lp + 1, $degree) - $v) / (self::fValue($const, $lp + 1, $degree));

        return $lp;
    }

    public static function calc_rate(User $user, SUser $suser)
    {
        $args['defense'] = ['const' => 25000, 'degree' => 3, 'title' => 'Defense', 'hidden' => 1];
        $args['speed'] = ['const' => 25000, 'degree' => 3, 'title' => 'Speed', 'hidden' => 1];
        $args['strength'] = ['const' => 25000, 'degree' => 3, 'title' => 'Strength', 'hidden' => 1];
        $args['job'] = ['const' => 0, 'degree' => 1, 'title' => 'Job', 'hidden' => 1];
        $args['battlewon'] = ['const' => 1, 'degree' => 2, 'title' => 'Fights Won'];
        $args['concert'] = ['const' => 1, 'degree' => 2, 'title' => 'Operation Level'];
        $args['crimes'] = ['const' => 10, 'degree' => 2, 'title' => 'Missions'];
        $args['crimesmoney'] = ['const' => 100, 'degree' => 3, 'title' => 'Mission Money'];
        $args['daily_mugs'] = ['const' => 50000, 'degree' => 3.525, 'title' => 'Daily Mugged'];
        $args['level'] = ['title' => 'Experience'];
        $args['hitman'] = ['const' => 0, 'degree' => 1, 'title' => 'Fight Contracts'];
        $args['house'] = ['const' => 0, 'degree' => 1, 'title' => 'House'];
        $args['bust'] = ['const' => 0, 'degree' => 2, 'title' => 'Jail Busts'];
        $args['king'] = ['title' => 'Officers'];
        $args['gangentrance'] = ['const' => 450000, 'degree' => 1, 'title' => 'Regiment Loyalty'];
        $args['number_of_muggs'] = ['const' => 1, 'degree' => 2, 'title' => 'Mugs'];
        $args['mugged'] = ['const' => 50000, 'degree' => 3, 'title' => 'Mugged Total'];
        $values = self::vars($user, $suser);
        $args['total']['lp'] = 0;
        do {
            current($args);
            switch (key($args)) {
                case 'bust':
                case 'battlewon':
                case 'number_of_muggs':
                case 'crimes':
                case 'crimesmoney':
                case 'mugged':
                case 'speed':
                case 'strength':
                case 'defense':
                case 'hitman':
                    $lp = self::flPbyAcc($args[key($args)]['const'], $args[key($args)]['degree'], $values[key($args)]);
                    $args[key($args)]['level'] = (int) $lp;

                    $args[key($args)]['perc'] = (int) (($lp - $args[key($args)]['level']) * 100);
                    $args[key($args)]['value'] = $values[key($args)];
                    $args[key($args)]['lp'] = $lp;
                    $args['total']['value'] +=
                        self::fAccValue(0, $args[key($args)]['level'], VAR_CHATA) +
                        (($lp - $args[key($args)]['level']) * self::fValue(0, ceil($lp), VAR_CHATA));

                    break;
                case 'concert':
                case 'daily_mugs':
                case 'gangentrance':
                case 'house':
                case 'job':
                    $lp = self::flPbyValue($args[key($args)]['const'], $args[key($args)]['degree'], $values[key($args)]);
                    $args[key($args)]['level'] = (int) $lp;
                    $args[key($args)]['perc'] = (int) (($lp - $args[key($args)]['level']) * 100);
                    $args[key($args)]['value'] = $values[key($args)];
                    $args[key($args)]['lp'] = $lp;
                    $args['total']['value'] +=
                        self::fAccValue(0, $args[key($args)]['level'], VAR_CHATA) +
                        (($lp - $args[key($args)]['level']) * self::fValue(0, ceil($lp), VAR_CHATA));
                    break;
                case 'king':

                    $args[key($args)]['level'] = $user->lastKing - 1;
                    $args[key($args)]['perc'] = $user->percGuards * 100;
                    $args['total']['value'] += $args[key($args)]['level'] + $user->percGuards;
                    break;
                case 'level':
                    $args[key($args)]['level'] = $user->level - 1;
                    $args[key($args)]['perc'] = (int) (($user->GetNeededXPForLevel($user->level) ? $user->exp / $user->GetNeededXPForLevel($user->level):0.99) * 100);
                    $args['total']['value'] += $args[key($args)]['level'] +  ($user->GetNeededXPForLevel($user->level) ? $user->exp / $user->GetNeededXPForLevel($user->level):1);
                    break;
            }
        } while (next($args));

        $args['total']['value'] /= (count($args) - 1);
        $args['total']['lp'] = self::flPbyAcc(0, VAR_CHATA, $args['total']['value']);
        $args['total']['level'] = (int) $args['total']['lp'];
        $args['total']['title'] = 'Overall';
        $args['total']['perc'] = (int) (($args['total']['lp'] - $args['total']['level']) * 100);

        $args['total']['lp'] = $lp;
        for ($i = 0; $i < count($args); ++$i) {
            if (isset($args[$i]['hidden'])) {
                unset($args[$i]);
            }
        }

        return $args;
    }

    public static function set($userid, $name, $value)
    {
        $camp = new self($userid);
        if ($camp->$name > $value) {
            return;
        }

        $camp->SetAttribute($name, $value);
    }

    public static function vars(User $user, SUser $suser)
    {
        $t = [];
        $vars = new self($user->id);
        $t['bust'] = $suser->busts;
        $t['concert'] = $vars->concertlevel;
        $t['rate'] = ($vars->rate > 0 ? $vars->rate : 0);
        if (time() - $user->gangentrance > $vars->gangentrance && $user->gang != 0) {
            $t['gangentrance'] = time() - $user->gangentrance;
        } else {
            $t['gangentrance'] = $vars->gangentrance;
        }
        $t['house'] = $vars->house;

        $t['crimes'] = $user->crimesucceeded;
        $t['crimesmoney'] = $user->crimemoney;
        $t['daily_mugs'] = $vars->daily_mugs;
        $t['job'] = $vars->job;
        $t['mugged'] = $suser->mugged;
        $t['number_of_muggs'] = $suser->number_of_muggs;
        $t['battlewon'] = $user->battlewon;

        $t['speed'] = $user->speed;

        $t['strength'] = $user->strength;
        $t['defense'] = $user->defense;
        $sql = 'select count(id) as total from ' . HitList::$dataTable . ' where status=' . HitList::COMPLETED . ' and provider=' . $user->id;
        $rs = DBi::$conn->query($sql);
        $row = mysqli_fetch_object($rs);
        $t['hitman'] = $row->total;

        return $t;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'concertlevel',
            'rate',
            'gangentrance',
            'house',
            'job',
            'daily_mugs',
        ];
    }

    protected function GetIdentifierFieldName()
    {
        return self::$idField;
    }

    protected function GetClassName()
    {
        return __CLASS__;
    }
}
