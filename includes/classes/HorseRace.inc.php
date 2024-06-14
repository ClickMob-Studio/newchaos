<?php

final class HorseRace extends BaseObject
{
    public static $idField = 'id';
    public static $dataTable = 'horserace';
    public static $horses_favorite_preference = 25;
    public static $rake = 5;
    public static $horseColors = [0 => '#065901',1 => '#a06e01',2 => '#969402',3 => '#4f4f47',4 => '#671f72',5 => '#c931b5',6 => '#192675'];
    public static $horseIcons = [
        0 => 'fas fa-chess-knight',
        1 => 'fas fa-dragon',
        2 => 'fas fa-skull',
        3 => 'fas fa-moon',
        4 => 'fas fa-chess-king',
        5 => 'fas fa-skull-cow',
        6 => 'fas fa-sun',
    ];
    public static $horseNames = ["Ruby Slipper", 'Forest Attraction', 'Winter Soul', 'Rory Knows', 'Billiards Prince', 'King Of Kings', 'Forever Magic'];
    public static $FINISH = '0';
    public static $RUNNING = '1';
    public static $TIME_BETWEEN_RACES = 235;

    public function __construct($id)
    {
        parent::__construct($id);
    }

    public static function getRace()
    {
        $sql = 'select * from horserace where stat=' . self::$RUNNING;
        $res =DBi::$conn->query($sql);
        if (mysqli_num_rows($res) == 0) {
            return null;
        }
        $row = mysqli_fetch_object($res);

        return new HorseRace($row->id);
    }

    public function Winner()
    {
        $horses = $this->calculate_odds();

        $winner = rand(1, 98);
        $i = 0;
        while ($winner > 0) {
            $winner -= $horses[$i];
            ++$i;
            $horse_winner = $i - 1;
        }

        return $horse_winner;
    }

    public function Finish()
    {
        if ($this->timetostart > time()) {
            throw new Exception('Not finished');
        }
        $winner = -1;
        $bets = $this->Bets();
        if (count($bets) > 0) {
            $bet_owners = [];
            $bet_on_horses = [];
            foreach ($bets as $bet) {
                $bet_owners[$bet->user_id] = 1;
            }
            foreach ($bets as $bet) {
                $bet_on_horses[$bet->horse] = 1;
            }

            if (count($bet_owners) > 1) {
                $total = 0;
                $r = 0;
                $i = 0;
                $lucky = [];
                while ($r == 0 && count($lucky) < 7) {
                    $total = 0;
                    $r = 0;
                    $winner = $this->Winner();
                    $bets = $this->Bets();
                    $odd = $odds[$winner];
                    foreach ($bets as $bet) {
                        $total += $bet->value;
                        if ($bet->horse == $winner) {
                            $r += $bet->value;
                        }
                    }
                    $lucky[$winner] = 1;
                }

                $winners = [];
                $losers = [];
                foreach ($bets as $bet) {
                    if ($bet->horse == $winner) {
                        $user = UserFactory::getInstance()->getUser($bet->user_id);
                        $value = $bet->value * ($total / $r);
                        if (count($bet_on_horses) > 1) {
                            $value -= $value * (HorseRace::$rake / 100);
                        }
                        $value = (int) $value;
                        if ($value < $bet->value) {
                            $value = $bet->value;
                        }

                        $user->AddToAttribute('money', $value);
                        $this->addLog($user->id, $value);
                        if (isset($losers[$bet->user_id])) {
                            unset($losers[$bet->user_id]);
                        }
                        if (isset($winners[$bet->user_id])) {
                            $winners[$bet->user_id] += $value;
                        } else {
                            $winners[$bet->user_id] = $value;
                        }
                        unset($user);
                    } else {
                        if (!isset($winners[$bet->user_id])) {
                            $losers[$bet->user_id] = 1;
                        }
                    }
                }
                foreach ($winners as $key => $value) {
                    Event::Add($key, self::$horseNames[$winner] . ' horse was the winner. You won $' . number_format($value));
                }
                foreach ($losers as $key => $value) {
                    Event::Add($key, 'Horse ' . self::$horseNames[$winner] . " was the winner. You didn't win, better luck next time!");
                }
            } else {
                foreach ($bets as $bet) {
                    $total += $bet->value;
                }

                foreach ($bet_owners as $key => $value) {
                    $user = UserFactory::getInstance()->getUser($key);
                    $user->AddToAttribute('money', $total);
                    Event::Add($bet->user_id, 'The horse race you bet on has finished, there was only one person who bet, so all money was returned to you.');
                    unset($user);
                }
            }
        }

        $this->SetAttribute('stat', self::$FINISH);
        $this->SetAttribute('winner', $winner);
    }

    public function addLog($user, $value)
    {
        $args = ['user_id' => $user,
            'raceid' => $this->id,
            'time' => time(),
            'value' => $value,
        ];
        self::AddRecords($args, 'horseracelog');
    }

    public function calculate_odds()
    {
        return [$this->horse_1, $this->horse_2, $this->horse_3, $this->horse_4, $this->horse_5,
            $this->horse_6, $this->horse_7, ];
    }

    public function pot()
    {
        $bets = $this->Bets();
        $pot = 0;
        foreach ($bets as $bet) {
            $pot += $bet->value;
        }

        return $pot;
    }

    public static function newRace()
    {
        $num1 = rand(0, 6);
        $num2 = -1;
        while ($num2 == -1) {
            $num2 = rand(0, 6);
            if ($num2 == $num1) {
                $num2 = -1;
            }
        }
        $horse[$num1] = rand(20, 40);
        $horse[$num2] = rand(10, 30);
        $rest = 100 - $horse[$num1] - $horse[$num2];
        $l = 5;
        for ($i = 0; $i < 7; ++$i) {
            if ($i != $num1 && $i != $num2) {
                $horse[$i] = rand(1, $rest - $l);
                $rest -= $horse[$i];
                --$l;
            }
        }
        $horse[rand(0, 6)] += $rest;
        $args = ['horse_1' => $horse[0],
            'horse_2' => $horse[1],
            'horse_3' => $horse[2],
            'horse_4' => $horse[3],
            'horse_5' => $horse[4],
            'horse_6' => $horse[5],
            'horse_7' => $horse[6],
            'time' => time(),
            'timetostart' => time() + (HorseRace::$TIME_BETWEEN_RACES * 60),
            'stat' => HorseRace::$RUNNING,
        ];
        self::AddRecords($args, self::GetDataTable());
    }

    public static function getLast5Races()
    {
        $sql = 'select * from horserace where stat=' . self::$FINISH . ' and winner!=-1 order by time desc limit 5';
        $res =DBi::$conn->query($sql);
        $races = [];
        while ($row = mysqli_fetch_object($res)) {
            $row->winner = self::$horseNames[$row->winner];
            $sql = 'select count(user_id) as number, sum(value) as amount  from horseracelog where raceid=' . $row->id;
            $res1 =DBi::$conn->query($sql);
            $row1 = mysqli_fetch_object($res1);
            if ($row1->number != 0) {
                $row->amount = $row1->amount;
            } else {
                $row->amount = 0;
            }
            $row->number = $row1->number;
            $races[] = $row;
        }

        return $races;
    }

    public function addBet($user, $horse, $value)
    {
        $user_class = UserFactory::getInstance()->getUser($user);
        if ($user_class->money < $value) {
            throw new SoftException("You don't have enough money on hand to place this bet.");
        }
        $args = ['user_id' => $user,
            'horse' => $horse,
            'value' => $value,
            'time' => time(),
            'raceid' => $this->id,
        ];
        $user_class->RemoveFromAttribute('money', $value);
        self::AddRecords($args, 'horsebets');
    }

    public function Bets()
    {
        $sql = 'select * from horsebets where raceid=' . $this->id;
        $res =DBi::$conn->query($sql);
        $bets = [];
        while ($row = mysqli_fetch_object($res)) {
            $bets[] = $row;
        }

        return $bets;
    }

    public function User_Bets($user)
    {
        $sql = 'select * from horsebets where raceid=' . $this->id . ' and user_id=' . $user;
        $res =DBi::$conn->query($sql);
        $bets = [];
        while ($row = mysqli_fetch_object($res)) {
            $bets[] = $row;
        }

        return $bets;
    }

    protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'time',
            'timetostart',
            'horse_1',
            'horse_2',
            'horse_3',
            'horse_4',
            'horse_5',
            'horse_6',
            'horse_7',
            'stat',
            'winner',
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
