<?php

require ("header.php");

if($user_class->admin < 1){
  exit();
}

removeFromInventory(1,229,1);