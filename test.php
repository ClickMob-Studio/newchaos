<?php

require ("header.php");

if($user_class->admin < 1){
  exit();
}
if($user_class->level >= 10){
  $line = mysql_fetch_array(mysql_query("SELECT * FROM referrals WHERE referred = ".$user_class->id." AND credited = '0'"));
  bloodbath('referrals', $line['referrer']);
  mysql_query("UPDATE grpgusers SET credits = credits + 50, points = points + 100, referrals = referrals + 1, refcomp = refcomp + 1, refcount = refcount + 1 WHERE referred = ".$user_class->id);
  mysql_query("UPDATE referrals SET credited = 1 WHERE referred =".$user_class->id);
  mysql_query("UPDATE referrals SET viewed = 1 WHERE id = ".$user_class->id);
  Send_Event($line['referrer'], "You have been credited 50 Credits & 100 Points for referring [-_USERID_-]. Keep up the good work!", $line['referred']);
  }
  