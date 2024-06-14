<?php
if(!empty($_POST)){
    $user_class->AddToAttribute('poth', 1);
}
$users2 = SUserFactory::getInstance()->getUser( $user_class->id );

$id = CrimesContract::UnderContract( $user_class->id );

$crimesContract = $id == false ? array() : array( new CrimesContract( $id ) );

if ( count( $crimesContract ) == 1 ) {
    $ts  = UserFactory::getInstance()->getUser( $crimesContract[0]->buyer );
    $ts1 = SUserFactory::getInstance()->getUser( $crimesContract[0]->buyer );

}

if ( isset( $tutorial ) ) {
    $tutorial->setDone( 'Cell' );
}

try {
    if ( $user_class->jail > time() ) {
        throw new SoftException( CRIME_IN_SHOWER );
    } elseif ( $user_class->hospital > time() ) {
        throw new SoftException( CRIME_IN_HOSPITAL );
    }

    switch ( $_POST['action'] ) {
        case 'setExpToOwner':
            $users2->setAttribute( 'perexp', (int) $_POST['value'] );
            $users2->perexp                                  = (int) $_POST['value'];
            $response['ajaxValue']['missionContractPercent'] = (int) $_POST['value'];

            throw new SuccessResult( 'You have successfully set your Mission Contract experience share to ' . (int) $_POST['value'] . '%.' );
            break;
        default:
            break;
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
