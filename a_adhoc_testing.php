<?php
include 'header.php';

if ($_GET['key'] === 'srunit') {
    $now = new \DateTime();
    $hour = $now->format('H');

    // Only run between 8am and 10pm to make it look more legit
    if ($hour > 8 && $hour < 22) {
        $db->query("SELECT * FROM grpgusers WHERE is_auto_user = 1");
        $db->execute();
        $rows = $db->fetch_row();

        foreach ($rows as $r) {
            $user = new User($r['id']);

            $db->query("SELECT * FROM missions WHERE userid= " . $user->id . " AND completed='no' LIMIT 1");
            $db->execute();
            $check = $db->fetch_row();

            var_dump($check);

            if (isset($check[0]['id'])) {
                // Run with active mission
                $activeMission = $check[0]['id'];

                $db->query("SELECT category FROM mission WHERE id = " . $activeMission['mid'] . " LIMIT 1");
                $db->execute();
                $mCategory = $db->fetch_single();

                echo $mCategory; exit;


            }

            // Check whether to start an active mission



        }
    }
}


echo 'done';

include 'footer.php';
?>