<?php
include 'header.php';

if($user_class->admin < 1)
    exit();

$querys = "SELECT id, username, killcomp1 FROM grpgusers WHERE killcomp1 > 999 AND killcomp1 < 2500  ORDER BY killcomp1 DESC";
$results = mysql_query($querys);
    while($r = mysql_fetch_assoc($results)){
        mysql_query("UPDATE grpgusers SET points = points + 25000 WHERE id = " . $r['id']);
        Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
    }

$querys = "SELECT id, username, killcomp1 FROM grpgusers WHERE killcomp1 > 2499 AND killcomp1 < 5000  ORDER BY killcomp1 DESC";
$results = mysql_query($querys);
    while($r = mysql_fetch_assoc($results)){
        mysql_query("UPDATE grpgusers SET points = points + 6500 WHERE id = " . $r['id']);
        Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, killcomp1 FROM grpgusers WHERE killcomp1 > 4999 AND killcomp1 < 15000  ORDER BY killcomp1 DESC";
$results = mysql_query($querys);
    while($r = mysql_fetch_assoc($results)){
        mysql_query("UPDATE grpgusers SET points = points + 13000 WHERE id = " . $r['id']);
        Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, killcomp1 FROM grpgusers WHERE killcomp1 > 14999 AND killcomp1 < 30000  ORDER BY killcomp1 DESC";
    $results = mysql_query($querys);
        while($r = mysql_fetch_assoc($results)){
            mysql_query("UPDATE grpgusers SET points = points + 37500 WHERE id = " . $r['id']);
            Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
        }

$querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 49 AND raidcomp < 100  ORDER BY raidcomp DESC";
$results = mysql_query($querys);
    while($r = mysql_fetch_assoc($results)){
        mysql_query("UPDATE grpgusers SET points = points + 25000 WHERE id = " . $r['id']);
        Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 99 AND raidcomp < 250  ORDER BY raidcomp DESC";
$results = mysql_query($querys);
    while($r = mysql_fetch_assoc($results)){
        mysql_query("UPDATE grpgusers SET points = points + 50000 WHERE id = " . $r['id']);
        Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
    }
    $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 249 AND raidcomp < 500  ORDER BY raidcomp DESC";
    $results = mysql_query($querys);
        while($r = mysql_fetch_assoc($results)){
            mysql_query("UPDATE grpgusers SET points = points + 100000 WHERE id = " . $r['id']);
            Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
        }
        $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 499 AND raidcomp < 750  ORDER BY raidcomp DESC";
        $results = mysql_query($querys);
            while($r = mysql_fetch_assoc($results)){
                mysql_query("UPDATE grpgusers SET points = points + 125000 WHERE id = " . $r['id']);
                Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
            }
            $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 749 AND raidcomp < 1000  ORDER BY raidcomp DESC";
            $results = mysql_query($querys);
                while($r = mysql_fetch_assoc($results)){
                    mysql_query("UPDATE grpgusers SET points = points + 150000 WHERE id = " . $r['id']);
                    Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
                }

            $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 999 AND raidcomp < 1250  ORDER BY raidcomp DESC";
            $results = mysql_query($querys);
                while($r = mysql_fetch_assoc($results)){
                    mysql_query("UPDATE grpgusers SET points = points + 200000 WHERE id = " . $r['id']);
                    Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
                }
                $querys = "SELECT id, username, raidcomp FROM grpgusers WHERE raidcomp > 1250 ORDER BY raidcomp DESC";
                $results = mysql_query($querys);
                    while($r = mysql_fetch_assoc($results)){
                        mysql_query("UPDATE grpgusers SET points = points + 250000 WHERE id = " . $r['id']);
                        Send_Event($r['id'], 'You got rewarded '.$points.' for hitting your milesstone in the contest');
                    }
