<?php
include 'header.php';

if ($_GET['key'] === 'srunit') {
    $now = new \DateTime();
    $hour = $now->format('H');

    echo $hour; exit;
    
    $db->query("SELECT * FROM grpgusers WHERE is_auto_user = 1");
    $db->execute();
    $rows = $db->fetch_row();

    foreach ($rows as $r) {
        $user = new User($r['id']);


    }

}


echo 'done';

include 'footer.php';
?>