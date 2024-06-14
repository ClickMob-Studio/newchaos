<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get the pagename from the query parameters or set it to 'index' by default
$pagename = isset($_GET['pagename']) ? $_GET['pagename'] : 'index';


// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include header or start session based on pagename
if ($pagename != "poker") {
    require_once 'header.php';
} else {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
}

// Include general includes file unless on specific pages
if (!in_array($pagename, array('approve', 'logout'))) {
    if (file_exists('includes/gen_inc.php')) {
        require 'includes/gen_inc.php';
    } else {
        die('General includes file not found.');
    }
}

// Determine the frontpage file to include based on the pagename
$frontpage = "frontpages/{$pagename}.php";

// Include the frontpage file if it exists, otherwise execute the hook
if (file_exists($frontpage)) {
    include $frontpage;
} else {
    echo $addons->get_hooks(
        array(
            'page' => $pagename
        ),
        array(
            'page'     => 'general',
            'location' => 'frontpage'
        )
    );
}

// Include the footer if not on the 'poker' page
if ($pagename != "poker") {
    if (file_exists('footer.php')) {
        require_once 'footer.php';
    } else {
        die('Footer file not found.');
    }
}
?>
