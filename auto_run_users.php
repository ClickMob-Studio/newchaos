<?php

include_once 'dbcon.php';
include_once 'classes.php';
include 'database/pdo_class.php';

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

            if (isset($check[0]['id'])) {
                // Run with active mission
                $activeMission = $check[0]['id'];

                $db->query("SELECT * FROM mission WHERE id = " . $activeMission['mid'] . " LIMIT 1");
                $db->execute();
                $mMission = $db->fetch_row();

                if (isset($mMission[0]['id'])) {
                    $mMission = $mMission[0]['id'];

                    if ($mMission['crimes'] > 0) {

                        $timesToRun = mt_rand(10,100);

                        while ($i < $timesToRun) {
                            // Crime Mission
                            $durl = "https://chaoscity.co.uk/ajax_crimes2.php?au_user_or=" . $user->id;
                            $ch =  curl_init()  ;
                            curl_setopt($ch,CURLOPT_URL, $durl);
                            curl_setopt ($ch, CURLOPT_HEADER, 0);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                            curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS,
                                "id=1&cm=10");
                            $dinf = curl_exec ($ch);
                            if(!curl_errno($ch) ){

                            }else{

                            }

                            $i++;
                        }
                    }
                }


            }

            // Check whether to start an active mission



        }
    }
}


echo 'done';
?>