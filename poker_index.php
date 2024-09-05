<?php

// Set the pagename based on the URL parameter, defaulting to 'index' if not set
$pagename = isset($_GET['pagename']) ? $_GET['pagename'] : 'index';

// Check if the pagename is 'poker' to determine if a session should be started
if ($pagename != "poker") {
    require_once 'header.php';
} else {
    session_start();
}


// Include general include file unless pagename is 'approve' or 'logout'
if (!in_array($pagename, array('approve', 'logout'))) {
    require('includes/gen_inc.php');
}

// Set the frontpage file path
$frontpage = "frontpages/{$pagename}.php";

// Include the frontpage if it exists, otherwise handle with hooks
if (file_exists($frontpage)) {
    include $frontpage;
} else {
    echo $addons->get_hooks(
        array(
            'page' => $pagename
        ),
        array(
            'page' => 'general',
            'location' => 'frontpage'
        )
    );
}

// Include the footer unless the pagename is 'poker'
if ($pagename != "poker") {
    require_once 'footer.php';
}
?>
