<?php

class DailyTasks extends BaseObject {

	const TRAIN_GYM_SPEED    = 1;
	const TRAIN_GYM_STRENGTH = 2;
	const TRAIN_GYM_DEFENSE  = 3;
	const COMMIT_CRIMES      = 4;
	const USE_MAX_NERVE      = 5;
	const ATTACK_PLAYERS     = 6;
	const BLACK_JACK         = 7;
	const SLOT_MACHINE       = 8;
	const REFILL_ENERGY      = 9;
	const REFILL_NERVE       = 10;
	const COMPLETE_JOB_TASKS = 11;
    const BOSS_FIGHTS        = 12;

	public static $dataTable = 'daily_tasks';
	public static $idField   = 'id';

	private static $hardTasks = array(
		self::TRAIN_GYM_SPEED,
		self::TRAIN_GYM_STRENGTH,
		self::TRAIN_GYM_DEFENSE,
		self::COMMIT_CRIMES,
		self::USE_MAX_NERVE,
		self::ATTACK_PLAYERS,
		self::BLACK_JACK,
		self::SLOT_MACHINE,
		self::REFILL_ENERGY,
		self::REFILL_NERVE,
        self::BOSS_FIGHTS,
	);

	public static function GetAllDailyTasks( $where = '' ) {
		return parent::GetAll( self::GetDataTableFields(), self::GetDataTable(), $where );
	}

	/**
	 * Retrieve the reward for each task that gets completed.
	 *
	 * @return float[]|int[]
	 */
	public static function getTaskReward( User $user ) {
		$level = $user->level;

		return array(
			'points' => 40,
			'exp'   => round( ( $user->maxexp / 100 ) * 15 ),
		);
	}

	/**
	 * Retrieve the daily tasks for today.
	 *
	 * @throws Exception
	 */
	public static function getTodaysDailyTasks(): ?array {
		$today     = new \DateTime();
		$dailyTask = self::GetAllDailyTasks( 'date = "' . $today->format( 'Y-m-d' ) . '"' );
		if ( count( $dailyTask ) > 0 ) {
			return array( $dailyTask[0]->task_1, $dailyTask[0]->task_2, $dailyTask[0]->task_3 );
		}

		return null;
	}

	/**
	 * Get a users progress towards their tasks today.
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public static function getUserTaskProgress( User $user ) {
		global $conn;

		$todaysTasks = self::getTodaysDailyTasks();
		if ( ! $todaysTasks ) {
			return null;
		}

		$today     = new \DateTime();
		$todayDate = $today->format( 'Y-m-d' );
		$stmt      = $conn->prepare( 'SELECT * FROM user_daily_tasks WHERE user_id = ? AND task_date = ?' );
		$stmt->bind_param( 'is', $user->id, $todayDate );
		$stmt->execute();
		$userProgresses = $stmt->get_result()->fetch_all( MYSQLI_ASSOC );

		$progress = array();
		foreach ( $todaysTasks as $task ) {
			$hasProgress       = current(
				array_filter(
					$userProgresses,
					function ( $userProgress ) use ( $task ) {
						return (int) $userProgress['task_type'] === (int) $task;
					}
				)
			);
			$progress[ $task ] = ! empty( $hasProgress ) ? array(
				'progress' => (int) $hasProgress['task_total'],
				'complete' => (int) $hasProgress['task_complete'] === 1,
			) : array(
				'progress' => 0,
				'complete' => false,
			);
		}

		return $progress;
	}

	/**
	 * Record a user task action.
	 *
	 * @throws Exception
	 */
	public static function recordUserTaskAction( int $taskType, User $user, int $amount ) {
		$dailyTasks = self::getTodaysDailyTasks();
		if ( $dailyTasks && in_array( $taskType, $dailyTasks ) ) {
			UserDailyTasks::recordAction( $taskType, $user, $amount );
		}
	}

	/**
	 * Cron function to select tasks.
	 *
	 * @throws Exception
	 *
	 * @return bool
	 */
	public static function dailySelectTasks() {
		$hardTasks = array_rand( self::$hardTasks, 3 );

		$today = new \DateTime();
		self::AddRecords(
			array(
				'date'   => $today->format( 'Y-m-d' ),
				'task_1' => self::$hardTasks[ $hardTasks[0] ],
				'task_2' => self::$hardTasks[ $hardTasks[1] ],
				'task_3' => self::$hardTasks[ $hardTasks[2] ],
			),
			self::GetDataTable()
		);

		View::clearCache( 'sidebar/daily_tasks' );

		return true;
	}
	public static function increaseSlotJackpot( float $amount ): void {
		DBi::$conn->query( 'UPDATE server_variables SET value = value + ' . $amount . ' WHERE field = \'jackpot\'' );
	}

	public static function payOutJackpot( int $uid ): void {
		$pot = self::getJackpot();
		User::SAddMoney( $uid, $pot );
		DBi::$conn->query( 'UPDATE server_variables SET value = 0 WHERE field = \'jackpot\'' );
	}

	public static function getJackpot(): float {
		$selectPot = DBi::$conn->query( 'SELECT value FROM server_variables WHERE field = \'jackpot\'' );
		$row       = mysqli_fetch_assoc( $selectPot );
		return (float) $row['value'];
	}

	/**
	 * Retrieve the message for a specific task.
	 *
	 * @return string|null
	 */
	public static function getMessage( int $taskType, User $user ) {
		$amount = self::getAmountRequired( $taskType, $user );
		if ( ! $amount ) {
			return null;
		}

		$messages = array(
			self::TRAIN_GYM_SPEED    => 'Use ' . $amount . ' energy training Speed',
			self::TRAIN_GYM_STRENGTH => 'Use ' . $amount . ' energy training Strength',
			self::TRAIN_GYM_DEFENSE  => 'Use ' . $amount . ' energy training Defense',
			self::COMMIT_CRIMES      => 'Successfully complete ' . $amount . ' missions',
			self::USE_MAX_NERVE      => 'Use ' . $amount . ' nerve completing missions',
			self::ATTACK_PLAYERS     => 'Fight ' . $amount . ' other players successfully',
			self::BLACK_JACK         => 'Get Blackjack on Blackjack',
			self::SLOT_MACHINE       => 'Win x5 or more on Slots',
			self::REFILL_ENERGY      => 'Refill Energy using points',
			self::REFILL_NERVE       => 'Refill Nerve using points',
			self::COMPLETE_JOB_TASKS => 'Complete 3 Job Tasks',
            self::BOSS_FIGHTS        => 'Fight ' . $amount . ' bosses',
		);

		if ( isset( $messages[ $taskType ] ) ) {
			return $messages[ $taskType ];
		}
	}

	/**
	 * Get the amount required for the specific task.
	 *
	 * @return mixed
	 */
	public static function getAmountRequired( int $taskType, User $user ) {
		$amounts = array(
			self::TRAIN_GYM_SPEED    => $user->GetMaxEnergy() * 4,
			self::TRAIN_GYM_STRENGTH => $user->GetMaxEnergy() * 4,
			self::TRAIN_GYM_DEFENSE  => $user->GetMaxEnergy() * 4,
			self::COMMIT_CRIMES      => 15,
			self::USE_MAX_NERVE      => $user->GetMaxNerve() * 4,
			self::ATTACK_PLAYERS     => 5,
			self::BLACK_JACK         => 1,
			self::SLOT_MACHINE       => 1,
			self::REFILL_ENERGY      => 1,
			self::REFILL_NERVE       => 1,
			self::COMPLETE_JOB_TASKS => 3,
            self::BOSS_FIGHTS        => 5,
		);

		if ( isset( $amounts[ $taskType ] ) ) {
			return $amounts[ $taskType ];
		}
	}

	protected static function GetDataTable() {
		return self::$dataTable;
	}

	protected static function GetDataTableFields() {
		return array(
			'id',
			'date',
			'task_1',
			'task_2',
			'task_3',
		);
	}

	protected function GetIdentifierFieldName() {
		return self::$idField;
	}

	protected function GetClassName() {
		return __CLASS__;
	}
}
