<?php

$maxBet = $user_class->level * 100;

try {

} catch ( SoftException $e ) {
	echo HTML::ShowErrorMessage( $e->getMessage() );
	require_once 'footer.php';
	exit;
}

try {
	if ( $user_class->slots == 0 ) {
		throw new SoftException( MEGASLOTS_NOT_MORE_TURNS );
	}

	if ( $_POST['pull'] == 'lever' ) {

		if ( $user_class->slots > 0 ) {
			if ( (int) $_POST['bet'] < 100 ) {
				throw new SoftException( 'The minimum bet is $100.' );
			}

			if ( $user_class->money < (int) $_POST['bet'] ) {
				throw new SoftException( SLOTS_NOT_HAVE_MONEY );
			}

			if ( (int) $_POST['bet'] > $maxBet ) {
				throw new SoftException( 'The maximum bet is $' . number_format( $maxBet ) . '.' );
			}

			$user_class->RemoveFromAttribute( 'money', (int) $_POST['bet'] );
			$newslots                            = $user_class->slots - 1;
			$response['ajaxValue']['slotsTurns'] = $newslots;
			if ( ! $newslots ) {
				$response['ajaxShow']['slotsOutOfTurns'] = 1;
				$response['ajaxShow']['slotsPull']       = 0;
			}
			$user_class->SetAttribute( 'slots', $newslots );
            BattlePass::addExp($user_class->id, 25);
            $user_class->addActivityPoint();
			$resultIds = array(
				'7'          => 0,
				'bell'       => 1,
				'horse-shoe' => 2,
				'lemon'      => 3,
				'diamond'    => 4,
				'cherry'     => 5,
			);

			$potentialWins = array(
				array(
					'result'     => array( 'jackpot', 'jackpot', 'jackpot' ),
					'chance'     => 0.000001,
					'multiplier' => 20,
				),
				array(
					'result'     => array( '7', '7', '7' ),
					'chance'     => 8,
					'multiplier' => 15,
				),
				array(
					'result'     => array( 'diamond', 'diamond', 'diamond' ),
					'chance'     => 8,
					'multiplier' => 'points',
				),
				array(
					'result'     => array( 'bell', 'bell', 'bell' ),
					'chance'     => 16,
					'multiplier' => 10,
				),
				array(
					'result'     => array( 'horse-shoe', 'horse-shoe', 'horse-shoe' ),
					'chance'     => 18,
					'multiplier' => 7,
				),
				array(
					'result'     => array( 'lemon', 'lemon', 'lemon' ),
					'chance'     => 20,
					'multiplier' => 5,
				),
				array(
					'result'     => array( 'cherry', 'cherry', 'cherry' ),
					'chance'     => 22,
					'multiplier' => 3,
				),
				array(
					'result'     => array( 'cherry', 'cherry' ),
					'chance'     => 35,
					'multiplier' => 2,
				),
				array(
					'result'     => array( 'cherry' ),
					'chance'     => 100,
					'multiplier' => 1,
				),
			);

			$percentageWin = random_int( 0, 236 );
			foreach ( $potentialWins as $wins ) {
				if ( $percentageWin < $wins['chance'] ) {
					$winResult = $wins;
					$results   = $wins['result'];
					if ( count( $wins['result'] ) < 3 ) {
						$winning     = array_unique( $results );
						$fullResults = array_pad( $results, 3, 'random' );
						shuffle( $fullResults );
						$potential = array_diff( array_keys( $resultIds ), $winning );
						$results   = array();
						foreach ( $fullResults as $fullResult ) {
							if ( $fullResult === 'random' ) {
								$results[] = $potential[ mt_rand( 0, count( $potential ) - 1 ) ];
							} else {
								$results[] = $fullResult;
							}
						}
					}

					$response['slotsResult'] = $results;
					break;
				}
			}

			if ( ! isset( $response['slotsResult'] ) ) {
				$lose = array_keys( $resultIds );
				// We have to remove cherry, as it results in a win
				$lose = array_diff( $lose, array( 'cherry' ) );
				$lose = array_merge( $lose, $lose );
				shuffle( $lose );
				$response['slotsResult'] = array_slice( $lose, 0, 3 );
			}

			if ( isset( $winResult ) ) {
				if ( $winResult['multiplier'] === 'points' ) {
					$user_class->AddToAttribute( 'points', 10 );

					DailyTasks::recordUserTaskAction( DailyTasks::SLOT_MACHINE, $user_class, 1 );

					throw new SuccessResult( 'Congratulations, you won <strong>10 points</strong>.' );
				}
				$winAmount = floor( (int) $_POST['bet'] * $winResult['multiplier'] );
				$user_class->AddToAttribute( 'money', $winAmount );

				if ( $winResult['multiplier'] >= 5 ) {
					DailyTasks::recordUserTaskAction( DailyTasks::SLOT_MACHINE, $user_class, 1 );
				}
				$diff        = array_diff( $winResult['result'], array( 'jackpot' ) );
				$jackpotText = '';
				if ( ! count( $diff ) ) {
					$jackpotAmount = DailyTasks::getJackpot();
					DailyTasks::payOutJackpot( $user_class->id );
					$response['ajaxValue']['slotsJackpot'] = DailyTasks::getJackpot();
					$jackpotText                           = '<br>You won the jackpot! An additional <strong>$' . number_format( $jackpotAmount ) . '</strong> has been credited to you';
				}

				throw new SuccessResult( 'Congratulations, you won <strong>$' . number_format( $winAmount ) . '</strong>.' . $jackpotText );
			}
			DailyTasks::increaseSlotJackpot( $_POST['bet'] * .1 );
			$response['ajaxValue']['slotsJackpot'] = DailyTasks::getJackpot();
			$messages                              = array(
				'Sadly no dice this time, why not try again?',
				'Uh oh, so far yet so close, maybe luck will be on your side for the next spin?',
				'Wowza, that one was close. Could have been a winner for sure.',
				'Maybe the machine is broken, but regardless you didn\'t win.',
				'I hope you still have enough money to pay your house upkeep, as you didn\'t win anything that spin.',
				'Nothing to say, just sadness, $' . number_format( (int) $_POST['bet'] ) . ' down the drain eh.',
			);
			throw new FailedResult( $messages[ mt_rand( 0, count( $messages ) - 1 ) ] );
		}
	}
} catch ( SuccessResult $s ) {
	$response['result']  = 'success';
	$response['message'] = $s->getMessage();
} catch ( FailedResult $f ) {
	$response['result']  = 'failed';
	$response['message'] = $f->getMessage();
} catch ( SoftException $e ) {
	$response['result']  = 'error';
	$response['message'] = $e->getMessage();
}
