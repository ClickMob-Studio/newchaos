<?php

if ( strpos( $_SERVER['PHP_SELF'], '.inc.php' ) !== false ) {
    die( 'You cannot access this file directly.' );
}
if(!empty($_POST)){
    $user_class->AddToAttribute('poth', 1);
}
try {
    if ( $user_class->hospital > time() ) {
        throw new SoftException( GYM_CANNOT_TRAIN_HOSPITALIZED );
    } elseif ( $user_class->jail > time() ) {
        throw new SoftException( GYM_CANNOT_TRAIN_SHOWERS );
    } elseif ( $user_class->awake == 0 && $_POST['action'] == 'train' ) {
        throw new FailedResult( GYM_CANNOT_TRAIN_AWAKE );
    }

    $drugTaken = Drug::DrugTaken( $user_class );
    if ( ! empty( $drugTaken->effect ) && $drugTaken->effect != 'Caffeine' ) {
     //   throw new SoftException( GYM_CANNOT_TRAIN_DRUG );
    }

    if ( in_array( $_POST['train'], array( GYM_REFILL_TRAIN_STRENGTH, GYM_REFILL_TRAIN_DEFENSE, GYM_REFILL_TRAIN_SPEED ) ) ) {
        if ( ! UserBooks::UserHasStudied( $user_class->id, 1 ) ) {
            throw new FailedResult( INVALID_INPUT );
        }
        if ( $user_class->points < 10 ) {
            throw new FailedResult( GYM_NOT_ENOUGH_POINTS );
        }
        try {
            if ( ! $user_class->RefillEnergy( 'points', 10 ) ) {
                throw new FailedResult( GYM_NOT_ENOUGH_ENERGY );
            }
        } catch ( Exception $e ) {
        }

        $_POST['energy'] = $user_class->energy;
    }

    if ( isset( $_POST['action'] ) && $_POST['action'] == 'train' ) {
        if ( ! isset( $_POST['energy'] ) || ! is_numeric( $_POST['energy'] ) ) {
            throw new SoftException( GYM_NOT_VALID_AMT );
        } elseif ( $_POST['energy'] > $user_class->energy || $_POST['energy'] < 1 ) {
            if ( in_array( $_POST['train'], array( GYM_REFILL_TRAIN_STRENGTH, GYM_REFILL_TRAIN_DEFENSE, GYM_REFILL_TRAIN_SPEED ) ) ) {
                if ( ! UserBooks::UserHasStudied( $user_class->id, 1 ) ) {
                    throw new FailedResult( INVALID_INPUT );
                }

                if ( $user_class->points < 10 ) {
                    throw new FailedResult( GYM_NOT_ENOUGH_POINTS );
                }

                if ( ! $user_class->RefillEnergy( 'points', 10 ) ) {
                    throw new FailedResult( GYM_NOT_ENOUGH_ENERGY );
                }

                if ( empty( $_POST['energy'] ) ) {
                    $_POST['energy'] = $user_class->energy;
                }
            } else {
                throw new FailedResult( GYM_NOT_ENOUGH_ENERGY );
            }
        } elseif ( $_POST['energy'] < 1 ) {
            throw new FailedResult( GYM_NOT_ENOUGH_ENERGY );
        } elseif ( ! isset( $_POST['train'] ) ) {
            throw new SoftException( GYM_NOT_INVALID_TRAINING );
        }
        $_POST['energy'] = floor( $_POST['energy'] );
        $attribute       = '';
        if (
            $_POST['train'] == GYM_TRAIN_STRENGTH ||
            $_POST['train'] == GYM_REFILL_TRAIN_STRENGTH
        ) {
            $attribute = 'strength';
        } elseif (
            $_POST['train'] == GYM_TRAIN_DEFENSE ||
            $_POST['train'] == GYM_REFILL_TRAIN_DEFENSE
        ) {
            $attribute = 'defense';
        } elseif (
            $_POST['train'] == GYM_TRAIN_SPEED ||
            $_POST['train'] == GYM_REFILL_TRAIN_SPEED
        ) {
            $attribute = 'speed';
        } else {
            throw new CheatingException( GYM_NOT_INVALID_ATTEMPT );
        }
        $attribGain        = floor(
            ( (int) $_POST['energy'] ) * ( $user_class->awake / 100 )
        );
        if($attribute == 'speed' && $user_class->candy_time > time()){
            $attr = $attribGain / 100 * 20;
            $attribGain = $attribGain + $attr;
        }
        if($attribute == 'strength' && $user_class->lolly_time > time()){
            $attr = $attribGain / 100 * 20;
            $attribGain = $attribGain + $attr;
        }
        if($attribute == 'defense' && $user_class->apple_time > time()){
            $attr = $attribGain / 100 * 20;
            $attribGain = $attribGain + $attr;
        }
        $books             = array();
        $books['speed']    = array( 10, 7, 4, 17, 13 );
        $books['strength'] = array( 11, 8, 5, 18, 14 );
        $books['defense']  = array( 12, 9, 6, 19, 15 );

        if ( UserBooks::UserHasStudied( $user_class->id, $books[ $attribute ][0] ) ) {
            $attribGain = ceil( $attribGain + $attribGain * 0.05 );
        } elseif ( UserBooks::UserHasStudied( $user_class->id, $books[ $attribute ][1] ) ) {
            $attribGain = ceil( $attribGain + $attribGain * 0.025 );
        } elseif ( UserBooks::UserHasStudied( $user_class->id, $books[ $attribute ][2] ) ) {
            $attribGain = ceil( $attribGain + $attribGain * 0.01 );
        }
        $_POST['energy'] = ( (int) $_POST['energy'] );
        if ( $user_class->signuptime > ( time() - 2592000 ) ) {
            $black = DBi::$conn->query( 'SELECT * FROM blackops WHERE userid = ' . $user_class->id );
            if ( mysqli_num_rows( $black ) ) {
                DBi::$conn->query( 'UPDATE blackops SET gym = gym + ' . $_POST['energy'] . ' WHERE userid = ' . $user_class->id );
            } else {
                DBi::$conn->query( 'INSERT INTO blackops (userid, gym) VALUES(' . $user_class->id . ', ' . $_POST['energy'] . ')' );
            }
        }
        $umission = DBi::$conn->query("SELECT * FROM `user_missions` WHERE `user` = ".$user_class->id);
        if(($att = mysqli_fetch_assoc($umission)) == true) {
            $t1 = $att['task_one_amount'] += $attribGain;
            $t2 = $att['task_two_amount'] += $attribGain;
            $t3 = $att['task_three_amount'] += $attribGain;
            $t4 = $att['task_four_amount'] += $attribGain;
            $t5 = $att['task_five_amount'] += $attribGain;
            $t6 = $att['task_six_amount'] += $attribGain;
            $t7 = $att['task_seven_amount'] += $attribGain;
            $t8 = $att['task_eight_amount'] += $attribGain;

            if($att['task_one'] == $attribute)
                DBi::$conn->query("UPDATE `user_missions` SET `task_one_amount` = {$t1} WHERE `user` = ".$user_class->id);
            else if($att['task_two'] == $attribute)
                DBi::$conn->query("UPDATE `user_missions` SET `task_two_amount` = {$t2} WHERE `user` = ".$user_class->id);
            else if($att['task_three'] == $attribute)
                DBi::$conn->query("UPDATE `user_missions` SET `task_three_amount` = {$t3} WHERE `user` = ".$user_class->id);
            else if($att['task_four'] == $attribute)
                DBi::$conn->query("UPDATE `user_missions` SET `task_four_amount` = {$t4} WHERE `user` = ".$user_class->id);
            else if($att['task_five'] == $attribute)
                DBi::$conn->query("UPDATE `user_missions` SET `task_five_amount` = {$t5} WHERE `user` = ".$user_class->id);
            else if($att['task_six'] == $attribute)
                DBi::$conn->query("UPDATE `user_missions` SET `task_six_amount` = {$t6} WHERE `user` = ".$user_class->id);
            else if($att['task_seven'] == $attribute)
                DBi::$conn->query("UPDATE `user_missions` SET `task_seven_amount` = {$t7} WHERE `user` = ".$user_class->id);
            else if($att['task_eight'] == $attribute)
                DBi::$conn->query("UPDATE `user_missions` SET `task_eight_amount` = {$t8} WHERE `user` = ".$user_class->id);
        }
        if ( $user_class->securityLevel == 2 && $user_class->level > 249 ) {
            $black = DBi::$conn->query( 'SELECT * FROM prestige_tasks WHERE userid = ' . $user_class->id );
            if ( mysqli_num_rows( $black ) ) {
                DBi::$conn->query( 'UPDATE prestige_tasks SET gym = gym + 1 WHERE userid = ' . $user_class->id );
            } else {
                DBi::$conn->query( 'INSERT INTO prestige_tasks (userid, gym) VALUES(' . $user_class->id . ', 1)' );
            }
        }

        //BattlePass::addExp($user_class->id, $_POST['energy']);

        if ( $user_class->securityLevel > 0 ) {
            $percent    = $user_class->securityLevel * 10;
            $gain       = ( $attribGain / 100 ) * $percent;
            $attribGain = $attribGain + $gain;
        }
        // Yoga, free training
        if ( 6 >= rand( 1, 100 ) && ( UserBooks::UserHasStudied( $user_class->id, $books[ $attribute ][3] ) ) ) {
            if ( $user_class->RemoveFromAttribute( 'awake', floor( (int) $_POST['energy'] / 1 ) ) ) {
                try {
                    throw Gym::train( $user_class, $attribute, $attribGain );
                } catch ( SuccessResult $s ) {
                    throw new SuccessResult( $s->getMessage() . '<br><span class="red">' . BOOK_YOGA_EFFECT_ENERY . '</span>' );
                }
            }
        } elseif ( 3 >= rand( 1, 100 ) && UserBooks::UserHasStudied( $user_class->id, $books[ $attribute ][4] ) ) {
            if ( $user_class->RemoveFromAttribute( 'awake', floor( (int) $_POST['energy'] / 1 ) ) ) {
                try {
                    throw Gym::train( $user_class, $attribute, $attribGain );
                } catch ( SuccessResult $s ) {
                    throw new SuccessResult( $s->getMessage() . '<br><span class="red">' . BOOK_YOGA_EFFECT_ENERY . '</span>' );
                }
            }
        } elseif ( $user_class->RemoveFromAttribute( 'energy', ( (int) $_POST['energy'] ) ) && $user_class->RemoveFromAttribute( 'awake', floor( (int) $_POST['energy'] / 1 ) ) ) {
            if ( isset( $tutorial ) ) {
                $tutorial->setDone( 'Trained' );
            }
            throw Gym::train( $user_class, $attribute, $attribGain );

        }
        throw new CheatingException( GYM_NOT_INVALID_ATTEMPT );
    } elseif ( isset( $_POST['action'] ) && $_POST['action'] == 'refill' && isset( $_POST['spend'] ) && $_POST['spend'] == 'energy' ) {
        if ( $user_class->points < 10 ) {
            throw new FailedResult( GYM_NOT_ENOUGH_POINTS );
        }
        if ( $user_class->RefillEnergy( 'points', 10 ) ) {
            throw new SuccessResult( GYM_REFILLED_ENERGY );
        }
        throw new CheatingException( GYM_INVALID_REFILLING_ATTEMPT );
    } elseif ( isset( $_POST['action'] ) && $_POST['action'] == 'refillawake' ) {
        // Check for awake pill . item id:14
        if ( $user_class->awake >= $user_class->GetMaxAwake() ) {
            throw new FailedResult( USER_CANT_REFILL_AWAKE_ALREADY_MAX );
        }

        $awakePillQty = $user_class->GetItemQuantity( 36 );
        if ( $awakePillQty <= 0 ) {
            throw new FailedResult( GYM_REFILL_NOT_AWAKE_PILL );
        }

        if ( ! $user_class->RemoveItems( new Item( 36 ), 1 ) ) {
            throw new FailedResult( GYM_REFILL_NOT_AWAKE_PILL );
        }

        $user_class->RefillAwake();
        throw new SuccessResult( USER_POPPED_AWAKE_PILL );
    }
} catch ( CriticalSuccessResult $g ) {
    $response['result']  = 'critical';
    $response['message'] = $g->getMessage();
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

