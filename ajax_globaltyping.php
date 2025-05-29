<?php
include "ajax_header.php";

$t = ($_GET['is'] == 1) ? 1 : 0;
perform_query("UPDATE gcusers SET typing = ? WHERE userid = ?", [$t, $_SESSION['id']]);