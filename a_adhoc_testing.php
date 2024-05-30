<?php
include 'header.php';

if ($_GET['key'] === 'srunit') {
    $db->query("SELECT * FROM grpgusers WHERE is_auto_user = 1");
    $db->execute();
    $rows = $db->fetch_row();

    foreach ($rows as $r) {
        $user = new User($r['id']);

        echo $user->formattedname; exit;
    }

}


echo 'done';

include 'footer.php';
?>