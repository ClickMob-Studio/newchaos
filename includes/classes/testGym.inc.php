<?php
    class testGym extends BaseObject {

        public static $idField = 'id';
        public static $dataTable = 'stocks';

        public $pageName = '';
        public function __construct(){

        }


        public static function method_resetEnergy(User $user) {
            $cost = 20;
            if ($user->points < $cost) {
                return "You can't afford this!";
            }
            if($user->energy >= $user->GetMaxEnergy()){
                echo HTML::ShowErrorMessage("You alrady have full energy");
            }else{
                $cost = $user->points - 20;
                $user->SetAttribute('points', $cost);
                $user->SetAttribute('energy', $user->GetMaxEnergy());
                echo HTML::ShowMessage("You have refilled your energy");
            }
        }

        public static function method_resetAwake(User $user) {
            $cost = 20;
            if($user->points < 20){
                //return throw new FailedResult("You do not have enough points for this");

            }
            if($user->awake >= $user->GetMaxAwake()){
                echo HTML::ShowErrorMessage("You already have full Awake");
                return false;
            }
            $cost = $user->points - $cost;
            $user->SetAttribute('points', $cost);
            $user->SetAttribute('awake', $user->GetMaxAwake());
            echo HTML::ShowMessage("You have filled your awake back to 100%");
        }

        public function method_train($times, User $user, $stat) {

        	$times = abs(intval($times));

        	if (!$times || $times > $user->energy) {
        		throw new SoftException("You don't have enough energy to train this many times!");
        	}
        	switch ((int) $stat) {
        		case 1:
        			$name = "strength";
        			$stat = "strength";
        		break;
        		case 2:
        			$name = "defense";
        			$stat = "defense";
        		break;
        		case 3:
        			$name = "speed";
        			$stat = "speed";
        		break;
        		default:
                    throw new FailedResult("You did not select a valid option");
        			return false;
        	}

        	$awake = $user->awake;

        	$totalGain = 0;

            $newEnergy = $user->energy - $times;

        	while ($times > 0) {
        		$awakeUsed = mt_rand(1, 3);
        		if ($awakeUsed > $awake ) $awakeUsed = $will;
        		$gain = round(
        			mt_rand(1, 3) / mt_rand(800, 1000) * mt_rand(800, 1000) * (($awake + 20) / 150)
        		, 4);

        		$awake -= $awakeUsed;
        		$totalGain += $gain;
        		$times--;
        	}
            $user->SetAttribute('energy', $newEnergy);
        	$times = abs(intval($times));
            $user->AddToAttribute($stat, $totalGain);
            $user->SetAttribute('awake', $awake);

            $newstat = $user->$stat + $totalGain;

           throw new SuccessResult("You have trained $stat and gained $totalGain <br> You now have $newstat $stat");
           return;
         }
           protected static function GetDataTable()
    {
        return self::$dataTable;
    }

    protected static function GetDataTableFields()
    {
        return [
            self::$idField,
            'company_name',
            'cost',
            'direction',
            'color',
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


?>