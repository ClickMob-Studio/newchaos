<?php

include_once 'dbcon.php';
include_once 'classes.php';
include 'database/pdo_class.php';

if ($_GET['key'] === 'srunit') {
    $now = new \DateTime();
    $hour = $now->format('H');

    // Only run between 8am and 10pm to make it look more legit
    if ($hour >= 7 && $hour < 21) {
        $db->query("SELECT * FROM grpgusers WHERE is_auto_user = 1");
        $db->execute();
        $rows = $db->fetch_row();

        foreach ($rows as $r) {
            $user = new User($r['id']);

            $db->query("SELECT * FROM missions WHERE userid= " . $user->id . " AND completed='no' LIMIT 1");
            $db->execute();
            $check = $db->fetch_row();

            $hasActionComplete = false;

            if (isset($check[0]['id'])) {
                // Run with active mission
                $activeMission = $check[0];

                $db->query("SELECT * FROM mission WHERE id = " . $activeMission['mid'] . " LIMIT 1");
                $db->execute();
                $mMission = $db->fetch_row();

                $runChance = mt_rand(1,100);
                if ($runChance > 90) {
                    $hasActionComplete = true;

                    if (isset($mMission[0]['id'])) {
                        $mMission = $mMission[0];

                        if ($mMission['crimes'] > 1) {

                            if ($user->nerref < 1) {
                                $user->nerref = 2;
                                $db->query("UPDATE grpgusers SET nerref = ?, nerreftime = unix_timestamp() WHERE id = ?");
                                $db->execute(array(
                                    $user->nerref,
                                    $user->id
                                ));
                            }

                            $timesToRun = mt_rand(100,500);

                            $i = 0;
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
                                    "id=1&cm=50");
                                $dinf = curl_exec ($ch);
                                if(!curl_errno($ch) ){

                                }else{

                                }

                                $i++;
                            }

                            $money = $user->money;

                            $db->query('UPDATE grpgusers SET bank = bank + ' . $money . ', money = 0 WHERE id = ' . $user->id);
                            $db->execute();
                        }

                        if ($mMission['backalleys'] > 1) {
                            $timesToRun = mt_rand(50,250);

                            $i = 0;
                            while ($i < $timesToRun) {
                                $durl = "https://chaoscity.co.uk/ajax_ba_new.php?alv=yes&au_user_or=" . $user->id;
                                $ch =  curl_init()  ;
                                curl_setopt($ch,CURLOPT_URL, $durl);
                                curl_setopt ($ch, CURLOPT_HEADER, 0);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                                curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
                                $dinf = curl_exec ($ch);
                                if(!curl_errno($ch) ){
                                }else{
                                }

                                $i++;

                                $money = $user->money;

                                $db->query('UPDATE grpgusers SET bank = bank + ' . $money . ', money = 0, `hospital` = 0 WHERE id = ' . $user->id);
                                $db->execute();
                            }

                        }
                    }
                }

                $durl = "https://chaoscity.co.uk/ajax_supergym.php?au_user_or=" . $user_class->id;

                $stats = array('strength', 'defense', 'speed', 'agility');
                $stat = $stats[mt_rand(0, 3)];

                $i = 1;
                while ($i < 50) {

                    $ch =  curl_init();
                    curl_setopt($ch,CURLOPT_URL, $durl);
                    curl_setopt ($ch, CURLOPT_HEADER, 0);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS,
                        "amnt=" . $user_class->maxenergy . "&stat=" . $stat . '&what=trainrefill&mega_train=no&multiplier=10');
                    $dinf = curl_exec ($ch);
                    var_dump($dinf);

                    $i++;
                }
            }

            // Check whether to start an active mission - 33% chance of starting a mission
            if (mt_rand(1,4) > 1) {
                $hasActionComplete = true;

                if (!isset($check[0]['id'])) {
                    $timeCheck = time() - 87400;

                    $db->query("SELECT * FROM missions WHERE userid= " . $user->id . " AND timestamp > " . $timeCheck);
                    $db->execute();
                    $mChecks = $db->fetch_row();

                    $missionsComplete = array();
                    foreach ($mChecks as $mCheck) {
                        $missionsComplete[] = $mCheck['mid'];
                    }

                    // Check if any Crime missions
                    if (count($missionsComplete) > 0) {
                        $db->query("SELECT * FROM mission WHERE category = 2 AND id NOT IN (" . join(',', $missionsComplete) . ")");
                    } else {
                        $db->query("SELECT * FROM mission WHERE category = 2");
                    }
                    $db->execute();
                    $cmChecks = $db->fetch_row(true);


                    if (isset($cmChecks['id'])) {
                        $now = time();
                        $db->query("INSERT INTO missions (`userid`, `timestamp`, `mid`) VALUES({$user->id}, {$now}, {$cmChecks['id']})");
                        $db->execute();
                        $db->query("INSERT INTO missionlog (`text`, `timestamp`) VALUES('[x] started a {$cmChecks['name']},{$user->id}', unix_timestamp())");
                        $db->execute();
                    }

                    // Check if any BA missions
                    if (!isset($cmChecks['id'])) {
                        // Check if any BA missions
                        if (count($missionsComplete) > 0) {
                            $db->query("SELECT * FROM mission WHERE category = 6 AND id NOT IN (" . join(',', $missionsComplete) . ")");
                        } else {
                            $db->query("SELECT * FROM mission WHERE category = 6");
                        }
                        $db->execute();
                        $baChecks = $db->fetch_row(true);

                        if (isset($baChecks['id'])) {
                            $now = time();
                            $db->query("INSERT INTO missions (`userid`, `timestamp`, `mid`) VALUES({$user->id}, {$now}, {$baChecks['id']})");
                            $db->execute();
                            $db->query("INSERT INTO missionlog (`text`, `timestamp`) VALUES('[x] started a {$baChecks['name']},{$user->id}', unix_timestamp())");
                            $db->execute();
                        }
                    }

                    // Check if any kill missions
                    if (!isset($cmChecks['id'])) {

                    }

                    // Check if any mug missions
                }
            }

//            if (!$hasActionComplete) {
//                if (mt_rand(1,100) < 2) {
//                    if ($user->nerref < 1) {
//                        $user->nerref = 2;
//                        $db->query("UPDATE grpgusers SET nerref = ?, nerreftime = unix_timestamp() WHERE id = ?");
//                        $db->execute(array(
//                            $user->nerref,
//                            $user->id
//                        ));
//                    }
//
//                    $timesToRun = mt_rand(100,500);
//
//                    $i = 0;
//                    while ($i < $timesToRun) {
//                        // Crime Mission
//                        $durl = "https://chaoscity.co.uk/ajax_crimes2.php?au_user_or=" . $user->id;
//                        $ch =  curl_init()  ;
//                        curl_setopt($ch,CURLOPT_URL, $durl);
//                        curl_setopt ($ch, CURLOPT_HEADER, 0);
//                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//                        curl_setopt ($ch, CURLOPT_FAILONERROR, 1);
//                        curl_setopt($ch, CURLOPT_POST, 1);
//                        curl_setopt($ch, CURLOPT_POSTFIELDS,
//                            "id=250&cm=50");
//                        $dinf = curl_exec ($ch);
//                        if(!curl_errno($ch) ){
//
//                        }else{
//
//                        }
//
//                        $i++;
//                    }
//
//                    $money = $user->money;
//
//                    $db->query('UPDATE grpgusers SET bank = bank + ' . $money . ', money = 0 WHERE id = ' . $user->id);
//                    $db->execute();
//                }
//            }



        }
    }
}


echo 'done';
?>