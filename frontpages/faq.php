<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$addons->get_hooks(array(), array(

	'page'     => 'faq.php',
	'location'  => 'page_start'

));

$opsTheme->addVariable('faq', FAQ);

include '/templates/header.php';

echo $addons->get_hooks(array(), array(

	'page'     => 'faq.php',
	'location'  => 'html_start'

));

echo $opsTheme->viewPage('faq');

echo $addons->get_hooks(array(), array(

	'page'     => 'faq.php',
	'location'  => 'html_end'

));

include '/templates/footer.php';
?>