<?php

if (isset($action) && $action == 'logout' && $plyrname != '')
{
    if (isset($_SESSION['playername']))
    {
    	unset($_SESSION['playername']);
    }

    if (isset($_SESSION['SGUID']))
    {
    	unset($_SESSION['SGUID']);
    }

    if (isset($_COOKIE['remember_me'])) {
        unset($_COOKIE['remember_me']);
    }

    header('Location: index.php');
}
?>